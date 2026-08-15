<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficerVerification extends Model
{
    public $timestamps = false;
    const CREATED_AT = 'verified_at';
    const UPDATED_AT = null;

    protected $fillable = ['farm_profile_id', 'extension_officer_id', 'status', 'notes'];

    public function farmProfile() { return $this->belongsTo(FarmProfile::class); }
    public function officer() { return $this->belongsTo(User::class, 'extension_officer_id'); }
}
