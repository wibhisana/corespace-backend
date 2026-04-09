<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jalankan setiap tanggal 1 Januari jam 00:01 pagi
Schedule::command('hris:generate-leave-balances')->yearlyOn(1, 1, '00:01');
