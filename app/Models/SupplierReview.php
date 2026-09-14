<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierReview extends Model
{
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = ['order_id', 'farmer_id', 'supplier_id', 'rating', 'comment'];

    public function order() { return $this->belongsTo(Order::class); }
    public function farmer() { return $this->belongsTo(User::class, 'farmer_id'); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
}
