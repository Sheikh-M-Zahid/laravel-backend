<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FertilizerRecommendation extends Model
{
    public $timestamps = false;
    protected $fillable = ['recommendation_id', 'fertilizer_type', 'dosage_kg_per_acre', 'notes'];

    public function recommendation() { return $this->belongsTo(Recommendation::class); }
}
