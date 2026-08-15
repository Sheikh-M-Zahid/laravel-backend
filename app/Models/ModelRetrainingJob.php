<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModelRetrainingJob extends Model
{
    public $timestamps = false;
    protected $fillable = ['triggered_by', 'model_name', 'status', 'started_at', 'completed_at'];

    public function triggeredBy() { return $this->belongsTo(User::class, 'triggered_by'); }
}
