<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // NICE-TO-HAVE: Backup / Restore Database use case
    public function up(): void
    {
        Schema::create('system_backups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('initiated_by')->constrained('users')->cascadeOnDelete();
            $table->string('backup_path')->nullable();
            $table->enum('status', ['success', 'failed'])->default('success');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_backups');
    }
};
