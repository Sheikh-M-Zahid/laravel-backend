<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuperAdminNomination extends Model
{
    protected $fillable = ['nominee_user_id', 'created_by_user_id', 'status', 'decided_at'];
    protected $casts = ['decided_at' => 'datetime'];

    public function nominee() { return $this->belongsTo(User::class, 'nominee_user_id'); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by_user_id'); }
    public function approvals() { return $this->hasMany(SuperAdminApproval::class, 'nomination_id'); }
}
