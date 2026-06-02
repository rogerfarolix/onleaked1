@extends('layouts.app')

@section('content')
<div class="py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">

        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-white">Scan History</h1>
                <p class="text-zinc-500 text-sm mt-1">Your authenticated scans from all tools</p>
            </div>
            <a href="{{ route('dashboard') }}" class="text-sm text-zinc-500 hover:text-white transition-colors">&larr; Dashboard</a>
        </div>

        @if($history->isEmpty())
            <div class="glass-card rounded-2xl p-16 text-center">
                <svg class="w-10 h-10 text-zinc-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-zinc-400 font-medium mb-1">No scans yet</p>
                <p class="text-zinc-600 text-sm">Use any tool while logged in and your history will appear here.</p>
            </div>
        @else
            <div class="glass-card rounded-2xl overflow-hidden" x-data="historyModal()">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-white/5">
                            <th class="px-6 py-3.5 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3.5 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Tool</th>
                            <th class="px-6 py-3.5 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Target</th>
                            <th class="px-6 py-3.5 text-right text-xs font-medium text-zinc-500 uppercase tracking-wider"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/4">
                        @foreach($history as $scan)
                        @php
                            $types = [
                                'leak_check' => ['Leak Check',  'rose'],
                                'domain'     => ['Domain',      'amber'],
                                'password'   => ['Password',    'cyan'],
                                'ssl'        => ['SSL',         'sky'],
                                'ip'         => ['IP Rep.',     'indigo'],
                                'header'     => ['Headers',     'teal'],
                            ];
                            [$label, $color] = $types[$scan->scan_type] ?? [$scan->scan_type, 'zinc'];
                        @endphp
                        <tr class="hover:bg-white/2 transition-colors">
                            <td class="px-6 py-4 text-xs text-zinc-500 font-mono whitespace-nowrap">{{ $scan->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $color }}-500/10 text-{{ $color }}-400 border border-{{ $color }}-500/20">
                                    {{ $label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-300 font-mono truncate max-w-xs">{{ $scan->target }}</td>
                            <td class="px-6 py-4 text-right">
                                <button @click="open({{ $scan->id }})"
                                    class="text-xs text-zinc-500 hover:text-white transition-colors">
                                    View →
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($history->hasPages())
                    <div class="px-6 py-4 border-t border-white/5">
                        {{ $history->links() }}
                    </div>
                @endif

                <!-- Modal -->
                <div x-show="show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="show=false">
                    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="show=false"></div>
                    <div class="relative max-w-2xl w-full max-h-[80vh] overflow-y-auto rounded-2xl border border-white/10 bg-[#0c0c0f] p-6 shadow-2xl">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold text-white text-sm">Scan Details</h3>
                            <button @click="show=false" class="text-zinc-600 hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <template x-if="loading">
                            <div class="flex justify-center py-8">
                                <svg class="w-5 h-5 animate-spin text-zinc-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            </div>
                        </template>
                        <template x-if="!loading && data">
                            <pre class="text-xs text-zinc-400 overflow-x-auto font-mono bg-black/30 rounded-xl p-4 whitespace-pre-wrap" x-text="JSON.stringify(data.results, null, 2)"></pre>
                        </template>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce }}">
function historyModal() {
    return {
        show: false,
        loading: false,
        data: null,
        async open(id) {
            this.show = true; this.loading = true; this.data = null;
            try {
                const res = await fetch(`/history/${id}`, { headers: { 'Accept': 'application/json' } });
                this.data = await res.json();
            } catch {}
            finally { this.loading = false; }
        }
    }
}
</script>
@endpush
