@extends('layouts.app')

@section('content')
<style>
    /* Sidebar Styles */
    #sidebar {
        width: 60px;
        height: calc(100vh - 70px); /* Adjusted to not overlap the navbar */
        background-color: #00796b;
        color: white;
        position: fixed;
        top: 70px; /* Push down below navbar */
        left: 0;
        z-index: 1;
        padding: 1rem 0;
        transition: width 0.3s;
        overflow: hidden;
    }

    /* Expand Sidebar on Hover */
    #sidebar:hover {
        width: 200px;
    }

    /* Sidebar Links */
    #sidebar a {
        color: white;
        text-decoration: none;
        padding: 10px 20px;
        display: flex;
        align-items: center;
        white-space: nowrap;
    }

    #sidebar a i {
        margin-right: 10px;
    }

    /* Hide Text When Collapsed */
    #sidebar .link-text {
        opacity: 0;
        transition: opacity 0.2s;
    }

    /* Show Text When Hovered */
    #sidebar:hover .link-text {
        opacity: 1;
    }

    /* Adjust Main Content */
    #main-content {
        margin-left: 60px;
        transition: margin-left 0.3s;
        margin-top: 70px; /* Align content with navbar */
    }

    #sidebar:hover + #main-content {
        margin-left: 200px;
    }
    #sidebar:hover {
    color: #b2dfdb;
    }


    /* Product Card Body Background */
    .card-body {
        background-color: rgb(151, 231, 243) !important; /* Nude color */
        border-radius: 0 0 10px 10px;
        padding: 1rem;
    }
</style>

<div class="d-flex">
    <!-- Sidebar -->
    <div id="sidebar" class="d-flex flex-column">
        @auth
            <a href="{{ route('dashboard') }}">
                <i class="fas fa-tachometer-alt fa-lg"></i>
                <span class="link-text">Dashboard</span>
            </a>
            <a href="{{ route('returns.index') }}">
                <i class="fas fa-file-alt fa-lg"></i>
                <span class="link-text">My Returns</span>
            </a>
        @endauth
        
        
        @auth
            @if(Auth::user()->isAdmin() || Auth::user()->isSupportAgent())
                <!-- Admin Section Divider -->
                <div class="border-top border-light my-3"></div>
                <div class="px-3 mb-2">
                    <span class="link-text small text-white-50">ADMIN SECTION</span>
                </div>
                
                <a href="{{ route('admin.returns.index') }}">
                    <i class="fas fa-tasks fa-lg"></i>
                    <span class="link-text">Manage Returns</span>
                </a>
                <a href="{{ route('admin.returns.reports') }}">
                    <i class="fas fa-chart-bar fa-lg"></i>
                    <span class="link-text">Return Analytics</span>
                </a>
            @endif
        @endauth
    </div>

    <!-- Main Content -->
    <div id="main-content" class="container py-4 flex-grow-1">
        @auth
            @if(Auth::user()->isAdmin() || Auth::user()->isSupportAgent())
                <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-shield me-2 fa-lg"></i>
                        <div>
                            <strong>Welcome, {{ Auth::user()->role === 'admin' ? 'Administrator' : 'Support Agent' }}!</strong>
                            <p class="mb-0">You have access to the admin return management system. Use the sidebar or navigation menu to access <a href="{{ route('admin.returns.index') }}" class="alert-link">Return Management</a> and <a href="{{ route('admin.returns.reports') }}" class="alert-link">Return Analytics</a>.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        @endauth
        <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-info text-white">
                            <h3><i class="fas fa-shopping-bag me-2"></i> Your Orders</h3>
                        </div>
                        <div class="card-body">
                            <p>View your order history, track shipments, and initiate returns.</p>
                            <a href="http://127.0.0.1:8000/profile/orders" class="btn btn-outline-info">
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
                            <a href="http://127.0.0.1:8000/returns" class="btn btn-outline-success">
                                <i class="fas fa-exchange-alt me-2"></i> View Returns
                            </a>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>
@endsection
