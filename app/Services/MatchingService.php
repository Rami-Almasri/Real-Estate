<?php

namespace App\Services;

use App\Models\House;
use App\Models\MatchNotification;
use App\Models\Preference;
use Illuminate\Support\Collection;

/**
 * The heart of the platform: matches buyer "standing preferences" against the
 * live inventory of houses. When an office publishes a new listing we instantly
 * scan every active preference and notify the buyers it satisfies — turning the
 * old paper-notebook workflow into a real-time demand engine.
 */
class MatchingService
{
    /**
     * Run matching for a single newly-created / updated house.
     * Returns the number of buyers newly notified.
     */
    public function runForHouse(House $house): int
    {
        if ($house->status !== 'empty') {
            return 0;
        }

        $preferences = Preference::active()
            ->where(function ($q) use ($house) {
                $q->whereNull('type')->orWhere('type', $house->type);
            })
            ->get();

        $notified = 0;
        foreach ($preferences as $preference) {
            $score = $this->score($house, $preference);
            if ($score === null) {
                continue;
            }

            $created = MatchNotification::firstOrCreate(
                [
                    'user_id'       => $preference->user_id,
                    'house_id'      => $house->id,
                    'preference_id' => $preference->id,
                ],
                ['score' => $score, 'is_read' => false]
            );

            if ($created->wasRecentlyCreated) {
                $notified++;
            }
        }

        return $notified;
    }

    /**
     * Back-fill matches for a single preference against existing inventory.
     * Used the moment a buyer saves a new search agent.
     */
    public function runForPreference(Preference $preference): Collection
    {
        $houses = $this->candidates($preference)->get();

        return $houses->map(function (House $house) use ($preference) {
            $score = $this->score($house, $preference);
            if ($score === null) {
                return null;
            }

            return MatchNotification::firstOrCreate(
                [
                    'user_id'       => $preference->user_id,
                    'house_id'      => $house->id,
                    'preference_id' => $preference->id,
                ],
                ['score' => $score, 'is_read' => false]
            );
        })->filter()->values();
    }

    /**
     * Live filter used by the "instant matches" buyer wizard — returns the
     * matching houses ordered by relevance, without persisting anything.
     */
    public function preview(array $criteria, int $limit = 24): Collection
    {
        $pref = new Preference($criteria);

        return $this->candidates($pref)
            ->with(['district.city', 'office.provider'])
            ->get()
            ->map(fn (House $h) => tap($h, fn ($house) => $house->match_score = $this->score($house, $pref)))
            ->filter(fn ($h) => $h->match_score !== null)
            ->sortByDesc('match_score')
            ->take($limit)
            ->values();
    }

    /** Base query of plausible candidates for a preference (hard filters only). */
    private function candidates(Preference $preference)
    {
        return House::query()
            ->where('status', 'empty')
            ->when($preference->type, fn ($q) => $q->where('type', $preference->type))
            ->when($preference->district_id, fn ($q) => $q->where('district_id', $preference->district_id))
            ->when($preference->city_id && ! $preference->district_id, function ($q) use ($preference) {
                $q->whereHas('district', fn ($d) => $d->where('city_id', $preference->city_id));
            })
            ->when($preference->max_price, fn ($q) => $q->where('price', '<=', $preference->max_price))
            ->when($preference->min_rooms, fn ($q) => $q->where('rooms', '>=', $preference->min_rooms))
            ->when($preference->min_area, fn ($q) => $q->where('area', '>=', $preference->min_area));
    }

    /**
     * Soft scoring 0-100. Returns null when a hard constraint is violated.
     * Closer-to-budget and roomier listings score higher, so the buyer sees
     * the best fit first.
     */
    private function score(House $house, Preference $preference): ?int
    {
        // Hard constraints.
        if ($preference->type && $house->type !== $preference->type) {
            return null;
        }
        if ($preference->district_id && $house->district_id != $preference->district_id) {
            return null;
        }
        if ($preference->city_id && ! $preference->district_id) {
            $cityId = optional($house->district)->city_id ?? optional(\App\Models\District::find($house->district_id))->city_id;
            if ($cityId != $preference->city_id) {
                return null;
            }
        }
        if ($preference->max_price && $house->price > $preference->max_price) {
            return null;
        }
        if ($preference->min_rooms && $house->rooms < $preference->min_rooms) {
            return null;
        }
        if ($preference->min_area && $house->area < $preference->min_area) {
            return null;
        }

        // Soft scoring.
        $score = 60;
        if ($preference->district_id) {
            $score += 15; // exact district
        }
        if ($preference->max_price) {
            // Reward listings comfortably under budget.
            $ratio = $house->price / max(1, (float) $preference->max_price);
            $score += (int) round((1 - $ratio) * 15);
        }
        if ($preference->min_rooms && $house->rooms > $preference->min_rooms) {
            $score += 5;
        }
        if ($house->featured) {
            $score += 5;
        }

        return max(0, min(100, $score));
    }
}
