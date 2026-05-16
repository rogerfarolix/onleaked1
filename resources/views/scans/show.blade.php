@extends('layouts.app')

@section('title', 'Analyse en cours — Onleaked')

@push('head-styles')
<style>
.scan-page {
    min-height: calc(100vh - 64px);
    padding: 60px 24px;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
}

/* ─── AMBIENT ─── */
.scan-page::before {
    content: '';
    position: fixed;
    top: 0; left: 50%;
    transform: translateX(-50%);
    width: 700px; height: 400px;
    background: radial-gradient(ellipse, rgba(135,35,35,0.12) 0%, transparent 70%);
    pointer-events: none;
    z-index: 0;
}

.scan-inner {
    width: 100%;
    max-width: 780px;
    position: relative;
    z-index: 1;
}

/* ─── EMAIL TARGET ─── */
.target-bar {
    background: var(--c-surface);
    border: 1px solid var(--c-border);
    border-radius: var(--radius);
    padding: 14px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 32px;
    font-family: var(--font-mono);
    font-size: 0.88rem;
    color: var(--c-text);
    animation: fade-in-up 0.4s ease both;
}

.target-dot {
    width: 8px; height: 8px;
    background: var(--c-red);
    border-radius: 50%;
    flex-shrink: 0;
    animation: pulse-red 1.5s infinite;
}

/* ─── LOADING STATE ─── */
.loading-card {
    background: var(--c-surface);
    border: 1px solid var(--c-border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    margin-bottom: 24px;
    animation: fade-in-up 0.5s 0.1s ease both;
}

.loading-header {
    padding: 28px 32px 0;
}

.loading-title {
    font-family: var(--font-display);
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--c-white);
    margin-bottom: 6px;
}

.loading-sub {
    font-size: 0.85rem;
    color: var(--c-muted);
    margin-bottom: 28px;
}

/* Scan visual terminal */
.scan-terminal {
    background: #0d0d10;
    border-top: 1px solid var(--c-border);
    padding: 24px 32px;
    font-family: var(--font-mono);
    font-size: 0.8rem;
    min-height: 220px;
    position: relative;
    overflow: hidden;
}

.scan-terminal::after {
    content: '';
    position: absolute;
    left: 0; right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, var(--c-red), transparent);
    animation: scan-line 2.5s linear infinite;
    opacity: 0.6;
}

.terminal-line {
    color: var(--c-muted);
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 10px;
    animation: fade-in-up 0.3s ease both;
}

.terminal-line.active { color: var(--c-text); }
.terminal-line.done   { color: #4ade80; }
.terminal-line.error  { color: #f87171; }

.terminal-prefix {
    color: var(--c-red-light);
    flex-shrink: 0;
}

.terminal-cursor {
    display: inline-block;
    width: 8px; height: 14px;
    background: var(--c-red-light);
    vertical-align: middle;
    animation: blink 1s step-end infinite;
    margin-left: 4px;
}

/* Progress bar */
.progress-wrap {
    padding: 0 32px 28px;
}

.progress-label {
    display: flex;
    justify-content: space-between;
    font-size: 0.75rem;
    color: var(--c-muted);
    margin-bottom: 8px;
    font-family: var(--font-mono);
}

.progress-bar {
    height: 4px;
    background: var(--c-surface2);
    border-radius: 2px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--c-red), #e05252);
    border-radius: 2px;
    transition: width 0.8s ease;
    width: 0%;
}

/* ─── RESULTS STATE ─── */
.results-header {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 28px;
    animation: fade-in-up 0.4s ease both;
}

.risk-badge {
    width: 72px; height: 72px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--font-display);
    font-size: 2rem;
    font-weight: 800;
    flex-shrink: 0;
    border: 3px solid;
    position: relative;
}

.results-meta h2 {
    font-family: var(--font-display);
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--c-white);
    margin-bottom: 4px;
}

.results-meta p {
    font-size: 0.85rem;
    color: var(--c-muted);
}

/* Stats row */
.stats-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}

.stat-box {
    background: var(--c-surface);
    border: 1px solid var(--c-border);
    border-radius: var(--radius);
    padding: 20px;
    text-align: center;
    animation: fade-in-up 0.4s ease both;
}

