<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // NICE-TO-HAVE: View Platform Analytics Dashboard (precomputed snapshot, fast query)
    public function up(): void
    {
        Schema::create('analytics_snapshots', function (Blueprint $table) {
            $table->id();
            $table->date('snapshot_date')->nullable();
            $table->integer('active_farmers')->nullable();
            $table->integer('total_recommendations')->nullable();
            $table->decimal('avg_model_accuracy', 5, 2)->nullable();
            $table->integer('total_orders')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_snapshots');
    }
};
