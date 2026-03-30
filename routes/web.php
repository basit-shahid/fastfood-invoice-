<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OwnerController;

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Common routes
    Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
    Route::get('/orders/history', [OrderController::class, 'history'])->name('orders.history');
    Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    
    // Cashier routes (and above)
    Route::prefix('cashier')->middleware([\App\Http\Middleware\RoleMiddleware::class.':cashier,manager,owner'])->group(function () {
        Route::get('/dashboard', function () {
            return view('cashier.dashboard');
        })->name('cashier.dashboard');
        
        Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    });
    
    // Manager & Owner routes
    Route::middleware([\App\Http\Middleware\RoleMiddleware::class.':manager,owner'])->group(function () {
        Route::prefix('manager')->group(function () {
            Route::get('/dashboard', function () {
                $totalMenuItems = \App\Models\MenuItem::count();
                $todayOrders = \App\Models\Order::whereDate('created_at', today())->count();
                $staffCount = \App\Models\User::whereIn('role', ['manager', 'cashier'])->count();
                return view('manager.dashboard', compact('totalMenuItems', 'todayOrders', 'staffCount'));
            })->name('manager.dashboard');
        });

        // Menu Management
        Route::resource('menu', MenuController::class)->parameters(['menu' => 'menuItem'])->except(['index', 'show']);
        Route::post('/menu/{menuItem}/toggle-availability', [MenuController::class, 'toggleAvailability'])->name('menu.toggle-availability');
    });
    
    // Staff Management (Owner & Manager)
    Route::middleware([\App\Http\Middleware\RoleMiddleware::class.':owner,manager'])->group(function () {
        Route::get('/staff', [OwnerController::class, 'staff'])->name('staff.index');
        Route::get('/staff/create', [OwnerController::class, 'createStaff'])->name('staff.create');
        Route::post('/staff', [OwnerController::class, 'storeStaff'])->name('staff.store');
        Route::get('/staff/{user}/edit', [OwnerController::class, 'editStaff'])->name('staff.edit');
        Route::put('/staff/{user}', [OwnerController::class, 'updateStaff'])->name('staff.update');
        Route::delete('/staff/{user}', [OwnerController::class, 'destroyStaff'])->name('staff.destroy');
    });

    // Owner specific routes
    Route::prefix('owner')->middleware([\App\Http\Middleware\RoleMiddleware::class.':owner'])->group(function () {
        Route::get('/dashboard', [OwnerController::class, 'dashboard'])->name('owner.dashboard');
        Route::get('/reports', [OwnerController::class, 'reports'])->name('owner.reports');
        Route::get('/export-report', [OwnerController::class, 'exportPdf'])->name('owner.export.pdf');
    });
});