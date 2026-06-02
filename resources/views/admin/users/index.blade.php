<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.dashboard') }}" class="text-zinc-400 hover:text-white transition-colors">&larr;</a>
            <h2 class="font-semibold text-xl text-white">Users</h2>
        </div>
    </x-slot>

    <div class="py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto space-y-6">

            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">{{ session('error') }}</div>
            @endif

            <!-- Search -->
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by email or name…"
                    class="flex-1 rounded-xl border border-white/10 bg-white/5 text-white text-sm px-4 py-2.5 placeholder-zinc-500 focus:outline-none focus:border-violet-500/50">
                <button type="submit" class="px-5 py-2.5 bg-violet-600 text-white text-sm font-semibold rounded-xl hover:bg-violet-500 transition-colors">Search</button>
                @if(request('search'))
                    <a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 bg-white/5 border border-white/10 text-zinc-400 text-sm rounded-xl hover:bg-white/10 transition-colors">Clear</a>
                @endif
            </form>

            <!-- Users Table -->
            <div class="glass-card rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/5">
                    <h3 class="font-semibold text-white">{{ $users->total() }} user(s)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-left border-b border-white/5">
                            <tr>
                                <th class="px-6 py-3 text-zinc-400 font-medium text-xs">Name / Email</th>
                                <th class="px-6 py-3 text-zinc-400 font-medium text-xs">Role</th>
                                <th class="px-6 py-3 text-zinc-400 font-medium text-xs">Status</th>
                                <th class="px-6 py-3 text-zinc-400 font-medium text-xs">Member since</th>
                                <th class="px-6 py-3 text-zinc-400 font-medium text-xs text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($users as $user)
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="px-6 py-4">
                                    <p class="text-white text-sm font-medium">{{ $user->name }}</p>
                                    <p class="text-zinc-500 text-xs">{{ $user->email }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $user->role === 'admin' ? 'bg-violet-500/10 text-violet-400 border border-violet-500/20' : 'bg-zinc-500/10 text-zinc-400 border border-zinc-500/20' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->isSuspended())
                                        <span class="px-2 py-0.5 rounded-full text-xs bg-red-500/10 text-red-400 border border-red-500/20">Suspended</span>
                                    @elseif($user->email_verified_at)
                                        <span class="px-2 py-0.5 rounded-full text-xs bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Verified</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-xs bg-amber-500/10 text-amber-400 border border-amber-500/20">Unverified</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-zinc-500 text-xs">{{ $user->created_at->format('Y-m-d') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.toggle-role', $user->id) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-xs px-3 py-1.5 rounded-lg bg-white/5 border border-white/10 text-zinc-400 hover:text-white hover:bg-white/10 transition-colors">
                                                    {{ $user->role === 'admin' ? 'Demote' : 'Promote' }}
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.users.suspend', $user->id) }}" class="inline"
                                                  onsubmit="return confirm('{{ $user->isSuspended() ? 'Unsuspend' : 'Suspend' }} this user?')">
                                                @csrf
                                                <button type="submit" class="text-xs px-3 py-1.5 rounded-lg {{ $user->isSuspended() ? 'border border-emerald-500/20 text-emerald-400 hover:bg-emerald-500/10' : 'border border-red-500/20 text-red-400 hover:bg-red-500/10' }} transition-colors">
                                                    {{ $user->isSuspended() ? 'Unsuspend' : 'Suspend' }}
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-zinc-600">You</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-6 py-10 text-center text-zinc-600 text-sm">No users found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($users->hasPages())
                    <div class="px-6 py-4 border-t border-white/5">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
