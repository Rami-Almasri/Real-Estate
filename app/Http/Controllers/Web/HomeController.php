<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\House;
use App\Services\AnalyticsService;

class HomeController extends Controller
{
    public function index(AnalyticsService $analytics)
    {
        $overview = $analytics->overview();

        $featured = House::with(['district.city', 'office.provider'])
            ->withCount('view')
            ->where('status', 'empty')
            ->orderByDesc('featured')
            ->orderByDesc('created_at')
            ->take(6)
            ->get();

        $cities = City::withCount(['districts'])
            ->get()
            ->map(function ($city) {
                $city->houses_count = House::whereHas('district', fn ($q) => $q->where('city_id', $city->id))->count();
                return $city;
            });

        return view('pages.home', [
            'overview'   => $overview,
            'featured'   => $featured,
            'cities'     => $cities,
            'topAreas'   => $analytics->pricePerMeterByDistrict(),
        ]);
    }
}
