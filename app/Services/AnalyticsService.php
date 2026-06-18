<?php

namespace App\Services;

use App\Models\City;
use App\Models\District;
use App\Models\House;
use App\Models\Preference;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Market-intelligence engine. Turns the raw stream of listings & closed deals
 * into the numbers a Syrian real-estate office has never had before:
 * average price per square metre by area, supply vs. demand, and price trends.
 */
class AnalyticsService
{
    /** Headline KPIs for the dashboard hero strip. */
    public function overview(): array
    {
        $total      = House::count();
        $available  = House::where('status', 'empty')->count();
        $closed     = House::where('status', 'occupied')->count();
        $avgPpm     = (float) House::where('area', '>', 0)->avg(DB::raw('price / area'));
        $offices    = \App\Models\Office::count();
        $demand     = Preference::active()->count();

        return [
            'total_listings'   => $total,
            'available'        => $available,
            'closed_deals'     => $closed,
            'avg_price_meter'  => round($avgPpm),
            'active_offices'   => $offices,
            'open_demand'      => $demand,
            'absorption_rate'  => $total > 0 ? round($closed / $total * 100) : 0,
        ];
    }

    /**
     * Average price-per-m² and inventory per district — the flagship table
     * investors use as a pricing reference.
     */
    public function pricePerMeterByDistrict(): array
    {
        return District::query()
            ->leftJoin('houses', 'houses.district_id', '=', 'districts.id')
            ->leftJoin('cities', 'cities.id', '=', 'districts.city_id')
            ->where('houses.area', '>', 0)
            ->groupBy('districts.id', 'districts.name', 'cities.name')
            ->select([
                'districts.id',
                'districts.name as district',
                'cities.name as city',
                DB::raw('ROUND(AVG(houses.price / houses.area)) as price_per_meter'),
                DB::raw('ROUND(AVG(houses.price)) as avg_price'),
                DB::raw('COUNT(houses.id) as listings'),
                DB::raw("SUM(CASE WHEN houses.type = 'rent' THEN 1 ELSE 0 END) as rent_count"),
                DB::raw("SUM(CASE WHEN houses.type = 'sale' THEN 1 ELSE 0 END) as sale_count"),
            ])
            ->orderByDesc('price_per_meter')
            ->get()
            ->toArray();
    }

    /** Average sale price per m² per city — for the bar chart. */
    public function pricePerMeterByCity(): array
    {
        return City::query()
            ->leftJoin('districts', 'districts.city_id', '=', 'cities.id')
            ->leftJoin('houses', 'houses.district_id', '=', 'districts.id')
            ->where('houses.area', '>', 0)
            ->groupBy('cities.id', 'cities.name')
            ->select([
                'cities.name as city',
                DB::raw('ROUND(AVG(houses.price / houses.area)) as price_per_meter'),
                DB::raw('COUNT(houses.id) as listings'),
            ])
            ->orderByDesc('price_per_meter')
            ->get()
            ->toArray();
    }

    /** Rent vs. sale split (for the doughnut chart). */
    public function typeSplit(): array
    {
        $rows = House::select('type', DB::raw('COUNT(*) as c'))->groupBy('type')->pluck('c', 'type');
        return [
            'rent' => (int) ($rows['rent'] ?? 0),
            'sale' => (int) ($rows['sale'] ?? 0),
        ];
    }

    /**
     * Average price-per-m² month over month for the last 6 months — the trend
     * line that tells investors whether the market is heating up or cooling.
     */
    public function priceTrend(int $months = 6): array
    {
        $labels = [];
        $values = [];
        $volume = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $start = Carbon::now()->subMonths($i)->startOfMonth();
            $end   = (clone $start)->endOfMonth();

            $ppm = (float) House::whereBetween('created_at', [$start, $end])
                ->where('area', '>', 0)
                ->avg(DB::raw('price / area'));

            $count = House::whereBetween('created_at', [$start, $end])->count();

            $labels[] = $start->translatedFormat('M Y');
            $values[] = round($ppm);
            $volume[] = $count;
        }

        return ['labels' => $labels, 'price_per_meter' => $values, 'volume' => $volume];
    }

    /** Supply (available listings) vs. demand (active buyer preferences) by city. */
    public function supplyDemand(): array
    {
        $supply = City::query()
            ->leftJoin('districts', 'districts.city_id', '=', 'cities.id')
            ->leftJoin('houses', function ($j) {
                $j->on('houses.district_id', '=', 'districts.id')->where('houses.status', '=', 'empty');
            })
            ->groupBy('cities.id', 'cities.name')
            ->selectRaw('cities.name as city, COUNT(houses.id) as cnt')
            ->pluck('cnt', 'city')
            ->toArray();

        $demand = City::query()
            ->leftJoin('preferences', function ($j) {
                $j->on('preferences.city_id', '=', 'cities.id')->where('preferences.is_active', '=', true);
            })
            ->groupBy('cities.id', 'cities.name')
            ->selectRaw('cities.name as city, COUNT(preferences.id) as cnt')
            ->pluck('cnt', 'city')
            ->toArray();

        $labels = array_keys($supply);
        return [
            'labels' => $labels,
            'supply' => array_map(fn ($c) => (int) ($supply[$c] ?? 0), $labels),
            'demand' => array_map(fn ($c) => (int) ($demand[$c] ?? 0), $labels),
        ];
    }

    /** Most-viewed listings — a proxy for hottest demand. */
    public function hottestListings(int $limit = 5)
    {
        return House::query()
            ->withCount('view')
            ->with(['district.city', 'office.provider'])
            ->orderByDesc('view_count')
            ->take($limit)
            ->get();
    }

    /** Everything the dashboard needs, in one call. */
    public function dashboard(): array
    {
        return [
            'overview'      => $this->overview(),
            'by_district'   => $this->pricePerMeterByDistrict(),
            'by_city'       => $this->pricePerMeterByCity(),
            'type_split'    => $this->typeSplit(),
            'trend'         => $this->priceTrend(),
            'supply_demand' => $this->supplyDemand(),
            'hottest'       => $this->hottestListings(),
        ];
    }
}
