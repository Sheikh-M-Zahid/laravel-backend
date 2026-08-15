<?php

return [
    'ml' => [
        'url' => env('ML_SERVICE_URL', 'http://localhost:5000'),
    ],
    'openweather' => [
        'key' => env('OPENWEATHER_API_KEY'),
        'base_url' => env('OPENWEATHER_BASE_URL', 'https://api.openweathermap.org/data/2.5'),
    ],
    'brevo' => [
        'key' => env('BREVO_API_KEY'),
        'sender_email' => env('BREVO_SENDER_EMAIL'),
        'sender_name' => env('BREVO_SENDER_NAME', 'Smart Agri-Advisory Platform'),
    ],
];
