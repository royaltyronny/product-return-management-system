@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Return Request Details - {{ $returnRequest->rma_number }}</h5>
                    <a href="{{ route('returns.index') }}" class="btn btn-secondary btn-sm">Back to Returns</a>
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

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Return Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>RMA Number</th>
                                            <td>{{ $returnRequest->rma_number }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
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
                                        </tr>
                                        <tr>
                                            <th>Return Category</th>
                                            <td>{{ ucfirst(str_replace('_', ' ', $returnRequest->return_category)) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Return Reason</th>
                                            <td>{{ $returnRequest->return_reason }}</td>
                                        </tr>
                                        <tr>
                                            <th>Description</th>
                                            <td>{{ $returnRequest->description ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Refund Method</th>
                                            <td>{{ ucfirst(str_replace('_', ' ', $returnRequest->refund_method)) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Refund Amount</th>
                                            <td>${{ number_format($returnRequest->refund_amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Created Date</th>
                                            <td>{{ $returnRequest->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Product & Order Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Product</th>
                                            <td>{{ $returnRequest->product->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Order Number</th>
                                            <td>{{ $returnRequest->order->order_number ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Order Date</th>
                                            <td>{{ $returnRequest->order->order_date ? $returnRequest->order->order_date->format('M d, Y') : 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($returnRequest->evidence_images && count($returnRequest->evidence_images) > 0)
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">Evidence Images</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach($returnRequest->evidence_images as $image)
                                                <div class="col-md-4 mb-3">
                                                    <a href="{{ asset('storage/' . $image) }}" target="_blank">
                                                        <img src="{{ asset('storage/' . $image) }}" class="img-fluid img-thumbnail" alt="Evidence Image">
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($returnRequest->returnShipment)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Shipment Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>Shipping Method</th>
                                                <td>{{ $returnRequest->returnShipment->shipping_method }}</td>
                                            </tr>
                                            <tr>
                                                <th>Tracking Number</th>
                                                <td>{{ $returnRequest->returnShipment->tracking_number }}</td>
                                            </tr>
                                            <tr>
                                                <th>Shipping Label</th>
                                                <td>
                                                    @if($returnRequest->returnShipment->shipping_label_url)
                                                        <a href="{{ $returnRequest->returnShipment->shipping_label_url }}" target="_blank" class="btn btn-sm btn-primary">View Shipping Label</a>
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>Ship Date</th>
                                                <td>{{ $returnRequest->returnShipment->ship_date ? $returnRequest->returnShipment->ship_date->format('M d, Y') : 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Estimated Arrival</th>
                                                <td>{{ $returnRequest->returnShipment->estimated_arrival ? $returnRequest->returnShipment->estimated_arrival->format('M d, Y') : 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Actual Arrival</th>
                                                <td>{{ $returnRequest->returnShipment->actual_arrival ? $returnRequest->returnShipment->actual_arrival->format('M d, Y') : 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($returnRequest->returnRefund)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Refund Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>Refund Method</th>
                                                <td>{{ ucfirst(str_replace('_', ' ', $returnRequest->returnRefund->refund_method)) }}</td>
                                            </tr>
                                            <tr>
                                                <th>Refund Amount</th>
                                                <td>${{ number_format($returnRequest->returnRefund->amount, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <th>Restocking Fee</th>
                                                <td>${{ number_format($returnRequest->returnRefund->restocking_fee, 2) }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>Refund Status</th>
                                                <td>{{ ucfirst($returnRequest->returnRefund->status) }}</td>
                                            </tr>
                                            <tr>
                                                <th>Refund Date</th>
                                                <td>{{ $returnRequest->returnRefund->refund_date ? $returnRequest->returnRefund->refund_date->format('M d, Y') : 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Transaction ID</th>
                                                <td>{{ $returnRequest->returnRefund->transaction_id ?? 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Actions</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('returns.index') }}" class="btn btn-secondary">Back to Returns</a>
                                
                                @if($returnRequest->status == 'pending')
                                    <form action="{{ route('returns.cancel', $returnRequest) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this return request?')">Cancel Return</button>
                                    </form>
                                @endif
                                
                                @if(auth()->user()->isAdmin() || auth()->user()->isSupportAgent())
                                    @if($returnRequest->status == 'pending')
                                        <form action="{{ route('returns.process', $returnRequest) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="btn btn-success">Approve Return</button>
                                        </form>
                                        
                                        <form action="{{ route('returns.process', $returnRequest) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="btn btn-danger">Reject Return</button>
                                        </form>
                                    @endif
                                @endif
                                
                                @if(auth()->user()->isWarehouseStaff() || auth()->user()->isAdmin())
                                    @if(in_array($returnRequest->status, ['approved', 'in_transit', 'received']))
                                        <form action="{{ route('returns.update-status', $returnRequest) }}" method="POST" class="d-inline">
                                            @csrf
                                            <div class="input-group">
                                                <select name="status" class="form-select">
                                                    <option value="in_transit" @if($returnRequest->status == 'in_transit') selected @endif>In Transit</option>
                                                    <option value="received" @if($returnRequest->status == 'received') selected @endif>Received</option>
                                                    <option value="inspected" @if($returnRequest->status == 'inspected') selected @endif>Inspected</option>
                                                </select>
                                                <button type="submit" class="btn btn-primary">Update Status</button>
                                            </div>
                                        </form>
                                    @endif
                                @endif
                                
                                @if(auth()->user()->isFinance() || auth()->user()->isAdmin())
                                    @if($returnRequest->status == 'inspected')
                                        <form action="{{ route('returns.process-refund', $returnRequest) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success">Process Refund</button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
