@extends('layouts.app')
@section('title', 'طلبات المشترين — لوحة المكتب')

@section('content')
<div class="max-w-7xl mx-auto px-4 pt-28 pb-16">
    <div class="grid lg:grid-cols-4 gap-6">
        @include('partials.dashboard-sidebar')

        <div class="lg:col-span-3 space-y-6">

            {{-- Header --}}
            <div class="glass rounded-3xl p-7" data-aos="fade-up">
                <h1 class="font-display font-black text-2xl md:text-3xl">طلبات المشترين المطابقة 🎯</h1>
                <div class="gold-line w-24 mt-3 mb-4"></div>
                <p class="text-white/55 text-sm leading-7">
                    هؤلاء مشترون حفظوا تفضيلاتهم، وتطابقت مع عقاراتك المنشورة. إنهم عملاء محتملون "دافئون" مهتمون فعلاً
                    بما يشبه ما تعرضه — تواصل معهم لإغلاق الصفقة أسرع.
                </p>
            </div>

            {{-- Leads list --}}
            <div class="space-y-4">
                @forelse($leads as $lead)
                    @php
                        $score = (int) $lead->score;
                        $scoreColor = $score >= 75 ? 'text-emerald-300' : ($score >= 50 ? 'text-gold' : 'text-white/60');
                        $pref = $lead->preference;
                    @endphp
                    <div class="glass rounded-3xl p-6 card-hover {{ $lead->is_read ? '' : 'border-r-4 border-gold' }}"
                         data-aos="fade-up">
                        <div class="flex flex-col md:flex-row md:items-center gap-5">

                            {{-- Buyer --}}
                            <div class="flex items-center gap-4 min-w-0 flex-1">
                                <div class="w-12 h-12 rounded-2xl grid place-items-center shrink-0 bg-gold/15 text-gold font-display font-black text-lg">
                                    {{ mb_substr($lead->user?->name ?? '؟', 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-bold text-sm truncate">{{ $lead->user?->name }}</h3>
                                        @unless($lead->is_read)
                                            <span class="badge bg-gold/20 text-gold">جديد</span>
                                        @endunless
                                    </div>
                                    <a href="mailto:{{ $lead->user?->email }}" class="text-xs text-white/45 hover:text-gold truncate block">
                                        ✉ {{ $lead->user?->email }}
                                    </a>
                                    <div class="text-[11px] text-white/35 mt-1">⏱ {{ $lead->created_at->diffForHumans() }}</div>
                                </div>
                            </div>

                            {{-- Score --}}
                            <div class="shrink-0 text-center">
                                <div class="w-16 h-16 rounded-full grid place-items-center border-2 {{ $score >= 75 ? 'border-emerald-400/40' : 'border-gold/40' }}">
                                    <span class="font-display font-extrabold text-lg {{ $scoreColor }}">{{ $score }}%</span>
                                </div>
                                <div class="text-[10px] text-white/40 mt-1">نسبة التطابق</div>
                            </div>
                        </div>

                        {{-- Matched house --}}
                        <div class="mt-5 pt-5 border-t border-white/10 grid sm:grid-cols-2 gap-4">
                            <div>
                                <div class="text-[11px] text-white/40 mb-1">العقار المطابق</div>
                                <a href="{{ route('listings.show', $lead->house) }}" target="_blank"
                                   class="text-sm font-bold text-gold hover:underline flex items-center gap-1">
                                    🏠 {{ $lead->house?->title ?: 'عقار' }}
                                </a>
                                <div class="text-xs text-white/45 mt-1">📍 {{ $lead->house?->district?->name }}</div>
                            </div>

                            {{-- Buyer wants --}}
                            <div>
                                <div class="text-[11px] text-white/40 mb-1">ما يبحث عنه المشتري</div>
                                @if($pref)
                                    <div class="flex flex-wrap gap-2 text-[11px]">
                                        @if($pref->label)
                                            <span class="badge bg-white/5 text-white/70">{{ $pref->label }}</span>
                                        @endif
                                        <span class="badge bg-white/5 text-white/70">
                                            {{ $pref->type === 'rent' ? 'إيجار' : 'بيع' }}
                                        </span>
                                        @if($pref->max_price)
                                            <span class="badge bg-white/5 text-white/70">حتى ${{ number_format($pref->max_price) }}</span>
                                        @endif
                                        @if($pref->min_rooms)
                                            <span class="badge bg-white/5 text-white/70">{{ $pref->min_rooms }}+ غرف</span>
                                        @endif
                                        @if($pref->district?->name || $pref->city?->name)
                                            <span class="badge bg-white/5 text-white/70">
                                                📍 {{ $pref->district?->name ?? $pref->city?->name }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <p class="text-xs text-white/40">تفضيلات غير محددة</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="glass rounded-3xl p-12 text-center" data-aos="fade-up">
                        <div class="text-5xl mb-4 opacity-40">🎯</div>
                        <h3 class="font-display font-bold text-lg mb-2">لا توجد طلبات مطابقة بعد</h3>
                        <p class="text-white/45 text-sm mb-6 max-w-md mx-auto">
                            كلما نشرت عقارات أكثر، زادت فرص تطابقها مع تفضيلات المشترين. انشر عقاراً الآن لتبدأ في استقبال العملاء.
                        </p>
                        <a href="{{ route('dashboard.listings.create') }}" class="btn-gold rounded-2xl px-6 py-3 text-sm inline-block shine">
                            ＋ نشر عقار جديد
                        </a>
                    </div>
                @endforelse
            </div>

            @if($leads->hasPages())
                <div class="glass rounded-3xl p-5">
                    {{ $leads->links() }}
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
