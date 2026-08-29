<?php

namespace App\Services;

use App\Models\ClimateZone;
use App\Models\WeatherLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    protected int $cacheHours = 3; // এর চেয়ে পুরনো হলে নতুন করে API কল হবে

    public function getForZone(ClimateZone $zone): ?WeatherLog
    {
        $cached = WeatherLog::where('zone_id', $zone->id)
            ->where('fetched_at', '>=', now()->subHours($this->cacheHours))
            ->latest('fetched_at')
            ->first();

        if ($cached) {
            return $cached;
        }

        return $this->fetchAndStore($zone)
            ?? WeatherLog::where('zone_id', $zone->id)->latest('fetched_at')->first();
    }

    protected function fetchAndStore(ClimateZone $zone): ?WeatherLog
    {
        $key = config('services.openweather.key');
        if (empty($key)) {
            return null;
        }

        $city = $zone->region ?: $zone->zone_name;

        try {
            $response = Http::timeout(5)->get(config('services.openweather.base_url') . '/weather', [
                'q' => "{$city},BD",
                'appid' => $key,
                'units' => 'metric',
            ]);

            if (! $response->successful()) {
                Log::warning("OpenWeather fetch failed for zone {$zone->id}: " . $response->body());
                return null;
            }

            $data = $response->json();

            return WeatherLog::create([
                'zone_id' => $zone->id,
                'temperature' => $data['main']['temp'] ?? null,
                'humidity' => $data['main']['humidity'] ?? null,
                'rainfall' => $data['rain']['1h'] ?? $data['rain']['3h'] ?? 0,
                'fetched_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error("OpenWeather exception for zone {$zone->id}: " . $e->getMessage());
            return null;
        }
    }
}
