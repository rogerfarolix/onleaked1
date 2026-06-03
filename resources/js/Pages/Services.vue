<template>
  <PublicLayout title="Services">
    <div class="gradient-bg min-h-screen py-16 px-4">
      <div class="max-w-6xl mx-auto">

        <!-- Header -->
        <div class="text-center mb-16">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand/10 border border-brand/25 text-brand-bright mono-label mb-4">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            Outils de cybersécurité
          </div>
          <h1 class="text-4xl font-bold text-white mb-4">Suite de renseignement de sécurité</h1>
          <p class="text-text-muted text-lg max-w-2xl mx-auto">Des outils de cybersécurité de niveau professionnel pour investiguer domaines, IP, en-têtes d'e-mails, certificats SSL et fuites de données — sur une seule plateforme.</p>
        </div>

        <!-- Tools grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-px bg-line rounded-lg overflow-hidden border border-line mb-20">
          <ToolCard
            href="/leak-check"
            name="Vérification de fuite d'e-mail"
            desc="Découvrez si une adresse e-mail a été exposée dans des fuites de données connues. Propulsé par les API XposedOrNot et HaveIBeenPwned."
            cta="Vérifier les fuites"
            badge="Gratuit"
          >
            <template #icon>
              <svg class="w-5 h-5 text-brand-bright" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
            </template>
          </ToolCard>

          <ToolCard
            href="/domain"
            name="Analyse de domaine"
            desc="Reconnaissance DNS complète, scoring de sécurité e-mail (SPF/DMARC/DKIM), découverte de sous-domaines et vérification de réputation."
            cta="Analyser un domaine"
          >
            <template #icon>
              <svg class="w-5 h-5 text-brand-bright" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/>
              </svg>
            </template>
          </ToolCard>

          <ToolCard
            href="/password-check"
            name="Mot de passe compromis"
            desc="Vérifiez si un mot de passe figure dans des jeux de données de fuites publics. Recherche par k-anonymat — votre mot de passe ne quitte jamais votre navigateur en entier."
            cta="Vérifier un mot de passe"
          >
            <template #icon>
              <svg class="w-5 h-5 text-brand-bright" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
              </svg>
            </template>
          </ToolCard>

          <ToolCard
            href="/ssl-check"
            name="Inspecteur de certificat SSL"
            desc="Inspectez les détails d'un certificat TLS, notez sa configuration SSL, vérifiez les dates d'expiration et examinez les Subject Alternative Names."
            cta="Inspecter le SSL"
          >
            <template #icon>
              <svg class="w-5 h-5 text-brand-bright" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
              </svg>
            </template>
          </ToolCard>

          <ToolCard
            href="/ip-check"
            name="Renseignement sur la réputation IP"
            desc="Géolocalisez n'importe quelle IP, consultez les signalements d'abus via AbuseIPDB, détectez l'usage de VPN/Tor/proxy et évaluez le score de menace."
            cta="Vérifier une IP"
          >
            <template #icon>
              <svg class="w-5 h-5 text-brand-bright" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
              </svg>
            </template>
          </ToolCard>

          <ToolCard
            href="/header-check"
            name="Analyseur d'en-têtes d'e-mail"
            desc="Analysez les en-têtes bruts d'un e-mail pour retracer les sauts de routage, vérifier l'authentification SPF/DKIM/DMARC et détecter les indices de phishing."
            cta="Analyser les en-têtes"
          >
            <template #icon>
              <svg class="w-5 h-5 text-brand-bright" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
              </svg>
            </template>
          </ToolCard>
        </div>

        <!-- CVE Alerts section -->
        <div class="glass-card p-10 text-center">
          <div class="w-14 h-14 rounded-md bg-brand/10 border border-brand/25 flex items-center justify-center mx-auto mb-6">
            <svg class="w-7 h-7 text-brand-bright" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
          </div>
          <h2 class="text-2xl font-bold text-white mb-3">Alertes de vulnérabilités CVE</h2>
          <p class="text-text-muted text-base leading-relaxed max-w-xl mx-auto mb-8">
            Abonnez-vous pour recevoir des alertes CVE en temps réel sur les technologies qui comptent pour vous — frameworks, CMS, bibliothèques et plus encore. Soyez notifié par e-mail dès qu'une vulnérabilité critique est divulguée.
          </p>
          <div class="flex flex-wrap items-center justify-center gap-6 mb-8">
            <div v-for="feat in cveFeatures" :key="feat" class="flex items-center gap-2 text-sm text-text-muted">
              <svg class="w-4 h-4 text-brand-bright" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              {{ feat }}
            </div>
          </div>
          <Link href="/register" class="inline-flex items-center gap-2 px-6 py-2.5 bg-brand text-white font-semibold rounded-md hover:bg-brand-bright transition-colors text-sm ring-1 ring-brand/40">
            Créer un compte gratuit
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
          </Link>
        </div>

      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import ToolCard from '@/Components/ToolCard.vue'

const cveFeatures = [
  'Scores CVSS en temps réel',
  'Digest instantané / quotidien / hebdomadaire',
  'Filtrage par technologie',
]
</script>
