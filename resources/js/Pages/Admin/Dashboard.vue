<template>
  <AppLayout title="Admin Dashboard">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-6xl mx-auto space-y-6">

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div class="glass-card rounded-2xl p-5">
            <p class="text-xs text-zinc-500 mb-1">Total Users</p>
            <p class="text-3xl font-bold text-white">{{ stats.total_users }}</p>
            <p class="text-xs text-zinc-600 mt-1">{{ stats.verified_users }} verified</p>
          </div>
          <div class="glass-card rounded-2xl p-5">
            <p class="text-xs text-zinc-500 mb-1">Technologies</p>
            <p class="text-3xl font-bold text-emerald-400">{{ stats.tech_count }}</p>
            <p class="text-xs text-zinc-600 mt-1">tracked for CVEs</p>
          </div>
          <div class="glass-card rounded-2xl p-5">
            <p class="text-xs text-zinc-500 mb-1">Scans Today</p>
            <p class="text-3xl font-bold text-violet-400">{{ stats.scans_today }}</p>
            <p class="text-xs text-zinc-600 mt-1">authenticated users</p>
          </div>
          <div class="glass-card rounded-2xl p-5">
            <p class="text-xs text-zinc-500 mb-1">Unverified</p>
            <p class="text-3xl font-bold text-amber-400">{{ stats.total_users - stats.verified_users }}</p>
            <p class="text-xs text-zinc-600 mt-1">email not confirmed</p>
          </div>
        </div>

        <!-- Quick nav -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <Link :href="route('admin.users.index')" class="glass-card rounded-2xl p-5 hover:border-violet-500/30 transition-colors group">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-violet-500/10 flex items-center justify-center group-hover:bg-violet-500/20 transition-colors">
                <svg class="w-5 h-5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
              </div>
              <div><p class="font-semibold text-white">Users</p><p class="text-xs text-zinc-500">Manage roles & access</p></div>
            </div>
          </Link>
          <Link :href="route('admin.technologies.index')" class="glass-card rounded-2xl p-5 hover:border-emerald-500/30 transition-colors group">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center group-hover:bg-emerald-500/20 transition-colors">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
              </div>
              <div><p class="font-semibold text-white">Technologies</p><p class="text-xs text-zinc-500">Add & remove tracked tech</p></div>
            </div>
          </Link>
          <Link :href="route('admin.logs.index')" class="glass-card rounded-2xl p-5 hover:border-amber-500/30 transition-colors group">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center group-hover:bg-amber-500/20 transition-colors">
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
              </div>
              <div><p class="font-semibold text-white">Audit Logs</p><p class="text-xs text-zinc-500">View admin actions</p></div>
            </div>
          </Link>
        </div>

        <!-- Recent logs -->
        <div class="glass-card rounded-2xl overflow-hidden">
          <div class="px-6 py-4 border-b border-white/5 flex items-center justify-between">
            <h3 class="font-semibold text-white">Recent Admin Activity</h3>
            <Link :href="route('admin.logs.index')" class="text-xs text-zinc-500 hover:text-white transition-colors">View all →</Link>
          </div>
          <div v-if="!recentLogs?.length" class="px-6 py-8 text-center text-zinc-600 text-sm">No admin activity yet.</div>
          <table v-else class="w-full text-sm">
            <thead class="text-left">
              <tr>
                <th class="px-6 py-3 text-zinc-500 font-medium text-xs">User</th>
                <th class="px-6 py-3 text-zinc-500 font-medium text-xs">Action</th>
                <th class="px-6 py-3 text-zinc-500 font-medium text-xs">IP</th>
                <th class="px-6 py-3 text-zinc-500 font-medium text-xs">Time</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/4">
              <tr v-for="log in recentLogs" :key="log.id">
                <td class="px-6 py-3 text-zinc-300 text-xs">{{ log.user?.email ?? '—' }}</td>
                <td class="px-6 py-3 text-zinc-400 font-mono text-xs">{{ log.action }}</td>
                <td class="px-6 py-3 text-zinc-500 text-xs">{{ log.ip_address }}</td>
                <td class="px-6 py-3 text-zinc-600 text-xs">{{ log.created_at }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({
  stats:      { type: Object, default: () => ({}) },
  recentLogs: { type: Array,  default: () => [] },
})
</script>
