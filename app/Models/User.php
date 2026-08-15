<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $fillable = ['name', 'email', 'phone', 'password', 'role', 'status', 'profile_photo', 'restricted_until', 'removed_original_email', 'removed_at', 'is_admin', 'is_super_admin', 'admin_application_status', 'admin_applied_at'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'restricted_until' => 'datetime',
            'removed_at' => 'datetime',
            'admin_applied_at' => 'datetime',
            'is_admin' => 'boolean',
            'is_super_admin' => 'boolean',
        ];
    }

    /** One of the 3 founding Super Admins (see config/super_admins.php) — only they can approve a new Super Admin nomination. */
    public function isFoundingSuperAdmin(): bool
    {
        return in_array(strtolower($this->email), array_map('strtolower', config('super_admins.emails', [])), true);
    }

    public function isFarmer(): bool { return $this->role === 'farmer'; }
    public function isExtensionOfficer(): bool { return $this->role === 'extension_officer'; }
    public function isSupplier(): bool { return $this->role === 'supplier'; }
    public function isAdmin(): bool { return $this->role === 'admin'; }

    public function farmProfiles() { return $this->hasMany(FarmProfile::class); }
    public function supplierProfile() { return $this->hasOne(Supplier::class); }
    public function recommendations() { return $this->hasMany(Recommendation::class, 'farmer_id'); }
    public function orders() { return $this->hasMany(Order::class, 'farmer_id'); }
    public function appNotifications() { return $this->hasMany(AppNotification::class); }
}
