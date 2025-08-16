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
        return House::withCount('view')
            ->get();
    }
    public function create(array $data): House
    {
        $house = House::create([
            'district_id' => $data['district_id'],
            'floor' => $data['floor'],
            'office_id' => Auth::user()->userable_id,
            'status' => $data['status'],
            'rooms' => $data['rooms'],
            'type' => $data['type'],
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
        $user = Auth::user();

        if ($user) {
            $this->user_id = $user->id;

            $alreadyViewed = $house->view()
                ->where('user_id', $this->user_id)
                ->exists();

            if (!$alreadyViewed) {
                View::create([
                    'house_id' => $house->id,
                    'user_id' => $this->user_id
                ]);
            }
        }


        $house->loadCount('view');


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
    public function filter(array $data)
    {
        $query = House::query();

        if (!empty($data['price'])) {
            $query->where('price', ">=", $data['price']);
        }

        if (!empty($data['type'])) {
            $query->where('type', $data['type']);
        }

        if (!empty($data['rooms'])) {
            $query->where('rooms', '<=', $data['rooms']);
        }

        // تحميل عدد المشاهدات ومتوسط التقييم
        $query->withCount('view');

        return $query->get();
    }
}
