<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    public $timestamps = false;
    protected $fillable = ['extension_officer_id', 'zone_id', 'alert_type', 'message', 'sent_at'];

    public function officer() { return $this->belongsTo(User::class, 'extension_officer_id'); }
    public function zone() { return $this->belongsTo(ClimateZone::class, 'zone_id'); }
}
