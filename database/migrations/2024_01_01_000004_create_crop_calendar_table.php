<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // NICE-TO-HAVE: Bangladesh-specific crop calendar (sowing/harvest windows per zone).
    public function up(): void
    {
        Schema::create('crop_calendar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crop_id')->constrained('crops')->cascadeOnDelete();
            $table->foreignId('zone_id')->constrained('climate_zones')->cascadeOnDelete();
            $table->date('sowing_start')->nullable();
            $table->date('sowing_end')->nullable();
            $table->date('harvest_start')->nullable();
            $table->date('harvest_end')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crop_calendar');
    }
};
