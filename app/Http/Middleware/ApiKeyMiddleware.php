<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = null;

        $authHeader = $request->header('Authorization', '');
        if (str_starts_with($authHeader, 'Bearer ')) {
            $token = substr($authHeader, 7);
        }

        if (!$token) {
            $token = $request->query('api_key');
        }

        if (!$token) {
            return response()->json(['error' => 'API key required.'], 401);
        }

        $apiKey = ApiKey::where('key', $token)->with('user')->first();

        if (!$apiKey || !$apiKey->user) {
            return response()->json(['error' => 'Invalid API key.'], 401);
        }

        if ($apiKey->user->isSuspended()) {
            return response()->json(['error' => 'Account suspended.'], 403);
        }

        Auth::login($apiKey->user, false);
        $apiKey->update(['last_used_at' => now()]);

        return $next($request);
    }
}
