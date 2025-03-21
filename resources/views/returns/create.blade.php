@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h2>Create Return Request</h2>
                </div>
                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('returns.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        @if (isset($order))
                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                            
                            <div class="form-group mb-4">
                                <label class="form-label fw-bold">Order Information</label>
                                <div class="card">
                                    <div class="card-body">
                                        <p><strong>Order #:</strong> {{ $order->order_number }}</p>
                                        <p><strong>Date:</strong> {{ $order->created_at->format('F j, Y') }}</p>
                                        <p><strong>Total:</strong> ${{ number_format($order->total, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            @if (!isset($product) && $order->orderItems->count() > 0)
                                <div class="form-group mb-4">
                                    <label for="product_id" class="form-label fw-bold">Select Product to Return</label>
                                    <select name="product_id" id="product_id" class="form-control @error('product_id') is-invalid @enderror" required>
                                        <option value="">-- Select Product --</option>
                                        @foreach ($order->orderItems as $item)
                                            <option value="{{ $item->product_id }}" {{ old('product_id') == $item->product_id ? 'selected' : '' }}>
                                                {{ $item->product->name }} - ${{ number_format($item->price, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            @endif
                        @else
                            <div class="form-group mb-4">
                                <label for="order_id" class="form-label fw-bold">Order ID</label>
                                <input type="text" name="order_id" id="order_id" class="form-control @error('order_id') is-invalid @enderror" value="{{ old('order_id') }}" required>
                                @error('order_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            
                            <div class="form-group mb-4">
                                <label for="product_id" class="form-label fw-bold">Product ID</label>
                                <input type="text" name="product_id" id="product_id" class="form-control @error('product_id') is-invalid @enderror" value="{{ old('product_id', $product->id ?? '') }}" required>
                                @error('product_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        @endif
                        
                        @if (isset($product))
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            
                            <div class="form-group mb-4">
                                <label class="form-label fw-bold">Product Information</label>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                @if ($product->image)
                                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid">
                                                @else
                                                    <div class="bg-light text-center p-4">
                                                        <i class="fas fa-image fa-3x text-muted"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-9">
                                                <h5>{{ $product->name }}</h5>
                                                <p><strong>Price:</strong> ${{ number_format($product->price, 2) }}</p>
                                                <p><strong>SKU:</strong> {{ $product->sku }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="form-group mb-4">
                            <label for="return_category" class="form-label fw-bold">Return Reason Category</label>
                            <select name="return_category" id="return_category" class="form-control @error('return_category') is-invalid @enderror" required>
                                <option value="">-- Select Category --</option>
                                @foreach ($returnCategories as $category)
                                    <option value="{{ $category }}" {{ old('return_category') == $category ? 'selected' : '' }}>
                                        {{ ucwords(str_replace('_', ' ', $category)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('return_category')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-4">
                            <label for="return_reason" class="form-label fw-bold">Detailed Reason</label>
                            <textarea name="return_reason" id="return_reason" rows="4" class="form-control @error('return_reason') is-invalid @enderror" required>{{ old('return_reason') }}</textarea>
                            @error('return_reason')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-4">
                            <label for="description" class="form-label fw-bold">Additional Details (Optional)</label>
                            <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-4">
                            <label for="evidence_images" class="form-label fw-bold">Upload Images (Optional)</label>
                            <div class="input-group">
                                <input type="file" name="evidence_images[]" id="evidence_images" class="form-control @error('evidence_images.*') is-invalid @enderror" multiple accept="image/*">
                                <label class="input-group-text" for="evidence_images">
                                    <i class="fas fa-upload"></i>
                                </label>
                            </div>
                            <small class="text-muted">You can upload up to 5 images (max 5MB each) to support your return request.</small>
                            @error('evidence_images.*')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-4">
                            <label for="refund_method" class="form-label fw-bold">Preferred Refund Method</label>
                            <select name="refund_method" id="refund_method" class="form-control @error('refund_method') is-invalid @enderror" required>
                                <option value="">-- Select Refund Method --</option>
                                <option value="original_payment" {{ old('refund_method') == 'original_payment' ? 'selected' : '' }}>
                                    Refund to Original Payment Method
                                </option>
                                <option value="store_credit" {{ old('refund_method') == 'store_credit' ? 'selected' : '' }}>
                                    Store Credit
                                </option>
                                <option value="bank_transfer" {{ old('refund_method') == 'bank_transfer' ? 'selected' : '' }}>
                                    Bank Transfer
                                </option>
                                <option value="replacement" {{ old('refund_method') == 'replacement' ? 'selected' : '' }}>
                                    Product Replacement
                                </option>
                            </select>
                            @error('refund_method')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <div class="alert alert-info">
                                <h5><i class="fas fa-info-circle"></i> Return Policy Information</h5>
                                <ul>
                                    <li>Returns must be initiated within 30 days of purchase.</li>
                                    <li>Items must be in original condition with all packaging and accessories.</li>
                                    <li>A restocking fee may apply for non-defective returns.</li>
                                    <li>You will receive return shipping instructions after your request is approved.</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="form-group d-flex justify-content-between">
                            <a href="{{ route('returns.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Returns
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Submit Return Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Preview uploaded images
    document.getElementById('evidence_images').addEventListener('change', function(event) {
        const previewContainer = document.createElement('div');
        previewContainer.className = 'mt-3 row';
        previewContainer.id = 'image-previews';
        
        // Remove any existing previews
        const existingPreview = document.getElementById('image-previews');
        if (existingPreview) {
            existingPreview.remove();
        }
        
        const files = event.target.files;
        
        if (files.length > 0) {
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();
                
                const previewCol = document.createElement('div');
                previewCol.className = 'col-md-3 mb-3';
                
                const previewCard = document.createElement('div');
                previewCard.className = 'card h-100';
                
                const previewImage = document.createElement('img');
                previewImage.className = 'card-img-top';
                previewImage.style.height = '150px';
                previewImage.style.objectFit = 'cover';
                
                const cardBody = document.createElement('div');
                cardBody.className = 'card-body p-2 text-center';
                cardBody.innerHTML = `<small>${file.name.substring(0, 20)}${file.name.length > 20 ? '...' : ''}</small>`;
                
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                }
                
                reader.readAsDataURL(file);
                
                previewCard.appendChild(previewImage);
                previewCard.appendChild(cardBody);
                previewCol.appendChild(previewCard);
                previewContainer.appendChild(previewCol);
            }
            
            document.getElementById('evidence_images').parentNode.parentNode.appendChild(previewContainer);
        }
    });
</script>
@endsection
