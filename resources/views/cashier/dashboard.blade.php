@extends('layouts.app')

@section('title', 'Cashier Dashboard')

@push('styles')
<style>
    .welcome-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border: none;
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        overflow: hidden;
        position: relative;
    }
    
    .welcome-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 150px;
        height: 150px;
        background: radial-gradient(circle, rgba(255,193,7,0.1) 0%, transparent 70%);
        border-radius: 50%;
        transform: translate(30%, -30%);
    }

    .stat-card {
        border-radius: 20px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: 1px solid rgba(0,0,0,0.03);
    }
    
    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(255,193,7,0.15);
    }

    .action-btn {
        border-radius: 16px;
        padding: 1.2rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s;
        border: none;
        box-shadow: 0 4px 15px rgba(255,193,7,0.2);
    }

    .action-btn:hover {
        transform: scale(1.02);
        box-shadow: 0 8px 25px rgba(255,193,7,0.3);
    }

    .icon-box {
        width: 80px;
        height: 80px;
        background: var(--light-yellow);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card welcome-card p-5 fade-in">
                <div class="card-body text-center">
                    <div class="icon-box">
                        <i class="fas fa-cash-register fa-2x text-warning"></i>
                    </div>
                    
                    <h1 class="fw-bolder mb-3" style="letter-spacing: -1px; color: #1a1a1a;">
                        Welcome back, <span class="text-warning">{{ auth()->user()->name }}</span>!
                    </h1>
                    
                    <p class="text-muted fs-5 mb-5 px-lg-5">
                        Manage your point of sale session and track historical orders with ease. A dose of flavor, seamlessly executed.
                    </p>
                    
                    <div class="row g-4 mt-2">
                        <div class="col-sm-6">
                            <a href="{{ route('orders.create') }}" class="btn btn-primary btn-lg action-btn w-100 d-flex flex-column align-items-center justify-content-center gap-2">
                                <i class="fas fa-plus-circle fs-3"></i>
                                Start New Order
                            </a>
                        </div>
                        <div class="col-sm-6">
                            <a href="{{ route('orders.history') }}" class="btn btn-dark btn-lg action-btn w-100 d-flex flex-column align-items-center justify-content-center gap-2" style="background: #1a1a1a;">
                                <i class="fas fa-history fs-3"></i>
                                Order History
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Stats/Info Tip -->
            <div class="mt-5 text-center fade-in" style="animation-delay: 0.2s;">
                <p class="text-muted small">
                    <i class="fas fa-lightbulb text-warning me-1"></i>
                    <strong>Tip:</strong> Use the "Maintain session" option on login for faster access.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection