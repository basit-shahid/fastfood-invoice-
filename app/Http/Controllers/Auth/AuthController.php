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

        if (Auth::attempt(array_merge($credentials, ['is_active' => true]), $request->remember)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            if ($user->role == 'owner') {
                return redirect()->route('owner.dashboard');
            } elseif ($user->role == 'manager') {
                return redirect()->route('manager.dashboard');
            } else {
                return redirect()->route('cashier.dashboard');
            }
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