.stat-box-num {
    font-family: var(--font-display);
    font-size: 2rem;
    font-weight: 800;
    color: var(--c-white);
    display: block;
    letter-spacing: -0.03em;
}

.stat-box-num.danger { color: #f87171; }
.stat-box-num.warn   { color: #fbbf24; }
.stat-box-num.safe   { color: #4ade80; }

.stat-box-label {
    font-size: 0.72rem;
    color: var(--c-muted);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-top: 4px;
    display: block;
}

/* Source result cards */
.results-grid {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.result-card {
    background: var(--c-surface);
    border: 1px solid var(--c-border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    animation: fade-in-up 0.4s ease both;
    transition: border-color 0.2s;
}

.result-card:hover { border-color: var(--c-border2); }

.result-card-header {
    padding: 18px 24px;
    display: flex;
    align-items: center;
    gap: 14px;
    cursor: pointer;
    user-select: none;
}

.result-card-icon {
    width: 40px; height: 40px;
    background: var(--c-surface2);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.result-card-info { flex: 1; }

.result-card-name {
    font-family: var(--font-display);
    font-weight: 600;
    font-size: 0.95rem;
    color: var(--c-white);
}

.result-card-sub {
    font-size: 0.78rem;
    color: var(--c-muted);
    margin-top: 2px;
}

.result-card-badge {
    font-family: var(--font-mono);
    font-size: 0.7rem;
    padding: 4px 10px;
    border-radius: 100px;
    font-weight: 600;
}

.badge-found {
    background: rgba(239,68,68,0.15);
    color: #f87171;
    border: 1px solid rgba(239,68,68,0.3);
}

.badge-safe {
    background: rgba(74,222,128,0.1);
    color: #4ade80;
    border: 1px solid rgba(74,222,128,0.25);
}

.badge-warn {
    background: rgba(251,191,36,0.1);
    color: #fbbf24;
    border: 1px solid rgba(251,191,36,0.25);
}

.badge-info {
    background: var(--c-surface2);
    color: var(--c-muted);
    border: 1px solid var(--c-border);
}

.result-card-chevron {
    color: var(--c-muted);
    font-size: 0.8rem;
    transition: transform 0.2s;
}

.result-card-body {
    padding: 0 24px 20px;
    display: none;
    border-top: 1px solid var(--c-border);
    padding-top: 18px;
}

.result-card-body.open { display: block; }

/* Site list (Holehe) */
.site-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.site-chip {
    background: var(--c-surface2);
    border: 1px solid var(--c-border);
    border-radius: var(--radius);
    padding: 6px 12px;
    font-family: var(--font-mono);
    font-size: 0.75rem;
    color: var(--c-text);
    display: flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    transition: border-color 0.2s;
}

.site-chip:hover { border-color: var(--c-border2); }

.site-chip-dot {
    width: 6px; height: 6px;
    background: var(--c-red);
    border-radius: 50%;
    flex-shrink: 0;
}

/* Info rows */
.info-table {
    width: 100%;
    border-collapse: collapse;
}

.info-table tr { border-bottom: 1px solid var(--c-border); }
.info-table tr:last-child { border-bottom: none; }

.info-table td {
    padding: 10px 0;
    font-size: 0.82rem;
}

.info-table td:first-child {
    color: var(--c-muted);
    width: 40%;
    font-family: var(--font-mono);
    font-size: 0.75rem;
}

.info-table td:last-child {
    color: var(--c-text);
    text-align: right;
    font-weight: 500;
}

/* Gravatar */
.gravatar-wrap {
    display: flex;
    align-items: center;
    gap: 16px;
}

.gravatar-img {
    width: 64px; height: 64px;
    border-radius: 50%;
    border: 2px solid var(--c-border2);
    object-fit: cover;
}

/* Warnings / positives */
.summary-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 20px;
}

.summary-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    font-size: 0.85rem;
    padding: 10px 14px;
    border-radius: var(--radius);
}

.summary-item.warning {
    background: rgba(239,68,68,0.08);
    border: 1px solid rgba(239,68,68,0.2);
    color: #fca5a5;
}

.summary-item.positive {
    background: rgba(74,222,128,0.07);
    border: 1px solid rgba(74,222,128,0.2);
    color: #86efac;
}

.summary-item-icon { flex-shrink: 0; }

/* Failed state */
.failed-card {
    background: var(--c-surface);
    border: 1px solid rgba(239,68,68,0.3);
    border-radius: var(--radius-lg);
    padding: 40px;
    text-align: center;
}

/* New scan CTA */
.new-scan-cta {
    margin-top: 32px;
    text-align: center;
    animation: fade-in-up 0.5s ease both;
}

@media (max-width: 600px) {
    .stats-row { grid-template-columns: 1fr 1fr; }
    .results-header { flex-direction: column; text-align: center; }
}
</style>
@endpush

@section('content')
<div class="scan-page">
<div class="scan-inner">

    <!-- Target bar -->
    <div class="target-bar">
        <div class="target-dot" id="targetDot"></div>
        <span>Cible : </span>
        <strong>{{ $scan->maskedEmail() }}</strong>
        <span style="margin-left:auto;font-size:0.72rem;color:var(--c-muted);">
            {{ $scan->uuid }}
        </span>
    </div>

    {{-- ──────────── PENDING / RUNNING ──────────── --}}
    @if(in_array($scan->status->value, ['pending', 'running']))
    <div class="loading-card" id="loadingCard">
        <div class="loading-header">
            <div class="loading-title">⚡ Analyse en cours...</div>
            <div class="loading-sub">Interrogation des sources OSINT · Ne quittez pas cette page</div>
        </div>

        <div class="progress-wrap">
            <div class="progress-label">
                <span id="progressLabel">Initialisation...</span>
                <span id="progressPct">0%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill"></div>
            </div>
        </div>

        <div class="scan-terminal" id="terminal">
            <div class="terminal-line active">
                <span class="terminal-prefix">$</span>
                <span>onleaked scan --email {{ $scan->email_target }}</span>
                <span class="terminal-cursor"></span>
            </div>
        </div>
    </div>

    {{-- ──────────── COMPLETED ──────────── --}}
    @elseif($scan->isCompleted() && $scan->report)
    @php $report = $scan->report; $full = $report->full_report; @endphp

    <!-- Risk header -->
    <div class="results-header">
        <div class="risk-badge" style="color:{{ $report->riskColor() }};border-color:{{ $report->riskColor() }};background:{{ $report->riskColor() }}18">
            {{ $report->risk_level->value }}
        </div>
        <div class="results-meta">
            <h2>{{ $report->riskLabel() }}</h2>
            <p>Rapport généré en {{ $scan->duration() ?? '—' }}s · {{ $scan->completed_at?->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <!-- Summary warnings -->
    @if(!empty($full['summary']['warnings']) || !empty($full['summary']['positives']))
    <div class="summary-list" style="margin-bottom:24px;">
        @foreach($full['summary']['warnings'] ?? [] as $w)
            <div class="summary-item warning">
                <span class="summary-item-icon">⚠️</span>
                <span>{{ $w }}</span>
            </div>
        @endforeach
        @foreach($full['summary']['positives'] ?? [] as $p)
            <div class="summary-item positive">
                <span class="summary-item-icon">✅</span>
                <span>{{ $p }}</span>
            </div>
        @endforeach
    </div>
    @endif

    <!-- Stats -->
    <div class="stats-row">
        <div class="stat-box">
            <span class="stat-box-num {{ $report->accounts_found > 0 ? 'warn' : 'safe' }}">
                {{ $report->accounts_found }}
            </span>
            <span class="stat-box-label">Comptes détectés</span>
        </div>
        <div class="stat-box">
            <span class="stat-box-num {{ $report->breaches_found > 0 ? 'danger' : 'safe' }}">
                {{ $report->breaches_found }}
            </span>
            <span class="stat-box-label">Fuites détectées</span>
        </div>
        <div class="stat-box">
            <span class="stat-box-num" style="color:{{ $report->riskColor() }}">
                {{ $report->risk_level->value }}
            </span>
            <span class="stat-box-label">Niveau de risque</span>
        </div>
    </div>

    <!-- Source result cards -->
    <div class="results-grid">

        {{-- HOLEHE --}}
        @if(!empty($full['holehe']['sites']))
        <div class="result-card">
            <div class="result-card-header" onclick="toggleCard(this)">
                <div class="result-card-icon">🔍</div>
                <div class="result-card-info">
                    <div class="result-card-name">Holehe — Comptes détectés</div>
                    <div class="result-card-sub">{{ count($full['holehe']['sites']) }} service(s) sur {{ $full['holehe']['total_checked'] ?? '?' }} vérifiés</div>
                </div>
                <span class="result-card-badge badge-found">{{ count($full['holehe']['sites']) }} trouvés</span>
                <span class="result-card-chevron">▼</span>
            </div>
            <div class="result-card-body">
                <div class="site-list">
                    @foreach($full['holehe']['sites'] as $site)
                    <a href="{{ $site['url'] ?? '#' }}" target="_blank" rel="noopener" class="site-chip">
                        <span class="site-chip-dot"></span>
                        {{ $site['name'] }}
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- EMAILREP --}}
        @if(!empty($full['emailrep']))
        @php $er = $full['emailrep']; @endphp
        <div class="result-card">
            <div class="result-card-header" onclick="toggleCard(this)">
                <div class="result-card-icon">📊</div>
                <div class="result-card-info">
                    <div class="result-card-name">EmailRep.io — Réputation</div>
                    <div class="result-card-sub">Analyse de la réputation globale de l'adresse</div>
                </div>
                @php
                    $repBadge = match($er['reputation'] ?? 'unknown') {
                        'high'   => ['safe',  'Haute'],
                        'medium' => ['warn',  'Moyenne'],
                        'low'    => ['found', 'Faible'],
                        default  => ['info',  'Inconnue'],
                    };
                @endphp
                <span class="result-card-badge badge-{{ $repBadge[0] }}">{{ $repBadge[1] }}</span>
                <span class="result-card-chevron">▼</span>
            </div>
            <div class="result-card-body">
                <table class="info-table">
                    <tr><td>Réputation</td><td>{{ ucfirst($er['reputation'] ?? '—') }}</td></tr>
                    <tr><td>Suspecte</td><td>{{ ($er['suspicious'] ?? false) ? '⚠️ Oui' : '✅ Non' }}</td></tr>
                    <tr><td>Blacklisté</td><td>{{ ($er['blacklisted'] ?? false) ? '🚨 Oui' : '✅ Non' }}</td></tr>
                    <tr><td>Identifiants fuités</td><td>{{ ($er['credentials_leaked'] ?? false) ? '💥 Oui' : '✅ Non' }}</td></tr>
                    <tr><td>Fournisseur gratuit</td><td>{{ ($er['free_provider'] ?? false) ? 'Oui' : 'Non' }}</td></tr>
                    <tr><td>Email jetable</td><td>{{ ($er['disposable'] ?? false) ? '⚠️ Oui' : '✅ Non' }}</td></tr>
                    <tr><td>Vu pour la première fois</td><td>{{ $er['first_seen'] ?? '—' }}</td></tr>
                    <tr><td>Références</td><td>{{ $er['references'] ?? 0 }}</td></tr>
                </table>
                @if(!empty($er['profiles']))
                <div style="margin-top:14px;">
                    <div style="font-size:0.75rem;color:var(--c-muted);margin-bottom:8px;font-family:var(--font-mono);">PROFILS PUBLICS</div>
                    <div class="site-list">
                        @foreach($er['profiles'] as $profile)
                        <span class="site-chip">{{ $profile }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- BREACHDIRECTORY --}}
        @if(!empty($full['breachdirectory']))
        @php $bd = $full['breachdirectory']; @endphp
        <div class="result-card">
            <div class="result-card-header" onclick="toggleCard(this)">
                <div class="result-card-icon">💥</div>
                <div class="result-card-info">
                    <div class="result-card-name">BreachDirectory — Fuites</div>
                    <div class="result-card-sub">Mots de passe exposés dans des bases de données volées</div>
                </div>
                <span class="result-card-badge {{ ($bd['found'] ?? false) ? 'badge-found' : 'badge-safe' }}">
                    {{ ($bd['found'] ?? false) ? ($bd['count'] ?? 0).' fuite(s)' : 'Aucune fuite' }}
                </span>
                <span class="result-card-chevron">▼</span>
            </div>
            @if(!empty($bd['result']))
            <div class="result-card-body">
                <table class="info-table">
                    @foreach($bd['result'] as $breach)
                    <tr>
                        <td>Mot de passe masqué</td>
                        <td style="font-family:var(--font-mono)">{{ $breach['password'] ?? '—' }}</td>
                    </tr>
                    @endforeach
                </table>
                <p style="font-size:0.72rem;color:var(--c-muted);margin-top:12px;">
                    ⚠️ Les mots de passe sont partiellement masqués pour votre sécurité. Changez-les immédiatement si vous les reconnaissez.
                </p>
            </div>
            @endif
        </div>
        @endif

        {{-- GRAVATAR --}}
        @if(!empty($full['gravatar']))
        @php $gr = $full['gravatar']; @endphp
        <div class="result-card">
            <div class="result-card-header" onclick="toggleCard(this)">
                <div class="result-card-icon">👤</div>
                <div class="result-card-info">
                    <div class="result-card-name">Gravatar — Profil public</div>
                    <div class="result-card-sub">Avatar et informations de profil associés</div>
                </div>
                <span class="result-card-badge {{ ($gr['has_gravatar'] ?? false) ? 'badge-warn' : 'badge-safe' }}">
                    {{ ($gr['has_gravatar'] ?? false) ? 'Profil trouvé' : 'Aucun profil' }}
                </span>
                <span class="result-card-chevron">▼</span>
            </div>
            @if($gr['has_gravatar'] ?? false)
            <div class="result-card-body">
                <div class="gravatar-wrap">
                    <img src="{{ $gr['avatar_url'] }}" alt="Gravatar" class="gravatar-img" onerror="this.style.display='none'">
                    <div>
                        @if(!empty($gr['profile']['display_name']))
                        <div style="font-weight:600;color:var(--c-white);margin-bottom:4px;">{{ $gr['profile']['display_name'] }}</div>
                        @endif
                        @if(!empty($gr['profile']['username']))
                        <div style="font-family:var(--font-mono);font-size:0.8rem;color:var(--c-muted);">@{{ $gr['profile']['username'] }}</div>
                        @endif
                        @if(!empty($gr['profile']['location']))
                        <div style="font-size:0.8rem;color:var(--c-muted);margin-top:4px;">📍 {{ $gr['profile']['location'] }}</div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- DNS / WHOIS --}}
        @if(!empty($full['dns']))
        @php $dns = $full['dns']; @endphp
        <div class="result-card">
            <div class="result-card-header" onclick="toggleCard(this)">
                <div class="result-card-icon">🌐</div>
                <div class="result-card-info">
                    <div class="result-card-name">DNS / WHOIS — Infrastructure</div>
                    <div class="result-card-sub">Domaine : {{ $dns['domain'] ?? '—' }}</div>
                </div>
                <span class="result-card-badge {{ ($dns['is_disposable'] ?? false) ? 'badge-found' : 'badge-info' }}">
                    {{ ($dns['is_disposable'] ?? false) ? 'Jetable' : (($dns['is_free'] ?? false) ? 'Gratuit' : 'Professionnel') }}
                </span>
                <span class="result-card-chevron">▼</span>
            </div>
            <div class="result-card-body">
                <table class="info-table">
                    <tr><td>Domaine</td><td>{{ $dns['domain'] ?? '—' }}</td></tr>
                    <tr><td>Email jetable</td><td>{{ ($dns['is_disposable'] ?? false) ? '⚠️ Oui' : '✅ Non' }}</td></tr>
                    <tr><td>Fournisseur gratuit</td><td>{{ ($dns['is_free'] ?? false) ? 'Oui' : 'Non' }}</td></tr>
                    <tr><td>SPF</td><td style="font-size:0.72rem;word-break:break-all">{{ $dns['spf'] ? '✅ Configuré' : '❌ Absent' }}</td></tr>
                    <tr><td>DMARC</td><td>{{ $dns['dmarc'] ? '✅ Configuré' : '❌ Absent' }}</td></tr>
                    @if(!empty($dns['mx_records']))
                    <tr><td>MX Principal</td><td style="font-family:var(--font-mono);font-size:0.75rem">{{ $dns['mx_records'][0]['host'] ?? '—' }}</td></tr>
                    @endif
                    @if(!empty($dns['whois']['registrar']))
                    <tr><td>Registrar</td><td>{{ $dns['whois']['registrar'] }}</td></tr>
                    @endif
                    @if(!empty($dns['whois']['created_at']))
                    <tr><td>Créé le</td><td>{{ $dns['whois']['created_at'] }}</td></tr>
                    @endif
                    @if(!empty($dns['whois']['expires_at']))
                    <tr><td>Expire le</td><td>{{ $dns['whois']['expires_at'] }}</td></tr>
                    @endif
                </table>
            </div>
        </div>
        @endif

        {{-- HIBP --}}
        @if(!empty($full['hibp']))
        @php $hibp = $full['hibp']; @endphp
        <div class="result-card">
            <div class="result-card-header" onclick="toggleCard(this)">
                <div class="result-card-icon">🚨</div>
                <div class="result-card-info">
                    <div class="result-card-name">HaveIBeenPwned — Fuites vérifiées</div>
                    <div class="result-card-sub">Base de référence mondiale des fuites de données</div>
                </div>
                <span class="result-card-badge {{ ($hibp['found'] ?? false) ? 'badge-found' : 'badge-safe' }}">
                    {{ ($hibp['found'] ?? false) ? ($hibp['count'] ?? 0).' fuite(s) HIBP' : 'Aucune fuite' }}
                </span>
                <span class="result-card-chevron">▼</span>
            </div>
            @if(!empty($hibp['breaches']))
            <div class="result-card-body">
                <div style="margin-bottom:12px;font-size:0.78rem;color:var(--c-muted);font-family:var(--font-mono);">
                    SOURCES DE FUITES DÉTECTÉES
                </div>
                <div style="display:flex;flex-direction:column;gap:10px;">
                    @foreach($hibp['breaches'] as $breach)
                    <div style="background:var(--c-surface2);border:1px solid var(--c-border);border-radius:var(--radius);padding:12px 16px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                            <span style="font-family:var(--font-display);font-weight:600;font-size:0.9rem;color:var(--c-white);">{{ $breach['name'] }}</span>
                            @if(!empty($breach['breach_date']))
                            <span style="font-family:var(--font-mono);font-size:0.72rem;color:var(--c-muted);">{{ $breach['breach_date'] }}</span>
                            @endif
                        </div>
                        @if(!empty($breach['pwn_count']))
                        <div style="font-size:0.75rem;color:var(--c-muted);">
                            {{ number_format($breach['pwn_count']) }} comptes affectés
                        </div>
                        @endif
                        @if(!empty($breach['data_classes']))
                        <div style="margin-top:8px;display:flex;flex-wrap:wrap;gap:6px;">
                            @foreach(array_slice($breach['data_classes'], 0, 5) as $dc)
                            <span style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);color:#fca5a5;font-family:var(--font-mono);font-size:0.68rem;padding:2px 8px;border-radius:100px;">{{ $dc }}</span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                <p style="font-size:0.72rem;color:var(--c-muted);margin-top:12px;">
                    Source : <a href="https://haveibeenpwned.com" target="_blank" rel="noopener" style="color:var(--c-muted);text-decoration:underline;">haveibeenpwned.com</a> — Données vérifiées par Troy Hunt.
                </p>
            </div>
            @elseif($hibp['requires_api_key'] ?? false)
            <div class="result-card-body">
                <p style="font-size:0.82rem;color:var(--c-muted);">
                    🔑 La recherche complète HIBP nécessite une clé API. Configurez <code style="font-family:var(--font-mono)">HIBP_API_KEY</code> pour activer cette source.
                </p>
            </div>
            @endif
        </div>
        @endif

    </div><!-- /results-grid -->

    {{-- ──────────── FAILED ──────────── --}}
    @elseif($scan->isFailed())
    <div class="failed-card">
        <div style="font-size:3rem;margin-bottom:16px;">⚠️</div>
        <div style="font-family:var(--font-display);font-size:1.2rem;font-weight:700;color:var(--c-white);margin-bottom:8px;">
            Analyse échouée
        </div>
        <div style="font-size:0.85rem;color:var(--c-muted);margin-bottom:24px;">
            Une erreur s'est produite lors de l'analyse. Veuillez réessayer.
            @if($scan->error_message && app()->isLocal())
            {{-- Message d'erreur technique affiché uniquement en local/dev --}}
            <br><code style="font-family:var(--font-mono);font-size:0.75rem;color:#f87171;display:block;margin-top:8px;">{{ $scan->error_message }}</code>
            @endif
        </div>
        <a href="{{ route('home') }}" class="btn btn-primary">Nouvelle analyse</a>
    </div>
    @endif

    <!-- New scan CTA -->
    @if($scan->isCompleted())
    <div class="new-scan-cta">
        <a href="{{ route('home') }}" class="btn btn-ghost">
            ← Analyser une autre adresse
        </a>
    </div>
    @endif

</div>
</div>
@endsection

@push('scripts')
<script>
// ── Accordion ──
function toggleCard(header) {
    const body    = header.nextElementSibling;
    const chevron = header.querySelector('.result-card-chevron');
    if (!body) return;
    const isOpen = body.classList.toggle('open');
    chevron.style.transform = isOpen ? 'rotate(180deg)' : '';
}

// ── Auto-open first card ──
document.addEventListener('DOMContentLoaded', () => {
    const firstHeader = document.querySelector('.result-card-header');
    if (firstHeader) toggleCard(firstHeader);
});

// ── Live polling for pending/running ──
@if(in_array($scan->status->value, ['pending', 'running']))
(function() {
    const uuid      = '{{ $scan->uuid }}';
    const token     = '{{ $scan->access_token }}';
    const terminal  = document.getElementById('terminal');
    const fill      = document.getElementById('progressFill');
    const label     = document.getElementById('progressLabel');
    const pct       = document.getElementById('progressPct');
    const targetDot = document.getElementById('targetDot');

    const steps = [
        { pct: 5,  text: 'Initialisation du scan...',          type: 'active' },
        { pct: 15, text: 'Holehe → vérification sur 250+ sites...', type: 'active' },
        { pct: 35, text: 'EmailRep.io → analyse réputation...', type: 'active' },
        { pct: 50, text: 'BreachDirectory → recherche fuites...', type: 'active' },
        { pct: 65, text: 'Gravatar → profil public...', type: 'active' },
        { pct: 78, text: 'DNS/WHOIS → infrastructure domaine...', type: 'active' },
        { pct: 90, text: 'Agrégation des résultats...', type: 'active' },
        { pct: 97, text: 'Génération du rapport...', type: 'active' },
    ];

    let stepIdx  = 0;
    let pollTimer;

    function addLine(text, type) {
        const line = document.createElement('div');
        line.className = 'terminal-line ' + type;
        line.innerHTML = `<span class="terminal-prefix">›</span><span>${text}</span>`;
        terminal.appendChild(line);
        terminal.scrollTop = terminal.scrollHeight;
    }

    function advanceStep() {
        if (stepIdx >= steps.length) return;
        const s = steps[stepIdx++];
        fill.style.width  = s.pct + '%';
        label.textContent = s.text;
        pct.textContent   = s.pct + '%';
        addLine(s.text, s.type);
    }

    // Advance steps over time
    advanceStep();
    const stepTimer = setInterval(() => {
        if (stepIdx < steps.length) advanceStep();
        else clearInterval(stepTimer);
    }, 3500);

    // Poll status
    async function poll() {
        try {
            const res  = await fetch(`/scan/${uuid}/status?token=${token}`);
            const data = await res.json();

            if (data.completed) {
                clearInterval(stepTimer);
                fill.style.width  = '100%';
                label.textContent = 'Analyse terminée !';
                pct.textContent   = '100%';
                addLine('✅ Rapport généré avec succès', 'done');
                targetDot.style.background = '#4ade80';
                setTimeout(() => location.reload(), 1200);
            } else if (data.failed) {
                clearInterval(stepTimer);
                addLine('❌ Erreur lors de l\'analyse', 'error');
                targetDot.style.background = '#ef4444';
                setTimeout(() => location.reload(), 1500);
            } else {
                pollTimer = setTimeout(poll, 3000);
            }
        } catch(e) {
            pollTimer = setTimeout(poll, 5000);
        }
    }

    pollTimer = setTimeout(poll, 4000);
})();
@endif
</script>
@endpush
