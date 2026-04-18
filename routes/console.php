<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduled tasks — active in production via Forge cron (php artisan schedule:run every minute)
Schedule::command('backup:run')->daily()->at('02:00');
