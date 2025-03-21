<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\Product;
use App\Models\ReturnRequest;
use App\Models\ReturnAuditLog;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventoryController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'can:manage-warehouses']);
    }

    /**
     * Display inventory for a specific warehouse.
     */
    public function index(Warehouse $warehouse)
    {
        // Get inventory items for this warehouse
        $inventory = DB::table('warehouse_inventory')
            ->join('products', 'warehouse_inventory.product_id', '=', 'products.id')
            ->where('warehouse_inventory.warehouse_id', $warehouse->id)
            ->select(
                'warehouse_inventory.id',
                'products.id as product_id',
                'products.name as product_name',
                'products.sku',
                'products.category',
                'warehouse_inventory.quantity',
                'warehouse_inventory.quantity_pending',
                'warehouse_inventory.quantity_damaged',
                'warehouse_inventory.quantity_refurbished',
                'warehouse_inventory.location_code',
                'warehouse_inventory.last_updated_at'
            )
            ->orderBy('products.name')
            ->paginate(20);
        
        // Get recent inventory movements
        $recentMovements = InventoryMovement::with(['product', 'user'])
            ->where('warehouse_id', $warehouse->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Get inventory statistics
        $stats = [
            'total_products' => DB::table('warehouse_inventory')
                ->where('warehouse_id', $warehouse->id)
                ->count(),
            'total_quantity' => DB::table('warehouse_inventory')
                ->where('warehouse_id', $warehouse->id)
                ->sum('quantity'),
            'total_damaged' => DB::table('warehouse_inventory')
                ->where('warehouse_id', $warehouse->id)
                ->sum('quantity_damaged'),
            'total_refurbished' => DB::table('warehouse_inventory')
                ->where('warehouse_id', $warehouse->id)
                ->sum('quantity_refurbished'),
            'movements_today' => InventoryMovement::where('warehouse_id', $warehouse->id)
                ->whereDate('created_at', today())
                ->count(),
        ];
        
        return view('warehouses.inventory.index', compact('warehouse', 'inventory', 'recentMovements', 'stats'));
    }

    /**
     * Update inventory for a specific warehouse.
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'action' => 'required|in:add,remove,move,adjust,scan,process_return',
            'quantity' => 'required_unless:action,scan,process_return|integer|min:1',
            'inventory_type' => 'required_unless:action,scan,process_return|in:regular,damaged,refurbished,pending',
            'location_code' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
            'return_request_id' => 'required_if:action,process_return|exists:return_requests,id',
            'barcode' => 'required_if:action,scan|string',
            'destination_warehouse_id' => 'required_if:action,move|exists:warehouses,id',
        ]);
        
        // Start a transaction
        DB::beginTransaction();
        
        try {
            $product = Product::findOrFail($validated['product_id']);
            $movementType = null;
            $quantityChange = 0;
            $details = [];
            
            // Handle different inventory actions
            switch ($validated['action']) {
                case 'add':
                    $movementType = InventoryMovement::TYPE_ADD;
                    $quantityChange = $validated['quantity'];
                    $this->updateInventoryQuantity(
                        $warehouse->id,
                        $product->id,
                        $validated['inventory_type'],
                        $quantityChange,
                        $validated['location_code'] ?? null
                    );
                    $details = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_sku' => $product->sku,
                        'quantity' => $quantityChange,
                        'inventory_type' => $validated['inventory_type'],
                        'location_code' => $validated['location_code'] ?? null,
                    ];
                    break;
                    
                case 'remove':
                    $movementType = InventoryMovement::TYPE_REMOVE;
                    $quantityChange = -$validated['quantity'];
                    $this->updateInventoryQuantity(
                        $warehouse->id,
                        $product->id,
                        $validated['inventory_type'],
                        $quantityChange,
                        $validated['location_code'] ?? null
                    );
                    $details = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_sku' => $product->sku,
                        'quantity' => $validated['quantity'],
                        'inventory_type' => $validated['inventory_type'],
                        'location_code' => $validated['location_code'] ?? null,
                    ];
                    break;
                    
                case 'move':
                    $movementType = InventoryMovement::TYPE_TRANSFER;
                    $quantityChange = -$validated['quantity'];
                    $destinationWarehouse = Warehouse::findOrFail($validated['destination_warehouse_id']);
                    
                    // Remove from source warehouse
                    $this->updateInventoryQuantity(
                        $warehouse->id,
                        $product->id,
                        $validated['inventory_type'],
                        $quantityChange,
                        $validated['location_code'] ?? null
                    );
                    
                    // Add to destination warehouse
                    $this->updateInventoryQuantity(
                        $destinationWarehouse->id,
                        $product->id,
                        $validated['inventory_type'],
                        abs($quantityChange),
                        null
                    );
                    
                    $details = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_sku' => $product->sku,
                        'quantity' => $validated['quantity'],
                        'inventory_type' => $validated['inventory_type'],
                        'source_warehouse_id' => $warehouse->id,
                        'source_warehouse_name' => $warehouse->name,
                        'destination_warehouse_id' => $destinationWarehouse->id,
                        'destination_warehouse_name' => $destinationWarehouse->name,
                        'location_code' => $validated['location_code'] ?? null,
                    ];
                    break;
                    
                case 'adjust':
                    $movementType = InventoryMovement::TYPE_ADJUSTMENT;
                    
                    // Get current quantity
                    $currentQuantity = $this->getInventoryQuantity(
                        $warehouse->id,
                        $product->id,
                        $validated['inventory_type']
                    );
                    
                    // Calculate adjustment
                    $quantityChange = $validated['quantity'] - $currentQuantity;
                    
                    if ($quantityChange != 0) {
                        $this->setInventoryQuantity(
                            $warehouse->id,
                            $product->id,
                            $validated['inventory_type'],
                            $validated['quantity'],
                            $validated['location_code'] ?? null
                        );
                        
                        $details = [
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'product_sku' => $product->sku,
                            'previous_quantity' => $currentQuantity,
                            'new_quantity' => $validated['quantity'],
                            'adjustment' => $quantityChange,
                            'inventory_type' => $validated['inventory_type'],
                            'location_code' => $validated['location_code'] ?? null,
                        ];
                    } else {
                        // No change needed
                        DB::commit();
                        return redirect()->back()->with('info', 'No inventory adjustment needed.');
                    }
                    break;
                    
                case 'scan':
                    // Handle barcode scanning - in a real implementation, this would use
                    // a barcode scanning library to decode the barcode
                    $barcode = $validated['barcode'];
                    
                    // For demonstration, we'll assume the barcode is in the format: SKU-LOCATION
                    $parts = explode('-', $barcode);
                    $sku = $parts[0] ?? '';
                    $locationCode = $parts[1] ?? null;
                    
                    // Find product by SKU
                    $scannedProduct = Product::where('sku', $sku)->first();
                    
                    if (!$scannedProduct) {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'Product with SKU ' . $sku . ' not found.');
                    }
                    
                    // Update the product and location in the form
                    DB::commit();
                    return redirect()->back()->with([
                        'scanned_product_id' => $scannedProduct->id,
                        'scanned_location' => $locationCode,
                        'success' => 'Product scanned successfully: ' . $scannedProduct->name,
                    ]);
                    break;
                    
                case 'process_return':
                    $returnRequest = ReturnRequest::findOrFail($validated['return_request_id']);
                    
                    // Ensure the return request is in the correct state
                    if ($returnRequest->status !== ReturnRequest::STATUS_INSPECTED) {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'Return request must be in inspected status to process inventory.');
                    }
                    
                    // Determine inventory type based on return condition
                    $inventoryType = 'regular';
                    if ($returnRequest->condition === ReturnRequest::CONDITION_DAMAGED) {
                        $inventoryType = 'damaged';
                    } elseif ($returnRequest->condition === ReturnRequest::CONDITION_NEEDS_REFURBISHMENT) {
                        $inventoryType = 'pending';
                    }
                    
                    // Add the returned product to inventory
                    $movementType = InventoryMovement::TYPE_RETURN;
                    $quantityChange = 1; // Assuming one product per return
                    
                    $this->updateInventoryQuantity(
                        $warehouse->id,
                        $returnRequest->product_id,
                        $inventoryType,
                        $quantityChange,
                        $validated['location_code'] ?? null
                    );
                    
                    // Update the return request
                    $returnRequest->update([
                        'inventory_processed' => true,
                        'inventory_location' => $validated['location_code'] ?? null,
                        'inventory_processed_at' => now(),
                        'inventory_processed_by' => Auth::id(),
                    ]);
                    
                    $details = [
                        'return_request_id' => $returnRequest->id,
                        'rma_number' => $returnRequest->rma_number,
                        'product_id' => $returnRequest->product_id,
                        'product_name' => $product->name,
                        'product_sku' => $product->sku,
                        'inventory_type' => $inventoryType,
                        'location_code' => $validated['location_code'] ?? null,
                    ];
                    break;
            }
            
            // Create inventory movement record
            InventoryMovement::create([
                'warehouse_id' => $warehouse->id,
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'movement_type' => $movementType,
                'quantity' => abs($quantityChange),
                'direction' => $quantityChange >= 0 ? 'in' : 'out',
                'inventory_type' => $validated['inventory_type'] ?? null,
                'reference_type' => $validated['action'] === 'process_return' ? 'return_request' : null,
                'reference_id' => $validated['action'] === 'process_return' ? $validated['return_request_id'] : null,
                'location_code' => $validated['location_code'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'details' => json_encode($details),
            ]);
            
            // Log the inventory action
            ReturnAuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'inventory_' . $validated['action'],
                'entity_type' => 'inventory',
                'entity_id' => $product->id,
                'details' => json_encode($details),
                'ip_address' => $request->ip(),
            ]);
            
            DB::commit();
            
            return redirect()->route('warehouses.inventory.index', $warehouse)
                ->with('success', 'Inventory updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()->with('error', 'Error updating inventory: ' . $e->getMessage());
        }
    }
    
    /**
     * Update inventory quantity for a specific product and type.
     */
    private function updateInventoryQuantity(int $warehouseId, int $productId, string $inventoryType, int $quantityChange, ?string $locationCode = null)
    {
        // Check if inventory record exists
        $exists = DB::table('warehouse_inventory')
            ->where('warehouse_id', $warehouseId)
            ->where('product_id', $productId)
            ->exists();
        
        if ($exists) {
            // Update existing record
            $query = DB::table('warehouse_inventory')
                ->where('warehouse_id', $warehouseId)
                ->where('product_id', $productId);
            
            switch ($inventoryType) {
                case 'regular':
                    $query->increment('quantity', $quantityChange);
                    break;
                case 'damaged':
                    $query->increment('quantity_damaged', $quantityChange);
                    break;
                case 'refurbished':
                    $query->increment('quantity_refurbished', $quantityChange);
                    break;
                case 'pending':
                    $query->increment('quantity_pending', $quantityChange);
                    break;
            }
            
            // Update location code if provided
            if ($locationCode) {
                $query->update(['location_code' => $locationCode, 'last_updated_at' => now()]);
            } else {
                $query->update(['last_updated_at' => now()]);
            }
        } else {
            // Create new record
            $data = [
                'warehouse_id' => $warehouseId,
                'product_id' => $productId,
                'quantity' => 0,
                'quantity_damaged' => 0,
                'quantity_refurbished' => 0,
                'quantity_pending' => 0,
                'location_code' => $locationCode,
                'last_updated_at' => now(),
            ];
            
            switch ($inventoryType) {
                case 'regular':
                    $data['quantity'] = max(0, $quantityChange);
                    break;
                case 'damaged':
                    $data['quantity_damaged'] = max(0, $quantityChange);
                    break;
                case 'refurbished':
                    $data['quantity_refurbished'] = max(0, $quantityChange);
                    break;
                case 'pending':
                    $data['quantity_pending'] = max(0, $quantityChange);
                    break;
            }
            
            DB::table('warehouse_inventory')->insert($data);
        }
        
        // Update product's total stock quantity
        Product::where('id', $productId)->update([
            'stock_quantity' => DB::raw('(SELECT SUM(quantity + quantity_refurbished) FROM warehouse_inventory WHERE product_id = ' . $productId . ')'),
            'updated_at' => now(),
        ]);
    }
    
    /**
     * Set inventory quantity for a specific product and type.
     */
    private function setInventoryQuantity(int $warehouseId, int $productId, string $inventoryType, int $quantity, ?string $locationCode = null)
    {
        // Check if inventory record exists
        $exists = DB::table('warehouse_inventory')
            ->where('warehouse_id', $warehouseId)
            ->where('product_id', $productId)
            ->exists();
        
        if ($exists) {
            // Update existing record
            $query = DB::table('warehouse_inventory')
                ->where('warehouse_id', $warehouseId)
                ->where('product_id', $productId);
            
            $updateData = ['last_updated_at' => now()];
            
            switch ($inventoryType) {
                case 'regular':
                    $updateData['quantity'] = max(0, $quantity);
                    break;
                case 'damaged':
                    $updateData['quantity_damaged'] = max(0, $quantity);
                    break;
                case 'refurbished':
                    $updateData['quantity_refurbished'] = max(0, $quantity);
                    break;
                case 'pending':
                    $updateData['quantity_pending'] = max(0, $quantity);
                    break;
            }
            
            // Update location code if provided
            if ($locationCode) {
                $updateData['location_code'] = $locationCode;
            }
            
            $query->update($updateData);
        } else {
            // Create new record
            $data = [
                'warehouse_id' => $warehouseId,
                'product_id' => $productId,
                'quantity' => 0,
                'quantity_damaged' => 0,
                'quantity_refurbished' => 0,
                'quantity_pending' => 0,
                'location_code' => $locationCode,
                'last_updated_at' => now(),
            ];
            
            switch ($inventoryType) {
                case 'regular':
                    $data['quantity'] = max(0, $quantity);
                    break;
                case 'damaged':
                    $data['quantity_damaged'] = max(0, $quantity);
                    break;
                case 'refurbished':
                    $data['quantity_refurbished'] = max(0, $quantity);
                    break;
                case 'pending':
                    $data['quantity_pending'] = max(0, $quantity);
                    break;
            }
            
            DB::table('warehouse_inventory')->insert($data);
        }
        
        // Update product's total stock quantity
        Product::where('id', $productId)->update([
            'stock_quantity' => DB::raw('(SELECT SUM(quantity + quantity_refurbished) FROM warehouse_inventory WHERE product_id = ' . $productId . ')'),
            'updated_at' => now(),
        ]);
    }
    
    /**
     * Get inventory quantity for a specific product and type.
     */
    private function getInventoryQuantity(int $warehouseId, int $productId, string $inventoryType): int
    {
        $inventory = DB::table('warehouse_inventory')
            ->where('warehouse_id', $warehouseId)
            ->where('product_id', $productId)
            ->first();
        
        if (!$inventory) {
            return 0;
        }
        
        switch ($inventoryType) {
            case 'regular':
                return $inventory->quantity;
            case 'damaged':
                return $inventory->quantity_damaged;
            case 'refurbished':
                return $inventory->quantity_refurbished;
            case 'pending':
                return $inventory->quantity_pending;
            default:
                return 0;
        }
    }
}
