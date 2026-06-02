<?php

namespace App\Console\Commands;

use App\Models\AdminLog;
use Illuminate\Console\Command;

class PruneLogs extends Command
{
    protected $signature   = 'onleaked:prune-logs';
    protected $description = 'Delete admin logs older than 90 days';

    public function handle(): int
    {
        $deleted = AdminLog::where('created_at', '<', now()->subDays(90))->delete();
        $this->info("Pruned {$deleted} admin log(s) older than 90 days.");
        return self::SUCCESS;
    }
}
