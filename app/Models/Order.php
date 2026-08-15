<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'farmer_id', 'supplier_id', 'order_status', 'total_amount',
        'payment_method', 'payment_status', 'amount_paid',
        'bkash_sender_number', 'bkash_trx_id',
        'payment_submitted_at', 'payment_verified_at',
    ];

    protected function casts(): array
    {
        return [
            'payment_submitted_at' => 'datetime',
            'payment_verified_at' => 'datetime',
        ];
    }

    public function farmer()
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /** How much of this order's total is still unpaid. */
    public function getDueAmountAttribute(): float
    {
        return max(0, (float) $this->total_amount - (float) $this->amount_paid);
    }

    public function getIsDeliveredAttribute(): bool
    {
        return $this->order_status === 'completed';
    }
}
