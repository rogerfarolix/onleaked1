@extends('layouts.app')

@section('title', 'Onleaked — Découvrez votre empreinte numérique')

@push('head-styles')
<style>
/* ─── HERO ─── */
.hero {
    min-height: calc(100vh - 64px);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 24px 80px;
    position: relative;
    overflow: hidden;
}

/* Ambient glow */
.hero::before {
    content: '';
    position: absolute;
    top: -200px; left: 50%;
    transform: translateX(-50%);
    width: 800px; height: 600px;
    background: radial-gradient(ellipse, rgba(135,35,35,0.18) 0%, transparent 70%);
    pointer-events: none;
}

.hero-eyebrow {
    font-family: var(--font-mono);
    font-size: 0.72rem;
    font-weight: 500;
    color: #e88888;
    background: var(--c-red-dim);
    border: 1px solid var(--c-border2);
    padding: 6px 14px;
    border-radius: 100px;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    margin-bottom: 28px;
    animation: fade-in-up 0.6s ease both;
}

.hero-title {
    font-family: var(--font-display);
    font-size: clamp(2.4rem, 6vw, 4.2rem);
    font-weight: 800;
    line-height: 1.1;
    letter-spacing: -0.03em;
    text-align: center;
    max-width: 700px;
    color: var(--c-white);
    margin-bottom: 20px;
    animation: fade-in-up 0.6s 0.1s ease both;
}

.hero-title span {
    color: var(--c-red-light);
    position: relative;
    display: inline-block;
}

.hero-sub {
    font-size: 1.05rem;
    color: var(--c-muted);
    text-align: center;
    max-width: 480px;
    line-height: 1.7;
    margin-bottom: 48px;
    animation: fade-in-up 0.6s 0.2s ease both;
}

/* ─── SCAN FORM ─── */
.scan-form-wrap {
    width: 100%;
    max-width: 560px;
    animation: fade-in-up 0.6s 0.3s ease both;
}

.scan-card {
    background: var(--c-surface);
    border: 1px solid var(--c-border);
    border-radius: var(--radius-lg);
    padding: 32px;
    position: relative;
    overflow: hidden;
}

.scan-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--c-red), transparent);
    opacity: 0.6;
}

.input-group {
    display: flex;
    gap: 12px;
    margin-bottom: 16px;
}

.email-input {
    flex: 1;
    background: var(--c-surface2);
    border: 1px solid var(--c-border);
    border-radius: var(--radius);
    padding: 14px 18px;
    font-family: var(--font-mono);
    font-size: 0.9rem;
    color: var(--c-text);
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
    -webkit-appearance: none;
}

.email-input::placeholder { color: var(--c-muted); }

.email-input:focus {
    border-color: var(--c-red);
    box-shadow: 0 0 0 3px var(--c-red-dim);
}

.email-input.error {
    border-color: #ef4444;
    box-shadow: 0 0 0 3px rgba(239,68,68,0.15);
}

.btn-scan {
    background: var(--c-red);
    color: #fff;
    font-family: var(--font-display);
    font-weight: 700;
    font-size: 0.9rem;
    padding: 14px 24px;
    border: none;
    border-radius: var(--radius);
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 8px;
    position: relative;
    overflow: hidden;
}

.btn-scan:hover {
    background: var(--c-red-light);
    transform: translateY(-1px);
    box-shadow: 0 8px 24px var(--c-red-glow);
}

.btn-scan:active { transform: translateY(0); }

.btn-scan.loading {
    pointer-events: none;
    opacity: 0.8;
}

.spinner {
    width: 16px; height: 16px;
    border: 2px solid rgba(255,255,255,0.3);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
}

.scan-privacy-note {
    font-size: 0.75rem;
    color: var(--c-muted);
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

/* ─── STATS BAR ─── */
.stats-bar {
    display: flex;
    gap: 40px;
    margin-top: 56px;
    animation: fade-in-up 0.6s 0.5s ease both;
}

.stat-item {
    text-align: center;
}

.stat-num {
    font-family: var(--font-display);
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--c-white);
    display: block;
    letter-spacing: -0.03em;
}

