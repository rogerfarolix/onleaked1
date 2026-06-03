<?php

namespace App\Http\Controllers;

use App\Services\PwnedPasswordsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class PasswordCheckController extends Controller
{
    public function show()
    {
        return \Inertia\Inertia::render('PasswordCheck');
    }

    public function check(Request $request, PwnedPasswordsService $pwned)
    {
        if ($request->filled('website')) {
            return response()->json(['status' => 'error', 'message' => 'Request blocked.'], 422);
        }

        $request->validate([
            'password' => ['required', 'string', 'max:1000'],
        ]);

        $key = 'password-check:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json(['status' => 'error', 'message' => 'Too many requests. Please wait before trying again.'], 429);
        }
        RateLimiter::hit($key, 60);

        try {
            $count = $pwned->countBreaches($request->input('password'));

            if ($count === 0) {
                $risk = 'safe';
                $riskLabel = 'Not found in any known breach';
                $riskColor = 'emerald';
            } elseif ($count <= 10) {
                $risk = 'low';
                $riskLabel = 'Found in a few breaches';
                $riskColor = 'amber';
            } elseif ($count <= 100) {
                $risk = 'medium';
                $riskLabel = 'Found in many breaches';
                $riskColor = 'orange';
            } else {
                $risk = 'high';
                $riskLabel = 'Extremely common change immediately';
                $riskColor = 'red';
            }

            return response()->json([
                'status'     => 'success',
                'count'      => $count,
                'risk'       => $risk,
                'risk_label' => $riskLabel,
                'risk_color' => $riskColor,
            ]);
        } catch (\Exception) {
            return response()->json(['status' => 'error', 'message' => 'Service temporarily unavailable.'], 503);
        }
    }
}
