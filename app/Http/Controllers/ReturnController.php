<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ReturnRequest;
use App\Models\ReturnShipment;
use App\Models\ReturnRefund;
use App\Models\ReturnPolicy;
use App\Models\ReturnNotification;
use App\Models\ReturnAuditLog;
use App\Models\ReturnSurvey;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReturnController extends Controller
{
    /**
     * Display a listing of return requests for the current user
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isCustomer()) {
            $returnRequests = ReturnRequest::where('user_id', $user->id)
                ->with(['product', 'order', 'returnShipment', 'returnRefund'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            // For staff users, show all returns with filters based on role
            $query = ReturnRequest::with(['product', 'order', 'user', 'returnShipment', 'returnRefund']);
            
            if ($user->isWarehouseStaff()) {
                // Warehouse staff only see returns that are in transit or received
                $query->whereIn('status', [
                    ReturnRequest::STATUS_APPROVED,
                    ReturnRequest::STATUS_SHIPPED,
                    ReturnRequest::STATUS_RECEIVED,
                    ReturnRequest::STATUS_INSPECTED
                ]);
            } elseif ($user->isFinance()) {
                // Finance team only see returns that need refund processing
                $query->whereIn('status', [
                    ReturnRequest::STATUS_INSPECTED,
                    ReturnRequest::STATUS_REFUND_PENDING,
                    ReturnRequest::STATUS_REFUND_PROCESSED
                ]);
            } elseif ($user->isSupportAgent()) {
                // Support agents see returns that need approval or have issues
                $query->whereIn('status', [
                    ReturnRequest::STATUS_PENDING,
                    ReturnRequest::STATUS_APPROVED,
                    ReturnRequest::STATUS_REJECTED
                ]);
            }
            
            $returnRequests = $query->orderBy('created_at', 'desc')->paginate(20);
        }
        
        return view('returns.index', compact('returnRequests'));
    }
    
    /**
     * Show the form for creating a new return request
     */
    public function create(Request $request)
    {
        $orderId = $request->query('order_id');
        $productId = $request->query('product_id');
        
        $order = null;
        $product = null;
        
        if ($orderId) {
            $order = Order::with('orderItems.product')->findOrFail($orderId);
            
            // Any logged-in user can initiate a return request
            // The review process will be handled by admins
        }
        
        if ($productId) {
            $product = Product::findOrFail($productId);
        }
        
        $returnCategories = ReturnRequest::getReturnCategories();
        
        return view('returns.create', compact('order', 'product', 'returnCategories'));
    }
    
    /**
     * Store a newly created return request
     */
    public function store(Request $request)
    {
        // Validate inputs
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'return_reason' => 'required|string|max:1000',
            'return_category' => 'required|string|in:' . implode(',', ReturnRequest::getReturnCategories()),
            'description' => 'nullable|string|max:2000',
            'evidence_images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'refund_method' => 'required|string|in:' . implode(',', ReturnRefund::getRefundMethods()),
        ]);
        
        // Get the order details
        $order = Order::findOrFail($validated['order_id']);
        // Any logged-in user can submit a return request
        // Admin review will determine if it's valid
        
        // Check if the product is part of the order
        $orderItem = OrderItem::where('order_id', $validated['order_id'])
            ->where('product_id', $validated['product_id'])
            ->firstOrFail();
        
        // Check if the product is eligible for return
        $product = Product::findOrFail($validated['product_id']);
        
        // Get applicable return policy
        $returnPolicy = ReturnPolicy::where('applies_to_product_id', $product->id)
            ->orWhere('applies_to_category', $product->category)
            ->where('is_active', true)
            ->first();
            
        if (!$returnPolicy) {
            // Use default return policy if no specific policy exists
            $returnPolicy = ReturnPolicy::where('name', 'Default Policy')
                ->where('is_active', true)
                ->first();
        }
        
        // Check if return period has passed
        $orderDate = $order->order_date;
        $returnPeriodDays = $returnPolicy ? $returnPolicy->return_period_days : 30;
        $returnDeadline = $orderDate->addDays($returnPeriodDays);
        
        if (now()->gt($returnDeadline)) {
            return back()->with('error', 'The return period for this product has expired.');
        }
        
        // Handle evidence image uploads
        $evidenceImages = [];
        if ($request->hasFile('evidence_images')) {
            foreach ($request->file('evidence_images') as $image) {
                $path = $image->store('return-evidence', 'public');
                $evidenceImages[] = $path;
            }
        }
        
        // Generate RMA number
        $rmaNumber = 'RMA-' . strtoupper(Str::random(8));
        
        // Create the return request
        $returnRequest = ReturnRequest::create([
            'user_id' => Auth::id(),
            'product_id' => $validated['product_id'],
            'order_id' => $validated['order_id'],
            'rma_number' => $rmaNumber,
            'return_reason' => $validated['return_reason'],
            'return_category' => $validated['return_category'],
            'description' => $validated['description'] ?? null,
            'evidence_images' => $evidenceImages,
            'status' => ReturnRequest::STATUS_PENDING,
            'refund_method' => $validated['refund_method'],
            'refund_amount' => $orderItem->unit_price,
        ]);
        
        // Create audit log entry
        ReturnAuditLog::logAction(
            $returnRequest,
            ReturnAuditLog::ACTION_CREATED,
            'Return request created by customer'
        );
        
        // Send notification
        ReturnNotification::createStatusNotification(
            $returnRequest,
            ReturnNotification::TYPE_RETURN_CREATED
        )->send();
        
        return redirect()->route('returns.index')
            ->with('success', 'Return request submitted successfully. Your RMA number is ' . $rmaNumber);
    }
    
    /**
     * Display the specified return request
     */
    public function show(ReturnRequest $returnRequest)
    {
        // Check if user is authorized to view this return
        $user = Auth::user();
        if ($user->isCustomer() && $returnRequest->user_id !== $user->id) {
            abort(403, 'You are not authorized to view this return request.');
        }
        
        $returnRequest->load([
            'product', 
            'order', 
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
        
        return view('returns.show', compact('returnRequest', 'auditTrail'));
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
        
        $user = Auth::user();
        
        // Check if user is authorized to process returns
        if (!$user->isAdmin() && !$user->isSupportAgent()) {
            abort(403, 'You are not authorized to process return requests.');
        }
        
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
            $previousStatus
        );
        
        return redirect()->route('returns.show', $returnRequest)
            ->with('success', $message);
    }
    
    /**
     * Update return status (for warehouse staff)
     */
    public function updateStatus(Request $request, ReturnRequest $returnRequest)
    {
        // Validate inputs
        $validated = $request->validate([
            'status' => 'required|string|in:' . implode(',', [
                ReturnRequest::STATUS_RECEIVED,
                ReturnRequest::STATUS_INSPECTED,
                ReturnRequest::STATUS_REFUND_PENDING,
            ]),
            'quality_check_result' => 'required_if:status,' . ReturnRequest::STATUS_INSPECTED . '|nullable|string|in:' . implode(',', ReturnRequest::getQualityCheckResults()),
            'warehouse_notes' => 'nullable|string|max:1000',
        ]);
        
        $user = Auth::user();
        
        // Check if user is authorized to update return status
        if (!$user->isAdmin() && !$user->isWarehouseStaff()) {
            abort(403, 'You are not authorized to update return status.');
        }
        
        // Check if the status transition is valid
        $validTransitions = [
            ReturnRequest::STATUS_SHIPPED => [ReturnRequest::STATUS_RECEIVED],
            ReturnRequest::STATUS_RECEIVED => [ReturnRequest::STATUS_INSPECTED],
            ReturnRequest::STATUS_INSPECTED => [ReturnRequest::STATUS_REFUND_PENDING],
        ];
        
        if (!isset($validTransitions[$returnRequest->status]) || 
            !in_array($validated['status'], $validTransitions[$returnRequest->status])) {
            return back()->with('error', 'Invalid status transition.');
        }
        
        $previousStatus = $returnRequest->status;
        $returnRequest->status = $validated['status'];
        $returnRequest->warehouse_notes = $validated['warehouse_notes'];
        
        if ($validated['status'] === ReturnRequest::STATUS_INSPECTED) {
            $returnRequest->quality_check_result = $validated['quality_check_result'];
        }
        
        $returnRequest->save();
        
        // Update shipment status if applicable
        if ($validated['status'] === ReturnRequest::STATUS_RECEIVED) {
            $returnShipment = $returnRequest->returnShipment;
            if ($returnShipment) {
                $returnShipment->status = ReturnShipment::STATUS_DELIVERED;
                $returnShipment->actual_delivery_date = now();
                $returnShipment->save();
            }
            
            // Send notification
            ReturnNotification::createStatusNotification(
                $returnRequest,
                ReturnNotification::TYPE_RETURN_RECEIVED
            )->send();
        } elseif ($validated['status'] === ReturnRequest::STATUS_INSPECTED) {
            // Send notification
            ReturnNotification::createStatusNotification(
                $returnRequest,
                ReturnNotification::TYPE_RETURN_INSPECTED
            )->send();
        }
        
        // Create audit log entry
        $actionMap = [
            ReturnRequest::STATUS_RECEIVED => ReturnAuditLog::ACTION_RECEIVED,
            ReturnRequest::STATUS_INSPECTED => ReturnAuditLog::ACTION_INSPECTED,
            ReturnRequest::STATUS_REFUND_PENDING => 'marked_for_refund',
        ];
        
        ReturnAuditLog::logAction(
            $returnRequest,
            $actionMap[$validated['status']],
            $validated['warehouse_notes'],
            $previousStatus
        );
        
        return redirect()->route('returns.show', $returnRequest)
            ->with('success', 'Return status updated successfully.');
    }
    
    /**
     * Process refund for a return request
     */
    public function processRefund(Request $request, ReturnRequest $returnRequest)
    {
        // Validate inputs
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'restocking_fee' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        $user = Auth::user();
        
        // Check if user is authorized to process refunds
        if (!$user->isAdmin() && !$user->isFinance()) {
            abort(403, 'You are not authorized to process refunds.');
        }
        
        // Check if return is in a refundable state
        if ($returnRequest->status !== ReturnRequest::STATUS_REFUND_PENDING &&
            $returnRequest->status !== ReturnRequest::STATUS_INSPECTED) {
            return back()->with('error', 'This return is not ready for refund processing.');
        }
        
        $previousStatus = $returnRequest->status;
        
        // Create or update refund record
        $returnRefund = $returnRequest->returnRefund ?? new ReturnRefund();
        $returnRefund->return_request_id = $returnRequest->id;
        $returnRefund->amount = $validated['amount'];
        $returnRefund->refund_method = $returnRequest->refund_method;
        $returnRefund->status = ReturnRefund::STATUS_COMPLETED;
        $returnRefund->refund_date = now();
        $returnRefund->restocking_fee_applied = $validated['restocking_fee'] ?? 0;
        $returnRefund->notes = $validated['notes'];
        $returnRefund->processed_by = $user->id;
        $returnRefund->transaction_id = 'TXN-' . strtoupper(Str::random(10)); // Simulated transaction ID
        $returnRefund->save();
        
        // Update return request status
        $returnRequest->status = ReturnRequest::STATUS_REFUND_PROCESSED;
        $returnRequest->save();
        
        // Send notification
        ReturnNotification::createStatusNotification(
            $returnRequest,
            ReturnNotification::TYPE_REFUND_PROCESSED
        )->send();
        
        // Create audit log entry
        ReturnAuditLog::logAction(
            $returnRequest,
            ReturnAuditLog::ACTION_REFUNDED,
            $validated['notes'],
            $previousStatus
        );
        
        return redirect()->route('returns.show', $returnRequest)
            ->with('success', 'Refund processed successfully.');
    }
    
    /**
     * Complete a return request
     */
    public function complete(ReturnRequest $returnRequest)
    {
        $user = Auth::user();
        
        // Check if user is authorized to complete returns
        if (!$user->isAdmin() && !$user->isWarehouseStaff() && !$user->isFinance()) {
            abort(403, 'You are not authorized to complete return requests.');
        }
        
        // Check if return is in a completable state
        if ($returnRequest->status !== ReturnRequest::STATUS_REFUND_PROCESSED) {
            return back()->with('error', 'This return cannot be completed because it is not in the correct state.');
        }
        
        $previousStatus = $returnRequest->status;
        
        // Update return request status
        $returnRequest->status = ReturnRequest::STATUS_COMPLETED;
        $returnRequest->save();
        
        // Update inventory if applicable
        if ($returnRequest->quality_check_result === ReturnRequest::QUALITY_GOOD) {
            // Add item back to inventory
            $warehouse = $returnRequest->processingWarehouse;
            if ($warehouse) {
                $inventoryItem = $warehouse->inventoryItems()
                    ->where('product_id', $returnRequest->product_id)
                    ->first();
                
                if ($inventoryItem) {
                    $inventoryItem->quantity += 1;
                    $inventoryItem->quantity_returned += 1;
                    $inventoryItem->save();
                }
            }
        }
        
        // Send notification
        ReturnNotification::createStatusNotification(
            $returnRequest,
            ReturnNotification::TYPE_RETURN_COMPLETED
        )->send();
        
        // Generate customer satisfaction survey
        ReturnSurvey::generateSurvey($returnRequest);
        
        // Create audit log entry
        ReturnAuditLog::logAction(
            $returnRequest,
            ReturnAuditLog::ACTION_COMPLETED,
            'Return process completed',
            $previousStatus
        );
        
        return redirect()->route('returns.show', $returnRequest)
            ->with('success', 'Return process completed successfully.');
    }
    
    /**
     * Cancel a return request (customer only)
     */
    public function cancel(ReturnRequest $returnRequest)
    {
        $user = Auth::user();
        
        // Check if user is authorized to cancel this return
        if ($user->isCustomer() && $returnRequest->user_id !== $user->id) {
            abort(403, 'You are not authorized to cancel this return request.');
        }
        
        // Check if return is in a cancellable state
        if (!in_array($returnRequest->status, [
            ReturnRequest::STATUS_PENDING,
            ReturnRequest::STATUS_APPROVED
        ])) {
            return back()->with('error', 'This return cannot be cancelled because it has already been processed.');
        }
        
        $previousStatus = $returnRequest->status;
        
        // Update return request status
        $returnRequest->status = ReturnRequest::STATUS_CANCELLED;
        $returnRequest->save();
        
        // Create audit log entry
        ReturnAuditLog::logAction(
            $returnRequest,
            ReturnAuditLog::ACTION_CANCELLED,
            'Return request cancelled by ' . ($user->isCustomer() ? 'customer' : 'staff'),
            $previousStatus
        );
        
        return redirect()->route('returns.show', $returnRequest)
            ->with('success', 'Return request cancelled successfully.');
    }
    
    /**
     * Submit customer satisfaction survey
     */
    public function submitSurvey(Request $request, ReturnRequest $returnRequest)
    {
        // Validate inputs
        $validated = $request->validate([
            'overall_satisfaction' => 'required|integer|min:1|max:5',
            'process_rating' => 'required|integer|min:1|max:5',
            'support_rating' => 'required|integer|min:1|max:5',
            'timeliness_rating' => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string|max:1000',
            'suggestions' => 'nullable|string|max:1000',
            'would_recommend' => 'required|boolean',
        ]);
        
        $user = Auth::user();
        
        // Check if user is authorized to submit survey for this return
        if ($returnRequest->user_id !== $user->id) {
            abort(403, 'You are not authorized to submit a survey for this return request.');
        }
        
        // Check if return is completed
        if ($returnRequest->status !== ReturnRequest::STATUS_COMPLETED) {
            return back()->with('error', 'You can only submit a survey for completed returns.');
        }
        
        // Find existing survey or create new one
        $survey = ReturnSurvey::where('return_request_id', $returnRequest->id)
            ->where('user_id', $user->id)
            ->first();
            
        if (!$survey) {
            $survey = new ReturnSurvey();
            $survey->return_request_id = $returnRequest->id;
            $survey->user_id = $user->id;
        }
        
        // Update survey data
        $survey->overall_satisfaction = $validated['overall_satisfaction'];
        $survey->process_rating = $validated['process_rating'];
        $survey->support_rating = $validated['support_rating'];
        $survey->timeliness_rating = $validated['timeliness_rating'];
        $survey->comments = $validated['comments'];
        $survey->suggestions = $validated['suggestions'];
        $survey->would_recommend = $validated['would_recommend'];
        $survey->completed_at = now();
        $survey->save();
        
        return redirect()->route('returns.show', $returnRequest)
            ->with('success', 'Thank you for your feedback!');
    }
}