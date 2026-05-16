@extends('layouts.app')
@section('title', 'Confidentialité — Onleaked')
@section('content')
<div style="max-width:720px;margin:0 auto;padding:80px 24px;">
    <h1 style="font-family:var(--font-display);font-size:2.2rem;font-weight:800;color:var(--c-white);margin-bottom:8px;letter-spacing:-0.03em;">
        Politique de confidentialité
    </h1>
    <p style="color:var(--c-muted);font-size:0.8rem;font-family:var(--font-mono);margin-bottom:40px;">Dernière mise à jour : {{ date('d/m/Y') }}</p>

    @foreach([
        ['🔒', 'Données collectées', 'Nous collectons uniquement l\'adresse email que vous saisissez, votre adresse IP (pour le rate-limiting anti-abus), et les résultats de l\'analyse. Votre email n\'est jamais stocké en clair — nous utilisons un hash SHA-256.'],
        ['🗃️', 'Durée de conservation', 'Les résultats d\'analyse sont conservés 6 heures en cache pour éviter de surcharger les APIs tierces. Passé ce délai, les données sont supprimées automatiquement.'],
        ['🌐', 'Sources tierces', 'Pour effectuer l\'analyse, votre email est transmis à des services tiers (EmailRep.io, BreachDirectory, Gravatar, etc.). Ces services ont leurs propres politiques de confidentialité.'],
        ['🚫', 'Ce que nous ne faisons pas', 'Nous ne vendons pas vos données. Nous n\'affichons aucune publicité. Nous ne créons pas de profil utilisateur. Nous ne partageons pas vos informations avec des tiers à des fins commerciales.'],
        ['🛡️', 'Sécurité', 'Toutes les communications sont chiffrées via HTTPS/TLS. Les résultats ne sont accessibles que via l\'URL unique générée lors de votre scan.'],
        ['📧', 'Contact', 'Pour toute question relative à vos données, contactez-nous à privacy@nealix.org'],
    ] as [$icon, $title, $text])
    <div style="margin-bottom:28px;">
        <h2 style="font-family:var(--font-display);font-size:1.1rem;font-weight:700;color:var(--c-white);margin-bottom:10px;display:flex;align-items:center;gap:10px;">
            <span>{{ $icon }}</span> {{ $title }}
        </h2>
        <p style="color:var(--c-muted);font-size:0.9rem;line-height:1.8;padding-left:28px;">{{ $text }}</p>
    </div>
    @endforeach

    <div style="margin-top:32px;text-align:center;">
        <a href="{{ route('home') }}" class="btn btn-ghost">← Retour à l'accueil</a>
    </div>
</div>
@endsection
