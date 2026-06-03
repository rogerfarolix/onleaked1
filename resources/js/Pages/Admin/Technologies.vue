<template>
  <AppLayout title="Technologies">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-4xl mx-auto space-y-6">

        <div class="flex items-center gap-4 mb-2">
          <Link :href="route('admin.dashboard')" class="text-text-muted hover:text-white transition-colors">&larr;</Link>
          <h1 class="text-2xl font-bold text-white">Technologies</h1>
        </div>

        <div v-if="flash?.success" class="p-4 rounded-md bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">{{ flash.success }}</div>

        <!-- Add form -->
        <div class="glass-card rounded-lg p-6">
          <h3 class="font-semibold text-white mb-4">Ajouter une technologie</h3>
          <form @submit.prevent="addForm.post(route('admin.technologies.store'), { onSuccess: () => addForm.reset() })" class="flex gap-3">
            <input v-model="addForm.name" type="text" placeholder="ex. Apache 2.4" required maxlength="100"
              class="flex-1 bg-white/5 border border-line rounded-md px-4 py-2.5 text-white text-sm placeholder-text-dim focus:outline-none focus:border-brand glow-input"
              :class="{ 'border-red-500/50': addForm.errors.name }">
            <button type="submit" :disabled="addForm.processing"
              class="px-5 py-2.5 bg-brand text-white font-semibold rounded-md hover:bg-brand-bright transition-colors ring-1 ring-brand/40 text-sm disabled:opacity-60">Ajouter</button>
          </form>
          <p v-if="addForm.errors.name" class="mt-2 text-xs text-red-400">{{ addForm.errors.name }}</p>
        </div>

        <!-- Table -->
        <div class="glass-card rounded-lg overflow-hidden">
          <div class="px-6 py-4 border-b border-line">
            <h3 class="font-semibold text-white">Toutes les technologies ({{ technologies.total }})</h3>
          </div>
          <table class="w-full text-sm">
            <thead class="text-left border-b border-line">
              <tr>
                <th class="px-6 py-3 text-text-muted font-medium text-xs">Nom</th>
                <th class="px-6 py-3 text-text-muted font-medium text-xs text-center">Utilisateurs</th>
                <th class="px-6 py-3 text-text-muted font-medium text-xs text-center">CVE</th>
                <th class="px-6 py-3 text-text-muted font-medium text-xs text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/4">
              <tr v-for="tech in technologies.data" :key="tech.id" class="hover:bg-white/2 transition-colors">
                <td class="px-6 py-4 text-white font-medium">{{ tech.name }}</td>
                <td class="px-6 py-4 text-text-muted text-center">{{ tech.users_count }}</td>
                <td class="px-6 py-4 text-text-muted text-center">{{ tech.vulnerabilities_count }}</td>
                <td class="px-6 py-4 text-right">
                  <button @click="deleteTech(tech.id, tech.name)"
                    class="text-xs px-3 py-1.5 rounded-md border border-red-500/20 text-red-400 hover:bg-red-500/10 transition-colors">Supprimer</button>
                </td>
              </tr>
            </tbody>
          </table>
          <div v-if="technologies.links?.length > 3" class="px-6 py-4 border-t border-line flex gap-1">
            <component v-for="link in technologies.links" :key="link.label"
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
import { computed } from 'vue'
import { Link, useForm, usePage, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({ technologies: { type: Object, default: () => ({ data: [], total: 0, links: [] }) } })
const page  = usePage()
const flash = computed(() => page.props.flash)
const addForm = useForm({ name: '' })

function deleteTech(id, name) {
  if (!confirm(`Supprimer « ${name} » ? Cela retirera également toutes ses CVE.`)) return
  router.delete(route('admin.technologies.destroy', id))
}
</script>
