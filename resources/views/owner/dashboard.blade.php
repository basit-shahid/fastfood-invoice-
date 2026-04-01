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

    /* left-edge accent bar slides in */
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

    /* icon */
    .stat-icon {
        width: 60px; height: 60px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 20px;
    }

    .icon-menu { background: rgba(255, 193, 7, 0.1); color: #e5ac00; }
    .icon-orders { background: rgba(255, 193, 7, 0.1); color: #e5ac00; }
    .icon-staff { background: rgba(255, 193, 7, 0.1); color: #e5ac00; }

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

    /* Chart Card Animations */
    @keyframes slideUpFade {
        from { opacity: 0; transform: translateY(30px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .chart-card {
        animation: slideUpFade 0.6s cubic-bezier(0.22, 1, 0.36, 1) both;
        transition: box-shadow 0.3s ease, transform 0.3s ease;
    }
    .chart-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 24px 48px rgba(0,0,0,0.1) !important;
    }
    .chart-card:nth-child(1) { animation-delay: 0.1s; }
    .chart-card:nth-child(2) { animation-delay: 0.25s; }

    .chart-card .card-title {
        position: relative;
        display: inline-block;
    }
    .chart-card .card-title::after {
        content: '';
        position: absolute;
        bottom: -6px;
        left: 0;
        width: 40px;
        height: 3px;
        border-radius: 2px;
        background: linear-gradient(90deg, #ffc107, #ff9f43);
    }

    /* Dark mode dashboard overrides */
    html.dark .time-badge   { background: rgba(255,255,255,0.08); color: var(--body-color); }
    html.dark .stat-card    { box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
    html.dark .chart-card .card { background-color: var(--card-bg); }
    html.dark h4, html.dark h5  { color: var(--body-color); }
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
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <h1 class="display-4 fw-900 mb-0 text-align-center">Hello, Owner! </h1>
                    </div>
                <p class="lead opacity-75 mt-3 mb-0">Manage your business operations and check real-time statistics.</p>
            </div>
            <div class="col-md-4 text-end d-none d-md-block">
                <img src="{{ asset('images/empty-states/dashboard-icon.png') }}" class="dash-img" style="width: 150px; opacity: 0.9;">
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5 reveal-group">
        <!-- Menu Items Stat -->
        <div class="col-md-4 reveal">
            <div class="stat-card">
                <div>
                    <div class="stat-icon icon-menu">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div class="stat-label">Total Menu Items</div>
                    <div class="stat-value" data-counter="{{ \App\Models\MenuItem::count() }}">{{ \App\Models\MenuItem::count() }}</div>
                </div>
                <a href="{{ route('menu.index') }}" class="quick-link">
                    Manage Menu <i class="fas fa-arrow-right small"></i>
                </a>
            </div>
        </div>

        <!-- Orders Stat -->
        <div class="col-md-4 reveal">
            <div class="stat-card">
                <div>
                    <div class="stat-icon icon-orders">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="stat-label">Today's Orders</div>
                    <div class="stat-value" data-counter="{{ \App\Models\Order::whereDate('created_at', today())->count() }}">{{ \App\Models\Order::whereDate('created_at', today())->count() }}</div>
                </div>
                <a href="{{ route('owner.reports') }}" class="quick-link">
                    View Reports <i class="fas fa-arrow-right small"></i>
                </a>
            </div>
        </div>

        <!-- Staff Stat -->
        <div class="col-md-4 reveal">
            <div class="stat-card">
                <div>
                    <div class="stat-icon icon-staff">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-label">System Staff</div>
                    <div class="stat-value" data-counter="{{ \App\Models\User::whereIn('role', ['manager', 'cashier'])->count() }}">{{ \App\Models\User::whereIn('role', ['manager', 'cashier'])->count() }}</div>
                </div>
                <a href="{{ route('staff.index') }}" class="quick-link">
                    Manage Staff <i class="fas fa-arrow-right small"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Revenue Metrics -->
    <div class="row g-4 mb-4 reveal-group">
        <!-- Daily Revenue -->
        <div class="col-md-4 reveal">
            <div class="stat-card">
                <div>
                    <div class="stat-icon" style="background: rgba(255, 193, 7, 0.1); color: #e5ac00;">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stat-label">Daily Revenue</div>
                    <div class="stat-value">Rs. {{ number_format($dailyRevenue, 2) }}</div>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue -->
        <div class="col-md-4 reveal">
            <div class="stat-card">
                <div>
                    <div class="stat-icon" style="background: rgba(255, 193, 7, 0.1); color: #e5ac00;">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-label">Monthly Revenue</div>
                    <div class="stat-value">Rs. {{ number_format($monthlyRevenue, 2) }}</div>
                </div>
            </div>
        </div>

        <!-- All Time Revenue -->
        <div class="col-md-4 reveal">
            <div class="stat-card">
                <div>
                    <div class="stat-icon" style="background: rgba(255, 193, 7, 0.1); color: #e5ac00;">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="stat-label">All-Time Revenue</div>
                    <div class="stat-value">Rs. {{ number_format($allTimeRevenue, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-5 g-4">
        <!-- Sales Graph -->
        <div class="col-md-6 chart-card">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h4 class="fw-900 mb-4 card-title">Monthly Sales ({{ now()->year }})</h4>
                <div style="height: 300px; width: 100%;">
                    <canvas id="monthlySalesChart"></canvas>
                </div>
                <a href="{{ route('owner.export.pdf') }}" class="btn btn-outline-dark rounded-pill px-4 fw-bold mt-3 border-2 shadow-sm btn-export">
                        <i class="fas fa-file-pdf me-2" style="color:#e5ac00;"></i> Export Report
                    </a>
            </div>
        </div>
        <!-- Peak Hours Graph -->
        <div class="col-md-6 chart-card">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h4 class="fw-900 mb-4 card-title">Peak Hours (Today)</h4>
                <div style="height: 300px; width: 100%;">
                    <canvas id="peakHoursChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaderboards & Best Sellers -->
    <div class="row g-4 mb-5 reveal-group">
        <!-- Staff Performance -->
        <div class="col-lg-4 reveal">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 info-card">
                <h5 class="fw-900 mb-4"><i class="fas fa-medal me-2" style="color:#e5ac00;"></i>Top Staff</h5>
                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0">
                        <tbody>
                            @foreach($staffPerformance as $staff)
                            <tr class="border-bottom staff-row">
                                <td class="px-0">
                                    <div class="fw-bold text-dark">{{ $staff->name }}</div>
                                    <small class="text-muted text-capitalize">{{ $staff->role }}</small>
                                </td>
                                <td class="text-end px-0">
                                    <div class="fw-bold" style="color:#e5ac00;">Rs. {{ number_format($staff->total_sales, 0) }}</div>
                                    <small class="text-muted">{{ $staff->orders_count }} orders</small>
                                </td>
                            </tr>
                            @endforeach
                            @if($staffPerformance->isEmpty())
                            <tr><td colspan="2" class="text-center text-muted px-0">No data available</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Best Sellers -->
        <div class="col-lg-4 reveal">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 info-card">
                <h5 class="fw-900 mb-4"><i class="fas fa-arrow-up me-2" style="color:#e5ac00;"></i>Best Sellers</h5>
                <div class="list-group list-group-flush">
                    @forelse($bestSellers as $item)
                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center border-bottom mb-1 seller-item">
                        <div>
                            <span class="fw-bold text-dark">{{ $item->menuItem->name ?? 'Deleted Item' }}</span>
                        </div>
                        <span class="badge rounded-pill px-3 badge-pop" style="background:#e5ac00; color:#0f172a;">{{ $item->total_quantity }} sold</span>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3">No data available</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Worst Sellers -->
        <div class="col-lg-4 reveal">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 info-card">
                <h5 class="fw-900 mb-4"><i class="fas fa-arrow-down me-2" style="color:#e5ac00;"></i>Needs Improv.</h5>
                <div class="list-group list-group-flush">
                    @forelse($worstSellers as $item)
                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center border-bottom mb-1 seller-item">
                        <div>
                            <span class="fw-bold text-dark">{{ $item->menuItem->name ?? 'Deleted Item' }}</span>
                        </div>
                        <span class="badge rounded-pill px-3 badge-pop" style="background:#0f172a; color:#ffc107;">{{ $item->total_quantity }} sold</span>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3">No data available</div>
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
<script src="{{ asset('assets/js/chart.umd.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        /* ── Monthly Sales Bar Chart ── */
        const ctx = document.getElementById('monthlySalesChart').getContext('2d');
        const monthlySalesData = @json($monthlySalesData);
        const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

        // Gradient fill for bars
        const barGradient = ctx.createLinearGradient(0, 0, 0, 300);
        barGradient.addColorStop(0,   'rgba(255, 193,  7, 0.95)');
        barGradient.addColorStop(1,   'rgba(255, 159,  67, 0.6)');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'Revenue (Rs.)',
                    data: monthlySalesData,
                    backgroundColor: barGradient,
                    borderColor: 'rgba(255, 179, 0, 1)',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    hoverBackgroundColor: 'rgba(255, 179, 0, 1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 900,
                    easing: 'easeOutQuart',
                    delay: (ctx) => ctx.dataIndex * 60
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (v) => 'Rs. ' + v.toLocaleString(),
                            font: { family: 'Segoe UI', size: 11 }
                        },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Segoe UI', size: 11 } }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { size: 13, family: 'Segoe UI', weight: '600' },
                        bodyFont:  { size: 13, family: 'Segoe UI' },
                        padding: 14,
                        cornerRadius: 10,
                        displayColors: false,
                        callbacks: {
                            label: (ctx) => 'Revenue: Rs. ' + ctx.parsed.y.toLocaleString()
                        }
                    }
                }
            }
        });

        /* ── Peak Hours Line Chart ── */
        const peakCtx = document.getElementById('peakHoursChart').getContext('2d');
        const peakHoursData = @json($peakHoursData);
        const hoursLabels = ['12a','1a','2a','3a','4a','5a','6a','7a','8a','9a','10a','11a',
                             '12p','1p','2p','3p','4p','5p','6p','7p','8p','9p','10p','11p'];

        // Gradient fill under line
        const lineGradient = peakCtx.createLinearGradient(0, 0, 0, 280);
        lineGradient.addColorStop(0,   'rgba(255, 193,  7, 0.95)');
        lineGradient.addColorStop(1,   'rgba(255, 159,  67, 0.6)');

        new Chart(peakCtx, {
            type: 'line',
            data: {
                labels: hoursLabels,
                datasets: [{
                    label: 'Orders',
                    data: peakHoursData,
                    backgroundColor: lineGradient,
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 2.5,
                    tension: 0.45,
                    fill: true,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: 'rgba(0, 0, 0, 1)',
                    pointBorderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: 'rgba(13, 202, 240, 1)',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1000,
                    easing: 'easeInOutCubic'
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: { family: 'Segoe UI', size: 11 }
                        },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Segoe UI', size: 10 } }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { size: 13, family: 'Segoe UI', weight: '600' },
                        bodyFont:  { size: 13, family: 'Segoe UI' },
                        padding: 14,
                        cornerRadius: 10,
                        displayColors: false,
                        callbacks: {
                            label: (ctx) => 'Orders: ' + ctx.parsed.y
                        }
                    }
                }
            }
        });
    });
</script>

<script>
/* ═══════════════════════════════════════════
   SCROLL REVEAL  (IntersectionObserver)
═══════════════════════════════════════════ */
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

    // chart-card title underline trigger
    const chartObs = new IntersectionObserver(function(entries) {
        entries.forEach(function(e) {
            if (e.isIntersecting) {
                e.target.classList.add('visible');
                chartObs.unobserve(e.target);
            }
        });
    }, { threshold: 0.2 });
    document.querySelectorAll('.chart-card').forEach(function(el) { chartObs.observe(el); });

    // Staff rows – stagger left slide-in
    const rowObs = new IntersectionObserver(function(entries) {
        entries.forEach(function(e) {
            if (e.isIntersecting) {
                var rows = e.target.querySelectorAll('.staff-row');
                rows.forEach(function(row, i) {
                    setTimeout(function() { row.classList.add('visible'); }, i * 90);
                });
                rowObs.unobserve(e.target);
            }
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.table-responsive').forEach(function(el) { rowObs.observe(el); });

    // Seller / worst items – stagger right slide-in
    var sellerGroups = document.querySelectorAll('.list-group');
    sellerGroups.forEach(function(group) {
        var itemObs = new IntersectionObserver(function(entries) {
            entries.forEach(function(e) {
                if (e.isIntersecting) {
                    var items = e.target.querySelectorAll('.seller-item');
                    items.forEach(function(item, i) {
                        setTimeout(function() { item.classList.add('visible'); }, i * 80);
                    });
                    itemObs.unobserve(e.target);
                }
            });
        }, { threshold: 0.1 });
        itemObs.observe(group);
    });

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
