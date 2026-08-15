<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('otp_hash');
            $table->enum('purpose', ['register', 'password_reset']);
            // For 'register': holds the pending signup data (name, hashed password,
            // role, business fields) until the code is confirmed, so no user row
            // is created until the email is verified.
            $table->text('payload')->nullable();
            $table->timestamp('expires_at');
            $table->timestamp('consumed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_verifications');
    }
};
