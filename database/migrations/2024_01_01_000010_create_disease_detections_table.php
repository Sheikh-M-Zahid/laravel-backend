<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // NICE-TO-HAVE: Pest/Disease detection (CNN — optional/advanced module)
    public function up(): void
    {
        Schema::create('disease_detections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('farm_profile_id')->constrained('farm_profiles')->cascadeOnDelete();
            $table->string('image_path')->nullable();
            $table->string('detected_disease', 150)->nullable();
            $table->decimal('confidence_score', 5, 2)->nullable();
            $table->text('suggested_action')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disease_detections');
    }
};
