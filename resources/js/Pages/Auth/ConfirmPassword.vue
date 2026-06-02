<template>
  <GuestLayout title="Confirm Password">
    <h2 class="text-xl font-bold text-white text-center mb-2">Confirm your password</h2>
    <p class="text-zinc-500 text-sm text-center mb-6">This is a secure area. Please confirm your password to continue.</p>

    <form @submit.prevent="form.post('/confirm-password', { onFinish: () => form.reset('password') })" class="space-y-4">
      <div>
        <label class="block text-sm text-zinc-400 mb-1.5">Password</label>
        <input v-model="form.password" type="password" required autofocus
          class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-violet-500/50"
          :class="{ 'border-red-500/50': form.errors.password }">
        <p v-if="form.errors.password" class="mt-1 text-xs text-red-400">{{ form.errors.password }}</p>
      </div>
      <button type="submit" :disabled="form.processing"
        class="w-full py-2.5 bg-gradient-to-r from-violet-600 to-violet-500 text-white font-semibold rounded-xl hover:from-violet-500 hover:to-violet-400 transition-all text-sm disabled:opacity-60">
        {{ form.processing ? 'Confirming…' : 'Confirm' }}
      </button>
    </form>
  </GuestLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'

const form = useForm({ password: '' })
</script>
