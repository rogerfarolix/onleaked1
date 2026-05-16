<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Onleaked — Découvrez votre empreinte numérique')</title>
    <meta name="description" content="@yield('meta_description', 'Analysez l\'exposition de votre email sur internet. Comptes détectés, fuites de données, réputation — en quelques secondes.')">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Mono:ital,wght@0,400;0,500;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --c-red:        #872323;
            --c-red-light:  #a62b2b;
            --c-red-dim:    rgba(135,35,35,0.15);
            --c-red-glow:   rgba(135,35,35,0.4);
            --c-bg:         #0a0a0c;
            --c-surface:    #111116;
            --c-surface2:   #18181f;
            --c-border:     rgba(255,255,255,0.07);
            --c-border2:    rgba(135,35,35,0.3);
            --c-text:       #e8e8ef;
            --c-muted:      #6b6b7e;
            --c-white:      #ffffff;
            --font-display: 'Syne', sans-serif;
            --font-body:    'Inter', sans-serif;
            --font-mono:    'DM Mono', monospace;
            --radius:       12px;
            --radius-lg:    20px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        body {
            font-family: var(--font-body);
            background: var(--c-bg);
            color: var(--c-text);
            min-height: 100vh;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Noise texture overlay */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
            opacity: 0.4;
        }

        /* ─── NAV ─── */
        nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            background: rgba(10,10,12,0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--c-border);
        }

        .nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-logo {
            font-family: var(--font-display);
            font-weight: 800;
            font-size: 1.35rem;
            color: var(--c-white);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: -0.02em;
        }

        .nav-logo-icon {
            width: 32px; height: 32px;
            background: var(--c-red);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 32px;
            list-style: none;
        }

        .nav-links a {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--c-muted);
            text-decoration: none;
            transition: color 0.2s;
        }
        .nav-links a:hover { color: var(--c-text); }

        .nav-badge {
            background: var(--c-red-dim);
            border: 1px solid var(--c-border2);
            color: #e88888;
            font-size: 0.7rem;
            font-weight: 600;
            font-family: var(--font-mono);
            padding: 3px 8px;
            border-radius: 100px;
            letter-spacing: 0.05em;
        }

        /* ─── MAIN WRAPPER ─── */
        .page-wrapper {
            position: relative;
            z-index: 1;
            padding-top: 64px;
        }

        /* ─── FOOTER ─── */
        footer {
            position: relative;
            z-index: 1;
            border-top: 1px solid var(--c-border);
            padding: 32px 24px;
            text-align: center;
        }

        footer p {
            font-size: 0.8rem;
            color: var(--c-muted);
        }

        footer a {
            color: var(--c-muted);
            text-decoration: none;
            transition: color 0.2s;
        }
        footer a:hover { color: var(--c-text); }

        /* ─── UTILITIES ─── */
        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .text-red  { color: var(--c-red-light); }
        .text-mono { font-family: var(--font-mono); }
        .text-muted { color: var(--c-muted); }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-family: var(--font-display);
            font-weight: 600;
            font-size: 0.9rem;
            padding: 12px 24px;
            border-radius: var(--radius);
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            letter-spacing: 0.01em;
        }

        .btn-primary {
            background: var(--c-red);
            color: #fff;
        }
        .btn-primary:hover {
            background: var(--c-red-light);
            transform: translateY(-1px);
            box-shadow: 0 8px 24px var(--c-red-glow);
        }

        .btn-ghost {
            background: transparent;
            color: var(--c-muted);
            border: 1px solid var(--c-border);
        }
        .btn-ghost:hover {
            background: var(--c-surface2);
            color: var(--c-text);
        }

        /* ─── CARD ─── */
        .card {
            background: var(--c-surface);
            border: 1px solid var(--c-border);
            border-radius: var(--radius-lg);
            padding: 28px;
        }

        .card-red {
            background: var(--c-red-dim);
            border-color: var(--c-border2);
        }

        /* ─── ALERTS ─── */
        .alert-error {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.3);
            color: #fca5a5;
            border-radius: var(--radius);
            padding: 12px 16px;
            font-size: 0.875rem;
        }

        /* ─── PULSE ANIMATION ─── */
        @keyframes pulse-red {
            0%, 100% { box-shadow: 0 0 0 0 var(--c-red-glow); }
            50%       { box-shadow: 0 0 0 12px transparent; }
        }

        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes scan-line {
            0%   { top: 0%; }
            100% { top: 100%; }
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0; }
        }

        /* ─── SCROLLBAR ─── */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--c-bg); }
        ::-webkit-scrollbar-thumb { background: var(--c-surface2); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--c-red); }

        @media (max-width: 640px) {
            .nav-links { display: none; }
        }
    </style>

    @stack('head-styles')
</head>
<body>

<nav>
    <div class="nav-inner">
        <a href="{{ route('home') }}" class="nav-logo">
            <div class="nav-logo-icon">🔍</div>
            Onleaked
        </a>
        <ul class="nav-links">
            <li><a href="{{ route('home') }}">Accueil</a></li>
            <li><a href="{{ route('about') }}">À propos</a></li>
            <li><a href="{{ route('privacy') }}">Confidentialité</a></li>
            <li><span class="nav-badge">100% GRATUIT</span></li>
        </ul>
    </div>
</nav>

<div class="page-wrapper">
    @yield('content')
</div>

<footer>
    <p>
        <a href="{{ route('home') }}">Onleaked</a> · 
        <a href="{{ route('privacy') }}">Politique de confidentialité</a> · 
        <a href="{{ route('about') }}">À propos</a>
        <br><br>
        Outil de sensibilisation à la vie privée numérique. 
        Nous ne stockons pas votre adresse email en clair. 
        © {{ date('Y') }} Onleaked by <a href="https://nealix.org" target="_blank">Nealix</a>
    </p>
</footer>

@stack('scripts')
</body>
</html>
