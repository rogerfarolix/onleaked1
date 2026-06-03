@extends('layouts.public')

@section('title', 'Email Header Analyzer Onleaked')

@section('content')
<div class="pt-20 pb-20 px-6">
    <div class="max-w-4xl mx-auto" x-data="headerAnalyzer()">

        <div class="text-center mb-12 fade-up">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-teal-500/20 bg-teal-500/10 text-teal-400 text-xs mb-6">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                SPF &bull; DKIM &bull; DMARC &bull; Routing analysis &bull; No external requests
            </div>
            <h1 class="text-4xl md:text-5xl font-bold tracking-tight mb-4">
                Email Header <span class="text-teal-400">Analyzer</span>
            </h1>
            <p class="text-zinc-400 text-lg max-w-2xl mx-auto">
                Paste raw email headers to verify authentication, detect spoofing, and visualize the delivery routing path. No data leaves your server.
            </p>
        </div>

        <!-- Input -->
        <div class="max-w-3xl mx-auto mb-16 fade-up" style="animation-delay:.1s">
            <div aria-hidden="true" style="position:absolute;left:-9999px;overflow:hidden;height:1px;width:1px">
                <input type="text" name="website" x-model="honeypot" autocomplete="off" tabindex="-1">
            </div>
            <div class="glass-card rounded-2xl p-4 glow-input transition-all duration-300">
                <form @submit.prevent="analyzeHeaders">
                    <textarea x-model="headers" rows="8"
                        placeholder="Paste raw email headers here…&#10;&#10;Received: from mail.example.com...&#10;DKIM-Signature: v=1; a=rsa-sha256...&#10;Authentication-Results: mx.google.com; spf=pass..."
                        class="w-full bg-transparent border-none outline-none text-sm text-zinc-300 placeholder-zinc-600 font-mono focus:ring-0 resize-none"
                        required></textarea>
                    <div class="flex justify-between items-center pt-3 border-t border-white/5">
                        <p class="text-xs text-zinc-600">Parsed entirely server-side no external API calls</p>
                        <button type="submit" :disabled="loading"
                            class="px-6 py-2.5 bg-white text-black font-semibold rounded-xl hover:bg-zinc-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                            <span x-show="!loading">Analyze</span>
                            <span x-show="loading" x-cloak class="flex items-center gap-2">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                Parsing…
                            </span>
                        </button>
                    </div>
                </form>
            </div>
            <template x-if="error">
                <div class="mt-4 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm text-center" x-text="error"></div>
            </template>
        </div>

        <!-- Results -->
        <template x-if="results !== null && !error">
            <div class="max-w-3xl mx-auto fade-up" style="animation-delay:.2s">

                <!-- Authentication summary banner -->
                <div class="glass-card rounded-2xl p-5 mb-6 flex items-center gap-4"
                    :style="results.authentication.summary === 'secure'
                        ? 'border-color:rgba(52,211,153,0.2)'
                        : results.authentication.summary === 'suspicious'
                            ? 'border-color:rgba(248,113,113,0.2)'
                            : 'border-color:rgba(251,191,36,0.2)'">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                        :class="{
                            'bg-emerald-500/10 text-emerald-400': results.authentication.summary === 'secure',
                            'bg-amber-500/10 text-amber-400': results.authentication.summary === 'partial',
                            'bg-red-500/10 text-red-400': results.authentication.summary === 'suspicious',
                        }">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <p class="font-semibold text-white text-sm" x-text="summaryLabel()"></p>
                        <p class="text-zinc-400 text-xs mt-0.5" x-text="summaryDesc()"></p>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="flex border-b border-white/10 mb-6">
                    <button @click="tab='auth'" :class="tab==='auth' ? 'border-teal-400 text-teal-400' : 'border-transparent text-zinc-400 hover:text-white'" class="px-5 py-3 border-b-2 font-medium text-sm transition-colors">Authentication</button>
                    <button @click="tab='routing'" :class="tab==='routing' ? 'border-teal-400 text-teal-400' : 'border-transparent text-zinc-400 hover:text-white'" class="px-5 py-3 border-b-2 font-medium text-sm transition-colors">
                        Routing Path
                        <span x-show="results.routing && results.routing.length" class="ml-1 px-1.5 py-0.5 rounded-full bg-teal-500/20 text-teal-400 text-xs" x-text="results.routing.length"></span>
                    </button>
                    <button @click="tab='headers'" :class="tab==='headers' ? 'border-teal-400 text-teal-400' : 'border-transparent text-zinc-400 hover:text-white'" class="px-5 py-3 border-b-2 font-medium text-sm transition-colors">Key Headers</button>
                </div>

                <!-- Authentication Tab -->
                <div x-show="tab === 'auth'" x-cloak class="space-y-3">
                    <template x-for="m in authMethods()" :key="m.name">
                        <div class="glass-card rounded-xl p-5 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center font-bold text-xs"
                                    :class="{
                                        'bg-emerald-500/10 text-emerald-400': m.result === 'pass',
                                        'bg-red-500/10 text-red-400': ['fail','softfail'].includes(m.result),
                                        'bg-zinc-800 text-zinc-500': !['pass','fail','softfail'].includes(m.result),
                                    }"
                                    x-text="m.name.toUpperCase()"></div>
                                <div>
                                    <p class="font-medium text-white text-sm" x-text="m.title"></p>
                                    <p class="text-zinc-500 text-xs mt-0.5" x-text="m.desc"></p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg text-xs font-semibold font-mono uppercase"
                                :class="{
                                    'bg-emerald-500/10 text-emerald-400': m.result === 'pass',
                                    'bg-red-500/10 text-red-400': ['fail','softfail'].includes(m.result),
                                    'bg-zinc-800 text-zinc-500': !['pass','fail','softfail'].includes(m.result),
                                }"
                                x-text="m.result"></span>
                        </div>
                    </template>
                </div>

                <!-- Routing Tab -->
                <div x-show="tab === 'routing'" x-cloak>
                    <template x-if="results.routing && results.routing.length > 0">
                        <div class="space-y-2">
                            <template x-for="(hop, i) in results.routing" :key="i">
                                <div class="flex items-start gap-3">
                                    <div class="w-6 h-6 rounded-full bg-teal-500/10 border border-teal-500/20 flex items-center justify-center text-xs text-teal-400 font-mono shrink-0 mt-1" x-text="i + 1"></div>
                                    <div class="glass-card flex-1 rounded-xl px-4 py-3 font-mono text-xs text-zinc-400 break-all" x-text="hop"></div>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template x-if="!results.routing || results.routing.length === 0">
                        <p class="text-zinc-500 text-sm text-center py-10">No <code class="font-mono text-zinc-400">Received:</code> headers found in the pasted content.</p>
                    </template>
                </div>

                <!-- Headers Tab -->
                <div x-show="tab === 'headers'" x-cloak class="space-y-2">
                    <template x-for="[name, value] in Object.entries(results.key_headers)" :key="name">
                        <div class="glass-card rounded-xl px-5 py-3.5">
                            <p class="text-xs text-zinc-500 font-mono mb-1" x-text="name"></p>
                            <p class="text-sm text-zinc-300 break-all" x-text="value"></p>
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
function headerAnalyzer() {
    return {
        headers: '',
        honeypot: '',
        loading: false,
        results: null,
        error: null,
        tab: 'auth',
        summaryLabel() {
            return {
                secure:     'Email is authenticated',
                partial:    'Partial authentication',
                suspicious: 'Authentication failed',
            }[this.results?.authentication?.summary] ?? 'Unknown';
        },
        summaryDesc() {
            return {
                secure:     'SPF, DKIM, and DMARC all passed this email is likely legitimate.',
                partial:    'Some authentication checks could not be determined treat with caution.',
                suspicious: 'One or more authentication checks failed this email may be spoofed.',
            }[this.results?.authentication?.summary] ?? '';
        },
        authMethods() {
            const a = this.results?.authentication;
            if (!a) return [];
            return [
                { name: 'SPF',   result: a.spf,   title: 'Sender Policy Framework',   desc: 'Verifies the sending server is authorized to send for this domain' },
                { name: 'DKIM',  result: a.dkim,  title: 'DomainKeys Identified Mail', desc: 'Cryptographic signature verifying the message has not been tampered with' },
                { name: 'DMARC', result: a.dmarc, title: 'Domain-based Message Auth',  desc: 'Policy alignment check combining SPF and DKIM results' },
            ];
        },
        async analyzeHeaders() {
            this.loading = true; this.results = null; this.error = null; this.tab = 'auth';
            try {
                const res = await fetch('{{ route("analyze-header") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ headers: this.headers, website: this.honeypot }),
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
