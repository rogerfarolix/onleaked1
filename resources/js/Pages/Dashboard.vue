<template>
  <AppLayout title="Dashboard">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-5xl mx-auto space-y-8">

        <!-- Tech subscriptions -->
        <div class="glass-card rounded-2xl p-6 md:p-8">
          <h2 class="text-xl font-bold text-white mb-1">Vulnerability Alerts</h2>
          <p class="text-zinc-500 text-sm mb-6">Select the technologies you use. We'll send you an alert when a new CVE is published.</p>

          <div v-if="flash?.status === 'technologies-updated'" class="mb-4 p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Your technology preferences have been saved.
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 mb-6">
            <label v-for="tech in technologies" :key="tech.id"
              class="relative flex items-center p-4 rounded-xl border cursor-pointer transition-all duration-200"
              :class="selected.includes(tech.id) ? 'border-violet-500 bg-violet-500/10' : 'border-white/10 bg-white/5 hover:border-violet-500/50'">
              <input type="checkbox" :value="tech.id" v-model="selected"
                class="w-4 h-4 text-violet-500 bg-transparent border-white/20 rounded focus:ring-violet-500/50">
              <span class="ml-3 text-sm font-medium" :class="selected.includes(tech.id) ? 'text-white' : 'text-zinc-300'">{{ tech.name }}</span>
            </label>
          </div>

          <div class="flex items-center justify-between">
            <Link :href="route('history')" class="text-sm text-zinc-500 hover:text-white transition-colors flex items-center gap-1.5">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              Scan History
            </Link>
            <button @click="save" :disabled="saving"
              class="px-6 py-2.5 bg-white text-black font-semibold rounded-xl hover:bg-zinc-200 transition-all text-sm disabled:opacity-60">
              {{ saving ? 'Saving…' : 'Save Preferences' }}
            </button>
          </div>
        </div>

        <!-- CVE Activity -->
        <div v-if="cveActivity.length" class="space-y-4">
          <h3 class="text-lg font-bold text-white px-1">Recent CVE Activity <span class="text-sm font-normal text-zinc-500">(last 30 days)</span></h3>
          <div v-for="item in cveActivity" :key="item.tech.id" class="glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-violet-500/10 border border-violet-500/20 flex items-center justify-center">
                  <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                </div>
                <div>
                  <h4 class="font-semibold text-white">{{ item.tech.name }}</h4>
                  <p class="text-xs text-zinc-500">{{ item.stats.total }} CVE(s) in the last 30 days</p>
                </div>
              </div>
              <div class="flex gap-1.5 flex-wrap">
                <span v-if="item.stats.critical" class="px-2 py-0.5 rounded-full text-xs bg-red-600/20 text-red-400 border border-red-600/20">{{ item.stats.critical }} Critical</span>
                <span v-if="item.stats.high" class="px-2 py-0.5 rounded-full text-xs bg-orange-500/20 text-orange-400 border border-orange-500/20">{{ item.stats.high }} High</span>
                <span v-if="item.stats.medium" class="px-2 py-0.5 rounded-full text-xs bg-amber-500/20 text-amber-400 border border-amber-500/20">{{ item.stats.medium }} Medium</span>
                <span v-if="item.stats.low" class="px-2 py-0.5 rounded-full text-xs bg-blue-500/20 text-blue-400 border border-blue-500/20">{{ item.stats.low }} Low</span>
              </div>
            </div>
            <div class="space-y-2">
              <div v-for="vuln in item.stats.recent" :key="vuln.id" class="flex items-start gap-3 p-3 rounded-xl bg-white/2 border border-white/5">
                <span class="mt-0.5 px-1.5 py-0.5 rounded text-[10px] font-bold border shrink-0"
                  :class="severityClass(vuln.severity)">{{ (vuln.severity || 'N/A').toUpperCase() }}</span>
                <div class="flex-1 min-w-0">
                  <p class="text-xs font-mono text-violet-400">{{ vuln.cve_id }}</p>
                  <p class="text-sm text-zinc-300 line-clamp-1 mt-0.5">{{ vuln.title || vuln.description }}</p>
                </div>
                <span class="text-xs text-zinc-600 shrink-0">{{ formatDate(vuln.published_at) }}</span>
              </div>
            </div>
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

const props = defineProps({
  technologies:     { type: Array, default: () => [] },
  userTechnologies: { type: Array, default: () => [] },
  subscribedTechs:  { type: Array, default: () => [] },
  cveStats:         { type: Object, default: () => ({}) },
})

const page    = usePage()
const flash   = computed(() => page.props.flash)
const selected = ref([...props.userTechnologies])
const saving  = ref(false)

const cveActivity = computed(() =>
  props.subscribedTechs
    .map(tech => ({ tech, stats: props.cveStats[tech.id] ?? { total: 0, recent: [] } }))
    .filter(item => item.stats.total > 0)
)

function save() {
  saving.value = true
  router.post('/technologies', { technologies: selected.value }, {
    onFinish: () => { saving.value = false }
  })
}

function severityClass(sev) {
  const s = (sev || '').toUpperCase()
  if (s === 'CRITICAL') return 'text-red-400 bg-red-500/10 border-red-500/20'
  if (s === 'HIGH')     return 'text-orange-400 bg-orange-500/10 border-orange-500/20'
  if (s === 'MEDIUM')   return 'text-amber-400 bg-amber-500/10 border-amber-500/20'
  return 'text-blue-400 bg-blue-500/10 border-blue-500/20'
}

function formatDate(d) {
  if (!d) return ''
  return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
}
</script>
