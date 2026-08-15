<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Crop extends Model
{
    public $timestamps = false;
    protected $fillable = ['crop_name', 'season', 'description'];

    public function calendarEntries() { return $this->hasMany(CropCalendar::class, 'crop_id'); }
    public function recommendations() { return $this->hasMany(Recommendation::class, 'recommended_crop_id'); }

    /**
     * The ML service returns crop names as plain strings (e.g. "Rice (Boro)").
     * This resolves that string to a crops.id, creating a reference row on the
     * fly (default season Kharif-1) if the ML model ever returns a crop name
     * that hasn't been seeded yet -- keeps the FK constraint satisfiable
     * without the request failing.
     */
    public static function findByNameOrCreate(string $cropName): self
    {
        return self::firstOrCreate(
            ['crop_name' => $cropName],
            ['season' => 'Kharif-1', 'description' => null]
        );
    }
}

