@extends('layouts.app')
@section('title', 'مطابقاتي — عقّار سوريا')

@section('content')
<section class="pt-32 pb-8 max-w-6xl mx-auto px-4">
    <div class="flex items-end justify-between" data-aos="fade-up">
        <div>
            <span class="text-gold text-sm font-bold tracking-widest">حسابي</span>
            <h1 class="font-display font-black text-3xl md:text-5xl mt-2">المطابقات الخاصة بك</h1>
            <p class="text-white/50 mt-3">العقارات التي طابقت طلباتك المحفوظة — مرتّبة حسب درجة التطابق.</p>
        </div>
        @if($matches->total())
            <form method="POST" action="{{ route('matches.read') }}">@csrf
                <button class="btn-ghost rounded-xl px-4 py-2.5 text-sm">تحديد الكل كمقروء</button>
            </form>
        @endif
    </div>
</section>

{{-- Saved preferences --}}
<section class="max-w-6xl mx-auto px-4 mb-10">
    <div class="glass rounded-3xl p-7" data-aos="fade-up">
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-display font-bold text-lg">طلباتي المحفوظة</h2>
            <a href="{{ route('match.wizard') }}" class="btn-gold rounded-xl px-4 py-2.5 text-sm">＋ طلب جديد</a>
        </div>
        @forelse($preferences as $p)
            <div class="glass-light rounded-2xl p-4 mb-3 flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-4 flex-wrap text-sm">
                    <span class="badge {{ $p->type==='rent' ? 'bg-sky-500/20 text-sky-300' : ($p->type==='sale' ? 'bg-gold/20 text-gold':'bg-white/10 text-white/60') }}">
                        {{ ['rent'=>'إيجار','sale'=>'بيع'][$p->type] ?? 'الكل' }}
                    </span>
                    <span class="text-white/60">📍 {{ $p->district?->name ?? $p->city?->name ?? 'كل المناطق' }}</span>
                    @if($p->max_price)<span class="text-white/60">💰 حتى ${{ number_format($p->max_price) }}</span>@endif
                    @if($p->min_rooms)<span class="text-white/60">🛏 {{ $p->min_rooms }}+ غرف</span>@endif
                    <span class="text-gold text-xs">{{ $p->matches_count }} مطابقة</span>
                </div>
                <form method="POST" action="{{ route('preferences.destroy', $p) }}" onsubmit="return confirm('حذف هذا الطلب؟')">
                    @csrf @method('DELETE')
                    <button class="text-white/30 hover:text-red-300 text-sm transition">🗑 حذف</button>
                </form>
            </div>
        @empty
            <p class="text-white/40 text-sm text-center py-6">لا توجد طلبات محفوظة بعد. <a href="{{ route('match.wizard') }}" class="text-gold">أنشئ طلبك الأول ←</a></p>
        @endforelse
    </div>
</section>

{{-- Matches --}}
<section class="max-w-6xl mx-auto px-4 pb-20">
    @if($matches->count())
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-7">
            @foreach($matches as $m)
                @php $house = $m->house; @endphp
                <a href="{{ route('listings.show', $house) }}" class="group glass rounded-3xl overflow-hidden card-hover block relative">
                    @unless($m->is_read)<span class="absolute top-3 left-3 z-10 w-3 h-3 rounded-full bg-gold animate-pulse"></span>@endunless
                    <div class="relative h-44 overflow-hidden">
                        <img src="{{ $house->cover }}" class="reveal-img w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                        <span class="absolute top-3 right-3 badge bg-gold text-ink font-black">تطابق {{ $m->score }}%</span>
                        <div class="absolute bottom-3 right-4 text-white font-display text-xl font-black">${{ number_format($house->price) }}</div>
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold mb-1">{{ $house->title ?: ($house->type==='rent'?'شقة للإيجار':'عقار للبيع') }}</h3>
                        <p class="text-white/45 text-xs mb-3">📍 {{ $house->district?->name }}، {{ $house->district?->city?->name }}</p>
                        <div class="flex justify-between text-xs text-white/60 border-t border-white/5 pt-3">
                            <span>🛏 {{ $house->rooms }}</span><span>📐 {{ rtrim(rtrim(number_format($house->area,1),'0'),'.') }} م²</span>
                            <span class="text-gold">${{ number_format($house->price_per_meter) }}/م²</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-10">{{ $matches->links() }}</div>
    @else
        <div class="glass rounded-3xl p-16 text-center" data-aos="fade-up">
            <div class="text-6xl mb-4">🔔</div>
            <h3 class="font-display font-bold text-xl mb-2">لا توجد مطابقات بعد</h3>
            <p class="text-white/50 mb-6">احفظ طلباً بمواصفاتك وسنخبرك فور توفّر عقار مناسب.</p>
            <a href="{{ route('match.wizard') }}" class="btn-gold rounded-xl px-6 py-3 text-sm">ابدأ المطابقة الذكية</a>
        </div>
    @endif
</section>
@endsection
