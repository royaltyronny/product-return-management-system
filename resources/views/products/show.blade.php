@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4 text-center">{{ $shoe->name }}</h1>

    <div class="card mb-4 shadow-sm">
        <img src="{{ $shoe->image_url }}" class="card-img-top" alt="{{ $shoe->name }}" style="height: 300px; object-fit: cover;">
        <div class="card-body">
            <h5 class="card-title">{{ $shoe->name }}</h5>
            <p class="card-text">{{ $shoe->description }}</p>
            <ul class="list-unstyled">
                <li><strong>Color:</strong> {{ $shoe->color }}</li>
                <li><strong>Size:</strong> {{ $shoe->size }}</li>
                <li><strong>Price:</strong> ${{ number_format($shoe->price, 2) }}</li>
            </ul>
            <form action="{{ route('returns.create') }}" method="POST" class="mt-3">
                @csrf
                <input type="hidden" name="shoe_id" value="{{ $shoe->id }}">
                <button type="submit" class="btn btn-danger">Return Item</button>
            </form>
        </div>
    </div>
</div>
@endsection
