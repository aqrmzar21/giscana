<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
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
        // Memberikan hak akses penuh (Super Admin) untuk role 'admin'
        Gate::before(function ($user, $ability) {
            if ($user->role === 'admin' || $user->hasRole('admin')) {
                return true; // Bypass semua pengecekan izin
            }
            return null;
        });
    }
}
