<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Cart;

class CartServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load cart from session or database for authenticated users
        $this->initializeCart();
    }

    private function initializeCart()
    {
        // If user is authenticated, load their cart from session/database
        if (auth()->check()) {
            $userId = auth()->id();
            
            // You can add logic here to load cart from database if needed
            // For now, the package handles session-based persistence
        }
    }
}
