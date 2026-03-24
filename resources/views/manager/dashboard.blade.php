@extends('layouts.app')

@section('title', 'Manager Dashboard')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Manager Dashboard</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="card bg-primary text-white h-100 shadow-sm border-0">
                                <div class="card-body">
                                    <h6 class="text-uppercase fw-bold text-white-50">Total Menu Items</h6>
                                    <h2 class="mb-0">{{ $totalMenuItems ?? 0 }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card bg-success text-white h-100 shadow-sm border-0">
                                <div class="card-body">
                                    <h6 class="text-uppercase fw-bold text-white-50">Today's Orders</h6>
                                    <h2 class="mb-0">{{ $todayOrders ?? 0 }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card bg-info text-white h-100 shadow-sm border-0">
                                <div class="card-body">
                                    <h6 class="text-uppercase fw-bold text-white-50">Total Staff</h6>
                                    <h2 class="mb-0">{{ $staffCount ?? 0 }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-utensils fa-3x text-warning"></i>
                                    <h5 class="mt-3">Menu Management</h5>
                                    <a href="{{ route('menu.index') }}" class="btn btn-primary mt-2">
                                        Manage Menu
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-history fa-3x text-warning"></i>
                                    <h5 class="mt-3">Order History</h5>
                                    <a href="{{ route('orders.history') }}" class="btn btn-primary mt-2">
                                        View Orders
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-users fa-3x text-warning"></i>
                                    <h5 class="mt-3">Staff Management</h5>
                                    <a href="{{ route('staff.index') }}" class="btn btn-primary mt-2">
                                        Manage Staff
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection