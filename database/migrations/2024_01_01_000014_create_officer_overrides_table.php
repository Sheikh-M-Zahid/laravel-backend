<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // NICE-TO-HAVE: Review/Override ML Recommendation (<<extend>>)
    public function up(): void
    {
        Schema::create('officer_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recommendation_id')->constrained('recommendations')->cascadeOnDelete();
            $table->foreignId('extension_officer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('overridden_crop_id')->constrained('crops');
            $table->text('reason')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('officer_overrides');
    }
};
