{{-- resources/views/about.blade.php --}}
@extends('layouts.app')
@section('title', 'À propos — Onleaked')
@section('content')
<div style="max-width:720px;margin:0 auto;padding:80px 24px;">
    <h1 style="font-family:var(--font-display);font-size:2.2rem;font-weight:800;color:var(--c-white);margin-bottom:16px;letter-spacing:-0.03em;">
        À propos d'Onleaked
    </h1>
    <p style="color:var(--c-muted);font-size:1rem;line-height:1.8;margin-bottom:24px;">
        <strong style="color:var(--c-text);">Onleaked</strong> est un outil de sensibilisation à la vie privée numérique,
        développé par <a href="https://nealix.org" style="color:var(--c-red-light);">Nealix</a>.
        Il permet à tout utilisateur de découvrir quelle est l'empreinte numérique de son adresse email
        sur internet — comptes détectés, fuites de données, réputation — en quelques secondes.
    </p>
    <p style="color:var(--c-muted);font-size:1rem;line-height:1.8;margin-bottom:24px;">
        Notre mission est simple : <strong style="color:var(--c-text);">la transparence au service de votre sécurité.</strong>
        Comme <a href="https://haveibeenpwned.com" target="_blank" style="color:var(--c-red-light);">HaveIBeenPwned</a>,
        nous agrégeons des sources publiques pour vous donner une vision claire de votre exposition.
    </p>
    <div style="background:var(--c-surface);border:1px solid var(--c-border);border-radius:var(--radius-lg);padding:24px;margin-top:32px;">
        <div style="font-family:var(--font-display);font-weight:700;color:var(--c-white);margin-bottom:12px;">Principes fondamentaux</div>
        <ul style="list-style:none;display:flex;flex-direction:column;gap:10px;">
            @foreach(['100% gratuit, sans publicité, sans inscription','Emails jamais stockés en clair (hash SHA-256 uniquement)','Code orienté confidentialité et sécurité by design','Outil de sensibilisation — non d\'attaque'] as $item)
            <li style="display:flex;align-items:center;gap:10px;font-size:0.88rem;color:var(--c-muted);">
                <span style="color:#4ade80;flex-shrink:0;">✓</span> {{ $item }}
            </li>
            @endforeach
        </ul>
    </div>
    <div style="margin-top:32px;text-align:center;">
        <a href="{{ route('home') }}" class="btn btn-primary">Lancer une analyse</a>
    </div>
</div>
@endsection
