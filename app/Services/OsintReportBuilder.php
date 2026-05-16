<?php

namespace App\Services;

use App\Enums\RiskLevel;
use App\Models\Report;
use App\Models\Scan;

class OsintReportBuilder
{
    public function build(Scan $scan, array $rawResults): Report
    {
        $holehe    = $rawResults['holehe']          ?? [];
        $emailrep  = $rawResults['emailrep']         ?? [];
        $breach    = $rawResults['breachdirectory']  ?? [];
        $gravatar  = $rawResults['gravatar']          ?? [];
        $dns       = $rawResults['dns']               ?? [];
        $hibp      = $rawResults['hibp']              ?? [];

        $accountsFound = $holehe['total_found'] ?? 0;

        // FIX: use null-coalescing before boolean cast to avoid null → true bug
        $breachesFound = ($breach['count'] ?? 0)
            + (($emailrep['credentials_leaked'] ?? false) ? 1 : 0)
            + ($hibp['count'] ?? 0);

        // --- Risk score calculation ---
        $score = 0;
        $score += min($accountsFound, 15);                                       // max 15 pts from accounts
        $score += ($breach['count'] ?? 0) * 2;                                   // 2 pts per breach (BreachDir)
        $score += ($hibp['count'] ?? 0) * 3;                                     // 3 pts per HIBP breach (verified)
        $score += ($emailrep['suspicious']         ?? false) ? 3 : 0;
        $score += ($emailrep['blacklisted']         ?? false) ? 5 : 0;
        $score += ($emailrep['malicious_activity']  ?? false) ? 5 : 0;
        $score += ($emailrep['data_breach']         ?? false) ? 3 : 0;
        $score += ($breach['found']                 ?? false) ? 2 : 0;
        $score += ($hibp['found']                   ?? false) ? 4 : 0;           // HIBP is highly reliable
        $riskLevel = RiskLevel::fromScore($score);

        $fullReport = [
            'email'          => $scan->email_target,
            'scanned_at'     => now()->toIso8601String(),
            'risk_score'     => $score,
            'accounts_found' => $accountsFound,
            'breaches_found' => $breachesFound,
            'risk_level'     => $riskLevel->value,
            'gravatar'       => $gravatar,
            'holehe'         => $holehe,
            'emailrep'       => $emailrep,
            'breachdirectory'=> $breach,
            'dns'            => $dns,
            'hibp'           => $hibp,
            'summary'        => $this->buildSummary($holehe, $emailrep, $breach, $gravatar, $dns, $hibp),
        ];

        return Report::updateOrCreate(
            ['scan_id' => $scan->id],
            [
                'accounts_found' => $accountsFound,
                'breaches_found' => $breachesFound,
                'risk_level'     => $riskLevel,
                'gravatar_url'   => $gravatar['avatar_url'] ?? null,
                'full_report'    => $fullReport,
                'email_sent'     => false,
            ]
        );
    }

    private function buildSummary(
        array $holehe,
        array $emailrep,
        array $breach,
        array $gravatar,
        array $dns,
        array $hibp = []
    ): array {
        $warnings  = [];
        $positives = [];

        if (!empty($holehe['sites'])) {
            $count      = count($holehe['sites']);
            $warnings[] = "{$count} compte(s) détecté(s) sur des services en ligne.";
        }

        if ($emailrep['credentials_leaked'] ?? false) {
            $warnings[] = 'Des identifiants associés à cet email ont été détectés dans des fuites.';
        }

        if ($emailrep['blacklisted'] ?? false) {
            $warnings[] = 'Cet email est référencé dans des listes noires de spam/phishing.';
        }

        if ($breach['found'] ?? false) {
            $count      = $breach['count'] ?? 0;
            $warnings[] = "{$count} mot(s) de passe exposé(s) dans des bases de données fuites (BreachDirectory).";
        }

        if ($hibp['found'] ?? false) {
            $count      = $hibp['count'] ?? 0;
            $warnings[] = "Cet email apparaît dans {$count} fuite(s) de données référencées par HaveIBeenPwned.";
        }

        if ($emailrep['suspicious'] ?? false) {
            $warnings[] = 'Activité suspecte détectée associée à cet email.';
        }

        if ($dns['is_disposable'] ?? false) {
            $warnings[] = "Le domaine semble être un service d'email jetable.";
        }

        if ($gravatar['has_gravatar'] ?? false) {
            $positives[] = 'Un profil Gravatar public est associé à cet email.';
        }

        $noThreat = !($emailrep['suspicious'] ?? false)
            && !($breach['found'] ?? false)
            && !($hibp['found'] ?? false)
            && !($emailrep['credentials_leaked'] ?? false);

        if ($noThreat) {
            $positives[] = 'Aucune activité malveillante détectée sur les sources vérifiées.';
        }

        return ['warnings' => $warnings, 'positives' => $positives];
    }
}
