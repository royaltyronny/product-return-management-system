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

    /* Product Card Body Background */
    .card-body {
        background-color:rgb(151, 231, 243) !important; /* Nude color */
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
        <h1 class="mb-4 text-center">
            <i class="fas fa-shoe-prints"></i> Shoe Collection
        </h1>

        <div class="row">
            @forelse($shoes as $shoe)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-lg border-0">
                        <a href="{{ route('products.show', $shoe->id) }}" class="text-decoration-none text-dark">
                            <img src="{{ $shoe->image_url }}" class="card-img-top" alt="{{ $shoe->name }}" style="height: 220px; object-fit: cover; border-bottom: 3px solid #00796b;">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title text-center fw-bold">{{ $shoe->name }}</h5>
                            <p class="card-text text-muted">{{ Str::limit($shoe->description, 100) }}</p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-palette me-2"></i> <strong>Color:</strong> {{ $shoe->color }}</li>
                                <li><i class="fas fa-ruler me-2"></i> <strong>Size:</strong> {{ $shoe->size }}</li>
                                <li><i class="fas fa-dollar-sign me-2"></i> <strong>Price:</strong> ${{ number_format($shoe->price, 2) }}</li>
                            </ul>

                            <!-- Conditionally show return button -->
                            @if($shoe->can_be_returned)
                            <div class="text-center">
                            
    <a href= "{{ route('returns.return') }}" class="btn btn-primary">Request Return</a>
</div>



                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p class="text-muted"><i class="fas fa-box-open"></i> No shoes found.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection