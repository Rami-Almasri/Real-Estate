@extends('layouts.app')
@section('title', 'عقّار سوريا — ثورة رقمية في سوق العقارات')

@section('content')
{{-- ============ HERO ============ --}}
<section class="relative min-h-screen flex items-center pt-28 pb-16 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 grid lg:grid-cols-2 gap-12 items-center w-full">
        {{-- Copy --}}
        <div class="relative z-10">
            <div class="inline-flex items-center gap-2 glass-light rounded-full px-4 py-2 mb-7 text-xs text-gold"
                 data-aos="fade-down">
                <span class="w-2 h-2 rounded-full bg-gold animate-pulse"></span>
                أول منصة عقارية ذكية في سوريا · مدعومة بالبيانات
            </div>

            <h1 class="font-display font-black leading-[1.15] text-4xl md:text-6xl mb-6" data-aos="fade-up">
                من <span class="text-white/40 line-through decoration-red-400/60">دفاتر الورق</span><br>
                إلى <span class="gold-text">ذكاء السوق</span>
            </h1>

            <p class="text-white/60 text-lg leading-8 max-w-xl mb-9" data-aos="fade-up" data-aos-delay="100">
                نطابق آلاف العقارات مع طلبات المشترين فوراً، ونكشف متوسط سعر المتر في كل منطقة،
                ونؤتمت عقودك مع تنبيهات الاستحقاق. كل ما يحتاجه مكتبك العقاري في منصة واحدة.
            </p>

            <div class="flex flex-wrap gap-4 mb-12" data-aos="fade-up" data-aos-delay="200">
                <a href="{{ route('match.wizard') }}" class="btn-gold rounded-2xl px-7 py-4 text-sm shine">
                    🔍 جرّب المطابقة الذكية
                </a>
                <a href="{{ route('pricing') }}" class="btn-ghost rounded-2xl px-7 py-4 text-sm">
                    🏢 سجّل مكتبك العقاري
                </a>
            </div>

            {{-- mini stats --}}
            <div class="grid grid-cols-3 gap-4 max-w-lg" data-aos="fade-up" data-aos-delay="300">
                <div>
                    <div class="font-display text-3xl font-extrabold gold-text" data-count="{{ $overview['total_listings'] }}" data-fmt="1">0</div>
                    <div class="text-xs text-white/40 mt-1">عقار في المنصة</div>
                </div>
                <div>
                    <div class="font-display text-3xl font-extrabold gold-text" data-count="{{ $overview['active_offices'] }}">0</div>
                    <div class="text-xs text-white/40 mt-1">مكتب عقاري</div>
                </div>
                <div>
                    <div class="font-display text-3xl font-extrabold gold-text" data-count="{{ $overview['avg_price_meter'] }}" data-fmt="1">0</div>
                    <div class="text-xs text-white/40 mt-1">متوسط $/م²</div>
                </div>
            </div>
        </div>

        {{-- Visual --}}
        <div class="relative hidden lg:block" data-aos="fade-left" data-aos-delay="150">
            <div class="relative">
                <img src="https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=900&q=80"
                     class="rounded-[2rem] shadow-2xl border border-white/10 w-full h-[520px] object-cover">
                <div class="absolute inset-0 rounded-[2rem] bg-gradient-to-t from-ink via-transparent to-transparent"></div>

                {{-- floating match card --}}
                <div class="absolute -right-6 top-16 glass rounded-2xl p-4 w-60 shadow-glass animate-float">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-8 h-8 rounded-lg bg-emerald-500/20 grid place-items-center text-emerald-300">⚡</span>
                        <span class="text-xs text-white/70">مطابقة فورية</span>
                    </div>
                    <p class="text-sm font-bold">وُجد 12 عقار يطابق طلب المشتري</p>
                    <div class="mt-2 h-1.5 rounded-full bg-white/10 overflow-hidden"><div class="h-full bg-gold rounded-full" style="width:88%"></div></div>
                    <p class="text-[10px] text-gold mt-1">تطابق 88%</p>
                </div>

                {{-- floating price card --}}
                <div class="absolute -left-6 bottom-16 glass rounded-2xl p-4 w-56 shadow-glass animate-float" style="animation-delay:1.5s">
                    <div class="text-xs text-white/60 mb-1">متوسط سعر المتر · المزة</div>
                    <div class="font-display text-2xl font-extrabold gold-text">${{ number_format($overview['avg_price_meter']) }}</div>
                    <div class="flex items-center gap-1 text-[11px] text-emerald-300 mt-1">▲ 6.2% هذا الشهر</div>
                </div>
            </div>
        </div>
    </div>

    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 text-white/30 text-xs flex flex-col items-center gap-2 animate-bounce">
        <span>اكتشف المزيد</span><span>↓</span>
    </div>
