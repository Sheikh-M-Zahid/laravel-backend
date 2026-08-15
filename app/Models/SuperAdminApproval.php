<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuperAdminApproval extends Model
{
    protected $fillable = ['nomination_id', 'approver_user_id', 'decision'];

    public function nomination() { return $this->belongsTo(SuperAdminNomination::class, 'nomination_id'); }
    public function approver() { return $this->belongsTo(User::class, 'approver_user_id'); }
}
