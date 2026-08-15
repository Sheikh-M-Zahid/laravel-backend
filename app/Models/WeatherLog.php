<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeatherLog extends Model
{
    public $timestamps = false;
    protected $fillable = ['zone_id', 'temperature', 'rainfall', 'humidity', 'fetched_at'];

    public function zone() { return $this->belongsTo(ClimateZone::class, 'zone_id'); }
}
