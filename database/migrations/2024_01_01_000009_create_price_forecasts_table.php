<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // NICE-TO-HAVE: Market price forecast (LSTM output)
    public function up(): void
    {
        Schema::create('price_forecasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crop_id')->constrained('crops');
            $table->foreignId('zone_id')->constrained('climate_zones');
            $table->date('forecast_date')->nullable();
            $table->decimal('predicted_price', 10, 2)->nullable();
            $table->string('model_version', 20)->default('v1');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_forecasts');
    }
};
