<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class NvdService
{
    private string $baseUrl = 'https://services.nvd.nist.gov/rest/json/cves/2.0';
    private ?string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.nvd.api_key');
    }

    /**
     * Fetch CVEs for a technology keyword published since a given date.
     *
     * @return array<int, array{cveId: string, title: string, description: string, severity: string, published: string}>
     */
    public function fetchRecent(string $technology, Carbon $since): array
    {
        $params = [
            'keywordSearch'  => $technology,
            'keywordExactMatch' => '',
            'pubStartDate'   => $since->toIso8601String(),
            'pubEndDate'     => Carbon::now()->toIso8601String(),
            'resultsPerPage' => 20,
        ];

        $request = Http::timeout(15);
        if ($this->apiKey) {
            $request = $request->withHeaders(['apiKey' => $this->apiKey]);
        }

        $response = $request->get($this->baseUrl, $params);

        if (!$response->successful()) {
            return [];
        }

        $data = $response->json();
        $vulnerabilities = $data['vulnerabilities'] ?? [];

        return collect($vulnerabilities)->map(function (array $item): array {
            $cve  = $item['cve'];
            $desc = collect($cve['descriptions'] ?? [])
                ->firstWhere('lang', 'en')['value'] ?? 'No description available.';

            $metrics  = $cve['metrics'] ?? [];
            $severity = $this->extractSeverity($metrics);

            return [
                'cveId'       => $cve['id'],
                'title'       => $cve['id'],
                'description' => $desc,
                'severity'    => $severity,
                'published'   => $cve['published'] ?? null,
            ];
        })->toArray();
    }

    private function extractSeverity(array $metrics): string
    {
        // Try CVSS v3.1, then v3.0, then v2
        $cvss31 = $metrics['cvssMetricV31'][0]['cvssData']['baseSeverity'] ?? null;
        $cvss30 = $metrics['cvssMetricV30'][0]['cvssData']['baseSeverity'] ?? null;
        $cvss2  = $metrics['cvssMetricV2'][0]['baseSeverity'] ?? null;

        return strtolower($cvss31 ?? $cvss30 ?? $cvss2 ?? 'unknown');
    }

    public static function aiRecommendation(string $severity, string $cveId): string
    {
        return match (strtolower($severity)) {
            'critical' => "CRITICAL Patch {$cveId} immediately. This vulnerability can be exploited remotely and may lead to full system compromise. Apply the vendor patch now, isolate affected systems if not yet patched, and review access logs.",
            'high'     => "HIGH Prioritize patching {$cveId} within 7 days. Restrict access to affected services in the meantime and monitor for exploitation attempts.",
            'medium'   => "MEDIUM Schedule patching for {$cveId} within 30 days. Review configurations that might mitigate the risk while the patch is pending.",
            'low'      => "LOW Plan to patch {$cveId} in your next maintenance window. Monitor vendor advisories for changes in severity.",
            default    => "Review {$cveId} and apply the available patch according to your organization's vulnerability management policy.",
        };
    }
}
