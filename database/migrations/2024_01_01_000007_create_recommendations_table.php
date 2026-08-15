<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Crop recommendation (Random Forest output)
    public function up(): void
    {
        Schema::create('recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('farm_profile_id')->constrained('farm_profiles')->cascadeOnDelete();
            $table->foreignId('recommended_crop_id')->constrained('crops');
            $table->decimal('confidence_score', 5, 2)->nullable(); // e.g. 87.5 (%)
            $table->string('model_version', 20)->default('v1');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommendations');
    }
};
