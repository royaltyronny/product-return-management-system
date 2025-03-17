@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4">Our Services</h1>
    
    <p class="text-center">At PRMS, we provide a seamless and efficient product return process to ensure your satisfaction and convenience.</p>

    <div class="row mt-4">
        <!-- Service 1 -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">🌐 Easy Return Processing</h5>
                    <p class="card-text">Submit return requests quickly and track the status in real-time.</p>
                </div>
            </div>
        </div>

        <!-- Service 2 -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">📦 Product Inspection</h5>
                    <p class="card-text">Thorough inspection of returned products to ensure quality and compliance.</p>
                </div>
            </div>
        </div>

        <!-- Service 3 -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">🔄 Replacement & Exchange</h5>
                    <p class="card-text">Hassle-free product replacements or exchanges, tailored to your needs.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Service 4 -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">💸 Refund Management</h5>
                    <p class="card-text">Fast and secure refund processing directly to your preferred payment method.</p>
                </div>
            </div>
        </div>

        <!-- Service 5 -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">📊 Return Reports & Analytics</h5>
                    <p class="card-text">Access detailed reports to analyze return trends and improve product handling.</p>
                </div>
            </div>
        </div>

        <!-- Service 6 -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">🤝 Customer Support</h5>
                    <p class="card-text">Dedicated support team to assist you at every step of the return process.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-5">
        <a href="" class="btn btn-primary">Submit a Return</a>
        <a href="" class="btn btn-secondary">Contact Support</a>
    </div>
</div>
@endsection
