@extends('layouts.app')

@section('content')
<style>
    /* About Page Styling */
    .about-container {
        margin-top: 70px;
        max-width: 900px;
        margin-left: auto;
        margin-right: auto;
        background: #f5f5f5;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    .about-heading {
        color: #00796b;
        text-align: center;
        margin-bottom: 20px;
    }
    .about-content p {
        color: #555;
        line-height: 1.8;
        text-align: justify;
    }
    .about-footer {
        text-align: center;
        margin-top: 30px;
    }
</style>

<div class="about-container">
    <h1 class="about-heading">
        <i class="fas fa-info-circle"></i> About PRMS
    </h1>
    <div class="about-content">
        <p>
            Welcome to the Product Return Management System (PRMS) – your comprehensive solution for handling product returns with ease and efficiency. 
            PRMS is designed to streamline the return process, track product statuses, and provide insights into return trends, ensuring customer satisfaction and operational excellence.
        </p>
        
        <h3 class="mt-4 mb-3 text-success"><i class="fas fa-bullseye"></i> Our Mission</h3>
        <p>
            Our mission is to transform the traditionally complex return process into a seamless experience for both customers and businesses. 
            We believe that a well-managed return system is not just about handling refunds – it's about building customer trust, optimizing inventory, and turning potential losses into opportunities.
        </p>
        
        <h3 class="mt-4 mb-3 text-success"><i class="fas fa-cogs"></i> Key Features</h3>
        <div class="row">
            <div class="col-md-6">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><i class="fas fa-check-circle text-success"></i> Online return initiation with categorization</li>
                    <li class="list-group-item"><i class="fas fa-check-circle text-success"></i> Image uploads for return validation</li>
                    <li class="list-group-item"><i class="fas fa-check-circle text-success"></i> Automated RMA generation</li>
                    <li class="list-group-item"><i class="fas fa-check-circle text-success"></i> Return request approval workflow</li>
                </ul>
            </div>
            <div class="col-md-6">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><i class="fas fa-check-circle text-success"></i> Warehouse & inventory management</li>
                    <li class="list-group-item"><i class="fas fa-check-circle text-success"></i> Multiple refund options</li>
                    <li class="list-group-item"><i class="fas fa-check-circle text-success"></i> Comprehensive analytics & reporting</li>
                    <li class="list-group-item"><i class="fas fa-check-circle text-success"></i> Automated customer communications</li>
                </ul>
            </div>
        </div>
        
        <h3 class="mt-4 mb-3 text-success"><i class="fas fa-users"></i> Who We Serve</h3>
        <p>
            PRMS is designed for businesses of all sizes, from small e-commerce shops to large retail enterprises. Our system supports multiple user roles including customers, support agents, warehouse staff, finance teams, and administrators – ensuring everyone has the tools they need to perform their specific functions efficiently.
        </p>
        
        <h3 class="mt-4 mb-3 text-success"><i class="fas fa-shield-alt"></i> Our Commitment</h3>
        <p>
            We are committed to continuous improvement and innovation. Our system is regularly updated with new features and optimizations based on user feedback and industry best practices. We prioritize security and compliance, ensuring that all data handling meets the highest standards of protection.
        </p>
    </div>
    <div class="about-footer mt-5">
        <a href="{{ route('products.index') }}" class="btn btn-primary">
            <i class="fas fa-shoe-prints"></i> Browse Our Collection
        </a>
        @auth
            <a href="{{ route('dashboard') }}" class="btn btn-success ms-2">
                <i class="fas fa-tachometer-alt"></i> Go to Dashboard
            </a>
        @else
            <a href="{{ route('login') }}" class="btn btn-success ms-2">
                <i class="fas fa-sign-in-alt"></i> Login
            </a>
            <a href="{{ route('register') }}" class="btn btn-outline-success ms-2">
                <i class="fas fa-user-plus"></i> Register
            </a>
        @endauth
    </div>
</div>
@endsection
