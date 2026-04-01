<!DOCTYPE html>
<html lang="en" id="htmlRoot">
<script>
    // Apply dark mode BEFORE render to prevent flash
    if (localStorage.getItem('theme') === 'dark') {
        document.documentElement.classList.add('dark');
    }
</script>
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
            /* Light mode surface tokens */
            --body-bg: #f8f9fa;
            --body-color: #0f172a;
            --card-bg: #ffffff;
            --card-border: rgba(0,0,0,0.06);
            --muted-color: #64748b;
            --dropdown-bg: #ffffff;
            --dropdown-color: #0f172a;
            --input-bg: #ffffff;
        }

        /* ── Dark mode tokens ── */
        html.dark {
            --body-bg: #0f1117;
            --body-color: #e2e8f0;
            --card-bg: #1a1d27;
            --card-border: rgba(255,255,255,0.06);
            --muted-color: #94a3b8;
            --dropdown-bg: #1e2130;
            --dropdown-color: #e2e8f0;
            --input-bg: #1e2130;
        }

        body {
            background-color: var(--body-bg);
            color: var(--body-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: background-color 0.35s ease, color 0.35s ease;
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

        /* Dark mode card */
        .card {
            border: none;
            border-radius: 15px;
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s, background-color 0.35s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        }

        html.dark .card {
            box-shadow: 0 5px 20px rgba(0,0,0,0.4);
        }

        /* Dark mode text helpers */
        html.dark .text-dark   { color: #e2e8f0 !important; }
        html.dark .text-muted  { color: var(--muted-color) !important; }
        html.dark .stat-value  { color: #f1f5f9; }

        /* Dark mode table/list */
        html.dark .table       { color: var(--body-color); }
        html.dark .border-bottom { border-color: rgba(255,255,255,0.08) !important; }
        html.dark .list-group-item { background: transparent; color: var(--body-color); }

        /* Dark mode alert */
        html.dark .alert-success { background: #14532d; color: #bbf7d0; border-color: #166534; }
        html.dark .alert-danger  { background: #450a0a; color: #fecaca; border-color: #7f1d1d; }

        /* Dark mode dropdown */
        html.dark .dropdown-menu {
            background-color: var(--dropdown-bg);
            border: 1px solid rgba(255,255,255,0.1);
        }
        html.dark .dropdown-item {
            color: var(--dropdown-color);
        }
        html.dark .dropdown-item:hover {
            background: rgba(255,193,7,0.15);
            color: #ffc107;
        }

        /* Dark mode welcome card override */
        html.dark .welcome-card {
            background: linear-gradient(135deg, #b8860b 0%, #a0720a 100%) !important;
        }

        /* Theme toggle button */
        #themeToggle {
            background: rgba(0,0,0,0.12);
            border: none;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1rem;
            color: #0f172a;
            transition: background 0.25s ease, transform 0.25s ease;
            flex-shrink: 0;
        }
        #themeToggle:hover {
            background: rgba(0,0,0,0.22);
            transform: rotate(20deg) scale(1.1);
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
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .fade-in { animation: fadeIn 0.5s ease-out; }

        main { flex: 1; }
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
                        <!-- Dark / Light Mode Toggle -->
                        <li class="nav-item d-flex align-items-center me-2">
                            <button id="themeToggle" title="Toggle dark/light mode" aria-label="Toggle dark/light mode">
                                <i id="themeIcon" class="fas fa-moon"></i>
                            </button>
                        </li>
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

    <script>
        (function() {
            const html = document.documentElement;
            const btn  = document.getElementById('themeToggle');
            const icon = document.getElementById('themeIcon');

            function applyTheme(dark) {
                if (dark) {
                    html.classList.add('dark');
                    icon.classList.replace('fa-moon', 'fa-sun');
                } else {
                    html.classList.remove('dark');
                    icon.classList.replace('fa-sun', 'fa-moon');
                }
            }

            // Sync icon to stored preference on load
            applyTheme(localStorage.getItem('theme') === 'dark');

            btn.addEventListener('click', function() {
                const isDark = html.classList.toggle('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');

                // Animate icon swap
                icon.style.transform = 'scale(0) rotate(180deg)';
                setTimeout(function() {
                    isDark
                        ? icon.classList.replace('fa-moon', 'fa-sun')
                        : icon.classList.replace('fa-sun',  'fa-moon');
                    icon.style.transition = 'transform 0.25s ease';
                    icon.style.transform  = 'scale(1) rotate(0deg)';
                }, 150);
            });
        })();
    </script>
</body>
</html>