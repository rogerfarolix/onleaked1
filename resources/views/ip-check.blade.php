@extends('layouts.public')

@section('title', 'IP Reputation Check Onleaked')

@section('content')
<div class="pt-20 pb-20 px-6">
    <div class="max-w-4xl mx-auto" x-data="ipChecker()">

        <div class="text-center mb-12 fade-up">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-indigo-500/20 bg-indigo-500/10 text-indigo-400 text-xs mb-6">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3"/></svg>
                Geolocation &bull; ASN lookup &bull; Abuse intelligence
            </div>
            <h1 class="text-4xl md:text-5xl font-bold tracking-tight mb-4">
                IP Reputation <span class="text-indigo-400">Intelligence</span>
            </h1>
            <p class="text-zinc-400 text-lg max-w-2xl mx-auto">
                Analyze any IP address geolocation, ISP, ASN, and abuse confidence score from global threat intelligence feeds.
            </p>
        </div>

        <!-- Search Bar -->
        <div class="max-w-2xl mx-auto mb-16 fade-up" style="animation-delay:.1s">
            <div aria-hidden="true" style="position:absolute;left:-9999px;overflow:hidden;height:1px;width:1px">
                <input type="text" name="website" x-model="honeypot" autocomplete="off" tabindex="-1">
            </div>
            <div class="glass-card rounded-2xl p-2 glow-input transition-all duration-300">
                <form @submit.prevent="checkIp" class="flex items-center gap-2">
                    <div class="flex-1 flex items-center gap-3 px-4">
                        <svg class="w-5 h-5 text-zinc-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                        <input x-model="ip" type="text" placeholder="8.8.8.8 or 2001:4860:4860::8888"
                            class="w-full bg-transparent border-none outline-none text-white placeholder-zinc-500 py-3 text-base focus:ring-0"
                            required autocomplete="off">
                    </div>
                    <button type="submit" :disabled="loading"
                        class="px-6 py-3 bg-white text-black font-semibold rounded-xl hover:bg-zinc-200 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shrink-0 text-sm">
                        <span x-show="!loading">Analyze</span>
                        <span x-show="loading" x-cloak class="flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            Querying…
                        </span>
                    </button>
                </form>
            </div>
            <template x-if="error">
                <div class="mt-4 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm text-center" x-text="error"></div>
            </template>
        </div>

        <!-- Results -->
        <template x-if="results !== null && !error">
            <div class="fade-up" style="animation-delay:.2s">

                <!-- Header card -->
                <div class="glass-card rounded-2xl p-5 mb-6 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-2xl shrink-0" x-text="flagEmoji(results.geo?.countryCode)"></div>
                        <div>
                            <p class="text-zinc-400 text-sm">Analyzed IP</p>
                            <p class="font-mono font-semibold text-white text-lg" x-text="results.ip"></p>
                            <p class="text-zinc-500 text-sm" x-text="[results.geo?.city, results.geo?.country].filter(Boolean).join(', ')"></p>
                        </div>
                    </div>
                    <div x-show="results.abuse?.available && results.abuse?.abuseConfidenceScore !== undefined" class="text-right">
                        <p class="text-xs text-zinc-500 mb-1">Abuse Score</p>
                        <p class="text-3xl font-bold" :class="abuseColor()" x-text="results.abuse?.abuseConfidenceScore + '%'"></p>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="flex border-b border-white/10 mb-6">
                    <button @click="tab='geo'" :class="tab==='geo' ? 'border-indigo-400 text-indigo-400' : 'border-transparent text-zinc-400 hover:text-white'" class="px-5 py-3 border-b-2 font-medium text-sm transition-colors">Geolocation</button>
                    <button @click="tab='abuse'" :class="tab==='abuse' ? 'border-indigo-400 text-indigo-400' : 'border-transparent text-zinc-400 hover:text-white'" class="px-5 py-3 border-b-2 font-medium text-sm transition-colors">Abuse Reports</button>
                    <button @click="tab='network'" :class="tab==='network' ? 'border-indigo-400 text-indigo-400' : 'border-transparent text-zinc-400 hover:text-white'" class="px-5 py-3 border-b-2 font-medium text-sm transition-colors">Network</button>
                </div>

                <!-- Geo Tab -->
                <div x-show="tab === 'geo'" x-cloak class="space-y-2">
                    <template x-for="row in geoRows()" :key="row.label">
                        <div class="glass-card rounded-xl px-5 py-3.5 flex items-center justify-between gap-4">
                            <span class="text-zinc-500 text-sm shrink-0" x-text="row.label"></span>
                            <span class="text-zinc-200 text-sm text-right" x-text="row.value || '—'"></span>
                        </div>
                    </template>
                </div>

                <!-- Abuse Tab -->
                <div x-show="tab === 'abuse'" x-cloak>
                    <template x-if="results.abuse?.available === false">
                        <div class="glass-card rounded-2xl p-8 text-center">
                            <p class="text-zinc-500 text-sm">AbuseIPDB integration not configured.</p>
                            <p class="text-zinc-600 text-xs mt-1">Add <code class="text-zinc-400 font-mono">ABUSEIPDB_API_KEY</code> to your <code class="text-zinc-400 font-mono">.env</code> file to enable abuse data.</p>
                        </div>
                    </template>
                    <template x-if="results.abuse?.available !== false">
                        <div class="space-y-2">
                            <template x-for="row in abuseRows()" :key="row.label">
                                <div class="glass-card rounded-xl px-5 py-3.5 flex items-center justify-between gap-4">
                                    <span class="text-zinc-500 text-sm shrink-0" x-text="row.label"></span>
                                    <span class="text-sm text-right font-medium" :class="row.highlight ? abuseColor() : 'text-zinc-200'" x-text="row.value ?? '—'"></span>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Network Tab -->
                <div x-show="tab === 'network'" x-cloak class="space-y-2">
                    <template x-for="row in networkRows()" :key="row.label">
                        <div class="glass-card rounded-xl px-5 py-3.5 flex items-center justify-between gap-4">
                            <span class="text-zinc-500 text-sm shrink-0" x-text="row.label"></span>
                            <span class="text-zinc-200 text-sm font-mono text-right truncate max-w-xs" x-text="row.value || '—'"></span>
                        </div>
                    </template>
                </div>

            </div>
        </template>

    </div>
