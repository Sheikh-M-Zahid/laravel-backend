<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Thin HTTP client wrapping the Python Flask ML microservice
 * (see ml-service/app.py). Every method returns the decoded JSON array
 * from the corresponding /api/predict/* endpoint.
 */
class MlService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.ml.url', env('ML_SERVICE_URL', 'http://localhost:5000')), '/');
    }

    public function recommendCrop(array $soilData): array
    {
        $response = Http::timeout(10)->post("{$this->baseUrl}/api/predict/crop", $soilData);
        $response->throw();
        return $response->json();
    }

    public function recommendFertilizer(string $crop, array $soilData): array
    {
        $payload = array_merge(['crop' => $crop], $soilData);
        $response = Http::timeout(10)->post("{$this->baseUrl}/api/predict/fertilizer", $payload);
        $response->throw();
        return $response->json();
    }

    public function forecastPrice(string $crop, int $monthsAhead = 3): array
    {
        $response = Http::timeout(10)->post("{$this->baseUrl}/api/predict/price", [
            'crop' => $crop,
            'months_ahead' => $monthsAhead,
        ]);

        // The ML service replies 400 (not 500) when a crop has no trained
        // price model -- that's an expected, user-facing outcome ("Rice
        // works, 'Wheat' doesn't yet"), not a server failure. ->throw()
        // would turn that into an uncaught 500 Laravel error page, which is
        // the "Market Price Forecast / Demand Forecast only works for rice"
        // bug. Only bubble up on a genuine server/connection failure (5xx
        // or no response at all); otherwise always return the decoded body
        // (which is either the forecast or a friendly {"error": "..."}).
        if ($response->serverError()) {
            $response->throw();
        }

        return $response->json() ?? ['error' => 'The forecasting service is unavailable right now. Please try again shortly.'];
    }

    /**
     * What crops/classes each model actually knows how to predict, straight
     * from the ML service (not hand-maintained on the PHP side, so it can
     * never drift out of sync with what's actually trained). Used by the
     * public "what can the models predict?" page.
     */
    public function capabilities(): array
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/api/capabilities");
            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Throwable $e) {
            // ML service unreachable -- fall through to the static fallback
            // below so the page still renders something useful instead of
            // a 500.
        }

        return [
            'crop_recommendation' => ['apple', 'banana', 'blackgram', 'chickpea', 'coconut', 'coffee', 'cotton', 'grapes', 'jute', 'kidneybeans', 'lentil', 'maize', 'mango', 'mothbeans', 'mungbean', 'muskmelon', 'orange', 'papaya', 'pigeonpeas', 'pomegranate', 'rice', 'watermelon'],
            'price_forecast' => ['rice', 'maize', 'cotton', 'jute', 'banana', 'mango', 'coffee', 'coconut'],
            'pest_detection' => ['trained' => false, 'classes' => ['Rice_Blast', 'Rice_Brown_Spot', 'Rice_Healthy', 'Maize_Leaf_Blight', 'Maize_Healthy', 'Cotton_Leaf_Curl', 'Cotton_Healthy']],
            'unavailable' => true,
        ];
    }

    public function detectPest(string $imagePath): array
    {
        $response = Http::timeout(20)->attach(
            'image', file_get_contents($imagePath), basename($imagePath)
        )->post("{$this->baseUrl}/api/predict/pest");
        $response->throw();
        return $response->json();
    }
}
