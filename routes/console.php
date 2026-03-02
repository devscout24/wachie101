<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('app:check-for-new-property-command')
->everySixHours()
// ->everyFifteenSeconds()
->appendOutputTo(storage_path('logs/scheduled-tasks.log'));