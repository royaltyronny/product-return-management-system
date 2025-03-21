<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseInventory extends Model
{
    use HasFactory;

    protected $table = 'warehouse_inventory';

    protected $fillable = [
        'warehouse_id',
        'product_id',
        'quantity',
        'quantity_damaged',
        'quantity_refurbished',
        'quantity_pending',
        'location_code',
        'last_updated_at',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'quantity_damaged' => 'integer',
        'quantity_refurbished' => 'integer',
        'quantity_pending' => 'integer',
        'last_updated_at' => 'datetime',
    ];

    /**
     * Get the warehouse this inventory belongs to
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the product this inventory is for
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get total quantity of all inventory types
     */
    public function getTotalQuantityAttribute(): int
    {
        return $this->quantity + $this->quantity_damaged + $this->quantity_refurbished + $this->quantity_pending;
    }

    /**
     * Update inventory quantity based on a movement
     */
    public function applyMovement(InventoryMovement $movement): bool
    {
        $inventoryType = $movement->inventory_type ?? InventoryMovement::INVENTORY_REGULAR;
        $quantity = $movement->quantity;
        $direction = $movement->direction;
        
        // Determine which inventory type to update
        $field = match($inventoryType) {
            InventoryMovement::INVENTORY_DAMAGED => 'quantity_damaged',
            InventoryMovement::INVENTORY_REFURBISHED => 'quantity_refurbished',
            InventoryMovement::INVENTORY_PENDING => 'quantity_pending',
            default => 'quantity',
        };
        
        // Apply the movement
        if ($direction === 'in') {
            $this->$field += $quantity;
        } else {
            // Ensure we don't go below zero
            $this->$field = max(0, $this->$field - $quantity);
        }
        
        $this->last_updated_at = now();
        return $this->save();
    }

    /**
     * Find or create inventory record for a product in a warehouse
     */
    public static function findOrCreate(int $warehouseId, int $productId, ?string $locationCode = null)
    {
        $inventory = self::where('warehouse_id', $warehouseId)
            ->where('product_id', $productId)
            ->first();
            
        if (!$inventory) {
            $inventory = self::create([
                'warehouse_id' => $warehouseId,
                'product_id' => $productId,
                'quantity' => 0,
                'quantity_damaged' => 0,
                'quantity_refurbished' => 0,
                'quantity_pending' => 0,
                'location_code' => $locationCode,
                'last_updated_at' => now(),
            ]);
        }
        
        return $inventory;
    }
    
    /**
     * Get inventory levels for a product across all warehouses
     */
    public static function getInventoryForProduct(int $productId)
    {
        return self::with('warehouse')
            ->where('product_id', $productId)
            ->get();
    }
    
    /**
     * Get low inventory items for a warehouse
     */
    public static function getLowInventoryItems(int $warehouseId, int $threshold = 5)
    {
        return self::with('product')
            ->where('warehouse_id', $warehouseId)
            ->where('quantity', '<', $threshold)
            ->get();
    }
}
