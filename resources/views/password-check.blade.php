@extends('layouts.public')

@section('title', 'Password Breach Check Onleaked')

@section('content')
<div class="pt-20 pb-20 px-6">
    <div class="max-w-4xl mx-auto" x-data="passwordChecker()">

        <div class="text-center mb-12 fade-up">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-cyan-500/20 bg-cyan-500/10 text-cyan-400 text-xs mb-6">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                k-Anonymity password never sent in full
            </div>
            <h1 class="text-4xl md:text-5xl font-bold tracking-tight mb-4">
                Has your password been <span class="text-cyan-400">leaked?</span>
            </h1>
            <p class="text-zinc-400 text-lg max-w-2xl mx-auto">
                Check against billions of known breached passwords. Only the first 5 characters of your password's SHA-1 hash are ever sent your password never leaves your browser in full.
            </p>
        </div>

        <!-- Search Bar -->
        <div class="max-w-2xl mx-auto mb-16 fade-up" style="animation-delay:.1s">
            <div aria-hidden="true" style="position:absolute;left:-9999px;overflow:hidden;height:1px;width:1px">
                <input type="text" name="website" x-model="honeypot" autocomplete="off" tabindex="-1">
            </div>
            <div class="glass-card rounded-2xl p-2 glow-input transition-all duration-300">
                <form @submit.prevent="checkPassword" class="flex items-center gap-2">
                    <div class="flex-1 flex items-center gap-3 px-4">
                        <svg class="w-5 h-5 text-zinc-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <input x-model="password" type="text" placeholder="Enter a password to check…"
                            class="w-full bg-transparent border-none outline-none text-white placeholder-zinc-500 py-3 text-base focus:ring-0"
                            required autocomplete="off" spellcheck="false">
                    </div>
                    <button type="submit" :disabled="loading"
                        class="px-6 py-3 bg-white text-black font-semibold rounded-xl hover:bg-zinc-200 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shrink-0 text-sm">
                        <span x-show="!loading">Check</span>
                        <span x-show="loading" x-cloak class="flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            Checking…
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

                <!-- Score card -->
                <div class="glass-card rounded-2xl p-5 mb-6 flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="text-zinc-400 text-sm mb-1">Result</p>
                        <div class="text-4xl font-bold"
                            :class="{
                                'text-emerald-400': results.risk === 'safe',
                                'text-amber-400':   results.risk === 'low',
                                'text-orange-400':  results.risk === 'medium',
                                'text-red-400':     results.risk === 'high'
                            }">
                            <span x-text="results.count === 0 ? '0' : results.count.toLocaleString()"></span>
                            <span class="text-xl text-zinc-500"> breach(es)</span>
                        </div>
                        <p class="text-sm mt-1 text-zinc-500" x-text="results.risk_label"></p>
                    </div>
                    <div class="flex gap-2 items-center">
                        <span class="px-3 py-1.5 rounded-lg text-sm font-semibold border"
                            :class="{
                                'bg-emerald-500/10 text-emerald-400 border-emerald-500/20': results.risk === 'safe',
                                'bg-amber-500/10 text-amber-400 border-amber-500/20': results.risk === 'low',
                                'bg-orange-500/10 text-orange-400 border-orange-500/20': results.risk === 'medium',
                                'bg-red-500/10 text-red-400 border-red-500/20': results.risk === 'high',
                            }"
                            x-text="results.risk.toUpperCase()">
                        </span>
                        <button @click="downloadPdf()" :disabled="pdfLoading"
                            class="px-4 py-2 bg-white/5 border border-white/10 text-zinc-300 font-semibold rounded-xl hover:bg-white/10 transition-all text-sm flex items-center gap-2 disabled:opacity-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span x-show="!pdfLoading">PDF</span>
                            <span x-show="pdfLoading" x-cloak>…</span>
                        </button>
                    </div>
                </div>

                <!-- Recommendations -->
                <div class="glass-card rounded-2xl p-6">
                    <h3 class="font-semibold mb-4 text-sm uppercase tracking-widest text-zinc-500">Recommendations</h3>
                    <template x-if="results.risk === 'safe'">
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3 text-sm text-zinc-300">
                                <svg class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                This password was not found in any known data breach database
                            </li>
                            <li class="flex items-start gap-3 text-sm text-zinc-300">
                                <svg class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Continue using a unique password for each service
                            </li>
                            <li class="flex items-start gap-3 text-sm text-zinc-300">
                                <svg class="w-4 h-4 text-zinc-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Consider using a password manager to generate stronger passwords
                            </li>
                        </ul>
                    </template>
                    <template x-if="results.risk !== 'safe'">
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3 text-sm text-zinc-300">
                                <svg class="w-4 h-4 text-red-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                Change this password immediately on every service where it is used
                            </li>
                            <li class="flex items-start gap-3 text-sm text-zinc-300">
                                <svg class="w-4 h-4 text-red-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                Never reuse this password anywhere
                            </li>
                            <li class="flex items-start gap-3 text-sm text-zinc-300">
                                <svg class="w-4 h-4 text-zinc-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                Enable two-factor authentication (2FA) on all important accounts
                            </li>
                            <li class="flex items-start gap-3 text-sm text-zinc-300">
                                <svg class="w-4 h-4 text-zinc-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                Use a password manager to generate long, unique passwords for every site
                            </li>
                        </ul>
                    </template>
                </div>
            </div>
        </template>

    </div>
</div>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce }}">
function passwordChecker() {
    return {
        password: '',
        honeypot: '',
        loading: false,
        pdfLoading: false,
        results: null,
        error: null,
        async checkPassword() {
            this.loading = true; this.results = null; this.error = null;
            try {
                const res = await fetch('{{ route("check-password") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ password: this.password, website: this.honeypot }),
                });
                const data = await res.json();
                if (data.status === 'error') this.error = data.message;
                else this.results = data;
            } catch { this.error = 'Connection error. Please try again.'; }
            finally { this.loading = false; }
        },
        async downloadPdf() {
            this.pdfLoading = true;
            try {
                const res = await fetch('{{ route("pdf.password-check") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        count: this.results.count,
                        risk: this.results.risk,
                        risk_label: this.results.risk_label,
                    }),
                });
                if (!res.ok) throw new Error();
                const blob = await res.blob();
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a'); a.href = url; a.download = 'onleaked-password-report.pdf'; a.click();
                URL.revokeObjectURL(url);
            } catch { this.error = 'Could not generate PDF.'; }
            finally { this.pdfLoading = false; }
        },
    }
}
</script>
@endpush
