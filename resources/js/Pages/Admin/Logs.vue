<template>
  <AppLayout title="Audit Logs">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-6xl mx-auto space-y-6">

        <div class="flex items-center gap-4 mb-2">
          <Link :href="route('admin.dashboard')" class="text-zinc-400 hover:text-white transition-colors">&larr;</Link>
          <h1 class="text-2xl font-bold text-white">Audit Logs</h1>
        </div>

        <!-- Filters -->
        <div class="glass-card rounded-2xl p-5">
          <form @submit.prevent="applyFilters" class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <input v-model="filters.user" type="text" placeholder="Filter by email"
              class="bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm placeholder-zinc-500 focus:outline-none focus:border-violet-500/50">
            <input v-model="filters.action" type="text" placeholder="Filter by action"
              class="bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm placeholder-zinc-500 focus:outline-none focus:border-violet-500/50">
            <input v-model="filters.ip" type="text" placeholder="Filter by IP"
              class="bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm placeholder-zinc-500 focus:outline-none focus:border-violet-500/50">
            <div class="flex gap-2">
              <button type="submit" class="flex-1 px-4 py-2.5 bg-violet-600 text-white text-sm font-semibold rounded-xl hover:bg-violet-500 transition-colors">Filter</button>
              <Link :href="route('admin.logs.index')" class="px-4 py-2.5 bg-white/5 border border-white/10 text-zinc-400 text-sm rounded-xl hover:bg-white/10 transition-colors">Reset</Link>
            </div>
          </form>
        </div>

        <!-- Table -->
        <div class="glass-card rounded-2xl overflow-hidden">
          <div class="px-6 py-4 border-b border-white/5 flex items-center justify-between">
            <h3 class="font-semibold text-white">{{ logs.total }} log(s)</h3>
            <a :href="exportUrl" class="text-xs px-3 py-1.5 rounded-lg bg-white/5 border border-white/10 text-zinc-400 hover:text-white transition-colors">
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
            <tbody class="divide-y divide-white/4">
              <tr v-for="log in logs.data" :key="log.id" class="hover:bg-white/2 transition-colors">
                <td class="px-6 py-3 text-zinc-300 text-xs">{{ log.user?.email ?? '—' }}</td>
                <td class="px-6 py-3 text-zinc-400 font-mono text-xs">{{ log.action }}</td>
                <td class="px-6 py-3 text-zinc-500 text-xs font-mono">{{ log.ip_address }}</td>
                <td class="px-6 py-3 text-zinc-600 text-xs">{{ log.created_at }}</td>
              </tr>
              <tr v-if="!logs.data?.length">
                <td colspan="4" class="px-6 py-10 text-center text-zinc-600 text-sm">No logs found.</td>
              </tr>
            </tbody>
          </table>
          <div v-if="logs.links?.length > 3" class="px-6 py-4 border-t border-white/5 flex gap-1 flex-wrap">
            <component v-for="link in logs.links" :key="link.label"
              :is="link.url ? Link : 'span'" :href="link.url" v-html="link.label"
              class="px-3 py-1.5 rounded-lg text-xs transition-colors"
              :class="link.active ? 'bg-violet-600 text-white' : link.url ? 'bg-white/5 text-zinc-400 hover:bg-white/10' : 'text-zinc-600 cursor-default'" />
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
