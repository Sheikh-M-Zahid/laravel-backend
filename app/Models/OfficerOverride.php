<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficerOverride extends Model
{
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = ['recommendation_id', 'extension_officer_id', 'overridden_crop_id', 'reason'];

    public function recommendation() { return $this->belongsTo(Recommendation::class); }
    public function officer() { return $this->belongsTo(User::class, 'extension_officer_id'); }
    public function overriddenCrop() { return $this->belongsTo(Crop::class, 'overridden_crop_id'); }
}
