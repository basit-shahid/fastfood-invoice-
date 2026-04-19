@extends('layouts.app')

@section('title', 'Menu Inventory')

@push('styles')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 40px;
        border-bottom: 2px solid var(--card-border);
        padding-bottom: 20px;
    }

    /* Card Styling */
    .menu-item-card {
        background-color: var(--card-bg);
        border-radius: 20px;
        border: 1px solid var(--card-border);
        box-shadow: 0 10px 30px rgba(0,0,0,0.02);
        padding: 20px;
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.35s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        gap: 15px;
        position: relative;
        overflow: hidden;
    }

    .menu-item-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: linear-gradient(90deg, #ffc107, #ff9f43);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.35s ease;
    }

    .menu-item-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.08);
    }
    .menu-item-card:hover::before {
        transform: scaleX(1);
    }

    /* Dark Mode Details */
    html.dark .menu-item-card {
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }
    html.dark .menu-item-card:hover {
        box-shadow: 0 20px 40px rgba(0,0,0,0.5);
    }

    .item-img-container {
        width: 100%;
        aspect-ratio: 16/10;
        border-radius: 12px;
        overflow: hidden;
        background: rgba(0,0,0,0.02);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .item-img-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .menu-item-card:hover .item-img-container img {
        transform: scale(1.05);
    }

    .status-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        z-index: 10;
    }

    .status-available {
        background: #10b981;
        color: white;
    }

    .status-unavailable {
        background: #ef4444;
        color: white;
    }

    .cat-badge {
        display: inline-block;
        padding: 5px 12px;
        background: rgba(0,0,0,0.05);
        color: var(--muted-color);
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
    }
    html.dark .cat-badge {
        background: rgba(255,255,255,0.05);
    }

    .prep-time {
        font-size: 0.8rem;
        color: var(--muted-color);
        display: flex;
        align-items: center;
        gap: 5px;
        font-weight: 600;
    }

    .price-tag {
        font-weight: 900;
        color: var(--body-color);
        font-size: 1.4rem;
    }
    
    .item-desc {
        font-size: 0.85rem;
        color: var(--muted-color);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin-bottom: 0;
        line-height: 1.5;
    }

    .btn-manage {
        flex: 1;
        padding: 10px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.85rem;
        transition: all 0.2s;
        border: 1px solid var(--card-border);
        color: var(--body-color);
        background: transparent;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    .btn-manage:hover {
        background: rgba(0,0,0,0.05);
        color: var(--body-color);
    }
    
    html.dark .btn-manage:hover {
        background: rgba(255,255,255,0.08);
        color: var(--body-color);
    }

    .add-btn {
        background: linear-gradient(135deg, var(--primary-yellow) 0%, var(--dark-yellow) 100%);
        color: var(--black);
        padding: 12px 25px;
        border-radius: 15px;
        font-weight: 800;
        transition: all 0.3s;
        border: none;
        box-shadow: 0 5px 15px rgba(255,193,7,0.3);
        display: inline-flex;
        align-items: center;
    }

    .add-btn:hover {
        color: var(--black);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255,193,7,0.4);
    }

    /* Staggered Reveal */
    .reveal {
        opacity: 1;
        transform: none;
        transition: opacity 0.15s ease;
    }
    .reveal.visible {
        opacity: 1;
        transform: none;
    }

    .btn-manage.is-loading {
        opacity: 0.7;
        pointer-events: none;
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="page-header animate__animated animate__fadeInDown">
        <div>
            <h2 class="fw-900 mb-1">Inventory & Menu</h2>
            <p class="text-muted mb-0">Detailed list of your offerings and current stock availability</p>
        </div>
        @if(auth()->user()->role != 'cashier')
            <a href="{{ route('menu.create') }}" class="add-btn text-decoration-none">
                <i class="fas fa-plus me-2"></i> Add New Item
            </a>
        @endif
    </div>

    <div class="row g-4" id="menuGrid">
        @foreach($menuItems as $item)
        <div class="col-md-4 col-lg-3 reveal">
            <div class="menu-item-card">
                <div class="item-img-container">
                    <div class="status-badge js-status-badge {{ $item->is_available ? 'status-available' : 'status-unavailable' }}">
                        @if($item->is_available)
                            <i class="fas fa-check-circle me-1"></i> In Stock
                        @else
                            <i class="fas fa-times-circle me-1"></i> Sold Out
                        @endif
                    </div>
                    @if($item->image)
                        <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}" loading="lazy">
                    @else
                        <i class="fas fa-utensils fa-3x text-muted opacity-25"></i>
                    @endif
                </div>
                
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span class="cat-badge">{{ $item->category }}</span>
                    @if($item->preparation_time)
                    <span class="prep-time"><i class="far fa-clock"></i> {{ $item->preparation_time }}m</span>
                    @endif
                </div>
                
                <div>
                    <h5 class="fw-800 mb-1">{{ $item->name }}</h5>
                    <p class="item-desc" title="{{ $item->description }}">{{ $item->description ?: 'No description available for this item.' }}</p>
                </div>
                
                <div class="d-flex justify-content-between align-items-end mt-2">
                    <div class="price-tag">Rs. {{ number_format($item->price, 0) }}</div>
                </div>
                
                @if(auth()->user()->role != 'cashier')
                <div class="d-flex gap-2 mt-auto border-top pt-3" style="border-color: var(--card-border) !important;">
                    <form action="{{ route('menu.toggle-availability', $item) }}" method="POST" class="flex-grow-1 mb-0 js-toggle-availability-form">
                        @csrf
                        <button type="submit" class="btn-manage w-100 js-toggle-btn" style="padding: 8px;" data-available="{{ $item->is_available ? '1' : '0' }}">
                            <i class="fas {{ $item->is_available ? 'fa-ban text-warning' : 'fa-check text-success' }} me-1"></i>
                            <span class="js-toggle-label">{{ $item->is_available ? 'Disable' : 'Enable' }}</span>
                        </button>
                    </form>
                    <a href="{{ route('menu.edit', $item) }}" class="btn-manage text-center" style="padding: 8px; flex: 0 0 auto; width: 42px;" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    
    @if($menuCount === 0)
    <div class="text-center py-5 reveal">
        <div class="mb-4">
            <i class="fas fa-box-open fa-4x text-muted opacity-50"></i>
        </div>
        <h4 class="fw-bold">No items found</h4>
        <p class="text-muted">Your inventory is empty. Start adding some delicious items!</p>
        @if(auth()->user()->role != 'cashier')
            <a href="{{ route('menu.create') }}" class="btn-primary d-inline-flex align-items-center mt-3 text-decoration-none px-4 py-2 rounded-pill fw-bold">
                <i class="fas fa-plus me-2"></i> Add First Item
            </a>
        @endif
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        document.querySelectorAll('.js-toggle-availability-form').forEach((form) => {
            form.addEventListener('submit', async function(event) {
                event.preventDefault();

                const button = form.querySelector('.js-toggle-btn');
                const label = form.querySelector('.js-toggle-label');
                const card = form.closest('.menu-item-card');
                const badge = card ? card.querySelector('.js-status-badge') : null;
                const icon = button ? button.querySelector('i') : null;

                if (!button || !label || !badge || !csrfToken) {
                    form.submit();
                    return;
                }

                button.classList.add('is-loading');

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Toggle request failed');
                    }

                    const data = await response.json();
                    const available = Boolean(data.is_available);

                    button.dataset.available = available ? '1' : '0';
                    label.textContent = available ? 'Disable' : 'Enable';

                    badge.classList.toggle('status-available', available);
                    badge.classList.toggle('status-unavailable', !available);
                    badge.innerHTML = available
                        ? '<i class="fas fa-check-circle me-1"></i> In Stock'
                        : '<i class="fas fa-times-circle me-1"></i> Sold Out';

                    if (icon) {
                        icon.classList.toggle('fa-ban', available);
                        icon.classList.toggle('text-warning', available);
                        icon.classList.toggle('fa-check', !available);
                        icon.classList.toggle('text-success', !available);
                    }
                } catch (error) {
                    form.submit();
                } finally {
                    button.classList.remove('is-loading');
                }
            });
        });

        document.querySelectorAll('.reveal').forEach((el) => el.classList.add('visible'));
    });
</script>
@endpush