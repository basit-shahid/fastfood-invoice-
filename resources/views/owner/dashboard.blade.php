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
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <h1 class="display-4 fw-900 mb-0">Hello, Owner! 👋</h1>
                    <a href="{{ route('owner.export.pdf') }}" class="btn btn-outline-dark rounded-pill px-4 fw-bold mt-2 border-2 shadow-sm">
                        <i class="fas fa-file-pdf text-danger me-2"></i> Export Report
                    </a>
                </div>
                <p class="lead opacity-75 mt-3 mb-0">Manage your business operations and check real-time statistics.</p>
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

    <!-- Revenue Metrics -->
    <div class="row g-4 mb-4">
        <!-- Daily Revenue -->
        <div class="col-md-4">
            <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
                <div>
                    <div class="stat-icon" style="background: rgba(13, 202, 240, 0.1); color: #0dcaf0;">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stat-label">Daily Revenue</div>
                    <div class="stat-value">Rs. {{ number_format($dailyRevenue, 2) }}</div>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue -->
        <div class="col-md-4">
            <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.5s;">
                <div>
                    <div class="stat-icon" style="background: rgba(111, 66, 193, 0.1); color: #6f42c1;">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-label">Monthly Revenue</div>
                    <div class="stat-value">Rs. {{ number_format($monthlyRevenue, 2) }}</div>
                </div>
            </div>
        </div>

        <!-- All Time Revenue -->
        <div class="col-md-4">
            <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.6s;">
                <div>
                    <div class="stat-icon" style="background: rgba(220, 53, 69, 0.1); color: #dc3545;">
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
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h4 class="fw-900 mb-4">Monthly Sales ({{ now()->year }})</h4>
                <div style="height: 300px; width: 100%;">
                    <canvas id="monthlySalesChart"></canvas>
                </div>
            </div>
        </div>
        <!-- Peak Hours Graph -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h4 class="fw-900 mb-4">Peak Hours (Today/Month)</h4>
                <div style="height: 300px; width: 100%;">
                    <canvas id="peakHoursChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaderboards & Best Sellers -->
    <div class="row g-4 mb-5">
        <!-- Staff Performance -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-900 mb-4"><i class="fas fa-medal text-warning me-2"></i>Top Staff</h5>
                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0">
                        <tbody>
                            @foreach($staffPerformance as $staff)
                            <tr class="border-bottom">
                                <td class="px-0">
                                    <div class="fw-bold text-dark">{{ $staff->name }}</div>
                                    <small class="text-muted text-capitalize">{{ $staff->role }}</small>
                                </td>
                                <td class="text-end px-0">
                                    <div class="fw-bold text-success">Rs. {{ number_format($staff->total_sales, 0) }}</div>
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
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-900 mb-4"><i class="fas fa-arrow-up text-success me-2"></i>Best Sellers</h5>
                <div class="list-group list-group-flush">
                    @forelse($bestSellers as $item)
                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center border-bottom mb-1">
                        <div>
                            <span class="fw-bold text-dark">{{ $item->menuItem->name ?? 'Deleted Item' }}</span>
                        </div>
                        <span class="badge bg-success rounded-pill px-3">{{ $item->total_quantity }} sold</span>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3">No data available</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Worst Sellers -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-900 mb-4"><i class="fas fa-arrow-down text-danger me-2"></i>Needs Improv.</h5>
                <div class="list-group list-group-flush">
                    @forelse($worstSellers as $item)
                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center border-bottom mb-1">
                        <div>
                            <span class="fw-bold text-dark">{{ $item->menuItem->name ?? 'Deleted Item' }}</span>
                        </div>
                        <span class="badge bg-danger rounded-pill px-3">{{ $item->total_quantity }} sold</span>
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
            hour12: true 
        };
        const paktTime = new Intl.DateTimeFormat('en-US', options).format(now);
        document.getElementById('pakt-time').innerText = paktTime;
    }
    setInterval(updateTime, 1000);
</script>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('monthlySalesChart').getContext('2d');
        const monthlySalesData = @json($monthlySalesData);
        
        // Define months
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'Revenue (Rs.)',
                    data: monthlySalesData,
                    backgroundColor: 'rgba(255, 193, 7, 0.8)',
                    borderColor: 'rgba(255, 179, 0, 1)',
                    borderWidth: 2,
                    borderRadius: 5,
                    hoverBackgroundColor: 'rgba(255, 179, 0, 1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rs. ' + value;
                            }
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { size: 14, family: 'Segoe UI' },
                        bodyFont: { size: 14, family: 'Segoe UI' },
                        padding: 15,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: Rs. ' + context.parsed.y;
                            }
                        }
                    }
                }
            }
        });

        const peakCtx = document.getElementById('peakHoursChart').getContext('2d');
        const peakHoursData = @json($peakHoursData);
        const hoursLabels = ['12 AM','1 AM','2 AM','3 AM','4 AM','5 AM','6 AM','7 AM','8 AM','9 AM','10 AM','11 AM','12 PM','1 PM','2 PM','3 PM','4 PM','5 PM','6 PM','7 PM','8 PM','9 PM','10 PM','11 PM'];

        new Chart(peakCtx, {
            type: 'line',
            data: {
                labels: hoursLabels,
                datasets: [{
                    label: 'Orders',
                    data: peakHoursData,
                    backgroundColor: 'rgba(13, 202, 240, 0.2)',
                    borderColor: 'rgba(13, 202, 240, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: 'rgba(13, 202, 240, 1)',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    },
                    x: {
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 15,
                        displayColors: false
                    }
                }
            }
        });
    });
</script>
@endpush
