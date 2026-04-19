<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\SmsService;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLoginForm(Request $request)
    {
        if ($request->session()->has('auth.temp_user_id')) {
            return view('auth.login', ['showOtp' => true]);
        }
        return view('auth.login');
    }

    public function login(Request $request, SmsService $smsService)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::validate(array_merge($credentials, ['is_active' => true]))) {
            $user = User::where('email', $request->email)->first();

            // Generate OTP
            $otp = rand(100000, 999999);
            $user->otp = $otp;
            $user->otp_expires_at = Carbon::now()->addMinutes(10);
            $user->save();

            // Send OTP
            if ($user->phone) {
                $smsService->sendOtp($user->phone, $otp);
            }

            // Store user ID and remember status in session
            $request->session()->put('auth.temp_user_id', $user->id);
            $request->session()->put('auth.remember', $request->has('remember'));

            return redirect()->route('login.otp');
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

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}