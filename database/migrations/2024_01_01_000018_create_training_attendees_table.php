<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // NICE-TO-HAVE: attendance roster for training_sessions
    public function up(): void
    {
        Schema::create('training_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('training_sessions')->cascadeOnDelete();
            $table->foreignId('farmer_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['registered', 'attended', 'absent'])->default('registered');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_attendees');
    }
};
