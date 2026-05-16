<?php

namespace App\Services\Sources;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class BreachDirectoryService
{
    public function lookup(string $email): array
    {
        $apiKey = config('services.breachdirectory.key');

        if (!$apiKey) {
            return ['error' => 'no_api_key', 'found' => false, 'result' => []];
        }

        $response = Http::withHeaders([
            'X-RapidAPI-Key'  => $apiKey,
            'X-RapidAPI-Host' => 'breachdirectory.p.rapidapi.com',
        ])->timeout(15)->get('https://breachdirectory.p.rapidapi.com/', [
            'func' => 'auto',
            'term' => $email,
        ]);

        if ($response->status() === 429) {
            return ['error' => 'rate_limited', 'found' => false, 'result' => []];
        }

        if ($response->failed()) {
            throw new RuntimeException("BreachDirectory API error: " . $response->status());
        }

        $data = $response->json() ?? [];

        // Normalize output
        $results = [];
        foreach ($data['result'] ?? [] as $breach) {
            $results[] = [
                'password'  => $this->maskPassword($breach['password'] ?? ''),
                'sha1'      => $breach['sha1'] ?? null,
                'hash'      => $breach['hash'] ?? null,
                'sources'   => $breach['sources'] ?? [],
            ];
        }

        return [
            'found'   => $data['found'] ?? false,
            'count'   => count($results),
            'result'  => $results,
        ];
    }

    private function maskPassword(string $password): string
    {
        if (empty($password)) return '';
        $len = strlen($password);
        if ($len <= 2) return str_repeat('*', $len);
        return substr($password, 0, 1) . str_repeat('*', $len - 2) . substr($password, -1);
    }
}
