@extends('layouts.app')
@section('title', ($house->title ?: 'عقار') . ' — عقّار سوريا')

@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

@section('content')
@php
    $isRent = $house->type === 'rent';
    $cycles = ['monthly' => 'شهرياً'];
@endphp
<section class="pt-28 pb-6 max-w-7xl mx-auto px-4">
    <a href="{{ route('listings.index') }}" class="text-white/40 text-sm hover:text-gold transition">← العودة إلى العقارات</a>
</section>

<section class="max-w-7xl mx-auto px-4 grid lg:grid-cols-3 gap-7">
    {{-- Main --}}
    <div class="lg:col-span-2 space-y-7">
        <div class="glass rounded-3xl overflow-hidden" data-aos="fade-up">
            <div class="relative h-[420px]">
                <img src="{{ $house->cover }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                <div class="absolute top-4 right-4 flex gap-2">
                    <span class="badge {{ $isRent ? 'bg-sky-500/25 text-sky-200':'bg-gold/25 text-gold' }}">{{ $isRent ? 'للإيجار':'للبيع' }}</span>
                    @if($house->featured)<span class="badge bg-gold text-ink">⭐ مميّز</span>@endif
                    <span class="badge {{ $house->status==='empty' ? 'bg-emerald-500/25 text-emerald-200':'bg-red-500/25 text-red-200' }}">
                        {{ $house->status==='empty' ? 'متاح الآن' : ($isRent ? 'مُؤجّر':'مُباع') }}
                    </span>
                </div>
                <div class="absolute bottom-5 right-6 left-6 flex items-end justify-between">
                    <div>
                        <h1 class="font-display font-black text-3xl text-white">{{ $house->title ?: ($isRent ? 'شقة للإيجار':'عقار للبيع') }}</h1>
                        <p class="text-white/70 text-sm mt-1">📍 {{ $house->district?->name }}، {{ $house->district?->city?->name }}</p>
                    </div>
                    <div class="text-left">
                        <div class="font-display text-4xl font-black gold-text">${{ number_format($house->price) }}</div>
                        <div class="text-gold/80 text-sm">{{ number_format($ppm) }}$ / م²</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Key specs --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4" data-aos="fade-up">
            @foreach([
                ['🛏','الغرف',$house->rooms],
                ['📐','المساحة', rtrim(rtrim(number_format($house->area,1),'0'),'.').' م²'],
                ['🏢','الطابق','الطابق '.$house->floor],
                ['🧭','الاتجاه',$house->direction],
            ] as $spec)
                <div class="glass-light rounded-2xl p-5 text-center">
                    <div class="text-2xl mb-2">{{ $spec[0] }}</div>
                    <div class="text-white/40 text-xs">{{ $spec[1] }}</div>
                    <div class="font-bold mt-1">{{ $spec[2] }}</div>
                </div>
            @endforeach
        </div>

        {{-- Description --}}
        <div class="glass rounded-3xl p-7" data-aos="fade-up">
            <h2 class="font-display font-bold text-lg mb-4">تفاصيل العقار</h2>
            <p class="text-white/60 leading-8">
                {{ $house->description ?: 'عقار '.($isRent?'للإيجار':'للبيع').' في '.$house->district?->name.'، '.$house->district?->city?->name.'. مساحة '.rtrim(rtrim(number_format($house->area,1),'0'),'.').' م²، '.$house->rooms.' غرف، في الطابق '.$house->floor.' باتجاه '.$house->direction.'. عقار مثالي يجمع بين الموقع المميّز والسعر التنافسي.' }}
            </p>
        </div>

        {{-- Map --}}
        <div class="glass rounded-3xl p-3 overflow-hidden" data-aos="fade-up">
            <div id="map" class="w-full h-72 rounded-2xl"></div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">
        {{-- Office --}}
        <div class="glass rounded-3xl p-6" data-aos="fade-left">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-xl grid place-items-center text-gold text-xl font-black"
                     style="background:linear-gradient(135deg,#0f3d2e,#081b15);border:1px solid rgba(212,175,55,.4)">🏢</div>
                <div>
                    <div class="font-bold">{{ $house->office?->name ?? 'مكتب عقاري' }}</div>
                    <div class="text-xs text-gold/80">✔ مكتب موثّق</div>
                </div>
            </div>
            <div class="text-sm text-white/50 space-y-2 border-t border-white/5 pt-4">
                <p>📍 {{ $house->office?->address }}</p>
                <p>🗺 {{ $house->district?->name }}، {{ $house->district?->city?->name }}</p>
            </div>
            <div class="flex gap-2 mt-5">
                <a href="#" class="btn-gold rounded-xl flex-1 py-3 text-sm text-center">📞 تواصل</a>
                <a href="#" class="btn-ghost rounded-xl px-4 py-3 text-sm">💬</a>
            </div>
        </div>

        {{-- Stats --}}
        <div class="glass rounded-3xl p-6 space-y-4" data-aos="fade-left" data-aos-delay="80">
            <h3 class="font-display font-bold text-sm text-gold">إحصائيات العقار</h3>
            <div class="flex justify-between text-sm"><span class="text-white/50">👁 المشاهدات</span><span class="font-bold">{{ $house->view_count }}</span></div>
            <div class="flex justify-between text-sm"><span class="text-white/50">⭐ التقييم</span><span class="font-bold">{{ number_format($house->averageRating(),1) }} / 5</span></div>
            <div class="flex justify-between text-sm"><span class="text-white/50">❤ المفضّلة</span><span class="font-bold">{{ $house->favortie_count }}</span></div>
            <div class="flex justify-between text-sm"><span class="text-white/50">🏷 سعر المتر</span><span class="font-bold gold-text">${{ number_format($ppm) }}</span></div>
        </div>

        {{-- Want similar CTA --}}
        <div class="glass rounded-3xl p-6 text-center" style="background:linear-gradient(135deg,rgba(15,61,46,.6),rgba(8,27,21,.6))" data-aos="fade-left" data-aos-delay="160">
            <div class="text-3xl mb-2">⚡</div>
            <h3 class="font-display font-bold mb-2">تريد عقاراً مثل هذا؟</h3>
            <p class="text-white/50 text-sm mb-4">احفظ مواصفاتك ودع الخوارزمية تنبّهك فور توفّر عقار مطابق.</p>
            <a href="{{ route('match.wizard') }}" class="btn-gold rounded-xl px-5 py-3 text-sm block">فعّل المطابقة الذكية</a>
        </div>
    </div>
</section>

{{-- Similar --}}
@if($similar->count())
<section class="max-w-7xl mx-auto px-4 mt-20">
    <h2 class="font-display font-black text-2xl mb-8" data-aos="fade-up">عقارات مشابهة</h2>
    <div class="grid md:grid-cols-3 gap-7">
        @foreach($similar as $house)
            @include('partials.property-card', ['house' => $house])
        @endforeach
    </div>
</section>
@endif
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const lat = {{ $house->latitude ?: 33.5138 }}, lng = {{ $house->longitude ?: 36.2765 }};
    const map = L.map('map', { scrollWheelZoom:false }).setView([lat, lng], 14);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        attribution:'© OpenStreetMap, © CARTO', maxZoom:19
    }).addTo(map);
    const gold = L.divIcon({ className:'', html:'<div style="width:22px;height:22px;background:#d4af37;border:3px solid #0a0f0d;border-radius:50%;box-shadow:0 0 0 6px rgba(212,175,55,.3)"></div>', iconSize:[22,22] });
    L.marker([lat,lng], { icon:gold }).addTo(map)
        .bindPopup('{{ addslashes($house->district?->name) }} — ${{ number_format($house->price) }}');
</script>
@endpush
