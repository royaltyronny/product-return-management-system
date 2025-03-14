@extends('layouts.app')

@section('content')
<div class="container py-4">
    @isset($shoe)
        <h1 class="mb-4 text-center">
            <i class="fas fa-undo-alt"></i> Return {{ $shoe->name }}
        </h1>

        <form action="{{ route('returns.store', $shoe->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="reason" class="form-label">Reason for Return</label>
                <select name="reason" id="reason" class="form-control" required>
                    <option value="Defective">Defective</option>
                    <option value="Wrong Size">Wrong Size</option>
                    <option value="Wrong Product">Wrong Product</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="image_evidence" class="form-label">Image Evidence (optional)</label>
                <input type="file" name="image_evidence" id="image_evidence" class="form-control">
            </div>

            <div class="mb-3">
                <label for="delivery_method" class="form-label">Delivery Method</label>
                <select name="delivery_method" id="delivery_method" class="form-control" required>
                    <option value="Courier">Courier</option>
                    <option value="In-Store">In-Store</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="refund_method" class="form-label">Refund Method</label>
                <select name="refund_method" id="refund_method" class="form-control" required>
                    <option value="Store Credit">Store Credit</option>
                    <option value="Original Payment Method">Original Payment Method</option>
                </select>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Submit Return Request
                </button>
            </div>
        </form>
    @else
        <h1 class="mb-4 text-center text-danger">Shoe not found</h1>
    @endisset
</div>
@endsection