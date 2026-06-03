<template>
  <AppLayout title="Tableau de bord administrateur">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-6xl mx-auto space-y-6">

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div class="glass-card rounded-2xl p-5">
            <p class="text-xs text-text-dim mb-1">Utilisateurs</p>
            <p class="text-3xl font-bold text-white font-mono">{{ stats.total_users }}</p>
            <p class="text-xs text-text-dim mt-1">{{ stats.verified_users }} vérifiés</p>
          </div>
          <div class="glass-card rounded-2xl p-5">
            <p class="text-xs text-text-dim mb-1">Technologies</p>
            <p class="text-3xl font-bold text-brand-bright font-mono">{{ stats.tech_count }}</p>
            <p class="text-xs text-text-dim mt-1">suivies pour les CVE</p>
          </div>
          <div class="glass-card rounded-2xl p-5">
            <p class="text-xs text-text-dim mb-1">Scans aujourd'hui</p>
            <p class="text-3xl font-bold text-brand-bright font-mono">{{ stats.scans_today }}</p>
            <p class="text-xs text-text-dim mt-1">utilisateurs authentifiés</p>
          </div>
          <div class="glass-card rounded-2xl p-5">
            <p class="text-xs text-text-dim mb-1">Non vérifiés</p>
            <p class="text-3xl font-bold text-amber-400 font-mono">{{ stats.total_users - stats.verified_users }}</p>
            <p class="text-xs text-text-dim mt-1">e-mail non confirmé</p>
          </div>
        </div>

        <!-- Quick nav -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <Link :href="route('admin.users.index')" class="glass-card rounded-2xl p-5 hover:border-brand/30 transition-colors group">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-md bg-brand/10 border border-brand/25 flex items-center justify-center group-hover:bg-brand/20 transition-colors">
                <svg class="w-5 h-5 text-brand-bright" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
              </div>
              <div><p class="font-semibold text-white">Utilisateurs</p><p class="text-xs text-text-dim">Gérer les rôles & accès</p></div>
            </div>
          </Link>
          <Link :href="route('admin.technologies.index')" class="glass-card rounded-2xl p-5 hover:border-brand/30 transition-colors group">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-md bg-brand/10 border border-brand/25 flex items-center justify-center group-hover:bg-brand/20 transition-colors">
                <svg class="w-5 h-5 text-brand-bright" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
              </div>
              <div><p class="font-semibold text-white">Technologies</p><p class="text-xs text-text-dim">Ajouter & retirer des technos suivies</p></div>
            </div>
          </Link>
          <Link :href="route('admin.logs.index')" class="glass-card rounded-2xl p-5 hover:border-brand/30 transition-colors group">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-md bg-brand/10 border border-brand/25 flex items-center justify-center group-hover:bg-brand/20 transition-colors">
                <svg class="w-5 h-5 text-brand-bright" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
              </div>
              <div><p class="font-semibold text-white">Journaux d'audit</p><p class="text-xs text-text-dim">Consulter les actions admin</p></div>
            </div>
          </Link>
        </div>

        <!-- Recent logs -->
        <div class="glass-card rounded-2xl overflow-hidden">
          <div class="px-6 py-4 border-b border-line flex items-center justify-between">
            <h3 class="font-semibold text-white">Activité admin récente</h3>
            <Link :href="route('admin.logs.index')" class="text-xs text-text-dim hover:text-white transition-colors">Tout voir →</Link>
          </div>
          <div v-if="!recentLogs?.length" class="px-6 py-8 text-center text-text-dim text-sm">Aucune activité admin pour l'instant.</div>
          <table v-else class="w-full text-sm">
            <thead class="text-left">
              <tr>
                <th class="px-6 py-3 text-text-dim font-medium text-xs">Utilisateur</th>
                <th class="px-6 py-3 text-text-dim font-medium text-xs">Action</th>
                <th class="px-6 py-3 text-text-dim font-medium text-xs">IP</th>
                <th class="px-6 py-3 text-text-dim font-medium text-xs">Heure</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/4">
              <tr v-for="log in recentLogs" :key="log.id">
                <td class="px-6 py-3 text-text text-xs">{{ log.user?.email ?? '—' }}</td>
                <td class="px-6 py-3 text-text-muted font-mono text-xs">{{ log.action }}</td>
                <td class="px-6 py-3 text-text-dim text-xs">{{ log.ip_address }}</td>
                <td class="px-6 py-3 text-text-dim text-xs">{{ log.created_at }}</td>
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
