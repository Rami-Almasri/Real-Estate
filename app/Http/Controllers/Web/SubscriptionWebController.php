<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Office;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class SubscriptionWebController extends Controller
{
    public function __construct(private SubscriptionService $subscriptions) {}

    /** Public pricing page. */
    public function pricing()
    {
        return view('pages.pricing', [
            'plans' => $this->subscriptions->plans(),
        ]);
    }

    private function office(Request $request): Office
    {
        $office = $request->user()->office;
        abort_unless($office, 403, 'الاشتراكات مخصّصة للمكاتب العقارية. سجّل حساب مكتب للمتابعة.');
        return $office;
    }

    public function show(Request $request)
    {
        $office = $this->office($request);
        $active = $office->activeSubscription();

        return view('dashboard.subscription', [
            'office'       => $office,
            'plans'        => $this->subscriptions->plans(),
            'subscription' => $active,
            'currentPlan'  => $active ? $this->subscriptions->plan($active->plan) : null,
            'history'      => $office->subscriptions()->latest()->take(10)->get(),
            'remaining'    => $this->subscriptions->remainingListings($office),
        ]);
    }

    public function subscribe(Request $request, string $plan)
    {
        $office = $this->office($request);

        abort_unless($this->subscriptions->plan($plan), 404);

        // In production a payment gateway would run here; we activate directly.
        $subscription = $this->subscriptions->subscribe($office, $plan);

        return redirect()->route('dashboard.subscription')
            ->with('success', "تم تفعيل باقة «{$this->subscriptions->plan($plan)['name']}» حتى {$subscription->end_date->format('Y-m-d')} 🎉");
    }
}
