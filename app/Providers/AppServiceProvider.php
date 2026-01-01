<?php

namespace App\Providers;

use App\Listeners\ForceRememberMe;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

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
        // Enregistrer les listeners pour forcer le remember me
        Event::listen(Login::class, ForceRememberMe::class);
        Event::listen(Registered::class, ForceRememberMe::class);
    }
}
