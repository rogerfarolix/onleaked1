<template>
  <div class="min-h-screen bg-ink">

    <!-- Top bar -->
    <nav class="border-b border-line bg-ink/85 backdrop-blur-md">
      <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between gap-4">

        <!-- Logo -->
        <Link :href="route('home')" class="flex items-center gap-2.5 shrink-0">
          <div class="w-8 h-8 rounded-md bg-brand flex items-center justify-center ring-1 ring-brand/40">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
            </svg>
          </div>
          <span class="font-bold text-base tracking-tight text-text">
            On<span class="text-brand-bright">leaked</span>
          </span>
        </Link>

        <!-- Nav links -->
        <div class="hidden md:flex items-center gap-1 text-sm">
          <Link :href="route('dashboard')" class="px-3 py-2 rounded-md transition-colors" :class="isActive('dashboard') ? 'text-white bg-white/5' : 'text-text-muted hover:text-white hover:bg-white/5'">Tableau de bord</Link>
          <Link :href="route('history')" class="px-3 py-2 rounded-md transition-colors" :class="isActive('history') ? 'text-white bg-white/5' : 'text-text-muted hover:text-white hover:bg-white/5'">Historique</Link>
          <Link v-if="auth?.user?.role === 'admin'" :href="route('admin.dashboard')" class="px-3 py-2 rounded-md text-brand-bright hover:text-white hover:bg-brand/10 transition-colors">Administration</Link>
        </div>

        <!-- User menu -->
        <div class="flex items-center gap-3">
          <span class="hidden sm:block text-sm text-text-muted">{{ auth?.user?.name }}</span>
          <Link :href="route('profile.edit')" class="w-8 h-8 rounded-md bg-brand flex items-center justify-center text-white text-sm font-semibold ring-1 ring-brand/40">
            {{ auth?.user?.name?.charAt(0)?.toUpperCase() }}
          </Link>
          <Link :href="route('logout')" method="post" as="button" class="text-xs text-text-dim hover:text-white transition-colors">
            Déconnexion
          </Link>
        </div>
      </div>
    </nav>

    <!-- Flash messages -->
    <Transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0">
      <div v-if="flash?.success" class="max-w-7xl mx-auto px-6 pt-4">
        <div class="p-3 rounded-md bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-2">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          {{ flash.success }}
        </div>
      </div>
    </Transition>

    <slot />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const page  = usePage()
const auth  = computed(() => page.props.auth)
const flash = computed(() => page.props.flash)

const isActive = (name) => { try { return route().current(name) } catch { return false } }
</script>
