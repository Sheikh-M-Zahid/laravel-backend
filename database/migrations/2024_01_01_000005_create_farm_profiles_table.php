<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // role='farmer'
            $table->foreignId('zone_id')->constrained('climate_zones');
            $table->decimal('land_size_acres', 6, 2)->nullable();
            $table->string('location_text')->nullable();
            $table->decimal('latitude', 9, 6)->nullable();
            $table->decimal('longitude', 9, 6)->nullable();
            $table->decimal('soil_ph', 3, 1)->nullable();
            $table->decimal('nitrogen', 6, 2)->nullable();
            $table->decimal('phosphorus', 6, 2)->nullable();
            $table->decimal('potassium', 6, 2)->nullable();
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_profiles');
    }
};
