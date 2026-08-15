<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            // The number farmers should send bKash payment to.
            $table->string('bkash_number', 20)->nullable()->after('business_address');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_method', ['bkash', 'cod'])->default('bkash')->after('order_status');
            // unpaid: no payment submitted yet
            // pending_verification: farmer submitted a bKash TrxID, supplier hasn't confirmed
            // paid: supplier confirmed the payment
            $table->enum('payment_status', ['unpaid', 'pending_verification', 'paid'])->default('unpaid')->after('payment_method');
            $table->decimal('amount_paid', 10, 2)->default(0)->after('payment_status');
            $table->string('bkash_sender_number', 20)->nullable()->after('amount_paid');
            $table->string('bkash_trx_id', 50)->nullable()->after('bkash_sender_number');
            $table->timestamp('payment_submitted_at')->nullable()->after('bkash_trx_id');
            $table->timestamp('payment_verified_at')->nullable()->after('payment_submitted_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method', 'payment_status', 'amount_paid',
                'bkash_sender_number', 'bkash_trx_id',
                'payment_submitted_at', 'payment_verified_at',
            ]);
        });
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('bkash_number');
        });
    }
};
