<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;

class IpCheckController extends Controller
{
    public function show()
    {
        return \Inertia\Inertia::render('IpCheck');
    }

    public function check(Request $request)
    {
        if ($request->filled('website')) {
            return response()->json(['status' => 'error', 'message' => 'Request blocked.'], 422);
        }

        $request->validate([
            'ip' => ['required', 'string', 'ip'],
        ]);

        $ip = trim($request->input('ip'));

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return response()->json(['status' => 'error', 'message' => 'Please enter a public IP address.'], 422);
        }

        $key = 'ip-check:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json(['status' => 'error', 'message' => 'Too many requests.'], 429);
        }
        RateLimiter::hit($key, 60);

        try {
            $result = Cache::remember('ip:' . $ip, 900, function () use ($ip) {
                $geo   = $this->getGeoInfo($ip);
                $abuse = $this->getAbuseInfo($ip);
                return compact('geo', 'abuse');
            });

            return response()->json(['status' => 'success', 'ip' => $ip] + $result);
        } catch (\Exception) {
            return response()->json(['status' => 'error', 'message' => 'Service temporarily unavailable.'], 503);
        }
    }

    private function getGeoInfo(string $ip): array
    {
        $fields = 'status,country,countryCode,regionName,city,isp,org,as,proxy,hosting,query,timezone';
        try {
            $res = Http::timeout(5)->get("http://ip-api.com/json/{$ip}?fields={$fields}");
            return $res->successful() ? $res->json() : [];
        } catch (\Exception) {
            return [];
        }
    }

    private function getAbuseInfo(string $ip): array
    {
        $apiKey = config('services.abuseipdb.api_key');
        if (empty($apiKey)) {
            return ['available' => false];
        }

        try {
            $res = Http::timeout(5)
                ->withHeaders(['Key' => $apiKey, 'Accept' => 'application/json'])
                ->get('https://api.abuseipdb.com/api/v2/check', [
                    'ipAddress'    => $ip,
                    'maxAgeInDays' => 90,
                ]);

            if ($res->successful()) {
                $data = $res->json('data', []);
                return ['available' => true] + $data;
            }
        } catch (\Exception) {}

        return ['available' => false];
    }
}
