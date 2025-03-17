@extends('layouts.app')

@section('content')
<style>
    #sidebar {
        width: 60px;
        height: calc(100vh - 70px);
        background-color: #00796b;
        color: white;
        position: fixed;
        top: 70px;
        left: 0;
        z-index: 1;
        padding: 1rem 0;
        transition: width 0.3s;
        overflow: hidden;
    }

    #sidebar:hover {
        width: 200px;
    }

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

    #sidebar .link-text {
        opacity: 0;
        transition: opacity 0.2s;
    }

    #sidebar:hover .link-text {
        opacity: 1;
    }

    #main-content {
        margin-left: 60px;
        transition: margin-left 0.3s;
        margin-top: 70px;
    }

    #sidebar:hover + #main-content {
        margin-left: 200px;
    }

    .card-body {
        background-color: rgb(151, 231, 243) !important;
        border-radius: 0 0 10px 10px;
        padding: 1rem;
    }
</style>

<div class="d-flex">
    <!-- Sidebar -->
    <div id="sidebar" class="d-flex flex-column">
        <a href="{{ route('dashboard') }}">
            <i class="fas fa-tachometer-alt fa-lg"></i>
            <span class="link-text">Dashboard</span>
        </a>
        <a href="{{ route('returns.reportss') }}">
            <i class="fas fa-file-alt fa-lg"></i>
            <span class="link-text">Return Report</span>
        </a>
        <a href="{{ route('products.index') }}">
            <i class="fas fa-shoe-prints fa-lg"></i>
            <span class="link-text">Shoe Collection</span>
        </a>
    </div>

    <!-- Main Content -->
    <div id="main-content" class="container py-4 flex-grow-1">
        <h1 class="mb-4 text-center"><i class="fas fa-undo"></i> Product Return Request</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            <div class="text-center mb-4">
                <a href="{{ route('returns.report') }}" class="btn btn-secondary">View Return Reports</a>
            </div>
        @endif

        <div class="card shadow-lg border-0">
            <div class="card-body">
                <form action="{{ route('returns.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason for Return</label>
                        <textarea class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" rows="4" required>{{ old('reason') }}</textarea>
                        @error('reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="refund_method" class="form-label">Refund Method</label>
                        <select class="form-select @error('refund_method') is-invalid @enderror" id="refund_method" name="refund_method" required>
                            <option value="" disabled selected>Select Refund Method</option>
                            <option value="cash" {{ old('refund_method') == 'cash' ? 'selected' : '' }}>Cash Refund</option>
                            <option value="replacement" {{ old('refund_method') == 'replacement' ? 'selected' : '' }}>Replacement</option>
                        </select>
                        @error('refund_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="pickup_location" class="form-label">Pickup Location</label>
                        <select class="form-select @error('pickup_location') is-invalid @enderror" id="pickup_location" name="pickup_location" required>
                            <option value="" disabled selected>Select Pickup Location</option>
                            <option value="store" {{ old('pickup_location') == 'store' ? 'selected' : '' }}>Store</option>
                            <option value="home" {{ old('pickup_location') == 'home' ? 'selected' : '' }}>Home Pickup</option>
                        </select>
                        @error('pickup_location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="evidence" class="form-label">Upload Evidence (Optional)</label>
                        <input type="file" class="form-control @error('evidence') is-invalid @enderror" id="evidence" name="evidence">
                        @error('evidence')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Submit Return Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection