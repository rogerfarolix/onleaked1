<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ThrottleScanRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'scan:' . ($request->ip());

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            if ($request->expectsJson()) {
                return response()->json([
                    'error'   => 'too_many_requests',
                    'message' => "Limite atteinte. Réessayez dans {$seconds} secondes.",
                    'retry_after' => $seconds,
                ], 429);
            }
            return back()->withErrors([
                'email' => "Limite atteinte. Réessayez dans {$seconds} secondes.",
            ]);
        }

        RateLimiter::hit($key, 3600);
        return $next($request);
    }
}
