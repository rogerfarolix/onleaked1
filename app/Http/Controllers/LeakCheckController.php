<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessTimedOutException;

class LeakCheckController extends Controller
{
    public function check(Request $request)
    {
        // Honeypot: bots fill this field, humans don't
        if ($request->filled('website')) {
            return response()->json(['status' => 'error', 'message' => 'Request blocked.'], 422);
        }

        $request->validate([
            'email' => ['required', 'string', 'email:rfc,dns', 'max:254'],
        ]);

        $email = strtolower(trim($request->input('email')));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid email address.'], 422);
        }

        $key = 'leak-check:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Too many requests. Please wait a moment before trying again.',
            ], 429);
        }
        RateLimiter::hit($key, 60);

        try {
            $breaches   = $this->queryXposedOrNot($email);
            $footprint  = $this->getDigitalFootprint($email);

            return response()->json([
                'status'    => 'success',
                'found'     => count($breaches) > 0 || count($footprint) > 0,
                'count'     => count($breaches),
                'breaches'  => $breaches,
                'footprint' => $footprint,
            ]);
        } catch (\Exception) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Service temporarily unavailable. Please try again later.',
            ], 503);
        }
    }

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
                        $detail = collect($details)->firstWhere('breachID', $breachName);

                        return [
                            'source'          => $breachName,
                            'logo'            => $detail['logo'] ?? null,
                            'description'     => $detail['breachedData'] ?? 'Your email was found in the ' . $breachName . ' data breach.',
                            'date'            => $detail['breachedDate'] ?? null,
                            'password_leaked' => isset($detail['passwordRisk']) && $detail['passwordRisk'] === 'Yes',
                            'domain'          => $detail['domain'] ?? null,
                        ];
                    })->toArray();
                }
            }
        }

        return [];
    }

    private function getAllBreachDetails(): array
    {
        return cache()->remember('xon_breaches', now()->addDay(), function () {
            $response = Http::timeout(10)->get('https://api.xposedornot.com/v1/breaches');
            if ($response->successful()) {
                return $response->json()['exposedBreaches'] ?? [];
            }
            return [];
        });
    }

    private function getDigitalFootprint(string $email): array
    {
        $scriptPath  = base_path('python_service/footprint.py');
        $pythonBin   = base_path('python_service/venv/bin/python');

        if (!file_exists($scriptPath) || !file_exists($pythonBin)) {
            return [];
        }

        // Allow enough time for holehe to scan 120+ sites (up to 60s)
        set_time_limit(90);

        $process = new Process([$pythonBin, $scriptPath, $email]);
        $process->setTimeout(60);

        try {
            $process->run();

            if (!$process->isSuccessful()) {
                // stderr captured for debugging — not exposed to user
                \Illuminate\Support\Facades\Log::debug('footprint.py stderr: ' . $process->getErrorOutput());
                return [];
            }

            $decoded = json_decode($process->getOutput(), true);
            return is_array($decoded) ? $decoded : [];

        } catch (ProcessTimedOutException) {
            return [];
        } catch (\Exception) {
            return [];
        }
    }
}
