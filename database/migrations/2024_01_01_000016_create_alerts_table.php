<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // NICE-TO-HAVE: Send Pest/Weather Alerts
    public function up(): void
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('extension_officer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('zone_id')->constrained('climate_zones')->cascadeOnDelete();
            $table->enum('alert_type', ['pest', 'weather']);
            $table->text('message');
            $table->timestamp('sent_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
