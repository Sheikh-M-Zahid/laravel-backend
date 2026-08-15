<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // NICE-TO-HAVE: cached OpenWeather API pulls per zone, avoids hammering the API.
    public function up(): void
    {
        Schema::create('weather_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zone_id')->constrained('climate_zones')->cascadeOnDelete();
            $table->decimal('temperature', 5, 2)->nullable();
            $table->decimal('rainfall', 6, 2)->nullable();
            $table->decimal('humidity', 5, 2)->nullable();
            $table->timestamp('fetched_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weather_logs');
    }
};
