@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="jumbotron bg-light p-5 rounded-3 mb-5">
        <h1 class="display-4 text-center"><i class="fas fa-cogs"></i> Our Services</h1>
        <p class="lead text-center">Comprehensive return management solutions for modern businesses</p>
        <hr class="my-4">
        <p class="text-center">At PRMS, we provide a seamless and efficient product return process to ensure customer satisfaction and operational excellence. Our end-to-end solution covers the entire return lifecycle, from initiation to resolution.</p>
    </div>

    <!-- Customer Services Section -->
    <h2 class="mb-4 text-primary"><i class="fas fa-user"></i> Customer Return Services</h2>
    <div class="row mt-4 mb-5">
        <!-- Service 1 -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-laptop"></i> Online Return Portal</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Our user-friendly online portal allows customers to initiate returns with just a few clicks. Upload photos, select return reasons, and get instant eligibility validation.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check-circle text-success"></i> 24/7 return initiation</li>
                        <li><i class="fas fa-check-circle text-success"></i> Mobile-responsive interface</li>
                        <li><i class="fas fa-check-circle text-success"></i> Automated eligibility checks</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Service 2 -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-shipping-fast"></i> Shipping Solutions</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Hassle-free shipping options for returning products. Generate pre-paid shipping labels, schedule pickups, and track shipments in real-time.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check-circle text-success"></i> Pre-paid return labels</li>
                        <li><i class="fas fa-check-circle text-success"></i> Multiple carrier options</li>
                        <li><i class="fas fa-check-circle text-success"></i> Real-time tracking</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Service 3 -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-comments"></i> Communication Hub</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Stay informed throughout the return process with automated notifications and updates. Access support when you need it.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check-circle text-success"></i> Status notifications</li>
                        <li><i class="fas fa-check-circle text-success"></i> Live chat support</li>
                        <li><i class="fas fa-check-circle text-success"></i> Return history access</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Business Services Section -->
    <h2 class="mb-4 text-success"><i class="fas fa-building"></i> Business Management Services</h2>
    <div class="row mt-4 mb-5">eamlined approval processes for return requests with customizable rules and automated validations to reduce manual review time.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check-circle text-success"></i> Role-based approvals</li>
                        <li><i class="fas fa-check-circle text-success"></i> Custom validation rules</li>
                        <li><i class="fas fa-check-circle text-success"></i> Fraud detection</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Service 5 -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-warehouse"></i> Warehouse Management</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Optimize your warehouse operations with our integrated return processing system. Scan, inspect, and route returned items efficiently.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check-circle text-success"></i> Barcode/QR scanning</li>
                        <li><i class="fas fa-check-circle text-success"></i> Quality control workflows</li>
                        <li><i class="fas fa-check-circle text-success"></i> Inventory reintegration</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Service 6 -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-chart-line"></i> Analytics & Reporting</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Gain valuable insights into your return patterns with comprehensive analytics and customizable reports.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check-circle text-success"></i> Return trend analysis</li>
                        <li><i class="fas fa-check-circle text-success"></i> Financial impact reports</li>
                        <li><i class="fas fa-check-circle text-success"></i> Product quality insights</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Services Section -->
    <h2 class="mb-4 text-danger"><i class="fas fa-dollar-sign"></i> Financial Services</h2>
    <div class="row mt-4 mb-5">
        <!-- Service 7 -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-money-bill-wave"></i> Refund Processing</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Fast and secure refund processing with multiple payment method options and automated reconciliation.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check-circle text-success"></i> Multiple refund methods</li>
                        <li><i class="fas fa-check-circle text-success"></i> Partial refunds</li>
                        <li><i class="fas fa-check-circle text-success"></i> Automated processing</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Service 8 -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-exchange-alt"></i> Exchange Management</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Streamlined product exchange processes that handle inventory checks, price differences, and shipping coordination.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check-circle text-success"></i> Size/color exchanges</li>
                        <li><i class="fas fa-check-circle text-success"></i> Cross-product exchanges</li>
                        <li><i class="fas fa-check-circle text-success"></i> Price difference handling</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Service 9 -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-file-invoice-dollar"></i> Financial Reconciliation</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Automated financial reconciliation for returns, ensuring accurate accounting and reporting.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check-circle text-success"></i> Return cost tracking</li>
                        <li><i class="fas fa-check-circle text-success"></i> Restocking fee management</li>
                        <li><i class="fas fa-check-circle text-success"></i> Tax calculations</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="text-center py-5">
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
