<?php

namespace App\Console\Commands;

use App\Models\EmailCache;
use App\Models\Scan;
use Illuminate\Console\Command;

class CleanExpiredScans extends Command
{
    protected $signature   = 'onleaked:clean';
    protected $description = 'Supprime les scans et caches expirés';

    public function handle(): int
    {
        // Supprimer les caches expirés
        $deletedCaches = EmailCache::where('expires_at', '<', now())->delete();

        // Supprimer les vieux scans échoués (> 7 jours)
        $deletedFailed = Scan::where('status', 'failed')
            ->where('created_at', '<', now()->subDays(7))
            ->delete();

        // Supprimer les scans complétés > 48h (garder les récents pour UX)
        $deletedOld = Scan::whereIn('status', ['completed', 'cached'])
            ->where('completed_at', '<', now()->subHours(48))
            ->delete();

        $this->info("Nettoyage terminé:");
        $this->line("  - Caches expirés supprimés : {$deletedCaches}");
        $this->line("  - Scans échoués supprimés  : {$deletedFailed}");
        $this->line("  - Vieux scans supprimés    : {$deletedOld}");

        return self::SUCCESS;
    }
}
