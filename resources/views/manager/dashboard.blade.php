@extends('layouts.app')

@section('title', 'Manager Dashboard')

@push('styles')
<style>
    .manager-hero {
        border-radius: 24px;
        padding: 28px;
        background: linear-gradient(130deg, #fff7df 0%, #ffe7a8 100%);
        border: 1px solid rgba(255, 193, 7, 0.25);
        box-shadow: 0 14px 30px rgba(0, 0, 0, 0.06);
        margin-bottom: 1.5rem;
    }

    .manager-hero h2 {
        font-weight: 800;
        margin-bottom: 0.4rem;
        color: #1f2937;
    }

    .manager-kpi {
        border-radius: 18px;
        border: 1px solid var(--card-border);
        background: var(--card-bg);
        padding: 18px;
        height: 100%;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .manager-kpi .label {
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--muted-color);
        font-weight: 700;
    }

    .manager-kpi .value {
        font-size: 1.9rem;
        line-height: 1.1;
        font-weight: 800;
        color: var(--body-color);
    }

    .manager-kpi .meta {
        font-size: 0.88rem;
        color: var(--muted-color);
    }

    .manager-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 193, 7, 0.16);
        color: #b07900;
    }

    .manager-section {
        border-radius: 20px;
        border: 1px solid var(--card-border);
        background: var(--card-bg);
        padding: 20px;
        height: 100%;
    }

    .manager-section h5 {
        font-weight: 800;
        margin-bottom: 0;
        color: var(--body-color);
    }

    .manager-page {
        padding-bottom: max(2.5rem, env(safe-area-inset-bottom));
    }

    .manager-side-rail {
        position: sticky;
        top: 1rem;
    }

    .manager-quick-actions .btn {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 44px;
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

    html.dark .manager-hero {
        background: linear-gradient(130deg, #3a2a00 0%, #5b4300 100%);
        border-color: rgba(255, 193, 7, 0.25);
    }

    html.dark .manager-hero h2,
    html.dark .manager-hero p,
    html.dark .manager-hero .small {
        color: #f8fafc !important;
    }

    html.dark .status-pending { background: rgba(245, 158, 11, 0.2); color: #fcd34d; }
    html.dark .status-preparing { background: rgba(59, 130, 246, 0.2); color: #93c5fd; }
    html.dark .status-ready { background: rgba(16, 185, 129, 0.2); color: #6ee7b7; }
    html.dark .status-completed { background: rgba(34, 197, 94, 0.2); color: #86efac; }
    html.dark .status-cancelled { background: rgba(239, 68, 68, 0.2); color: #fca5a5; }

    @media (max-width: 768px) {
        .manager-hero {
            padding: 22px;
        }

        .manager-kpi .value {
            font-size: 1.6rem;
        }
    }

    @media (max-width: 991.98px) {
        .manager-page {
            padding-bottom: max(1.75rem, env(safe-area-inset-bottom));
        }

        .manager-side-rail {
            position: static;
        }

        .manager-section {
            margin-bottom: 0.75rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container py-4 manager-page">
    <div class="manager-hero">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h2>Manager Operations Dashboard</h2>
                <p class="mb-1">Track today's service health, sales flow, and menu readiness from one place.</p>
                <span class="small">{{ now()->format('l, d M Y') }}</span>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ route('orders.create') }}" class="btn btn-primary me-2 mb-2 mb-lg-0">
                    <i class="fas fa-cash-register me-1"></i> New Order
                </a>
                <a href="{{ route('orders.history') }}" class="btn btn-outline-dark rounded-pill px-4 mb-2 mb-lg-0">
                    View Timeline
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="manager-kpi">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="label">Today's Revenue</span>
                    <span class="manager-icon"><i class="fas fa-wallet"></i></span>
                </div>
                <div class="value">PKR {{ number_format($todayRevenue ?? 0, 0) }}</div>
                <div class="meta">Avg order: PKR {{ number_format($averageOrderValue ?? 0, 0) }}</div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="manager-kpi">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="label">Orders Today</span>
                    <span class="manager-icon"><i class="fas fa-receipt"></i></span>
                </div>
                <div class="value">{{ $todayOrders ?? 0 }}</div>
                <div class="meta">Completed: {{ $completedOrders ?? 0 }} | Active: {{ $activeOrders ?? 0 }}</div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="manager-kpi">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="label">Menu Coverage</span>
                    <span class="manager-icon"><i class="fas fa-utensils"></i></span>
                </div>
                <div class="value">{{ $availableMenuItems ?? 0 }}/{{ $totalMenuItems ?? 0 }}</div>
                <div class="meta">Unavailable items: {{ $unavailableMenuItems ?? 0 }}</div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="manager-kpi">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="label">Operational Staff</span>
                    <span class="manager-icon"><i class="fas fa-users"></i></span>
                </div>
                <div class="value">{{ $staffCount ?? 0 }}</div>
                <div class="meta">Managers + cashiers assigned</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="manager-section">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5>Recent Orders</h5>
                    <a href="{{ route('orders.history') }}" class="btn btn-sm btn-outline-secondary rounded-pill">All Orders</a>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Cashier</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Time</th>
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
                                <tr>
                                    <td class="fw-semibold">{{ $order->invoice_number }}</td>
                                    <td>{{ $order->user->name ?? 'N/A' }}</td>
                                    <td>PKR {{ number_format($order->total, 0) }}</td>
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

        <div class="col-lg-4">
            <div class="manager-side-rail">
            <div class="manager-section mb-4">
                <h5 class="mb-3">Top Selling Items Today</h5>
                <div class="list-group list-group-flush">
                    @forelse($topItemsToday as $item)
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                            <div>
                                <div class="fw-semibold">{{ $item->menuItem->name ?? 'Deleted Item' }}</div>
                                <small class="text-muted">Menu item</small>
                            </div>
                            <span class="badge rounded-pill" style="background:#ffc107;color:#1f2937;">{{ $item->sold_count }} sold</span>
                        </div>
                    @empty
                        <div class="text-center text-muted py-3">No top-selling data available for today.</div>
                    @endforelse
                </div>
            </div>

            <div class="manager-section manager-quick-actions">
                <h5 class="mb-3">Quick Actions</h5>
                <div class="d-grid gap-2">
                    <a href="{{ route('menu.index') }}" class="btn btn-outline-dark rounded-pill">
                        <i class="fas fa-utensils me-1"></i> Manage Menu
                    </a>
                    <a href="{{ route('staff.index') }}" class="btn btn-outline-dark rounded-pill">
                        <i class="fas fa-user-cog me-1"></i> Manage Staff
                    </a>
                    <a href="{{ route('orders.create') }}" class="btn btn-outline-dark rounded-pill">
                        <i class="fas fa-plus-circle me-1"></i> Start POS Session
                    </a>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
@endsection