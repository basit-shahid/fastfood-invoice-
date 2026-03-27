<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dr. Shawarma POS - Login</title>
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
            --black: #000000;
        }
        
        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            width: 100vw;
            margin: 0;
            overflow: hidden !important; /* Forces strict removal of all scrollbars */
            background-color: #ffffff;
        }
        
        .split-layout {
            display: flex;
            height: 100vh;
            width: 100%;
        }
        
        /* Left Side */
        .login-left {
            flex: 1;
            background: linear-gradient(135deg, var(--primary-yellow) 0%, var(--dark-yellow) 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: var(--black);
            padding: 20px;
            text-align: center;
        }
        
        @media (max-width: 991.98px) {
            .login-left {
                display: none !important;
            }
        }
        
        /* Right Side */
        .login-right {
            flex: 1.2;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #ffffff;
            padding: 10px;
        }
        
        /* Max constraints for form width */
        .login-card {
            width: 100%;
            max-width: 440px;
            padding: 20px;
            max-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .branding-icon {
            font-size: 5rem;
            margin-bottom: 15px;
            animation: bounceIn 1s cubic-bezier(0.28, 0.84, 0.42, 1);
        }
        
        .branding-logo {
            max-width: 160px;
            max-height: 160px;
            object-fit: contain;
            margin-bottom: 20px;
            animation: bounceIn 1s cubic-bezier(0.28, 0.84, 0.42, 1);
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
        }
        
        .branding-logo-mobile {
            max-width: 100px;
            max-height: 100px;
            object-fit: contain;
            margin-bottom: 12px;
        }
        
        /* Fallback mobile header */
        .login-header-mobile {
            display: none;
            text-align: center;
            margin-bottom: 15px;
        }
        
        @media (max-width: 991.98px) {
            .login-header-mobile {
                display: block;
            }
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            font-weight: 700;
            margin-bottom: 5px;
            color: #2b2b36;
            font-size: 0.9rem;
        }
        
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #eef0f3;
            background: #f8f9fa;
            transition: all 0.3s;
            font-size: 0.95rem;
        }
        
        .form-control:focus {
            border-color: var(--primary-yellow);
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(255,193,7,0.15);
        }
        
        .btn-login {
            background: linear-gradient(135deg, var(--primary-yellow) 0%, var(--dark-yellow) 100%);
            color: var(--black);
            padding: 12px;
            border-radius: 10px;
            font-weight: 900;
            font-size: 1.05rem;
            width: 100%;
            border: none;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 10px;
            box-shadow: 0 6px 12px rgba(255,193,7,0.2);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 18px rgba(255,193,7,0.3);
        }
        
        .demo-credentials {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            border: 2px dashed #eef0f3;
        }
        
        .demo-credentials small {
            display: block;
            margin-bottom: 5px;
            color: #6c757d;
            font-size: 0.85rem;
        }
        
        @keyframes bounceIn {
            0% { transform: scale(0.1); opacity: 0; }
            60% { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(1); }
        }
        
        .fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="split-layout">
        <!-- Left Side: Branding (Hidden on Tablets & Phones) -->
        <div class="login-left">
            <img src="{{ asset('images/logo.png') }}" class="branding-logo" alt="FastFood Logo" 
                 onerror="this.onerror=null; this.outerHTML='<i class=\'fas fa-hamburger branding-icon\'></i>';">
            <h1 class="fw-bolder display-4 mb-2" style="letter-spacing: -2px;">Dr.Shawarma</h1>
            <p class="fs-6 fw-bold m-0" style="opacity: 0.85;">A dose of flavour. <br> Seamless Checkout Execution.</p>
        </div>
        
        <!-- Right Side: Login Form (Stretches to 100% width on Mobile) -->
        <div class="login-right">
            <div class="login-card fade-in-up">
                
                <!-- Visible only on Mobile where Left Side is hidden -->
                <div class="login-header-mobile">
                    <img src="{{ asset('images/logo.png') }}" class="branding-logo-mobile" alt="FastFood Logo" 
                         onerror="this.onerror=null; this.outerHTML='<i class=\'fas fa-hamburger\' style=\'font-size: 3.5rem; color: var(--primary-yellow); margin-bottom: 10px;\'></i>';">
                    <h3 class="fw-bolder m-0" style="letter-spacing: -1px;">Dr. Shawarma POS</h3>
                    <p class="text-muted fw-bold m-0 mt-1">Sign in below to proceed.</p>
                </div>
                
                <!-- Visible only on Desktop -->
                <h3 class="fw-bolder mb-3 d-none d-lg-block" style="letter-spacing: -1px; font-size: 2rem; text-align: center;"
                >Welcome back!</h3>
                
                @if($errors->any())
                    <div class="alert alert-danger" style="border-radius: 10px; border: none; background: #fff5f5; color: #e53e3e; font-weight: 600; padding: 10px;">
                        <i class="fas fa-exclamation-circle me-1"></i> {{ $errors->first() }}
                    </div>
                @endif
                
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope text-muted me-2"></i> Email Address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="name@fastfood.com">
                    </div>
                    
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock text-muted me-2"></i> Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" required placeholder="Enter your password">
                    </div>
                    
                    <div class="form-group form-check d-flex align-items-center mb-3">
                        <input type="checkbox" class="form-check-input me-3" id="remember" name="remember" style="width: 1.2rem; height: 1.2rem; margin-top: 0;">
                        <label class="form-check-label text-muted fw-bold" for="remember" style="margin-bottom: 0; cursor: pointer;">Maintain session</label>
                    </div>
                    
                    <button type="submit" class="btn btn-login">
                        Authenticate <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </form>
                
                <div class="demo-credentials">
                    <h6 class="fw-bold mb-2 d-flex align-items-center"><i class="fas fa-id-badge text-warning me-2 fs-5"></i> Demo Credentials:</h6>
                    <small>👑 <strong>Owner:</strong> owner@drshawarma.com / password</small>
                    <small>📊 <strong>Manager:</strong> manager@drshawarma.com / password</small>
                    <small>🍔 <strong>Cashier:</strong> cashier@drshawarma.com / password</small>
                </div>
            </div>
        </div>
    </div>
    
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
