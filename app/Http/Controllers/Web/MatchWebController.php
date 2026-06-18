<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\District;
use App\Models\Preference;
use App\Services\MatchingService;
use Illuminate\Http\Request;

class MatchWebController extends Controller
{
    public function __construct(private MatchingService $matching) {}

    /** Public instant-match wizard. */
    public function wizard()
    {
        return view('pages.match', [
            'cities'    => City::all(),
            'districts' => District::with('city')->get(),
        ]);
    }

    /** Live preview JSON consumed by the wizard via fetch(). */
    public function preview(Request $request)
    {
        $criteria = $request->only(['city_id', 'district_id', 'type', 'min_rooms', 'max_price', 'min_area']);
        $criteria = array_filter($criteria, fn ($v) => $v !== null && $v !== '');

        $houses = $this->matching->preview($criteria);

        return response()->json([
            'count'   => $houses->count(),
            'results' => $houses->map(fn ($h) => [
                'id'       => $h->id,
                'title'    => $h->title ?: ($h->type === 'rent' ? 'شقة للإيجار' : 'عقار للبيع'),
                'price'    => (float) $h->price,
                'area'     => (float) $h->area,
                'rooms'    => $h->rooms,
                'type'     => $h->type,
                'score'    => $h->match_score,
                'ppm'      => $h->price_per_meter,
                'district' => $h->district?->name,
                'city'     => $h->district?->city?->name,
                'cover'    => $h->cover,
                'url'      => route('listings.show', $h),
            ])->values(),
        ]);
    }

    /** Buyer saves a standing preference; we back-fill matches immediately. */
    public function storePreference(Request $request)
    {
        $data = $request->validate([
            'city_id'     => ['nullable', 'exists:cities,id'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'type'        => ['nullable', 'in:rent,sale'],
            'min_rooms'   => ['nullable', 'integer', 'min:1', 'max:20'],
            'max_price'   => ['nullable', 'numeric', 'min:0'],
            'min_area'    => ['nullable', 'numeric', 'min:0'],
            'label'       => ['nullable', 'string', 'max:100'],
        ]);
        $data['user_id'] = $request->user()->id;
        $data['is_active'] = true;

        $preference = Preference::create($data);
        $matches = $this->matching->runForPreference($preference);

        return redirect()->route('account.matches')
            ->with('success', "تم حفظ طلبك بنجاح ووجدنا {$matches->count()} عقار مطابق فوراً!");
    }

    public function destroyPreference(Request $request, Preference $preference)
    {
        abort_unless($preference->user_id === $request->user()->id, 403);
        $preference->delete();

        return back()->with('success', 'تم حذف الطلب.');
    }

    public function myMatches(Request $request)
    {
        $user = $request->user();

        $preferences = $user->preferences()->with(['city', 'district'])->withCount('matches')->latest()->get();

        $matches = $user->matchNotifications()
            ->with(['house.district.city', 'house.office.provider', 'preference'])
            ->latest()
            ->paginate(12);

        return view('pages.my-matches', compact('preferences', 'matches'));
    }

    public function markAllRead(Request $request)
    {
        $request->user()->matchNotifications()->update(['is_read' => true]);
        return back();
    }
}
