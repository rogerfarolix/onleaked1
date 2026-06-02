<template>
  <GuestLayout title="Set New Password">
    <h2 class="text-xl font-bold text-white text-center mb-6">Set a new password</h2>

    <form @submit.prevent="form.post('/reset-password', { onFinish: () => form.reset('password', 'password_confirmation') })" class="space-y-4">
      <div>
        <label class="block text-sm text-zinc-400 mb-1.5">Email</label>
        <input v-model="form.email" type="email" required
          class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-violet-500/50"
          :class="{ 'border-red-500/50': form.errors.email }">
        <p v-if="form.errors.email" class="mt-1 text-xs text-red-400">{{ form.errors.email }}</p>
      </div>
      <div>
        <label class="block text-sm text-zinc-400 mb-1.5">New Password</label>
        <input v-model="form.password" type="password" required autofocus
          class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-violet-500/50"
          :class="{ 'border-red-500/50': form.errors.password }">
        <p v-if="form.errors.password" class="mt-1 text-xs text-red-400">{{ form.errors.password }}</p>
      </div>
      <div>
        <label class="block text-sm text-zinc-400 mb-1.5">Confirm Password</label>
        <input v-model="form.password_confirmation" type="password" required
          class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-violet-500/50">
      </div>
      <button type="submit" :disabled="form.processing"
        class="w-full py-2.5 bg-gradient-to-r from-violet-600 to-violet-500 text-white font-semibold rounded-xl hover:from-violet-500 hover:to-violet-400 transition-all text-sm disabled:opacity-60">
        {{ form.processing ? 'Resetting…' : 'Reset Password' }}
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
