<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('climate_zones', function (Blueprint $table) {
            $table->id();
            $table->string('zone_name', 100); // e.g. "Barind Tract", "Haor Region"
            $table->string('region', 100)->nullable(); // e.g. "Rajshahi", "Sylhet"
            $table->text('description')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('climate_zones');
    }
};
