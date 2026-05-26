<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color: #18181b; background: #fff; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #7c3aed; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 22px; font-weight: 700; color: #7c3aed; }
        .logo span { color: #f43f5e; }
        .meta { font-size: 11px; color: #71717a; text-align: right; }
        h2 { font-size: 18px; font-weight: 700; color: #18181b; margin-bottom: 6px; }
        h3 { font-size: 14px; font-weight: 600; color: #3f3f46; margin: 24px 0 10px; }
        .section { background: #f4f4f5; border-radius: 8px; padding: 16px; margin-bottom: 16px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
        .badge-red { background: #fee2e2; color: #dc2626; }
        .badge-green { background: #d1fae5; color: #059669; }
        .badge-amber { background: #fef3c7; color: #d97706; }
        .breach-item { background: #fff; border: 1px solid #e4e4e7; border-radius: 6px; padding: 12px; margin-bottom: 8px; }
        .breach-name { font-weight: 600; font-size: 13px; }
        .breach-meta { font-size: 11px; color: #71717a; margin-top: 4px; }
        .footprint-grid { display: flex; flex-wrap: wrap; gap: 6px; }
        .footprint-item { background: #ede9fe; color: #7c3aed; padding: 3px 10px; border-radius: 20px; font-size: 11px; }
        .score-box { text-align: center; padding: 20px; background: #faf5ff; border-radius: 8px; border: 2px solid #7c3aed; margin-bottom: 24px; }
        .score-number { font-size: 48px; font-weight: 800; }
        .score-red { color: #dc2626; }
        .score-amber { color: #d97706; }
        .score-green { color: #059669; }
        .privacy-note { font-size: 11px; color: #71717a; border-top: 1px solid #e4e4e7; padding-top: 16px; margin-top: 24px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 6px 8px; border-bottom: 1px solid #f4f4f5; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="logo">On<span>leaked</span></div>
            <div style="font-size:11px; color:#71717a; margin-top:4px;">Cybersecurity Intelligence Platform</div>
        </div>
        <div class="meta">
            <div>Email Breach Report</div>
            <div>Generated: {{ $date }}</div>
            <div>onleaked.nealix.org</div>
        </div>
    </div>

    <h2>Breach Report for: {{ $email }}</h2>

    @php
        $score = 100;
        $breaches = $results['breaches'] ?? [];
        $footprint = $results['footprint'] ?? [];
        $score -= min(50, count($breaches) * 15);
        foreach ($breaches as $b) {
            if ($b['password_leaked'] ?? false) { $score -= 20; break; }
        }
        $score = max(0, $score);
        $scoreClass = $score >= 80 ? 'score-green' : ($score >= 50 ? 'score-amber' : 'score-red');
        $scoreLabel = $score >= 80 ? 'Low Risk' : ($score >= 50 ? 'Medium Risk' : 'High Risk');
    @endphp

    <div class="score-box" style="margin-top:20px;">
        <div class="score-number {{ $scoreClass }}">{{ $score }}/100</div>
        <div style="font-size:14px; font-weight:600; margin-top:4px;">{{ $scoreLabel }}</div>
        <div style="font-size:11px; color:#71717a; margin-top:4px;">
            {{ count($breaches) }} breach(es) found &bull; {{ count($footprint) }} platform(s) detected
        </div>
    </div>

    @if(count($breaches) > 0)
        <h3>Data Breaches ({{ count($breaches) }})</h3>
        @foreach($breaches as $breach)
            <div class="breach-item">
                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                    <div class="breach-name">{{ $breach['source'] }}</div>
                    <div style="display:flex; gap:6px;">
                        @if($breach['password_leaked'] ?? false)
                            <span class="badge badge-red">Password exposed</span>
                        @endif
                        @if($breach['date'] ?? null)
                            <span class="badge" style="background:#f4f4f5; color:#52525b;">{{ $breach['date'] }}</span>
                        @endif
                    </div>
                </div>
                @if($breach['description'] ?? null)
                    <div class="breach-meta">{{ $breach['description'] }}</div>
                @endif
            </div>
        @endforeach
    @else
        <div class="section">
            <span class="badge badge-green">No breaches found</span>
            <span style="margin-left:8px; color:#52525b;">Your email was not found in any known data breach.</span>
        </div>
    @endif

    @if(count($footprint) > 0)
        <h3>Digital Footprint ({{ count($footprint) }} platforms)</h3>
        <div class="section">
            <div class="footprint-grid">
                @foreach($footprint as $site)
                    <span class="footprint-item">{{ $site }}</span>
                @endforeach
            </div>
        </div>
    @endif

    <div class="privacy-note">
        <strong>Privacy Statement:</strong> This report was generated on-demand. Your email address was never stored, logged, or retained by Onleaked. This document is for your personal use only.
        <br>Data sourced from: XposedOrNot API, Holehe digital footprint scanner. &copy; {{ date('Y') }} Nealix.
    </div>
</body>
</html>
