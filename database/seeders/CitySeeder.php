<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            ['name' => 'دمشق',     'latitude' => 33.5138, 'longitude' => 36.2765],
            ['name' => 'حلب',      'latitude' => 36.2021, 'longitude' => 37.1343],
            ['name' => 'حمص',      'latitude' => 34.7324, 'longitude' => 36.7137],
            ['name' => 'اللاذقية', 'latitude' => 35.5317, 'longitude' => 35.7915],
            ['name' => 'طرطوس',    'latitude' => 34.8890, 'longitude' => 35.8866],
        ];

        foreach ($cities as $city) {
            City::create($city);
        }
    }
}
