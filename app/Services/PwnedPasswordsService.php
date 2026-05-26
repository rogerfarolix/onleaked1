<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PwnedPasswordsService
{
    /**
     * Check if a plaintext password appears in the Have I Been Pwned database
     * using the k-anonymity model (only the first 5 chars of the SHA-1 hash are sent).
     *
     * Returns the number of times the password has been seen in breaches (0 = not found).
     */
    public function countBreaches(string $password): int
    {
        $hash   = strtoupper(sha1($password));
        $prefix = substr($hash, 0, 5);
        $suffix = substr($hash, 5);

        $response = Http::withHeaders([
            'Add-Padding' => 'true',
        ])->timeout(5)->get("https://api.pwnedpasswords.com/range/{$prefix}");

        if (!$response->successful()) {
            return 0;
        }

        foreach (explode("\n", $response->body()) as $line) {
            $parts = explode(':', trim($line));
            if (count($parts) === 2 && strtoupper($parts[0]) === $suffix) {
                return (int) $parts[1];
            }
        }

        return 0;
    }
}
