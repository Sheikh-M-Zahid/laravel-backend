<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id', 'business_name', 'business_address', 'bkash_number', 'verified'];
    protected function casts(): array { return ['verified' => 'boolean']; }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(SupplierReview::class);
    }

    /** Average rating (1-5), rounded to 1 decimal, or null if no reviews yet. Uses the loaded `reviews` relation if eager-loaded, to avoid N+1 queries. */
    public function getAvgRatingAttribute(): ?float
    {
        $avg = $this->relationLoaded('reviews') ? $this->reviews->avg('rating') : $this->reviews()->avg('rating');
        return $avg ? round($avg, 1) : null;
    }
}
