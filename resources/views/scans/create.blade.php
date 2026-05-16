@extends('layouts.app')

@section('title', 'Nouvelle analyse — Onleaked')

@section('content')
<div style="max-width:560px;margin:0 auto;padding:80px 24px;">

    <div style="text-align:center;margin-bottom:40px;">
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:800;color:var(--c-white);letter-spacing:-0.03em;margin-bottom:8px;">
            Analyser un email
        </h1>
        <p style="color:var(--c-muted);font-size:0.9rem;">
            Entrez l'adresse email à analyser. Le rapport sera disponible dans ~30 secondes.
        </p>
    </div>

    @if($errors->any())
    <div style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#fca5a5;border-radius:12px;padding:14px 18px;margin-bottom:20px;font-size:0.85rem;">
        {{ $errors->first() }}
    </div>
    @endif

    <div style="background:var(--c-surface);border:1px solid var(--c-border);border-radius:20px;padding:32px;position:relative;overflow:hidden;">
        <div style="position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,var(--c-red),transparent);opacity:0.6;"></div>

        <form action="{{ route('scans.submit') }}" method="POST" id="scanForm">
            @csrf

            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:0.78rem;color:var(--c-muted);font-family:var(--font-mono);letter-spacing:0.05em;margin-bottom:8px;text-transform:uppercase;">
                    Adresse email cible
                </label>
                <input
                    type="email"
                    name="email"
                    placeholder="exemple@domaine.com"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    style="width:100%;background:var(--c-surface2);border:1px solid var(--c-border);border-radius:12px;padding:14px 18px;font-family:var(--font-mono);font-size:0.9rem;color:var(--c-text);outline:none;transition:border-color 0.2s;box-sizing:border-box;"
                    onfocus="this.style.borderColor='var(--c-red)'"
                    onblur="this.style.borderColor='var(--c-border)'"
                >
            </div>

            <div style="margin-bottom:24px;">
                <label style="display:block;font-size:0.78rem;color:var(--c-muted);font-family:var(--font-mono);letter-spacing:0.05em;margin-bottom:8px;text-transform:uppercase;">
                    Email de notification (optionnel)
                </label>
                <input
                    type="email"
                    name="notify_email"
                    placeholder="Recevoir le rapport par email"
                    value="{{ old('notify_email') }}"
                    style="width:100%;background:var(--c-surface2);border:1px solid var(--c-border);border-radius:12px;padding:14px 18px;font-family:var(--font-mono);font-size:0.9rem;color:var(--c-text);outline:none;transition:border-color 0.2s;box-sizing:border-box;"
                    onfocus="this.style.borderColor='var(--c-red)'"
                    onblur="this.style.borderColor='var(--c-border)'"
                >
            </div>

            <button type="submit" id="scanBtn"
                style="width:100%;background:var(--c-red);color:#fff;font-family:var(--font-display);font-weight:700;font-size:0.95rem;padding:16px;border:none;border-radius:12px;cursor:pointer;transition:all 0.2s;display:flex;align-items:center;justify-content:center;gap:10px;">
                <span id="btnText">Lancer l'analyse</span>
                <span id="btnIcon">→</span>
            </button>
        </form>
    </div>

    <div style="margin-top:20px;text-align:center;">
        <p style="font-size:0.75rem;color:var(--c-muted);line-height:1.6;">
            🔒 Email hashé SHA-256 · Résultats disponibles 6h · Aucune inscription requise
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('scanForm').addEventListener('submit', function() {
    const btn  = document.getElementById('scanBtn');
    const text = document.getElementById('btnText');
    const icon = document.getElementById('btnIcon');
    btn.style.opacity = '0.7';
    btn.style.pointerEvents = 'none';
    text.textContent = 'Lancement en cours...';
    icon.innerHTML = '<div style="width:16px;height:16px;border:2px solid rgba(255,255,255,0.3);border-top-color:#fff;border-radius:50%;animation:spin 0.7s linear infinite;"></div>';
});
</script>
@endpush
