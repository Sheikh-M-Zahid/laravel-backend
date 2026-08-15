<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Verify Soil/Farm Data use case
    public function up(): void
    {
        Schema::create('officer_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_profile_id')->constrained('farm_profiles')->cascadeOnDelete();
            $table->foreignId('extension_officer_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['verified', 'rejected']);
            $table->text('notes')->nullable();
            $table->timestamp('verified_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('officer_verifications');
    }
};
