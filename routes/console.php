<?php

use Illuminate\Support\Facades\Schedule;

// Nettoyage quotidien des scans et caches expirés
Schedule::command('onleaked:clean')->daily()->at('03:00');

// Nettoyage des jobs échoués en queue (> 7 jours)
Schedule::command('queue:prune-failed --hours=168')->daily()->at('03:30');
