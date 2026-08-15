<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClimateZone extends Model
{
    public $timestamps = false;
    protected $fillable = ['zone_name', 'region', 'description'];

    public function farmProfiles() { return $this->hasMany(FarmProfile::class, 'zone_id'); }
    public function cropCalendar() { return $this->hasMany(CropCalendar::class, 'zone_id'); }
    public function weatherLogs() { return $this->hasMany(WeatherLog::class, 'zone_id'); }
}
