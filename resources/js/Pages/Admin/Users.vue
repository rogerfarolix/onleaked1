<template>
  <AppLayout title="Utilisateurs">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-6xl mx-auto space-y-6">

        <div class="flex items-center gap-4 mb-2">
          <Link :href="route('admin.dashboard')" class="text-text-muted hover:text-white transition-colors">&larr;</Link>
          <h1 class="text-2xl font-bold text-white">Utilisateurs</h1>
        </div>

        <div v-if="flash?.success" class="p-4 rounded-md bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">{{ flash.success }}</div>
        <div v-if="flash?.error" class="p-4 rounded-md bg-red-500/10 border border-red-500/20 text-red-400 text-sm">{{ flash.error }}</div>

        <!-- Search -->
        <form @submit.prevent="search" class="flex gap-3">
          <input v-model="searchQuery" type="text" placeholder="Rechercher par e-mail ou nom…"
            class="flex-1 bg-white/5 border border-line rounded-md px-4 py-2.5 text-white text-sm placeholder-text-dim focus:outline-none focus:border-brand glow-input">
          <button type="submit" class="px-5 py-2.5 bg-brand text-white font-semibold rounded-md hover:bg-brand-bright transition-colors ring-1 ring-brand/40 text-sm">Rechercher</button>
          <Link v-if="searchQuery" :href="route('admin.users.index')" class="px-4 py-2.5 bg-white/5 border border-line text-text-muted text-sm rounded-md hover:bg-white/10 transition-colors">Effacer</Link>
        </form>

        <!-- Table -->
        <div class="glass-card rounded-lg overflow-hidden">
          <div class="px-6 py-4 border-b border-line">
            <h3 class="font-semibold text-white">{{ users.total }} utilisateur(s)</h3>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="text-left border-b border-line">
                <tr>
                  <th class="px-6 py-3 text-text-muted font-medium text-xs">Nom / E-mail</th>
                  <th class="px-6 py-3 text-text-muted font-medium text-xs">Rôle</th>
                  <th class="px-6 py-3 text-text-muted font-medium text-xs">Statut</th>
                  <th class="px-6 py-3 text-text-muted font-medium text-xs">Depuis</th>
                  <th class="px-6 py-3 text-text-muted font-medium text-xs text-right">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/4">
                <tr v-for="u in users.data" :key="u.id" class="hover:bg-white/2 transition-colors">
                  <td class="px-6 py-4">
                    <p class="text-white text-sm font-medium">{{ u.name }}</p>
                    <p class="text-text-dim text-xs">{{ u.email }}</p>
                  </td>
                  <td class="px-6 py-4">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                      :class="u.role === 'admin' ? 'bg-brand/10 text-brand-bright border border-brand/20' : 'bg-zinc-500/10 text-text-muted border border-zinc-500/20'">
                      {{ u.role === 'admin' ? 'Admin' : 'Utilisateur' }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <span v-if="u.suspended_at" class="px-2 py-0.5 rounded-full text-xs bg-red-500/10 text-red-400 border border-red-500/20">Suspendu</span>
                    <span v-else-if="u.email_verified_at" class="px-2 py-0.5 rounded-full text-xs bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Vérifié</span>
                    <span v-else class="px-2 py-0.5 rounded-full text-xs bg-amber-500/10 text-amber-400 border border-amber-500/20">Non vérifié</span>
                  </td>
                  <td class="px-6 py-4 text-text-dim text-xs">{{ formatDate(u.created_at) }}</td>
                  <td class="px-6 py-4 text-right">
                    <div v-if="u.id !== currentUserId" class="flex items-center justify-end gap-2">
                      <button @click="toggleRole(u.id)"
                        class="text-xs px-3 py-1.5 rounded-md bg-white/5 border border-line text-text-muted hover:text-white hover:bg-white/10 transition-colors">
                        {{ u.role === 'admin' ? 'Rétrograder' : 'Promouvoir' }}
                      </button>
                      <button @click="toggleSuspend(u.id, !!u.suspended_at)"
                        class="text-xs px-3 py-1.5 rounded-md transition-colors"
                        :class="u.suspended_at ? 'border border-emerald-500/20 text-emerald-400 hover:bg-emerald-500/10' : 'border border-red-500/20 text-red-400 hover:bg-red-500/10'">
                        {{ u.suspended_at ? 'Réactiver' : 'Suspendre' }}
                      </button>
                    </div>
                    <span v-else class="text-xs text-text-dim">Vous</span>
                  </td>
                </tr>
                <tr v-if="!users.data?.length">
                  <td colspan="5" class="px-6 py-10 text-center text-text-dim text-sm">Aucun utilisateur trouvé.</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-if="users.links?.length > 3" class="px-6 py-4 border-t border-line flex gap-1 flex-wrap">
            <component v-for="link in users.links" :key="link.label"
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

const props         = defineProps({ users: { type: Object, default: () => ({ data: [], total: 0, links: [] }) } })
const page          = usePage()
const flash         = computed(() => page.props.flash)
const currentUserId = computed(() => page.props.auth?.user?.id)
const searchQuery   = ref(page.props.ziggy?.query?.search ?? '')

const formatDate = (d) => d ? new Date(d).toLocaleDateString('fr-FR') : ''

function search() {
  router.get(route('admin.users.index'), { search: searchQuery.value || undefined }, { preserveState: true, replace: true })
}

function toggleRole(id) {
  router.post(route('admin.users.toggle-role', id))
}

function toggleSuspend(id, isSuspended) {
  if (!confirm(isSuspended ? 'Réactiver cet utilisateur ?' : 'Suspendre cet utilisateur ?')) return
  router.post(route('admin.users.suspend', id))
}
</script>
