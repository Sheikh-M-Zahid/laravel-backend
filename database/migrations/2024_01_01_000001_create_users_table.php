<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('email', 150)->unique();
            $table->string('phone', 20)->nullable();
            $table->string('password');
            $table->enum('role', ['farmer', 'extension_officer', 'supplier', 'admin']);
            // Supplier ও Extension Officer সাইনআপে admin approval লাগবে (status='pending')
            $table->enum('status', ['pending', 'active', 'suspended'])->default('active');
            $table->string('profile_photo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
