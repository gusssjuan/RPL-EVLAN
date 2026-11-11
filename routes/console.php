<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('evlan:kirim-reminder-deadline')
    ->dailyAt('07:00')
    ->timezone('Asia/Makassar')
    ->withoutOverlapping();
