<template>
  <PublicLayout title="Plateforme de renseignement cybersécurité">

    <!-- Hero -->
    <section class="relative pt-28 sm:pt-32 md:pt-36 pb-16 md:pb-24 px-4 sm:px-6 overflow-x-clip">
      <div class="grid-backdrop absolute inset-0 -z-10"></div>

      <div class="max-w-3xl mx-auto text-center">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-line bg-surface text-text-muted mb-7 fade-up">
          <span class="w-2 h-2 rounded-full bg-emerald-400 pulse-dot"></span>
          <span class="mono-label text-[10px]! text-text-muted">Sans pistage &bull; Données jamais conservées</span>
        </div>
        <h1 class="text-4xl sm:text-5xl md:text-7xl font-bold tracking-tight mb-5 fade-up leading-[1.05]" style="animation-delay:.1s">
          Reprenez le contrôle de<br class="hidden sm:block">
          <span class="text-brand-bright"> votre sécurité numérique</span>
        </h1>
        <p class="text-text-muted text-base sm:text-lg md:text-xl mb-9 max-w-2xl mx-auto fade-up" style="animation-delay:.2s">
          Six outils de sécurité réunis sur une seule plateforme. Vérifiez les fuites, auditez vos domaines, inspectez les certificats sans jamais conserver vos données.
        </p>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-center gap-3 fade-up" style="animation-delay:.3s">
          <Link :href="route('leak-check')" class="px-7 py-3.5 bg-brand text-white font-semibold rounded-md hover:bg-brand-bright transition-colors text-sm ring-1 ring-brand/40">
            Vérifier mon e-mail
          </Link>
          <Link :href="route('domain.show')" class="px-7 py-3.5 bg-surface border border-line text-text font-medium rounded-md hover:border-line-strong hover:bg-surface-2 transition-colors text-sm">
            Analyser un domaine
          </Link>
        </div>
      </div>

      <!-- Product mockup -->
      <div class="relative max-w-5xl mx-auto mt-14 md:mt-20 fade-up" style="animation-delay:.4s">

        <!-- Floating card (desktop only) — leak check result -->
        <div class="hidden lg:block absolute -left-8 top-20 w-60 z-20 rotate-[-5deg] rounded-lg bg-surface-2 border border-line shadow-2xl shadow-black/60 p-4">
          <div class="flex items-center gap-2 mb-3">
            <span class="w-7 h-7 rounded-md bg-brand/10 border border-brand/25 flex items-center justify-center">
              <svg class="w-3.5 h-3.5 text-brand-bright" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <span class="mono-label text-[9px]! text-text-dim">Vérification de fuite</span>
          </div>
          <p class="text-sm text-text font-mono truncate">jean@exemple.com</p>
          <div class="mt-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-sm text-emerald-400 font-medium">0 fuite trouvée</span>
          </div>
        </div>

        <!-- Floating card (desktop only) — digital footprint -->
        <div class="hidden lg:block absolute -right-6 top-44 w-52 z-20 rotate-[4deg] rounded-lg bg-surface-2 border border-line shadow-2xl shadow-black/60 p-4">
          <span class="mono-label text-[9px]! text-text-dim">Empreinte numérique</span>
          <p class="text-3xl font-bold font-mono text-text mt-1">14<span class="text-base text-text-dim"> services</span></p>
          <div class="mt-3 h-1.5 rounded-full bg-white/5 overflow-hidden">
            <div class="h-full w-2/3 bg-brand rounded-full"></div>
          </div>
        </div>

        <!-- Main app window -->
        <div class="relative z-10 rounded-xl bg-surface border border-line shadow-2xl shadow-black/70 ring-1 ring-white/5 overflow-hidden">

          <!-- Window top bar -->
          <div class="flex items-center gap-3 px-4 py-3 border-b border-line bg-surface-2/50">
            <div class="flex items-center gap-2">
              <span class="w-7 h-7 rounded-md bg-brand flex items-center justify-center ring-1 ring-brand/40 shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
              </span>
              <span class="hidden sm:block text-sm font-semibold text-text">Onleaked</span>
            </div>
            <div class="hidden md:flex items-center gap-1 ml-4 text-xs">
              <span class="px-3 py-1.5 rounded-md bg-white/5 text-text">Tableau de bord</span>
              <span class="px-3 py-1.5 rounded-md text-text-dim">Fuites</span>
              <span class="px-3 py-1.5 rounded-md text-text-dim">Domaines</span>
              <span class="px-3 py-1.5 rounded-md text-text-dim">SSL</span>
            </div>
            <div class="ml-auto inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border border-line bg-surface">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 pulse-dot"></span>
              <span class="mono-label text-[9px]! text-text-muted">Analyse en direct</span>
            </div>
          </div>

          <!-- Window body -->
          <div class="p-3 sm:p-5 grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">

            <!-- Security score -->
            <div class="col-span-2 rounded-lg bg-surface-2 border border-line p-4 sm:p-5">
              <div class="flex items-center justify-between mb-2">
                <span class="mono-label text-[9px]! text-text-dim">Score de sécurité</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/20 text-emerald-400">Faible risque</span>
              </div>
              <p class="text-4xl sm:text-5xl font-bold font-mono text-text">82<span class="text-xl text-text-dim">/100</span></p>
              <svg class="mt-3 w-full h-10" viewBox="0 0 240 40" preserveAspectRatio="none" fill="none">
                <polyline points="0,30 30,26 60,28 90,18 120,22 150,12 180,16 210,8 240,11" stroke="var(--color-brand-bright)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>

            <!-- Breaches detected -->
            <div class="rounded-lg bg-surface-2 border border-line p-4">
              <span class="mono-label text-[9px]! text-text-dim">Fuites détectées</span>
              <p class="text-3xl sm:text-4xl font-bold font-mono text-text mt-1">3</p>
              <span class="mt-2 inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-500/20 text-red-400">Compromis</span>
            </div>

            <!-- SSL grade -->
            <div class="rounded-lg bg-surface-2 border border-line p-4 flex flex-col">
              <span class="mono-label text-[9px]! text-text-dim">Note SSL</span>
              <p class="text-4xl sm:text-5xl font-bold font-mono text-emerald-400 mt-auto">A+</p>
              <span class="text-[11px] text-text-dim mt-1">exemple.com</span>
            </div>

            <!-- Exposures chart -->
            <div class="col-span-2 rounded-lg bg-surface-2 border border-line p-4 sm:p-5">
              <div class="flex items-center justify-between mb-4">
                <span class="mono-label text-[9px]! text-text-dim">Expositions / semaine</span>
                <span class="text-[10px] text-text-dim">7 derniers jours</span>
              </div>
              <div class="flex items-end justify-between gap-1.5 sm:gap-2.5 h-20 sm:h-24">
                <div v-for="(h, i) in [40, 65, 35, 80, 55, 95, 50]" :key="i" class="flex-1 flex flex-col items-center gap-1.5">
                  <div class="w-full rounded-sm transition-all" :class="i === 5 ? 'bg-brand-bright' : 'bg-brand/35'" :style="`height:${h}%`"></div>
                  <span class="mono-label text-[8px]! text-text-dim">{{ ['L','M','M','J','V','S','D'][i] }}</span>
                </div>
              </div>
            </div>

            <!-- Recent analyses table -->
            <div class="col-span-2 rounded-lg bg-surface-2 border border-line overflow-hidden">
              <div class="px-4 py-3 border-b border-line flex items-center justify-between">
                <span class="mono-label text-[9px]! text-text-dim">Analyses récentes</span>
                <span class="text-[10px] text-brand-bright">Tout voir</span>
              </div>
              <div class="divide-y divide-line">
                <div v-for="row in mockRows" :key="row.target" class="flex items-center gap-3 px-4 py-2.5">
                  <span class="w-7 h-7 rounded-md bg-brand/10 border border-brand/20 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5 text-brand-bright" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="row.icon"/></svg>
                  </span>
                  <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm text-text font-mono truncate">{{ row.target }}</p>
                    <p class="text-[10px] text-text-dim hidden sm:block">{{ row.type }}</p>
                  </div>
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold shrink-0" :class="row.cls">{{ row.status }}</span>
                  <span class="text-[10px] text-text-dim shrink-0 hidden sm:block w-12 text-right">{{ row.date }}</span>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </section>

    <!-- Stats -->
    <section class="py-10 px-6 border-y border-line" ref="statsRef">
      <div class="max-w-4xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
        <div v-for="s in stats" :key="s.label">
          <p class="text-3xl font-bold text-white font-mono">{{ s.display }}</p>
          <p class="text-sm text-text-dim mt-1">{{ s.label }}</p>
        </div>
      </div>
    </section>

    <!-- Tools grid -->
    <section class="py-24 px-6">
      <div class="max-w-5xl mx-auto">
        <div class="text-center mb-16">
          <p class="mono-label text-brand-bright mb-3">// Services</p>
          <h2 class="text-3xl md:text-4xl font-bold mb-4">Six outils puissants, une seule plateforme.</h2>
          <p class="text-text-muted max-w-xl mx-auto">Aucun compte requis pour les vérifications de sécurité. Gratuit, instantané, respectueux de votre vie privée.</p>
        </div>
        <div class="grid md:grid-cols-3 gap-px bg-line rounded-lg overflow-hidden border border-line">
          <ToolCard v-for="tool in tools" :key="tool.name" v-bind="tool" />
        </div>
      </div>
    </section>

    <!-- How it works -->
    <section class="py-24 px-6 border-t border-line">
      <div class="max-w-4xl mx-auto">
        <div class="text-center mb-16">
          <p class="mono-label text-brand-bright mb-3">// Fonctionnement</p>
          <h2 class="text-3xl md:text-4xl font-bold">Simple. Rapide. Privé.</h2>
        </div>
        <div class="grid md:grid-cols-3 gap-10 text-center">
          <div v-for="step in steps" :key="step.n" class="flex flex-col items-center gap-4">
            <div class="w-11 h-11 rounded-md bg-surface border border-brand/30 flex items-center justify-center text-lg font-bold text-brand-bright font-mono">{{ step.n }}</div>
            <h3 class="font-semibold">{{ step.title }}</h3>
            <p class="text-text-dim text-sm">{{ step.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Privacy -->
    <section class="py-24 px-6 border-t border-line">
      <div class="max-w-4xl mx-auto flex flex-col md:flex-row items-center gap-12">
        <div class="flex-1">
          <p class="mono-label text-brand-bright mb-3">// Vie privée d'abord</p>
          <h2 class="text-3xl font-bold mb-4">On vérifie vos données.<br>On ne les garde jamais.</h2>
          <p class="text-text-muted leading-relaxed mb-6">Votre e-mail ou domaine n'existe qu'en mémoire le temps de la requête. Jamais écrit sur disque, jamais journalisé, jamais mis en cache au-delà de l'analyse.</p>
          <ul class="space-y-3">
            <li v-for="item in privacyItems" :key="item" class="flex items-center gap-3 text-sm text-text">
              <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
              {{ item }}
            </li>
          </ul>
        </div>
        <div class="flex-1 flex justify-center">
          <div class="glass-card p-8 w-full max-w-sm text-center">
            <div class="w-16 h-16 rounded-md bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <p class="font-semibold text-white mb-1">Zéro rétention de données</p>
            <p class="text-text-dim text-sm">Vos requêtes ne laissent aucune trace sur nos serveurs.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="py-24 px-6 border-t border-line">
      <div class="max-w-3xl mx-auto text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">Prêt à vérifier votre exposition ?</h2>
        <p class="text-text-muted mb-8">Gratuit, instantané et totalement privé. Aucune inscription requise.</p>
        <div class="flex flex-wrap items-center justify-center gap-3">
          <Link :href="route('leak-check')" class="px-7 py-3.5 bg-brand text-white font-semibold rounded-md hover:bg-brand-bright transition-colors text-sm ring-1 ring-brand/40">Vérifier mon e-mail</Link>
          <Link :href="route('services')" class="px-7 py-3.5 bg-surface border border-line text-text font-medium rounded-md hover:border-line-strong hover:bg-surface-2 transition-colors text-sm">Voir tous les services</Link>
        </div>
      </div>
    </section>

  </PublicLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import ToolCard from '@/Components/ToolCard.vue'

// Demo data for the hero dashboard mockup
const mockRows = [
  { target: 'jean@exemple.com', type: 'Vérification de fuite', status: 'Compromis',    cls: 'bg-red-500/20 text-red-400',         date: '12 mar', icon: 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' },
  { target: 'exemple.com',      type: 'Analyse de domaine',    status: 'Sain',          cls: 'bg-emerald-500/20 text-emerald-400', date: '11 mar', icon: 'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3' },
  { target: '8.8.8.8',          type: 'Réputation IP',         status: 'Faible risque', cls: 'bg-emerald-500/20 text-emerald-400', date: '10 mar', icon: 'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3' },
  { target: 'exemple.com',      type: 'Certificat SSL',        status: 'Note A+',       cls: 'bg-emerald-500/20 text-emerald-400', date: '09 mar', icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z' },
]

const statsRef = ref(null)
const stats = ref([
  { label: 'enregistrements indexés', target: 15, suffix: ' Md+', display: '0' },
  { label: 'plateformes scannées',    target: 120, suffix: '+', display: '0' },
  { label: 'outils de sécurité',      target: 6,  suffix: '',  display: '0' },
  { label: 'octet de vos données stocké', target: 0, suffix: '', display: '0' },
])

const tools = [
  { href: '/leak-check',     color: 'brand', name: 'Vérification de fuite', desc: 'Vérifiez si votre e-mail a été exposé dans une fuite et découvrez votre empreinte numérique sur 120+ plateformes.', cta: 'Vérifier gratuitement' },
  { href: '/domain',         color: 'brand', name: 'Analyse de domaine',    desc: 'Renseignement complet : DNS, audit SPF/DMARC, énumération de sous-domaines et vérification de réputation.', cta: 'Analyser maintenant' },
  { href: '/password-check', color: 'brand', name: 'Mot de passe compromis', desc: 'Vérifiez si un mot de passe a été exposé via le k-anonymat. Votre mot de passe n\'est jamais envoyé en entier.', cta: 'Vérifier un mot de passe' },
  { href: '/ssl-check',      color: 'brand', name: 'Inspecteur SSL',        desc: 'Inspectez le certificat d\'un domaine émetteur, validité, SAN, jours avant expiration et note de sécurité.', cta: 'Inspecter le SSL' },
  { href: '/ip-check',       color: 'brand', name: 'Réputation IP',         desc: 'Géolocalisation, FAI, recherche ASN et score de confiance d\'abus issu du renseignement mondial sur les menaces.', cta: 'Vérifier une IP' },
  { href: '/register',       color: 'brand', name: 'Alertes CVE',           desc: 'Abonnez-vous à des technologies et recevez des alertes e-mail dès la publication de nouvelles CVE.', cta: 'Commencer gratuitement', badge: 'PRO' },
]

const steps = [
  { n: 1, title: 'Saisissez une cible', desc: 'E-mail, domaine, mot de passe, adresse IP ou en-têtes bruts. Aucun compte requis.' },
  { n: 2, title: 'Nous interrogeons les sources OSINT', desc: 'Vérifications en temps réel des bases de fuites, enregistrements DNS et journaux de transparence des certificats.' },
  { n: 3, title: 'Recevez votre rapport complet', desc: 'Résultats détaillés et instantanés avec recommandations concrètes et export PDF.' },
]

const privacyItems = [
  'Aucun compte nécessaire pour les vérifications',
  'E-mails jamais stockés, journalisés ni mis en cache',
  'API open source uniquement (XposedOrNot, crt.sh)',
  'Limitation de débit pour prévenir les abus',
]

onMounted(() => {
  if (!statsRef.value) return
  const obs = new IntersectionObserver(([entry]) => {
    if (!entry.isIntersecting) return
    obs.disconnect()
    stats.value.forEach(s => {
      if (s.target === 0) { s.display = '0'; return }
      let v = 0
      const step = Math.max(1, Math.ceil(s.target / 40))
      const id = setInterval(() => {
        v = Math.min(v + step, s.target)
        s.display = v + s.suffix
        if (v >= s.target) clearInterval(id)
      }, 30)
    })
  }, { threshold: 0.3 })
  obs.observe(statsRef.value)
})
</script>