</div>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce }}">
function ipChecker() {
    return {
        ip: '',
        honeypot: '',
        loading: false,
        results: null,
        error: null,
        tab: 'geo',
        flagEmoji(code) {
            if (!code || code.length !== 2) return '🌐';
            return String.fromCodePoint(...[...code.toUpperCase()].map(c => c.codePointAt(0) + 127397));
        },
        abuseColor() {
            const s = this.results?.abuse?.abuseConfidenceScore ?? 0;
            return s > 50 ? 'text-red-400' : s > 20 ? 'text-amber-400' : 'text-emerald-400';
        },
        geoRows() {
            const g = this.results?.geo;
            if (!g) return [];
            return [
                { label: 'Country',    value: g.country },
                { label: 'Region',     value: g.regionName },
                { label: 'City',       value: g.city },
                { label: 'Timezone',   value: g.timezone },
                { label: 'Proxy/VPN',  value: g.proxy ? 'Yes' : 'No' },
                { label: 'Hosting/DC', value: g.hosting ? 'Yes' : 'No' },
            ];
        },
        abuseRows() {
            const a = this.results?.abuse;
            if (!a) return [];
            return [
                { label: 'Abuse Confidence Score', value: a.abuseConfidenceScore + '%', highlight: true },
                { label: 'Total Reports (90d)',     value: a.totalReports },
                { label: 'Usage Type',              value: a.usageType },
                { label: 'Associated Domain',       value: a.domain },
                { label: 'Is Whitelisted',          value: a.isWhitelisted ? 'Yes' : 'No' },
                { label: 'Is Tor Exit Node',        value: a.isTor ? 'Yes' : 'No' },
            ];
        },
        networkRows() {
            const g = this.results?.geo;
            if (!g) return [];
            return [
                { label: 'ISP',          value: g.isp },
                { label: 'Organization', value: g.org },
                { label: 'ASN',          value: g.as },
            ];
        },
        async checkIp() {
            this.loading = true; this.results = null; this.error = null; this.tab = 'geo';
            try {
                const res = await fetch('{{ route("check-ip") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ ip: this.ip, website: this.honeypot }),
                });
                const data = await res.json();
                if (data.status === 'error') this.error = data.message;
                else this.results = data;
            } catch { this.error = 'Connection error. Please try again.'; }
            finally { this.loading = false; }
        },
    }
}
</script>
@endpush
