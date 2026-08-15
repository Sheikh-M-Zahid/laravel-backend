<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceForecast extends Model
{
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = ['crop_id', 'zone_id', 'forecast_date', 'predicted_price', 'model_version'];

    public function crop() { return $this->belongsTo(Crop::class); }
    public function zone() { return $this->belongsTo(ClimateZone::class, 'zone_id'); }
}