.stat-label {
    font-size: 0.72rem;
    color: var(--c-muted);
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

.stat-divider {
    width: 1px;
    background: var(--c-border);
}

/* ─── SOURCES SCROLL TICKER ─── */
.sources-ticker-wrap {
    width: 100%;
    overflow: hidden;
    padding: 20px 0;
    border-top: 1px solid var(--c-border);
    border-bottom: 1px solid var(--c-border);
    background: var(--c-surface);
    margin-top: 80px;
    position: relative;
    z-index: 1;
}

.sources-ticker-inner {
    display: flex;
    gap: 0;
    animation: ticker 20s linear infinite;
    width: max-content;
}

.source-tag {
    font-family: var(--font-mono);
    font-size: 0.75rem;
    color: var(--c-muted);
    padding: 0 32px;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 8px;
}

.source-tag::before {
    content: '';
    width: 6px; height: 6px;
    background: var(--c-red);
    border-radius: 50%;
    flex-shrink: 0;
}

@keyframes ticker {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

/* ─── HOW IT WORKS ─── */
.section {
    padding: 80px 24px;
    position: relative;
    z-index: 1;
}

.section-title {
    font-family: var(--font-display);
    font-size: 1.8rem;
    font-weight: 700;
    letter-spacing: -0.02em;
    margin-bottom: 8px;
    color: var(--c-white);
}

.section-sub {
    color: var(--c-muted);
    font-size: 0.9rem;
    margin-bottom: 48px;
}

.steps-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
}

.step-card {
    background: var(--c-surface);
    border: 1px solid var(--c-border);
    border-radius: var(--radius-lg);
    padding: 28px;
    transition: border-color 0.2s, transform 0.2s;
    position: relative;
    overflow: hidden;
}

.step-card:hover {
    border-color: var(--c-border2);
    transform: translateY(-2px);
}

.step-num {
    font-family: var(--font-mono);
    font-size: 0.7rem;
    color: var(--c-red-light);
    letter-spacing: 0.1em;
    margin-bottom: 12px;
}

.step-icon {
    font-size: 1.8rem;
    margin-bottom: 14px;
    display: block;
}

.step-title {
    font-family: var(--font-display);
    font-weight: 700;
    font-size: 1rem;
    color: var(--c-white);
    margin-bottom: 8px;
}

.step-desc {
    font-size: 0.85rem;
    color: var(--c-muted);
    line-height: 1.6;
}

/* ─── SOURCES GRID ─── */
.sources-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 14px;
}

.source-card {
    background: var(--c-surface);
    border: 1px solid var(--c-border);
    border-radius: var(--radius);
    padding: 18px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    transition: border-color 0.2s;
}

.source-card:hover { border-color: var(--c-border2); }

.source-card-icon { font-size: 1.4rem; }

.source-card-name {
    font-family: var(--font-mono);
    font-size: 0.78rem;
    font-weight: 500;
    color: var(--c-text);
}

.source-card-desc {
    font-size: 0.72rem;
    color: var(--c-muted);
    line-height: 1.5;
}

/* ─── ERROR BLOCK ─── */
.error-block {
    background: rgba(239,68,68,0.08);
    border: 1px solid rgba(239,68,68,0.25);
    border-radius: var(--radius);
    padding: 12px 16px;
    margin-bottom: 16px;
    font-size: 0.83rem;
    color: #fca5a5;
}

@media (max-width: 600px) {
    .input-group { flex-direction: column; }
    .stats-bar { gap: 24px; }
    .stat-num { font-size: 1.4rem; }
}
</style>
@endpush

@section('content')

<!-- ── HERO ── -->
<section class="hero">
    <div class="hero-eyebrow">🔍 Analyse de réputation email · 100% Gratuit</div>

    <h1 class="hero-title">
        Votre email est-il<br><span>exposé sur internet&nbsp;?</span>
    </h1>

    <p class="hero-sub">
        Entrez votre adresse email et découvrez en quelques secondes
        sur quels services elle apparaît, si elle a été compromise,
        et quelle est votre empreinte numérique réelle.
    </p>

    <div class="scan-form-wrap">
        <div class="scan-card">
            @if($errors->any())
                <div class="error-block">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('scans.submit') }}" method="POST" id="scanForm">
                @csrf
                <div class="input-group">
                    <input
                        type="email"
                        name="email"
                        id="emailInput"
                        class="email-input @error('email') error @enderror"
                        placeholder="votre@email.com"
                        required
                        autocomplete="email"
                        value="{{ old('email') }}"
                    >
                    <button type="submit" class="btn-scan" id="scanBtn">
                        <span id="btnText">Analyser</span>
                        <span id="btnIcon">→</span>
                    </button>
                </div>
                <p class="scan-privacy-note">
                    🔒 Votre email est hashé et jamais stocké en clair. Résultats disponibles 6h.
                </p>
            </form>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-bar">
        <div class="stat-item">
            <span class="stat-num" data-count="10">10+</span>
            <span class="stat-label">Sources analysées</span>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
            <span class="stat-num">~30s</span>
            <span class="stat-label">Temps d'analyse</span>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
            <span class="stat-num">100%</span>
            <span class="stat-label">Gratuit</span>
        </div>
    </div>
