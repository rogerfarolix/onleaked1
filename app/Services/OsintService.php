<?php

namespace App\Services;

use App\Models\EmailCache;
use App\Models\Scan;
use App\Models\ScanResult;
use App\Models\Report;
use App\Services\Sources\HoleheScannerService;
use App\Services\Sources\EmailRepService;
use App\Services\Sources\BreachDirectoryService;
use App\Services\Sources\GravatarService;
use App\Services\Sources\DnsIntelService;
use App\Services\Sources\HibpService;
use Illuminate\Support\Facades\Log;
use Throwable;

class OsintService
{
    public function __construct(
        private readonly HoleheScannerService  $holehe,
        private readonly EmailRepService        $emailrep,
        private readonly BreachDirectoryService $breach,
        private readonly GravatarService        $gravatar,
        private readonly DnsIntelService        $dns,
        private readonly HibpService            $hibp,
        private readonly OsintReportBuilder     $builder,
    ) {}

    public function run(Scan $scan): Report
    {
        $email = $scan->email_target;

        // Check cache first
        $cached = EmailCache::findByEmail($email);
        if ($cached && !$cached->isExpired()) {
            Log::info("OSINT cache hit for domain: " . substr(strrchr($email, '@'), 1));
            return $this->buildFromCache($scan, $cached);
        }

        $sources = [
            'holehe'          => fn() => $this->holehe->scan($email),
            'emailrep'        => fn() => $this->emailrep->lookup($email),
            'breachdirectory' => fn() => $this->breach->lookup($email),
            'gravatar'        => fn() => $this->gravatar->lookup($email),
            'dns'             => fn() => $this->dns->lookup($email),
            'hibp'            => fn() => $this->hibp->lookup($email),
        ];

        $rawResults = [];
        foreach ($sources as $source => $callable) {
            try {
                $rawData = $callable();
                $rawResults[$source] = $rawData;

                ScanResult::create([
                    'scan_id'     => $scan->id,
                    'source'      => $source,
                    'raw_data'    => $rawData,
                    'parsed_data' => $this->parseForSource($source, $rawData),
                    'fetched_at'  => now(),
                ]);
            } catch (Throwable $e) {
                Log::warning("OSINT source [{$source}] failed", [
                    'email'   => hash('sha256', $email),
                    'message' => $e->getMessage(),
                ]);
                $rawResults[$source] = null;
            }
        }

        $report = $this->builder->build($scan, $rawResults);

        // Store in cache for future fast lookups
        EmailCache::storeForEmail($email, $report->full_report);

        return $report;
    }

    /**
     * Parse raw API data into a normalized, UI-friendly format.
     * This differentiates raw_data (full API response) from parsed_data (normalized subset).
     */
    private function parseForSource(string $source, array $raw): array
    {
        return match($source) {
            'holehe' => [
                'sites_found'   => $raw['total_found'] ?? 0,
                'sites_checked' => $raw['total_checked'] ?? 0,
                'site_names'    => array_column($raw['sites'] ?? [], 'name'),
            ],
            'emailrep' => [
                'reputation'         => $raw['reputation'] ?? 'unknown',
                'suspicious'         => $raw['suspicious'] ?? false,
                'blacklisted'        => $raw['blacklisted'] ?? false,
                'credentials_leaked' => $raw['credentials_leaked'] ?? false,
                'data_breach'        => $raw['data_breach'] ?? false,
            ],
            'breachdirectory' => [
                'found'       => $raw['found'] ?? false,
                'count'       => $raw['count'] ?? 0,
                'has_results' => !empty($raw['result']),
            ],
            'gravatar' => [
                'has_gravatar'  => $raw['has_gravatar'] ?? false,
                'display_name'  => $raw['profile']['display_name'] ?? null,
                'username'      => $raw['profile']['username'] ?? null,
            ],
            'dns' => [
                'domain'         => $raw['domain'] ?? null,
                'is_disposable'  => $raw['is_disposable'] ?? false,
                'is_free'        => $raw['is_free'] ?? false,
                'has_spf'        => !empty($raw['spf']),
                'has_dmarc'      => !empty($raw['dmarc']),
                'mx_count'       => count($raw['mx_records'] ?? []),
            ],
            'hibp' => [
                'found'        => $raw['found'] ?? false,
                'breach_count' => $raw['count'] ?? 0,
                'breach_names' => array_column($raw['breaches'] ?? [], 'name'),
            ],
            default => $raw,
        };
    }

    private function buildFromCache(Scan $scan, EmailCache $cache): Report
    {
        return Report::updateOrCreate(
            ['scan_id' => $scan->id],
            [
                'accounts_found' => $cache->accounts_count,
                'breaches_found' => $cache->breaches_count,
                'risk_level'     => $cache->risk_level,
                'gravatar_url'   => $cache->cached_data['gravatar']['avatar_url'] ?? null,
                'full_report'    => $cache->cached_data,
                'email_sent'     => false,
            ]
        );
    }
}
