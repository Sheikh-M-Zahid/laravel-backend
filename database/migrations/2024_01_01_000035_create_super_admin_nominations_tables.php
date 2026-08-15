<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('super_admin_nominations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nominee_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('created_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();
        });

        // One row per one of the 3 founding emails who has weighed in on a
        // given nomination. A nomination only takes effect once all 3
        // founders have an 'approve' row here.
        Schema::create('super_admin_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nomination_id')->constrained('super_admin_nominations')->cascadeOnDelete();
            $table->foreignId('approver_user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('decision', ['approve', 'reject']);
            $table->timestamps();
            $table->unique(['nomination_id', 'approver_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('super_admin_approvals');
        Schema::dropIfExists('super_admin_nominations');
    }
};
