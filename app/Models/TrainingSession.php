<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingSession extends Model
{
    public $timestamps = false;
    protected $fillable = ['extension_officer_id', 'zone_id', 'title', 'description', 'session_date', 'location'];

    public function officer() { return $this->belongsTo(User::class, 'extension_officer_id'); }
    public function zone() { return $this->belongsTo(ClimateZone::class, 'zone_id'); }
    public function attendees() { return $this->hasMany(TrainingAttendee::class, 'session_id'); }
}
