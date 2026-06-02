<template>
  <Link :href="href"
    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150 group"
    :class="active ? 'bg-white/5' : 'hover:bg-white/5'"
  >
    <div
      class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition-colors"
      :class="`bg-${color}-500/10 border border-${color}-500/20 group-hover:bg-${color}-500/15`"
    >
      <svg class="w-4 h-4" :class="`text-${color}-400`" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <slot />
      </svg>
    </div>
    <div class="min-w-0 flex-1">
      <div class="flex items-center gap-2">
        <span class="text-sm font-medium text-zinc-200 group-hover:text-white transition-colors">{{ label }}</span>
        <span v-if="badge" class="text-[10px] px-1.5 py-0.5 rounded-full bg-violet-500/20 text-violet-400 font-semibold leading-none">{{ badge }}</span>
      </div>
      <div class="text-xs text-zinc-600 mt-0.5">{{ desc }}</div>
    </div>
    <div v-if="active" class="ml-auto w-1.5 h-1.5 rounded-full shrink-0" :class="`bg-${color}-400`"></div>
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  href:   { type: String, required: true },
  color:  { type: String, default: 'violet' },
  active: { type: Boolean, default: false },
  label:  { type: String, required: true },
  desc:   { type: String, default: '' },
  badge:  { type: String, default: null },
})
</script>
