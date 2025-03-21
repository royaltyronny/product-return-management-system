@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Return Requests</h5>
                    <a href="{{ route('returns.create') }}" class="btn btn-primary btn-sm">Create New Return</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(count($returnRequests) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>RMA Number</th>
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
                                            <td>{{ $returnRequest->rma_number }}</td>
                                            <td>{{ $returnRequest->product->name ?? 'N/A' }}</td>
                                            <td>{{ $returnRequest->order->order_number ?? 'N/A' }}</td>
                                            <td>{{ Str::limit($returnRequest->return_reason, 30) }}</td>
                                            <td>
                                                <span class="badge 
                                                    @if($returnRequest->status == 'pending') bg-warning
                                                    @elseif($returnRequest->status == 'approved') bg-info
                                                    @elseif($returnRequest->status == 'rejected') bg-danger
                                                    @elseif($returnRequest->status == 'in_transit') bg-primary
                                                    @elseif($returnRequest->status == 'received') bg-secondary
                                                    @elseif($returnRequest->status == 'inspected') bg-dark
                                                    @elseif($returnRequest->status == 'refunded') bg-success
                                                    @elseif($returnRequest->status == 'completed') bg-success
                                                    @elseif($returnRequest->status == 'cancelled') bg-danger
                                                    @endif">
                                                    {{ ucfirst($returnRequest->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $returnRequest->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('returns.show', $returnRequest) }}" class="btn btn-sm btn-info">View</a>
                                                
                                                @if($returnRequest->status == 'pending')
                                                    <form action="{{ route('returns.cancel', $returnRequest) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to cancel this return request?')">Cancel</button>
                                                    </form>
                                                @endif
                                                
                                                @if(auth()->user()->isAdmin() || auth()->user()->isSupportAgent())
                                                    @if($returnRequest->status == 'pending')
                                                        <form action="{{ route('returns.process', $returnRequest) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" name="action" value="approve">
                                                            <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                                        </form>
                                                        
                                                        <form action="{{ route('returns.process', $returnRequest) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" name="action" value="reject">
                                                            <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                                        </form>
                                                    @endif
                                                @endif
                                                
                                                @if(auth()->user()->isWarehouseStaff() || auth()->user()->isAdmin())
                                                    @if(in_array($returnRequest->status, ['approved', 'in_transit', 'received']))
                                                        <form action="{{ route('returns.update-status', $returnRequest) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <select name="status" class="form-select form-select-sm d-inline" style="width: auto;">
                                                                <option value="in_transit" @if($returnRequest->status == 'in_transit') selected @endif>In Transit</option>
                                                                <option value="received" @if($returnRequest->status == 'received') selected @endif>Received</option>
                                                                <option value="inspected" @if($returnRequest->status == 'inspected') selected @endif>Inspected</option>
                                                            </select>
                                                            <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                                        </form>
                                                    @endif
                                                @endif
                                                
                                                @if(auth()->user()->isFinance() || auth()->user()->isAdmin())
                                                    @if($returnRequest->status == 'inspected')
                                                        <form action="{{ route('returns.process-refund', $returnRequest) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success">Process Refund</button>
                                                        </form>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $returnRequests->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            No return requests found. <a href="{{ route('returns.create') }}">Create a new return request</a>.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
