<template>
  <GuestLayout title="Confirmer le mot de passe">
    <h2 class="text-xl font-bold text-white text-center mb-2">Confirmez votre mot de passe</h2>
    <p class="text-text-dim text-sm text-center mb-6">Ceci est une zone sécurisée. Veuillez confirmer votre mot de passe pour continuer.</p>

    <form @submit.prevent="form.post('/confirm-password', { onFinish: () => form.reset('password') })" class="space-y-4">
      <div>
        <label class="block text-sm text-text-muted mb-1.5">Mot de passe</label>
        <input v-model="form.password" type="password" required autofocus
          class="w-full bg-white/5 border border-line rounded-md px-4 py-2.5 text-white text-sm focus:outline-none focus:border-brand glow-input"
          :class="{ 'border-red-500/50': form.errors.password }">
        <p v-if="form.errors.password" class="mt-1 text-xs text-red-400">{{ form.errors.password }}</p>
      </div>
      <button type="submit" :disabled="form.processing"
        class="w-full py-2.5 bg-brand text-white font-semibold rounded-md hover:bg-brand-bright transition-colors ring-1 ring-brand/40 text-sm disabled:opacity-60">
        {{ form.processing ? 'Confirmation…' : 'Confirmer' }}
      </button>
    </form>
  </GuestLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'

const form = useForm({ password: '' })
</script>
