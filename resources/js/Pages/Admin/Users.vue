<template>
  <AppLayout title="Users">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-6xl mx-auto space-y-6">

        <div class="flex items-center gap-4 mb-2">
          <Link :href="route('admin.dashboard')" class="text-zinc-400 hover:text-white transition-colors">&larr;</Link>
          <h1 class="text-2xl font-bold text-white">Users</h1>
        </div>

        <div v-if="flash?.success" class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">{{ flash.success }}</div>
        <div v-if="flash?.error" class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">{{ flash.error }}</div>

        <!-- Search -->
        <form @submit.prevent="search" class="flex gap-3">
          <input v-model="searchQuery" type="text" placeholder="Search by email or name…"
            class="flex-1 bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm placeholder-zinc-500 focus:outline-none focus:border-violet-500/50">
          <button type="submit" class="px-5 py-2.5 bg-violet-600 text-white font-semibold rounded-xl hover:bg-violet-500 transition-colors text-sm">Search</button>
          <Link v-if="searchQuery" :href="route('admin.users.index')" class="px-4 py-2.5 bg-white/5 border border-white/10 text-zinc-400 text-sm rounded-xl hover:bg-white/10 transition-colors">Clear</Link>
        </form>

        <!-- Table -->
        <div class="glass-card rounded-2xl overflow-hidden">
          <div class="px-6 py-4 border-b border-white/5">
            <h3 class="font-semibold text-white">{{ users.total }} user(s)</h3>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="text-left border-b border-white/5">
                <tr>
                  <th class="px-6 py-3 text-zinc-400 font-medium text-xs">Name / Email</th>
                  <th class="px-6 py-3 text-zinc-400 font-medium text-xs">Role</th>
                  <th class="px-6 py-3 text-zinc-400 font-medium text-xs">Status</th>
                  <th class="px-6 py-3 text-zinc-400 font-medium text-xs">Since</th>
                  <th class="px-6 py-3 text-zinc-400 font-medium text-xs text-right">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/4">
                <tr v-for="u in users.data" :key="u.id" class="hover:bg-white/2 transition-colors">
                  <td class="px-6 py-4">
                    <p class="text-white text-sm font-medium">{{ u.name }}</p>
                    <p class="text-zinc-500 text-xs">{{ u.email }}</p>
                  </td>
                  <td class="px-6 py-4">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                      :class="u.role === 'admin' ? 'bg-violet-500/10 text-violet-400 border border-violet-500/20' : 'bg-zinc-500/10 text-zinc-400 border border-zinc-500/20'">
                      {{ u.role }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <span v-if="u.suspended_at" class="px-2 py-0.5 rounded-full text-xs bg-red-500/10 text-red-400 border border-red-500/20">Suspended</span>
                    <span v-else-if="u.email_verified_at" class="px-2 py-0.5 rounded-full text-xs bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Verified</span>
                    <span v-else class="px-2 py-0.5 rounded-full text-xs bg-amber-500/10 text-amber-400 border border-amber-500/20">Unverified</span>
                  </td>
                  <td class="px-6 py-4 text-zinc-500 text-xs">{{ formatDate(u.created_at) }}</td>
                  <td class="px-6 py-4 text-right">
                    <div v-if="u.id !== currentUserId" class="flex items-center justify-end gap-2">
                      <button @click="toggleRole(u.id)"
                        class="text-xs px-3 py-1.5 rounded-lg bg-white/5 border border-white/10 text-zinc-400 hover:text-white hover:bg-white/10 transition-colors">
                        {{ u.role === 'admin' ? 'Demote' : 'Promote' }}
                      </button>
                      <button @click="toggleSuspend(u.id, !!u.suspended_at)"
                        class="text-xs px-3 py-1.5 rounded-lg transition-colors"
                        :class="u.suspended_at ? 'border border-emerald-500/20 text-emerald-400 hover:bg-emerald-500/10' : 'border border-red-500/20 text-red-400 hover:bg-red-500/10'">
                        {{ u.suspended_at ? 'Unsuspend' : 'Suspend' }}
                      </button>
                    </div>
                    <span v-else class="text-xs text-zinc-600">You</span>
                  </td>
                </tr>
                <tr v-if="!users.data?.length">
                  <td colspan="5" class="px-6 py-10 text-center text-zinc-600 text-sm">No users found.</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-if="users.links?.length > 3" class="px-6 py-4 border-t border-white/5 flex gap-1 flex-wrap">
            <component v-for="link in users.links" :key="link.label"
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

const props         = defineProps({ users: { type: Object, default: () => ({ data: [], total: 0, links: [] }) } })
const page          = usePage()
const flash         = computed(() => page.props.flash)
const currentUserId = computed(() => page.props.auth?.user?.id)
const searchQuery   = ref(page.props.ziggy?.query?.search ?? '')

const formatDate = (d) => d ? new Date(d).toLocaleDateString() : ''

function search() {
  router.get(route('admin.users.index'), { search: searchQuery.value || undefined }, { preserveState: true, replace: true })
}

function toggleRole(id) {
  router.post(route('admin.users.toggle-role', id))
}

function toggleSuspend(id, isSuspended) {
  if (!confirm(isSuspended ? 'Unsuspend this user?' : 'Suspend this user?')) return
  router.post(route('admin.users.suspend', id))
}
</script>
