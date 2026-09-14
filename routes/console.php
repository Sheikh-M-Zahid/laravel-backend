<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Keep weather_logs fresh for every climate zone (OpenWeather), so
// recommendCrop() always has recent data instead of stale/default values.
Schedule::command('weather:refresh')->hourly();
