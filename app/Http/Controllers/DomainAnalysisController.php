<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;

class DomainAnalysisController extends Controller
{
    public function show(): \Inertia\Response
    {
        return \Inertia\Inertia::render('DomainAnalysis');
    }

    public function analyze(Request $request): \Illuminate\Http\JsonResponse
    {
        // Honeypot: bots fill this field, humans don't
        if ($request->filled('website')) {
            return response()->json(['status' => 'error', 'message' => 'Request blocked.'], 422);
        }

        $request->validate([
            'domain' => ['required', 'string', 'max:253'],
        ]);

        $domain = strtolower(trim($request->input('domain')));
        $domain = preg_replace('#^https?://#', '', $domain);
        $domain = preg_replace('#^www\.#', '', $domain);
        $domain = explode('/', $domain)[0];
        $domain = explode('?', $domain)[0];
        $domain = explode('#', $domain)[0];

        if (!preg_match('/^(?:[a-z0-9](?:[a-z0-9\-]{0,61}[a-z0-9])?\.)+[a-z]{2,}$/', $domain)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid domain format.'], 422);
        }

        $key = 'domain-check:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Too many requests. Please wait a moment before trying again.',
            ], 429);
        }
        RateLimiter::hit($key, 60);

        // Cache full analysis for 10 minutes to avoid hammering DNS + VirusTotal
        $cacheKey = 'domain-analysis:' . $domain;
        $results  = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($domain) {
            try {
                $dns        = $this->getDnsRecords($domain);
                $emailCfg   = $this->getEmailConfig($domain, $dns);
                $reputation = $this->getReputation($domain);
                $subdomains = $this->getSubdomains($domain);

                return [
                    'dns'          => $dns,
                    'email_config' => $emailCfg,
                    'reputation'   => $reputation,
                    'subdomains'   => $subdomains,
                ];
            } catch (\Exception) {
                return null;
            }
        });

        if ($results === null) {
            return response()->json(['status' => 'error', 'message' => 'Analysis failed. Please ensure the domain is valid.'], 500);
        }

        return response()->json([
            'status'  => 'success',
            'domain'  => $domain,
            'cached'  => Cache::has($cacheKey),
            'results' => $results,
        ]);
    }

    private function getDnsRecords(string $domain): array
    {
        $records = [];

        foreach ([DNS_A, DNS_MX, DNS_NS, DNS_TXT, DNS_AAAA] as $type) {
            $fetched = @dns_get_record($domain, $type);
            if ($fetched) {
                $records = array_merge($records, $fetched);
            }
        }

        return $records;
    }

    private function getEmailConfig(string $domain, array $dns): array
    {
        $hasSPF   = false;
        $hasDMARC = false;

        foreach ($dns as $record) {
            if ($record['type'] === 'TXT' && isset($record['txt']) && str_starts_with($record['txt'], 'v=spf1')) {
                $hasSPF = true;
                break;
            }
        }

        $dmarcRecord = @dns_get_record('_dmarc.' . $domain, DNS_TXT);
        if ($dmarcRecord) {
            foreach ($dmarcRecord as $record) {
                if (isset($record['txt']) && str_starts_with($record['txt'], 'v=DMARC1')) {
                    $hasDMARC = true;
                    break;
                }
            }
        }

        $hasMX = false;
        foreach ($dns as $record) {
            if ($record['type'] === 'MX') {
                $hasMX = true;
                break;
            }
        }

        return [
            'has_mx'    => $hasMX,
            'has_spf'   => $hasSPF,
            'has_dmarc' => $hasDMARC,
            'secure'    => $hasMX && $hasSPF && $hasDMARC,
        ];
    }

    private function getReputation(string $domain): array
    {
        $vtData       = $this->queryVirusTotal($domain);
        $iocData      = $this->queryTweetFeed($domain);

        $enginesFlag  = $vtData['malicious'] ?? 0;
        $iocHits      = $iocData['hits'] ?? 0;

        if ($enginesFlag >= 5 || $iocHits > 0) {
            $status  = $enginesFlag >= 10 ? 'malicious' : 'suspicious';
        } else {
            $status  = 'clean';
        }

        $details = match ($status) {
            'malicious'  => "{$enginesFlag} security engine(s) flagged this domain as malicious." . ($iocHits > 0 ? " Found in {$iocHits} IOC report(s) in the infosec community." : ''),
            'suspicious' => "{$enginesFlag} security engine(s) flagged this domain as suspicious." . ($iocHits > 0 ? " Found in {$iocHits} IOC report(s)." : ''),
            default      => 'No malicious activity detected by VirusTotal or the infosec IOC community.',
        };

        return [
            'status'          => $status,
            'engines_flagged' => $enginesFlag,
            'harmless'        => $vtData['harmless'] ?? 0,
            'undetected'      => $vtData['undetected'] ?? 0,
            'ioc_hits'        => $iocHits,
            'ioc_tags'        => $iocData['tags'] ?? [],
            'details'         => $details,
            'sources'         => [
                'virustotal' => $vtData['available'],
                'tweetfeed'  => $iocData['available'],
            ],
        ];
    }

    private function queryVirusTotal(string $domain): array
    {
        $apiKey = config('services.virustotal.api_key');

        if (!$apiKey) {
            return ['available' => false];
        }

        // Cache VT results for 1 hour (respect 500 req/day limit)
        return Cache::remember('vt:' . $domain, now()->addHour(), function () use ($domain, $apiKey): array {
            try {
                $response = Http::withHeaders(['x-apikey' => $apiKey])
                    ->timeout(10)
                    ->get("https://www.virustotal.com/api/v3/domains/{$domain}");

                if (!$response->successful()) {
                    return ['available' => false];
                }

                $stats = $response->json('data.attributes.last_analysis_stats', []);

                return [
                    'available'  => true,
                    'malicious'  => $stats['malicious'] ?? 0,
                    'suspicious' => $stats['suspicious'] ?? 0,
                    'harmless'   => $stats['harmless'] ?? 0,
                    'undetected' => $stats['undetected'] ?? 0,
                ];
            } catch (\Exception) {
                return ['available' => false];
            }
        });
    }

    private function queryTweetFeed(string $domain): array
    {
        // Cache the full IOC domain list for 15 minutes (TweetFeed updates every 15 min)
        $iocList = Cache::remember('tweetfeed:domains:month', now()->addMinutes(15), function (): array {
            try {
                $response = Http::timeout(8)->get('https://api.tweetfeed.live/v1/month/domain');
                if ($response->successful() && is_array($response->json())) {
                    return $response->json();
                }
            } catch (\Exception) {}
            return [];
        });

        $hits = [];
        $tags = [];

        foreach ($iocList as $ioc) {
            $value = $ioc['value'] ?? '';
            // Match exact domain or any subdomain of it
            if ($value === $domain || str_ends_with($value, '.' . $domain)) {
                $hits[] = $ioc;
                $tags   = array_merge($tags, $ioc['tags'] ?? []);
            }
        }

        return [
            'available' => true,
            'hits'      => count($hits),
            'tags'      => array_values(array_unique($tags)),
        ];
    }

    private function getSubdomains(string $domain): array
    {
        try {
            $response = Http::timeout(5)->get('https://crt.sh/', [
                'q'      => '%.' . $domain,
                'output' => 'json',
            ]);

            if ($response->successful()) {
                $data       = $response->json();
                $subdomains = [];

                if (is_array($data)) {
                    foreach (array_slice($data, 0, 50) as $cert) {
                        $name = str_replace('*.', '', $cert['name_value'] ?? '');
                        if ($name && !in_array($name, $subdomains) && $name !== $domain) {
                            $subdomains[] = $name;
                        }
                    }
                }

                return array_slice($subdomains, 0, 10);
            }
        } catch (\Exception) {}

        return [];
    }
}
