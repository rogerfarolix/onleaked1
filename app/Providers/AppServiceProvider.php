<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // API rate limits
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Scan submission: 5 par heure par IP
        RateLimiter::for('scan-submit', function (Request $request) {
            return Limit::perHour(5)->by($request->ip());
        });

        // Status polling: 60 par minute par IP (pour le JS polling)
        RateLimiter::for('scan-status', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });
    }
}
