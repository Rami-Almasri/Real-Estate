<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\District;
use App\Models\House;
use Illuminate\Http\Request;

class ListingWebController extends Controller
{
    public function index(Request $request)
    {
        $houses = House::query()
            ->with(['district.city', 'office.provider'])
            ->withCount(['view', 'rate'])
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->when($request->filled('city'), fn ($q) => $q->whereHas('district', fn ($d) => $d->where('city_id', $request->city)))
            ->when($request->filled('district'), fn ($q) => $q->where('district_id', $request->district))
            ->when($request->filled('rooms'), fn ($q) => $q->where('rooms', '>=', (int) $request->rooms))
            ->when($request->filled('min_price'), fn ($q) => $q->where('price', '>=', (float) $request->min_price))
            ->when($request->filled('max_price'), fn ($q) => $q->where('price', '<=', (float) $request->max_price))
            ->when($request->filled('min_area'), fn ($q) => $q->where('area', '>=', (float) $request->min_area))
            ->when($request->status !== 'all', fn ($q) => $q->where('status', 'empty'));

        $houses = match ($request->get('sort')) {
            'price_asc'  => $houses->orderBy('price'),
            'price_desc' => $houses->orderByDesc('price'),
            'area_desc'  => $houses->orderByDesc('area'),
            'popular'    => $houses->orderByDesc('view_count'),
            default      => $houses->orderByDesc('featured')->orderByDesc('created_at'),
        };

        return view('pages.listings', [
            'houses'    => $houses->paginate(12)->withQueryString(),
            'cities'    => City::all(),
            'districts' => District::with('city')->get(),
            'filters'   => $request->all(),
        ]);
    }

    public function show(House $house)
    {
        $house->load(['district.city', 'office.provider', 'rate']);
        $house->loadCount(['view', 'rate', 'favortie']);

        // Track the view (only for logged-in users — views.user_id is required).
        if (auth()->check()) {
            $house->view()->create(['user_id' => auth()->id()]);
        }

        $similar = House::with('district.city')
            ->where('id', '!=', $house->id)
            ->where('district_id', $house->district_id)
            ->where('status', 'empty')
            ->take(3)->get();

        if ($similar->count() < 3) {
            $similar = $similar->merge(
                House::with('district.city')
                    ->where('id', '!=', $house->id)
                    ->where('type', $house->type)
                    ->whereNotIn('id', $similar->pluck('id')->push($house->id))
                    ->take(3 - $similar->count())->get()
            );
        }

        return view('pages.listing-show', [
            'house'   => $house,
            'similar' => $similar,
            'ppm'     => $house->price_per_meter,
        ]);
    }
}
