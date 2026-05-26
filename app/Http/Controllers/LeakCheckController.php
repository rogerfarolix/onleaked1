<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;

class LeakCheckController extends Controller
{
    /**
     * Check if an email has been compromised in known data breaches.
     * 
     * PRIVACY: The email is NEVER stored, logged, or cached.
     * It exists only in memory during the request lifecycle.
     */
    public function check(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->input('email');

        // Rate limiting: max 5 checks per minute per IP
        $key = 'leak-check:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Too many requests. Please wait a moment before trying again.',
            ], 429);
        }
        RateLimiter::hit($key, 60);

        try {
            $breaches = $this->queryXposedOrNot($email);
            $footprint = $this->getDigitalFootprint($email);

            return response()->json([
                'status' => 'success',
                'found' => count($breaches) > 0 || count($footprint) > 0,
                'count' => count($breaches),
                'breaches' => $breaches,
                'footprint' => $footprint,
            ]);
        } catch (\Exception $e) {
            // Fallback: return a safe error without exposing internals
            return response()->json([
                'status' => 'error',
                'message' => 'Service temporarily unavailable. Please try again later.',
            ], 503);
        }
    }

    /**
     * Query the XposedOrNot API for email breaches.
     * Maps the breach names to full details (logo, description).
     */
    private function queryXposedOrNot(string $email): array
    {
        $response = Http::timeout(10)->get('https://api.xposedornot.com/v1/check-email/' . urlencode($email));

        if ($response->successful()) {
            $data = $response->json();
            
            if (isset($data['breaches']) && is_array($data['breaches']) && count($data['breaches']) > 0) {
                $breachList = $data['breaches'][0];
                
                if (is_array($breachList)) {
                    $details = $this->getAllBreachDetails();
                    
                    return collect($breachList)->map(function ($breachName) use ($details) {
                        // Find matching detail or fallback
                        $detail = collect($details)->firstWhere('breachID', $breachName);
                        
                        return [
                            'source' => $breachName,
                            'logo' => $detail['logo'] ?? null,
                            'description' => $detail['breachedData'] ?? 'Your email was found in the ' . $breachName . ' data breach.',
                            'date' => $detail['breachedDate'] ?? null,
                            'password_leaked' => isset($detail['passwordRisk']) && $detail['passwordRisk'] === 'Yes',
                            'domain' => $detail['domain'] ?? null,
                        ];
                    })->toArray();
                }
            }
        }

        return [];
    }

    /**
     * Get details of all breaches from XposedOrNot, cached for 24 hours.
     */
    private function getAllBreachDetails(): array
    {
        return cache()->remember('xon_breaches', now()->addDay(), function () {
            $response = Http::timeout(10)->get('https://api.xposedornot.com/v1/breaches');
            if ($response->successful()) {
                $data = $response->json();
                return $data['exposedBreaches'] ?? [];
            }
            return [];
        });
    }

    /**
     * Execute the Python Holehe script to footprint the email across 120+ sites.
     */
    private function getDigitalFootprint(string $email): array
    {
        $scriptPath = base_path('python_service/footprint.py');
        if (!file_exists($scriptPath)) {
            return []; // Script not created yet
        }

        // Run python script and capture JSON output
        $cmd = escapeshellcmd(base_path('python_service/venv/bin/python')) . ' ' . escapeshellarg($scriptPath) . ' ' . escapeshellarg($email);
        $output = shell_exec($cmd);
        
        if ($output) {
            $decoded = json_decode($output, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }
}
