@extends('layouts.app')
@section('title', 'الاشتراك — لوحة المكتب')

@section('content')
<div class="max-w-7xl mx-auto px-4 pt-28 pb-16">
    <div class="grid lg:grid-cols-4 gap-6">
        @include('partials.dashboard-sidebar')

        <div class="lg:col-span-3 space-y-6">

            {{-- Header --}}
            <div class="glass rounded-3xl p-7" data-aos="fade-up">
                <h1 class="font-display font-black text-2xl md:text-3xl">الاشتراك 💎</h1>
                <div class="gold-line w-24 mt-3"></div>
            </div>

            {{-- Current subscription status --}}
            <div class="glass rounded-3xl p-7" data-aos="fade-up">
                @if($subscription)
                    @php $daysLeft = max(0, (int) $subscription->days_left); $pct = min(100, max(0, round(($daysLeft / 30) * 100))); @endphp
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
                        <div>
                            <div class="text-sm text-white/50">اشتراكك الحالي</div>
                            <div class="font-display text-2xl font-extrabold gold-text">
                                {{ $currentPlan['name'] ?? $subscription->plan }}
                            </div>
                        </div>
                        <span class="badge bg-emerald-500/20 text-emerald-300">✓ فعّال</span>
                    </div>

                    <div class="grid sm:grid-cols-3 gap-4 mb-5">
                        <div class="glass-light rounded-2xl p-4">
                            <div class="text-xs text-white/45 mb-1">الأيام المتبقية</div>
                            <div class="font-display text-xl font-extrabold">{{ $daysLeft }} يوم</div>
                        </div>
                        <div class="glass-light rounded-2xl p-4">
                            <div class="text-xs text-white/45 mb-1">تاريخ الانتهاء</div>
                            <div class="font-display text-xl font-extrabold">{{ $subscription->end_date->translatedFormat('d M Y') }}</div>
                        </div>
                        <div class="glass-light rounded-2xl p-4">
                            <div class="text-xs text-white/45 mb-1">العقارات المتبقية</div>
                            <div class="font-display text-xl font-extrabold text-gold">
                                {{ is_null($remaining) ? 'غير محدود' : number_format($remaining) . ' عقار' }}
                            </div>
                        </div>
                    </div>

                    <div class="w-full h-2.5 rounded-full bg-white/5 overflow-hidden">
                        <div class="h-full rounded-full" style="width:{{ $pct }}%;background:linear-gradient(90deg,#f0d27a,#d4af37);"></div>
                    </div>
                @else
                    <div class="text-center py-6">
                        <div class="text-4xl mb-3 opacity-50">💤</div>
                        <h3 class="font-display font-bold text-lg mb-2">لا يوجد اشتراك فعّال</h3>
                        <p class="text-white/45 text-sm">اختر إحدى الباقات أدناه لتفعيل حسابك والبدء بنشر العقارات.</p>
                    </div>
                @endif
            </div>

            {{-- Pricing grid --}}
            <div class="grid md:grid-cols-3 gap-5">
                @foreach($plans as $key => $plan)
                    @php
                        $isPopular = !empty($plan['popular']);
                        $isCurrent = $subscription && $subscription->plan === $plan['key'];
                        $color = $plan['color'] ?? '#d4af37';
                    @endphp
                    <div class="relative glass rounded-3xl p-6 card-hover flex flex-col {{ $isPopular ? 'ring-1' : '' }}"
                         style="{{ $isPopular ? '--tw-ring-color:' . $color . '; box-shadow:0 0 0 1px ' . $color . '55;' : '' }}"
                         data-aos="fade-up" data-aos-delay="{{ $loop->index * 90 }}">

                        @if($isPopular)
                            <span class="absolute -top-3 right-6 badge shine"
                                  style="background:{{ $color }};color:#0a0f0d;">⭐ الأكثر طلباً</span>
                        @endif

                        {{-- Name --}}
                        <div class="mb-4">
                            <h3 class="font-display font-extrabold text-xl" style="color:{{ $color }};">{{ $plan['name'] }}</h3>
                            <div class="text-xs text-white/35 tracking-widest uppercase">{{ $plan['name_en'] }}</div>
                        </div>

                        {{-- Price --}}
                        <div class="mb-3">
                            <span class="font-display text-4xl font-black">${{ number_format($plan['price']) }}</span>
                            <span class="text-white/45 text-sm">/شهر</span>
                        </div>

                        @if(!empty($plan['tagline']))
                            <p class="text-white/55 text-sm mb-5 leading-6">{{ $plan['tagline'] }}</p>
                        @endif

                        {{-- Listing limit --}}
                        <div class="glass-light rounded-2xl px-4 py-3 mb-5 flex items-center justify-between">
                            <span class="text-xs text-white/50">حد العقارات</span>
                            <span class="font-display font-extrabold" style="color:{{ $color }};">
                                {{ is_null($plan['listing_limit']) ? 'غير محدود' : number_format($plan['listing_limit']) . ' عقار' }}
                            </span>
                        </div>

                        {{-- Features --}}
                        <ul class="space-y-2.5 mb-6 flex-1">
                            @foreach($plan['features'] as $feature)
                                <li class="flex items-start gap-2 text-sm text-white/70">
                                    <span class="text-gold mt-0.5">✓</span>
                                    <span>{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>

                        {{-- CTA --}}
                        @if($isCurrent)
                            <span class="rounded-2xl px-6 py-3 text-sm text-center block border border-emerald-400/40 text-emerald-300 bg-emerald-500/10 cursor-default">
                                ✓ باقتك الحالية
                            </span>
                        @else
                            <form method="POST" action="{{ route('dashboard.subscription.subscribe', $plan['key']) }}">
                                @csrf
                                <button type="submit" class="btn-gold rounded-2xl px-6 py-3 text-sm w-full shine">
                                    اشترك الآن
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- History --}}
            <div class="glass rounded-3xl p-5 md:p-7" data-aos="fade-up">
                <h3 class="font-display font-bold text-lg mb-1 flex items-center gap-2">🧾 سجلّ الاشتراكات</h3>
                <div class="gold-line w-24 mb-5"></div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-right min-w-[560px]">
                        <thead>
                            <tr class="text-white/40 text-xs border-b border-white/10">
                                <th class="py-3 px-3 font-medium">الباقة</th>
                                <th class="py-3 px-3 font-medium">السعر</th>
                                <th class="py-3 px-3 font-medium">البداية</th>
                                <th class="py-3 px-3 font-medium">النهاية</th>
                                <th class="py-3 px-3 font-medium">الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $row)
                                <tr class="border-b border-white/5 hover:bg-white/5 transition">
                                    <td class="py-3 px-3 font-bold">{{ $plans[$row->plan]['name'] ?? $row->plan }}</td>
                                    <td class="py-3 px-3 text-gold">${{ number_format($row->price) }}</td>
                                    <td class="py-3 px-3 text-white/65">{{ $row->start_date->format('Y-m-d') }}</td>
                                    <td class="py-3 px-3 text-white/65">{{ $row->end_date->format('Y-m-d') }}</td>
                                    <td class="py-3 px-3">
                                        @if($row->is_active)
                                            <span class="badge bg-emerald-500/20 text-emerald-300">نشط</span>
                                        @else
                                            <span class="badge bg-white/5 text-white/45">منتهٍ</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-10 text-center text-white/40">
                                        لا يوجد سجلّ اشتراكات بعد.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
