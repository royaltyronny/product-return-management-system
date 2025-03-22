@extends('layouts.app')

@section('title', 'Your Orders')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h2><i class="fas fa-shopping-bag me-2"></i> Your Order History</h2>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (count($orders) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Total</th>
                                        <th>Items</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $order)
                                        <tr>
                                            <td>{{ $order->order_number }}</td>
                                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                                            <td>
                                                @switch($order->status)
                                                    @case('pending')
                                                        <span class="badge bg-warning text-dark">Pending</span>
                                                        @break
                                                    @case('processing')
                                                        <span class="badge bg-info">Processing</span>
                                                        @break
                                                    @case('shipped')
                                                        <span class="badge bg-primary">Shipped</span>
                                                        @break
                                                    @case('delivered')
                                                        <span class="badge bg-success">Delivered</span>
                                                        @break
                                                    @case('completed')
                                                        <span class="badge bg-success">Completed</span>
                                                        @break
                                                    @case('cancelled')
                                                        <span class="badge bg-danger">Cancelled</span>
                                                        @break
                                                    @case('refunded')
                                                        <span class="badge bg-secondary">Refunded</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                                @endswitch
                                            </td>
                                            <td>${{ number_format($order->total_amount, 2) }}</td>
                                            <td>{{ $order->orderItems->count() }} item(s)</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#orderDetails{{ $order->id }}" aria-expanded="false">
                                                    <i class="fas fa-eye"></i> Details
                                                </button>
                                                
                                                @if ($order->status === 'delivered' || $order->status === 'completed')
                                                    <a href="{{ route('returns.create', ['order_id' => $order->id]) }}" class="btn btn-sm btn-outline-secondary">
                                                        <i class="fas fa-undo"></i> Return Order
                                                    </a>
                                                    <button class="btn btn-sm btn-outline-danger" type="button" data-bs-toggle="collapse" data-bs-target="#orderDetails{{ $order->id }}" aria-expanded="false">
                                                        <i class="fas fa-undo-alt"></i> Return Items
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr class="collapse" id="orderDetails{{ $order->id }}">
                                            <td colspan="6">
                                                <div class="card card-body bg-light border-0">
                                                    @if ($order->status === 'delivered' || $order->status === 'completed')
                                                        <div class="alert alert-info mb-3">
                                                            <i class="fas fa-info-circle me-2"></i> You can return individual items from this order by clicking the "Return Item" button next to each product.
                                                        </div>
                                                    @endif
                                                    <h5 class="mb-3">Order Items</h5>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Product</th>
                                                                    <th>Quantity</th>
                                                                    <th>Price</th>
                                                                    <th>Total</th>
                                                                    <th>Status</th>
                                                                    <th>Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($order->orderItems as $item)
                                                                    <tr>
                                                                        <td>
                                                                            <div class="d-flex align-items-center">
                                                                                @if ($item->product && $item->product->image)
                                                                                    <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="img-thumbnail me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                                                                @else
                                                                                    <div class="bg-secondary text-white rounded me-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                                                        <i class="fas fa-box"></i>
                                                                                    </div>
                                                                                @endif
                                                                                <div>
                                                                                    <strong>{{ $item->product ? $item->product->name : 'Unknown Product' }}</strong>
                                                                                    @if ($item->product && $item->product->sku)
                                                                                        <br><small class="text-muted">SKU: {{ $item->product->sku }}</small>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td>{{ $item->quantity }}</td>
                                                                        <td>${{ number_format($item->unit_price, 2) }}</td>
                                                                        <td>${{ number_format($item->total_price, 2) }}</td>
                                                                        <td>
                                                                            @if ($item->returned_quantity > 0)
                                                                                @if ($item->returned_quantity == $item->quantity)
                                                                                    <span class="badge bg-info">Fully Returned</span>
                                                                                @else
                                                                                    <span class="badge bg-warning text-dark">Partially Returned ({{ $item->returned_quantity }})</span>
                                                                                @endif
                                                                            @else
                                                                                <span class="badge bg-success">{{ ucfirst($item->status) }}</span>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @if (($order->status === 'delivered' || $order->status === 'completed') && 
                                                                                ($item->returned_quantity < $item->quantity) && 
                                                                                $item->status !== 'returned')
                                                                                <a href="{{ route('returns.create', ['order_id' => $order->id, 'product_id' => $item->product_id]) }}" 
                                                                                   class="btn btn-sm btn-outline-danger">
                                                                                    <i class="fas fa-undo-alt"></i> Return Item
                                                                                </a>
                                                                            @elseif ($item->returned_quantity > 0 && $item->returned_quantity < $item->quantity)
                                                                                <a href="{{ route('returns.create', ['order_id' => $order->id, 'product_id' => $item->product_id]) }}" 
                                                                                   class="btn btn-sm btn-outline-warning">
                                                                                    <i class="fas fa-undo-alt"></i> Return More
                                                                                </a>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    
                                                    <div class="row mt-3">
                                                        <div class="col-md-6">
                                                            <h6>Shipping Information</h6>
                                                            @if (is_array($order->shipping_address))
                                                                <p class="mb-1">{{ $order->shipping_address['address'] ?? '' }}</p>
                                                                <p class="mb-1">
                                                                    {{ $order->shipping_address['city'] ?? '' }}, 
                                                                    {{ $order->shipping_address['state'] ?? '' }} 
                                                                    {{ $order->shipping_address['zip'] ?? '' }}
                                                                </p>
                                                                <p class="mb-1">{{ $order->shipping_address['country'] ?? '' }}</p>
                                                            @else
                                                                <p>{{ $order->shipping_address }}</p>
                                                            @endif
                                                            <p class="mb-1"><strong>Method:</strong> {{ ucfirst($order->shipping_method) }}</p>
                                                            @if ($order->tracking_number)
                                                                <p class="mb-1"><strong>Tracking:</strong> {{ $order->tracking_number }}</p>
                                                            @endif
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6>Payment Information</h6>
                                                            <p class="mb-1"><strong>Method:</strong> {{ ucfirst($order->payment_method ?? 'Not specified') }}</p>
                                                            <p class="mb-1"><strong>Date:</strong> {{ $order->order_date ? $order->order_date->format('M d, Y') : $order->created_at->format('M d, Y') }}</p>
                                                            <p class="mb-1"><strong>Total:</strong> ${{ number_format($order->total_amount, 2) }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $orders->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> You haven't placed any orders yet.
                        </div>
                        <div class="text-center mt-4">
                            <a href="{{ url('/') }}" class="btn btn-primary">
                                <i class="fas fa-shopping-cart me-2"></i> Start Shopping
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="mt-3">
                <a href="{{ route('returns.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> see your returns.
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
