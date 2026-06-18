@extends('layouts.app')
@section('title', 'تصفّح العقارات — عقّار سوريا')

@section('content')
<section class="pt-32 pb-10 max-w-7xl mx-auto px-4">
    <div data-aos="fade-up">
        <span class="text-gold text-sm font-bold tracking-widest">سوق العقارات</span>
        <h1 class="font-display font-black text-3xl md:text-5xl mt-2">اعثر على عقارك التالي</h1>
        <p class="text-white/50 mt-3">{{ $houses->total() }} عقار متاح · فلترة فورية بالمواصفات التي تريدها</p>
    </div>

    {{-- Filter bar --}}
    <form method="GET" class="glass rounded-3xl p-5 md:p-6 mt-8 grid md:grid-cols-12 gap-3 items-end" data-aos="fade-up">
        <div class="md:col-span-3">
            <label class="text-xs text-white/40 mb-1 block">المدينة</label>
            <select name="city" class="w-full bg-ink-800/70 border border-white/10 rounded-xl px-3 py-3 text-sm focus:border-gold outline-none">
                <option value="">كل المدن</option>
                @foreach($cities as $c)<option value="{{ $c->id }}" @selected(($filters['city'] ?? '')==$c->id)>{{ $c->name }}</option>@endforeach
            </select>
        </div>
        <div class="md:col-span-3">
            <label class="text-xs text-white/40 mb-1 block">المنطقة</label>
            <select name="district" class="w-full bg-ink-800/70 border border-white/10 rounded-xl px-3 py-3 text-sm focus:border-gold outline-none">
                <option value="">كل المناطق</option>
                @foreach($districts as $d)<option value="{{ $d->id }}" @selected(($filters['district'] ?? '')==$d->id)>{{ $d->name }} — {{ $d->city?->name }}</option>@endforeach
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="text-xs text-white/40 mb-1 block">النوع</label>
            <select name="type" class="w-full bg-ink-800/70 border border-white/10 rounded-xl px-3 py-3 text-sm focus:border-gold outline-none">
                <option value="">الكل</option>
                <option value="rent" @selected(($filters['type'] ?? '')=='rent')>إيجار</option>
                <option value="sale" @selected(($filters['type'] ?? '')=='sale')>بيع</option>
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="text-xs text-white/40 mb-1 block">غرف (الأدنى)</label>
            <input type="number" name="rooms" min="1" value="{{ $filters['rooms'] ?? '' }}" placeholder="أي"
                   class="w-full bg-ink-800/70 border border-white/10 rounded-xl px-3 py-3 text-sm focus:border-gold outline-none">
        </div>
        <div class="md:col-span-2">
            <button class="btn-gold rounded-xl w-full py-3 text-sm">🔍 بحث</button>
        </div>

        <div class="md:col-span-3">
            <label class="text-xs text-white/40 mb-1 block">السعر الأقصى ($)</label>
            <input type="number" name="max_price" value="{{ $filters['max_price'] ?? '' }}" placeholder="بلا حد"
                   class="w-full bg-ink-800/70 border border-white/10 rounded-xl px-3 py-3 text-sm focus:border-gold outline-none">
        </div>
        <div class="md:col-span-3">
            <label class="text-xs text-white/40 mb-1 block">المساحة الأدنى (م²)</label>
            <input type="number" name="min_area" value="{{ $filters['min_area'] ?? '' }}" placeholder="أي"
                   class="w-full bg-ink-800/70 border border-white/10 rounded-xl px-3 py-3 text-sm focus:border-gold outline-none">
        </div>
        <div class="md:col-span-4">
            <label class="text-xs text-white/40 mb-1 block">الترتيب</label>
            <select name="sort" class="w-full bg-ink-800/70 border border-white/10 rounded-xl px-3 py-3 text-sm focus:border-gold outline-none">
                @php $sort = $filters['sort'] ?? ''; @endphp
                <option value="" @selected($sort=='')>الأحدث</option>
                <option value="price_asc" @selected($sort=='price_asc')>الأقل سعراً</option>
                <option value="price_desc" @selected($sort=='price_desc')>الأعلى سعراً</option>
                <option value="area_desc" @selected($sort=='area_desc')>الأكبر مساحة</option>
                <option value="popular" @selected($sort=='popular')>الأكثر مشاهدة</option>
            </select>
        </div>
        <div class="md:col-span-2">
            <a href="{{ route('listings.index') }}" class="btn-ghost rounded-xl w-full py-3 text-sm block text-center">إعادة تعيين</a>
        </div>
    </form>
</section>

<section class="max-w-7xl mx-auto px-4 pb-20">
    @if($houses->count())
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-7">
            @foreach($houses as $house)
                @include('partials.property-card', ['house' => $house])
            @endforeach
        </div>
        <div class="mt-12">{{ $houses->links() }}</div>
    @else
        <div class="glass rounded-3xl p-16 text-center" data-aos="fade-up">
            <div class="text-6xl mb-4">🏚️</div>
            <h3 class="font-display font-bold text-xl mb-2">لا توجد عقارات مطابقة</h3>
            <p class="text-white/50 mb-6">جرّب توسيع نطاق البحث أو إزالة بعض الفلاتر.</p>
            <a href="{{ route('listings.index') }}" class="btn-gold rounded-xl px-6 py-3 text-sm">عرض كل العقارات</a>
        </div>
    @endif
</section>
@endsection
