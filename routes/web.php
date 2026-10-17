<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MenuImageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OwnerController;
use App\Http\Middleware\RoleMiddleware;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// Menu photos are public, just like the legacy /storage images. Content hashes
// version the URL; the upload endpoints remain role-protected.
Route::get('/menu-images/{menuItem}/{checksum}', [MenuImageController::class, 'show'])
    ->where('checksum', '[a-f0-9]{64}')->name('menu.image');

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/login/guest', [AuthController::class, 'loginGuest'])->name('login.guest');
// Route::get('/otp-verify', [AuthController::class, 'showOtpForm'])->name('otp.verify');
// Route::post('/otp-verify', [AuthController::class, 'verifyOtp']);
// Route::post('/otp-resend', [AuthController::class, 'resendOtp'])->name('otp.resend');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Common routes
    Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
    Route::get('/orders/history', [OrderController::class, 'history'])->name('orders.history');
    Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');

    // Cashier routes (and above)
    Route::prefix('cashier')->middleware([RoleMiddleware::class.':cashier,manager,owner,guest'])->group(function () {
        Route::get('/dashboard', function () {
            return view('cashier.dashboard');
        })->name('cashier.dashboard');

        Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    });

    // Manager & Owner routes
    Route::middleware([RoleMiddleware::class.':manager,owner,guest'])->group(function () {
        Route::prefix('manager')->group(function () {
            Route::get('/dashboard', function () {
                $totalMenuItems = MenuItem::count();
                $todayOrders = Order::whereDate('created_at', today())->count();
                $staffCount = User::whereIn('role', ['manager', 'cashier'])->count();

                $availableMenuItems = MenuItem::where('is_available', true)->count();
                $unavailableMenuItems = $totalMenuItems - $availableMenuItems;

                $todayRevenue = Order::whereDate('created_at', today())->sum('total');
                $averageOrderValue = $todayOrders > 0 ? ($todayRevenue / $todayOrders) : 0;

                $activeOrders = Order::whereDate('created_at', today())
                    ->whereIn('status', ['pending', 'preparing', 'ready'])
                    ->count();

                $completedOrders = Order::whereDate('created_at', today())
                    ->where('status', 'completed')
                    ->count();

                $recentOrders = Order::with('user')
                    ->whereDate('created_at', today())
                    ->latest()
                    ->take(6)
                    ->get();

                $topItemsToday = OrderItem::with('menuItem')
                    ->whereHas('order', function ($query) {
                        $query->whereDate('created_at', today());
                    })
                    ->select('menu_item_id', DB::raw('SUM(quantity) as sold_count'))
                    ->groupBy('menu_item_id')
                    ->orderByDesc('sold_count')
                    ->take(5)
                    ->get();

                return view('manager.dashboard', compact(
                    'totalMenuItems',
                    'todayOrders',
                    'staffCount',
                    'availableMenuItems',
                    'unavailableMenuItems',
                    'todayRevenue',
                    'averageOrderValue',
                    'activeOrders',
                    'completedOrders',
                    'recentOrders',
                    'topItemsToday'
                ));
            })->name('manager.dashboard');
        });

        // Menu Management
        Route::resource('menu', MenuController::class)->parameters(['menu' => 'menuItem'])->except(['index', 'show']);
        Route::post('/menu/{menuItem}/toggle-availability', [MenuController::class, 'toggleAvailability'])->name('menu.toggle-availability');

        // Stock Management
        // Route::resource('stock', \App\Http\Controllers\StockController::class);
    });

    // Staff Management (Owner & Manager)
    Route::middleware([RoleMiddleware::class.':owner,manager'])->group(function () {
        Route::get('/staff', [OwnerController::class, 'staff'])->name('staff.index');
        Route::get('/staff/create', [OwnerController::class, 'createStaff'])->name('staff.create');
        Route::post('/staff', [OwnerController::class, 'storeStaff'])->name('staff.store');
        Route::get('/staff/{user}/edit', [OwnerController::class, 'editStaff'])->name('staff.edit');
        Route::put('/staff/{user}', [OwnerController::class, 'updateStaff'])->name('staff.update');
        Route::delete('/staff/{user}', [OwnerController::class, 'destroyStaff'])->name('staff.destroy');
    });

    // Owner specific routes
    Route::prefix('owner')->middleware([RoleMiddleware::class.':owner,guest'])->group(function () {
        Route::get('/dashboard', [OwnerController::class, 'dashboard'])->name('owner.dashboard');
        Route::get('/reports', [OwnerController::class, 'reports'])->name('owner.reports');
        Route::get('/export-report', [OwnerController::class, 'exportPdf'])->name('owner.export.pdf');
    });
});
