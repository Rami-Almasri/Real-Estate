@extends('layouts.app')
@section('title', 'عقاراتي — لوحة المكتب')

@section('content')
<div class="max-w-7xl mx-auto px-4 pt-28 pb-16">
    <div class="grid lg:grid-cols-4 gap-6">
        @include('partials.dashboard-sidebar')

        <div class="lg:col-span-3 space-y-6">

            {{-- Header --}}
            <div class="glass rounded-3xl p-7 flex flex-wrap items-center justify-between gap-4" data-aos="fade-up">
                <div>
                    <h1 class="font-display font-black text-2xl md:text-3xl">عقاراتي 🏠</h1>
                    <div class="gold-line w-24 mt-3"></div>
                    <p class="text-white/50 text-sm mt-3">
                        المتبقّي من رصيدك:
                        <span class="text-gold font-bold">
                            {{ is_null($remaining) ? 'غير محدود' : number_format($remaining) . ' عقار' }}
                        </span>
                    </p>
                </div>
                <div class="text-left">
                    @if($canPublish)
                        <a href="{{ route('dashboard.listings.create') }}" class="btn-gold rounded-2xl px-6 py-3 text-sm shine">
                            ＋ نشر عقار
                        </a>
                    @else
                        <div class="space-y-2">
                            <span class="btn-ghost rounded-2xl px-6 py-3 text-sm inline-block opacity-50 cursor-not-allowed">
                                ＋ نشر عقار
                            </span>
                            <a href="{{ route('dashboard.subscription') }}" class="block text-xs text-gold hover:underline text-center">
                                استنفدت رصيدك — رقّ اشتراكك ←
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Listings --}}
            <div class="glass rounded-3xl p-5 md:p-7" data-aos="fade-up">
                @forelse($houses as $house)
                    @php
                        $typeColor = $house->type === 'rent' ? 'bg-sky-500/20 text-sky-300' : 'bg-gold/20 text-gold';
                        $typeLabel = $house->type === 'rent' ? 'إيجار' : 'بيع';
                        $isOccupied = $house->status === 'occupied';
                        $statusLabel = $isOccupied ? ($house->type === 'rent' ? 'مؤجّر' : 'مُباع') : 'متاح';
                    @endphp
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 glass-light rounded-2xl p-4 mb-3 card-hover">
                        <img src="{{ $house->cover }}" alt="{{ $house->title }}" loading="lazy"
                             class="w-full sm:w-28 h-40 sm:h-20 rounded-xl object-cover shrink-0">

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <h3 class="font-bold text-sm truncate">{{ $house->title ?: 'عقار' }}</h3>
                                <span class="badge {{ $typeColor }}">{{ $typeLabel }}</span>
                                <span class="badge {{ $isOccupied ? 'bg-red-500/20 text-red-300' : 'bg-emerald-500/20 text-emerald-300' }}">
                                    {{ $statusLabel }}
                                </span>
                            </div>
                            <p class="text-xs text-white/45 flex items-center gap-1">
                                📍 {{ $house->district?->name }}<span class="text-white/20">،</span> {{ $house->district?->city?->name }}
                            </p>
                            <div class="flex items-center gap-4 text-xs text-white/55 mt-2">
                                <span>🛏 {{ $house->rooms }} غرف</span>
                                <span>📐 {{ rtrim(rtrim(number_format($house->area, 1), '0'), '.') }} م²</span>
                                <span>🏢 ط{{ $house->floor }}</span>
                                <span>👁 {{ number_format($house->view_count) }}</span>
                            </div>
                        </div>

                        <div class="text-left shrink-0 flex sm:flex-col items-center sm:items-end justify-between gap-3">
                            <div>
                                <div class="font-display font-extrabold text-gold text-lg">${{ number_format($house->price) }}</div>
                                <div class="text-[11px] text-white/45">{{ number_format($house->price_per_meter) }}$ / م²</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('listings.show', $house) }}" target="_blank"
                                   class="text-xs btn-ghost rounded-xl px-3 py-2">عرض</a>
                                <form method="POST" action="{{ route('dashboard.listings.destroy', $house) }}"
                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا العقار؟ لا يمكن التراجع.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-xs rounded-xl px-3 py-2 border border-red-400/40 text-red-300 hover:bg-red-500/10 transition">
                                        🗑 حذف
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16">
                        <div class="text-5xl mb-4 opacity-40">🏠</div>
                        <h3 class="font-display font-bold text-lg mb-2">لا توجد عقارات بعد</h3>
                        <p class="text-white/45 text-sm mb-6">ابدأ بنشر أول عقار لتصل إلى المشترين المطابقين فوراً.</p>
                        <a href="{{ route('dashboard.listings.create') }}" class="btn-gold rounded-2xl px-6 py-3 text-sm inline-block shine">
                            ＋ نشر أول عقار
                        </a>
                    </div>
                @endforelse

                @if($houses->hasPages())
                    <div class="mt-6 pt-5 border-t border-white/10">
                        {{ $houses->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
