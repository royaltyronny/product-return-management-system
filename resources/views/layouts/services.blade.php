@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="jumbotron bg-light p-5 rounded-3 mb-5">
        <h1 class="display-4 text-center text-dark"><i class="fas fa-cogs"></i> Our Services</h1>
        <p class="lead text-center text-dark">Comprehensive return management solutions for modern businesses</p>
        <hr class="my-4">
        <p class="text-center text-dark">At PRMS, we provide a seamless and efficient product return process to ensure customer satisfaction and operational excellence. Our end-to-end solution covers the entire return lifecycle, from initiation to resolution.</p>
    </div>

    <!-- Sections -->
    <h2 class="mb-4 text-primary text-dark"><i class="fas fa-user"></i> Customer Return Services</h2>
    <div class="row mt-4 mb-5 text-dark">
        <!-- Service Cards -->
        @foreach (range(1, 9) as $service)
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0 text-dark"><i class="fas fa-laptop"></i> Online Return Portal</h5>
                    </div>
                    <div class="card-body text-dark">
                        <p class="card-text">Our user-friendly online portal allows customers to initiate returns with just a few clicks. Upload photos, select return reasons, and get instant eligibility validation.</p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check-circle text-success"></i> 24/7 return initiation</li>
                            <li><i class="fas fa-check-circle text-success"></i> Mobile-responsive interface</li>
                            <li><i class="fas fa-check-circle text-success"></i> Automated eligibility checks</li>
                        </ul>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Call to Action -->
    <div class="text-center py-5 text-dark">
        <h3 class="mb-4">Ready to streamline your return management process?</h3>
        <div class="d-flex justify-content-center">
            <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg mx-2">
                <i class="fas fa-shoe-prints"></i> Browse Products
            </a>
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-success btn-lg mx-2">
                    <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                </a>
            @else
                <a href="{{ route('register') }}" class="btn btn-success btn-lg mx-2">
                    <i class="fas fa-user-plus"></i> Register Now
                </a>
            @endauth
        </div>
    </div>
</div>
@endsection
