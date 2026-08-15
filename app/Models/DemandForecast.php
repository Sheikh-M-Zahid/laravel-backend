<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemandForecast extends Model
{
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = ['category', 'zone_id', 'forecast_period', 'predicted_demand_units', 'model_version'];

    public function zone() { return $this->belongsTo(ClimateZone::class, 'zone_id'); }
}
