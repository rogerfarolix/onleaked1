<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; background: #09090b; color: #e4e4e7; padding: 40px; font-size: 13px; }
        .header { border-bottom: 2px solid #8b5cf6; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 22px; font-weight: bold; color: #fff; }
        .logo span { color: #a78bfa; }
        .subtitle { color: #71717a; font-size: 11px; margin-top: 4px; }
        .score-section { background: #18181b; border: 1px solid #27272a; border-radius: 12px; padding: 24px; margin-bottom: 24px; text-align: center; }
        .score-label { font-size: 11px; color: #71717a; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
        .count { font-size: 42px; font-weight: bold; }
        .risk-safe    { color: #34d399; }
        .risk-low     { color: #fbbf24; }
        .risk-medium  { color: #f97316; }
        .risk-high    { color: #f87171; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; margin-top: 8px; }
        .badge-safe   { background: rgba(52,211,153,0.1); color: #34d399; border: 1px solid rgba(52,211,153,0.3); }
        .badge-low    { background: rgba(251,191,36,0.1); color: #fbbf24; border: 1px solid rgba(251,191,36,0.3); }
        .badge-medium { background: rgba(249,115,22,0.1); color: #f97316; border: 1px solid rgba(249,115,22,0.3); }
        .badge-high   { background: rgba(248,113,113,0.1); color: #f87171; border: 1px solid rgba(248,113,113,0.3); }
        .section { background: #18181b; border: 1px solid #27272a; border-radius: 12px; padding: 20px; margin-bottom: 16px; }
        .section-title { font-size: 12px; font-weight: bold; color: #a1a1aa; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; }
        .rec-item { padding: 6px 0; color: #a1a1aa; border-bottom: 1px solid #27272a; }
        .rec-item:last-child { border-bottom: none; }
        .footer { margin-top: 40px; padding-top: 16px; border-top: 1px solid #27272a; color: #52525b; font-size: 10px; text-align: center; }
        .privacy { background: rgba(139,92,246,0.1); border: 1px solid rgba(139,92,246,0.2); border-radius: 8px; padding: 12px 16px; margin-bottom: 20px; color: #a78bfa; font-size: 11px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">On<span>leaked</span></div>
        <div class="subtitle">Password Breach Report &mdash; Generated {{ now()->format('F j, Y') }}</div>
    </div>

    <div class="privacy">
        Privacy note: This report was generated using k-anonymity. Only the first 5 characters of the SHA-1 hash were
        ever sent to the Have I Been Pwned API. Your password was never transmitted in full.
    </div>

    <div class="score-section">
        <div class="score-label">Breach Count</div>
        <div class="count risk-{{ $risk }}">{{ $count > 0 ? number_format($count) . 'x' : 'Safe' }}</div>
        <div class="badge badge-{{ $risk }}">{{ strtoupper($risk_label) }}</div>
    </div>

    <div class="section">
        <div class="section-title">Recommendations</div>
        @if($risk === 'safe')
            <div class="rec-item">✓ This password was not found in any known data breach.</div>
            <div class="rec-item">✓ Continue using unique passwords for each service.</div>
            <div class="rec-item">✓ Use a password manager to generate strong passwords.</div>
        @else
            <div class="rec-item">! Change this password immediately on all services where it is used.</div>
            <div class="rec-item">! Never reuse this password anywhere.</div>
            <div class="rec-item">→ Enable two-factor authentication on all important accounts.</div>
            <div class="rec-item">→ Use a password manager to generate strong unique passwords.</div>
        @endif
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} Onleaked by Nealix. All rights reserved. &mdash; Privacy-first cybersecurity intelligence.
        Results are based on the Have I Been Pwned database and are provided for informational purposes only.
    </div>
</body>
</html>
