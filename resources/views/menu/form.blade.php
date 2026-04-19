@extends('layouts.app')

@section('title', isset($menuItem) ? 'Edit Menu Item' : 'Add Menu Item')

@push('styles')
<style>
    .form-modern-card {
        background: white;
        border-radius: 30px;
        border: none;
        box-shadow: 0 20px 50px rgba(0,0,0,0.05);
        padding: 40px;
        margin-top: 20px;
    }

    .form-label {
        font-weight: 700;
        color: #334155;
        margin-bottom: 10px;
        font-size: 0.9rem;
    }

    .form-control, .form-select {
        border-radius: 15px;
        padding: 12px 20px;
        border: 2px solid #f1f5f9;
        background: #f8fafc;
        transition: all 0.3s;
        font-weight: 500;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--accent-color);
        background: white;
        box-shadow: 0 0 0 4px rgba(255,193,7,0.1);
        outline: none;
    }

    .btn-save {
        background: #ffc107;
        color: #000;
        padding: 15px 30px;
        border-radius: 18px;
        font-weight: 700;
        border: none;
        transition: all 0.3s;
        width: 100%;
        margin-top: 20px;
    }

    .btn-save:hover {
        background: #ffb300;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(255,193,7,0.2);
    }

    .back-btn {
        color: #64748b;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 20px;
        transition: color 0.2s;
    }

    .back-btn:hover { color: #000; }

    .required-star { color: #ef4444; }

    .image-upload-wrapper {
        border: 2px dashed #e2e8f0;
        border-radius: 20px;
        padding: 30px;
        text-align: center;
        background: #f8fafc;
        transition: all 0.3s;
    }

    .image-upload-wrapper:hover {
        border-color: var(--accent-color);
        background: #fff;
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <a href="{{ route('menu.index') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Menu List
            </a>

            <div class="form-modern-card animate__animated animate__fadeIn">
                <div class="text-center mb-5">
                    <h2 class="fw-900 mb-2">{{ isset($menuItem) ? 'Edit Item' : 'Add New Item' }}</h2>
                    <p class="text-muted">Fill in the details for your new menu masterpiece</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger border-0 rounded-4 mb-4">
                        <ul class="mb-0 fw-600 p-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ isset($menuItem) ? route('menu.update', $menuItem) : route('menu.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($menuItem))
                        @method('PUT')
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="name" class="form-label">ITEM NAME <span class="required-star">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $menuItem->name ?? '') }}" placeholder="e.g. Zinger Burger" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="category" class="form-label">CATEGORY <span class="required-star">*</span></label>
                            <input type="text" class="form-control" id="category" name="category" value="{{ old('category', $menuItem->category ?? '') }}" placeholder="e.g. Burgers" required list="categories-list">
                            <datalist id="categories-list">
                                <option value="Burgers">
                                <option value="Drinks">
                                <option value="Sides">
                                <option value="Desserts">
                                <option value="Combos">
                            </datalist>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label">DESCRIPTION</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Describe this item...">{{ old('description', $menuItem->description ?? '') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="price" class="form-label">PRICE (₱) <span class="required-star">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" value="{{ old('price', $menuItem->price ?? '') }}" placeholder="0.00" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="preparation_time" class="form-label">PREP TIME (MINS)</label>
                            <input type="number" min="1" class="form-control" id="preparation_time" name="preparation_time" value="{{ old('preparation_time', $menuItem->preparation_time ?? 5) }}">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">ITEM IMAGE</label>
                        <div class="image-upload-wrapper">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                            <input class="form-control" type="file" id="image" name="image" accept="image/*">
                            @if(isset($menuItem) && $menuItem->image)
                                <div class="mt-3">
                                    <div class="badge bg-info text-dark">Current image exists</div>
                                </div>
                            @endif
                            <p class="text-muted small mt-2 fw-500">Upload a crisp image of the product</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="stock_id" class="form-label">LINKED STOCK ITEM (OPTIONAL)</label>
                        <select name="stock_id" id="stock_id" class="form-select">
                            <option value="">-- No Stock Deduction --</option>
                            @foreach($stocks as $stock)
                                <option value="{{ $stock->id }}" {{ old('stock_id', $menuItem->stock_id ?? '') == $stock->id ? 'selected' : '' }}>
                                    {{ $stock->item_name }} (Current: {{ $stock->quantity }} {{ $stock->unit }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-muted small mt-2">Connecting this item will automatically deduct stock when an order is placed.</p>
                    </div>

                    <div class="mb-4 p-3 rounded-4 border-1 border" style="background: var(--bg-color); border-color: var(--border-color) !important;">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="is_available" name="is_available" value="1" 
                                {{ old('is_available', $menuItem->is_available ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-700 ms-2" for="is_available" style="color: var(--text-color);">Item is Available for Sale</label>
                        </div>
                    </div>

                    <button type="submit" class="btn-save">
                        <i class="fas fa-save me-2"></i> {{ isset($menuItem) ? 'Update Menu Item' : 'Publish Menu Item' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
