@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 bg-light p-3 min-vh-100">
            <h5 class="mb-3">Return Management</h5>
            <div class="list-group mb-4">
                <a href="{{ route('admin.returns.index') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-list-ul me-2"></i> All Returns
                </a>
                <a href="{{ route('admin.returns.index', ['status' => 'pending']) }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-clock me-2"></i> Pending
                </a>
                <a href="{{ route('admin.returns.index', ['status' => 'approved']) }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-check me-2"></i> Approved
                </a>
                <a href="{{ route('admin.returns.index', ['status' => 'in_transit']) }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-shipping-fast me-2"></i> In Transit
                </a>
                <a href="{{ route('admin.returns.index', ['status' => 'received']) }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-box-open me-2"></i> Received
                </a>
                <a href="{{ route('admin.returns.index', ['status' => 'inspected']) }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-search me-2"></i> Inspected
                </a>
                <a href="{{ route('admin.returns.index', ['status' => 'refund_processed']) }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-money-bill-wave me-2"></i> Refunded
                </a>
                <a href="{{ route('admin.returns.index', ['status' => 'rejected']) }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-times me-2"></i> Rejected
                </a>
            </div>
            
            <h5 class="mb-3">Analytics</h5>
            <div class="list-group">
                <a href="{{ route('admin.returns.reports') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-chart-bar me-2"></i> Reports & Analytics
                </a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3>Return Request Details</h3>
                <a href="{{ route('admin.returns.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Returns
                </a>
            </div>
            
            <!-- Alerts -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <!-- Return Status Card -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Return #{{ $returnRequest->rma_number }}</h5>
                        <span class="badge 
                            @if($returnRequest->status == 'pending') bg-warning text-dark
                            @elseif($returnRequest->status == 'approved') bg-info
                            @elseif($returnRequest->status == 'rejected') bg-danger
                            @elseif($returnRequest->status == 'in_transit') bg-primary
                            @elseif($returnRequest->status == 'received') bg-secondary
                            @elseif($returnRequest->status == 'inspected') bg-dark
                            @elseif($returnRequest->status == 'refund_pending') bg-warning text-dark
                            @elseif($returnRequest->status == 'refund_processed') bg-success
                            @elseif($returnRequest->status == 'completed') bg-success
                            @elseif($returnRequest->status == 'cancelled') bg-danger
                            @endif">
                            {{ ucwords(str_replace('_', ' ', $returnRequest->status)) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Return Information</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 40%">RMA Number</th>
                                    <td>{{ $returnRequest->rma_number }}</td>
                                </tr>
                                <tr>
                                    <th>Created Date</th>
                                    <td>{{ $returnRequest->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Return Category</th>
                                    <td>{{ ucwords(str_replace('_', ' ', $returnRequest->return_category)) }}</td>
                                </tr>
                                <tr>
                                    <th>Return Reason</th>
                                    <td>{{ $returnRequest->return_reason }}</td>
                                </tr>
                                <tr>
                                    <th>Quantity</th>
                                    <td>{{ $returnRequest->quantity }}</td>
                                </tr>
                                @if($returnRequest->admin_notes)
                                <tr>
                                    <th>Admin Notes</th>
                                    <td>{{ $returnRequest->admin_notes }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">Customer & Order Information</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 40%">Customer</th>
                                    <td>
                                        @if($returnRequest->user)
                                            {{ $returnRequest->user->name }}<br>
                                            <small class="text-muted">{{ $returnRequest->user->email }}</small>
                                        @else
                                            <span class="text-muted">Unknown User</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Order Number</th>
                                    <td>
                                        @if($returnRequest->order)
                                            {{ $returnRequest->order->order_number }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Product</th>
                                    <td>
                                        @if($returnRequest->product)
                                            {{ $returnRequest->product->name }}<br>
                                            <small class="text-muted">SKU: {{ $returnRequest->product->sku }}</small>
                                        @else
                                            <span class="text-muted">Unknown Product</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Product Price</th>
                                    <td>
                                        @if($returnRequest->product)
                                            ${{ number_format($returnRequest->product->price, 2) }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Total Return Value</th>
                                    <td>
                                        @if($returnRequest->product)
                                            ${{ number_format($returnRequest->product->price * $returnRequest->quantity, 2) }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Images -->
            @if($returnRequest->images && count(json_decode($returnRequest->images)) > 0)
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Return Images</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach(json_decode($returnRequest->images) as $image)
                            <div class="col-md-3 mb-3">
                                <a href="{{ asset('storage/' . $image) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $image) }}" alt="Return Image" class="img-fluid img-thumbnail">
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Shipping Information -->
            @if($returnRequest->returnShipment)
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Shipping Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 40%">Carrier</th>
                                    <td>{{ $returnRequest->returnShipment->shipping_carrier }}</td>
                                </tr>
                                <tr>
                                    <th>Tracking Number</th>
                                    <td>
                                        @if($returnRequest->returnShipment->tracking_number)
                                            {{ $returnRequest->returnShipment->tracking_number }}
                                        @else
                                            <span class="text-muted">Not yet assigned</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge 
                                            @if($returnRequest->returnShipment->status == 'label_created') bg-info
                                            @elseif($returnRequest->returnShipment->status == 'in_transit') bg-primary
                                            @elseif($returnRequest->returnShipment->status == 'delivered') bg-success
                                            @endif">
                                            {{ ucwords(str_replace('_', ' ', $returnRequest->returnShipment->status)) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Destination Warehouse</th>
                                    <td>
                                        @if($returnRequest->processingWarehouse)
                                            {{ $returnRequest->processingWarehouse->name }} ({{ $returnRequest->processingWarehouse->location }})
                                        @else
                                            <span class="text-muted">Not assigned</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 40%">Estimated Delivery</th>
                                    <td>
                                        @if($returnRequest->returnShipment->estimated_delivery_date)
                                            {{ \Carbon\Carbon::parse($returnRequest->returnShipment->estimated_delivery_date)->format('M d, Y') }}
                                        @else
                                            <span class="text-muted">Not available</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Actual Delivery</th>
                                    <td>
                                        @if($returnRequest->returnShipment->actual_delivery_date)
                                            {{ \Carbon\Carbon::parse($returnRequest->returnShipment->actual_delivery_date)->format('M d, Y') }}
                                        @else
                                            <span class="text-muted">Not delivered yet</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Shipping Label</th>
                                    <td>
                                        @if($returnRequest->returnShipment->shipping_label_url)
                                            <a href="{{ asset('storage/' . $returnRequest->returnShipment->shipping_label_url) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                <i class="fas fa-file-pdf me-1"></i> View Label
                                            </a>
                                        @else
                                            <span class="text-muted">No label available</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Refund Information -->
            @if($returnRequest->returnRefund)
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Refund Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 40%">Refund Method</th>
                                    <td>{{ ucwords(str_replace('_', ' ', $returnRequest->returnRefund->refund_method)) }}</td>
                                </tr>
                                <tr>
                                    <th>Refund Amount</th>
                                    <td>${{ number_format($returnRequest->returnRefund->refund_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Refund Date</th>
                                    <td>{{ \Carbon\Carbon::parse($returnRequest->returnRefund->refund_date)->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Refund Status</th>
                                    <td>
                                        <span class="badge 
                                            @if($returnRequest->returnRefund->refund_status == 'pending') bg-warning text-dark
                                            @elseif($returnRequest->returnRefund->refund_status == 'processed') bg-success
                                            @elseif($returnRequest->returnRefund->refund_status == 'failed') bg-danger
                                            @endif">
                                            {{ ucwords($returnRequest->returnRefund->refund_status) }}
                                        </span>
                                    </td>
                                </tr>
                                @if($returnRequest->returnRefund->notes)
                                <tr>
                                    <th>Notes</th>
                                    <td>{{ $returnRequest->returnRefund->notes }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Audit Trail -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Audit Trail</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Action</th>
                                    <th>User</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($auditTrail as $log)
                                    <tr>
                                        <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <span class="badge 
                                                @if(in_array($log->action, ['created', 'updated'])) bg-info
                                                @elseif(in_array($log->action, ['approved', 'refund_processed', 'completed'])) bg-success
                                                @elseif(in_array($log->action, ['rejected', 'cancelled'])) bg-danger
                                                @else bg-secondary
                                                @endif">
                                                {{ ucwords(str_replace('_', ' ', $log->action)) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($log->user)
                                                {{ $log->user->name }}
                                                <div><small class="text-muted">{{ ucfirst($log->user->role) }}</small></div>
                                            @else
                                                <span class="text-muted">System</span>
                                            @endif
                                        </td>
                                        <td>{{ $log->notes ?: 'No notes provided' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @if($returnRequest->status == 'pending')
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                                <i class="fas fa-check me-1"></i> Approve Return
                            </button>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="fas fa-times me-1"></i> Reject Return
                            </button>
                        @endif
                        
                        @if(in_array($returnRequest->status, ['approved', 'in_transit', 'received']))
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                                <i class="fas fa-sync-alt me-1"></i> Update Status
                            </button>
                        @endif
                        
                        @if($returnRequest->status == 'inspected')
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#processRefundModal">
                                <i class="fas fa-money-bill-wave me-1"></i> Process Refund
                            </button>
                        @endif
                        
                        @if($returnRequest->returnShipment && $returnRequest->returnShipment->shipping_label_url)
                            <a href="{{ asset('storage/' . $returnRequest->returnShipment->shipping_label_url) }}" class="btn btn-outline-primary" target="_blank">
                                <i class="fas fa-file-pdf me-1"></i> View Shipping Label
                            </a>
                        @endif
                        
                        <a href="{{ route('admin.returns.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Returns
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
@if($returnRequest->status == 'pending')
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.returns.process', $returnRequest) }}" method="POST">
                @csrf
                <input type="hidden" name="action" value="approve">
                
                <div class="modal-header">
                    <h5 class="modal-title">Approve Return Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>You are about to approve return request <strong>{{ $returnRequest->rma_number }}</strong>.</p>
                    
                    <div class="mb-3">
                        <label for="warehouse_id" class="form-label">Processing Warehouse</label>
                        <select name="warehouse_id" id="warehouse_id" class="form-select" required>
                            <option value="">Select Warehouse</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }} ({{ $warehouse->location }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Admin Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Optional notes for this approval"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve Return</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.returns.process', $returnRequest) }}" method="POST">
                @csrf
                <input type="hidden" name="action" value="reject">
                
                <div class="modal-header">
                    <h5 class="modal-title">Reject Return Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>You are about to reject return request <strong>{{ $returnRequest->rma_number }}</strong>.</p>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Rejection Reason</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Provide a reason for rejection" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Return</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Update Status Modal -->
@if(in_array($returnRequest->status, ['approved', 'in_transit', 'received']))
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.returns.update-status', $returnRequest) }}" method="POST">
                @csrf
                
                <div class="modal-header">
                    <h5 class="modal-title">Update Return Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Update status for return request <strong>{{ $returnRequest->rma_number }}</strong>.</p>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select" required>
                            @if($returnRequest->status == 'approved')
                                <option value="in_transit">In Transit</option>
                            @endif
                            
                            @if(in_array($returnRequest->status, ['approved', 'in_transit']))
                                <option value="received">Received</option>
                            @endif
                            
                            @if(in_array($returnRequest->status, ['approved', 'in_transit', 'received']))
                                <option value="inspected">Inspected</option>
                            @endif
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Optional notes for this status update"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Process Refund Modal -->
@if($returnRequest->status == 'inspected')
<div class="modal fade" id="processRefundModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.returns.process-refund', $returnRequest) }}" method="POST">
                @csrf
                
                <div class="modal-header">
                    <h5 class="modal-title">Process Refund</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Process refund for return request <strong>{{ $returnRequest->rma_number }}</strong>.</p>
                    
                    <div class="mb-3">
                        <label for="refund_method" class="form-label">Refund Method</label>
                        <select name="refund_method" id="refund_method" class="form-select" required>
                            <option value="original_payment">Original Payment Method</option>
                            <option value="store_credit">Store Credit</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="refund_amount" class="form-label">Refund Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="refund_amount" id="refund_amount" class="form-control" value="{{ $returnRequest->product ? $returnRequest->product->price * $returnRequest->quantity : 0 }}" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Optional notes for this refund"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Process Refund</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
