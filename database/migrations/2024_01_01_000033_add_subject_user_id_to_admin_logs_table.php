<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Lets the admin pull "everything that ever happened to user #N" reliably
    // (e.g. when reviewing a removed account's history) instead of relying on
    // string-matching the free-text description column.
    public function up(): void
    {
        Schema::table('admin_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('subject_user_id')->nullable()->after('admin_id');
        });
    }

    public function down(): void
    {
        Schema::table('admin_logs', function (Blueprint $table) {
            $table->dropColumn('subject_user_id');
        });
    }
};
