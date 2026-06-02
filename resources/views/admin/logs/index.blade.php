<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.dashboard') }}" class="text-zinc-400 hover:text-white transition-colors">&larr;</a>
            <h2 class="font-semibold text-xl text-white">Audit Logs</h2>
        </div>
    </x-slot>

    <div class="py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto space-y-6">

            <!-- Filters -->
            <div class="glass-card rounded-2xl p-5">
                <form method="GET" action="{{ route('admin.logs.index') }}" class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <input type="text" name="user" value="{{ request('user') }}" placeholder="Filter by email"
                        class="rounded-xl border border-white/10 bg-white/5 text-white text-sm px-4 py-2.5 placeholder-zinc-500 focus:outline-none focus:border-violet-500/50">
                    <input type="text" name="action" value="{{ request('action') }}" placeholder="Filter by action"
                        class="rounded-xl border border-white/10 bg-white/5 text-white text-sm px-4 py-2.5 placeholder-zinc-500 focus:outline-none focus:border-violet-500/50">
                    <input type="text" name="ip" value="{{ request('ip') }}" placeholder="Filter by IP"
                        class="rounded-xl border border-white/10 bg-white/5 text-white text-sm px-4 py-2.5 placeholder-zinc-500 focus:outline-none focus:border-violet-500/50">
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-violet-600 text-white text-sm font-semibold rounded-xl hover:bg-violet-500 transition-colors">Filter</button>
                        <a href="{{ route('admin.logs.index') }}" class="px-4 py-2.5 bg-white/5 border border-white/10 text-zinc-400 text-sm rounded-xl hover:bg-white/10 transition-colors">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Logs Table -->
            <div class="glass-card rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/5 flex items-center justify-between">
                    <h3 class="font-semibold text-white">{{ $logs->total() }} log(s)</h3>
                    <a href="{{ route('admin.logs.export', request()->query()) }}"
                        class="text-xs px-3 py-1.5 rounded-lg bg-white/5 border border-white/10 text-zinc-400 hover:text-white transition-colors">
                        Export CSV
                    </a>
                </div>
                <table class="w-full text-sm">
                    <thead class="text-left border-b border-white/5">
                        <tr>
                            <th class="px-6 py-3 text-zinc-400 font-medium text-xs">User</th>
                            <th class="px-6 py-3 text-zinc-400 font-medium text-xs">Action</th>
                            <th class="px-6 py-3 text-zinc-400 font-medium text-xs">IP</th>
                            <th class="px-6 py-3 text-zinc-400 font-medium text-xs">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($logs as $log)
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="px-6 py-3 text-zinc-300 text-xs">{{ $log->user?->email ?? '—' }}</td>
                            <td class="px-6 py-3 text-zinc-400 font-mono text-xs">{{ $log->action }}</td>
                            <td class="px-6 py-3 text-zinc-500 text-xs font-mono">{{ $log->ip_address }}</td>
                            <td class="px-6 py-3 text-zinc-600 text-xs">{{ $log->created_at?->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-6 py-10 text-center text-zinc-600 text-sm">No logs found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @if($logs->hasPages())
                    <div class="px-6 py-4 border-t border-white/5">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
