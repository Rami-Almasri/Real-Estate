<?php

namespace App\Services;

use App\Models\City;
use Illuminate\Support\Arr;

class CityService
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}
    public function index()
    {
        $city = City::all();
        return $city;
    }
    public function store(array $data)
    {
        $city = City::create($data);
        return $city;
    }
    public function update(array $data, City $city)
    {
        $city->update($data);
        return $city;
    }
    public function destroy(City $city)
    {
        $city->delete();
        return $city;
    }
}