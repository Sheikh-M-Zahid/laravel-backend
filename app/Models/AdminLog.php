<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model
{
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = ['admin_id', 'subject_user_id', 'action', 'description'];

    public function admin() { return $this->belongsTo(User::class, 'admin_id'); }
    public function subjectUser() { return $this->belongsTo(User::class, 'subject_user_id')->withTrashed(); }

    /** Convenience static logger so any controller can record an admin action without duplicating the insert. */
    public static function record(string $action, string $description, ?int $subjectUserId = null): void
    {
        static::create([
            'admin_id' => \Illuminate\Support\Facades\Auth::id(),
            'subject_user_id' => $subjectUserId,
            'action' => $action,
            'description' => $description,
        ]);
    }
}
