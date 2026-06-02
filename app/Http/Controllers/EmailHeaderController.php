<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class EmailHeaderController extends Controller
{
    public function show()
    {
        return \Inertia\Inertia::render('HeaderCheck');
    }

    public function analyze(Request $request)
    {
        if ($request->filled('website')) {
            return response()->json(['status' => 'error', 'message' => 'Request blocked.'], 422);
        }

        $request->validate([
            'headers' => ['required', 'string', 'max:50000'],
        ]);

        $key = 'header-check:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json(['status' => 'error', 'message' => 'Too many requests.'], 429);
        }
        RateLimiter::hit($key, 60);

        $raw = $request->input('headers');

        try {
            $parsed = $this->parseHeaders($raw);
            return response()->json(['status' => 'success'] + $parsed);
        } catch (\Exception) {
            return response()->json(['status' => 'error', 'message' => 'Could not parse headers.'], 422);
        }
    }

    private function parseHeaders(string $raw): array
    {
        $lines = preg_split('/\r?\n/', $raw);

        $headers   = [];
        $routing   = [];
        $authResults = [];

        $currentHeader = '';
        $currentValue  = '';

        foreach ($lines as $line) {
            if (preg_match('/^\s+/', $line) && $currentHeader !== '') {
                $currentValue .= ' ' . trim($line);
            } else {
                if ($currentHeader !== '') {
                    $headers[] = ['name' => $currentHeader, 'value' => $currentValue];
                }
                if (str_contains($line, ':')) {
                    [$name, $val] = explode(':', $line, 2);
                    $currentHeader = trim($name);
                    $currentValue  = trim($val);
                } else {
                    $currentHeader = '';
                    $currentValue  = '';
                }
            }
        }
        if ($currentHeader !== '') {
            $headers[] = ['name' => $currentHeader, 'value' => $currentValue];
        }

        foreach ($headers as $h) {
            if (strcasecmp($h['name'], 'Authentication-Results') === 0) {
                $authResults[] = $h['value'];
            }
            if (strcasecmp($h['name'], 'Received') === 0) {
                $routing[] = $h['value'];
            }
        }

        $spf   = $this->extractAuthResult($authResults, 'spf');
        $dkim  = $this->extractAuthResult($authResults, 'dkim');
        $dmarc = $this->extractAuthResult($authResults, 'dmarc');

        // Fallback: check Received-SPF header
        if ($spf === 'none') {
            foreach ($headers as $h) {
                if (strcasecmp($h['name'], 'Received-SPF') === 0) {
                    $spf = $this->firstWord($h['value']);
                    break;
                }
            }
        }

        $summary = ($spf === 'pass' && $dkim === 'pass' && $dmarc === 'pass') ? 'secure'
            : (($spf === 'fail' || $dkim === 'fail' || $dmarc === 'fail') ? 'suspicious' : 'partial');

        $key_headers = [];
        $interesting = ['From', 'To', 'Subject', 'Date', 'Message-ID', 'Reply-To', 'Return-Path', 'X-Mailer', 'X-Originating-IP'];
        foreach ($interesting as $want) {
            foreach ($headers as $h) {
                if (strcasecmp($h['name'], $want) === 0) {
                    $key_headers[$want] = $h['value'];
                    break;
                }
            }
        }

        return [
            'authentication' => compact('spf', 'dkim', 'dmarc', 'summary'),
            'routing'        => array_slice(array_reverse($routing), 0, 10),
            'key_headers'    => $key_headers,
            'all_headers'    => array_slice($headers, 0, 100),
        ];
    }

    private function extractAuthResult(array $authResults, string $method): string
    {
        foreach ($authResults as $value) {
            if (preg_match('/\b' . preg_quote($method) . '=(\S+)/i', $value, $m)) {
                $result = strtolower(trim($m[1], ';,'));
                if (in_array($result, ['pass', 'fail', 'neutral', 'softfail', 'none', 'temperror', 'permerror'])) {
                    return $result;
                }
            }
        }
        return 'none';
    }

    private function firstWord(string $value): string
    {
        return strtolower(trim(explode(' ', trim($value))[0], '; '));
    }
}
