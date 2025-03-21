@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 bg-light p-3 min-vh-100">
            <h5 class="mb-3">Return Management</h5>
            <div class="list-group mb-4">
                <a href="{{ route('admin.returns.index') }}" class="list-group-item list-group-item-action active">
                    <i class="fas fa-list-ul me-2"></i> All Returns
                </a>
                <a href="{{ route('admin.returns.index', ['status' => 'pending']) }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-clock me-2"></i> Pending
                    <span class="badge bg-warning text-dark float-end">{{ $stats['pending'] }}</span>
                </a>
                <a href="{{ route('admin.returns.index', ['status' => 'approved']) }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-check me-2"></i> Approved
                    <span class="badge bg-info float-end">{{ $stats['approved'] }}</span>
                </a>
                <a href="{{ route('admin.returns.index', ['status' => 'in_transit']) }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-shipping-fast me-2"></i> In Transit
                    <span class="badge bg-primary float-end">{{ $stats['in_transit'] }}</span>
                </a>
                <a href="{{ route('admin.returns.index', ['status' => 'received']) }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-box-open me-2"></i> Received
                    <span class="badge bg-secondary float-end">{{ $stats['received'] }}</span>
                </a>
                <a href="{{ route('admin.returns.index', ['status' => 'inspected']) }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-search me-2"></i> Inspected
                    <span class="badge bg-dark float-end">{{ $stats['inspected'] }}</span>
                </a>
                <a href="{{ route('admin.returns.index', ['status' => 'refund_processed']) }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-money-bill-wave me-2"></i> Refunded
                    <span class="badge bg-success float-end">{{ $stats['refunded'] }}</span>
                </a>
                <a href="{{ route('admin.returns.index', ['status' => 'rejected']) }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-times me-2"></i> Rejected
                    <span class="badge bg-danger float-end">{{ $stats['rejected'] }}</span>
                </a>
            </div>
            
            <h5 class="mb-3">Analytics</h5>
            <div class="list-group">
                <a href="{{ route('admin.returns.reports') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-chart-bar me-2"></i> Reports & Analytics
                </a>
                <a href="#" class="list-group-item list-group-item-action" data-bs-toggle="modal" data-bs-target="#exportModal">
                    <i class="fas fa-file-export me-2"></i> Export Data
                </a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3>Return Requests Management</h3>
                <div>
                    <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse">
                        <i class="fas fa-filter me-1"></i> Filters
                    </button>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="collapse mb-4" id="filtersCollapse">
                <div class="card card-body">
                    <form action="{{ route('admin.returns.index') }}" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="in_transit" {{ request('status') == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                                <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Received</option>
                                <option value="inspected" {{ request('status') == 'inspected' ? 'selected' : '' }}>Inspected</option>
                                <option value="refund_pending" {{ request('status') == 'refund_pending' ? 'selected' : '' }}>Refund Pending</option>
                                <option value="refund_processed" {{ request('status') == 'refund_processed' ? 'selected' : '' }}>Refund Processed</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="date_from" class="form-label">Date From</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to" class="form-label">Date To</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" placeholder="RMA, Order #, Customer..." value="{{ request('search') }}">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                            <a href="{{ route('admin.returns.index') }}" class="btn btn-outline-secondary">Clear Filters</a>
                        </div>
                    </form>
                </div>
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
            
            <!-- Returns Table -->
            <div class="card">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Return Requests</h5>
                        <span class="badge bg-primary">{{ $returnRequests->total() }} Returns</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(count($returnRequests) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>RMA Number</th>
                                        <th>Customer</th>
                                        <th>Product</th>
                                        <th>Order Number</th>
                                        <th>Return Reason</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($returnRequests as $returnRequest)
                                        <tr>
                                            <td>
                                                <strong>{{ $returnRequest->rma_number }}</strong>
                                            </td>
                                            <td>
                                                @if($returnRequest->user)
                                                    <div>{{ $returnRequest->user->name }}</div>
                                                    <small class="text-muted">{{ $returnRequest->user->email }}</small>
                                                @else
                                                    <span class="text-muted">Unknown User</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($returnRequest->product)
                                                    <div>{{ $returnRequest->product->name }}</div>
                                                    <small class="text-muted">SKU: {{ $returnRequest->product->sku }}</small>
                                                @else
                                                    <span class="text-muted">Unknown Product</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($returnRequest->order)
                                                    {{ $returnRequest->order->order_number }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span title="{{ $returnRequest->return_reason }}">
                                                    {{ Str::limit($returnRequest->return_reason, 30) }}
                                                </span>
                                            </td>
                                            <td>
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
                                            </td>
                                            <td>
                                                {{ $returnRequest->created_at->format('M d, Y') }}
                                                <div><small class="text-muted">{{ $returnRequest->created_at->diffForHumans() }}</small></div>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.returns.show', $returnRequest) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    @if($returnRequest->status == 'pending')
                                                        <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $returnRequest->id }}">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $returnRequest->id }}">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif
                                                    
                                                    @if(in_array($returnRequest->status, ['approved', 'in_transit', 'received']))
                                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#statusModal{{ $returnRequest->id }}">
                                                            <i class="fas fa-sync-alt"></i>
                                                        </button>
                                                    @endif
                                                    
                                                    @if($returnRequest->status == 'inspected')
                                                        <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#refundModal{{ $returnRequest->id }}">
                                                            <i class="fas fa-money-bill-wave"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                                
                                                <!-- Approve Modal -->
                                                <div class="modal fade" id="approveModal{{ $returnRequest->id }}" tabindex="-1" aria-hidden="true">
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
                                                <div class="modal fade" id="rejectModal{{ $returnRequest->id }}" tabindex="-1" aria-hidden="true">
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
                                                
                                                <!-- Status Update Modal -->
                                                <div class="modal fade" id="statusModal{{ $returnRequest->id }}" tabindex="-1" aria-hidden="true">
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
                                                                                <option value="in_transit" {{ $returnRequest->status == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                                                                            @endif
                                                                            
                                                                            @if(in_array($returnRequest->status, ['approved', 'in_transit']))
                                                                                <option value="received" {{ $returnRequest->status == 'received' ? 'selected' : '' }}>Received</option>
                                                                            @endif
                                                                            
                                                                            @if(in_array($returnRequest->status, ['approved', 'in_transit', 'received']))
                                                                                <option value="inspected" {{ $returnRequest->status == 'inspected' ? 'selected' : '' }}>Inspected</option>
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
                                                
                                                <!-- Refund Modal -->
                                                <div class="modal fade" id="refundModal{{ $returnRequest->id }}" tabindex="-1" aria-hidden="true">
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
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center p-3">
                            {{ $returnRequests->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="p-4 text-center">
                            <div class="text-muted mb-3">
                                <i class="fas fa-search fa-3x"></i>
                            </div>
                            <h5>No return requests found</h5>
                            <p>Try adjusting your search filters or check back later.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.returns.export') }}" method="GET">
                <div class="modal-header">
                    <h5 class="modal-title">Export Return Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="export_status" class="form-label">Status</label>
                        <select name="status" id="export_status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="in_transit">In Transit</option>
                            <option value="received">Received</option>
                            <option value="inspected">Inspected</option>
                            <option value="refund_processed">Refund Processed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="export_date_from" class="form-label">Date From</label>
                        <input type="date" class="form-control" id="export_date_from" name="date_from">
                    </div>
                    
                    <div class="mb-3">
                        <label for="export_date_to" class="form-label">Date To</label>
                        <input type="date" class="form-control" id="export_date_to" name="date_to">
                    </div>
                    
                    <div class="mb-3">
                        <label for="export_format" class="form-label">Export Format</label>
                        <select name="format" id="export_format" class="form-select">
                            <option value="csv">CSV</option>
                            <option value="excel">Excel</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Export Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
