<?php

namespace App\Services\Sources;

use Illuminate\Support\Str;
use RuntimeException;

class HoleheScannerService
{
    private string $tempDir;

    public function __construct()
    {
        $this->tempDir = storage_path('app/osint_tmp');
        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0700, true);
        }
    }

    public function scan(string $email): array
    {
        $outputFile = $this->tempDir . '/' . Str::uuid() . '.json';
        $sanitizedEmail  = escapeshellarg($email);
        $sanitizedOutput = escapeshellarg($outputFile);

        // holehe --json outputs one JSON object per line (NDJSON)
        $command = "python3 -m holehe {$sanitizedEmail} --json --no-color 2>/dev/null > {$sanitizedOutput}";
        exec($command, $output, $returnCode);

        if (!file_exists($outputFile)) {
            return ['sites' => [], 'total_checked' => 0, 'total_found' => 0, 'error' => 'holehe_no_output'];
        }

        $raw = file_get_contents($outputFile);
        @unlink($outputFile);

        if (empty(trim($raw))) {
            return ['sites' => [], 'total_checked' => 0, 'total_found' => 0];
        }

        // Holehe outputs NDJSON (one JSON per line)
        $lines = array_filter(explode("\n", trim($raw)));
        $sites = [];
        foreach ($lines as $line) {
            $decoded = json_decode($line, true);
            if ($decoded && isset($decoded['exists']) && $decoded['exists'] === true) {
                $sites[] = [
                    'name'  => $decoded['name'] ?? 'Unknown',
                    'url'   => $decoded['mainpage_url'] ?? null,
                    'email' => $decoded['email'] ?? $email,
                    'rateLimit' => $decoded['rateLimit'] ?? false,
                ];
            }
        }

        return [
            'sites'         => $sites,
            'total_checked' => count($lines),
            'total_found'   => count($sites),
        ];
    }
}
