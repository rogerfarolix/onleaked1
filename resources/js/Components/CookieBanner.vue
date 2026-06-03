<template>
  <Transition
    enter-active-class="transition ease-out duration-300"
    enter-from-class="opacity-0 translate-y-4"
    enter-to-class="opacity-100 translate-y-0"
    leave-active-class="transition ease-in duration-200"
    leave-from-class="opacity-100 translate-y-0"
    leave-to-class="opacity-0 translate-y-4"
  >
    <div v-if="visible" class="fixed bottom-0 inset-x-0 z-[100] p-4">
      <div class="max-w-3xl mx-auto bg-surface border border-line rounded-lg shadow-2xl shadow-black/50 p-4 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center gap-4">
        <div class="flex items-start gap-3 flex-1 min-w-0">
          <div class="w-9 h-9 rounded-md bg-brand/10 border border-brand/25 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 text-brand-bright" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2a10 10 0 100 20 10 10 0 00-3-19.5M9 9h.01M15 13h.01M10 15h.01M14 8h.01"/>
            </svg>
          </div>
          <p class="text-sm text-text-muted leading-relaxed">
            Ce site utilise des cookies essentiels pour assurer son bon fonctionnement et la sécurité de votre session. Aucune donnée de pistage publicitaire n'est collectée.
          </p>
        </div>
        <div class="flex items-center gap-2 shrink-0 self-stretch sm:self-auto">
          <button @click="decline"
            class="flex-1 sm:flex-none px-4 py-2 rounded-md bg-white/5 border border-line text-text-muted text-sm hover:bg-white/10 hover:text-white transition-colors whitespace-nowrap">
            Refuser
          </button>
          <button @click="accept"
            class="flex-1 sm:flex-none px-4 py-2 rounded-md bg-brand text-white text-sm font-medium hover:bg-brand-bright transition-colors ring-1 ring-brand/40 whitespace-nowrap">
            Accepter
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const STORAGE_KEY = 'onleaked-cookie-consent'
const visible = ref(false)

onMounted(() => {
  try {
    if (!localStorage.getItem(STORAGE_KEY)) visible.value = true
  } catch {
    visible.value = true
  }
})

function persist(value) {
  try { localStorage.setItem(STORAGE_KEY, value) } catch {}
  visible.value = false
}

const accept  = () => persist('accepted')
const decline = () => persist('declined')
</script>
