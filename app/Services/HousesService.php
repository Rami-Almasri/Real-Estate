<?php

namespace App\Services;

use App\Models\House;
use Illuminate\Support\Facades\Auth;
use Exception;

class HousesService
{
    public function listUserHouses()
    {
        //return House::where('owner_id', Auth::id())->paginate(10);
    }
    public function index()
    {
        $houses = House::all();
        return $houses;
    }
    public function create(array $data): House
    {
        //$data['owner_id'] = Auth::id();
        return House::create($data);
    }

    public function show(House $house): House
    {
        if ($house->owner_id !== Auth::id()) {
            throw new Exception('Unauthorized access to this house.');
        }

        return $house;
    }

    public function update(House $house, array $data): House
    {
        if ($house->owner_id !== Auth::id()) {
            throw new Exception('Unauthorized update attempt.');
        }

        $house->update($data);
        return $house;
    }

    public function delete(House $house)
    {
        if ($house->owner_id !== Auth::id()) {
            throw new Exception('Unauthorized delete attempt.');
        }

        $house->delete();
        return $house;
    }
}
