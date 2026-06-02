<template>
  <Link :href="href"
    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-colors"
    :class="active ? 'text-white bg-white/5' : 'text-zinc-400 hover:text-white hover:bg-white/5'"
  >
    <span v-if="color" class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0" :class="`bg-${color}-500/10 border border-${color}-500/20`">
      <slot name="icon">
        <svg class="w-3.5 h-3.5" :class="`text-${color}-400`" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
      </slot>
    </span>
    <slot />
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  href:   { type: String, required: true },
  color:  { type: String, default: null },
  active: { type: Boolean, default: false },
})
</script>
