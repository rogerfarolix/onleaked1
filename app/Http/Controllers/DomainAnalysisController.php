<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;

class DomainAnalysisController extends Controller
{
    public function show()
    {
        return view('domain-analysis');
    }

    public function analyze(Request $request)
    {
        $request->validate([
            'domain' => 'required|string|max:255',
        ]);

        $domain = strtolower(trim($request->input('domain')));
        
        // Strip protocols and www if provided
        $domain = preg_replace('#^https?://#', '', $domain);
        $domain = preg_replace('#^www\.#', '', $domain);
        // Strip paths
        $domain = explode('/', $domain)[0];

        // Rate limiting: max 5 checks per minute per IP
        $key = 'domain-check:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Too many requests. Please wait a moment before trying again.',
            ], 429);
        }
        RateLimiter::hit($key, 60);

        try {
            $dns = $this->getDnsRecords($domain);
            $emailConfig = $this->getEmailConfig($domain, $dns);
            $reputation = $this->getReputation($domain);

            return response()->json([
                'status' => 'success',
                'domain' => $domain,
                'results' => [
                    'dns' => $dns,
                    'email_config' => $emailConfig,
                    'reputation' => $reputation,
                    // Subdomains usually require a dedicated tool or API (e.g. crt.sh)
                    'subdomains' => $this->getSubdomains($domain),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Analysis failed. Please ensure the domain is valid.',
            ], 500);
        }
    }

    private function getDnsRecords($domain)
    {
        $records = [];
        $types = [DNS_A, DNS_MX, DNS_NS, DNS_TXT, DNS_AAAA];
        
        foreach ($types as $type) {
            $fetched = @dns_get_record($domain, $type);
            if ($fetched) {
                $records = array_merge($records, $fetched);
            }
        }

        return $records;
    }

    private function getEmailConfig($domain, $dns)
    {
        $hasSPF = false;
        $hasDMARC = false;
        
        // Check SPF in TXT records
        foreach ($dns as $record) {
            if ($record['type'] === 'TXT' && isset($record['txt']) && str_starts_with($record['txt'], 'v=spf1')) {
                $hasSPF = true;
                break;
            }
        }
        
        // Check DMARC
        $dmarcRecord = @dns_get_record('_dmarc.' . $domain, DNS_TXT);
        if ($dmarcRecord) {
            foreach ($dmarcRecord as $record) {
                if (isset($record['txt']) && str_starts_with($record['txt'], 'v=DMARC1')) {
                    $hasDMARC = true;
                    break;
                }
            }
        }

        // Check MX
        $hasMX = false;
        foreach ($dns as $record) {
            if ($record['type'] === 'MX') {
                $hasMX = true;
                break;
            }
        }

        return [
            'has_mx' => $hasMX,
            'has_spf' => $hasSPF,
            'has_dmarc' => $hasDMARC,
            'secure' => $hasMX && $hasSPF && $hasDMARC
        ];
    }

    private function getReputation($domain)
    {
        // For MVP, we mock the reputation check.
        // In production, integrate with Google Safe Browsing API or VirusTotal API.
        return [
            'status' => 'clean',
            'engines_flagged' => 0,
            'details' => 'No malicious activity detected.'
        ];
    }

    private function getSubdomains($domain)
    {
        // For MVP, we query crt.sh (Certificate Transparency Logs) which is free and open.
        try {
            $response = Http::timeout(5)->get('https://crt.sh/', [
                'q' => '%.' . $domain,
                'output' => 'json'
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                $subdomains = [];
                if (is_array($data)) {
                    foreach (array_slice($data, 0, 50) as $cert) { // Limit to 50
                        $name = str_replace('*.', '', $cert['name_value']);
                        if (!in_array($name, $subdomains) && $name !== $domain) {
                            $subdomains[] = $name;
                        }
                    }
                }
                return array_slice($subdomains, 0, 10); // Return top 10 unique
            }
        } catch (\Exception $e) {
            // Ignore timeout
        }
        
        return [];
    }
}
