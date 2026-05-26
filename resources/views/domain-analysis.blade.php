@extends('layouts.public')

@section('title', 'Domain Analysis - ' . config('app.name', 'Onleaked'))

@section('content')
    <!-- Main Content -->
    <div class="pt-20 pb-20 px-6">
        <div class="max-w-4xl mx-auto" x-data="domainAnalyzer()">
            
            <div class="text-center mb-12 fade-up">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-amber-500/20 bg-amber-500/10 text-amber-400 text-xs mb-6">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                    Domain Intelligence
                </div>
                <h1 class="text-4xl md:text-5xl font-bold tracking-tight mb-4">
                    Analyze Any <span class="text-amber-400">Domain</span>
                </h1>
                <p class="text-zinc-400 text-lg max-w-2xl mx-auto">
                    Instantly retrieve DNS records, email security configurations (SPF, DMARC), subdomains, and reputation data.
                </p>
            </div>

            <!-- Search Bar -->
            <div class="max-w-2xl mx-auto mb-16 fade-up" style="animation-delay:.1s">
                <div class="glass-card rounded-2xl p-2 glow-input transition-all duration-300">
                    <form @submit.prevent="analyzeDomain" class="flex items-center gap-2">
                        <div class="flex-1 flex items-center gap-3 px-4">
                            <svg class="w-5 h-5 text-zinc-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input x-model="domain" type="text" placeholder="example.com" class="w-full bg-transparent border-none outline-none text-white placeholder-zinc-500 py-3 text-base focus:ring-0" required>
                        </div>
                        <button type="submit" :disabled="loading" class="px-6 py-3 bg-amber-500 text-black font-semibold rounded-xl hover:bg-amber-400 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shrink-0 text-sm">
                            <span x-show="!loading">Analyze</span>
                            <span x-show="loading" class="flex items-center gap-2">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                Scanning...
                            </span>
                        </button>
                    </form>
                </div>
                <template x-if="error">
                    <div class="mt-4 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm text-center" x-text="error"></div>
                </template>
            </div>

            <!-- Results Section -->
            <template x-if="results && !loading && !error">
                <div class="fade-up" style="animation-delay:.2s">
                    <!-- Tabs Header -->
                    <div class="flex border-b border-white/10 mb-6 overflow-x-auto hide-scrollbar">
                        <button @click="tab = 'dns'" :class="tab === 'dns' ? 'border-amber-400 text-amber-400' : 'border-transparent text-zinc-400 hover:text-white'" class="px-6 py-3 border-b-2 font-medium text-sm whitespace-nowrap transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            DNS Records
                        </button>
                        <button @click="tab = 'email'" :class="tab === 'email' ? 'border-amber-400 text-amber-400' : 'border-transparent text-zinc-400 hover:text-white'" class="px-6 py-3 border-b-2 font-medium text-sm whitespace-nowrap transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            Email Security
                        </button>
                        <button @click="tab = 'subdomains'" :class="tab === 'subdomains' ? 'border-amber-400 text-amber-400' : 'border-transparent text-zinc-400 hover:text-white'" class="px-6 py-3 border-b-2 font-medium text-sm whitespace-nowrap transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            Subdomains
                        </button>
                        <button @click="tab = 'reputation'" :class="tab === 'reputation' ? 'border-amber-400 text-amber-400' : 'border-transparent text-zinc-400 hover:text-white'" class="px-6 py-3 border-b-2 font-medium text-sm whitespace-nowrap transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Reputation
                        </button>
                    </div>

                    <!-- Tab Content: DNS -->
                    <div x-show="tab === 'dns'" class="glass-card rounded-2xl p-6" x-cloak>
                        <h3 class="text-xl font-semibold mb-4 text-white">DNS Records for <span class="text-amber-400" x-text="domainName"></span></h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="text-zinc-400 border-b border-white/10">
                                    <tr>
                                        <th class="py-3 px-4 font-medium">Type</th>
                                        <th class="py-3 px-4 font-medium">Host</th>
                                        <th class="py-3 px-4 font-medium">Value</th>
                                        <th class="py-3 px-4 font-medium">TTL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="record in results.dns">
                                        <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                                            <td class="py-3 px-4 font-medium text-white" x-text="record.type"></td>
                                            <td class="py-3 px-4 text-zinc-300" x-text="record.host"></td>
                                            <td class="py-3 px-4 text-zinc-400 font-mono text-xs break-all" x-text="formatDnsValue(record)"></td>
                                            <td class="py-3 px-4 text-zinc-500" x-text="record.ttl"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                            <template x-if="results.dns.length === 0">
                                <p class="text-center text-zinc-500 py-6">No DNS records found.</p>
                            </template>
                        </div>
                    </div>

                    <!-- Tab Content: Email Config -->
                    <div x-show="tab === 'email'" class="grid md:grid-cols-3 gap-4" x-cloak>
                        <!-- MX -->
                        <div class="glass-card rounded-2xl p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-semibold text-lg text-white">MX Records</h4>
                                <span :class="results.email_config.has_mx ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400'" class="px-2 py-1 rounded text-xs font-bold uppercase" x-text="results.email_config.has_mx ? 'Pass' : 'Fail'"></span>
                            </div>
                            <p class="text-zinc-400 text-sm">Mail Exchange records route emails for the domain. Essential for receiving mail.</p>
                        </div>
                        <!-- SPF -->
                        <div class="glass-card rounded-2xl p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-semibold text-lg text-white">SPF</h4>
                                <span :class="results.email_config.has_spf ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400'" class="px-2 py-1 rounded text-xs font-bold uppercase" x-text="results.email_config.has_spf ? 'Pass' : 'Fail'"></span>
                            </div>
                            <p class="text-zinc-400 text-sm">Sender Policy Framework prevents spoofing by verifying sender IP addresses.</p>
                        </div>
                        <!-- DMARC -->
                        <div class="glass-card rounded-2xl p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-semibold text-lg text-white">DMARC</h4>
                                <span :class="results.email_config.has_dmarc ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400'" class="px-2 py-1 rounded text-xs font-bold uppercase" x-text="results.email_config.has_dmarc ? 'Pass' : 'Fail'"></span>
                            </div>
                            <p class="text-zinc-400 text-sm">Domain-based Message Authentication helps protect domains from fraudulent email.</p>
                        </div>
                    </div>

                    <!-- Tab Content: Subdomains -->
                    <div x-show="tab === 'subdomains'" class="glass-card rounded-2xl p-6" x-cloak>
                         <h3 class="text-xl font-semibold mb-4 text-white">Discovered Subdomains</h3>
                         <div class="flex flex-wrap gap-2">
                             <template x-for="sub in results.subdomains">
                                 <span class="px-3 py-1.5 rounded-lg bg-white/5 border border-white/10 text-sm text-zinc-300" x-text="sub"></span>
                             </template>
                         </div>
                         <template x-if="results.subdomains.length === 0">
                             <p class="text-zinc-500">No subdomains discovered via certificate transparency logs.</p>
                         </template>
                    </div>

                    <!-- Tab Content: Reputation -->
                    <div x-show="tab === 'reputation'" class="glass-card rounded-2xl p-6 text-center" x-cloak>
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full mb-4" :class="results.reputation.status === 'clean' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-red-500/10 text-red-400'">
                            <svg x-show="results.reputation.status === 'clean'" class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <svg x-show="results.reputation.status !== 'clean'" class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-2" x-text="results.reputation.status === 'clean' ? 'Domain is Clean' : 'Domain Flagged'"></h3>
                        <p class="text-zinc-400" x-text="results.reputation.details"></p>
                    </div>
                </div>
            </template>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function domainAnalyzer() {
        return {
            domain: '',
            domainName: '',
            loading: false,
            results: null,
            error: null,
            tab: 'dns',
            async analyzeDomain() {
                this.loading = true;
                this.results = null;
                this.error = null;
                try {
                    const res = await fetch('{{ route("domain.analyze") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ domain: this.domain }),
                    });
                    const data = await res.json();
                    if (data.status === 'error') {
                        this.error = data.message;
                    } else {
                        this.results = data.results;
                        this.domainName = data.domain;
                        this.tab = 'dns';
                    }
                } catch (e) {
                    this.error = 'Connection error. Please try again.';
                } finally {
                    this.loading = false;
                }
            },
            formatDnsValue(record) {
                if (record.type === 'A' || record.type === 'AAAA') return record.ip || record.ipv6;
                if (record.type === 'MX') return `${record.pri} ${record.target}`;
                if (record.type === 'TXT') return record.txt;
                if (record.type === 'NS') return record.target;
                if (record.type === 'CNAME') return record.target;
                return JSON.stringify(record);
            }
        }
    }
</script>
@endpush
