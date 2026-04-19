<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class OwnerController extends Controller
{
    public function dashboard()
    {
        $totalMenuItems = MenuItem::count();
        $todayOrders = Order::whereDate('created_at', today())->count();
        $staffCount = User::whereIn('role', ['manager', 'cashier'])->count();
        
        // Revenue Calculations
        $dailyRevenue = Order::whereDate('created_at', today())->sum('total');
        $monthlyRevenue = Order::whereMonth('created_at', now()->month)
                               ->whereYear('created_at', now()->year)
                               ->sum('total');
        $allTimeRevenue = Order::sum('total');

        // Monthly Sales Graph Data (for current year)
        $ordersThisYear = Order::whereYear('created_at', now()->year)
                               ->select('created_at', 'total')
                               ->get();
                               
        $monthlySales = array_fill(1, 12, 0);
        foreach ($ordersThisYear as $order) {
            $month = (int)$order->created_at->format('m');
            $monthlySales[$month] += $order->total;
        }
        
        // Ensure values are numeric
        $monthlySalesData = array_values($monthlySales);

        // Peak Hours Analysis (Current Month)
        $ordersThisMonth = Order::whereMonth('created_at', now()->month)
                                 ->whereYear('created_at', now()->year)
                                 ->select('created_at', 'total')
                                 ->get();
        $peakHoursData = array_fill(0, 24, 0);
        foreach($ordersThisMonth as $order) {
            $hour = (int)$order->created_at->format('G'); // 0-23
            $peakHoursData[$hour]++;
        }

        // Staff Performance Leaderboard
        $staffPerformance = User::whereIn('role', ['manager', 'cashier'])
                                ->withCount(['orders' => function($query) {
                                    $query->whereMonth('created_at', now()->month)
                                          ->whereYear('created_at', now()->year);
                                }])
                                ->withSum(['orders as total_sales' => function($query) {
                                    $query->whereMonth('created_at', now()->month)
                                          ->whereYear('created_at', now()->year);
                                }], 'total')
                                ->orderByDesc('total_sales')
                                ->get();

        // Best vs Worst Sellers (All Time)
        // Fetch all available menu items to properly account for 0-sale items
        $allMenuItems = \App\Models\MenuItem::where('is_available', true)->get();
        $itemSalesPlucked = \App\Models\OrderItem::select('menu_item_id', DB::raw('SUM(quantity) as total_quantity'))
                                                 ->groupBy('menu_item_id')
                                                 ->pluck('total_quantity', 'menu_item_id');

        $menuItemStats = $allMenuItems->map(function($item) use ($itemSalesPlucked) {
            return (object) [
                'menuItem' => $item,
                'total_quantity' => $itemSalesPlucked->get($item->id, 0)
            ];
        });

        // Best Sellers (Top 5 by volume)
        $bestSellers = $menuItemStats->sortByDesc('total_quantity')->values()->take(5);
        
        // Worst Sellers (Bottom 5, strictly excluding the Best Sellers to prevent overlap)
        $bestSellerIds = $bestSellers->pluck('menuItem.id')->toArray();
        $worstSellers = $menuItemStats->reject(function($item) use ($bestSellerIds) {
            return in_array($item->menuItem->id, $bestSellerIds);
        })->sortBy('total_quantity')->values()->take(5);

        return view('owner.dashboard', compact(
            'totalMenuItems', 'todayOrders', 'staffCount',
            'dailyRevenue', 'monthlyRevenue', 'allTimeRevenue', 
            'monthlySalesData', 'peakHoursData', 'staffPerformance', 'bestSellers', 'worstSellers'
        ));
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

    public function exportPdf()
    {
        $month = request('month', now()->month);
        $year = request('year', now()->year);

        $date = \Carbon\Carbon::create($year, $month, 1);
        $monthName = $date->format('F Y');
        
        $totalOrders = Order::whereMonth('created_at', $month)
                            ->whereYear('created_at', $year)
                            ->count();
                            
        $totalRevenue = Order::whereMonth('created_at', $month)
                             ->whereYear('created_at', $year)
                             ->sum('total');

        $topStaff = User::whereIn('role', ['manager', 'cashier'])
                        ->withSum(['orders as total_sales' => function($query) use ($month, $year) {
                            $query->whereMonth('created_at', $month)
                                  ->whereYear('created_at', $year);
                        }], 'total')
                        ->orderByDesc('total_sales')
                        ->first();

        $itemSales = \App\Models\OrderItem::with('menuItem')
                        ->select('menu_item_id', DB::raw('SUM(quantity) as total_quantity'))
                        ->whereHas('order', function($q) use ($month, $year) {
                            $q->whereMonth('created_at', $month)
                              ->whereYear('created_at', $year);
                        })
                        ->groupBy('menu_item_id')
                        ->orderByDesc('total_quantity')
                        ->get();
                        
        $bestSeller = $itemSales->first();

        $pdf = Pdf::loadView('owner.pdf_report', compact(
            'monthName', 'totalOrders', 'totalRevenue', 'topStaff', 'bestSeller'
        ));

        return $pdf->download('Business-Report-' . $date->format('M-Y') . '.pdf');
    }
}