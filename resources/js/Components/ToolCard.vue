<template>
  <Link :href="href"
    class="glass-card p-7 hover:border-current transition-all duration-300 group block"
    :style="`--tw-border-opacity: 0; --color: var(--tw-${color}-400)`"
    :class="`hover:border-${color}-500/20`"
  >
    <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-5 transition-colors"
      :class="`bg-${color}-500/10 group-hover:bg-${color}-500/20`">
      <slot name="icon">
        <svg class="w-6 h-6" :class="`text-${color}-400`" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
      </slot>
    </div>
    <div class="flex items-center gap-2 mb-2">
      <h3 class="font-semibold text-lg">{{ name }}</h3>
      <span v-if="badge" class="text-[10px] px-1.5 py-0.5 rounded-full bg-violet-500/20 text-violet-400 font-semibold">{{ badge }}</span>
    </div>
    <p class="text-zinc-400 text-sm leading-relaxed mb-4">{{ desc }}</p>
    <span class="text-sm font-medium flex items-center gap-1 group-hover:gap-2 transition-all" :class="`text-${color}-400`">
      {{ cta }}
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </span>
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  href:  { type: String, required: true },
  color: { type: String, default: 'violet' },
  name:  { type: String, required: true },
  desc:  { type: String, required: true },
  cta:   { type: String, required: true },
  badge: { type: String, default: null },
})
</script>
