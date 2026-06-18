<?php

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    public function run(): void
    {
        $districts = [
            // دمشق (1)
            ['name' => 'المزة',        'city_id' => 1, 'latitude' => 33.4934, 'longitude' => 36.2515],
            ['name' => 'أبو رمانة',    'city_id' => 1, 'latitude' => 33.5189, 'longitude' => 36.2870],
            ['name' => 'المالكي',      'city_id' => 1, 'latitude' => 33.5126, 'longitude' => 36.2782],
            ['name' => 'كفرسوسة',      'city_id' => 1, 'latitude' => 33.4979, 'longitude' => 36.2698],
            ['name' => 'القصاع',       'city_id' => 1, 'latitude' => 33.5215, 'longitude' => 36.3132],
            // حلب (2)
            ['name' => 'الفرقان',      'city_id' => 2, 'latitude' => 36.2150, 'longitude' => 37.1400],
            ['name' => 'الموكامبو',    'city_id' => 2, 'latitude' => 36.2010, 'longitude' => 37.1480],
            ['name' => 'السبيل',       'city_id' => 2, 'latitude' => 36.2090, 'longitude' => 37.1620],
            // حمص (3)
            ['name' => 'الإنشاءات',    'city_id' => 3, 'latitude' => 34.7290, 'longitude' => 36.7200],
            ['name' => 'الحضارة',      'city_id' => 3, 'latitude' => 34.7360, 'longitude' => 36.7050],
            // اللاذقية (4)
            ['name' => 'الزراعة',      'city_id' => 4, 'latitude' => 35.5290, 'longitude' => 35.7960],
            ['name' => 'الصليبة',      'city_id' => 4, 'latitude' => 35.5230, 'longitude' => 35.7880],
            // طرطوس (5)
            ['name' => 'الكورنيش',     'city_id' => 5, 'latitude' => 34.8920, 'longitude' => 35.8790],
        ];

        foreach ($districts as $district) {
            District::create($district);
        }
    }
}
