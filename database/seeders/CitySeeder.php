<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            [
                'name' => 'Damascus',
                'latitude' => 33.5138,
                'longitude' => 36.2765,
            ],
            [
                'name' => 'Aleppo',
                'latitude' => 36.2154,
                'longitude' => 37.1596,
            ],
            [
                'name' => 'Homs',
                'latitude' => 34.7304,
                'longitude' => 36.7098,
            ],
            [
                'name' => 'Latakia',
                'latitude' => 35.5196,
                'longitude' => 35.7906,
            ],
        ];

        foreach ($cities as $city) {
            City::create($city);
        }
    }
}