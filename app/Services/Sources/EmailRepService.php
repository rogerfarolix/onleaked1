<?php

namespace App\Services\Sources;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class EmailRepService
{
    private string $baseUrl = 'https://emailrep.io';

    public function lookup(string $email): array
    {
        $headers = ['User-Agent' => 'onleaked/1.0'];
        $apiKey  = config('services.emailrep.key');
        if ($apiKey) {
            $headers['Key'] = $apiKey;
        }

        $response = Http::withHeaders($headers)
            ->timeout(10)
            ->get("{$this->baseUrl}/{$email}");

        if ($response->status() === 429) {
            return ['error' => 'rate_limited', 'reputation' => 'unknown'];
        }

        if ($response->failed()) {
            throw new RuntimeException("EmailRep API error: HTTP " . $response->status());
        }

        $data = $response->json();

        return [
            'reputation'        => $data['reputation'] ?? 'unknown',
            'suspicious'        => $data['suspicious'] ?? false,
            'references'        => $data['references'] ?? 0,
            'blacklisted'       => $data['details']['blacklisted'] ?? false,
            'malicious_activity'=> $data['details']['malicious_activity'] ?? false,
            'credentials_leaked'=> $data['details']['credentials_leaked'] ?? false,
            'data_breach'       => $data['details']['data_breach'] ?? false,
            'first_seen'        => $data['details']['first_seen'] ?? null,
            'last_seen'         => $data['details']['last_seen'] ?? null,
            'domain_exists'     => $data['details']['domain_exists'] ?? true,
            'free_provider'     => $data['details']['free_provider'] ?? false,
            'disposable'        => $data['details']['disposable'] ?? false,
            'deliverable'       => $data['details']['deliverable'] ?? true,
            'profiles'          => $data['details']['profiles'] ?? [],
        ];
    }
}
