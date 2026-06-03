<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

class DigitalFootprintJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 120;
    public int $tries   = 1;

    public function __construct(
        private readonly string $email,
        private readonly string $jobId
    ) {}

    public function handle(): void
    {
        $sources = [];

        // 1) Fast HTTP enrichers (PHP-side): identity profile + reputation.
        $profile = $this->gravatarProfile();
        if ($profile) { $sources[] = 'gravatar'; }

        $reputation = $this->emailRep();
        if ($reputation) { $sources[] = 'emailrep'; }

        // 2) Account discovery via local OSINT tools (holehe + maigret).
        $holehe = $this->runHolehe();
        if (!empty($holehe)) { $sources[] = 'holehe'; }

        $maigret = $this->runMaigret();
        if (!empty($maigret)) { $sources[] = 'maigret'; }

        $accounts = $this->mergeAccounts($holehe, $maigret);

        // EmailRep also returns profile URLs — fold them in.
        if ($reputation && !empty($reputation['profiles'])) {
            $emailrepAccounts = collect($reputation['profiles'])->map(fn ($p) => [
                'name'   => ucfirst((string) $p),
                'url'    => 'https://' . $p,
                'domain' => $p,
                'source' => 'emailrep',
            ])->all();
            $accounts = $this->mergeAccounts($accounts, $emailrepAccounts);
        }

        Cache::put('footprint:' . $this->jobId, [
            'status' => 'done',
            'data'   => [
                'profile'    => $profile,
                'reputation' => $reputation,
                'accounts'   => $accounts,
                'sources'    => array_values(array_unique($sources)),
            ],
        ], now()->addMinutes(15));
    }

    /* ───────────────────────── Gravatar (free, no key) ───────────────────────── */

    private function gravatarProfile(): ?array
    {
        try {
            $hash = hash('sha256', strtolower(trim($this->email)));
            $res  = Http::timeout(8)->get('https://api.gravatar.com/v3/profiles/' . $hash);

            if (!$res->successful()) {
                return null;
            }

            $d = $res->json();
            if (!is_array($d) || (empty($d['display_name']) && empty($d['avatar_url']))) {
                return null;
            }

            $accounts = collect($d['verified_accounts'] ?? [])
                ->filter(fn ($a) => empty($a['is_hidden']))
                ->map(fn ($a) => [
                    'label' => $a['service_label'] ?? ($a['service_type'] ?? 'Compte'),
                    'type'  => $a['service_type'] ?? null,
                    'url'   => $a['url'] ?? null,
                    'icon'  => $a['service_icon'] ?? null,
                ])
                ->filter(fn ($a) => !empty($a['url']))
                ->values()
                ->all();

            return [
                'display_name' => $d['display_name'] ?? null,
                'avatar_url'   => $d['avatar_url'] ?? null,
                'profile_url'  => $d['profile_url'] ?? null,
                'location'     => $d['location'] ?? null,
                'job_title'    => $d['job_title'] ?? null,
                'company'      => $d['company'] ?? null,
                'description'  => $d['description'] ?? null,
                'pronouns'     => $d['pronouns'] ?? null,
                'accounts'     => $accounts,
            ];
        } catch (\Throwable $e) {
            return null;
        }
    }

    /* ───────────────────────── EmailRep (free key, optional) ───────────────────────── */

    private function emailRep(): ?array
    {
        $key = config('services.emailrep.api_key');
        if (empty($key)) {
            return null;
        }

        try {
            $res = Http::timeout(8)
                ->withHeaders([
                    'Key'        => $key,
                    'User-Agent' => 'Onleaked-OSINT/1.0',
                    'Accept'     => 'application/json',
                ])
                ->get('https://emailrep.io/' . urlencode($this->email));

            if (!$res->successful()) {
                return null;
            }

            $d = $res->json();
            if (!is_array($d) || ($d['status'] ?? null) === 'fail') {
                return null;
            }

            $details = $d['details'] ?? [];

            return [
                'reputation'         => $d['reputation'] ?? null,
                'suspicious'         => (bool) ($d['suspicious'] ?? false),
                'references'         => (int) ($d['references'] ?? 0),
                'blacklisted'        => (bool) ($details['blacklisted'] ?? false),
                'malicious_activity' => (bool) ($details['malicious_activity'] ?? false),
                'credentials_leaked' => (bool) ($details['credentials_leaked'] ?? false),
                'data_breach'        => (bool) ($details['data_breach'] ?? false),
                'first_seen'         => $details['first_seen'] ?? null,
                'last_seen'          => $details['last_seen'] ?? null,
                'profiles'           => is_array($details['profiles'] ?? null) ? $details['profiles'] : [],
            ];
        } catch (\Throwable $e) {
            return null;
        }
    }

    /* ───────────────────────── holehe ───────────────────────── */

    private function runHolehe(): array
    {
        $scriptPath = base_path('python_service/footprint.py');
        $pythonBin  = base_path('python_service/venv/bin/python');

        if (!file_exists($scriptPath) || !file_exists($pythonBin)) {
            return [];
        }

        try {
            $process = new Process([$pythonBin, $scriptPath, $this->email]);
            $process->setTimeout(45);
            $process->run();

            if (!$process->isSuccessful()) {
                return [];
            }

            $decoded = json_decode($process->getOutput(), true);
            if (!is_array($decoded)) {
                return [];
            }

            return collect($decoded)
                ->map(fn ($site) => preg_replace('/[^a-zA-Z0-9._\-]/', '', (string) $site))
                ->filter(fn ($s) => strlen($s) >= 2 && strlen($s) <= 100)
                ->map(fn ($domain) => [
                    'name'   => $domain,
                    'url'    => 'https://' . $domain,
                    'domain' => $domain,
                    'source' => 'holehe',
                ])
                ->values()
                ->all();
        } catch (ProcessTimedOutException) {
            return [];
        } catch (\Throwable $e) {
            Log::debug('holehe failed: ' . $e->getMessage());
            return [];
        }
    }

    /* ───────────────────────── maigret (optional) ───────────────────────── */

    private function runMaigret(): array
    {
        $scriptPath = base_path('python_service/maigret.py');
        $pythonBin  = base_path('python_service/venv/bin/python');
        $username   = $this->usernameFromEmail();

        if (!$username || !file_exists($scriptPath) || !file_exists($pythonBin)) {
            return [];
        }

        try {
            $process = new Process([$pythonBin, $scriptPath, $username]);
            $process->setTimeout(55);
            $process->run();

            if (!$process->isSuccessful()) {
                return [];
            }

            $decoded = json_decode($process->getOutput(), true);
            if (!is_array($decoded)) {
                return [];
            }

            return collect($decoded)
                ->filter(fn ($e) => is_array($e) && !empty($e['url']) && str_starts_with((string) $e['url'], 'http'))
                ->map(fn ($e) => [
                    'name'   => substr((string) ($e['name'] ?? ''), 0, 60),
                    'url'    => substr((string) $e['url'], 0, 300),
                    'domain' => $this->hostFromUrl((string) $e['url']),
                    'source' => 'maigret',
                ])
                ->values()
                ->all();
        } catch (ProcessTimedOutException) {
            return [];
        } catch (\Throwable $e) {
            Log::debug('maigret failed: ' . $e->getMessage());
            return [];
        }
    }

    /* ───────────────────────── helpers ───────────────────────── */

    private function usernameFromEmail(): ?string
    {
        $local = strtolower(explode('@', $this->email)[0] ?? '');
        $local = explode('+', $local)[0];
        $local = preg_replace('/[^a-z0-9._-]/', '', $local);

        return ($local && strlen($local) >= 3 && strlen($local) <= 40) ? $local : null;
    }

    private function hostFromUrl(string $url): ?string
    {
        $host = parse_url($url, PHP_URL_HOST);
        return $host ? preg_replace('/^www\./', '', $host) : null;
    }

    /**
     * Merge two account lists, de-duplicating by domain (falling back to url).
     */
    private function mergeAccounts(array $a, array $b): array
    {
        $out  = [];
        $seen = [];

        foreach (array_merge($a, $b) as $acc) {
            $keyRaw = $acc['domain'] ?? $acc['url'] ?? null;
            if (!$keyRaw) {
                continue;
            }
            $key = strtolower($keyRaw);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $out[] = $acc;
        }

        return array_slice($out, 0, 400);
    }
}
