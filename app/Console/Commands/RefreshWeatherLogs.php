<?php

namespace App\Console\Commands;

use App\Models\ClimateZone;
use App\Services\WeatherService;
use Illuminate\Console\Command;

class RefreshWeatherLogs extends Command
{
    protected $signature = 'weather:refresh';
    protected $description = 'Fetch fresh weather data (OpenWeather) for every climate zone and store it in weather_logs.';

    public function handle(WeatherService $weather): int
    {
        $zones = ClimateZone::all();

        if ($zones->isEmpty()) {
            $this->warn('No climate zones found — nothing to refresh.');
            return self::SUCCESS;
        }

        foreach ($zones as $zone) {
            $log = $weather->refreshZone($zone);
            $log
                ? $this->info("✓ {$zone->zone_name}: {$log->temperature}°C, {$log->humidity}% humidity, {$log->rainfall}mm rain")
                : $this->warn("✗ {$zone->zone_name}: fetch failed (check OPENWEATHER_API_KEY in .env)");
        }

        return self::SUCCESS;
    }
}
