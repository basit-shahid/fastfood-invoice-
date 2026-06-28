<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dr. Shawarma POS - Verify OTP</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
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
            overflow: hidden !important;
            background-color: #ffffff;
        }
        
        .split-layout {
            display: flex;
            height: 100vh;
            width: 100%;
        }
        
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
        
        .login-right {
            flex: 1.2;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #ffffff;
            padding: 10px;
        }
        
        .login-card {
            width: 100%;
            max-width: 440px;
            padding: 20px;
            max-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
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
            margin-bottom: 25px;
        }
        
        .otp-input-container {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 20px;
        }
        
        .otp-input {
            width: 50px;
            height: 60px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 900;
            border-radius: 12px;
            border: 2px solid #cbd5e1; /* Darker border */
            background: #ffffff; /* White background */
            transition: all 0.3s;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05); /* Subtle shadow */
        }
        
        .otp-input:focus {
            border-color: var(--primary-yellow);
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(255,193,7,0.25); /* More prominent focus ring */
            outline: none;
            transform: translateY(-2px); /* Slight lift on focus */
        }
        
        .btn-verify {
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
        
        .btn-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 18px rgba(255,193,7,0.3);
        }

        .btn-resend {
            background: none;
            border: none;
            color: #64748b;
            font-weight: 700;
            font-size: 0.9rem;
            text-decoration: underline;
            padding: 0;
            margin-top: 15px;
            cursor: pointer;
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
        <div class="login-left">
            <img src="{{ asset('images/logo.png') }}" class="branding-logo" alt="FastFood Logo" 
                 onerror="this.onerror=null; this.outerHTML='<i class=\'fas fa-hamburger branding-icon\'></i>';">
            <h1 class="fw-bolder display-4 mb-2" style="letter-spacing: -2px;">Verify Identity</h1>
            <p class="fs-6 fw-bold m-0" style="opacity: 0.85;">Please enter the 6-digit code <br> sent to your secondary email.</p>
        </div>
        
        <div class="login-right">
            <div class="login-card fade-in-up">
                
                <div class="login-header-mobile">
                    <img src="{{ asset('images/logo.png') }}" class="branding-logo-mobile" alt="FastFood Logo" 
                         onerror="this.onerror=null; this.outerHTML='<i class=\'fas fa-hamburger\' style=\'font-size: 3.5rem; color: var(--primary-yellow); margin-bottom: 10px;\'></i>';">
                    <h3 class="fw-bolder m-0" style="letter-spacing: -1px;">Security Verification</h3>
                    <p class="text-muted fw-bold m-0 mt-1">Enter the 6-digit OTP code.</p>
                </div>
                
                <h3 class="fw-bolder mb-3 d-none d-lg-block" style="letter-spacing: -1px; font-size: 2rem; text-align: center;">One-Time Password</h3>
                
                @if($errors->any())
                    <div class="alert alert-danger" style="border-radius: 10px; border: none; background: #fff5f5; color: #e53e3e; font-weight: 600; padding: 10px;">
                        <i class="fas fa-exclamation-circle me-1"></i> {{ $errors->first() }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success" style="border-radius: 10px; border: none; background: #f0fdf4; color: #15803d; font-weight: 600; padding: 10px;">
                        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                    </div>
                @endif
                
                <form method="POST" action="{{ url('/otp-verify') }}">
                    @csrf
                    <div class="form-group text-center">
                        <label class="d-block mb-3 fw-bold text-muted">ENTER 6-DIGIT CODE</label>
                        <div class="otp-input-container">
                            <input type="text" maxlength="1" class="otp-input" required>
                            <input type="text" maxlength="1" class="otp-input" required>
                            <input type="text" maxlength="1" class="otp-input" required>
                            <input type="text" maxlength="1" class="otp-input" required>
                            <input type="text" maxlength="1" class="otp-input" required>
                            <input type="text" maxlength="1" class="otp-input" required>
                        </div>
                        <input type="hidden" name="otp" id="otp_full">
                    </div>
                    
                    <button type="submit" class="btn btn-verify">
                        Verify & Login <i class="fas fa-check-circle ms-2"></i>
                    </button>
                </form>

                <div class="text-center">
                    <form action="{{ route('otp.resend') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-resend">Didn't receive code? Resend OTP</button>
                    </form>
                    <a href="{{ route('login') }}" class="d-block mt-3 text-muted text-decoration-none small fw-bold">Back to Login</a>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        const inputs = document.querySelectorAll('.otp-input');
        const fullInput = document.getElementById('otp_full');

        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                if (e.target.value.length > 1) {
                    e.target.value = e.target.value.slice(0, 1);
                }
                
                if (e.target.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
                
                updateFullValue();
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });

        function updateFullValue() {
            let value = '';
            inputs.forEach(input => value += input.value);
            fullInput.value = value;
        }
    </script>
</body>
</html>
