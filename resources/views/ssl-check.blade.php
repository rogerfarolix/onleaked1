@extends('layouts.public')

@section('title', 'SSL Certificate Inspector Onleaked')

@section('content')
<div class="pt-20 pb-20 px-6">
    <div class="max-w-4xl mx-auto" x-data="sslChecker()">

        <div class="text-center mb-12 fade-up">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-sky-500/20 bg-sky-500/10 text-sky-400 text-xs mb-6">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                TLS inspection &bull; Certificate transparency
            </div>
            <h1 class="text-4xl md:text-5xl font-bold tracking-tight mb-4">
                SSL Certificate <span class="text-sky-400">Inspector</span>
            </h1>
            <p class="text-zinc-400 text-lg max-w-2xl mx-auto">
                Inspect any domain's SSL/TLS certificate issuer, validity dates, Subject Alternative Names, and security grade.
            </p>
        </div>

        <!-- Search Bar -->
        <div class="max-w-2xl mx-auto mb-16 fade-up" style="animation-delay:.1s">
            <div aria-hidden="true" style="position:absolute;left:-9999px;overflow:hidden;height:1px;width:1px">
                <input type="text" name="website" x-model="honeypot" autocomplete="off" tabindex="-1">
            </div>
            <div class="glass-card rounded-2xl p-2 glow-input transition-all duration-300">
                <form @submit.prevent="checkSsl" class="flex items-center gap-2">
                    <div class="flex-1 flex items-center gap-3 px-4">
                        <svg class="w-5 h-5 text-zinc-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3"/></svg>
                        <input x-model="domain" type="text" placeholder="example.com"
                            class="w-full bg-transparent border-none outline-none text-white placeholder-zinc-500 py-3 text-base focus:ring-0"
                            required autocomplete="off">
                    </div>
                    <button type="submit" :disabled="loading"
                        class="px-6 py-3 bg-white text-black font-semibold rounded-xl hover:bg-zinc-200 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shrink-0 text-sm">
                        <span x-show="!loading">Inspect</span>
                        <span x-show="loading" x-cloak class="flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            Connecting…
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

                <!-- Grade card -->
                <div class="glass-card rounded-2xl p-5 mb-6 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-3xl font-black shrink-0"
                            :class="{
                                'bg-emerald-500/10 border border-emerald-500/20 text-emerald-400': results.ssl.grade === 'A',
                                'bg-amber-500/10 border border-amber-500/20 text-amber-400': results.ssl.grade === 'B',
                                'bg-orange-500/10 border border-orange-500/20 text-orange-400': results.ssl.grade === 'C',
                                'bg-red-500/10 border border-red-500/20 text-red-400': results.ssl.grade === 'F',
                            }" x-text="results.ssl.grade"></div>
                        <div>
                            <p class="text-zinc-400 text-sm mb-0.5">Security grade for <span class="font-mono text-white" x-text="results.domain"></span></p>
                            <p class="font-semibold text-white" x-text="gradeLabel()"></p>
                            <p x-show="results.ssl.days_left !== null" class="text-sm mt-0.5 font-mono"
                                :class="{'text-emerald-400': results.ssl.days_left > 30, 'text-amber-400': results.ssl.days_left >= 7 && results.ssl.days_left <= 30, 'text-red-400': results.ssl.days_left < 7}"
                                x-text="results.ssl.days_left + ' days remaining'"></p>
                        </div>
                    </div>
                    <button @click="downloadPdf()" :disabled="pdfLoading"
                        class="px-4 py-2 bg-white/5 border border-white/10 text-zinc-300 font-semibold rounded-xl hover:bg-white/10 transition-all text-sm flex items-center gap-2 disabled:opacity-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span x-show="!pdfLoading">PDF</span>
                        <span x-show="pdfLoading" x-cloak>…</span>
                    </button>
                </div>

                <!-- Tabs -->
                <div class="flex border-b border-white/10 mb-6">
                    <button @click="tab='details'" :class="tab==='details' ? 'border-sky-400 text-sky-400' : 'border-transparent text-zinc-400 hover:text-white'" class="px-5 py-3 border-b-2 font-medium text-sm transition-colors">Certificate</button>
                    <button @click="tab='sans'" :class="tab==='sans' ? 'border-sky-400 text-sky-400' : 'border-transparent text-zinc-400 hover:text-white'" class="px-5 py-3 border-b-2 font-medium text-sm transition-colors flex items-center gap-2">
                        SANs
                        <span x-show="results.ssl.sans && results.ssl.sans.length" class="px-1.5 py-0.5 rounded-full bg-sky-500/20 text-sky-400 text-xs" x-text="results.ssl.sans && results.ssl.sans.length"></span>
                    </button>
                    <button @click="tab='recs'" :class="tab==='recs' ? 'border-sky-400 text-sky-400' : 'border-transparent text-zinc-400 hover:text-white'" class="px-5 py-3 border-b-2 font-medium text-sm transition-colors">Recommendations</button>
                </div>

                <!-- Certificate Details -->
                <div x-show="tab === 'details'" x-cloak>
                    <div class="space-y-2">
                        <template x-for="row in detailRows()" :key="row.label">
                            <div class="glass-card rounded-xl px-5 py-3.5 flex items-center justify-between gap-4">
                                <span class="text-zinc-500 text-sm shrink-0" x-text="row.label"></span>
                                <span class="text-zinc-200 text-sm font-mono text-right truncate" x-text="row.value || '—'"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- SANs -->
                <div x-show="tab === 'sans'" x-cloak>
                    <template x-if="results.ssl.sans && results.ssl.sans.length > 0">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <template x-for="(san, i) in results.ssl.sans" :key="i">
                                <div class="glass-card rounded-xl px-4 py-2.5 font-mono text-sm text-zinc-300 truncate" x-text="san"></div>
                            </template>
                        </div>
                    </template>
                    <template x-if="!results.ssl.sans || results.ssl.sans.length === 0">
                        <p class="text-zinc-500 text-sm text-center py-10">No Subject Alternative Names found.</p>
                    </template>
                </div>

                <!-- Recommendations -->
                <div x-show="tab === 'recs'" x-cloak>
                    <div class="glass-card rounded-2xl p-6 space-y-3">
                        <template x-if="results.ssl.grade === 'A'">
                            <div class="space-y-3">
                                <div class="flex items-start gap-3 text-sm text-zinc-300">
                                    <svg class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Certificate is valid and trusted
                                </div>
                                <div class="flex items-start gap-3 text-sm text-zinc-300">
                                    <svg class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    More than 30 days until expiry
                                </div>
                                <div class="flex items-start gap-3 text-sm text-zinc-300">
                                    <svg class="w-4 h-4 text-zinc-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    Set up auto-renewal to avoid future expiry issues
                                </div>
                            </div>
                        </template>
                        <template x-if="results.ssl.grade === 'B'">
                            <div class="space-y-3">
                                <div class="flex items-start gap-3 text-sm text-zinc-300">
                                    <svg class="w-4 h-4 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Certificate expires within 30 days renew soon
                                </div>
                                <div class="flex items-start gap-3 text-sm text-zinc-300">
                                    <svg class="w-4 h-4 text-zinc-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    Configure automatic renewal (Let's Encrypt certbot, or your CA's renewal process)
                                </div>
                            </div>
                        </template>
                        <template x-if="results.ssl.grade === 'C' || results.ssl.grade === 'F'">
                            <div class="space-y-3">
                                <div class="flex items-start gap-3 text-sm text-zinc-300">
                                    <svg class="w-4 h-4 text-red-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                    Certificate is expired or critically close to expiry
                                </div>
                                <div class="flex items-start gap-3 text-sm text-zinc-300">
                                    <svg class="w-4 h-4 text-red-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                    Visitors will see browser security warnings this affects trust and SEO
                                </div>
                                <div class="flex items-start gap-3 text-sm text-zinc-300">
                                    <svg class="w-4 h-4 text-zinc-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    Renew the certificate immediately and check your renewal automation
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>

    </div>
