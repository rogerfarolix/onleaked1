<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="glass-card rounded-2xl overflow-hidden shadow-xl">
                <div class="p-6 md:p-10">
                    <h3 class="text-2xl font-bold text-white mb-2">Your Cybersecurity Overview</h3>
                    <p class="text-zinc-400 mb-8">{{ __("Select the technologies you want to monitor for vulnerabilities. We'll send you AI-powered alerts when new CVEs are published.") }}</p>

                    @if (session('status') === 'technologies-updated')
                        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm font-medium flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            {{ __('Your technology preferences have been saved.') }}
                        </div>
                    @endif

                    <form method="post" action="{{ route('technologies.update') }}">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
                            @foreach ($technologies as $tech)
                                <label class="relative flex items-center p-4 rounded-xl border {{ in_array($tech->id, $userTechnologies) ? 'border-violet-500 bg-violet-500/10' : 'border-white/10 bg-white/5' }} hover:border-violet-500/50 cursor-pointer transition-all duration-200 group">
                                    <div class="flex items-center h-5">
                                        <input 
                                            type="checkbox" 
                                            name="technologies[]" 
                                            value="{{ $tech->id }}"
                                            class="w-4 h-4 text-violet-500 bg-[#09090b] border-white/20 rounded focus:ring-violet-500/50 focus:ring-offset-[#09090b]"
                                            @if(in_array($tech->id, $userTechnologies)) checked @endif
                                        >
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <span class="font-medium {{ in_array($tech->id, $userTechnologies) ? 'text-white' : 'text-zinc-300' }} group-hover:text-white transition-colors">{{ $tech->name }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <a href="{{ route('history') }}" class="text-sm text-zinc-400 hover:text-white transition-colors flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Scan History
                            </a>
                            <button type="submit" class="px-6 py-3 bg-white text-black font-semibold rounded-xl hover:bg-zinc-200 transition-all duration-200 text-sm">
                                {{ __('Save Preferences') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- CVE Activity Section --}}
            @if(isset($subscribedTechs) && $subscribedTechs->isNotEmpty())
            <div class="mt-6 space-y-4">
                <h3 class="text-lg font-bold text-white px-1">Recent CVE Activity <span class="text-sm font-normal text-zinc-500">(last 30 days)</span></h3>

                @foreach($subscribedTechs as $tech)
                    @php $stats = $cveStats[$tech->id] ?? ['total' => 0, 'critical' => 0, 'high' => 0, 'medium' => 0, 'low' => 0, 'recent' => collect()]; @endphp
                    @if($stats['total'] > 0)
                    <div class="glass-card rounded-2xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-violet-500/10 border border-violet-500/20 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-white">{{ $tech->name }}</h4>
                                    <p class="text-xs text-zinc-500">{{ $stats['total'] }} CVE(s) in the last 30 days</p>
                                </div>
                            </div>
                            <!-- Severity bar -->
                            <div class="hidden sm:flex items-center gap-1 text-xs">
                                @if($stats['critical'] > 0) <span class="px-2 py-0.5 rounded-full bg-red-600/20 text-red-400 border border-red-600/20">{{ $stats['critical'] }} Critical</span> @endif
                                @if($stats['high'] > 0) <span class="px-2 py-0.5 rounded-full bg-orange-500/20 text-orange-400 border border-orange-500/20">{{ $stats['high'] }} High</span> @endif
                                @if($stats['medium'] > 0) <span class="px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-400 border border-amber-500/20">{{ $stats['medium'] }} Medium</span> @endif
                                @if($stats['low'] > 0) <span class="px-2 py-0.5 rounded-full bg-blue-500/20 text-blue-400 border border-blue-500/20">{{ $stats['low'] }} Low</span> @endif
                            </div>
                        </div>

                        <div class="space-y-2">
                            @foreach($stats['recent'] as $vuln)
                            <div class="flex items-start gap-3 p-3 rounded-xl bg-white/2 border border-white/5">
                                @php
                                    $sev = strtoupper($vuln->severity ?? 'UNKNOWN');
                                    $sevColor = match($sev) {
                                        'CRITICAL' => 'text-red-400 bg-red-500/10 border-red-500/20',
                                        'HIGH'     => 'text-orange-400 bg-orange-500/10 border-orange-500/20',
                                        'MEDIUM'   => 'text-amber-400 bg-amber-500/10 border-amber-500/20',
                                        'LOW'      => 'text-blue-400 bg-blue-500/10 border-blue-500/20',
                                        default    => 'text-zinc-400 bg-zinc-500/10 border-zinc-500/20',
                                    };
                                @endphp
                                <span class="mt-0.5 px-1.5 py-0.5 rounded text-[10px] font-bold border shrink-0 {{ $sevColor }}">{{ $sev }}</span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-mono text-violet-400">{{ $vuln->cve_id }}</p>
                                    <p class="text-sm text-zinc-300 line-clamp-1 mt-0.5">{{ $vuln->title ?? $vuln->description }}</p>
                                </div>
                                <span class="text-xs text-zinc-600 shrink-0">{{ $vuln->published_at?->format('M j') }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
