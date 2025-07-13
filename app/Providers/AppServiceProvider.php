<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\ShoppingCart;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share cart count with all views
        View::composer('*', function ($view) {
            $cartCount = 0;
            
            if (Auth::check()) {
                $cartCount = ShoppingCart::where('user_id', Auth::id())->sum('quantity');
            }
            
            $view->with('cartCount', $cartCount);
        });
    }
}