<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FarmProfile extends Model
{
    protected $fillable = [
        'user_id', 'zone_id', 'land_size_acres', 'location_text', 'latitude', 'longitude',
        'soil_ph', 'nitrogen', 'phosphorus', 'potassium', 'verification_status',
    ];

    public function farmer() { return $this->belongsTo(User::class, 'user_id'); }
    public function zone() { return $this->belongsTo(ClimateZone::class, 'zone_id'); }
    public function recommendations() { return $this->hasMany(Recommendation::class); }
    public function diseaseDetections() { return $this->hasMany(DiseaseDetection::class); }
    public function verifications() { return $this->hasMany(OfficerVerification::class); }
}
