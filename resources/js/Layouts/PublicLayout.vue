<template>
  <div class="min-h-screen flex flex-col">

    <!-- Navigation -->
    <nav
      class="fixed top-0 w-full z-50 border-b backdrop-blur-xl transition-all duration-300"
      :class="scrolled ? 'border-white/10 bg-[#09090b]/95 shadow-xl shadow-black/20' : 'border-transparent bg-[#09090b]/60'"
    >
      <div class="max-w-6xl mx-auto px-6 h-[68px] flex items-center justify-between gap-6">

        <!-- Logo -->
        <Link :href="route('home')" class="flex items-center gap-2.5 shrink-0 group">
          <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-violet-600 to-rose-500 flex items-center justify-center shadow-lg shadow-violet-500/30">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
            </svg>
          </div>
          <span class="font-bold text-[17px] tracking-tight">
            <span class="text-white">On</span><span class="bg-gradient-to-r from-violet-400 to-rose-400 bg-clip-text text-transparent">leaked</span>
          </span>
        </Link>

        <!-- Desktop nav -->
        <div class="hidden md:flex items-center gap-1 text-sm">
          <Link :href="route('home')" class="px-3 py-2 rounded-lg transition-colors" :class="isActive('home') ? 'text-white bg-white/5' : 'text-zinc-400 hover:text-white hover:bg-white/5'">
            Home
          </Link>

          <!-- Tools dropdown -->
          <div class="relative" ref="dropdownRef">
            <button
              @click="toolsOpen = !toolsOpen"
              class="flex items-center gap-1.5 px-3 py-2 rounded-lg transition-all duration-150"
              :class="toolsOpen || isToolPage ? 'text-white bg-white/5' : 'text-zinc-400 hover:text-white hover:bg-white/5'"
            >
              Tools
              <svg class="w-3 h-3 transition-transform duration-200" :class="{ 'rotate-180': toolsOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>

            <Transition
              enter-active-class="transition ease-out duration-150"
              enter-from-class="opacity-0 -translate-y-2"
              enter-to-class="opacity-100 translate-y-0"
              leave-active-class="transition ease-in duration-100"
              leave-from-class="opacity-100 translate-y-0"
              leave-to-class="opacity-0 -translate-y-2"
            >
              <div v-show="toolsOpen"
                class="absolute top-[calc(100%+10px)] left-1/2 -translate-x-1/2 w-[300px] rounded-2xl shadow-2xl z-60 overflow-hidden"
                style="background:#0c0c0f; border:1px solid rgba(255,255,255,0.08);"
              >
                <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 w-3 h-3 rotate-45" style="background:#0c0c0f; border-top:1px solid rgba(255,255,255,0.08); border-left:1px solid rgba(255,255,255,0.08);"></div>
                <div class="p-1.5 pt-3">
                  <p class="px-3 pb-2 text-[10px] font-semibold uppercase tracking-widest text-zinc-600">Security Tools</p>
                  <NavToolItem :href="route('leak-check')" color="rose" :active="isActive('leak-check')" label="Leak Check" desc="Email in 120+ breach databases">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                  </NavToolItem>
                  <NavToolItem :href="route('domain.show')" color="amber" :active="isActive('domain.show')" label="Domain Analysis" desc="DNS, SPF, DMARC & reputation">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3"/>
                  </NavToolItem>
                  <NavToolItem :href="route('password-check')" color="cyan" :active="isActive('password-check')" label="Password Check" desc="k-Anonymity breach lookup">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                  </NavToolItem>
                  <NavToolItem :href="route('ssl-check')" color="sky" :active="isActive('ssl-check')" label="SSL Inspector" desc="Certificate details & grade">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                  </NavToolItem>
                  <NavToolItem :href="route('ip-check')" color="indigo" :active="isActive('ip-check')" label="IP Reputation" desc="Geolocation & abuse score">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3"/>
                  </NavToolItem>
                  <NavToolItem :href="route('header-check')" color="teal" :active="isActive('header-check')" label="Header Analyzer" desc="SPF, DKIM, DMARC parsing">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                  </NavToolItem>
                  <NavToolItem v-if="!auth?.user" :href="route('register')" color="violet" label="CVE Alerts" desc="Real-time vulnerability alerts" badge="PRO">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                  </NavToolItem>
                </div>
                <div class="mx-3 my-1 border-t border-white/5"></div>
                <div class="p-1.5">
                  <Link :href="route('services')" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs text-zinc-500 hover:text-zinc-200 hover:bg-white/5 transition-all group">
                    <span>All tools overview</span>
                    <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                  </Link>
                </div>
              </div>
            </Transition>
          </div>

          <Link :href="route('contact')" class="px-3 py-2 rounded-lg transition-colors" :class="isActive('contact') ? 'text-white bg-white/5' : 'text-zinc-400 hover:text-white hover:bg-white/5'">
            Contact
          </Link>
        </div>

        <!-- Right actions -->
        <div class="hidden md:flex items-center gap-2 shrink-0">
          <button @click="toggle()" class="w-9 h-9 rounded-lg flex items-center justify-center text-zinc-500 hover:text-white hover:bg-white/5 transition-colors">
            <svg v-if="dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
          </button>
          <div class="w-px h-5 bg-white/10"></div>
          <template v-if="auth?.user">
            <Link :href="route('dashboard')" class="text-sm px-3 py-2 rounded-lg text-zinc-400 hover:text-white hover:bg-white/5 transition-colors flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
              Dashboard
            </Link>
          </template>
          <template v-else>
            <Link :href="route('login')" class="text-sm px-3 py-2 rounded-lg text-zinc-400 hover:text-white transition-colors">Log in</Link>
            <Link :href="route('register')" class="text-sm px-4 py-2 rounded-xl bg-gradient-to-r from-violet-600 to-violet-500 text-white font-medium hover:from-violet-500 hover:to-violet-400 transition-all shadow-lg shadow-violet-500/20">
              Get Started
            </Link>
          </template>
        </div>

        <!-- Mobile buttons -->
        <div class="md:hidden flex items-center gap-1">
          <button @click="toggle()" class="w-9 h-9 rounded-lg flex items-center justify-center text-zinc-500 hover:text-white transition-colors">
            <svg v-if="dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
          </button>
          <button @click="menuOpen = !menuOpen" class="w-9 h-9 rounded-lg flex items-center justify-center text-zinc-400 hover:text-white hover:bg-white/5 transition-colors">
            <svg v-if="!menuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
      </div>

      <!-- Mobile menu -->
      <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0 -translate-y-2">
        <div v-show="menuOpen" class="md:hidden border-t border-white/5 bg-[#09090b]/98">
          <div class="px-4 py-4 space-y-1">
            <p class="px-3 pt-1 pb-2 text-[10px] font-semibold uppercase tracking-widest text-zinc-600">Navigation</p>
            <MobileNavLink :href="route('home')" icon="home" :active="isActive('home')">Home</MobileNavLink>
            <MobileNavLink :href="route('contact')" :active="isActive('contact')">Contact</MobileNavLink>
            <p class="px-3 pt-3 pb-2 text-[10px] font-semibold uppercase tracking-widest text-zinc-600">Tools</p>
            <MobileNavLink :href="route('leak-check')" color="rose" :active="isActive('leak-check')">Leak Check</MobileNavLink>
            <MobileNavLink :href="route('domain.show')" color="amber" :active="isActive('domain.show')">Domain Analysis</MobileNavLink>
            <MobileNavLink :href="route('password-check')" color="cyan" :active="isActive('password-check')">Password Check</MobileNavLink>
            <MobileNavLink :href="route('ssl-check')" color="sky" :active="isActive('ssl-check')">SSL Inspector</MobileNavLink>
            <MobileNavLink :href="route('ip-check')" color="indigo" :active="isActive('ip-check')">IP Reputation</MobileNavLink>
            <MobileNavLink :href="route('header-check')" color="teal" :active="isActive('header-check')">Header Analyzer</MobileNavLink>
            <div class="pt-3 pb-1 border-t border-white/5 mt-2">
              <template v-if="auth?.user">
                <Link :href="route('dashboard')" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-zinc-400 hover:text-white hover:bg-white/5 transition-colors">Dashboard</Link>
              </template>
              <template v-else>
                <div class="flex gap-2 px-1 pt-1">
                  <Link :href="route('login')" class="flex-1 text-center text-sm px-4 py-2.5 rounded-xl border border-white/10 text-zinc-300 hover:bg-white/5 transition-colors">Log in</Link>
                  <Link :href="route('register')" class="flex-1 text-center text-sm px-4 py-2.5 rounded-xl bg-gradient-to-r from-violet-600 to-violet-500 text-white font-medium">Get Started</Link>
                </div>
              </template>
            </div>
          </div>
        </div>
      </Transition>
    </nav>

    <!-- Page content -->
    <main class="flex-1">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="border-t border-white/5 py-10 px-6">
      <div class="max-w-5xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2">
          <div class="w-6 h-6 rounded-md bg-gradient-to-br from-violet-500 to-rose-500 flex items-center justify-center font-bold text-xs text-white">O</div>
          <span class="text-sm text-zinc-500">&copy; {{ new Date().getFullYear() }} Onleaked by Nealix. All rights reserved.</span>
        </div>
        <div class="flex items-center gap-6 text-sm text-zinc-500">
          <Link :href="route('home')" class="hover:text-white transition-colors">Home</Link>
          <Link :href="route('services')" class="hover:text-white transition-colors">Services</Link>
          <Link :href="route('contact')" class="hover:text-white transition-colors">Contact</Link>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { useTheme } from '@/Composables/useTheme'
import NavToolItem from './Partials/NavToolItem.vue'
import MobileNavLink from './Partials/MobileNavLink.vue'

const { dark, toggle } = useTheme()
const page = usePage()

const auth      = computed(() => page.props.auth)
const menuOpen  = ref(false)
const toolsOpen = ref(false)
const scrolled  = ref(false)
const dropdownRef = ref(null)

const currentRoute = computed(() => page.props.ziggy?.route ?? '')

const isActive = (name) => {
  try { return route().current(name) } catch { return false }
}

const isToolPage = computed(() => {
  const tools = ['leak-check','domain.show','password-check','ssl-check','ip-check','header-check','services']
  return tools.some(t => { try { return route().current(t) } catch { return false } })
})

const onScroll = () => { scrolled.value = window.scrollY > 20 }
const onClickOutside = (e) => {
  if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
    toolsOpen.value = false
  }
}

onMounted(() => {
  window.addEventListener('scroll', onScroll)
  document.addEventListener('click', onClickOutside)
})
onUnmounted(() => {
  window.removeEventListener('scroll', onScroll)
  document.removeEventListener('click', onClickOutside)
})
</script>
