<template>
  <GuestLayout title="Create account">
    <div class="gradient-bg min-h-screen flex items-center justify-center px-4 py-12">
      <div class="w-full max-w-md">

        <!-- Brand -->
        <div class="text-center mb-8">
          <Link href="/" class="inline-flex items-center gap-2 mb-6">
            <div class="w-8 h-8 rounded-lg bg-violet-500 flex items-center justify-center">
              <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <span class="text-white font-bold text-lg">OnLeaked</span>
          </Link>
          <h1 class="text-2xl font-bold text-white mb-1">Create your account</h1>
          <p class="text-zinc-400 text-sm">Start monitoring your security for free</p>
        </div>

        <div class="glass-card p-8">
          <form @submit.prevent="submit" class="space-y-5">

            <!-- Name -->
            <div>
              <label class="block text-zinc-400 text-sm mb-1.5">Full name</label>
              <input
                v-model="form.name"
                type="text"
                autocomplete="name"
                required
                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm placeholder-zinc-500 focus:outline-none focus:border-violet-500/50"
                :class="{ 'border-red-500/50': form.errors.name }"
                placeholder="John Doe"
              />
              <p v-if="form.errors.name" class="mt-1.5 text-red-400 text-xs">{{ form.errors.name }}</p>
            </div>

            <!-- Email -->
            <div>
              <label class="block text-zinc-400 text-sm mb-1.5">Email address</label>
              <input
                v-model="form.email"
                type="email"
                autocomplete="username"
                required
                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm placeholder-zinc-500 focus:outline-none focus:border-violet-500/50"
                :class="{ 'border-red-500/50': form.errors.email }"
                placeholder="you@example.com"
              />
              <p v-if="form.errors.email" class="mt-1.5 text-red-400 text-xs">{{ form.errors.email }}</p>
            </div>

            <!-- Password -->
            <div>
              <label class="block text-zinc-400 text-sm mb-1.5">Password</label>
              <input
                v-model="form.password"
                type="password"
                autocomplete="new-password"
                required
                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm placeholder-zinc-500 focus:outline-none focus:border-violet-500/50"
                :class="{ 'border-red-500/50': form.errors.password }"
                placeholder="Min. 8 characters"
              />
              <p v-if="form.errors.password" class="mt-1.5 text-red-400 text-xs">{{ form.errors.password }}</p>
            </div>

            <!-- Confirm password -->
            <div>
              <label class="block text-zinc-400 text-sm mb-1.5">Confirm password</label>
              <input
                v-model="form.password_confirmation"
                type="password"
                autocomplete="new-password"
                required
                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm placeholder-zinc-500 focus:outline-none focus:border-violet-500/50"
                :class="{ 'border-red-500/50': form.errors.password_confirmation }"
                placeholder="••••••••"
              />
              <p v-if="form.errors.password_confirmation" class="mt-1.5 text-red-400 text-xs">{{ form.errors.password_confirmation }}</p>
            </div>

            <!-- Submit -->
            <button
              type="submit"
              :disabled="form.processing"
              class="w-full py-2.5 bg-gradient-to-r from-violet-600 to-violet-500 text-white font-semibold rounded-xl hover:from-violet-500 hover:to-violet-400 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            >
              <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
              </svg>
              <span>{{ form.processing ? 'Creating account…' : 'Create account' }}</span>
            </button>

          </form>
        </div>

        <p class="text-center text-zinc-500 text-sm mt-6">
          Already have an account?
          <Link href="/login" class="text-violet-400 hover:text-violet-300 transition-colors ml-1">Sign in</Link>
        </p>

      </div>
    </div>
  </GuestLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

function submit() {
  form.post('/register', {
    onFinish: () => form.reset('password', 'password_confirmation'),
  })
}
</script>
