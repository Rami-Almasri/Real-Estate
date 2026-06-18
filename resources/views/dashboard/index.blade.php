@extends('layouts.app')
@section('title', 'لوحة المكتب — نظرة عامة')

@section('content')
<div class="max-w-7xl mx-auto px-4 pt-28 pb-16">
    <div class="grid lg:grid-cols-4 gap-6">
        @include('partials.dashboard-sidebar')

        <div class="lg:col-span-3 space-y-6">

            {{-- Hero greeting --}}
            <div class="glass rounded-3xl p-7 flex flex-wrap items-center justify-between gap-4" data-aos="fade-up">
                <div>
                    <div class="text-xs text-gold mb-2 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-gold animate-pulse"></span>
                        لوحة تحكّم المكتب
                    </div>
                    <h1 class="font-display font-black text-2xl md:text-3xl">
                        أهلاً، <span class="gold-text">{{ $office->name }}</span>
                    </h1>
                    <p class="text-white/50 text-sm mt-2 flex items-center gap-1">
                        📍 {{ $office->district?->name }}<span class="text-white/20">،</span>
                        {{ $office->district?->city?->name }}
                        @if($office->address)<span class="text-white/30">— {{ $office->address }}</span>@endif
                    </p>
                </div>
                <a href="{{ route('dashboard.listings.create') }}" class="btn-gold rounded-2xl px-6 py-3 text-sm shine">
                    🚀 نشر عقار جديد
                </a>
            </div>

            {{-- KPI grid --}}
            <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4">
                @php
                    $kpis = [
                        ['label' => 'إجمالي العقارات', 'key' => 'listings',  'icon' => '🏠', 'fmt' => false],
                        ['label' => 'متاح الآن',       'key' => 'available', 'icon' => '✅', 'fmt' => false],
                        ['label' => 'مشاهدات',         'key' => 'views',     'icon' => '👁', 'fmt' => true],
                        ['label' => 'طلبات مطابقة',     'key' => 'leads',     'icon' => '🎯', 'fmt' => false],
                    ];
                @endphp
                @foreach($kpis as $i => $kpi)
                    <div class="glass rounded-2xl p-5 card-hover" data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-2xl">{{ $kpi['icon'] }}</span>
                            <span class="badge bg-white/5 text-white/40">إحصائية</span>
                        </div>
                        <div class="font-display text-3xl font-extrabold gold-text"
                             data-count="{{ $stats[$kpi['key']] }}" @if($kpi['fmt']) data-fmt="1" @endif>0</div>
                        <div class="text-xs text-white/45 mt-1">{{ $kpi['label'] }}</div>
                    </div>
                @endforeach
            </div>

            {{-- Secondary stats --}}
            <div class="grid sm:grid-cols-3 gap-4">
                <div class="glass-light rounded-2xl p-5" data-aos="fade-up">
                    <div class="text-xs text-white/45 mb-1">مؤجّر / مُباع</div>
                    <div class="font-display text-2xl font-extrabold">{{ number_format($stats['occupied']) }}</div>
                </div>
                <div class="glass-light rounded-2xl p-5" data-aos="fade-up" data-aos-delay="80">
                    <div class="text-xs text-white/45 mb-1">العقود</div>
                    <div class="font-display text-2xl font-extrabold">{{ number_format($stats['contracts']) }}</div>
                </div>
                <div class="glass-light rounded-2xl p-5" data-aos="fade-up" data-aos-delay="160">
                    <div class="text-xs text-white/45 mb-1">قيمة المحفظة</div>
                    <div class="font-display text-2xl font-extrabold gold-text">${{ number_format($stats['portfolio']) }}</div>
                </div>
            </div>

            <div class="grid lg:grid-cols-2 gap-6">

                {{-- Subscription status --}}
                <div class="glass rounded-3xl p-7" data-aos="fade-up">
                    <h3 class="font-display font-bold text-lg mb-1 flex items-center gap-2">💎 الاشتراك</h3>
                    <div class="gold-line w-24 mb-5"></div>

                    @if($subscription)
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <div class="text-sm text-white/50">باقتك الحالية</div>
                                <div class="font-display text-xl font-extrabold gold-text">
                                    {{ $plan['name'] ?? $subscription->plan }}
                                </div>
                            </div>
                            <span class="badge bg-emerald-500/20 text-emerald-300">فعّال</span>
                        </div>

                        @php
                            $daysLeft = max(0, (int) $subscription->days_left);
                            $pct = min(100, max(0, round(($daysLeft / 30) * 100)));
                        @endphp
                        <div class="mb-2 flex items-center justify-between text-xs text-white/55">
                            <span>متبقّي {{ $daysLeft }} يوم</span>
                            <span>ينتهي {{ $subscription->end_date->translatedFormat('d M Y') }}</span>
                        </div>
                        <div class="w-full h-2.5 rounded-full bg-white/5 overflow-hidden mb-5">
                            <div class="h-full rounded-full" style="width:{{ $pct }}%;background:linear-gradient(90deg,#f0d27a,#d4af37);"></div>
                        </div>

                        <div class="flex items-center justify-between glass-light rounded-2xl px-4 py-3">
                            <span class="text-sm text-white/55">العقارات المتبقية</span>
                            <span class="font-display font-extrabold text-gold">
                                {{ is_null($remaining) ? 'غير محدود' : number_format($remaining) . ' عقار' }}
                            </span>
                        </div>

                        <a href="{{ route('dashboard.subscription') }}" class="btn-ghost rounded-2xl px-6 py-3 text-sm block text-center mt-5">
                            إدارة الاشتراك
                        </a>
                    @else
                        <p class="text-white/55 text-sm mb-5 leading-7">
                            لم تقم بتفعيل اشتراك بعد. فعّل باقتك لتنشر عقاراتك وتصل إلى آلاف المشترين المطابقين فوراً.
                        </p>
                        <a href="{{ route('dashboard.subscription') }}" class="btn-gold rounded-2xl px-6 py-3 text-sm block text-center shine">
                            ✨ فعّل اشتراكك الآن
                        </a>
                    @endif
                </div>

                {{-- Due alerts --}}
                <div class="glass rounded-3xl p-7" data-aos="fade-up" data-aos-delay="120">
                    <h3 class="font-display font-bold text-lg mb-1 flex items-center gap-2">⏰ تنبيهات استحقاق العقود</h3>
                    <div class="gold-line w-24 mb-5"></div>

                    <div class="space-y-3">
                        @forelse($dueAlerts as $alert)
                            <div class="glass-light rounded-2xl px-4 py-3 border-r-4 {{ $alert->is_overdue ? 'border-red-400' : 'border-gold' }}">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="font-bold text-sm truncate">{{ $alert->party_name }}</div>
                                        <div class="text-xs text-white/45 truncate">
                                            #{{ $alert->reference }} · {{ $alert->house?->district?->name }}
                                        </div>
                                    </div>
                                    <div class="text-left shrink-0">
                                        <div class="font-display font-extrabold text-gold">${{ number_format($alert->amount) }}</div>
                                        <div class="text-[11px] {{ $alert->is_overdue ? 'text-red-300' : 'text-white/45' }}">
                                            @if($alert->is_overdue)
                                                متأخّر {{ abs($alert->days_until_due) }} يوم
                                            @else
                                                خلال {{ $alert->days_until_due }} يوم
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between mt-2 pt-2 border-t border-white/5">
                                    <span class="text-[11px] text-white/40">استحقاق {{ $alert->due_date->format('Y-m-d') }}</span>
                                    <a href="{{ route('dashboard.contracts.pdf', $alert) }}" target="_blank"
                                       class="text-[11px] text-gold hover:underline">⬇ تحميل PDF</a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10">
                                <div class="text-4xl mb-3 opacity-40">✅</div>
                                <p class="text-white/45 text-sm">لا توجد عقود مستحقة قريباً</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Top listings --}}
            <div class="glass rounded-3xl p-7" data-aos="fade-up">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="font-display font-bold text-lg flex items-center gap-2">🔥 أكثر عقاراتك مشاهدة</h3>
                    <a href="{{ route('dashboard.listings') }}" class="text-xs text-gold hover:underline">عرض الكل ←</a>
                </div>
                <div class="gold-line w-24 mb-5"></div>

                <div class="space-y-3">
                    @forelse($topListings as $h)
                        @php
                            $typeColor = $h->type === 'rent' ? 'bg-sky-500/20 text-sky-300' : 'bg-gold/20 text-gold';
                            $typeLabel = $h->type === 'rent' ? 'إيجار' : 'بيع';
                        @endphp
                        <a href="{{ route('listings.show', $h) }}"
                           class="flex items-center gap-4 glass-light rounded-2xl p-3 card-hover">
                            <img src="{{ $h->cover }}" alt="{{ $h->title }}" loading="lazy"
                                 class="w-20 h-16 rounded-xl object-cover shrink-0">
                            <div class="min-w-0 flex-1">
                                <div class="font-bold text-sm truncate">{{ $h->title ?: 'عقار' }}</div>
                                <div class="text-xs text-white/45 flex items-center gap-2 mt-1">
                                    <span class="badge {{ $typeColor }}">{{ $typeLabel }}</span>
                                    📍 {{ $h->district?->name }}
                                </div>
                            </div>
                            <div class="text-left shrink-0">
                                <div class="font-display font-extrabold text-gold">${{ number_format($h->price) }}</div>
                                <div class="text-[11px] text-white/45">👁 {{ number_format($h->view_count) }} مشاهدة</div>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-10">
                            <div class="text-4xl mb-3 opacity-40">🏠</div>
                            <p class="text-white/45 text-sm mb-4">لم تنشر أي عقار بعد</p>
                            <a href="{{ route('dashboard.listings.create') }}" class="btn-gold rounded-2xl px-6 py-3 text-sm inline-block">
                                نشر أول عقار
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
