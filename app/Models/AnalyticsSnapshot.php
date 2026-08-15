<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsSnapshot extends Model
{
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'snapshot_date', 'active_farmers', 'total_recommendations',
        'avg_model_accuracy', 'total_orders',
    ];
}
