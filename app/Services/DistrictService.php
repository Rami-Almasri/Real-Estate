<?php

namespace App\Services;

use App\Models\District;

class DistrictService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function index()
    {
        $district = District::all();
        return $district;
    }
    public function store(array $data)
    {
        $district = District::create($data);
        return $district;
    }
    public function update(array $data, District $district)
    {
        $district->update($data);
        return $district;
    }
    public function destroy(District $district)
    {
        $district->delete();
        return $district;
    }
}