<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('business_name', 150)->nullable();
            $table->string('business_address')->nullable();
            $table->boolean('verified')->default(false); // Admin approval লাগবে
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
