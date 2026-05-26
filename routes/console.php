<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Fetch new CVEs from NIST NVD at 6am, then send alert emails at 7am
Schedule::command('onleaked:fetch-cves')->dailyAt('06:00');
Schedule::command('onleaked:send-alerts')->dailyAt('07:00');
