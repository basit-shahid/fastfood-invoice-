<?php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Paginator::useBootstrapFive();
        Blade::if('owner', function () {
            return auth()->check() && auth()->user()->isOwner();
        });
        
        Blade::if('manager', function () {
            return auth()->check() && auth()->user()->isManager();
        });
        
        Blade::if('cashier', function () {
            return auth()->check() && auth()->user()->isCashier();
        });
        
        Blade::if('canManageMenu', function () {
            return auth()->check() && (auth()->user()->isOwner() || auth()->user()->isManager());
        });
    }
}