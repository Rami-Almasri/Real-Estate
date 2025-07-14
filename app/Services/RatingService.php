<?php

namespace App\Services;

use App\Models\Rate;

class RatingService
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}
    public function store(array $data)
    {
        $rate = Rate::create($data);
        return $rate;
    }
    public function update(array $data, Rate $rate)
    {

        $rate->update($data);
        return $rate;
    }
}
