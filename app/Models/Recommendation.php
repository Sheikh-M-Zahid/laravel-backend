<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    public $timestamps = false; // has created_at only, no updated_at (see migration)
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'farmer_id', 'farm_profile_id', 'recommended_crop_id', 'confidence_score', 'model_version',
    ];

    public function farmer() { return $this->belongsTo(User::class, 'farmer_id'); }
    public function farmProfile() { return $this->belongsTo(FarmProfile::class); }
    public function recommendedCrop() { return $this->belongsTo(Crop::class, 'recommended_crop_id'); }
    public function fertilizerRecommendation() { return $this->hasOne(FertilizerRecommendation::class); }
    public function feedback() { return $this->hasMany(RecommendationFeedback::class); }
    public function overrides() { return $this->hasMany(OfficerOverride::class); }
}
