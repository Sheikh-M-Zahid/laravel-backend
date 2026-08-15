<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CropCalendar extends Model
{
    public $timestamps = false;
    protected $fillable = ['crop_id', 'zone_id', 'sowing_start', 'sowing_end', 'harvest_start', 'harvest_end'];

    public function crop() { return $this->belongsTo(Crop::class); }
    public function zone() { return $this->belongsTo(ClimateZone::class, 'zone_id'); }
}
