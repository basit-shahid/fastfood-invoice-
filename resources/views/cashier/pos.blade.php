<!-- resources/views/cashier/pos.blade.php -->
@extends('layouts.app')

@section('title', 'Point of Sale')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/animate.min.css') }}"/>
<style>
    :root {
        --pos-bg: #f8fafc;
        --accent-color: #ffc107;
        --accent-dark: #e5ac00;
        --card-shadow: 0 4px 15px rgba(0,0,0,0.03);
        --sidebar-width: 420px;
    }

    body {
        background-color: var(--pos-bg);
        overflow: hidden;
    }

    .pos-layout {
        display: flex;
        height: calc(100vh - 70px);
        overflow: hidden;
    }

    /* Main Menu Area */
    .menu-panel {
        flex: 1;
        padding: 30px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 30px;
    }

    .menu-header {
        position: sticky;
        top: -30px; 
        background: var(--pos-bg);
        z-index: 100;
        padding-bottom: 15px; /* Reduced */
        display: flex;
        flex-direction: column;
        gap: 15px; /* Reduced gap */
        margin-top: -10px;
    }

    .top-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
        padding-top: 10px;
    }

    .search-input-group {
        position: relative;
        flex: 1;
        max-width: 400px;
    }

    .search-input-group i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }

    .clean-input {
        padding: 12px 15px 12px 45px;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        background: white;
        width: 100%;
        transition: all 0.3s;
        box-shadow: var(--card-shadow);
    }

    .clean-input:focus {
        outline: none;
        border-color: var(--accent-color);
        box-shadow: 0 0 0 4px rgba(255,193,7,0.1);
    }

    .category-nav {
        display: flex;
        gap: 15px;
        overflow-x: auto;
        padding: 5px 0;
        scrollbar-width: none;
    }
    .category-nav::-webkit-scrollbar { display: none; }

    .cat-pill {
        white-space: nowrap;
        padding: 12px 28px;
        border-radius: 50px;
        background: white;
        border: 2px solid #e2e8f0;
        color: #475569;
        font-weight: 700;
        font-size: 1rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        box-shadow: var(--card-shadow);
    }

    .cat-pill.active {
        background: var(--accent-color);
        color: #000;
        border-color: #000;
        box-shadow: 0 4px 15px rgba(255,193,7,0.4);
        position: relative;
        z-index: 5;
    }

    /* Performance & Spacing Optimizations */
    @media (min-width: 1024px) {
        .pos-item-card {
            transition: border-color 0.2s, box-shadow 0.2s; /* Lighter transition for speed */
        }
        .pos-item-card:hover {
            transform: none; /* Removed for performance on some laptops */
            border-color: var(--accent-color);
            box-shadow: 0 6px 15px rgba(0,0,0,0.06);
        }
        .item-grid {
            grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); /* Slightly smaller for airy feel */
            gap: 25px;
        }
        .menu-panel { padding: 40px; } /* More spacious on larger screens */
        :root { --sidebar-width: 400px; }
    }

    /* Item Grid - Compact Size */
    .item-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 12px;
    }

    .pos-item-card {
        background: white;
        border-radius: 16px;
        padding: 8px;
        transition: border-color 0.2s;
        cursor: pointer;
        border: 2px solid transparent;
        text-align: center;
        box-shadow: var(--card-shadow);
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .pos-item-card:hover {
        border-color: var(--accent-color);
        box-shadow: 0 6px 15px rgba(0,0,0,0.06);
    }

    .item-img-wrapper {
        height: 100px; /* Reduced fixed height for compactness */
        border-radius: 12px;
        overflow: hidden;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .item-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .item-info h6 {
        margin: 0;
        font-weight: 700;
        color: #0f172a;
        font-size: 0.9rem; /* Smaller font */
        line-height: 1.1;
        min-height: 2.2em;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .item-price {
        font-weight: 800;
        color: var(--accent-dark);
        font-size: 0.95rem; /* Smaller font */
    }

    /* Cart Sidebar */
    .cart-panel {
        width: var(--sidebar-width);
        background: white;
        border-left: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        z-index: 100;
        transition: transform 0.3s ease;
    }

    .cart-top {
        padding: 30px;
        border-bottom: 1px solid #f1f5f9;
    }

    .cart-body {
        flex: 1;
        overflow-y: auto;
        padding: 30px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .empty-state {
        margin: auto;
        text-align: center;
        color: #94a3b8;
    }

    .cart-item-row {
        background: #f8fafc;
        border-radius: 20px;
        padding: 15px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        animation: fadeIn 0.3s ease;
    }

    .cart-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .cart-item-controls {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 5px;
    }

    .qty-control {
        display: flex;
        align-items: center;
        gap: 12px;
        background: white;
        padding: 5px 10px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }

    .qty-btn {
        background: none;
        border: none;
        color: #64748b;
        padding: 2px 8px;
        border-radius: 6px;
        transition: all 0.2s;
    }
    .qty-btn:hover { background: #f1f5f9; color: var(--accent-color); }

    .cart-footer {
        padding: 20px 30px;
        background: #fff;
        border-top: 1px solid #f1f5f9;
        box-shadow: 0 -10px 40px rgba(0,0,0,0.02);
    }

    /* Vertical Responsiveness for Laptop/Small Screens */
    @media (max-height: 800px) {
        .cart-top { padding: 15px 30px; }
        .cart-body { padding: 15px 20px; gap: 10px; }
        .cart-footer { padding: 15px 20px; }
        .summary-row { margin-bottom: 5px; font-size: 0.9rem; }
        .total-display { margin: 10px 0; padding-top: 10px; }
        .total-val { font-size: 1.8rem; }
        .checkout-btn { padding: 12px; font-size: 1rem; }
        .cart-item-row { padding: 10px; border-radius: 12px; }
    }

    @media (max-height: 650px) {
        .cart-top { display: none !important; } /* Hide title on very short screens */
        .cart-footer .row.g-2 { margin-bottom: 10px !important; }
        .total-display { margin: 5px 0; }
        .total-val { font-size: 1.5rem; }
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        color: #64748b;
        font-weight: 500;
    }

    .total-display {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 20px 0;
        padding-top: 20px;
        border-top: 2px dashed #e2e8f0;
    }

    .total-val {
        font-size: 2.2rem;
        font-weight: 900;
        letter-spacing: -1px;
    }

    .checkout-btn {
        width: 100%;
        padding: 18px;
        border-radius: 20px;
        background: #0f172a;
        color: white;
        border: none;
        font-weight: 700;
        font-size: 1.1rem;
        transition: all 0.3s;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.2);
    }

    .checkout-btn:hover {
        background: #000;
        transform: translateY(-2px);
        box-shadow: 0 15px 30px rgba(15, 23, 42, 0.3);
    }

    /* Advanced Responsiveness */
    @media (max-width: 1200px) {
        .cart-panel { 
            position: fixed;
            right: 0;
            top: 0;
            bottom: 0;
            width: 400px;
            transform: translateX(100%);
            box-shadow: -20px 0 60px rgba(0,0,0,0.1);
        }
        .cart-panel.open { transform: translateX(0); }
        .mobile-cart-toggle { display: block !important; }
    }

    @media (max-width: 768px) {
        .menu-panel { 
            padding: 15px; 
            padding-bottom: 120px; /* Extra padding for mobile cart toggle */
            gap: 20px; 
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch;
        }
        .top-controls { flex-direction: column; align-items: flex-start; gap: 15px; }
        .search-input-group { max-width: 100%; }
        .item-grid { grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; }
        .cart-panel { width: 100%; } /* Full width cart on mobile */
        .category-nav { top: -15px; padding: 10px 0; }
        .pos-layout { height: calc(100vh - 60px); }
    }

    @media (max-width: 480px) {
        .item-grid { grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 10px; }
        .cat-pill { padding: 10px 20px; font-size: 0.9rem; }
        .total-val { font-size: 1.8rem; }
        .menu-panel { padding-bottom: 150px; } /* Even more space for smaller phones */
    }

    .mobile-cart-toggle {
        display: none;
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: var(--accent-color);
        width: 65px;
        height: 65px;
        border-radius: 50%;
        box-shadow: 0 10px 30px rgba(255,193,7,0.4);
        border: none;
        z-index: 1000;
        font-size: 1.5rem;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .mobile-cart-toggle:active { transform: scale(0.9); }

    .badge-cart {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #dc3545;
        color: white;
        border-radius: 50%;
        padding: 5px 10px;
        font-size: 12px;
        font-weight: 800;
    }

    /* Modal Styling */
    .modal-content { border-radius: 30px; border: none; padding: 20px; }
    .nice-textarea {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 15px;
    }
</style>
@endpush

@section('content')
<div class="pos-layout">
    <!-- Menu Section -->
    <div class="menu-panel">
        <div class="menu-header">
            <div class="top-controls">
                <h2 class="fw-900 mb-0">Discover Menu</h2>
                <div class="search-input-group">
                    <i class="fas fa-search"></i>
                    <input type="text" id="menu-search" class="clean-input" placeholder="Search by name or category...">
                </div>
            </div>

            <div class="category-nav">
                <div class="cat-pill active" data-category="all">All Items</div>
                @foreach($categories as $category => $items)
                    <div class="cat-pill" data-category="{{ Str::slug($category) }}">
                        {{ $category }}
                    </div>
                @endforeach
            </div>
        </div>

        <div class="item-grid" id="item-grid">
            @foreach($menuItems as $item)
                <div class="pos-item-card" 
                     data-id="{{ $item->id }}" 
                     data-name="{{ $item->name }}" 
                     data-price="{{ $item->price }}"
                     data-category="{{ Str::slug($item->category ?? 'uncategorized') }}">
                    <div class="item-img-wrapper">
                        @if($item->image)
                            <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}">
                        @else
                            <i class="fas fa-utensils fa-3x text-muted opacity-25"></i>
                        @endif
                    </div>
                    <div class="item-info">
                        <h6>{{ $item->name }}</h6>
                        <div class="item-price">PKR {{ number_format($item->price, 0) }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Cart Sidebar -->
    <div class="cart-panel" id="cart-sidebar">
        <div class="cart-top d-flex justify-content-between align-items-center">
            <h4 class="fw-900 mb-0">Order Summary</h4>
            <button class="btn btn-link text-muted p-0 d-xl-none" onclick="toggleCart()">
                <i class="fas fa-times fs-4"></i>
            </button>
        </div>

        <div class="cart-body" id="cart-content">
            <div class="empty-state">
                <img src="{{ asset('images/empty-states/empty-bag.png') }}" style="width: 120px; opacity: 0.3;" class="mb-4">
                <h5>Bag is empty</h5>
                <p class="small">Add items to start your checkout</p>
            </div>
        </div>

        <div class="cart-footer">
            <div class="summary-row">
                <span>Subtotal</span>
                <span id="subtotal">PKR 0.00</span>
            </div>
            <div class="summary-row align-items-center">
                <span>Item Discount</span>
                <input type="number" id="discount" class="form-control form-control-sm text-end border-0 bg-light rounded-pill px-3" style="width: 80px;" value="0">
            </div>
            
            <div class="total-display">
                <span class="fw-bold text-muted">Payable Total</span>
                <span class="total-val text-warning" id="total">PKR 0.00</span>
            </div>

            <div class="row g-2 mb-4">
                <div class="col-6">
                    <label class="small fw-800 text-muted mb-1">PAYMENT</label>
                    <select id="payment-method" class="form-select border-0 bg-light rounded-pill px-3">
                        <option value="cash">💵 Cash</option>
                        <option value="card">💳 Card</option>
                        <option value="online">📱 Online</option>
                    </select>
                </div>
                <div class="col-6" id="cash-input-wrap">
                    <label class="small fw-800 text-muted mb-1">RECEIVED</label>
                    <input type="number" id="cash-received" class="form-control border-0 bg-light rounded-pill px-3" placeholder="0.00">
                </div>
            </div>

            <div id="change-wrap" class="d-none mb-4 animate__animated animate__fadeIn">
                <div class="p-3 bg-warning bg-opacity-10 rounded-4 d-flex justify-content-between">
                    <span class="fw-bold">Change to Return</span>
                    <span class="fw-900 fs-5" id="change-val">PKR 0.00</span>
                </div>
            </div>

            <button class="checkout-btn" id="process-order">
                CONCLUDE ORDER <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </div>
    </div>
</div>

<button class="mobile-cart-toggle" onclick="toggleCart()">
    <i class="fas fa-shopping-bag"></i>
    <span class="badge-cart" id="cart-count">0</span>
</button>

<!-- Instructions Modal -->
<div class="modal fade" id="noteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-900">Add Instructions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <textarea id="item-instructions" class="form-control nice-textarea" rows="4" placeholder="Anything specific? (Extra spicy, no onions, etc.)"></textarea>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-warning w-100 rounded-pill fw-bold" id="confirm-instructions">Save Instructions</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('vendor-scripts')
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
@endpush

@push('scripts')
<script>
let cart = [];
let editingIndex = null;

$(document).ready(function() {
    // Search Filter
    $('#menu-search').on('input', function() {
        const q = $(this).val().toLowerCase();
        $('.pos-item-card').each(function() {
            const name = $(this).data('name').toLowerCase();
            $(this).toggle(name.includes(q));
        });
    });

    // Category Filter
    $('.cat-pill').click(function() {
        $('.cat-pill').removeClass('active');
        $(this).addClass('active');
        const cat = $(this).data('category');
        if (cat === 'all') $('.pos-item-card').fadeIn();
        else {
            $('.pos-item-card').hide();
            $(`.pos-item-card[data-category="${cat}"]`).fadeIn();
        }
    });

    // Add Item
    $('.pos-item-card').click(function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const price = $(this).data('price');
        addItem(id, name, price);
    });

    // Math Updates
    $('#discount, #cash-received').on('input', renderTotals);
    $('#payment-method').change(function() {
        $('#cash-input-wrap').toggle($(this).val() === 'cash');
        renderTotals();
    });

    // Process
    $('#process-order').click(function() {
        if (cart.length === 0) return Swal.fire('Error', 'Cart is empty', 'error');
        
        const total = parseFloat($('#total').text().replace('PKR ', '').replace(',', ''));
        const received = parseFloat($('#cash-received').val()) || 0;
        const method = $('#payment-method').val();

        if (method === 'cash' && received < total) return Swal.fire('Error', 'Insufficient amount', 'error');

        $.ajax({
            url: '{{ route("orders.store") }}',
            method: 'POST',
            data: {
                items: cart.map(i => ({ id: i.id, quantity: i.quantity, instructions: i.instructions })),
                discount: parseFloat($('#discount').val()) || 0,
                payment_method: method,
                cash_received: received
            },
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(res) {
                Swal.fire('Success', 'Order completed!', 'success').then(() => {
                    window.open('/orders/' + res.order.id + '/invoice?print=1', '_blank');
                    cart = [];
                    renderCart();
                    $('#discount').val(0);
                    $('#cash-received').val('');
                });
            }
        });
    });
});

function addItem(id, name, price) {
    const idx = cart.findIndex(i => i.id === id);
    if (idx > -1) cart[idx].quantity++;
    else cart.push({ id, name, price, quantity: 1, instructions: '' });
    renderCart();
}

function renderCart() {
    const wrap = $('#cart-content');
    if (cart.length === 0) {
        wrap.html('<div class="empty-state"><img src="{{ asset('images/empty-states/empty-bag.png') }}" style="width: 120px; opacity: 0.3;" class="mb-4"><h5>Bag is empty</h5><p class="small">Add items to start your checkout</p></div>');
        $('#cart-count').text(0);
        renderTotals();
        return;
    }

    let html = '';
    let count = 0;
    cart.forEach((item, i) => {
        count += item.quantity;
        html += `
            <div class="cart-item-row">
                <div class="cart-item-header">
                    <span class="fw-bold">${item.name}</span>
                    <span class="fw-900 text-dark">PKR ${(item.price * item.quantity).toFixed(0)}</span>
                </div>
                <div class="cart-item-controls">
                    <div class="qty-control">
                        <button class="qty-btn" onclick="updateQty(${i}, -1)"><i class="fas fa-minus small"></i></button>
                        <span class="fw-bold">${item.quantity}</span>
                        <button class="qty-btn" onclick="updateQty(${i}, 1)"><i class="fas fa-plus small"></i></button>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="qty-btn text-warning" onclick="editNote(${i})" title="Add Instruction">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="qty-btn text-danger" onclick="remove(${i})">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
                ${item.instructions ? `<div class="small text-muted italic">"${item.instructions}"</div>` : ''}
            </div>
        `;
    });
    wrap.html(html);
    $('#cart-count').text(count);
    renderTotals();
}

function updateQty(idx, d) {
    cart[idx].quantity += d;
    if (cart[idx].quantity <= 0) cart.splice(idx, 1);
    renderCart();
}

function remove(idx) {
    cart.splice(idx, 1);
    renderCart();
}

function editNote(idx) {
    editingIndex = idx;
    $('#item-instructions').val(cart[idx].instructions);
    new bootstrap.Modal('#noteModal').show();
}

$('#confirm-instructions').click(function() {
    cart[editingIndex].instructions = $('#item-instructions').val();
    bootstrap.Modal.getInstance('#noteModal').hide();
    renderCart();
});

function renderTotals() {
    const sub = cart.reduce((acc, i) => acc + (i.price * i.quantity), 0);
    const disc = parseFloat($('#discount').val()) || 0;
    const total = Math.max(0, sub - disc);
    
    $('#subtotal').text('PKR ' + sub.toLocaleString());
    $('#total').text('PKR ' + total.toLocaleString());

    const recv = parseFloat($('#cash-received').val()) || 0;
    if (recv >= total && total > 0) {
        $('#change-wrap').removeClass('d-none');
        $('#change-val').text('PKR ' + (recv - total).toLocaleString());
    } else {
        $('#change-wrap').addClass('d-none');
    }
}

function toggleCart() {
    $('#cart-sidebar').toggleClass('open');
}
</script>
<script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
@endpush
