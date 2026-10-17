<!DOCTYPE html>
<html lang="en" id="htmlRoot">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Workspace') · {{ config('product.name') }}</title>
    <link rel="icon" href="{{ asset('images/counterflow.svg') }}" type="image/svg+xml">
    <script>try { if (localStorage.getItem('theme') === 'dark') document.documentElement.classList.add('dark'); } catch (_) {}</script>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/all.min.css') }}" rel="stylesheet">
    @stack('styles')
    <link href="{{ asset('assets/css/counterflow.css') }}?v={{ filemtime(public_path('assets/css/counterflow.css')) }}" rel="stylesheet">
</head>
<body class="workspace {{ request()->routeIs('orders.create') ? 'workspace-pos' : '' }}">
    <a class="skip-link" href="#main-content">Skip to content</a>
    @php
        $role = auth()->user()?->role;
        $dashboard = match($role) { 'owner', 'guest' => 'owner.dashboard', 'manager' => 'manager.dashboard', default => 'cashier.dashboard' };
        $navigation = [
            [$dashboard, 'fa-border-all', 'Overview', '*dashboard'],
            ['orders.create', 'fa-cash-register', 'Point of sale', 'orders.create'],
            ['orders.history', 'fa-receipt', 'Order history', 'orders.history'],
            ['menu.index', 'fa-utensils', 'Menu catalog', 'menu.*'],
        ];
        if (in_array($role, ['owner', 'guest'])) $navigation[] = ['owner.reports', 'fa-chart-pie', 'Reports', 'owner.reports'];
        if (in_array($role, ['owner', 'manager'])) $navigation[] = ['staff.index', 'fa-users', 'Team', 'staff.*'];
    @endphp
    <aside class="app-sidebar" id="appSidebar" aria-label="Main navigation">
        <a href="{{ route($dashboard) }}" class="product-brand"><img src="{{ asset('images/counterflow.svg') }}" alt="" width="36" height="36"><span>CounterFlow<small>FAST FOOD POS</small></span></a>
        <div class="workspace-label">THE COUNTER</div>
        <nav class="sidebar-nav">
            @foreach($navigation as [$route, $icon, $label, $pattern])
                <a class="sidebar-link {{ request()->routeIs($pattern) ? 'active' : '' }}" href="{{ route($route) }}" @if(request()->routeIs($pattern)) aria-current="page" @endif><i class="fas {{ $icon }}" aria-hidden="true"></i><span>{{ $label }}</span>@if($route === 'orders.create')<kbd>F2</kbd>@endif</a>
            @endforeach
        </nav>
        <div class="sidebar-bottom">
            @auth
                <div class="sidebar-user"><span class="user-avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</span><span class="user-details">{{ auth()->user()->name }}<small>{{ ucfirst($role) }} workspace</small></span></div>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="signout-btn" type="submit"><i class="fas fa-arrow-right-from-bracket" aria-hidden="true"></i> Sign out</button></form>
            @endauth
        </div>
    </aside>
    <button class="sidebar-backdrop" id="sidebarBackdrop" aria-label="Close navigation" hidden></button>
    <div class="app-workspace">
        <header class="workspace-header">
            <div class="d-flex align-items-center gap-3"><button class="icon-button mobile-menu-button" id="menuToggle" aria-label="Open navigation" aria-controls="appSidebar" aria-expanded="false"><i class="fas fa-bars"></i></button><div><div class="header-title">@yield('title', 'Overview')</div></div></div>
            <div class="header-actions"><span class="workspace-date">{{ now()->format('D, d M Y') }}</span><button class="icon-button" id="themeToggle" aria-label="Switch to dark theme" title="Change appearance"><i id="themeIcon" class="fas fa-moon"></i></button></div>
        </header>
        <main id="main-content" tabindex="-1">
            @if(session('success') || session('error'))
                <div class="container pt-3"><div class="alert alert-{{ session('error') ? 'danger' : 'success' }} alert-dismissible fade show" role="alert">{{ session('error') ?? session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Dismiss message"></button></div></div>
            @endif
            @yield('content')
        </main>

    </div>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        (() => {
            const root = document.documentElement;
            const themeButton = document.getElementById('themeToggle');
            function syncTheme() {
                const dark = root.classList.contains('dark');
                document.getElementById('themeIcon').className = 'fas ' + (dark ? 'fa-sun' : 'fa-moon');
                themeButton.setAttribute('aria-label', 'Switch to ' + (dark ? 'light' : 'dark') + ' theme');
            }
            themeButton.addEventListener('click', () => { root.classList.toggle('dark'); try { localStorage.setItem('theme', root.classList.contains('dark') ? 'dark' : 'light'); } catch (_) {} syncTheme(); });
            syncTheme();
            const menu = document.getElementById('menuToggle');
            const backdrop = document.getElementById('sidebarBackdrop');
            const sidebar = document.getElementById('appSidebar');
            const mobileNavigation = matchMedia('(max-width:991px)');
            function syncNavigation() { sidebar.inert = mobileNavigation.matches && !document.body.classList.contains('navigation-open'); }
            mobileNavigation.addEventListener('change', () => { if (!mobileNavigation.matches) { document.body.classList.remove('navigation-open'); backdrop.hidden = true; menu.setAttribute('aria-expanded', 'false'); } syncNavigation(); });
            syncNavigation();
            function toggleNavigation(open) { document.body.classList.toggle('navigation-open', open); menu.setAttribute('aria-expanded', String(open)); backdrop.hidden = !open; syncNavigation(); if (!open) menu.focus(); else sidebar.querySelector('a').focus(); }
            menu.addEventListener('click', () => toggleNavigation(!document.body.classList.contains('navigation-open')));
            backdrop.addEventListener('click', () => toggleNavigation(false));
            document.addEventListener('keydown', event => { if (event.key === 'Escape' && document.body.classList.contains('navigation-open')) toggleNavigation(false); if (event.key === 'Tab' && document.body.classList.contains('navigation-open')) { const links = [...sidebar.querySelectorAll('a,button')]; const first = links[0], last = links[links.length - 1]; if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); } else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); } } if (event.key === 'F2') { event.preventDefault(); window.location.href = @json(route('orders.create')); } });
        })();
    </script>
    @stack('vendor-scripts')
    @stack('scripts')
</body>
</html>
