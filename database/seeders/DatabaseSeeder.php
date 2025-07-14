<?php

namespace Database\Seeders;

use App\Models\Office;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        /*
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
*/
        $this->call([
            CitySeeder::class,
            DistrictSeeder::class,

        ]);
        $user = User::create([
            'name' => 'test',
            'email' => 'test@gmail.com',
            'password' => Hash::make('111111'),
            'userable_id' => 1,
            'userable_type' => User::class
        ]);

        $provider = Office::create([

            'address' => 'address',
            'district_id' => 1,
            'latitude' => 1,
            'longitude' => 1
        ]);
        $provider->provider()->create([
            'name' => 'office',
            'email' => 'office@gmail.com',
            'password' => Hash::make('111111'),
        ]);
    }
}