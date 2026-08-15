<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiseaseDetection extends Model
{
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'farmer_id', 'farm_profile_id', 'image_path', 'detected_disease',
        'confidence_score', 'suggested_action',
    ];

    public function farmer() { return $this->belongsTo(User::class, 'farmer_id'); }
    public function farmProfile() { return $this->belongsTo(FarmProfile::class); }
}
