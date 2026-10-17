<!-- resources/views/cashier/pos.blade.php -->
@extends('layouts.app')

@section('title', 'Point of Sale')

@section('content')
<div class="pos-layout" aria-label="Point of sale workspace">
    <!-- Menu Section -->
    <div class="menu-panel">
        <div class="menu-header">
            <div class="top-controls">
                <div><div class="pos-kicker">01 / THE MENU</div><h2>What’s cooking?</h2></div>
                <div class="search-input-group">
                    <i class="fas fa-search"></i>
                    <input type="search" id="menu-search" class="clean-input" aria-label="Search menu by name or category" placeholder="Search menu…  /">
                </div>
            </div>

            <div class="category-nav" role="group" aria-label="Filter menu by category">
                <button type="button" class="cat-pill active" data-category="all" aria-pressed="true">All items <span class="category-count">{{ $menuItems->count() }}</span></button>
                @foreach($categories as $category => $items)
                    <button type="button" class="cat-pill" data-category="{{ Str::slug($category) }}" aria-pressed="false">
                        {{ $category ?: 'Other' }} <span class="category-count">{{ $items->count() }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="item-grid" id="item-grid">
            @foreach($menuItems as $item)
                <button type="button" class="pos-item-card" aria-label="Add {{ $item->name }} to order"
                     data-id="{{ $item->id }}"
                     data-name="{{ $item->name }}"
                     data-price="{{ $item->price }}"
                     data-category="{{ Str::slug($item->category) }}" data-category-name="{{ $item->category }}">
                    <div class="item-img-wrapper">
                        @if($item->image)
                            <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}">
                        @else
                            <i class="fas fa-utensils fa-3x text-muted opacity-25"></i>
                        @endif
                    </div>
                    <div class="item-info">
                        <div class="item-category visually-hidden">{{ $item->category ?: 'Other' }}</div><h6>{{ $item->name }}</h6>
                        <div class="item-price">PKR {{ number_format($item->price, 0) }}</div><span class="item-add" aria-hidden="true"><i class="fas fa-plus"></i></span>
                    </div>
                </button>
            @endforeach
            <div class="no-menu-results" id="menu-empty" hidden><div class="empty-state-icon"><i class="fas fa-utensils"></i></div><h3>{{ $menuItems->isEmpty() ? 'Your menu is ready for its first item' : 'No matching items' }}</h3><p class="text-muted">{{ $menuItems->isEmpty() ? 'Add available products in the menu catalog to start taking orders.' : 'Try another search or category.' }}</p><a href="{{ route('menu.index') }}" class="btn btn-outline-secondary">Open menu catalog</a></div>
        </div>
    </div>

    <!-- Cart Sidebar -->
    <button type="button" class="cart-backdrop" id="cart-backdrop" aria-label="Close order" hidden></button>
    <section class="cart-panel" id="cart-sidebar" aria-label="Current order">
        <div class="cart-top d-flex justify-content-between align-items-center">
            <div><div class="pos-kicker">02 / THE ORDER</div><h4>On the ticket</h4><small id="order-item-count" aria-live="polite">No items added</small></div><button type="button" class="btn btn-link text-muted p-0" id="clear-order" aria-label="Clear current order" title="Clear order"><i class="fas fa-trash-alt"></i></button>
            <button class="btn btn-link text-muted p-0 cart-close" id="close-cart" aria-label="Close order">
                <i class="fas fa-times fs-4"></i>
            </button>
        </div>

        <div class="cart-body" id="cart-content">
            <div class="empty-state">
                <div class="ticket-mark" aria-hidden="true">＋</div>
                <h5>Nothing on the ticket.</h5>
                <p class="small">Tap something good to start.</p>
            </div>
        </div>

        <form class="cart-footer" id="checkout-form" action="{{ route('orders.store') }}" method="POST">
            @csrf
            <div id="checkout-items" hidden></div>
            @if($errors->any())<div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>@endif
            <div class="summary-row">
                <span>Subtotal</span>
                <span id="subtotal">PKR 0.00</span>
            </div>
            <details class="discount-details"><summary>Apply a discount</summary><div class="summary-row align-items-center mt-2">
                <label for="discount">Order discount (PKR)</label>
                <input type="number" id="discount" name="discount" class="form-control form-control-sm text-end border-0 bg-light rounded-pill px-3" style="width: 90px;" value="0" min="0" step="0.01" inputmode="decimal">
            </div>

            </details>
            <div class="total-display">
                <span class="fw-bold text-muted">Total due</span>
                <span class="total-val text-warning" id="total">PKR 0.00</span>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label for="payment-method" class="small fw-800 text-muted mb-1">Payment method</label>
                    <select id="payment-method" name="payment_method" class="form-select border-0 bg-light rounded-pill px-3">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="online">Online</option>
                    </select>
                </div>
                <div class="col-6" id="cash-input-wrap">
                    <div class="d-flex align-items-center justify-content-between mb-1"><label for="cash-received" class="small fw-800 text-muted">Cash received</label><button type="button" id="exact-cash" class="exact-cash">Exact</button></div>
                    <input type="number" id="cash-received" name="cash_received" required min="0" step="0.01" inputmode="decimal" class="form-control border-0 bg-light rounded-pill px-3" placeholder="0.00">
                </div>
            </div>

            <div id="change-wrap" class="d-none mb-3">
                <div class="p-3 bg-warning bg-opacity-10 rounded-4 d-flex justify-content-between">
                    <span class="fw-bold">Change to Return</span>
                    <span class="fw-900 fs-5" id="change-val">PKR 0.00</span>
                </div>
            </div>

            <div id="checkout-feedback" class="checkout-feedback" role="alert" aria-live="assertive" tabindex="-1"></div>
            <button type="submit" class="checkout-btn" id="process-order" disabled>
                <span>Place order</span><i class="fas fa-arrow-right" aria-hidden="true"></i>
            </button>
        </form>
    </section>
</div>

<button type="button" class="mobile-cart-toggle" id="open-cart" aria-controls="cart-sidebar" aria-expanded="false"><i class="fas fa-shopping-bag" aria-hidden="true"></i> View order <span class="badge-cart" id="cart-count">0</span><span class="mobile-cart-total" id="mobile-cart-total">PKR 0.00</span></button>

<!-- Instructions Modal -->
<div class="modal fade" id="noteModal" tabindex="-1" aria-labelledby="note-title">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-900" id="note-title">Item instructions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close instructions"></button>
            </div>
            <div class="modal-body">
                <label for="item-instructions" class="form-label">Preparation notes</label><textarea maxlength="500" id="item-instructions" class="form-control nice-textarea" rows="4" placeholder="Anything specific? (Extra spicy, no onions, etc.)"></textarea>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-warning w-100 rounded-pill fw-bold" id="confirm-instructions">Save Instructions</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>window.counterflowRegister = { storeUrl: @json(route('orders.store')), invoiceUrl: @json(url('/orders')), historyUrl: @json(route('orders.history')) };</script>
<script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/js/register.js') }}?v={{ substr(hash_file('sha256', public_path('assets/js/register.js')), 0, 12) }}"></script>
@endpush
