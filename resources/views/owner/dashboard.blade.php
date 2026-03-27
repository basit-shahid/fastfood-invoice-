@extends('layouts.app')

@section('title', 'Owner Dashboard')

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
        border: none;
        background: white;
        box-shadow: 0 10px 30px rgba(0,0,0,0.04);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        border: 1px solid rgba(0,0,0,0.02);
    }

    .stat-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.08);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 20px;
    }

    .icon-menu { background: rgba(13, 110, 253, 0.1); color: #0d6efd; }
    .icon-orders { background: rgba(25, 135, 84, 0.1); color: #198754; }
    .icon-staff { background: rgba(255, 193, 7, 0.1); color: #e5ac00; }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 5px;
    }

    .stat-label {
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.8rem;
    }

    .quick-link {
        margin-top: 20px;
        text-decoration: none;
        color: #0f172a;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: gap 0.3s;
    }

    .quick-link:hover {
        gap: 12px;
        color: var(--accent-dark);
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
</style>
@endpush

@section('content')
<div class="container py-5">
    <!-- Welcome Header -->
    <div class="welcome-card animate__animated animate__fadeIn">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="time-badge mb-3">
                    <i class="far fa-clock"></i>
                    <span id="pakt-time">{{ now()->timezone('Asia/Karachi')->format('h:i A') }}</span>
                    <span class="text-muted small ms-2">Pakistan Time</span>
                </div>
                <h1 class="display-4 fw-900 mb-2">Hello, Owner! 👋</h1>
                <p class="lead opacity-75 mb-0">Manage your business operations and check real-time statistics.</p>
            </div>
            <div class="col-md-4 text-end d-none d-md-block">
                <img src="{{ asset('images/empty-states/dashboard-icon.png') }}" style="width: 150px; opacity: 0.9;">
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <!-- Menu Items Stat -->
        <div class="col-md-4">
            <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                <div>
                    <div class="stat-icon icon-menu">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div class="stat-label">Total Menu Items</div>
                    <div class="stat-value">{{ \App\Models\MenuItem::count() }}</div>
                </div>
                <a href="{{ route('menu.index') }}" class="quick-link">
                    Manage Menu <i class="fas fa-arrow-right small"></i>
                </a>
            </div>
        </div>

        <!-- Orders Stat -->
        <div class="col-md-4">
            <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                <div>
                    <div class="stat-icon icon-orders">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="stat-label">Today's Orders</div>
                    <div class="stat-value">{{ \App\Models\Order::whereDate('created_at', today())->count() }}</div>
                </div>
                <a href="{{ route('owner.reports') }}" class="quick-link text-success">
                    View Reports <i class="fas fa-arrow-right small"></i>
                </a>
            </div>
        </div>

        <!-- Staff Stat -->
        <div class="col-md-4">
            <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
                <div>
                    <div class="stat-icon icon-staff">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-label">System Staff</div>
                    <div class="stat-value">{{ \App\Models\User::whereIn('role', ['manager', 'cashier'])->count() }}</div>
                </div>
                <a href="{{ route('staff.index') }}" class="quick-link text-warning">
                    Manage Staff <i class="fas fa-arrow-right small"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Additional Quick Actions -->
    <div class="row">
        <div class="col-md-12">
            <h4 class="fw-900 mb-4">Quick Insights</h4>
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center">
                <p class="text-muted mb-0">Detailed analytics and sales charts will appear here soon.</p>
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
            hour12: true 
        };
        const paktTime = new Intl.DateTimeFormat('en-US', options).format(now);
        document.getElementById('pakt-time').innerText = paktTime;
    }
    setInterval(updateTime, 1000);
</script>
@endsection
