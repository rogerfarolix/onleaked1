<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class SslCheckController extends Controller
{
    public function show()
    {
        return \Inertia\Inertia::render('SslCheck');
    }

    public function check(Request $request)
    {
        if ($request->filled('website')) {
            return response()->json(['status' => 'error', 'message' => 'Request blocked.'], 422);
        }

        $request->validate([
            'domain' => ['required', 'string', 'max:253', 'regex:/^(?:[a-zA-Z0-9](?:[a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/'],
        ]);

        $domain = strtolower(trim($request->input('domain')));

        $key = 'ssl-check:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json(['status' => 'error', 'message' => 'Too many requests.'], 429);
        }
        RateLimiter::hit($key, 60);

        try {
            $result = Cache::remember('ssl:' . $domain, 300, fn() => $this->inspectSsl($domain));

            return response()->json([
                'status' => 'success',
                'domain' => $domain,
                'ssl'    => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Could not connect to ' . $domain . '.'], 503);
        }
    }

    private function inspectSsl(string $domain): array
    {
        $context = stream_context_create([
            'ssl' => [
                'capture_peer_cert'      => true,
                'verify_peer'            => false,
                'verify_peer_name'       => false,
                'allow_self_signed'      => true,
            ],
        ]);

        $errno = 0; $errstr = '';
        $client = @stream_socket_client(
            "ssl://{$domain}:443",
            $errno, $errstr, 10,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (!$client) {
            return [
                'connected'   => false,
                'grade'       => 'F',
                'error'       => $errstr ?: 'Connection failed',
                'issuer'      => null,
                'subject'     => null,
                'valid_from'  => null,
                'valid_to'    => null,
                'days_left'   => null,
                'sans'        => [],
            ];
        }

        $params = stream_context_get_params($client);
        $cert   = $params['options']['ssl']['peer_certificate'] ?? null;
        fclose($client);

        if (!$cert) {
            return ['connected' => false, 'grade' => 'F', 'error' => 'No certificate returned', 'issuer' => null, 'subject' => null, 'valid_from' => null, 'valid_to' => null, 'days_left' => null, 'sans' => []];
        }

        $parsed    = openssl_x509_parse($cert);
        $validFrom = \Carbon\Carbon::createFromTimestamp($parsed['validFrom_time_t']);
        $validTo   = \Carbon\Carbon::createFromTimestamp($parsed['validTo_time_t']);
        $daysLeft  = (int) now()->diffInDays($validTo, false);

        $sans = [];
        if (isset($parsed['extensions']['subjectAltName'])) {
            foreach (explode(',', $parsed['extensions']['subjectAltName']) as $san) {
                $san = trim($san);
                if (str_starts_with($san, 'DNS:')) {
                    $sans[] = substr($san, 4);
                }
            }
        }

        if ($daysLeft > 30) $grade = 'A';
        elseif ($daysLeft >= 7) $grade = 'B';
        elseif ($daysLeft >= 0) $grade = 'C';
        else $grade = 'F';

        return [
            'connected'    => true,
            'grade'        => $grade,
            'issuer_cn'    => $parsed['issuer']['CN'] ?? null,
            'issuer_org'   => $parsed['issuer']['O'] ?? null,
            'subject_cn'   => $parsed['subject']['CN'] ?? null,
            'valid_from'   => $validFrom->toDateString(),
            'valid_to'     => $validTo->toDateString(),
            'days_left'    => $daysLeft,
            'sans'         => array_slice($sans, 0, 50),
            'serial'       => $parsed['serialNumberHex'] ?? null,
        ];
    }
}
