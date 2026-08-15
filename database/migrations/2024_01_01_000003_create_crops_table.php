<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crops', function (Blueprint $table) {
            $table->id();
            $table->string('crop_name', 100);
            $table->enum('season', ['Kharif-1', 'Kharif-2', 'Rabi']);
            $table->text('description')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crops');
    }
};
