<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dr. Shawarma POS</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <!-- Bootstrap 5 CSS (local) -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome (local) -->
    <link rel="stylesheet" href="{{ asset('assets/css/all.min.css') }}">
    
    <style>
        body {
            background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .splash-card {
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }
        .logo-icon {
            font-size: 4rem;
            color: #ffc107;
            margin-bottom: 1.5rem;
        }
        .btn-start {
            background: #000;
            color: #fff;
            border: none;
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            margin-top: 1.5rem;
        }
        .btn-start:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="splash-card shadow-lg">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="mb-3" style="max-height: 100px;">
        <h1 class="fw-bold mb-2">Dr. Shawarma</h1>
        <p class="text-muted">Enterprise POS Solution</p>
        
        @auth
            <a href="{{ url('/dashboard') }}" class="btn-start">Go to Dashboard</a>
        @else
            <a href="{{ route('login') }}" class="btn-start">Launch POS</a>
        @endauth
        
        <p class="mt-4 small text-muted">&copy; {{ date('Y') }} Dr. Shawarma</p>
    </div>
</body>
</html>
