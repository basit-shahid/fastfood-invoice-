@extends('layouts.app')

@section('title', 'Menu Management')

@push('styles')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .menu-item-card {
        background: white;
        border-radius: 24px;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.04);
        padding: 20px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
        gap: 15px;
        border: 1px solid rgba(0,0,0,0.02);
    }

    .menu-item-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.08);
    }

    .item-img-container {
        width: 100%;
        aspect-ratio: 1/1;
        border-radius: 18px;
        overflow: hidden;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .item-img-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .cat-badge {
        display: inline-block;
        padding: 5px 12px;
        background: #f1f5f9;
        color: #64748b;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 10px;
    }

    .price-tag {
        font-weight: 900;
        color: var(--accent-dark);
        font-size: 1.3rem;
    }

    .btn-manage {
        flex: 1;
        padding: 10px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.85rem;
        transition: all 0.2s;
    }

    .add-btn {
        background: #0f172a;
        color: white;
        padding: 12px 25px;
        border-radius: 15px;
        font-weight: 700;
        transition: all 0.3s;
        border: none;
    }

    .add-btn:hover {
        background: #000;
        color: white;
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="page-header">
        <div>
            <h2 class="fw-900 mb-1">Menu Items</h2>
            <p class="text-muted mb-0">Manage your delicious offerings</p>
        </div>
        @if(auth()->user()->role != 'cashier')
            <a href="{{ route('menu.create') }}" class="add-btn text-decoration-none">
                <i class="fas fa-plus me-2"></i> Add New Item
            </a>
        @endif
    </div>

    <div class="row g-4 animate__animated animate__fadeIn">
        @foreach(\App\Models\MenuItem::all() as $item)
        <div class="col-md-3">
            <div class="menu-item-card">
                <div class="item-img-container shadow-sm">
                    @if($item->image)
                        <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}">
                    @else
                        <i class="fas fa-utensils fa-3x text-muted opacity-25"></i>
                    @endif
                </div>
                <div>
                    <span class="cat-badge">{{ $item->category }}</span>
                    <h5 class="fw-800 text-dark mb-1">{{ $item->name }}</h5>
                    <div class="price-tag">₱{{ number_format($item->price, 0) }}</div>
                </div>
                
                @if(auth()->user()->role != 'cashier')
                <div class="d-flex gap-2 mt-auto">
                    <a href="{{ route('menu.edit', $item) }}" class="btn btn-light btn-manage border text-dark shadow-sm">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                    <form action="{{ route('menu.destroy', $item) }}" method="POST" class="flex-grow-1" onsubmit="return confirm('Delete this menu item?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-manage w-100">
                            <i class="fas fa-trash-alt me-1"></i> Delete
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection