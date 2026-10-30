<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
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
        Route::resourceVerbs([
            'create' => 'sortu',
            'edit' => 'editatu',
        ]);

        RateLimiter::for('bookings', fn (Request $request) => Limit::perHour(10)->by($request->ip()));

        // A whole group may register from the school wifi, behind a single IP.
        RateLimiter::for('registrations', fn (Request $request) => Limit::perHour(60)->by($request->ip()));
    }
}
