@extends('layouts.app')

@section('title', 'Reports')

@push('styles')
<style>
    .report-card {
        background: white;
        border-radius: 20px;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        margin-bottom: 30px;
    }

    .table thead th {
        background-color: #f8fafc;
        border-bottom: 2px solid #edf2f7;
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.05em;
        padding: 15px 20px;
    }

    .table tbody td {
        padding: 15px 20px;
        vertical-align: middle;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
    }

    .order-id {
        color: #0f172a;
        font-weight: 700;
    }

    .amount-text {
        font-weight: 800;
        color: #0f172a;
    }

    .pagination {
        margin-top: 20px;
        justify-content: center;
    }

    .page-link {
        border-radius: 10px !important;
        margin: 0 3px;
        border: none;
        color: #64748b;
        font-weight: 600;
    }

    .page-item.active .page-link {
        background-color: var(--primary-yellow);
        color: var(--black);
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-900 mb-1">Sales Reports</h2>
            <p class="text-muted mb-0">Overview of recent transactions and sales activity</p>
        </div>
        <div class="dropdown">
            <button class="btn btn-white border-0 shadow-sm rounded-pill px-4 fw-bold" type="button">
                <i class="far fa-calendar-alt me-2"></i> This Month
            </button>
        </div>
    </div>

    <div class="modern-card animate__animated animate__fadeIn">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Cashier</th>
                        <th>Date & Time</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                    <tr>
                        <td>
                            <span class="order-id">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td>
                            <div class="fw-700">{{ $order->user->name ?? 'System' }}</div>
                        </td>
                        <td>
                            <div class="fw-500 text-color-inherit">{{ $order->created_at->format('M d, Y') }}</div>
                            <div class="small text-muted">{{ $order->created_at->format('H:i A') }}</div>
                        </td>
                        <td>
                            <span class="amount-text">₱{{ number_format($order->total, 2) }}</span>
                        </td>
                        <td>
                            <span class="badge-modern bg-success text-white text-uppercase">
                                {{ $order->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <img src="https://cdn-icons-png.flaticon.com/512/11329/11329061.png" style="width: 80px; opacity: 0.2;" class="mb-3">
                            <p class="text-muted">No recent orders found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($recentOrders->hasPages())
            <div class="px-4 py-3 border-top bg-light">
                {{ $recentOrders->links() }}
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    .pagination {
        margin-bottom: 0;
        gap: 5px;
    }
    .page-item .page-link {
        border-radius: 10px !important;
        border: 1px solid var(--border-color);
        padding: 10px 18px;
        font-weight: 700;
        color: var(--text-color);
        background: var(--card-bg);
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .page-item.active .page-link {
        background-color: var(--primary-yellow);
        border-color: var(--primary-yellow);
        color: var(--black);
    }
    .page-link:hover {
        background-color: var(--bg-color);
        color: var(--primary-yellow);
    }
</style>
@endpush
@endsection