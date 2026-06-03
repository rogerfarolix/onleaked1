<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #18181b; padding: 40px; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #f59e0b; padding-bottom: 20px; margin-bottom: 28px; }
        .logo { font-size: 22px; font-weight: 700; color: #7c3aed; }
        .logo span { color: #f43f5e; }
        .meta { font-size: 11px; color: #71717a; text-align: right; }
        h2 { font-size: 18px; font-weight: 700; margin-bottom: 4px; }
        h3 { font-size: 14px; font-weight: 600; color: #3f3f46; margin: 22px 0 10px; border-bottom: 1px solid #e4e4e7; padding-bottom: 4px; }
        .score-box { text-align: center; padding: 18px; background: #fffbeb; border-radius: 8px; border: 2px solid #f59e0b; margin: 18px 0; }
        .score-number { font-size: 44px; font-weight: 800; }
        .score-red { color: #dc2626; }
        .score-amber { color: #d97706; }
        .score-green { color: #059669; }
        .check-row { display: flex; justify-content: space-between; padding: 8px 12px; border-bottom: 1px solid #f4f4f5; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
        .badge-pass { background: #d1fae5; color: #059669; }
        .badge-fail { background: #fee2e2; color: #dc2626; }
        .badge-clean { background: #d1fae5; color: #059669; }
        .badge-susp { background: #fef3c7; color: #d97706; }
        .badge-malicious { background: #fee2e2; color: #dc2626; }
        .dns-table { width: 100%; border-collapse: collapse; font-size: 11px; }
        .dns-table th { background: #f4f4f5; padding: 6px 8px; text-align: left; font-size: 11px; }
        .dns-table td { padding: 5px 8px; border-bottom: 1px solid #f4f4f5; font-family: monospace; }
        .subdomain-list { display: flex; flex-wrap: wrap; gap: 6px; }
        .subdomain-item { background: #fef3c7; color: #92400e; padding: 3px 10px; border-radius: 20px; font-size: 11px; }
        .vt-grid { display: flex; gap: 10px; margin-top: 8px; }
        .vt-cell { flex: 1; text-align: center; padding: 10px; border-radius: 6px; }
        .privacy-note { font-size: 10px; color: #71717a; border-top: 1px solid #e4e4e7; padding-top: 14px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="logo">On<span>leaked</span></div>
            <div style="font-size:11px; color:#71717a; margin-top:4px;">Cybersecurity Intelligence Platform</div>
        </div>
        <div class="meta">
            <div>Domain Analysis Report</div>
            <div>Generated: {{ $date }}</div>
            <div>onleaked.nealix.org</div>
        </div>
    </div>

    <h2>Analysis Report: {{ $domain }}</h2>

    @php
        $emailCfg   = $results['email_config'] ?? [];
        $reputation = $results['reputation'] ?? [];
        $dns        = $results['dns'] ?? [];
        $subdomains = $results['subdomains'] ?? [];

        $score = 100;
        if (!($emailCfg['has_spf']   ?? false)) $score -= 20;
        if (!($emailCfg['has_dmarc'] ?? false)) $score -= 20;
        if (!($emailCfg['has_mx']    ?? false)) $score -= 10;
        $flagged = $reputation['engines_flagged'] ?? 0;
        if ($flagged > 0)  $score -= 30;
        if ($flagged >= 5) $score -= 10;
        if (($reputation['ioc_hits'] ?? 0) > 0) $score -= 20;
        $score = max(0, $score);
        $scoreClass = $score >= 80 ? 'score-green' : ($score >= 50 ? 'score-amber' : 'score-red');
        $scoreLabel = $score >= 80 ? 'Good Security Posture' : ($score >= 50 ? 'Needs Improvement' : 'Critical Issues Found');
    @endphp

    <div class="score-box">
        <div class="score-number {{ $scoreClass }}">{{ $score }}/100</div>
        <div style="font-size:14px; font-weight:600; margin-top:4px;">{{ $scoreLabel }}</div>
    </div>

    <h3>Email Security</h3>
    <div class="check-row">
        <span>MX Records (mail server configured)</span>
        <span class="badge {{ ($emailCfg['has_mx'] ?? false) ? 'badge-pass' : 'badge-fail' }}">{{ ($emailCfg['has_mx'] ?? false) ? 'PASS' : 'FAIL' }}</span>
    </div>
    <div class="check-row">
        <span>SPF (Sender Policy Framework)</span>
        <span class="badge {{ ($emailCfg['has_spf'] ?? false) ? 'badge-pass' : 'badge-fail' }}">{{ ($emailCfg['has_spf'] ?? false) ? 'PASS' : 'FAIL' }}</span>
    </div>
    <div class="check-row">
        <span>DMARC (Domain-based Message Authentication)</span>
        <span class="badge {{ ($emailCfg['has_dmarc'] ?? false) ? 'badge-pass' : 'badge-fail' }}">{{ ($emailCfg['has_dmarc'] ?? false) ? 'PASS' : 'FAIL' }}</span>
    </div>

    <h3>Reputation</h3>
    @php
        $repStatus = $reputation['status'] ?? 'clean';
        $repClass  = $repStatus === 'clean' ? 'badge-clean' : ($repStatus === 'suspicious' ? 'badge-susp' : 'badge-malicious');
    @endphp
    <div class="check-row">
        <span>Overall verdict</span>
        <span class="badge {{ $repClass }}">{{ strtoupper($repStatus) }}</span>
    </div>
    @if(($reputation['sources']['virustotal'] ?? false))
        <div class="vt-grid">
            <div class="vt-cell" style="background:#fee2e2;">
                <div style="font-size:20px; font-weight:700; color:#dc2626;">{{ $reputation['engines_flagged'] ?? 0 }}</div>
                <div style="font-size:10px; color:#71717a;">Malicious</div>
            </div>
            <div class="vt-cell" style="background:#d1fae5;">
                <div style="font-size:20px; font-weight:700; color:#059669;">{{ $reputation['harmless'] ?? 0 }}</div>
                <div style="font-size:10px; color:#71717a;">Harmless</div>
            </div>
            <div class="vt-cell" style="background:#f4f4f5;">
                <div style="font-size:20px; font-weight:700; color:#52525b;">{{ $reputation['undetected'] ?? 0 }}</div>
                <div style="font-size:10px; color:#71717a;">Undetected</div>
            </div>
            <div class="vt-cell" style="background:#ede9fe;">
                <div style="font-size:20px; font-weight:700; color:#7c3aed;">{{ $reputation['ioc_hits'] ?? 0 }}</div>
                <div style="font-size:10px; color:#71717a;">IOC Hits</div>
            </div>
        </div>
    @endif

    @if(count($subdomains) > 0)
        <h3>Subdomains ({{ count($subdomains) }} via Certificate Transparency)</h3>
        <div class="subdomain-list">
            @foreach($subdomains as $sub)
                <span class="subdomain-item">{{ $sub }}</span>
            @endforeach
        </div>
    @endif

    @if(count($dns) > 0)
        <h3>DNS Records ({{ count($dns) }})</h3>
        <table class="dns-table">
            <thead><tr><th>Type</th><th>Host</th><th>Value</th><th>TTL</th></tr></thead>
            <tbody>
                @foreach(array_slice($dns, 0, 20) as $record)
                    <tr>
                        <td>{{ $record['type'] }}</td>
                        <td>{{ $record['host'] ?? '' }}</td>
                        <td>{{ $record['ip'] ?? $record['ipv6'] ?? ($record['target'] ?? ($record['txt'] ?? '')) }}</td>
                        <td>{{ $record['ttl'] ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="privacy-note">
        Data sourced from: DNS resolver, VirusTotal API, TweetFeed IOC community, crt.sh Certificate Transparency logs.
        Results cached for 10 minutes. Report valid as of generation date. &copy; {{ date('Y') }} Nealix onleaked.nealix.org
    </div>
</body>
</html>
