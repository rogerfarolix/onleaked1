<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.dashboard') }}" class="text-zinc-400 hover:text-white transition-colors">&larr;</a>
            <h2 class="font-semibold text-xl text-white">Technologies</h2>
        </div>
    </x-slot>

    <div class="py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto space-y-6">

            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">{{ session('success') }}</div>
            @endif

            <!-- Add technology form -->
            <div class="glass-card rounded-2xl p-6">
                <h3 class="font-semibold text-white mb-4">Add Technology</h3>
                <form method="POST" action="{{ route('admin.technologies.store') }}" class="flex gap-3">
                    @csrf
                    <input type="text" name="name" placeholder="e.g. Apache 2.4"
                        class="flex-1 rounded-xl border border-white/10 bg-white/5 text-white text-sm px-4 py-2.5 placeholder-zinc-500 focus:outline-none focus:border-violet-500/50"
                        required maxlength="100">
                    <button type="submit"
                        class="px-5 py-2.5 bg-violet-600 text-white text-sm font-semibold rounded-xl hover:bg-violet-500 transition-colors">
                        Add
                    </button>
                </form>
                @error('name')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Technologies table -->
            <div class="glass-card rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/5">
                    <h3 class="font-semibold text-white">All Technologies ({{ $technologies->total() }})</h3>
                </div>
                <table class="w-full text-sm">
                    <thead class="text-left border-b border-white/5">
                        <tr>
                            <th class="px-6 py-3 text-zinc-400 font-medium">Name</th>
                            <th class="px-6 py-3 text-zinc-400 font-medium text-center">Users</th>
                            <th class="px-6 py-3 text-zinc-400 font-medium text-center">CVEs</th>
                            <th class="px-6 py-3 text-zinc-400 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($technologies as $tech)
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="px-6 py-4 text-white font-medium">{{ $tech->name }}</td>
                            <td class="px-6 py-4 text-zinc-400 text-center">{{ $tech->users_count }}</td>
                            <td class="px-6 py-4 text-zinc-400 text-center">{{ $tech->vulnerabilities_count }}</td>
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('admin.technologies.destroy', $tech) }}"
                                      onsubmit="return confirm('Delete {{ $tech->name }}? This will also remove all its CVEs.')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="text-xs px-3 py-1.5 rounded-lg border border-red-500/20 text-red-400 hover:bg-red-500/10 transition-colors">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($technologies->hasPages())
                    <div class="px-6 py-4 border-t border-white/5">
                        {{ $technologies->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
