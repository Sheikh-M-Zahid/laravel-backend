<?php

namespace Database\Seeders;

use App\Models\ClimateZone;
use App\Models\Crop;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---- Reference data: climate zones (must match ml-service zone names) ----
        $zones = [
            ['zone_name' => 'Barind Tract', 'region' => 'Rajshahi', 'description' => 'North-West (Barind) — drought-prone, low rainfall.'],
            ['zone_name' => 'Haor Region', 'region' => 'Sylhet', 'description' => 'North-East (Haor) — wetland, flash-flood prone.'],
            ['zone_name' => 'Ganges Plain', 'region' => 'Dhaka', 'description' => 'Central — fertile alluvial plain.'],
            ['zone_name' => 'Coastal Belt', 'region' => 'Khulna', 'description' => 'South-West (Coastal) — saline-affected soil.'],
            ['zone_name' => 'Chittagong Hill Tracts', 'region' => 'Chattogram', 'description' => 'South-East (Hilly) — hilly terrain, high rainfall.'],
            ['zone_name' => 'Brahmaputra Char', 'region' => 'Rangpur', 'description' => 'North — riverine char land.'],
        ];
        foreach ($zones as $z) {
            ClimateZone::firstOrCreate(['zone_name' => $z['zone_name']], $z);
        }

        // ---- Reference data: crops (must match ml-service/generate_data.py CROP_PROFILES) ----
        $crops = [
            ['crop_name' => 'Rice (Boro)', 'season' => 'Rabi', 'description' => 'Irrigated dry-season rice.'],
            ['crop_name' => 'Rice (Aman)', 'season' => 'Kharif-2', 'description' => 'Monsoon-season rice.'],
            ['crop_name' => 'Wheat', 'season' => 'Rabi', 'description' => 'Winter cereal crop.'],
            ['crop_name' => 'Jute', 'season' => 'Kharif-1', 'description' => "Bangladesh's golden fibre crop."],
            ['crop_name' => 'Potato', 'season' => 'Rabi', 'description' => 'Winter tuber crop.'],
            ['crop_name' => 'Maize', 'season' => 'Rabi', 'description' => 'Cereal, grown in Rabi and Kharif-1.'],
            ['crop_name' => 'Lentil (Masur)', 'season' => 'Rabi', 'description' => 'Winter pulse crop.'],
            ['crop_name' => 'Mustard', 'season' => 'Rabi', 'description' => 'Winter oilseed crop.'],
            ['crop_name' => 'Sugarcane', 'season' => 'Kharif-1', 'description' => 'Long-duration cash crop.'],
            ['crop_name' => 'Tea', 'season' => 'Kharif-1', 'description' => 'Perennial plantation crop (Sylhet/Chattogram).'],
            ['crop_name' => 'Tomato', 'season' => 'Rabi', 'description' => 'Winter vegetable.'],
            ['crop_name' => 'Onion', 'season' => 'Rabi', 'description' => 'Winter bulb crop.'],
            ['crop_name' => 'Chili', 'season' => 'Kharif-1', 'description' => 'Warm-season spice crop.'],
            ['crop_name' => 'Brinjal', 'season' => 'Kharif-1', 'description' => 'Warm-season vegetable.'],
            ['crop_name' => 'Cotton', 'season' => 'Kharif-1', 'description' => 'Fibre crop.'],
        ];
        foreach ($crops as $c) {
            Crop::firstOrCreate(['crop_name' => $c['crop_name']], $c);
        }

        // ---- Demo accounts ----
        User::firstOrCreate(
            ['email' => 'admin@agriadvisory.test'],
            ['name' => 'Platform Admin', 'password' => Hash::make('password123'), 'role' => 'admin', 'status' => 'active']
        );

        User::firstOrCreate(
            ['email' => 'farmer@agriadvisory.test'],
            ['name' => 'Karim Mia', 'password' => Hash::make('password123'), 'role' => 'farmer', 'status' => 'active']
        );

        User::firstOrCreate(
            ['email' => 'officer@agriadvisory.test'],
            ['name' => 'Extension Officer Rahim', 'password' => Hash::make('password123'), 'role' => 'extension_officer', 'status' => 'active']
        );

        $supplierUser = User::firstOrCreate(
            ['email' => 'supplier@agriadvisory.test'],
            ['name' => 'Green Agro Supplies', 'password' => Hash::make('password123'), 'role' => 'supplier', 'status' => 'active']
        );
        Supplier::firstOrCreate(
            ['user_id' => $supplierUser->id],
            ['business_name' => 'Green Agro Supplies Ltd.', 'business_address' => 'Rangpur Sadar, Rangpur', 'verified' => true]
        );
    }
}
