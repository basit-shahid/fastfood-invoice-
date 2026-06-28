<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::validate(array_merge($credentials, ['is_active' => true]))) {
            $user = User::where('email', $request->email)->first();
            
            // Generate OTP
            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $user->update([
                'otp_code' => $otp,
                'otp_expires_at' => now()->addMinutes(10),
            ]);

            // Send OTP
            $targetEmail = $user->secondary_email ?? $user->email;
            \Illuminate\Support\Facades\Mail::to($targetEmail)->send(new \App\Mail\OtpMail($otp));

            // Store user ID in session for verification
            session(['otp_user_id' => $user->id, 'remember' => $request->has('remember')]);

            return redirect()->route('otp.verify');
        }

        // Check if user exists but is inactive
        $user = User::where('email', $request->email)->first();
        if ($user && !$user->is_active) {
            return back()->withErrors([
                'email' => 'Your account is inactive. Please contact the owner.',
            ]);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function showOtpForm()
    {
        if (!session()->has('otp_user_id')) {
            return redirect()->route('login');
        }
        return view('auth.otp_verify');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|string|size:6']);

        $userId = session('otp_user_id');
        $user = User::findOrFail($userId);

        if ($user->otp_code === $request->otp && now()->isBefore($user->otp_expires_at)) {
            // Clear OTP
            $user->update(['otp_code' => null, 'otp_expires_at' => null]);

            // Login
            Auth::login($user, session('remember', false));
            session()->forget(['otp_user_id', 'remember']);

            if ($user->role == 'owner') {
                return redirect()->route('owner.dashboard');
            } elseif ($user->role == 'manager') {
                return redirect()->route('manager.dashboard');
            } else {
                return redirect()->route('cashier.dashboard');
            }
        }

        return back()->withErrors(['otp' => 'The provided OTP is invalid or has expired.']);
    }

    public function resendOtp()
    {
        if (!session()->has('otp_user_id')) {
            return redirect()->route('login');
        }

        $user = User::findOrFail(session('otp_user_id'));
        
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        $targetEmail = $user->secondary_email ?? $user->email;
        \Illuminate\Support\Facades\Mail::to($targetEmail)->send(new \App\Mail\OtpMail($otp));

        return back()->with('success', 'A new OTP has been sent to your email.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}