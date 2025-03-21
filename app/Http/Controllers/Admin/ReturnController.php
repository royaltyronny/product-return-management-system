<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ReturnAuditLog;
use App\Models\ReturnNotification;
use App\Models\ReturnRefund;
use App\Models\ReturnRequest;
use App\Models\ReturnShipment;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    /**
     * Constructor - ensure only admin users can access these routes
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isAdmin() && !Auth::user()->isSupportAgent()) {
                abort(403, 'Unauthorized access');
            }
            return $next($request);
        });
    }
    
    /**
     * Display a listing of all return requests with filtering options
     */
    public function index(Request $request)
    {
        $query = ReturnRequest::with(['product', 'order', 'user', 'returnShipment', 'returnRefund']);
        
        // Apply filters if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('rma_number', 'like', "%{$search}%")
                  ->orWhere('return_reason', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('order', function($q) use ($search) {
                      $q->where('order_number', 'like', "%{$search}%");
                  });
            });
        }
        
        // Get statistics for dashboard
        $stats = [
            'total' => ReturnRequest::count(),
            'pending' => ReturnRequest::where('status', ReturnRequest::STATUS_PENDING)->count(),
            'approved' => ReturnRequest::where('status', ReturnRequest::STATUS_APPROVED)->count(),
            'rejected' => ReturnRequest::where('status', ReturnRequest::STATUS_REJECTED)->count(),
            'in_transit' => ReturnRequest::where('status', ReturnRequest::STATUS_IN_TRANSIT)->count(),
            'received' => ReturnRequest::where('status', ReturnRequest::STATUS_RECEIVED)->count(),
            'inspected' => ReturnRequest::where('status', ReturnRequest::STATUS_INSPECTED)->count(),
            'refunded' => ReturnRequest::where('status', ReturnRequest::STATUS_REFUNDED)->count(),
        ];
        
        // Get return categories for filtering
        $returnCategories = ReturnRequest::getReturnCategories();
        
        // Get warehouses for processing
        $warehouses = Warehouse::all();
        
        // Get the return requests with pagination
        $returnRequests = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('admin.returns.index', compact(
            'returnRequests', 
            'stats', 
            'returnCategories', 
            'warehouses'
        ));
    }
    
    /**
     * Show detailed view of a return request with all actions
     */
    public function show(ReturnRequest $returnRequest)
    {
        $returnRequest->load([
            'product', 
            'order.orderItems', 
            'user', 
            'returnShipment', 
            'returnRefund',
            'processingWarehouse'
        ]);
        
        // Get audit trail
        $auditTrail = ReturnAuditLog::where('return_request_id', $returnRequest->id)
            ->with('user:id,name,email,role')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get warehouses for processing
        $warehouses = Warehouse::all();
        
        return view('admin.returns.show', compact('returnRequest', 'auditTrail', 'warehouses'));
    }
    
    /**
     * Process a return request (approve/reject)
     */
    public function process(Request $request, ReturnRequest $returnRequest)
    {
        // Validate inputs
        $validated = $request->validate([
            'action' => 'required|string|in:approve,reject',
            'notes' => 'nullable|string|max:1000',
            'warehouse_id' => 'required_if:action,approve|exists:warehouses,id',
        ]);
        
        // Check if return is in a processable state
        if ($returnRequest->status !== ReturnRequest::STATUS_PENDING) {
            return back()->with('error', 'This return request cannot be processed because it is not in a pending state.');
        }
        
        $previousStatus = $returnRequest->status;
        
        if ($validated['action'] === 'approve') {
            // Approve the return
            $returnRequest->status = ReturnRequest::STATUS_APPROVED;
            $returnRequest->admin_notes = $validated['notes'];
            $returnRequest->processing_warehouse_id = $validated['warehouse_id'];
            $returnRequest->save();
            
            // Create shipping label
            $warehouse = Warehouse::findOrFail($validated['warehouse_id']);
            
            $returnShipment = ReturnShipment::create([
                'return_request_id' => $returnRequest->id,
                'shipping_carrier' => 'Default Carrier', // This would be configurable in a real system
                'status' => ReturnShipment::STATUS_LABEL_CREATED,
                'destination_warehouse_id' => $warehouse->id,
                'estimated_delivery_date' => now()->addDays(7), // Estimate
            ]);
            
            // Generate shipping label (simulated)
            $labelUrl = 'shipping-labels/rma-' . $returnRequest->rma_number . '.pdf';
            $returnShipment->shipping_label_url = $labelUrl;
            $returnShipment->save();
            
            // Send approval notification
            ReturnNotification::createStatusNotification(
                $returnRequest,
                ReturnNotification::TYPE_RETURN_APPROVED
            )->send();
            
            // Send shipping label notification
            ReturnNotification::createStatusNotification(
                $returnRequest,
                ReturnNotification::TYPE_SHIPPING_LABEL
            )->send();
            
            $message = 'Return request approved successfully. Shipping label has been generated.';
        } else {
            // Reject the return
            $returnRequest->status = ReturnRequest::STATUS_REJECTED;
            $returnRequest->admin_notes = $validated['notes'];
            $returnRequest->save();
            
            // Send rejection notification
            ReturnNotification::createStatusNotification(
                $returnRequest,
                ReturnNotification::TYPE_RETURN_REJECTED
            )->send();
            
            $message = 'Return request rejected.';
        }
        
        // Create audit log entry
        ReturnAuditLog::logAction(
            $returnRequest,
            $validated['action'] === 'approve' ? ReturnAuditLog::ACTION_APPROVED : ReturnAuditLog::ACTION_REJECTED,
            $validated['notes'],
            Auth::user()
        );
        
        return redirect()->route('admin.returns.show', $returnRequest->id)->with('success', $message);
    }
    
    /**
     * Display analytics and reports for return requests
     */
    public function reports(Request $request)
    {
        // Set default date range if not provided (last 30 days)
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        
        // Get returns by status
        $returnsByStatus = ReturnRequest::select('status', DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy('status')
            ->get();
        
        // Get returns by category
        $returnsByCategory = ReturnRequest::select('return_category', DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy('return_category')
            ->get();
        
        // Get returns by day for timeline chart
        $returnsByDay = ReturnRequest::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Calculate total refund amount
        $totalRefundAmount = ReturnRefund::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('refund_amount');
        
        // Calculate average processing time (days between creation and completion)
        $avgProcessingTime = ReturnRequest::select(DB::raw("AVG(ROUND(JULIANDAY(updated_at) - JULIANDAY(created_at))) as avg_days"))
            ->where('status', ReturnRequest::STATUS_REFUNDED)
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->first();
        
        // Calculate return rate (returns / orders in the period)
        $totalOrders = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->count();
        
        $totalReturns = ReturnRequest::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->count();
        
        $totalReturnRate = $totalOrders > 0 ? $totalReturns / $totalOrders : 0;
        
        return view('admin.returns.reports', compact(
            'returnsByStatus',
            'returnsByCategory',
            'returnsByDay',
            'totalRefundAmount',
            'avgProcessingTime',
            'totalReturnRate',
            'startDate',
            'endDate'
        ));
    }
    
    /**
     * Export return requests data to CSV
     */
    public function export(Request $request)
    {
        // Set default date range if not provided (last 30 days)
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        
        // Get return requests within date range with relationships
        $returnRequests = ReturnRequest::with(['user:id,name,email', 'order:id,order_number', 'product:id,name'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->get();
        
        // Generate CSV filename
        $filename = 'return_requests_' . $startDate . '_to_' . $endDate . '.csv';
        
        // Create CSV headers
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0'
        ];
        
        // Create a callback to stream the CSV output
        $callback = function() use ($returnRequests) {
            $file = fopen('php://output', 'w');
            
            // Add CSV header row
            fputcsv($file, [
                'RMA Number',
                'Created Date',
                'Status',
                'Customer Name',
                'Customer Email',
                'Order Number',
                'Product',
                'Return Reason',
                'Return Category',
                'Refund Amount',
                'Refund Status',
                'Processing Time (Days)'
            ]);
            
            // Add data rows
            foreach ($returnRequests as $return) {
                // Calculate processing time in days
                $processingTime = null;
                if (in_array($return->status, [ReturnRequest::STATUS_REFUNDED, ReturnRequest::STATUS_COMPLETED])) {
                    $createdDate = new \DateTime($return->created_at);
                    $completedDate = new \DateTime($return->updated_at);
                    $processingTime = $createdDate->diff($completedDate)->days;
                }
                
                // Get refund amount and status if exists
                $refundAmount = null;
                $refundStatus = null;
                if ($return->returnRefund) {
                    $refundAmount = $return->returnRefund->refund_amount;
                    $refundStatus = $return->returnRefund->status;
                }
                
                fputcsv($file, [
                    $return->rma_number,
                    $return->created_at->format('Y-m-d H:i:s'),
                    $return->status,
                    $return->user->name ?? 'N/A',
                    $return->user->email ?? 'N/A',
                    $return->order->order_number ?? 'N/A',
                    $return->product->name ?? 'N/A',
                    $return->return_reason,
                    $return->return_category,
                    $refundAmount,
                    $refundStatus,
                    $processingTime
                ]);
            }
            
            fclose($file);
        };
        
        // Stream the CSV as a download
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Update the status of a return request
     */
    public function updateStatus(Request $request, ReturnRequest $returnRequest)
    {
        // Validate inputs
        $validated = $request->validate([
            'status' => 'required|string|in:' . implode(',', [
                ReturnRequest::STATUS_IN_TRANSIT,
                ReturnRequest::STATUS_RECEIVED,
                ReturnRequest::STATUS_INSPECTED,
                ReturnRequest::STATUS_REFUNDED,
                ReturnRequest::STATUS_COMPLETED,
                ReturnRequest::STATUS_CANCELLED
            ]),
            'notes' => 'nullable|string|max:1000',
        ]);
        
        $previousStatus = $returnRequest->status;
        $returnRequest->status = $validated['status'];
        
        // Add additional processing based on the new status
        switch ($validated['status']) {
            case ReturnRequest::STATUS_RECEIVED:
                $returnRequest->received_date = now();
                
                // Update the return shipment status
                if ($returnRequest->returnShipment) {
                    $returnRequest->returnShipment->status = ReturnShipment::STATUS_DELIVERED;
                    $returnRequest->returnShipment->actual_delivery_date = now();
                    $returnRequest->returnShipment->save();
                }
                
                // Send notification
                ReturnNotification::createStatusNotification(
                    $returnRequest,
                    ReturnNotification::TYPE_RETURN_RECEIVED
                )->send();
                break;
                
            case ReturnRequest::STATUS_INSPECTED:
                $returnRequest->inspection_date = now();
                $returnRequest->inspection_notes = $validated['notes'] ?? 'Item inspected and ready for refund processing.';
                
                // Send notification
                ReturnNotification::createStatusNotification(
                    $returnRequest,
                    ReturnNotification::TYPE_RETURN_INSPECTED
                )->send();
                break;
                
            case ReturnRequest::STATUS_REFUND_PROCESSED:
                // This would typically be handled by the processRefund method
                break;
                
            case ReturnRequest::STATUS_COMPLETED:
                $returnRequest->completed_date = now();
                
                // Send notification
                ReturnNotification::createStatusNotification(
                    $returnRequest,
                    ReturnNotification::TYPE_RETURN_COMPLETED
                )->send();
                break;
        }
        
        $returnRequest->save();
        
        // Create audit log entry
        ReturnAuditLog::logAction(
            $returnRequest,
            ReturnAuditLog::ACTION_STATUS_UPDATED,
            "Status updated from {$previousStatus} to {$validated['status']}. " . ($validated['notes'] ?? ''),
            Auth::user()
        );
        
        return redirect()->route('admin.returns.show', $returnRequest)
            ->with('success', 'Return status updated successfully.');
    }
    
    /**
     * Process a refund for a return request
     */
    public function processRefund(Request $request, ReturnRequest $returnRequest)
    {
        // Validate inputs
        $validated = $request->validate([
            'refund_method' => 'required|string|in:original_payment,store_credit,bank_transfer',
            'refund_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        // Check if return is in a state that can be refunded
        if ($returnRequest->status !== ReturnRequest::STATUS_INSPECTED) {
            return back()->with('error', 'This return request cannot be refunded because it has not been inspected yet.');
        }
        
        // Start a database transaction
        DB::beginTransaction();
        
        try {
            // Create the refund record
            $refund = ReturnRefund::create([
                'return_request_id' => $returnRequest->id,
                'refund_method' => $validated['refund_method'],
                'amount' => $validated['refund_amount'], // Changed from refund_amount to amount to match the database schema
                'refund_date' => now(),
                'status' => ReturnRefund::STATUS_PROCESSED, // Changed from refund_status to status to match the database schema
                'notes' => $validated['notes'],
                'processed_by' => Auth::id(),
            ]);
            
            // Update the return request status
            $returnRequest->status = ReturnRequest::STATUS_REFUNDED;
            $returnRequest->save();
            
            // Update the order item's returned quantity
            $orderItem = OrderItem::where('order_id', $returnRequest->order_id)
                ->where('product_id', $returnRequest->product_id)
                ->first();
                
            if ($orderItem) {
                $orderItem->returned_quantity = $returnRequest->quantity;
                $orderItem->save();
            }
            
            // Send refund notification
            ReturnNotification::createStatusNotification(
                $returnRequest,
                ReturnNotification::TYPE_REFUND_PROCESSED
            )->send();
            
            // Create audit log entry
            ReturnAuditLog::logAction(
                $returnRequest,
                ReturnAuditLog::ACTION_REFUND_PROCESSED,
                "Refund processed: {$validated['refund_method']} - \${$validated['refund_amount']}. " . ($validated['notes'] ?? ''),
                Auth::user()
            );
            
            // Commit the transaction
            DB::commit();
            
            return redirect()->route('admin.returns.show', $returnRequest)
                ->with('success', 'Refund processed successfully.');
                
        } catch (\Exception $e) {
            // Rollback the transaction if something goes wrong
            DB::rollBack();
            
            return back()->with('error', 'Error processing refund: ' . $e->getMessage());
        }
    }
    

    

}