</section>

{{-- ============ CITIES MARQUEE ============ --}}
<section class="py-8 border-y border-white/5 overflow-hidden">
    <div class="marquee whitespace-nowrap">
        @foreach(array_merge($cities->all(), $cities->all()) as $city)
            <div class="flex items-center gap-3 text-white/40">
                <span class="text-gold">◆</span>
                <span class="font-display font-bold text-lg">{{ $city->name }}</span>
                <span class="text-xs text-white/25">{{ $city->houses_count }} عقار</span>
            </div>
        @endforeach
    </div>
</section>

{{-- ============ POWER FEATURES ============ --}}
<section class="py-24 max-w-7xl mx-auto px-4">
    <div class="text-center mb-16" data-aos="fade-up">
        <span class="text-gold text-sm font-bold tracking-widest">القوة التقنية</span>
        <h2 class="font-display font-black text-3xl md:text-5xl mt-3">ثلاث قوى تقلب موازين السوق</h2>
        <div class="gold-line w-24 mx-auto mt-6"></div>
    </div>

    <div class="grid md:grid-cols-3 gap-7">
        @php $features = [
            ['icon'=>'⚡','t'=>'خوارزمية المطابقة','d'=>'المشتري يحدّد المواصفات والمنطقة، فيفلتر النظام آلاف العقارات في لحظة ويرسل إشعاراً فورياً عند توفّر عقار مطابق.','l'=>route('match.wizard'),'c'=>'الجرّب الآن'],
            ['icon'=>'📊','t'=>'لوحة تحليل السوق','d'=>'متوسط سعر المتر في كل منطقة بناءً على الصفقات الحقيقية، مع اتجاهات الأسعار ومؤشرات العرض والطلب — مرجع المستثمرين.','l'=>route('market.index'),'c'=>'استكشف اللوحة'],
            ['icon'=>'📄','t'=>'أتمتة العقود','d'=>'توليد عقود إيجار وبيع PDF احترافية بالعربية خلال ثوانٍ، مع نظام تنبيهات ذكي بمواعيد الاستحقاق والتجديد.','l'=>route('pricing'),'c'=>'للمكاتب'],
        ]; @endphp
        @foreach($features as $i => $f)
            <div class="glass rounded-3xl p-8 card-hover relative overflow-hidden" data-aos="fade-up" data-aos-delay="{{ $i*120 }}">
                <div class="absolute -top-10 -left-10 w-32 h-32 rounded-full bg-gold/5 blur-2xl"></div>
                <div class="w-16 h-16 rounded-2xl glass-light grid place-items-center text-3xl mb-6 shine">{{ $f['icon'] }}</div>
                <h3 class="font-display font-bold text-xl mb-3">{{ $f['t'] }}</h3>
                <p class="text-white/55 text-sm leading-7 mb-6">{{ $f['d'] }}</p>
                <a href="{{ $f['l'] }}" class="text-gold text-sm font-bold inline-flex items-center gap-2 group">
                    {{ $f['c'] }} <span class="group-hover:-translate-x-1 transition">←</span>
                </a>
            </div>
        @endforeach
    </div>
</section>

