<template>
  <PublicLayout title="Vérification de fuite & empreinte numérique">
    <div class="pt-28 pb-20 px-6">
      <div class="max-w-4xl mx-auto">

        <div class="text-center mb-12 fade-up">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-brand/25 bg-brand/10 text-brand-bright mb-6">
            <span class="w-2 h-2 rounded-full bg-emerald-400 pulse-dot"></span>
            <span class="mono-label text-[10px]! text-brand-bright">Sans pistage &bull; E-mail jamais conservé</span>
          </div>
          <h1 class="text-4xl md:text-5xl font-bold tracking-tight mb-4">
            Votre e-mail a-t-il été <span class="text-brand-bright">compromis ?</span>
          </h1>
          <p class="text-text-muted text-lg max-w-2xl mx-auto">
            Vérifiez les fuites de données et découvrez votre empreinte numérique complète sur 120+ plateformes. Nous ne stockons, ne journalisons et ne pistons jamais vos données.
          </p>
        </div>

        <!-- Search bar -->
        <div class="max-w-2xl mx-auto mb-16 fade-up" style="animation-delay:.1s">
          <div aria-hidden="true" style="position:absolute;left:-9999px;overflow:hidden;height:1px;width:1px">
            <input type="text" v-model="honeypot" autocomplete="off" tabindex="-1">
          </div>
          <div class="glass-card rounded-lg p-2 glow-input transition-all duration-300">
            <form @submit.prevent="checkEmail" class="flex items-center gap-2">
              <div class="flex-1 flex items-center gap-3 px-4">
                <svg class="w-5 h-5 text-text-dim shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <input v-model="email" type="email" placeholder="Saisissez votre adresse e-mail…"
                  class="w-full bg-transparent border-none outline-none text-white placeholder-text-dim py-3 text-base focus:ring-0" required>
              </div>
              <button type="submit" :disabled="loading"
                class="px-6 py-3 bg-brand text-white font-semibold rounded-md hover:bg-brand-bright transition-colors ring-1 ring-brand/40 disabled:opacity-50 disabled:cursor-not-allowed shrink-0 text-sm">
                <span v-if="!loading">Vérifier</span>
                <span v-else class="flex items-center gap-2">
                  <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                  Analyse…
                </span>
              </button>
            </form>
          </div>
          <div v-if="error" class="mt-4 p-4 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-sm text-center">{{ error }}</div>
        </div>

        <!-- Results -->
        <div v-if="results" class="fade-up" style="animation-delay:.2s">

          <!-- Score card -->
          <div class="glass-card rounded-lg p-5 mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
              <p class="text-text-muted text-sm mb-1">Score de sécurité</p>
              <div class="flex items-center gap-3">
                <div class="text-4xl font-bold font-mono" :class="scoreColor">
                  {{ score }}<span class="text-xl text-text-dim">/100</span>
                </div>
                <span v-if="results.risk?.label" class="px-2.5 py-1 rounded-full text-xs font-semibold" :class="xonRiskCls(results.risk.label)">
                  Risque {{ riskLabelFr(results.risk.label) }}
                </span>
              </div>
              <p class="text-sm mt-1 text-text-dim">{{ scoreLabel }}</p>
            </div>
            <div class="flex gap-2">
              <button @click="downloadCsv" :disabled="csvLoading"
                class="px-4 py-2 bg-white/5 border border-line text-text font-semibold rounded-lg hover:bg-white/10 transition-all text-sm flex items-center gap-2 disabled:opacity-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                {{ csvLoading ? '…' : 'CSV' }}
              </button>
              <button @click="downloadPdf" :disabled="pdfLoading"
                class="px-4 py-2 bg-brand text-white font-semibold rounded-md hover:bg-brand-bright transition-colors ring-1 ring-brand/40 text-sm flex items-center gap-2 disabled:opacity-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                {{ pdfLoading ? 'Génération…' : 'Télécharger le PDF' }}
              </button>
            </div>
          </div>

          <!-- Tabs -->
          <div class="flex border-b border-line mb-6">
            <button @click="tab = 'breaches'"
              class="px-6 py-3 border-b-2 font-medium text-sm transition-colors flex items-center gap-2"
              :class="tab === 'breaches' ? 'border-brand-bright text-brand-bright' : 'border-transparent text-text-muted hover:text-white'">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
              Fuites de données
              <span v-if="results.breaches?.length" class="px-1.5 py-0.5 rounded-full bg-red-500/20 text-red-400 text-xs">{{ results.breaches.length }}</span>
            </button>
            <button @click="tab = 'footprint'"
              class="px-6 py-3 border-b-2 font-medium text-sm transition-colors flex items-center gap-2"
              :class="tab === 'footprint' ? 'border-brand-bright text-brand-bright' : 'border-transparent text-text-muted hover:text-white'">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3"/></svg>
              Empreinte numérique
              <span v-if="footprintAccounts.length" class="px-1.5 py-0.5 rounded-full bg-brand/20 text-brand-bright text-xs">{{ footprintAccounts.length }}</span>
            </button>
          </div>

          <!-- Breaches tab -->
          <div v-show="tab === 'breaches'">
            <div v-if="results.breaches?.length">
              <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-full bg-red-500/10 border border-red-500/20 flex items-center justify-center shrink-0">
                  <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                </div>
                <div>
                  <p class="font-semibold text-red-400">E-mail compromis</p>
                  <p class="text-text-dim text-sm">Trouvé dans <span class="text-red-400 font-bold">{{ results.breaches.length }}</span> fuite(s) de données connue(s)</p>
                </div>
              </div>

              <!-- Summary stats -->
              <div v-if="results.summary" class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                <div class="glass-card rounded-lg p-4">
                  <p class="text-2xl font-bold font-mono text-text">{{ results.breaches.length }}</p>
                  <p class="text-xs text-text-dim mt-0.5">fuites</p>
                </div>
                <div class="glass-card rounded-lg p-4">
                  <p class="text-2xl font-bold font-mono text-text">{{ formatNumber(results.summary.total_records) }}</p>
                  <p class="text-xs text-text-dim mt-0.5">comptes exposés</p>
                </div>
                <div class="glass-card rounded-lg p-4">
                  <p class="text-2xl font-bold font-mono" :class="results.summary.with_passwords ? 'text-red-400' : 'text-emerald-400'">{{ results.summary.with_passwords }}</p>
                  <p class="text-xs text-text-dim mt-0.5">avec mot de passe</p>
                </div>
                <div class="glass-card rounded-lg p-4">
                  <p class="text-2xl font-bold font-mono text-text">{{ results.summary.data_types_count }}</p>
                  <p class="text-xs text-text-dim mt-0.5">types de données</p>
                </div>
              </div>

              <!-- Exposed data categories -->
              <div v-if="results.summary?.data_types?.length" class="glass-card rounded-lg p-4 mb-6">
                <p class="mono-label text-text-dim mb-3">Données exposées au total</p>
                <div class="flex flex-wrap gap-2">
                  <span v-for="d in results.summary.data_types" :key="d"
                    class="px-2.5 py-1 rounded-full text-xs border"
                    :class="isSensitiveData(d) ? 'bg-red-500/10 text-red-400 border-red-500/20' : 'bg-white/5 text-text-muted border-line'">
                    {{ d }}
                  </span>
                </div>
              </div>

              <!-- Breach cards -->
              <div class="space-y-3">
                <div v-for="(b, i) in paginatedBreaches" :key="i" class="glass-card rounded-lg p-5">
                  <div class="flex items-start gap-4">
                    <img v-if="b.logo" :src="b.logo" :alt="b.source" class="w-10 h-10 rounded-lg object-contain bg-white/5 p-1 shrink-0" @error="$event.target.style.display='none'">
                    <div v-else class="w-10 h-10 rounded-lg bg-red-500/10 flex items-center justify-center shrink-0 text-red-400 font-bold text-sm">{{ b.source?.charAt(0) }}</div>
                    <div class="flex-1 min-w-0">
                      <!-- Title + badges -->
                      <div class="flex items-start justify-between gap-2 mb-1.5 flex-wrap">
                        <div class="flex items-center gap-2 flex-wrap">
                          <h4 class="font-semibold text-white">{{ b.source }}</h4>
                          <span v-if="b.verified" class="inline-flex items-center gap-1 text-[10px] px-1.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            Vérifiée
                          </span>
                          <span v-if="b.sensitive" class="text-[10px] px-1.5 py-0.5 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20">Sensible</span>
                        </div>
                        <span v-if="b.date" class="text-xs text-text-dim shrink-0">{{ formatDate(b.date) }}</span>
                      </div>

                      <!-- Meta -->
                      <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-text-dim mb-3">
                        <span v-if="b.domain" class="font-mono">{{ b.domain }}</span>
                        <span v-if="b.industry">{{ b.industry }}</span>
                        <span v-if="b.records" class="text-text-muted"><span class="font-mono">{{ formatNumber(b.records) }}</span> comptes exposés</span>
                      </div>

                      <!-- Description -->
                      <p class="text-text-muted text-sm leading-relaxed mb-3">{{ b.description }}</p>

                      <!-- Exposed data + password risk -->
                      <div class="flex flex-wrap items-center gap-1.5">
                        <span v-if="pwRisk(b.password_risk)" class="text-[10px] px-1.5 py-0.5 rounded font-semibold" :class="pwRisk(b.password_risk).cls">
                          {{ pwRisk(b.password_risk).label }}
                        </span>
                        <span v-for="d in b.exposed_data" :key="d"
                          class="text-[11px] px-2 py-0.5 rounded-full border"
                          :class="isSensitiveData(d) ? 'bg-red-500/10 text-red-400 border-red-500/20' : 'bg-white/5 text-text-dim border-line'">{{ d }}</span>
                      </div>

                      <!-- Reference -->
                      <a v-if="b.reference_url" :href="b.reference_url" target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center gap-1 text-xs text-brand-bright hover:text-white transition-colors mt-3">
                        En savoir plus
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
              <div v-if="totalBreachPages > 1" class="flex items-center justify-center gap-2 mt-6">
                <button @click="breachPage = Math.max(1, breachPage - 1)" :disabled="breachPage === 1" class="px-3 py-1.5 rounded-md bg-white/5 text-text-muted text-sm disabled:opacity-30 hover:bg-white/10 transition-colors">&larr; Préc.</button>
                <span class="text-text-dim text-sm">Page {{ breachPage }} sur {{ totalBreachPages }}</span>
                <button @click="breachPage = Math.min(totalBreachPages, breachPage + 1)" :disabled="breachPage === totalBreachPages" class="px-3 py-1.5 rounded-md bg-white/5 text-text-muted text-sm disabled:opacity-30 hover:bg-white/10 transition-colors">Suiv. &rarr;</button>
              </div>
            </div>
            <div v-else class="flex flex-col items-center gap-3 py-10">
              <div class="w-16 h-16 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center">
                <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
              </div>
              <p class="font-semibold text-emerald-400 text-lg">Aucune fuite trouvée</p>
              <p class="text-text-dim text-sm">Votre e-mail n'apparaît dans aucune fuite de données connue.</p>
            </div>

            <!-- Pastes -->
            <div v-if="results.pastes && results.pastes.count > 0" class="glass-card rounded-lg p-5 mt-4">
              <div class="flex items-center gap-2 mb-3">
                <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <p class="font-semibold text-text">Apparitions sur des sites de paste</p>
                <span class="px-1.5 py-0.5 rounded-full bg-amber-500/20 text-amber-400 text-xs">{{ results.pastes.count }}</span>
              </div>
              <p v-if="!results.pastes.items.length" class="text-text-dim text-sm">
                Votre e-mail a été retrouvé dans <span class="text-amber-400 font-semibold">{{ results.pastes.count }}</span> collage(s) public(s) de données.
              </p>
              <div v-else class="space-y-2">
                <div v-for="(p, i) in results.pastes.items" :key="i" class="flex items-center justify-between gap-3 bg-surface-2 rounded-md px-3 py-2">
                  <span class="text-sm text-text font-mono truncate">{{ p.source }}</span>
                  <span v-if="p.date" class="text-xs text-text-dim shrink-0">{{ formatDate(p.date) }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Footprint tab -->
          <div v-show="tab === 'footprint'">
            <div v-if="footprintPending" class="flex flex-col items-center gap-4 py-10">
              <div class="w-16 h-16 rounded-full bg-brand/10 border border-brand/20 flex items-center justify-center">
                <svg class="w-8 h-8 text-brand-bright animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              </div>
              <p class="font-semibold text-brand-bright">Analyse de 120+ plateformes…</p>
              <p class="text-text-dim text-sm">Les résultats s'afficheront automatiquement, cela peut prendre jusqu'à 60 secondes.</p>
            </div>
            <div v-else-if="hasFootprint">

              <!-- Identity profile (Gravatar) -->
              <div v-if="footprintProfile" class="glass-card rounded-lg p-5 mb-4">
                <div class="flex items-start gap-4">
                  <img v-if="footprintProfile.avatar_url" :src="footprintProfile.avatar_url" :alt="footprintProfile.display_name || 'avatar'" class="w-16 h-16 rounded-lg object-cover shrink-0 bg-white/5">
                  <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                      <h3 class="font-semibold text-white text-lg">{{ footprintProfile.display_name || 'Profil public' }}</h3>
                      <span class="mono-label text-[9px]! px-1.5 py-0.5 rounded bg-brand/15 text-brand-bright">Gravatar</span>
                    </div>
                    <p v-if="footprintProfile.job_title || footprintProfile.company" class="text-sm text-text-muted mt-0.5">
                      {{ [footprintProfile.job_title, footprintProfile.company].filter(Boolean).join(' · ') }}
                    </p>
                    <p v-if="footprintProfile.location" class="text-xs text-text-dim mt-1 flex items-center gap-1">
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                      {{ footprintProfile.location }}
                    </p>
                    <p v-if="footprintProfile.description" class="text-sm text-text-muted mt-2 leading-relaxed">{{ footprintProfile.description }}</p>
                    <div v-if="footprintProfile.accounts?.length" class="flex flex-wrap gap-2 mt-3">
                      <a v-for="a in footprintProfile.accounts" :key="a.url" :href="a.url" target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-surface-2 border border-line text-xs text-text hover:border-brand/30 hover:text-white transition-colors">
                        <img v-if="a.icon" :src="a.icon" :alt="a.label" class="w-3.5 h-3.5">
                        {{ a.label }}
                      </a>
                    </div>
                  </div>
                  <a v-if="footprintProfile.profile_url" :href="footprintProfile.profile_url" target="_blank" rel="noopener noreferrer" class="text-xs text-brand-bright hover:text-white shrink-0">Voir →</a>
                </div>
              </div>

              <!-- Reputation (EmailRep) -->
              <div v-if="footprintReputation" class="glass-card rounded-lg p-5 mb-4">
                <div class="flex items-center justify-between gap-3 mb-3 flex-wrap">
                  <div class="flex items-center gap-2">
                    <p class="font-semibold text-text">Réputation de l'e-mail</p>
                    <span class="mono-label text-[9px]! px-1.5 py-0.5 rounded bg-brand/15 text-brand-bright">EmailRep</span>
                  </div>
                  <span class="px-2.5 py-1 rounded-full text-xs font-semibold" :class="repCls(footprintReputation)">
                    {{ footprintReputation.suspicious ? 'Suspect' : repFr(footprintReputation.reputation) }}
                  </span>
                </div>
                <div class="flex flex-wrap gap-2">
                  <span v-if="footprintReputation.data_breach" class="text-[11px] px-2 py-0.5 rounded-full bg-red-500/10 text-red-400 border border-red-500/20">Présent dans une fuite</span>
                  <span v-if="footprintReputation.credentials_leaked" class="text-[11px] px-2 py-0.5 rounded-full bg-red-500/10 text-red-400 border border-red-500/20">Identifiants divulgués</span>
                  <span v-if="footprintReputation.malicious_activity" class="text-[11px] px-2 py-0.5 rounded-full bg-red-500/10 text-red-400 border border-red-500/20">Activité malveillante</span>
                  <span v-if="footprintReputation.blacklisted" class="text-[11px] px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20">Sur liste noire</span>
                  <span v-if="footprintReputation.references" class="text-[11px] px-2 py-0.5 rounded-full bg-white/5 text-text-dim border border-line">{{ footprintReputation.references }} référence(s)</span>
                  <span v-if="footprintReputation.first_seen && footprintReputation.first_seen !== 'never'" class="text-[11px] px-2 py-0.5 rounded-full bg-white/5 text-text-dim border border-line">Vu depuis {{ footprintReputation.first_seen }}</span>
                </div>
              </div>

              <!-- Accounts -->
              <template v-if="footprintAccounts.length">
                <div class="flex items-center gap-3 mb-4">
                  <div class="w-10 h-10 rounded-full bg-brand/10 border border-brand/20 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-brand-bright" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3"/></svg>
                  </div>
                  <div>
                    <p class="font-semibold text-brand-bright">Comptes associés</p>
                    <p class="text-text-dim text-sm">Cet e-mail est lié à <span class="text-brand-bright font-bold">{{ footprintAccounts.length }}</span> service(s) détecté(s)</p>
                  </div>
                </div>

                <!-- Summary stats -->
                <div class="grid grid-cols-3 gap-3 mb-4">
                  <div class="glass-card rounded-lg p-4">
                    <p class="text-2xl font-bold font-mono text-text">{{ footprintAccounts.length }}</p>
                    <p class="text-xs text-text-dim mt-0.5">comptes détectés</p>
                  </div>
                  <div class="glass-card rounded-lg p-4">
                    <p class="text-2xl font-bold font-mono text-text">{{ footprintGroups.length }}</p>
                    <p class="text-xs text-text-dim mt-0.5">catégories</p>
                  </div>
                  <div class="glass-card rounded-lg p-4">
                    <p class="text-sm font-medium text-text leading-tight pt-1">{{ (results.footprint.sources || []).join(', ') || '—' }}</p>
                    <p class="text-xs text-text-dim mt-1">sources OSINT</p>
                  </div>
                </div>

                <!-- Grouped by category -->
                <div class="space-y-5">
                  <div v-for="group in footprintGroups" :key="group.category">
                    <div class="flex items-center gap-2 mb-2.5">
                      <p class="mono-label text-text-dim">{{ group.category }}</p>
                      <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-white/5 text-text-dim">{{ group.accounts.length }}</span>
                      <div class="flex-1 h-px bg-line"></div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                      <a v-for="acc in group.accounts" :key="acc.url"
                        :href="acc.url" target="_blank" rel="noopener noreferrer"
                        class="glass-card rounded-lg p-3.5 flex items-center gap-3 hover:border-brand/30 hover:bg-surface-2 transition-all group">
                        <img :src="`https://www.google.com/s2/favicons?sz=64&domain=${acc.domain || acc.url}`" :alt="acc.name" class="w-7 h-7 rounded shrink-0 bg-white/5"
                          @error="$event.target.src='data:image/svg+xml,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'%23872323\'><circle cx=\'12\' cy=\'12\' r=\'10\'/></svg>'">
                        <div class="min-w-0 flex-1">
                          <p class="text-sm text-text truncate font-medium">{{ acc.name || siteName(acc.domain) }}</p>
                          <p class="text-[11px] text-text-dim truncate font-mono">{{ acc.domain }}</p>
                        </div>
                        <svg class="w-3.5 h-3.5 text-text-dim opacity-0 group-hover:opacity-100 transition-opacity shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                      </a>
                    </div>
                  </div>
                </div>

                <p class="text-xs text-text-dim mt-5 flex items-start gap-2">
                  <svg class="w-3.5 h-3.5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  Un compte détecté signifie qu'un compte associé à cet e-mail (ou au pseudo dérivé) existe sur la plateforme. Les résultats issus du pseudo peuvent contenir quelques faux positifs. Pensez à sécuriser ou supprimer les comptes inutilisés.
                </p>
              </template>
            </div>
            <div v-else class="flex flex-col items-center gap-3 py-10">
              <div class="w-16 h-16 rounded-full bg-zinc-500/10 border border-zinc-500/20 flex items-center justify-center">
                <svg class="w-8 h-8 text-text-dim" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z M9 12h6"/></svg>
              </div>
              <p class="font-semibold text-text-muted">Aucun compte associé trouvé</p>
              <p class="text-text-dim text-sm">Nous avons vérifié 120+ plateformes sans trouver cet e-mail enregistré.</p>
            </div>
          </div>

        </div>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { apiPost, downloadBlob } from '@/Composables/useApi'

const email     = ref('')
const honeypot  = ref('')
const loading   = ref(false)
const pdfLoading = ref(false)
const csvLoading = ref(false)
const results   = ref(null)
const error     = ref(null)
const tab       = ref('breaches')
const breachPage = ref(1)
const perPage   = 8
const footprintJobId  = ref(null)
const footprintPending = ref(false)
let   pollInterval = null

const score = computed(() => {
  if (!results.value) return 100
  let s = 100
  const b = results.value.breaches ?? []
  s -= Math.min(50, b.length * 15)
  if (b.some(x => x.password_leaked)) s -= 20
  return Math.max(0, s)
})

// --- Display helpers for breach details ---
const numberFmt = new Intl.NumberFormat('fr-FR')
function formatNumber(n) {
  if (n === null || n === undefined) return '—'
  return numberFmt.format(n)
}
function formatDate(iso) {
  if (!iso) return ''
  const d = new Date(iso)
  if (isNaN(d)) return ''
  return d.toLocaleDateString('fr-FR', { month: 'short', year: 'numeric' })
}
const PW_RISK = {
  plaintext:   { label: 'Mots de passe en clair',  cls: 'bg-red-500/15 text-red-400' },
  easytocrack: { label: 'Mots de passe vulnérables', cls: 'bg-red-500/15 text-red-400' },
  hardtocrack: { label: 'Mots de passe hachés (robustes)', cls: 'bg-amber-500/15 text-amber-400' },
  hashed:      { label: 'Mots de passe hachés', cls: 'bg-amber-500/15 text-amber-400' },
}
function pwRisk(risk) {
  return PW_RISK[risk] ?? null
}
function isSensitiveData(d) {
  const s = (d || '').toLowerCase()
  return /password|mot de passe|credit|bancaire|bank|carte|cvv|social security|sécurité sociale|passport|passeport|biometr|biométr|token|jeton|private message|message privé|health|santé|pin/.test(s)
}

// --- Digital footprint: categorisation of detected services ---
const FOOTPRINT_CATEGORIES = [
  ['Réseaux sociaux',       ['twitter', 'x.com', 'instagram', 'facebook', 'snapchat', 'pinterest', 'tiktok', 'vk.com', 'tumblr', 'flickr', 'badoo', 'mastodon', 'threads', 'bluesky', 'myspace', 'foursquare', 'nextdoor', 'weibo']],
  ['Messagerie & forums',   ['discord', 'telegram', 'reddit', 'quora', 'disqus', 'stackoverflow', 'stackexchange', 'slack', 'line.me', 'viber', 'wechat', 'skype', 'imgur']],
  ['Tech & développement',  ['github', 'gitlab', 'bitbucket', 'adobe', 'atlassian', 'docker', 'wordpress', 'gravatar', 'codepen', 'digitalocean', 'heroku', 'figma', 'replit', 'about.me']],
  ['Streaming & média',     ['spotify', 'deezer', 'soundcloud', 'lastfm', 'last.fm', 'netflix', 'twitch', 'vimeo', 'youtube', 'crunchyroll', 'mixcloud', 'bandcamp', 'tidal', 'imdb']],
  ['E-commerce',            ['amazon', 'ebay', 'aliexpress', 'etsy', 'rakuten', 'wish', 'shopify', 'vinted', 'leboncoin', 'cdiscount', 'asos', 'zalando', 'samsclub']],
  ['Jeux vidéo',            ['steam', 'roblox', 'epicgames', 'ea.com', 'origin', 'minecraft', 'chess.com', 'xbox', 'playstation', 'riotgames', 'ubisoft']],
  ['Finance & crypto',      ['paypal', 'coinbase', 'revolut', 'binance', 'kraken', 'wise', 'n26', 'venmo', 'cashapp', 'stripe']],
  ['Voyage & livraison',    ['airbnb', 'booking', 'deliveroo', 'ubereats', 'uber', 'lyft', 'tripadvisor', 'expedia', 'kayak', 'justeat']],
  ['Productivité',          ['evernote', 'trello', 'notion', 'dropbox', 'microsoft', 'office', 'zoom', 'asana', 'todoist', 'box.com', 'protonmail', 'google', 'mailru']],
  ['Rencontres',            ['tinder', 'bumble', 'okcupid', 'happn', 'meetic', 'grindr', 'hinge']],
]
function categorize(domain) {
  const d = (domain || '').toLowerCase()
  for (const [cat, keys] of FOOTPRINT_CATEGORIES) {
    if (keys.some(k => d.includes(k))) return cat
  }
  return 'Autres services'
}
// Footprint is now an aggregated object { profile, reputation, accounts[], sources[] }
const footprintProfile    = computed(() => results.value?.footprint?.profile ?? null)
const footprintReputation = computed(() => results.value?.footprint?.reputation ?? null)
const footprintAccounts   = computed(() => results.value?.footprint?.accounts ?? [])
const hasFootprint        = computed(() => !!(footprintProfile.value || footprintReputation.value || footprintAccounts.value.length))

const footprintGroups = computed(() => {
  const map = {}
  for (const acc of footprintAccounts.value) {
    const c = categorize(acc.domain || acc.url || '')
    ;(map[c] ||= []).push(acc)
  }
  return Object.entries(map)
    .map(([category, list]) => ({ category, accounts: [...list].sort((a, b) => (a.name || a.domain || '').localeCompare(b.name || b.domain || '')) }))
    .sort((a, b) => b.accounts.length - a.accounts.length || a.category.localeCompare(b.category))
})
function siteName(domain) {
  const base = (domain || '').split('.')[0].replace(/[-_]/g, ' ')
  return base.charAt(0).toUpperCase() + base.slice(1)
}

// EmailRep reputation display
function repFr(rep) {
  return { high: 'Bonne', medium: 'Moyenne', low: 'Faible', none: '—' }[rep] ?? (rep || '—')
}
function repCls(r) {
  if (r.suspicious || r.malicious_activity || r.blacklisted) return 'bg-red-500/20 text-red-400'
  if (r.reputation === 'high') return 'bg-emerald-500/20 text-emerald-400'
  if (r.reputation === 'medium') return 'bg-amber-500/20 text-amber-400'
  return 'bg-white/5 text-text-muted'
}

// XposedOrNot official risk label
function riskLabelFr(label) {
  return { Critical: 'critique', High: 'élevé', Medium: 'moyen', Low: 'faible' }[label] ?? (label || '').toLowerCase()
}
function xonRiskCls(label) {
  if (label === 'Critical' || label === 'High') return 'bg-red-500/20 text-red-400'
  if (label === 'Medium') return 'bg-amber-500/20 text-amber-400'
  return 'bg-emerald-500/20 text-emerald-400'
}
const scoreColor = computed(() => {
  const s = score.value
  return s >= 80 ? 'text-emerald-400' : s >= 50 ? 'text-amber-400' : 'text-red-400'
})
const scoreLabel = computed(() => {
  const s = score.value
  return s >= 80 ? 'Risque faible aucune menace significative détectée' : s >= 50 ? 'Risque moyen une certaine exposition détectée' : 'Risque élevé action immédiate recommandée'
})
const paginatedBreaches = computed(() => {
  if (!results.value?.breaches) return []
  const start = (breachPage.value - 1) * perPage
  return results.value.breaches.slice(start, start + perPage)
})
const totalBreachPages = computed(() => {
  if (!results.value?.breaches) return 1
  return Math.ceil(results.value.breaches.length / perPage)
})

async function checkEmail() {
  loading.value = true; results.value = null; error.value = null
  breachPage.value = 1; tab.value = 'breaches'
  footprintJobId.value = null; footprintPending.value = false
  if (pollInterval) clearInterval(pollInterval)

  try {
    const data = await apiPost('/check-email', { email: email.value, website: honeypot.value })
    if (data.status === 'error') { error.value = data.message; return }
    results.value = data
    if (data.footprint_job_id) {
      footprintJobId.value = data.footprint_job_id
      footprintPending.value = true
      pollInterval = setInterval(pollFootprint, 2000)
    }
  } catch { error.value = 'Erreur de connexion. Veuillez réessayer.' }
  finally { loading.value = false }
}

async function pollFootprint() {
  try {
    const data = await (await fetch(`/footprint-status/${footprintJobId.value}`)).json()
    if (data.status === 'done') {
      clearInterval(pollInterval)
      footprintPending.value = false
      if (results.value) results.value.footprint = data.data ?? null
    } else if (data.status === 'error') {
      clearInterval(pollInterval)
      footprintPending.value = false
    }
  } catch {}
}

async function downloadPdf() {
  pdfLoading.value = true
  try { await downloadBlob('/pdf/leak-check', { email: email.value, results: results.value }, 'onleaked-rapport-fuites.pdf') }
  catch { error.value = 'Impossible de générer le PDF.' }
  finally { pdfLoading.value = false }
}

async function downloadCsv() {
  csvLoading.value = true
  try { await downloadBlob('/csv/leak-check', { email: email.value, results: results.value }, 'onleaked-rapport-fuites.csv') }
  catch { error.value = 'Impossible de générer le CSV.' }
  finally { csvLoading.value = false }
}
</script>
