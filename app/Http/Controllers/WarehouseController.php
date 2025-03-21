<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\ReturnRequest;
use App\Models\ReturnShipment;
use App\Models\ReturnAuditLog;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WarehouseController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'can:manage-warehouses']);
    }

    /**
     * Display a listing of warehouses.
     */
    public function index()
    {
        $warehouses = Warehouse::withCount(['pendingReturns', 'processedReturns'])
            ->orderBy('name')
            ->paginate(10);
            
        // Get summary statistics
        $stats = [
            'total_warehouses' => Warehouse::count(),
            'total_pending_returns' => ReturnRequest::whereIn('status', [
                ReturnRequest::STATUS_SHIPPED,
                ReturnRequest::STATUS_RECEIVED
            ])->count(),
            'total_processed_today' => ReturnRequest::where('status', ReturnRequest::STATUS_INSPECTED)
                ->whereDate('updated_at', today())
                ->count(),
        ];
        
        return view('warehouses.index', compact('warehouses', 'stats'));
    }

    /**
     * Show the form for creating a new warehouse.
     */
    public function create()
    {
        return view('warehouses.create');
    }

    /**
     * Store a newly created warehouse in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:warehouses,code',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:20',
            'is_active' => 'boolean',
            'can_process_returns' => 'boolean',
            'can_process_refurbishment' => 'boolean',
        ]);
        
        // Start a transaction
        DB::beginTransaction();
        
        try {
            // Create the warehouse
            $warehouse = Warehouse::create([
                'name' => $validated['name'],
                'code' => strtoupper($validated['code']),
                'address' => $validated['address'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'zip_code' => $validated['zip_code'],
                'country' => $validated['country'],
                'contact_name' => $validated['contact_name'],
                'contact_email' => $validated['contact_email'],
                'contact_phone' => $validated['contact_phone'],
                'is_active' => $request->has('is_active'),
                'can_process_returns' => $request->has('can_process_returns'),
                'can_process_refurbishment' => $request->has('can_process_refurbishment'),
                'created_by' => Auth::id(),
            ]);
            
            // Log the warehouse creation
            ReturnAuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'warehouse_created',
                'entity_type' => 'warehouse',
                'entity_id' => $warehouse->id,
                'details' => json_encode([
                    'warehouse_name' => $warehouse->name,
                    'warehouse_code' => $warehouse->code,
                    'is_active' => $warehouse->is_active,
                ]),
                'ip_address' => $request->ip(),
            ]);
            
            DB::commit();
            
            return redirect()->route('warehouses.index')
                ->with('success', 'Warehouse created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()->with('error', 'Error creating warehouse: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified warehouse.
     */
    public function show(Warehouse $warehouse)
    {
        $warehouse->load('createdBy');
        
        // Get pending returns for this warehouse
        $pendingReturns = ReturnRequest::with(['user', 'product', 'order'])
            ->where('warehouse_id', $warehouse->id)
            ->whereIn('status', [
                ReturnRequest::STATUS_SHIPPED,
                ReturnRequest::STATUS_RECEIVED,
                ReturnRequest::STATUS_INSPECTED
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'pending_page');
        
        // Get processed returns for this warehouse
        $processedReturns = ReturnRequest::with(['user', 'product', 'order'])
            ->where('warehouse_id', $warehouse->id)
            ->whereIn('status', [
                ReturnRequest::STATUS_APPROVED_FOR_REFUND,
                ReturnRequest::STATUS_REFUNDED,
                ReturnRequest::STATUS_COMPLETED,
                ReturnRequest::STATUS_REJECTED_AFTER_INSPECTION
            ])
            ->orderBy('updated_at', 'desc')
            ->paginate(10, ['*'], 'processed_page');
        
        // Get warehouse statistics
        $stats = [
            'pending_returns' => ReturnRequest::where('warehouse_id', $warehouse->id)
                ->whereIn('status', [
                    ReturnRequest::STATUS_SHIPPED,
                    ReturnRequest::STATUS_RECEIVED
                ])
                ->count(),
            'processed_today' => ReturnRequest::where('warehouse_id', $warehouse->id)
                ->whereIn('status', [
                    ReturnRequest::STATUS_INSPECTED,
                    ReturnRequest::STATUS_APPROVED_FOR_REFUND,
                    ReturnRequest::STATUS_REJECTED_AFTER_INSPECTION
                ])
                ->whereDate('updated_at', today())
                ->count(),
            'total_processed' => ReturnRequest::where('warehouse_id', $warehouse->id)
                ->whereIn('status', [
                    ReturnRequest::STATUS_APPROVED_FOR_REFUND,
                    ReturnRequest::STATUS_REFUNDED,
                    ReturnRequest::STATUS_COMPLETED,
                    ReturnRequest::STATUS_REJECTED_AFTER_INSPECTION
                ])
                ->count(),
            'avg_processing_time' => DB::table('return_requests')
                ->where('warehouse_id', $warehouse->id)
                ->whereNotNull('received_at')
                ->whereNotNull('inspected_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, received_at, inspected_at)) as avg_hours')
                ->first()->avg_hours ?? 0,
        ];
        
        return view('warehouses.show', compact('warehouse', 'pendingReturns', 'processedReturns', 'stats'));
    }

    /**
     * Show the form for editing the specified warehouse.
     */
    public function edit(Warehouse $warehouse)
    {
        return view('warehouses.edit', compact('warehouse'));
    }

    /**
     * Update the specified warehouse in storage.
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:warehouses,code,' . $warehouse->id,
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:20',
            'is_active' => 'boolean',
            'can_process_returns' => 'boolean',
            'can_process_refurbishment' => 'boolean',
        ]);
        
        // Start a transaction
        DB::beginTransaction();
        
        try {
            // Store the old warehouse data for audit log
            $oldData = [
                'name' => $warehouse->name,
                'code' => $warehouse->code,
                'is_active' => $warehouse->is_active,
                'can_process_returns' => $warehouse->can_process_returns,
                'can_process_refurbishment' => $warehouse->can_process_refurbishment,
            ];
            
            // Update the warehouse
            $warehouse->update([
                'name' => $validated['name'],
                'code' => strtoupper($validated['code']),
                'address' => $validated['address'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'zip_code' => $validated['zip_code'],
                'country' => $validated['country'],
                'contact_name' => $validated['contact_name'],
                'contact_email' => $validated['contact_email'],
                'contact_phone' => $validated['contact_phone'],
                'is_active' => $request->has('is_active'),
                'can_process_returns' => $request->has('can_process_returns'),
                'can_process_refurbishment' => $request->has('can_process_refurbishment'),
                'updated_by' => Auth::id(),
            ]);
            
            // Log the warehouse update
            ReturnAuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'warehouse_updated',
                'entity_type' => 'warehouse',
                'entity_id' => $warehouse->id,
                'details' => json_encode([
                    'warehouse_name' => $warehouse->name,
                    'warehouse_code' => $warehouse->code,
                    'changes' => [
                        'old' => $oldData,
                        'new' => [
                            'name' => $warehouse->name,
                            'code' => $warehouse->code,
                            'is_active' => $warehouse->is_active,
                            'can_process_returns' => $warehouse->can_process_returns,
                            'can_process_refurbishment' => $warehouse->can_process_refurbishment,
                        ],
                    ],
                ]),
                'ip_address' => $request->ip(),
            ]);
            
            DB::commit();
            
            return redirect()->route('warehouses.index')
                ->with('success', 'Warehouse updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()->with('error', 'Error updating warehouse: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified warehouse from storage.
     */
    public function destroy(Request $request, Warehouse $warehouse)
    {
        // Check if the warehouse has any associated return requests
        $hasReturns = ReturnRequest::where('warehouse_id', $warehouse->id)->exists();
        
        if ($hasReturns) {
            return back()->with('error', 'This warehouse cannot be deleted because it has associated return requests.');
        }
        
        // Start a transaction
        DB::beginTransaction();
        
        try {
            // Log the warehouse deletion
            ReturnAuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'warehouse_deleted',
                'entity_type' => 'warehouse',
                'entity_id' => $warehouse->id,
                'details' => json_encode([
                    'warehouse_name' => $warehouse->name,
                    'warehouse_code' => $warehouse->code,
                    'deleted_at' => now()->format('Y-m-d H:i:s'),
                ]),
                'ip_address' => $request->ip(),
            ]);
            
            // Delete the warehouse
            $warehouse->delete();
            
            DB::commit();
            
            return redirect()->route('warehouses.index')
                ->with('success', 'Warehouse deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Error deleting warehouse: ' . $e->getMessage());
        }
    }
}
