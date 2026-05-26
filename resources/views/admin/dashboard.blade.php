<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="glass-card rounded-2xl overflow-hidden shadow-xl">
                <div class="p-6 md:p-10">
                    <h3 class="text-2xl font-bold text-white mb-2">Platform Administration</h3>
                    <p class="text-zinc-400 mb-8">{{ __("Welcome to the administrative panel. Manage users, technologies, and system settings here.") }}</p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Stats Card 1 -->
                        <div class="bg-white/5 border border-white/10 rounded-xl p-6">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-12 h-12 rounded-lg bg-violet-500/10 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-zinc-400">Total Users</p>
                                    <h4 class="text-2xl font-bold text-white">{{ \App\Models\User::count() }}</h4>
                                </div>
                            </div>
                        </div>

                        <!-- Stats Card 2 -->
                        <div class="bg-white/5 border border-white/10 rounded-xl p-6">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-12 h-12 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-zinc-400">Monitored Technologies</p>
                                    <h4 class="text-2xl font-bold text-white">{{ \App\Models\Technology::count() }}</h4>
                                </div>
                            </div>
                        </div>

                        <!-- Stats Card 3 -->
                        <div class="bg-white/5 border border-white/10 rounded-xl p-6">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-12 h-12 rounded-lg bg-rose-500/10 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-zinc-400">Recent Alerts</p>
                                    <h4 class="text-2xl font-bold text-white">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8 bg-amber-500/10 border border-amber-500/20 rounded-xl p-4 flex items-start gap-4">
                        <svg class="w-6 h-6 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <h4 class="font-medium text-amber-400">Admin Modules Pending</h4>
                            <p class="text-sm text-zinc-400 mt-1">Full administrative CRUD panels for users and technologies will be deployed in a future phase.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
