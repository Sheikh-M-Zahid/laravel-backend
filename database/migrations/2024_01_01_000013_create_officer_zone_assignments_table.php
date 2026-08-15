<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // NICE-TO-HAVE: one officer may be responsible for multiple zones
    public function up(): void
    {
        Schema::create('officer_zone_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('extension_officer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('zone_id')->constrained('climate_zones')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('officer_zone_assignments');
    }
};
