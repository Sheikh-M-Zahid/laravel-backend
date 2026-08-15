<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Supports two distinct admin moderation actions:
    //  - "Restrict" (temporary): status stays/becomes 'suspended', restricted_until
    //    holds when it auto-lifts (checked on login). NULL = indefinite, admin-lifted only.
    //  - "Remove": a soft delete. removed_original_email keeps the real address so
    //    admin can still find/search the account's history, while the `email`
    //    column itself gets mangled on removal to free that address up for a
    //    fresh registration (users.email has a DB-level unique index that a plain
    //    soft delete would NOT bypass).
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('restricted_until')->nullable()->after('status');
            $table->string('removed_original_email')->nullable()->after('restricted_until');
            $table->timestamp('removed_at')->nullable()->after('removed_original_email');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['restricted_until', 'removed_original_email', 'removed_at']);
            $table->dropSoftDeletes();
        });
    }
};
