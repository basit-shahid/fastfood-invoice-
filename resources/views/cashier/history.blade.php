@extends('layouts.app')

@section('title', 'Order History')

@push('styles')
<style>
    .history-card {
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

    .invoice-link {
        color: #0f172a;
        font-weight: 700;
        text-decoration: none;
        transition: color 0.2s;
    }

    .invoice-link:hover {
        color: var(--accent-color);
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .status-completed { background: #dcfce7; color: #15803d; }
    .status-pending { background: #fef9c3; color: #a16207; }

    .date-row {
        background-color: #f1f5f9;
        font-weight: 800;
        color: #475569;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.1em;
    }

    .action-btn {
        width: 35px;
        height: 35px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        border: none;
    }

    .btn-view { background: #f1f5f9; color: #64748b; }
    .btn-view:hover { background: #e2e8f0; color: #0f172a; }

    .btn-print { background: #fef9c3; color: #a16207; }
    .btn-print:hover { background: #fde68a; color: #854d0e; }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-900 mb-1" style="letter-spacing: -1px;">Order History</h2>
            <p class="text-muted mb-0">Track and manage your historical checkout sessions.</p>
        </div>
        <a href="{{ route('orders.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold">
            <i class="fas fa-plus me-2"></i> New Order
        </a>
    </div>

    <div class="card history-card overflow-hidden">
        <div class="card-body p-0">
            @if($orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Timestamp</th>
                                <th>Inventory</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $currentDate = null; @endphp
                            @foreach($orders as $order)
                                @php $orderDate = $order->created_at->format('F d, Y (l)'); @endphp
                                @if($orderDate !== $currentDate)
                                    <tr class="date-row">
                                        <td colspan="6" class="fw-800 py-3 px-4">
                                            <i class="far fa-calendar-alt text-warning me-2"></i> {{ $orderDate }}
                                        </td>
                                    </tr>
                                    @php $currentDate = $orderDate; @endphp
                                @endif
                                
                                <tr>
                                    <td>
                                        <a href="{{ route('orders.invoice', $order) }}" class="invoice-link">
                                            #{{ $order->invoice_number }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="small fw-bold">{{ $order->created_at->format('h:i A') }}</div>
                                        <div class="small text-muted">{{ $order->created_at->format('d M y') }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark rounded-pill px-3 py-2 border">
                                            {{ $order->items->sum('quantity') }} items
                                        </span>
                                    </td>
                                    <td class="fw-900">₱{{ number_format($order->total, 2) }}</td>
                                    <td>
                                        <span class="status-badge {{ $order->status == 'pending' ? 'status-pending' : 'status-completed' }}">
                                            <i class="fas fa-circle fs-xs me-1 small"></i>
                                            {{ ucfirst($order->status == 'pending' ? 'pending' : 'completed') }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('orders.invoice', $order) }}" target="_blank" class="action-icon-btn" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('orders.invoice', $order) }}?download=1" class="action-icon-btn" title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <a href="{{ route('orders.invoice', $order) }}?print=1" target="_blank" class="action-icon-btn" title="Print">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-cart-5521508-4610092.png" alt="Empty" style="max-height: 200px;" class="mb-4 opacity-75">
                    <h4 class="fw-bold">No orders yet</h4>
                    <p class="text-muted">Once you start processing orders, they will appear here.</p>
                </div>
            @endif
        </div>
    </div>
    
    @if($orders->count() > 0)
        <div class="d-flex justify-content-center mt-5">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection