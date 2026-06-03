@extends('layouts.public')

@section('title', 'Domain Report: ' . $domain . ' Onleaked')

@section('content')
<div class="pt-28 pb-20 px-6">
    <div class="max-w-4xl mx-auto">

        <!-- Header -->
        <div class="mb-10 fade-up">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-amber-500/20 bg-amber-500/10 text-amber-400 text-xs mb-6">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                Shared Report &bull; Expires {{ $expiresAt->diffForHumans() }}
            </div>
            <h1 class="text-3xl md:text-4xl font-bold mb-2">
                Domain Analysis: <span class="text-amber-400">{{ $domain }}</span>
            </h1>
            <div class="flex items-center gap-4 mt-4">
                <a href="{{ route('domain.show') }}?domain={{ urlencode($domain) }}" class="text-sm px-4 py-2 bg-white/5 border border-white/10 text-zinc-300 rounded-lg hover:bg-white/10 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Re-analyze
                </a>
            </div>
        </div>

        @php
            $emailCfg   = $results['email_config'] ?? [];
            $reputation = $results['reputation'] ?? [];
            $dns        = $results['dns'] ?? [];
            $subdomains = $results['subdomains'] ?? [];

            $score = 100;
            if (!($emailCfg['has_spf']   ?? false)) $score -= 20;
            if (!($emailCfg['has_dmarc'] ?? false)) $score -= 20;
            if (!($emailCfg['has_mx']    ?? false)) $score -= 10;
            $flagged = $reputation['engines_flagged'] ?? 0;
            if ($flagged > 0)  $score -= 30;
            if ($flagged >= 5) $score -= 10;
            if (($reputation['ioc_hits'] ?? 0) > 0) $score -= 20;
            $score = max(0, $score);
            $scoreColor = $score >= 80 ? 'emerald' : ($score >= 50 ? 'amber' : 'red');
        @endphp

        <!-- Score -->
        <div class="glass-card rounded-2xl p-6 mb-6 flex items-center justify-between fade-up">
            <div>
                <p class="text-zinc-400 text-sm mb-1">Security Score</p>
                <div class="text-5xl font-bold text-{{ $scoreColor }}-400">{{ $score }}<span class="text-2xl text-zinc-500">/100</span></div>
                <p class="text-sm mt-1 text-zinc-500">
                    @if($score >= 80) Good security posture
                    @elseif($score >= 50) Some improvements needed
                    @else Critical issues detected @endif
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('pdf.domain') }}" onclick="this.closest('form')?.submit(); return false;" class="px-4 py-2 bg-amber-500 text-black font-semibold rounded-xl hover:bg-amber-400 transition-all text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Download PDF
                </a>
            </div>
        </div>

        <!-- Email Security -->
        <div class="glass-card rounded-2xl p-6 mb-6 fade-up">
            <h3 class="font-semibold text-lg mb-4">Email Security</h3>
            <div class="grid md:grid-cols-3 gap-4">
                @foreach(['has_mx' => ['MX Records', 'Mail Exchange records route emails for the domain.'], 'has_spf' => ['SPF', 'Sender Policy Framework prevents spoofing.'], 'has_dmarc' => ['DMARC', 'Protects against fraudulent email.']] as $key => [$label, $desc])
                    <div class="bg-white/5 rounded-xl p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-medium">{{ $label }}</span>
                            @if($emailCfg[$key] ?? false)
                                <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400">PASS</span>
                            @else
                                <span class="text-xs px-2 py-0.5 rounded-full bg-red-500/20 text-red-400">FAIL</span>
                            @endif
                        </div>
                        <p class="text-xs text-zinc-500">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Reputation -->
        <div class="glass-card rounded-2xl p-6 mb-6 fade-up">
            <h3 class="font-semibold text-lg mb-4">Reputation</h3>
            @php $repStatus = $reputation['status'] ?? 'clean'; @endphp
            <div class="flex items-center gap-3 mb-4">
                <span class="px-3 py-1 rounded-full text-sm font-semibold
                    {{ $repStatus === 'clean' ? 'bg-emerald-500/20 text-emerald-400' : ($repStatus === 'suspicious' ? 'bg-amber-500/20 text-amber-400' : 'bg-red-500/20 text-red-400') }}">
                    {{ strtoupper($repStatus) }}
                </span>
                <span class="text-zinc-400 text-sm">{{ $reputation['details'] ?? '' }}</span>
            </div>
            @if($reputation['sources']['virustotal'] ?? false)
                <div class="grid grid-cols-4 gap-3">
                    <div class="text-center bg-red-500/10 rounded-xl p-3"><p class="text-2xl font-bold text-red-400">{{ $reputation['engines_flagged'] ?? 0 }}</p><p class="text-xs text-zinc-500">Malicious</p></div>
                    <div class="text-center bg-emerald-500/10 rounded-xl p-3"><p class="text-2xl font-bold text-emerald-400">{{ $reputation['harmless'] ?? 0 }}</p><p class="text-xs text-zinc-500">Harmless</p></div>
                    <div class="text-center bg-zinc-500/10 rounded-xl p-3"><p class="text-2xl font-bold text-zinc-400">{{ $reputation['undetected'] ?? 0 }}</p><p class="text-xs text-zinc-500">Undetected</p></div>
                    <div class="text-center bg-violet-500/10 rounded-xl p-3"><p class="text-2xl font-bold text-violet-400">{{ $reputation['ioc_hits'] ?? 0 }}</p><p class="text-xs text-zinc-500">IOC Hits</p></div>
                </div>
            @endif
        </div>

        <!-- Subdomains -->
        @if(count($subdomains) > 0)
            <div class="glass-card rounded-2xl p-6 mb-6 fade-up">
                <h3 class="font-semibold text-lg mb-4">Subdomains ({{ count($subdomains) }})</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($subdomains as $sub)
                        <span class="px-3 py-1.5 rounded-lg bg-white/5 border border-white/10 text-sm text-zinc-300">{{ $sub }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- DNS Records -->
        <div class="glass-card rounded-2xl p-6 fade-up">
            <h3 class="font-semibold text-lg mb-4">DNS Records ({{ count($dns) }})</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-zinc-400 border-b border-white/10">
                        <tr><th class="py-2 px-3">Type</th><th class="py-2 px-3">Host</th><th class="py-2 px-3">Value</th><th class="py-2 px-3">TTL</th></tr>
                    </thead>
                    <tbody>
                        @foreach($dns as $r)
                            <tr class="border-b border-white/5">
                                <td class="py-2 px-3 font-medium text-white">{{ $r['type'] }}</td>
                                <td class="py-2 px-3 text-zinc-400">{{ $r['host'] ?? '' }}</td>
                                <td class="py-2 px-3 text-zinc-400 font-mono text-xs break-all">{{ $r['ip'] ?? $r['ipv6'] ?? ($r['target'] ?? ($r['txt'] ?? '')) }}</td>
                                <td class="py-2 px-3 text-zinc-500">{{ $r['ttl'] ?? '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
