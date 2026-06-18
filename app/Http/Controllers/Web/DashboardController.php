<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\House;
use App\Models\MatchNotification;
use App\Models\Office;
use App\Services\ContractService;
use App\Services\MatchingService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private SubscriptionService $subscriptions,
        private MatchingService $matching,
        private ContractService $contracts,
    ) {}

    /** Resolve the office for the current user or bounce non-office accounts. */
    private function office(Request $request): Office
    {
        $office = $request->user()->office;
        abort_unless($office, 403, 'هذه المنطقة مخصّصة للمكاتب العقارية.');
        return $office;
    }

    public function index(Request $request)
    {
        $office = $this->office($request);
        $office->load('district.city');

        $listings = $office->houses()->withCount('view')->get();
        $leadsCount = MatchNotification::whereHas('house', fn ($q) => $q->where('office_id', $office->id))->count();

        return view('dashboard.index', [
            'office'        => $office,
            'subscription'  => $office->activeSubscription(),
            'plan'          => $office->activeSubscription() ? $this->subscriptions->plan($office->activeSubscription()->plan) : null,
            'remaining'     => $this->subscriptions->remainingListings($office),
            'stats'         => [
                'listings'   => $listings->count(),
                'available'  => $listings->where('status', 'empty')->count(),
                'occupied'   => $listings->where('status', 'occupied')->count(),
                'views'      => $listings->sum('view_count'),
                'leads'      => $leadsCount,
                'contracts'  => $office->contracts()->count(),
                'portfolio'  => $listings->sum('price'),
            ],
            'topListings'   => $listings->sortByDesc('view_count')->take(5),
            'dueAlerts'     => $this->contracts->dueAlerts($office, 30),
        ]);
    }

    public function listings(Request $request)
    {
        $office = $this->office($request);

        return view('dashboard.listings', [
            'office'    => $office,
            'houses'    => $office->houses()->with('district.city')->withCount('view')->latest()->paginate(10),
            'remaining' => $this->subscriptions->remainingListings($office),
            'canPublish'=> $this->subscriptions->canPublish($office),
        ]);
    }

    public function createListing(Request $request)
    {
        $office = $this->office($request);

        if (! $this->subscriptions->canPublish($office)) {
            return redirect()->route('dashboard.subscription')
                ->with('error', 'لقد وصلت إلى حد باقتك أو أن اشتراكك غير فعّال. قم بالترقية للنشر.');
        }

        return view('dashboard.listing-create', [
            'districts' => District::with('city')->get(),
        ]);
    }

    public function storeListing(Request $request)
    {
        $office = $this->office($request);

        abort_unless($this->subscriptions->canPublish($office), 403, 'اشتراكك لا يسمح بنشر المزيد من العقارات.');

        $data = $request->validate([
            'title'       => ['nullable', 'string', 'max:140'],
            'description' => ['nullable', 'string', 'max:2000'],
            'district_id' => ['required', 'exists:districts,id'],
            'type'        => ['required', 'in:rent,sale'],
            'rooms'       => ['required', 'integer', 'min:1', 'max:30'],
            'floor'       => ['required', 'integer', 'min:0', 'max:60'],
            'area'        => ['required', 'numeric', 'min:10'],
            'direction'   => ['required', 'string', 'max:40'],
            'price'       => ['required', 'numeric', 'min:1'],
            'cover_image' => ['nullable', 'url'],
            'featured'    => ['nullable', 'boolean'],
        ]);

        $district = District::find($data['district_id']);

        $house = $office->houses()->create(array_merge($data, [
            'status'    => 'empty',
            'latitude'  => $district->latitude ?? 0,
            'longitude' => $district->longitude ?? 0,
            'featured'  => $request->boolean('featured'),
        ]));

        // Fire the matching engine: notify every buyer whose preference fits.
        $notified = $this->matching->runForHouse($house);

        return redirect()->route('dashboard.listings')
            ->with('success', "تم نشر العقار! 🚀 وصل إشعار فوري إلى {$notified} مشترٍ مطابق.");
    }

    public function destroyListing(Request $request, House $house)
    {
        $office = $this->office($request);
        abort_unless($house->office_id === $office->id, 403);
        $house->delete();

        return back()->with('success', 'تم حذف العقار.');
    }

    public function leads(Request $request)
    {
        $office = $this->office($request);

        $leads = MatchNotification::query()
            ->whereHas('house', fn ($q) => $q->where('office_id', $office->id))
            ->with(['user', 'house.district', 'preference.city', 'preference.district'])
            ->latest()
            ->paginate(15);

        return view('dashboard.leads', compact('office', 'leads'));
    }
}
