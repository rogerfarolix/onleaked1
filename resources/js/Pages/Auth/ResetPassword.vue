<template>
  <GuestLayout title="Nouveau mot de passe">
    <h2 class="text-xl font-bold text-white text-center mb-6">Définir un nouveau mot de passe</h2>

    <form @submit.prevent="form.post('/reset-password', { onFinish: () => form.reset('password', 'password_confirmation') })" class="space-y-4">
      <div>
        <label class="block text-sm text-text-muted mb-1.5">E-mail</label>
        <input v-model="form.email" type="email" required
          class="w-full bg-white/5 border border-line rounded-md px-4 py-2.5 text-white text-sm focus:outline-none focus:border-brand glow-input"
          :class="{ 'border-red-500/50': form.errors.email }">
        <p v-if="form.errors.email" class="mt-1 text-xs text-red-400">{{ form.errors.email }}</p>
      </div>
      <div>
        <label class="block text-sm text-text-muted mb-1.5">Nouveau mot de passe</label>
        <input v-model="form.password" type="password" required autofocus
          class="w-full bg-white/5 border border-line rounded-md px-4 py-2.5 text-white text-sm focus:outline-none focus:border-brand glow-input"
          :class="{ 'border-red-500/50': form.errors.password }">
        <p v-if="form.errors.password" class="mt-1 text-xs text-red-400">{{ form.errors.password }}</p>
      </div>
      <div>
        <label class="block text-sm text-text-muted mb-1.5">Confirmer le mot de passe</label>
        <input v-model="form.password_confirmation" type="password" required
          class="w-full bg-white/5 border border-line rounded-md px-4 py-2.5 text-white text-sm focus:outline-none focus:border-brand glow-input">
      </div>
      <button type="submit" :disabled="form.processing"
        class="w-full py-2.5 bg-brand text-white font-semibold rounded-md hover:bg-brand-bright transition-colors ring-1 ring-brand/40 text-sm disabled:opacity-60">
        {{ form.processing ? 'Réinitialisation…' : 'Réinitialiser le mot de passe' }}
      </button>
    </form>
  </GuestLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'

const props = defineProps({ token: String, email: String })
const form = useForm({ token: props.token, email: props.email ?? '', password: '', password_confirmation: '' })
</script>
