<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dr. Shawarma POS')</title>
    <!-- Custom Favicon -->
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    
    <!-- Bootstrap 5 CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/css/all.min.css') }}">
    
    <style>
        :root {
            --primary-yellow: #ffc107;
            --dark-yellow: #ffb300;
            --light-yellow: #fff3cd;
            --black: #000000;
            --dark-gray: #333333;
            --light-gray: #f8f9fa;
        }
        
        body {
            background-color: var(--light-gray);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--primary-yellow) 0%, var(--dark-yellow) 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: bold;
            color: var(--black) !important;
        }
        
        .nav-link {
            color: var(--black) !important;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .nav-link:hover {
            transform: translateY(-2px);
            color: #fff !important;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-yellow) 0%, var(--dark-yellow) 100%);
            border: none;
            color: var(--black);
            font-weight: 600;
            padding: 10px 25px;
            border-radius: 25px;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255,193,7,0.3);
        }
        
        .footer {
            background-color: var(--black);
            color: white;
            padding: 20px 0;
            margin-top: auto;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        main {
            flex: 1;
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" height="40" class="me-2">
                Dr. Shawarma
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        @if(auth()->user()->role == 'owner')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('owner.dashboard') }}">
                                    <i class="fas fa-chart-line"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('orders.create') }}">
                                    <i class="fas fa-cash-register"></i> POS
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('menu.index') }}">
                                    <i class="fas fa-utensils"></i> Menu
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('owner.reports') }}">
                                    <i class="fas fa-chart-bar"></i> Reports
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('staff.index') }}">
                                    <i class="fas fa-users"></i> Staff
                                </a>
                            </li>
                        @elseif(auth()->user()->role == 'manager')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('manager.dashboard') }}">
                                    <i class="fas fa-chart-line"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('orders.create') }}">
                                    <i class="fas fa-cash-register"></i> POS
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('menu.index') }}">
                                    <i class="fas fa-utensils"></i> Menu
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('orders.history') }}">
                                    <i class="fas fa-history"></i> Orders
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('staff.index') }}">
                                    <i class="fas fa-users"></i> Staff
                                </a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('cashier.dashboard') }}">
                                    <i class="fas fa-home"></i> Home
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('orders.create') }}">
                                    <i class="fas fa-cash-register"></i> POS
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('orders.history') }}">
                                    <i class="fas fa-history"></i> History
                                </a>
                            </li>
                        @endif
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i> {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close"    <title>@yield('title', 'Dr. Shawarma POS')</title>
                </div>
            @endif
        </div>
        
        @yield('content')
    </main>

    <footer class="footer">
        <div class="container text-center">
            <p class="mb-0">&copy; {{ date('Y') }} Dr. Shawarma. All rights reserved.</p>
        </div>
    </footer>

    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    @stack('scripts')
</body>
</html>