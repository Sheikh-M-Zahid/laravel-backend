<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvisoryMessage extends Model
{
    public $timestamps = false;
    protected $fillable = ['extension_officer_id', 'farmer_id', 'message', 'sent_at', 'read_at'];

    public function officer() { return $this->belongsTo(User::class, 'extension_officer_id'); }
    public function farmer() { return $this->belongsTo(User::class, 'farmer_id'); }
}