{{-- ============ FEATURED LISTINGS ============ --}}
<section class="py-16 max-w-7xl mx-auto px-4">
    <div class="flex items-end justify-between mb-10" data-aos="fade-up">
        <div>
            <span class="text-gold text-sm font-bold tracking-widest">عقارات مختارة</span>
            <h2 class="font-display font-black text-3xl md:text-4xl mt-2">الأبرز هذا الأسبوع</h2>
        </div>
        <a href="{{ route('listings.index') }}" class="btn-ghost rounded-xl px-5 py-3 text-sm hidden md:block">كل العقارات ←</a>
    </div>
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-7">
        @forelse($featured as $house)
            @include('partials.property-card', ['house' => $house])
        @empty
            <p class="text-white/40 col-span-3 text-center py-10">لا توجد عقارات بعد — كن أول من ينشر!</p>
        @endforelse
    </div>
</section>

{{-- ============ MARKET PREVIEW ============ --}}
@if(count($topAreas))
<section class="py-20 max-w-7xl mx-auto px-4">
    <div class="glass rounded-[2.5rem] p-8 md:p-12 relative overflow-hidden" data-aos="zoom-in">
        <div class="absolute -top-20 -right-20 w-72 h-72 rounded-full bg-emerald-500/10 blur-3xl"></div>
        <div class="grid lg:grid-cols-2 gap-12 items-center relative">
            <div>
                <span class="text-gold text-sm font-bold tracking-widest">ذكاء الأسعار</span>
                <h2 class="font-display font-black text-3xl md:text-4xl mt-3 mb-5">كم يساوي المتر في منطقتك؟</h2>
                <p class="text-white/55 leading-8 mb-8">
                    لأول مرة، أرقام حقيقية بدل التخمين. نحلّل كل صفقة تتم على المنصة لنعطيك متوسط سعر المتر،
                    لتعرف بالضبط «بكم تبيع» و«بكم تشتري».
                </p>
                <a href="{{ route('market.index') }}" class="btn-gold rounded-2xl px-7 py-4 text-sm inline-block">افتح لوحة التحليل الكاملة</a>
            </div>
            <div class="space-y-3">
                @foreach(array_slice($topAreas, 0, 5) as $i => $area)
                    <div class="glass-light rounded-2xl p-4 flex items-center justify-between" data-aos="fade-left" data-aos-delay="{{ $i*80 }}">
                        <div class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-lg bg-gold/15 text-gold grid place-items-center font-bold text-sm">{{ $i+1 }}</span>
                            <div>
                                <div class="font-bold text-sm">{{ $area['district'] }}</div>
                                <div class="text-[11px] text-white/40">{{ $area['city'] }} · {{ $area['listings'] }} عقار</div>
                            </div>
                        </div>
                        <div class="text-left">
                            <div class="font-display font-extrabold gold-text text-lg">${{ number_format($area['price_per_meter']) }}</div>
                            <div class="text-[10px] text-white/40">للمتر²</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- ============ OFFICE CTA ============ --}}
<section class="py-20 max-w-7xl mx-auto px-4">
    <div class="relative rounded-[2.5rem] overflow-hidden p-10 md:p-16 text-center"
         style="background:linear-gradient(135deg,#0f3d2e,#081b15)" data-aos="fade-up">
        <div class="absolute inset-0 opacity-20" style="background:radial-gradient(circle at 30% 20%, #d4af37, transparent 40%)"></div>
        <div class="relative">
            <h2 class="font-display font-black text-3xl md:text-5xl mb-5">مكتبك العقاري يستحق التطوّر</h2>
            <p class="text-white/60 max-w-2xl mx-auto leading-8 mb-9">
                انضمّ إلى المكاتب التي تركت الورق خلفها. اعرف أكثر الطلبات رواجاً، وبكم تبيع،
                وأتمت عقودك — كل ذلك باشتراك شهري واحد.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('register') }}" class="btn-gold rounded-2xl px-8 py-4 text-sm shine">ابدأ تجربتك المجانية</a>
                <a href="{{ route('pricing') }}" class="btn-ghost rounded-2xl px-8 py-4 text-sm">شاهد الباقات والأسعار</a>
            </div>
        </div>
    </div>
</section>
@endsection
