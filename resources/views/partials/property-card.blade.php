@php
    $typeLabel = $house->type === 'rent' ? 'للإيجار' : 'للبيع';
    $typeColor = $house->type === 'rent' ? 'bg-sky-500/20 text-sky-300' : 'bg-gold/20 text-gold';
@endphp
<a href="{{ route('listings.show', $house) }}" data-aos="fade-up"
   class="group glass rounded-3xl overflow-hidden card-hover block">
    <div class="relative h-52 overflow-hidden">
        <img src="{{ $house->cover }}" alt="{{ $house->title }}" loading="lazy"
             class="reveal-img w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent"></div>
        <div class="absolute top-3 right-3 flex gap-2">
            <span class="badge {{ $typeColor }}">{{ $typeLabel }}</span>
            @if($house->featured)<span class="badge bg-gold text-ink shine">⭐ مميّز</span>@endif
        </div>
        @if($house->status === 'occupied')
            <span class="absolute top-3 left-3 badge bg-red-500/30 text-red-200">مُؤجّر</span>
        @endif
        <div class="absolute bottom-3 right-4 left-4 flex items-end justify-between">
            <div>
                <div class="text-2xl font-extrabold text-white font-display">${{ number_format($house->price) }}</div>
                <div class="text-[11px] text-gold/90">{{ number_format($house->price_per_meter) }}$ / م²</div>
            </div>
            <div class="text-left text-white/70 text-xs">
                <div class="flex items-center gap-1 justify-end">👁 {{ $house->view_count ?? $house->view()->count() }}</div>
            </div>
        </div>
    </div>
    <div class="p-5">
        <h3 class="font-display font-bold text-base mb-1 truncate">
            {{ $house->title ?: ($house->type === 'rent' ? 'شقة للإيجار' : 'عقار للبيع') }}
        </h3>
        <p class="text-white/45 text-xs mb-4 flex items-center gap-1">
            📍 {{ $house->district?->name }}<span class="text-white/20">،</span> {{ $house->district?->city?->name }}
        </p>
        <div class="flex items-center justify-between text-xs text-white/70 border-t border-white/5 pt-4">
            <span class="flex items-center gap-1.5">🛏 {{ $house->rooms }} غرف</span>
            <span class="flex items-center gap-1.5">📐 {{ rtrim(rtrim(number_format($house->area,1),'0'),'.') }} م²</span>
            <span class="flex items-center gap-1.5">🏢 ط{{ $house->floor }}</span>
        </div>
    </div>
</a>
