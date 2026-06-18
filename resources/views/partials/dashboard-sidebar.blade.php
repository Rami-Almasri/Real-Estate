@php
    $office = auth()->user()->office;
    $navItems = [
        ['route' => 'dashboard.index',        'label' => 'نظرة عامة',     'icon' => '📊'],
        ['route' => 'dashboard.listings',     'label' => 'عقاراتي',       'icon' => '🏠'],
        ['route' => 'dashboard.leads',        'label' => 'طلبات المشترين', 'icon' => '🎯'],
        ['route' => 'dashboard.contracts',    'label' => 'العقود',        'icon' => '📄'],
        ['route' => 'dashboard.subscription', 'label' => 'الاشتراك',      'icon' => '💎'],
    ];
@endphp
<aside class="lg:col-span-1" data-aos="fade-up">
    <div class="glass rounded-3xl p-5 lg:sticky lg:top-28 space-y-5">

        {{-- Office identity --}}
        <div class="text-center pb-5 border-b border-white/10">
            <div class="w-16 h-16 mx-auto rounded-2xl grid place-items-center shine mb-3"
                 style="background:linear-gradient(135deg,#0f3d2e,#081b15);border:1px solid rgba(212,175,55,.4)">
                <span class="text-gold text-2xl font-black font-display">🏢</span>
            </div>
            <h2 class="font-display font-extrabold text-base truncate">{{ $office?->name ?? 'مكتبي العقاري' }}</h2>
            <span class="badge bg-emerald-500/20 text-emerald-300 inline-flex items-center gap-1 mt-2">✓ موثّق</span>
        </div>

        {{-- Navigation --}}
        <nav class="space-y-1.5">
            @foreach($navItems as $item)
                @php $active = request()->routeIs($item['route']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition
                          {{ $active ? 'text-gold bg-gold/10 border border-gold/20' : 'text-white/70 hover:text-white hover:bg-white/5 border border-transparent' }}">
                    <span class="text-lg">{{ $item['icon'] }}</span>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        {{-- Quick action --}}
        <a href="{{ route('dashboard.listings.create') }}"
           class="btn-gold rounded-2xl px-5 py-3 text-sm text-center block shine">
            ＋ نشر عقار جديد
        </a>
    </div>
</aside>
