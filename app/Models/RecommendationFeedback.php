<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecommendationFeedback extends Model
{
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;
    protected $table = 'recommendation_feedback';

    protected $fillable = ['recommendation_id', 'farmer_id', 'extension_officer_id', 'rating', 'comment'];

    public function recommendation() { return $this->belongsTo(Recommendation::class); }
    public function farmer() { return $this->belongsTo(User::class, 'farmer_id'); }
    public function officer() { return $this->belongsTo(User::class, 'extension_officer_id'); }
}
