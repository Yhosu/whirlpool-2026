<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Alerta de canjes y stock en tiempo real / cada minuto
Schedule::command('report:canje-alert')->everyMinute()->withoutOverlapping();
