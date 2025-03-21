@extends('layouts.app')

@section('title', 'Your Profile')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h2><i class="fas fa-user-circle me-2"></i> Profile Details</h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center mb-4 mb-md-0">
                            <div class="bg-light rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 150px; height: 150px;">
                                <i class="fas fa-user fa-4x text-primary"></i>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h5 class="text-muted">Personal Information</h5>
                                    <p><strong>Name:</strong> {{ $user->name }}</p>
                                    <p><strong>Email:</strong> {{ $user->email }}</p>
                                    <p><strong>Phone:</strong> {{ $user->phone_number ?? 'Not provided' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="text-muted">Address Information</h5>
                                    @if (isset($user->address) && is_array($user->address))
                                        <p>{{ $user->address['address'] ?? '' }}</p>
                                        <p>{{ $user->address['city'] ?? '' }}, {{ $user->address['state'] ?? '' }} {{ $user->address['zip'] ?? '' }}</p>
                                        <p>{{ $user->address['country'] ?? '' }}</p>
                                    @else
                                        <p>No address information provided</p>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                                    <i class="fas fa-edit me-2"></i> Edit Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-info text-white">
                            <h3><i class="fas fa-shopping-bag me-2"></i> Your Orders</h3>
                        </div>
                        <div class="card-body">
                            <p>View your order history, track shipments, and initiate returns.</p>
                            <a href="{{ route('profile.orders') }}" class="btn btn-outline-info">
                                <i class="fas fa-list me-2"></i> View Orders
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-success text-white">
                            <h3><i class="fas fa-undo-alt me-2"></i> Your Returns</h3>
                        </div>
                        <div class="card-body">
                            <p>Track the status of your return requests and manage refunds.</p>
                            <a href="{{ route('returns.index') }}" class="btn btn-outline-success">
                                <i class="fas fa-exchange-alt me-2"></i> View Returns
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
