<?php

namespace App\Services;

use App\Models\House;
use App\Models\View;
use Illuminate\Support\Facades\Auth;
use Exception;

class HousesService
{
    public $user_id;
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
        $house = House::create([
            'district_id' => $data['district_id'],
            'floor' => $data['floor'],
            'office_id' => Auth::user()->userable_id,
            'status' => $data['status'],
            'rooms' => $data['rooms'],
            'area' => $data['area'],
            'direction' => $data['direction'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'price' => $data['price'],

        ]);
        return $house;
    }

    public function show(House $house): House
    {
        /*
        if ($house->office_id !== Auth::user()->userable_id) {
            throw new Exception('Unauthorized showing the house not belong to you.');
        }
*/

        $this->user_id = Auth::user()->id;

        $view = View::create([
            'house_id' => $house->id,
            'user_id' => $this->user_id

        ]);

        return $house;
    }

    public function update(House $house, array $data): House
    {
        $house->update([
            'district_id' => $data['district_id'],
            'floor' => $data['floor'],
            'office_id' => Auth::user()->userable_id,
            'status' => $data['status'],
            'rooms' => $data['rooms'],
            'area' => $data['area'],
            'direction' => $data['direction'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'price' => $data['price'],

        ]);
        return $house;
    }

    public function delete(House $house)
    {
        if ($house->office_id !== Auth::user()->userable_id) {
            throw new Exception('Unauthorized delete the house not belong to you.');
        }

        $house->delete();
        return $house;
    }
}