</div>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce }}">
function sslChecker() {
    return {
        domain: '',
        honeypot: '',
        loading: false,
        pdfLoading: false,
        results: null,
        error: null,
        tab: 'details',
        gradeLabel() {
            const g = this.results?.ssl?.grade;
            return {
                A: 'Excellent Certificate is valid and healthy',
                B: 'Good Expiring within 30 days',
                C: 'Warning Expiring very soon',
                F: 'Critical Expired or connection failed',
            }[g] ?? '';
        },
        detailRows() {
            const s = this.results?.ssl;
            if (!s) return [];
            return [
                { label: 'Subject (CN)',   value: s.subject_cn },
                { label: 'Issuer (CN)',    value: s.issuer_cn },
                { label: 'Issuer (Org)',   value: s.issuer_org },
                { label: 'Valid From',     value: s.valid_from },
                { label: 'Valid To',       value: s.valid_to },
                { label: 'Days Remaining', value: s.days_left !== null ? s.days_left + ' days' : null },
                { label: 'Serial Number',  value: s.serial },
            ];
        },
        async checkSsl() {
            this.loading = true; this.results = null; this.error = null; this.tab = 'details';
            try {
                const res = await fetch('{{ route("check-ssl") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ domain: this.domain, website: this.honeypot }),
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
                const res = await fetch('{{ route("pdf.ssl-check") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ domain: this.results.domain, ssl: this.results.ssl }),
                });
                if (!res.ok) throw new Error();
                const blob = await res.blob();
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a'); a.href = url; a.download = 'onleaked-ssl-report.pdf'; a.click();
                URL.revokeObjectURL(url);
            } catch { this.error = 'Could not generate PDF.'; }
            finally { this.pdfLoading = false; }
        },
    }
}
</script>
@endpush
