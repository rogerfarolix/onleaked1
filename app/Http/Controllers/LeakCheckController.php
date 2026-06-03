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
            $breaches  = $this->queryXposedOrNot($email);
            $analytics = $this->breachAnalytics($email);

            $jobId = Str::uuid()->toString();
            Cache::put('footprint:' . $jobId, ['status' => 'pending'], now()->addMinutes(10));
            DigitalFootprintJob::dispatch($email, $jobId);

            $responseData = [
                'status'           => 'success',
                'found'            => count($breaches) > 0,
                'count'            => count($breaches),
                'breaches'         => $breaches,
                'summary'          => $this->buildSummary($breaches),
                'risk'             => $analytics['risk'],
                'pastes'           => $analytics['pastes'],
                'footprint'        => null,
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
                        $exposed = $detail['exposedData'] ?? [];
                        $exposed = is_array($exposed) ? array_values($exposed) : [];

                        return [
                            'source'          => $breachName,
                            'logo'            => $detail['logo'] ?? null,
                            'description'     => $detail['exposureDescription']
                                ?? ('Votre e-mail a été trouvé dans la fuite de données ' . $breachName . '.'),
                            'date'            => $detail['breachedDate'] ?? null,
                            'domain'          => $detail['domain'] ?? null,
                            'industry'        => $detail['industry'] ?? null,
                            'records'         => isset($detail['exposedRecords']) ? (int) $detail['exposedRecords'] : null,
                            'exposed_data'    => $exposed,
                            'password_risk'   => $detail['passwordRisk'] ?? 'unknown',
                            'verified'        => (bool) ($detail['verified'] ?? false),
                            'sensitive'       => (bool) ($detail['sensitive'] ?? false),
                            'reference_url'   => $detail['referenceURL'] ?? null,
                            'password_leaked' => in_array('Passwords', $exposed, true),
                        ];
                    })
                    ->sortByDesc('records')
                    ->values()
                    ->toArray();
                }
            }
        }

        return [];
    }

    /**
     * Aggregate stats across all breaches for the summary band.
     */
    private function buildSummary(array $breaches): array
    {
        $b = collect($breaches);

        $dataTypes = $b->flatMap(fn ($x) => $x['exposed_data'] ?? [])
            ->countBy()
            ->sortDesc();

        $dates = $b->pluck('date')->filter()->values();

        return [
            'total_records'    => (int) $b->sum(fn ($x) => (int) ($x['records'] ?? 0)),
            'with_passwords'   => $b->where('password_leaked', true)->count(),
            'data_types'       => $dataTypes->keys()->values()->all(),
            'data_types_count' => $dataTypes->count(),
            'verified'         => $b->where('verified', true)->count(),
            'sensitive'        => $b->where('sensitive', true)->count(),
            'latest'           => $dates->max(),
            'earliest'         => $dates->min(),
        ];
    }

    /**
     * Official risk score + paste appearances from XposedOrNot analytics.
     * Tolerant: any failure yields safe empty defaults.
     */
    private function breachAnalytics(string $email): array
    {
        $empty = ['risk' => null, 'pastes' => ['count' => 0, 'items' => []]];

        try {
            $res = Http::timeout(8)->get('https://api.xposedornot.com/v1/breach-analytics', [
                'email' => $email,
            ]);

            if (!$res->successful()) {
                return $empty;
            }

            $d = $res->json();
            if (!is_array($d)) {
                return $empty;
            }

            $riskRaw = $d['BreachMetrics']['risk'][0] ?? null;
            $risk = $riskRaw ? [
                'label' => $riskRaw['risk_label'] ?? null,
                'score' => $riskRaw['risk_score'] ?? null,
            ] : null;

            // ExposedPastes shape varies (null / list / {pastes:[...]}). Normalise defensively.
            $rawPastes = $d['ExposedPastes'] ?? [];
            if (is_array($rawPastes) && isset($rawPastes['pastes']) && is_array($rawPastes['pastes'])) {
                $rawPastes = $rawPastes['pastes'];
            }
            $items = collect(is_array($rawPastes) ? $rawPastes : [])
                ->filter(fn ($p) => is_array($p))
                ->map(fn ($p) => [
                    'source' => $p['source'] ?? $p['domain'] ?? $p['title'] ?? 'Paste',
                    'date'   => $p['date'] ?? $p['tmpstmp'] ?? null,
                ])
                ->values()
                ->all();

            $count = (int) ($d['PastesSummary']['cnt'] ?? count($items));

            return ['risk' => $risk, 'pastes' => ['count' => $count, 'items' => $items]];
        } catch (\Throwable $e) {
            return $empty;
        }
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
