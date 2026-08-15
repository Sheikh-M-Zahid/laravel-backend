<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // NICE-TO-HAVE: Trigger ML Model Retraining use case
    public function up(): void
    {
        Schema::create('model_retraining_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('triggered_by')->constrained('users')->cascadeOnDelete();
            $table->enum('model_name', ['crop_rf', 'fertilizer_rule', 'price_lstm', 'disease_cnn']);
            $table->enum('status', ['queued', 'running', 'completed', 'failed'])->default('queued');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('model_retraining_jobs');
    }
};
