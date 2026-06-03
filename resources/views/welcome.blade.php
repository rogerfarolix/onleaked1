@extends('layouts.public')

@section('title', 'Onleaked — Plateforme de renseignement cybersécurité')

@section('content')
    <!-- Hero -->
    <section class="relative pt-36 pb-24 px-6 overflow-hidden">
        <div class="grid-backdrop absolute inset-0 -z-10"></div>
        <div class="max-w-4xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-line bg-surface text-xs text-text-muted mb-8 fade-up">
                <span class="w-2 h-2 rounded-full bg-emerald-400 pulse-dot"></span>
                <span class="mono-label !text-[10px] text-text-muted">Sans pistage &bull; Données jamais conservées</span>
            </div>

            <h1 class="text-5xl md:text-7xl font-bold tracking-tight mb-6 fade-up leading-[1.05]" style="animation-delay:.1s">
                Reprenez le contrôle de<br>
                <span class="text-brand-bright">votre sécurité numérique</span>
            </h1>

            <p class="text-text-muted text-lg md:text-xl mb-12 max-w-2xl mx-auto fade-up" style="animation-delay:.2s">
                Surveillez les fuites, auditez vos domaines et suivez les vulnérabilités — sur une seule plateforme. Nous ne stockons jamais vos données.
            </p>

            <div class="flex flex-wrap items-center justify-center gap-3 fade-up" style="animation-delay:.3s">
                <a href="{{ route('leak-check') }}" class="px-7 py-3.5 bg-brand text-white font-semibold rounded-md hover:bg-brand-bright transition-colors text-sm ring-1 ring-brand/40">
                    Vérifier mon e-mail
                </a>
                <a href="{{ route('domain.show') }}" class="px-7 py-3.5 bg-surface border border-line text-text font-medium rounded-md hover:border-line-strong hover:bg-surface-2 transition-colors text-sm">
                    Analyser un domaine
                </a>
            </div>
        </div>
    </section>

    <!-- Stats bar -->
    <section class="py-10 px-6 border-y border-line" id="stats-bar">
        <div class="max-w-4xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <p class="text-3xl font-bold text-text font-mono" data-counter="15" data-suffix=" Md+">0</p>
                <p class="text-sm text-text-dim mt-1">enregistrements indexés</p>
            </div>
            <div>
                <p class="text-3xl font-bold text-text font-mono" data-counter="120" data-suffix="+">0</p>
                <p class="text-sm text-text-dim mt-1">plateformes scannées</p>
            </div>
            <div>
                <p class="text-3xl font-bold text-text font-mono" data-counter="6" data-suffix="">0</p>
                <p class="text-sm text-text-dim mt-1">outils de sécurité</p>
            </div>
            <div>
                <p class="text-3xl font-bold text-text font-mono">0</p>
                <p class="text-sm text-text-dim mt-1">octet de vos données stocké</p>
            </div>
        </div>
    </section>

    @push('scripts')
    <script nonce="{{ $cspNonce }}">
        (function() {
            const section = document.getElementById('stats-bar');
            if (!section) return;
            const counters = section.querySelectorAll('[data-counter]');
            let done = false;
            function animate() {
                if (done) return; done = true;
                counters.forEach(el => {
                    const target = parseInt(el.dataset.counter);
                    const suffix = el.dataset.suffix || '';
                    let v = 0;
                    const step = Math.max(1, Math.ceil(target / 40));
                    const id = setInterval(() => {
                        v = Math.min(v + step, target);
                        el.textContent = v + suffix;
                        if (v >= target) clearInterval(id);
                    }, 30);
                });
            }
            const obs = new IntersectionObserver(entries => {
                if (entries[0].isIntersecting) { animate(); obs.disconnect(); }
            }, { threshold: 0.3 });
            obs.observe(section);
        })();
    </script>
    @endpush

    <!-- Services -->
    <section class="py-24 px-6">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-16">
                <p class="mono-label text-brand-bright mb-3">// Services</p>
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Six outils puissants, une seule plateforme.</h2>
                <p class="text-text-muted max-w-xl mx-auto">Aucun compte requis pour les vérifications de sécurité. Gratuit, instantané, respectueux de votre vie privée.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-px bg-line rounded-lg overflow-hidden border border-line">
                @php
                    $cards = [
                        ['leak-check', 'Vérification de fuite', 'Vérifiez si votre e-mail a été exposé dans une fuite et découvrez votre empreinte numérique sur 120+ plateformes.', 'Vérifier gratuitement', 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'],
                        ['domain.show', 'Analyse de domaine', 'Renseignement complet : DNS, audit SPF/DMARC, énumération de sous-domaines et vérification de réputation.', 'Analyser maintenant', 'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9'],
                        ['password-check', 'Mot de passe compromis', 'Vérifiez si un mot de passe a été exposé via le k-anonymat. Votre mot de passe n\'est jamais envoyé en entier.', 'Vérifier un mot de passe', 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
                        ['ssl-check', 'Inspecteur SSL', 'Inspectez le certificat d\'un domaine — émetteur, validité, SAN, jours avant expiration et note de sécurité.', 'Inspecter le SSL', 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                        ['ip-check', 'Réputation IP', 'Géolocalisation, FAI, recherche ASN et score de confiance d\'abus issu du renseignement mondial sur les menaces.', 'Vérifier une IP', 'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3'],
                        ['register', 'Alertes CVE', 'Abonnez-vous à des technologies et recevez des alertes e-mail dès la publication de nouvelles CVE.', 'Commencer gratuitement', 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'],
                    ];
                @endphp

                @foreach($cards as $i => [$route, $title, $desc, $cta, $icon])
                    <a href="{{ route($route) }}" class="bg-surface p-7 hover:bg-surface-2 transition-colors group block relative">
                        <div class="w-11 h-11 rounded-md bg-brand/10 border border-brand/25 flex items-center justify-center mb-5 group-hover:bg-brand/20 transition-colors">
                            <svg class="w-5 h-5 text-brand-bright" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $icon }}"/></svg>
                        </div>
                        <h3 class="font-semibold text-lg mb-2 flex items-center gap-2">
                            {{ $title }}
                            @if($route === 'register')
                                <span class="mono-label !text-[9px] px-1.5 py-0.5 rounded bg-brand/20 text-brand-bright font-semibold">PRO</span>
                            @endif
                        </h3>
                        <p class="text-text-muted text-sm leading-relaxed mb-4">{{ $desc }}</p>
                        <span class="text-brand-bright text-sm font-medium flex items-center gap-1 group-hover:gap-2 transition-all">{{ $cta }} <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
                    </a>
                @endforeach
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
                @php
                    $steps = [
                        ['Saisissez une cible', 'E-mail, domaine, mot de passe, adresse IP ou en-têtes bruts. Aucun compte requis.'],
                        ['Nous interrogeons les sources OSINT', 'Vérifications en temps réel des bases de fuites, enregistrements DNS et journaux de transparence des certificats.'],
                        ['Recevez votre rapport complet', 'Résultats détaillés et instantanés avec des recommandations concrètes.'],
                    ];
                @endphp
                @foreach($steps as $n => [$t, $d])
                    <div class="flex flex-col items-center gap-4">
                        <div class="w-11 h-11 rounded-md bg-surface border border-brand/30 flex items-center justify-center text-lg font-bold text-brand-bright font-mono">{{ $n + 1 }}</div>
                        <h3 class="font-semibold">{{ $t }}</h3>
                        <p class="text-text-dim text-sm">{{ $d }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Privacy section -->
    <section class="py-24 px-6 border-t border-line">
        <div class="max-w-4xl mx-auto flex flex-col md:flex-row items-center gap-12">
            <div class="flex-1">
                <p class="mono-label text-brand-bright mb-3">// Vie privée d'abord</p>
                <h2 class="text-3xl font-bold mb-4">On vérifie vos données.<br>On ne les garde jamais.</h2>
                <p class="text-text-muted leading-relaxed mb-6">
                    Votre adresse e-mail n'existe qu'en mémoire le temps de la requête. Elle n'est jamais écrite sur disque, journalisée ni envoyée à des tiers au-delà des API de vérification de fuites. Chaque requête est éphémère par conception.
                </p>
                <ul class="space-y-3">
                    @foreach([
                        'Aucun compte nécessaire pour les vérifications',
                        'E-mails jamais stockés, journalisés ni mis en cache',
                        'API open source uniquement (XposedOrNot, crt.sh)',
                        'Limitation de débit pour prévenir les abus',
                    ] as $item)
                        <li class="flex items-center gap-3 text-sm text-text">
                            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            {{ $item }}
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="flex-1 flex justify-center">
                <div class="glass-card p-8 w-full max-w-sm text-center">
                    <div class="w-16 h-16 rounded-md bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-text mb-1">Zéro rétention de données</p>
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
                <a href="{{ route('leak-check') }}" class="px-7 py-3.5 bg-brand text-white font-semibold rounded-md hover:bg-brand-bright transition-colors text-sm ring-1 ring-brand/40">
                    Vérifier mon e-mail
                </a>
                <a href="{{ route('services') }}" class="px-7 py-3.5 bg-surface border border-line text-text font-medium rounded-md hover:border-line-strong hover:bg-surface-2 transition-colors text-sm">
                    Voir tous les services
                </a>
            </div>
        </div>
    </section>
@endsection
