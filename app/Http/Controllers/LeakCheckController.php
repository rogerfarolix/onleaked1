<?php

namespace App\Http\Controllers;

use App\Jobs\DigitalFootprintJob;
use App\Models\ScanHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LeakCheckController extends Controller
{
    public function check(Request $request)
    {
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
            $breaches = $this->queryXposedOrNot($email);

            $jobId = Str::uuid()->toString();
            Cache::put('footprint:' . $jobId, ['status' => 'pending'], now()->addMinutes(10));
            DigitalFootprintJob::dispatch($email, $jobId);

            $responseData = [
                'status'           => 'success',
                'found'            => count($breaches) > 0,
                'count'            => count($breaches),
                'breaches'         => $breaches,
                'footprint'        => [],
                'footprint_job_id' => $jobId,
            ];

            if (auth()->check()) {
                ScanHistory::create([
                    'user_id'   => auth()->id(),
                    'scan_type' => 'leak_check',
                    'target'    => $email,
                    'results'   => $responseData,
                ]);
            }

            return response()->json($responseData);
        } catch (\Exception) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Service temporarily unavailable. Please try again later.',
            ], 503);
        }
    }

    public function footprintStatus(string $id): \Illuminate\Http\JsonResponse
    {
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $id)) {
            return response()->json(['status' => 'error'], 400);
        }

        $result = Cache::get('footprint:' . $id, ['status' => 'pending']);
        return response()->json($result);
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
}
