@extends('layouts.app')
@section('title', 'العقود — لوحة المكتب')

@section('content')
@php
    $cycleLabels = ['once' => 'دفعة واحدة', 'monthly' => 'شهري', 'quarterly' => 'ربع سنوي', 'yearly' => 'سنوي'];
    $statusMeta = [
        'active'    => ['label' => 'نشط',    'class' => 'bg-emerald-500/20 text-emerald-300'],
        'completed' => ['label' => 'مكتمل',  'class' => 'bg-sky-500/20 text-sky-300'],
        'cancelled' => ['label' => 'ملغى',   'class' => 'bg-red-500/20 text-red-300'],
    ];
@endphp
<div class="max-w-7xl mx-auto px-4 pt-28 pb-16">
    <div class="grid lg:grid-cols-4 gap-6">
        @include('partials.dashboard-sidebar')

        <div class="lg:col-span-3 space-y-6">

            {{-- Header --}}
            <div class="glass rounded-3xl p-7 flex flex-wrap items-center justify-between gap-4" data-aos="fade-up">
                <div>
                    <h1 class="font-display font-black text-2xl md:text-3xl">العقود 📄</h1>
                    <div class="gold-line w-24 mt-3"></div>
                </div>
                <a href="{{ route('dashboard.contracts.create') }}" class="btn-gold rounded-2xl px-6 py-3 text-sm shine">
                    ＋ عقد جديد
                </a>
            </div>

            {{-- Due alerts strip --}}
            @if($dueAlerts->isNotEmpty())
                <div class="glass rounded-3xl p-6 border-r-4 border-gold" data-aos="fade-up">
                    <h3 class="font-display font-bold text-base mb-4 flex items-center gap-2">⏰ عقود مستحقة قريباً</h3>
                    <div class="grid sm:grid-cols-2 gap-3">
                        @foreach($dueAlerts as $alert)
                            <div class="glass-light rounded-2xl px-4 py-3 flex items-center justify-between gap-3
                                        border-r-4 {{ $alert->is_overdue ? 'border-red-400' : 'border-gold' }}">
                                <div class="min-w-0">
                                    <div class="font-bold text-sm truncate">{{ $alert->party_name }}</div>
                                    <div class="text-[11px] text-white/45">#{{ $alert->reference }} · {{ $alert->house?->district?->name }}</div>
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
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Contracts --}}
            <div class="glass rounded-3xl p-5 md:p-7" data-aos="fade-up">
                @forelse($contracts as $contract)
                    @php
                        $typeColor = $contract->type === 'rent' ? 'bg-sky-500/20 text-sky-300' : 'bg-gold/20 text-gold';
                        $typeLabel = $contract->type === 'rent' ? 'إيجار' : 'بيع';
                        $st = $statusMeta[$contract->status] ?? ['label' => $contract->status, 'class' => 'bg-white/5 text-white/60'];
                    @endphp
                    <div class="flex flex-col lg:flex-row lg:items-center gap-4 glass-light rounded-2xl p-4 mb-3 card-hover">

                        {{-- Reference + house --}}
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <span class="font-display font-bold text-sm text-gold">#{{ $contract->reference }}</span>
                                <span class="badge {{ $typeColor }}">{{ $typeLabel }}</span>
                                <span class="badge {{ $st['class'] }}">{{ $st['label'] }}</span>
                            </div>
                            <div class="text-sm font-bold truncate">{{ $contract->party_name }}</div>
                            <div class="text-xs text-white/45 truncate">
                                🏠 {{ $contract->house?->title ?: 'عقار' }}<span class="text-white/20"> · </span>📍 {{ $contract->house?->district?->name }}
                            </div>
                        </div>

                        {{-- Amount + cycle --}}
                        <div class="lg:text-center shrink-0">
                            <div class="font-display font-extrabold text-gold text-lg">${{ number_format($contract->amount) }}</div>
                            <div class="text-[11px] text-white/45">{{ $cycleLabels[$contract->payment_cycle] ?? $contract->payment_cycle }}</div>
                        </div>

                        {{-- Due date --}}
                        <div class="lg:text-center shrink-0">
                            <div class="text-[11px] text-white/40">الاستحقاق</div>
                            @if($contract->due_date)
                                <div class="text-sm font-bold {{ $contract->is_overdue ? 'text-red-300' : 'text-white/80' }}">
                                    {{ $contract->due_date->format('Y-m-d') }}
                                </div>
                                @if($contract->is_overdue)
                                    <div class="text-[11px] text-red-300">متأخّر</div>
                                @endif
                            @else
                                <div class="text-sm text-white/40">—</div>
                            @endif
                        </div>

                        {{-- Dates + PDF --}}
                        <div class="shrink-0 flex items-center gap-3 justify-between lg:justify-end">
                            <div class="text-[11px] text-white/45 leading-5">
                                <div>من {{ $contract->start_date->format('Y-m-d') }}</div>
                                <div>إلى {{ $contract->end_date ? $contract->end_date->format('Y-m-d') : '—' }}</div>
                            </div>
                            <a href="{{ route('dashboard.contracts.pdf', $contract) }}" target="_blank"
                               class="btn-ghost rounded-xl px-3 py-2 text-xs whitespace-nowrap">⬇ تحميل PDF</a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16">
                        <div class="text-5xl mb-4 opacity-40">📄</div>
                        <h3 class="font-display font-bold text-lg mb-2">لا توجد عقود بعد</h3>
                        <p class="text-white/45 text-sm mb-6">أنشئ أول عقد لتوليد ملف PDF احترافي وتتبّع الاستحقاقات تلقائياً.</p>
                        <a href="{{ route('dashboard.contracts.create') }}" class="btn-gold rounded-2xl px-6 py-3 text-sm inline-block shine">
                            ＋ إنشاء أول عقد
                        </a>
                    </div>
                @endforelse

                @if($contracts->hasPages())
                    <div class="mt-6 pt-5 border-t border-white/10">
                        {{ $contracts->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
