<?php

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $districts = [
            [
                'name' => 'Downtown',
                'city_id' => 1,
                'latitude' => 33.5138,
                'longitude' => 36.2765,
            ],
            [
                'name' => 'Baramkeh',
                'city_id' => 1,
                'latitude' => 33.5102,
                'longitude' => 36.2797,
            ],
            [
                'name' => 'Mazzeh',
                'city_id' => 1,
                'latitude' => 33.4934,
                'longitude' => 36.2515,
            ],
            [
                'name' => 'Jaramana',
                'city_id' => 2,
                'latitude' => 33.4857,
                'longitude' => 36.3556,
            ],
        ];

        foreach ($districts as $district) {
            District::create($district);
        }
    }
}