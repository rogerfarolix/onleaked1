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
                        
                        <div class="flex justify-end">
                            <button type="submit" class="px-6 py-3 bg-white text-black font-semibold rounded-xl hover:bg-zinc-200 transition-all duration-200 text-sm">
                                {{ __('Save Preferences') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
