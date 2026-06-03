<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; background: #09090b; color: #e4e4e7; padding: 40px; font-size: 13px; }
        .header { border-bottom: 2px solid #0ea5e9; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 22px; font-weight: bold; color: #fff; }
        .logo span { color: #a78bfa; }
        .subtitle { color: #71717a; font-size: 11px; margin-top: 4px; }
        .grade-box { background: #18181b; border: 1px solid #27272a; border-radius: 12px; padding: 24px; margin-bottom: 24px; display: flex; align-items: center; gap: 24px; }
        .grade { font-size: 56px; font-weight: 900; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; border-radius: 16px; }
        .grade-A { background: rgba(52,211,153,0.1); color: #34d399; border: 2px solid rgba(52,211,153,0.3); }
        .grade-B { background: rgba(251,191,36,0.1); color: #fbbf24; border: 2px solid rgba(251,191,36,0.3); }
        .grade-C { background: rgba(249,115,22,0.1); color: #f97316; border: 2px solid rgba(249,115,22,0.3); }
        .grade-F { background: rgba(248,113,113,0.1); color: #f87171; border: 2px solid rgba(248,113,113,0.3); }
        .grade-info h2 { font-size: 18px; color: #fff; margin-bottom: 4px; }
        .grade-info p { color: #71717a; font-size: 12px; }
        .section { background: #18181b; border: 1px solid #27272a; border-radius: 12px; padding: 20px; margin-bottom: 16px; }
        .section-title { font-size: 12px; font-weight: bold; color: #a1a1aa; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 8px 0; border-bottom: 1px solid #27272a; }
        td:first-child { color: #71717a; width: 40%; }
        td:last-child { color: #e4e4e7; font-family: monospace; font-size: 12px; }
        tr:last-child td { border-bottom: none; }
        .sans-grid { display: flex; flex-wrap: wrap; gap: 8px; }
        .san { background: #27272a; padding: 3px 8px; border-radius: 4px; font-family: monospace; font-size: 11px; color: #a1a1aa; }
        .footer { margin-top: 40px; padding-top: 16px; border-top: 1px solid #27272a; color: #52525b; font-size: 10px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">On<span>leaked</span></div>
        <div class="subtitle">SSL Certificate Report for {{ $domain }} &mdash; Generated {{ now()->format('F j, Y') }}</div>
    </div>

    <div class="grade-box">
        <div class="grade grade-{{ $ssl['grade'] ?? 'F' }}">{{ $ssl['grade'] ?? 'F' }}</div>
        <div class="grade-info">
            <h2>{{ $domain }}</h2>
            <p>
                @if(($ssl['grade'] ?? 'F') === 'A') Excellent Certificate is valid and healthy
                @elseif(($ssl['grade'] ?? 'F') === 'B') Good Expiring within 30 days
                @elseif(($ssl['grade'] ?? 'F') === 'C') Warning Certificate expiring very soon
                @else Critical Certificate expired or connection failed
                @endif
            </p>
            @if(isset($ssl['days_left']))
                <p style="margin-top:8px; font-size:13px; {{ $ssl['days_left'] > 30 ? 'color:#34d399' : ($ssl['days_left'] >= 7 ? 'color:#fbbf24' : 'color:#f87171') }}">
                    {{ $ssl['days_left'] }} days remaining
                </p>
            @endif
        </div>
    </div>

    <div class="section">
        <div class="section-title">Certificate Details</div>
        <table>
            <tr><td>Subject (CN)</td><td>{{ $ssl['subject_cn'] ?? '—' }}</td></tr>
            <tr><td>Issuer (CN)</td><td>{{ $ssl['issuer_cn'] ?? '—' }}</td></tr>
            <tr><td>Issuer (Org)</td><td>{{ $ssl['issuer_org'] ?? '—' }}</td></tr>
            <tr><td>Valid From</td><td>{{ $ssl['valid_from'] ?? '—' }}</td></tr>
            <tr><td>Valid To</td><td>{{ $ssl['valid_to'] ?? '—' }}</td></tr>
            <tr><td>Serial Number</td><td>{{ $ssl['serial'] ?? '—' }}</td></tr>
        </table>
    </div>

    @if(!empty($ssl['sans']))
    <div class="section">
        <div class="section-title">Subject Alternative Names ({{ count($ssl['sans']) }})</div>
        <div class="sans-grid">
            @foreach($ssl['sans'] as $san)
                <span class="san">{{ $san }}</span>
            @endforeach
        </div>
    </div>
    @endif

    <div class="footer">
        &copy; {{ date('Y') }} Onleaked by Nealix. All rights reserved. &mdash; Privacy-first cybersecurity intelligence.
    </div>
</body>
</html>
