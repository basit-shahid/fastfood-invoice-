<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OtpController extends Controller
{
    public function showOtpForm(Request $request)
    {
        return redirect()->route('login');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string',
        ]);

        if (!$request->session()->has('auth.temp_user_id')) {
            return redirect()->route('login');
        }

        $userId = $request->session()->get('auth.temp_user_id');
        $user = User::find($userId);

        if (!$user || $user->otp !== $request->otp || Carbon::now()->gt($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }

        // Clear OTP field
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        // Login user
        $remember = $request->session()->get('auth.remember', false);
        Auth::login($user, $remember);
        $request->session()->forget(['auth.temp_user_id', 'auth.remember']);
        $request->session()->regenerate();

        // Redirect based on role
        if ($user->role == 'owner') {
            return redirect()->route('owner.dashboard');
        } elseif ($user->role == 'manager') {
            return redirect()->route('manager.dashboard');
        } else {
            return redirect()->route('cashier.dashboard');
        }
    }
}
