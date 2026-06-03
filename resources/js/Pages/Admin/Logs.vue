<template>
  <AppLayout title="Journaux d'audit">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-6xl mx-auto space-y-6">

        <div class="flex items-center gap-4 mb-2">
          <Link :href="route('admin.dashboard')" class="text-text-muted hover:text-white transition-colors">&larr;</Link>
          <h1 class="text-2xl font-bold text-white">Journaux d'audit</h1>
        </div>

        <!-- Filters -->
        <div class="glass-card rounded-2xl p-5">
          <form @submit.prevent="applyFilters" class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <input v-model="filters.user" type="text" placeholder="Filtrer par e-mail"
              class="bg-white/5 border border-line rounded-md px-4 py-2.5 text-white text-sm placeholder-text-dim focus:outline-none focus:border-brand glow-input">
            <input v-model="filters.action" type="text" placeholder="Filtrer par action"
              class="bg-white/5 border border-line rounded-md px-4 py-2.5 text-white text-sm placeholder-text-dim focus:outline-none focus:border-brand glow-input">
            <input v-model="filters.ip" type="text" placeholder="Filtrer par IP"
              class="bg-white/5 border border-line rounded-md px-4 py-2.5 text-white text-sm placeholder-text-dim focus:outline-none focus:border-brand glow-input">
            <div class="flex gap-2">
              <button type="submit" class="flex-1 px-4 py-2.5 bg-brand text-white text-sm font-semibold rounded-md hover:bg-brand-bright transition-colors ring-1 ring-brand/40">Filtrer</button>
              <Link :href="route('admin.logs.index')" class="px-4 py-2.5 bg-white/5 border border-line text-text-muted text-sm rounded-md hover:bg-white/10 transition-colors">Réinitialiser</Link>
            </div>
          </form>
        </div>

        <!-- Table -->
        <div class="glass-card rounded-2xl overflow-hidden">
          <div class="px-6 py-4 border-b border-line flex items-center justify-between">
            <h3 class="font-semibold text-white">{{ logs.total }} entrée(s)</h3>
            <a :href="exportUrl" class="text-xs px-3 py-1.5 rounded-md bg-white/5 border border-line text-text-muted hover:text-white transition-colors">
              Exporter en CSV
            </a>
          </div>
          <table class="w-full text-sm">
            <thead class="text-left border-b border-line">
              <tr>
                <th class="px-6 py-3 text-text-muted font-medium text-xs">Utilisateur</th>
                <th class="px-6 py-3 text-text-muted font-medium text-xs">Action</th>
                <th class="px-6 py-3 text-text-muted font-medium text-xs">IP</th>
                <th class="px-6 py-3 text-text-muted font-medium text-xs">Date</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/4">
              <tr v-for="log in logs.data" :key="log.id" class="hover:bg-white/2 transition-colors">
                <td class="px-6 py-3 text-text text-xs">{{ log.user?.email ?? '—' }}</td>
                <td class="px-6 py-3 text-text-muted font-mono text-xs">{{ log.action }}</td>
                <td class="px-6 py-3 text-text-dim text-xs font-mono">{{ log.ip_address }}</td>
                <td class="px-6 py-3 text-text-dim text-xs">{{ log.created_at }}</td>
              </tr>
              <tr v-if="!logs.data?.length">
                <td colspan="4" class="px-6 py-10 text-center text-text-dim text-sm">Aucune entrée trouvée.</td>
              </tr>
            </tbody>
          </table>
          <div v-if="logs.links?.length > 3" class="px-6 py-4 border-t border-line flex gap-1 flex-wrap">
            <component v-for="link in logs.links" :key="link.label"
              :is="link.url ? Link : 'span'" :href="link.url" v-html="link.label"
              class="px-3 py-1.5 rounded-lg text-xs transition-colors"
              :class="link.active ? 'bg-brand text-white' : link.url ? 'bg-white/5 text-text-muted hover:bg-white/10' : 'text-text-dim cursor-default'" />
          </div>
        </div>

      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props   = defineProps({ logs: { type: Object, default: () => ({ data: [], total: 0, links: [] }) } })
const page    = usePage()
const filters = ref({
  user:   page.props.ziggy?.query?.user ?? '',
  action: page.props.ziggy?.query?.action ?? '',
  ip:     page.props.ziggy?.query?.ip ?? '',
})

const exportUrl = computed(() => {
  const params = new URLSearchParams()
  if (filters.value.user)   params.set('user', filters.value.user)
  if (filters.value.action) params.set('action', filters.value.action)
  if (filters.value.ip)     params.set('ip', filters.value.ip)
  return `/admin/logs/export?${params.toString()}`
})

function applyFilters() {
  router.get(route('admin.logs.index'), {
    user: filters.value.user || undefined,
    action: filters.value.action || undefined,
    ip: filters.value.ip || undefined,
  }, { preserveState: true, replace: true })
}
</script>
