<?php

namespace App\Services;

use App\Models\House;
use App\Models\Office;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Hash;

class OfficeService
{
    /**
     * Create a new class instance.
     */
    public function create(array $data)
    {
        //return Office::create($data);
        $provider = Office::create([
            'address' => $data['address'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'district_id' => $data['district_id']
        ]);
        $provider->provider()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        $provider->load('provider');
        return $provider;
    }

    public function show(Office $office): Office
    {
        return $office->load(['owner', 'district']);
    }

    public function index()
    {
        return Office::with(['district', 'provider'])->paginate(10);
    }

    public function update(array $data, Office $office)
    {
        // تحديث بيانات المكتب
        $office->update([
            'address'     => $data['address'],
            'latitude'    => $data['latitude'],
            'longitude'   => $data['longitude'],
            'district_id' => $data['district_id']
        ]);

        // تحديث بيانات المزود المرتبط
        $office->provider()->update([
            'name'     => $data['name'],
            'email'    => $data['email'],
            // إذا بدك تحدث الباسورد فقط إذا أُرسل:
            'password' => isset($data['password']) ? Hash::make($data['password']) : $office->provider->password,
        ]);

        // تحميل العلاقة مجددًا
        $office->load('provider');

        return $office;
    }


    public function delete(Office $office)
    {
        $office->delete();
        return $office;
    }
    public function info()
    {
        $officeId = Auth::user()->userable_id;

        return [
            'total_houses'     => House::where('office_id', $officeId)->count(),
            'all_houses'       => House::where('office_id', $officeId)->withCount('view')->with('district')->get(),
            'occupied_rent'    => House::where('office_id', $officeId)
                ->where('status', 'occupied')
                ->where('type', 'rent')
                ->get(),
            'occupied_sale'    => House::where('office_id', $officeId)
                ->where('status', 'occupied')
                ->where('type', 'sale')
                ->get(),
        ];
    }
}
