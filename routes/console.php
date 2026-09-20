<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

$autoAlpaSchedule = Schedule::command('absensi:auto-alpa-murid')
    ->everyMinute()
    ->withoutOverlapping();

if (app()->environment('local')) {
    $autoAlpaSchedule->appendOutputTo(storage_path('logs/schedule-cron.log'));
}
