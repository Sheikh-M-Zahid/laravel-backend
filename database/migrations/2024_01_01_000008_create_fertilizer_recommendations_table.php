<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Fertilizer recommendation (<<extend>> of crop recommendation)
    public function up(): void
    {
        Schema::create('fertilizer_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recommendation_id')->constrained('recommendations')->cascadeOnDelete();
            $table->string('fertilizer_type', 100)->nullable();
            $table->decimal('dosage_kg_per_acre', 6, 2)->nullable();
            $table->text('notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fertilizer_recommendations');
    }
};
