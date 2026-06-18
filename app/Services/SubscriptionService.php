<?php

namespace App\Services;

use App\Models\Office;
use App\Models\Subsbcribe;
use Illuminate\Support\Carbon;

/**
 * Sells and manages the SaaS subscriptions that real-estate offices pay for.
 * Plans are defined in config/plans.php. This is the revenue layer of the
 * platform — listings, matching and analytics are gated behind an active plan.
 */
class SubscriptionService
{
    public function plans(): array
    {
        return config('plans');
    }

    public function plan(string $key): ?array
    {
        return config("plans.$key");
    }

    /**
     * Subscribe (or renew) an office to a plan. If the office already has a
     * valid subscription we extend from its end date; otherwise from today.
     * In a real deployment this is called after a successful payment.
     */
    public function subscribe(Office $office, string $planKey): Subsbcribe
    {
        $plan = $this->plan($planKey);
        abort_if(! $plan, 422, 'Unknown plan');

        $current = $office->activeSubscription();
        $start   = $current ? Carbon::parse($current->end_date) : Carbon::now();
        $end     = (clone $start)->addDays($plan['duration_days']);

        // Supersede any previously active subscription.
        $office->subscriptions()->update(['is_active' => false]);

        return $office->subscriptions()->create([
            'plan'          => $plan['key'],
            'price'         => $plan['price'],
            'listing_limit' => $plan['listing_limit'],
            'start_date'    => $start->toDateString(),
            'end_date'      => $end->toDateString(),
            'is_active'     => true,
        ]);
    }

    public function isActive(Office $office): bool
    {
        return $office->hasActiveSubscription();
    }

    /** Remaining listing slots, or null if unlimited / no plan. */
    public function remainingListings(Office $office): ?int
    {
        $sub = $office->activeSubscription();
        if (! $sub || $sub->listing_limit === null) {
            return null; // unlimited
        }
        $used = $office->houses()->count();
        return max(0, $sub->listing_limit - $used);
    }

    public function canPublish(Office $office): bool
    {
        if (! $this->isActive($office)) {
            return false;
        }
        $remaining = $this->remainingListings($office);
        return $remaining === null || $remaining > 0;
    }

    /** Subscriptions expiring within $days days — drives renewal reminders. */
    public function expiringSoon(int $days = 7)
    {
        return Subsbcribe::query()
            ->where('is_active', true)
            ->whereDate('end_date', '>=', now())
            ->whereDate('end_date', '<=', now()->addDays($days))
            ->with('office.provider')
            ->get();
    }
}
