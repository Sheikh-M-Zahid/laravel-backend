<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingAttendee extends Model
{
    public $timestamps = false;
    protected $fillable = ['session_id', 'farmer_id', 'status'];

    public function session() { return $this->belongsTo(TrainingSession::class, 'session_id'); }
    public function farmer() { return $this->belongsTo(User::class, 'farmer_id'); }
}
