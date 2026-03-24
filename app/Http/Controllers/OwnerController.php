<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Hash;

class OwnerController extends Controller
{
    public function dashboard()
    {
        $totalMenuItems = MenuItem::count();
        $todayOrders = Order::whereDate('created_at', today())->count();
        $staffCount = User::whereIn('role', ['manager', 'cashier'])->count();
        
        return view('owner.dashboard', compact('totalMenuItems', 'todayOrders', 'staffCount'));
    }
    
    public function reports()
    {
        $recentOrders = Order::with('user')->orderBy('created_at', 'desc')->paginate(10);
        return view('owner.reports', compact('recentOrders'));
    }
    
    public function staff()
    {
        $users = User::all();
        return view('owner.staff', compact('users'));
    }
    
    public function createStaff()
    {
        return view('owner.staff_form');
    }
    
    public function storeStaff(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,owner,manager,cashier',
            'is_active' => 'nullable|boolean'
        ]);
        
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => $request->has('is_active'),
        ]);
        
        return redirect()->route('staff.index')->with('success', 'Staff member created successfully.');
    }
    
    public function editStaff(User $user)
    {
        return view('owner.staff_form', compact('user'));
    }
    
    public function updateStaff(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,owner,manager,cashier',
            'is_active' => 'nullable|boolean'
        ]);
        
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'is_active' => $request->has('is_active'),
        ];
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        
        $user->update($data);
        
        return redirect()->route('staff.index')->with('success', 'Staff member updated successfully.');
    }
    
    public function destroyStaff(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('staff.index')->with('error', 'You cannot delete yourself.');
        }
        
        $user->delete();
        return redirect()->route('staff.index')->with('success', 'Staff member deleted successfully.');
    }
}