<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemBackup extends Model
{
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = ['initiated_by', 'backup_path', 'status'];

    public function initiatedBy() { return $this->belongsTo(User::class, 'initiated_by'); }
}
