<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // NICE-TO-HAVE: View Regional Demand Forecast (LSTM-derived, aggregated)
    public function up(): void
    {
        Schema::create('demand_forecasts', function (Blueprint $table) {
            $table->id();
            $table->enum('category', ['seed', 'fertilizer']);
            $table->foreignId('zone_id')->constrained('climate_zones')->cascadeOnDelete();
            $table->string('forecast_period', 20)->nullable(); // e.g. "2026-08"
            $table->integer('predicted_demand_units')->nullable();
            $table->string('model_version', 20)->default('v1');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demand_forecasts');
    }
};
