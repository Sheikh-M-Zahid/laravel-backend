<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['supplier_id', 'product_name', 'category', 'price', 'stock_quantity', 'description'];

    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function orderItems() { return $this->hasMany(OrderItem::class); }
}
