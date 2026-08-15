<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // These are PERMISSION flags, deliberately separate from the existing
    // `role` column (which stays "what dashboard this person primarily
    // uses" -- farmer/extension_officer/supplier/admin). is_admin /
    // is_super_admin can be true for a user whose `role` is still e.g.
    // 'farmer' -- that's what lets a Super Admin who is also a farmer reach
    // the admin dashboard without logging out and back in as a separate
    // admin account.
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('role');
            $table->boolean('is_super_admin')->default(false)->after('is_admin');
            // Tracks the "apply to become admin, a super admin reviews it"
            // flow -- independent of is_admin so we can show a pending
            // application without granting access yet.
            $table->enum('admin_application_status', ['none', 'pending', 'approved', 'rejected'])->default('none')->after('is_super_admin');
            $table->timestamp('admin_applied_at')->nullable()->after('admin_application_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_admin', 'is_super_admin', 'admin_application_status', 'admin_applied_at']);
        });
    }
};
