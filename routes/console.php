<?php

use App\Console\Commands\EnviarAlertasDiarias;
use Illuminate\Support\Facades\Schedule;

// Alertas diarias automáticas — 07:00 todos los días
// En el servidor agregar al crontab de www-data:
//   * * * * * cd /var/www/pump-tracker && php artisan schedule:run >> /dev/null 2>&1
Schedule::command(EnviarAlertasDiarias::class)
    ->dailyAt('07:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->onFailure(fn() => \Illuminate\Support\Facades\Log::error('alertas:enviar falló'));
