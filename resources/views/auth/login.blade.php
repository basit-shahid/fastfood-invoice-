<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign in · {{ config('product.name') }}</title>
    <link rel="icon" href="{{ asset('images/counterflow.svg') }}" type="image/svg+xml">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/counterflow.css') }}?v={{ filemtime(public_path('assets/css/counterflow.css')) }}" rel="stylesheet">
</head>
<body class="login-page">
    <section class="login-story" aria-label="About CounterFlow">
        <a class="product-brand" href="{{ route('login') }}"><img src="{{ asset('images/counterflow.svg') }}" alt="" width="40" height="40"><span>CounterFlow<small>FAST FOOD POS</small></span></a>
        <div class="login-story-content"><span class="story-label"><span class="status-dot"></span> BUILT FOR THE PACE OF SERVICE</span><h1>Great service.<br>Starts at<br>the counter<span>.</span></h1><p>From the first order to the last receipt, bring your team, menu, and sales into one clear workspace.</p>
            <div class="story-order" aria-hidden="true"><div class="story-order-heading"><span><i class="fas fa-receipt"></i> A smoother service</span><span class="story-pill">All in one place</span></div><div class="story-feature"><span class="story-feature-icon"><i class="fas fa-cash-register"></i></span><span>Take orders with confidence<small>Clear menus. Simple checkout.</small></span><i class="fas fa-check"></i></div><div class="story-feature"><span class="story-feature-icon"><i class="fas fa-chart-line"></i></span><span>Know how your day is going<small>Sales, reports, and team activity.</small></span><i class="fas fa-check"></i></div></div>
        </div>
        <div class="login-story-footer">A focused workspace for fast food teams.</div>
    </section>
    <main class="login-form-panel">
        <div class="login-form-wrap">
            <a class="product-brand mobile-login-brand" href="{{ route('login') }}"><img src="{{ asset('images/counterflow.svg') }}" width="36" height="36" alt=""><span>CounterFlow<small>FAST FOOD POS</small></span></a>
            <span class="eyebrow">YOUR WORKSPACE AWAITS</span><h2>Welcome back</h2><p class="login-intro">Sign in to keep service moving.</p>
            @if($errors->any())<div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>@endif
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-4"><label for="email" class="form-label">Email address</label><input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="you@yourbusiness.com">@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label for="password" class="form-label">Password</label><div class="password-field"><input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="current-password" placeholder="Enter your password"><button type="button" id="passwordToggle" class="password-toggle" aria-label="Show password" aria-pressed="false"><i class="far fa-eye" aria-hidden="true"></i></button></div>@error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror</div>
                <div class="form-check mb-4"><input type="checkbox" class="form-check-input" id="remember" name="remember" @checked(old('remember'))><label class="form-check-label" for="remember">Keep me signed in on this device</label></div>
                <button type="submit" class="btn btn-primary login-submit">Sign in to workspace <i class="fas fa-arrow-right" aria-hidden="true"></i></button>
            </form>
            <div class="login-divider"><span>Explore the workspace</span></div>
            <form method="POST" action="{{ route('login.guest') }}">@csrf<button type="submit" class="btn btn-outline-secondary w-100"><i class="far fa-compass me-2" aria-hidden="true"></i> Continue as guest</button></form>
            <p class="guest-note">Guest access lets you explore dashboards and place orders. Team administration is restricted.</p>
            <div class="login-help"><i class="fas fa-shield-alt" aria-hidden="true"></i> Use your assigned account for daily operations.</div>
        </div>
        <footer>CounterFlow POS <span>·</span> {{ date('Y') }}</footer>
    </main>
    <script>document.getElementById('passwordToggle').addEventListener('click', function () { const input = document.getElementById('password'); const show = input.type === 'password'; input.type = show ? 'text' : 'password'; this.setAttribute('aria-label', show ? 'Hide password' : 'Show password'); this.setAttribute('aria-pressed', String(show)); this.querySelector('i').className = show ? 'far fa-eye-slash' : 'far fa-eye'; });</script>
</body>
</html>
