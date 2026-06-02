<template>
  <AppLayout title="Technologies">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-4xl mx-auto space-y-6">

        <div class="flex items-center gap-4 mb-2">
          <Link :href="route('admin.dashboard')" class="text-zinc-400 hover:text-white transition-colors">&larr;</Link>
          <h1 class="text-2xl font-bold text-white">Technologies</h1>
        </div>

        <div v-if="flash?.success" class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">{{ flash.success }}</div>

        <!-- Add form -->
        <div class="glass-card rounded-2xl p-6">
          <h3 class="font-semibold text-white mb-4">Add Technology</h3>
          <form @submit.prevent="addForm.post(route('admin.technologies.store'), { onSuccess: () => addForm.reset() })" class="flex gap-3">
            <input v-model="addForm.name" type="text" placeholder="e.g. Apache 2.4" required maxlength="100"
              class="flex-1 bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm placeholder-zinc-500 focus:outline-none focus:border-violet-500/50"
              :class="{ 'border-red-500/50': addForm.errors.name }">
            <button type="submit" :disabled="addForm.processing"
              class="px-5 py-2.5 bg-violet-600 text-white font-semibold rounded-xl hover:bg-violet-500 transition-colors text-sm disabled:opacity-60">Add</button>
          </form>
          <p v-if="addForm.errors.name" class="mt-2 text-xs text-red-400">{{ addForm.errors.name }}</p>
        </div>

        <!-- Table -->
        <div class="glass-card rounded-2xl overflow-hidden">
          <div class="px-6 py-4 border-b border-white/5">
            <h3 class="font-semibold text-white">All Technologies ({{ technologies.total }})</h3>
          </div>
          <table class="w-full text-sm">
            <thead class="text-left border-b border-white/5">
              <tr>
                <th class="px-6 py-3 text-zinc-400 font-medium text-xs">Name</th>
                <th class="px-6 py-3 text-zinc-400 font-medium text-xs text-center">Users</th>
                <th class="px-6 py-3 text-zinc-400 font-medium text-xs text-center">CVEs</th>
                <th class="px-6 py-3 text-zinc-400 font-medium text-xs text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/4">
              <tr v-for="tech in technologies.data" :key="tech.id" class="hover:bg-white/2 transition-colors">
                <td class="px-6 py-4 text-white font-medium">{{ tech.name }}</td>
                <td class="px-6 py-4 text-zinc-400 text-center">{{ tech.users_count }}</td>
                <td class="px-6 py-4 text-zinc-400 text-center">{{ tech.vulnerabilities_count }}</td>
                <td class="px-6 py-4 text-right">
                  <button @click="deleteTech(tech.id, tech.name)"
                    class="text-xs px-3 py-1.5 rounded-lg border border-red-500/20 text-red-400 hover:bg-red-500/10 transition-colors">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
          <div v-if="technologies.links?.length > 3" class="px-6 py-4 border-t border-white/5 flex gap-1">
            <component v-for="link in technologies.links" :key="link.label"
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
import { computed } from 'vue'
import { Link, useForm, usePage, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({ technologies: { type: Object, default: () => ({ data: [], total: 0, links: [] }) } })
const page  = usePage()
const flash = computed(() => page.props.flash)
const addForm = useForm({ name: '' })

function deleteTech(id, name) {
  if (!confirm(`Delete "${name}"? This will also remove all its CVEs.`)) return
  router.delete(route('admin.technologies.destroy', id))
}
</script>
