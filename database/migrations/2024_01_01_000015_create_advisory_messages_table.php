<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // NICE-TO-HAVE: Provide Advisory Feedback to Farmer
    public function up(): void
    {
        Schema::create('advisory_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('extension_officer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('farmer_id')->constrained('users')->cascadeOnDelete();
            $table->text('message');
            $table->timestamp('sent_at')->useCurrent();
            $table->timestamp('read_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advisory_messages');
    }
};
