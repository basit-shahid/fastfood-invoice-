@extends('layouts.app')

@section('title', 'Manager Dashboard')

@push('styles')
<style>
    .welcome-card {
        background: linear-gradient(135deg, #ffc107 0%, #ffdb4d 100%);
        border-radius: 30px;
        padding: 40px;
        border: none;
        box-shadow: 0 20px 40px rgba(255, 193, 7, 0.2);
        position: relative;
        overflow: hidden;
        margin-bottom: 40px;
    }

    .welcome-card::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .stat-card {
        border-radius: 24px;
        padding: 30px;
        background: var(--card-bg);
        box-shadow: 0 10px 30px rgba(0,0,0,0.04);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.35s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        border: 1px solid var(--card-border);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 4px;
        background: linear-gradient(180deg, #ffc107, #ff9f43);
        border-radius: 4px 0 0 4px;
        transform: scaleY(0);
        transform-origin: bottom;
        transition: transform 0.35s cubic-bezier(0.34,1.56,0.64,1);
    }

    .stat-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 24px 48px rgba(0,0,0,0.1);
    }
    .stat-card:hover::before { transform: scaleY(1); }

    .stat-icon {
        width: 60px; height: 60px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 20px;
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 900;
        color: var(--body-color);
        margin-bottom: 5px;
    }

    .stat-label {
        color: var(--body-color);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.8rem;
    }

    .quick-link {
        margin-top: 20px;
        text-decoration: none;
        color: var(--body-color);
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: gap 0.3s, color 0.3s;
    }

    .quick-link:hover {
        gap: 12px;
        color: #e5ac00;
    }

    .time-badge {
        background: rgba(0,0,0,0.05);
        padding: 8px 20px;
        border-radius: 50px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .status-chip {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        border-radius: 999px;
        padding: 0.34rem 0.62rem;
    }

    .status-pending { background: #fff3cd; color: #7a5800; }
    .status-preparing { background: #dbeafe; color: #1e3a8a; }
    .status-ready { background: #d1fae5; color: #065f46; }
    .status-completed { background: #dcfce7; color: #166534; }
    .status-cancelled { background: #fee2e2; color: #991b1b; }

    html.dark .time-badge { background: rgba(255,255,255,0.08); color: var(--body-color); }
    html.dark .stat-card { box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
    html.dark h4, html.dark h5 { color: var(--body-color); }
    html.dark .status-pending { background: rgba(245, 158, 11, 0.2); color: #fcd34d; }
    html.dark .status-preparing { background: rgba(59, 130, 246, 0.2); color: #93c5fd; }
    html.dark .status-ready { background: rgba(16, 185, 129, 0.2); color: #6ee7b7; }
    html.dark .status-completed { background: rgba(34, 197, 94, 0.2); color: #86efac; }
    html.dark .status-cancelled { background: rgba(239, 68, 68, 0.2); color: #fca5a5; }
</style>
@endpush

@section('content')
<div class="container py-5 manager-page">
    <!-- Welcome Header -->
    <div class="welcome-card animate__animated animate__fadeIn">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="time-badge mb-3">
                    <i class="far fa-clock"></i>
                    <span id="pakt-time">{{ now()->timezone('Asia/Karachi')->format('h:i A') }}</span>
                    <span class="text-muted small ms-2">Pakistan Time</span>
                </div>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <h1 class="display-4 fw-900 mb-0">Hello, Manager!</h1>
                </div>
                <p class="lead opacity-75 mt-3 mb-0">Track today's service health, sales flow, and menu readiness from one place.</p>
                <div class="mt-4">
                    <a href="{{ route('orders.create') }}" class="btn btn-dark rounded-pill px-4 py-2 me-2 mb-2 mb-lg-0 fw-bold border-2">
                        <i class="fas fa-cash-register me-1" style="color:#ffc107;"></i> New Order
                    </a>
                    <a href="{{ route('orders.history') }}" class="btn btn-outline-dark rounded-pill px-4 py-2 mb-2 mb-lg-0 fw-bold border-2">
                        View Timeline
                    </a>
                </div>
            </div>
            <div class="col-md-4 text-end d-none d-md-block">
                <img src="{{ asset('images/empty-states/dashboard-icon.png') }}" class="dash-img" style="width: 150px; opacity: 0.9;">
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-4 mb-5 reveal-group">
        <div class="col-md-3 reveal">
            <div class="stat-card">
                <div>
                    <div class="stat-icon" style="background: rgba(255, 193, 7, 0.1); color: #e5ac00;">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="stat-label">Today's Revenue</div>
                    <div class="stat-value" style="font-size: 2rem;">PKR <span data-counter="{{ $todayRevenue ?? 0 }}">{{ $todayRevenue ?? 0 }}</span></div>
                    <div class="text-muted small mt-2">Avg order: PKR {{ number_format($averageOrderValue ?? 0, 0) }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-3 reveal">
            <div class="stat-card">
                <div>
                    <div class="stat-icon" style="background: rgba(255, 193, 7, 0.1); color: #e5ac00;">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div class="stat-label">Orders Today</div>
                    <div class="stat-value" data-counter="{{ $todayOrders ?? 0 }}">{{ $todayOrders ?? 0 }}</div>
                    <div class="text-muted small mt-2">Active: {{ $activeOrders ?? 0 }} | Completed: {{ $completedOrders ?? 0 }}</div>
                </div>
                <a href="{{ route('orders.history') }}" class="quick-link">
                    View Orders <i class="fas fa-arrow-right small"></i>
                </a>
            </div>
        </div>

        <div class="col-md-3 reveal">
            <div class="stat-card">
                <div>
                    <div class="stat-icon" style="background: rgba(255, 193, 7, 0.1); color: #e5ac00;">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div class="stat-label">Menu Coverage</div>
                    <div class="stat-value">{{ $availableMenuItems ?? 0 }}/{{ $totalMenuItems ?? 0 }}</div>
                    <div class="text-muted small mt-2">Unavailable items: {{ $unavailableMenuItems ?? 0 }}</div>
                </div>
                <a href="{{ route('menu.index') }}" class="quick-link">
                    Manage Menu <i class="fas fa-arrow-right small"></i>
                </a>
            </div>
        </div>

        <div class="col-md-3 reveal">
            <div class="stat-card">
                <div>
                    <div class="stat-icon" style="background: rgba(255, 193, 7, 0.1); color: #e5ac00;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-label">Operational Staff</div>
                    <div class="stat-value" data-counter="{{ $staffCount ?? 0 }}">{{ $staffCount ?? 0 }}</div>
                    <div class="text-muted small mt-2">Managers + cashiers</div>
                </div>
                <a href="{{ route('staff.index') }}" class="quick-link">
                    Manage Staff <i class="fas fa-arrow-right small"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Orders -->
        <div class="col-lg-8 reveal">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 info-card" style="background: var(--card-bg); border: 1px solid var(--card-border);">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h5 class="fw-900 mb-0"><i class="fas fa-clock me-2" style="color:#e5ac00;"></i>Recent Orders</h5>
                    <a href="{{ route('orders.history') }}" class="btn btn-sm btn-outline-dark rounded-pill border-2 fw-bold">All Orders</a>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0 border-0">
                        <thead>
                            <tr>
                                <th class="text-muted text-uppercase" style="font-size:0.75rem">Invoice</th>
                                <th class="text-muted text-uppercase" style="font-size:0.75rem">Cashier</th>
                                <th class="text-muted text-uppercase" style="font-size:0.75rem">Total</th>
                                <th class="text-muted text-uppercase" style="font-size:0.75rem">Status</th>
                                <th class="text-muted text-uppercase" style="font-size:0.75rem">Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                @php
                                    $statusClass = match($order->status) {
                                        'pending' => 'status-pending',
                                        'preparing' => 'status-preparing',
                                        'ready' => 'status-ready',
                                        'completed' => 'status-completed',
                                        'cancelled' => 'status-cancelled',
                                        default => 'status-pending',
                                    };
                                @endphp
                                <tr class="staff-row border-bottom">
                                    <td class="fw-bold text-dark">{{ $order->invoice_number }}</td>
                                    <td>{{ $order->user->name ?? 'N/A' }}</td>
                                    <td class="fw-bold" style="color:#e5ac00;">PKR {{ number_format($order->total, 0) }}</td>
                                    <td><span class="status-chip {{ $statusClass }}">{{ $order->status }}</span></td>
                                    <td>{{ $order->created_at->format('h:i A') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No orders have been recorded today.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top Items Today -->
        <div class="col-lg-4 reveal">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 info-card" style="background: var(--card-bg); border: 1px solid var(--card-border);">
                <h5 class="fw-900 mb-4"><i class="fas fa-fire-alt me-2" style="color:#e5ac00;"></i>Top Selling Items Today</h5>
                <div class="list-group list-group-flush">
                    @forelse($topItemsToday as $item)
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent border-bottom mb-1 seller-item">
                            <div>
                                <span class="fw-bold text-dark">{{ $item->menuItem->name ?? 'Deleted Item' }}</span>
                            </div>
                            <span class="badge rounded-pill px-3 py-2" style="background:#e5ac00; color:#0f172a; font-weight:700;">{{ $item->sold_count }} sold</span>
                        </div>
                    @empty
                        <div class="text-center text-muted py-3">No top-selling data available for today.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateTime() {
        const now = new Date();
        const options = { 
            timeZone: 'Asia/Karachi', 
            hour: '2-digit', 
            minute: '2-digit',
            second: '2-digit',
            hour12: true 
        };
        const paktTime = new Intl.DateTimeFormat('en-US', options).format(now);
        document.getElementById('pakt-time').innerText = paktTime;
    }
    setInterval(updateTime, 1000);
</script>
@endsection

@push('scripts')
<script>
(function() {
    // Generic reveal
    const revealObs = new IntersectionObserver(function(entries) {
        entries.forEach(function(e) {
            if (e.isIntersecting) {
                e.target.classList.add('visible');
                revealObs.unobserve(e.target);
            }
        });
    }, { threshold: 0.12 });

    document.querySelectorAll('.reveal').forEach(function(el) { revealObs.observe(el); });

    /* ── Counter roll-up for numeric stat values ── */
    var counterObs = new IntersectionObserver(function(entries) {
        entries.forEach(function(e) {
            if (!e.isIntersecting) return;
            var el  = e.target;
            var end = parseInt(el.getAttribute('data-counter'), 10);
            if (isNaN(end) || end === 0) return;
            var duration = 900, start = 0, step = Math.ceil(end / 40);
            var timer = setInterval(function() {
                start = Math.min(start + step, end);
                el.textContent = start;
                if (start >= end) clearInterval(timer);
            }, duration / 40);
            counterObs.unobserve(el);
        });
    }, { threshold: 0.5 });
    document.querySelectorAll('[data-counter]').forEach(function(el) { counterObs.observe(el); });
})();
</script>
@endpush