</section>

<!-- ── SOURCES TICKER ── -->
<div class="sources-ticker-wrap">
    <div class="sources-ticker-inner">
        @php
            $sources = ['Holehe','EmailRep.io','BreachDirectory','Gravatar','DNS/WHOIS','HaveIBeenPwned','Hunter.io','IntelX','Phonebook.cz','GHunt'];
            $items = array_merge($sources, $sources); // duplicate for infinite scroll
        @endphp
        @foreach($items as $src)
            <span class="source-tag">{{ $src }}</span>
        @endforeach
    </div>
</div>

<!-- ── HOW IT WORKS ── -->
<section class="section">
    <div class="container">
        <div class="section-title">Comment ça fonctionne</div>
        <div class="section-sub">Trois étapes, une analyse complète.</div>

        <div class="steps-grid">
            <div class="step-card">
                <div class="step-num">01</div>
                <span class="step-icon">✉️</span>
                <div class="step-title">Entrez votre email</div>
                <div class="step-desc">Aucune inscription requise. Entrez simplement votre adresse email dans le champ de recherche.</div>
            </div>
            <div class="step-card">
                <div class="step-num">02</div>
                <span class="step-icon">⚡</span>
                <div class="step-title">Analyse automatique</div>
                <div class="step-desc">Notre système interroge simultanément plus de 10 sources OSINT spécialisées en arrière-plan.</div>
            </div>
            <div class="step-card">
                <div class="step-num">03</div>
                <span class="step-icon">📊</span>
                <div class="step-title">Rapport détaillé</div>
                <div class="step-desc">Recevez un rapport complet : comptes détectés, fuites, réputation, infrastructure domaine.</div>
            </div>
            <div class="step-card">
                <div class="step-num">04</div>
                <span class="step-icon">🛡️</span>
                <div class="step-title">Protégez-vous</div>
                <div class="step-desc">Utilisez le rapport pour renforcer votre sécurité en ligne et réduire votre exposition.</div>
            </div>
        </div>
    </div>
</section>

<!-- ── SOURCES ── -->
<section class="section" style="padding-top: 0;">
    <div class="container">
        <div class="section-title">Sources analysées</div>
        <div class="section-sub">Données croisées depuis les meilleures bases de renseignement.</div>

        <div class="sources-grid">
            <div class="source-card">
                <div class="source-card-icon">🔍</div>
                <div class="source-card-name">Holehe</div>
                <div class="source-card-desc">Détection de comptes sur 250+ services</div>
            </div>
            <div class="source-card">
                <div class="source-card-icon">📊</div>
                <div class="source-card-name">EmailRep.io</div>
                <div class="source-card-desc">Réputation, activité malveillante</div>
            </div>
            <div class="source-card">
                <div class="source-card-icon">💥</div>
                <div class="source-card-name">BreachDirectory</div>
                <div class="source-card-desc">Fuites de mots de passe masqués</div>
            </div>
            <div class="source-card">
                <div class="source-card-icon">👤</div>
                <div class="source-card-name">Gravatar</div>
                <div class="source-card-desc">Profil public associé</div>
            </div>
            <div class="source-card">
                <div class="source-card-icon">🌐</div>
                <div class="source-card-name">DNS / WHOIS</div>
                <div class="source-card-desc">Infrastructure du domaine email</div>
            </div>
            <div class="source-card">
                <div class="source-card-icon">🚨</div>
                <div class="source-card-name">HaveIBeenPwned</div>
                <div class="source-card-desc">Base de référence mondiale</div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
document.getElementById('scanForm').addEventListener('submit', function() {
    const btn  = document.getElementById('scanBtn');
    const text = document.getElementById('btnText');
    const icon = document.getElementById('btnIcon');

    btn.classList.add('loading');
    text.textContent = 'Lancement...';
    icon.innerHTML = '<div class="spinner"></div>';
});
</script>
@endpush
