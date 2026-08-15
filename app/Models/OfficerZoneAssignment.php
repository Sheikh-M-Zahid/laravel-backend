<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficerZoneAssignment extends Model
{
    public $timestamps = false;
    protected $fillable = ['extension_officer_id', 'zone_id'];

    public function officer() { return $this->belongsTo(User::class, 'extension_officer_id'); }
    public function zone() { return $this->belongsTo(ClimateZone::class, 'zone_id'); }
}
