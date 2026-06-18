@extends('layouts.app')
@section('title', 'لوحة تحليل السوق — عقّار سوريا')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@endpush

@section('content')
<section class="pt-32 pb-8 max-w-7xl mx-auto px-4">
    <div data-aos="fade-up">
        <span class="text-gold text-sm font-bold tracking-widest">ذكاء السوق العقاري</span>
        <h1 class="font-display font-black text-3xl md:text-5xl mt-2">لوحة تحليل السوق</h1>
        <p class="text-white/50 mt-3 max-w-2xl">أرقام حقيقية مبنية على صفقات المنصة. اعرف متوسط سعر المتر في كل منطقة، واتجاه الأسعار، وأين يتركّز الطلب — مرجعك قبل أي قرار.</p>
    </div>

    {{-- KPI strip --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-10">
        @php $kpis = [
            ['عقار في السوق', $overview['total_listings'], '🏠', '1'],
            ['متوسط سعر المتر', $overview['avg_price_meter'], '💰', '1'],
            ['صفقة منجزة', $overview['closed_deals'], '🤝', '0'],
            ['طلب نشط للمشترين', $overview['open_demand'], '🎯', '0'],
        ]; @endphp
        @foreach($kpis as $i => $k)
            <div class="glass rounded-3xl p-6 card-hover" data-aos="fade-up" data-aos-delay="{{ $i*80 }}">
                <div class="text-2xl mb-3">{{ $k[2] }}</div>
                <div class="font-display text-3xl font-black gold-text">
                    @if($k[3]==='1')<span class="text-xl align-top">$</span>@endif<span data-count="{{ $k[1] }}" data-fmt="1">0</span>
                </div>
                <div class="text-white/40 text-xs mt-2">{{ $k[0] }}</div>
            </div>
        @endforeach
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 space-y-6">
    {{-- Trend + type split --}}
    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 glass rounded-3xl p-7" data-aos="fade-up">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-display font-bold text-lg">اتجاه سعر المتر · آخر 6 أشهر</h2>
                <span class="badge bg-emerald-500/20 text-emerald-300">مباشر</span>
            </div>
            <canvas id="trendChart" height="120"></canvas>
        </div>
        <div class="glass rounded-3xl p-7" data-aos="fade-up" data-aos-delay="100">
            <h2 class="font-display font-bold text-lg mb-6">توزّع العرض</h2>
            <canvas id="typeChart" height="200"></canvas>
            <div class="flex justify-center gap-6 mt-6 text-sm">
                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-gold"></span>بيع <b>{{ $type_split['sale'] }}</b></div>
                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full" style="background:#38bdf8"></span>إيجار <b>{{ $type_split['rent'] }}</b></div>
            </div>
        </div>
    </div>

    {{-- City prices + supply/demand --}}
    <div class="grid lg:grid-cols-2 gap-6">
        <div class="glass rounded-3xl p-7" data-aos="fade-up">
            <h2 class="font-display font-bold text-lg mb-6">متوسط سعر المتر حسب المدينة</h2>
            <canvas id="cityChart" height="160"></canvas>
        </div>
        <div class="glass rounded-3xl p-7" data-aos="fade-up" data-aos-delay="100">
            <h2 class="font-display font-bold text-lg mb-2">العرض مقابل الطلب</h2>
            <p class="text-white/40 text-xs mb-6">عقارات متاحة مقابل طلبات مشترين نشطة لكل مدينة</p>
            <canvas id="sdChart" height="160"></canvas>
        </div>
    </div>

    {{-- District table --}}
    <div class="glass rounded-3xl p-7" data-aos="fade-up">
        <h2 class="font-display font-bold text-lg mb-6">مرجع أسعار المتر حسب المنطقة</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-white/40 text-xs border-b border-white/10">
                        <th class="text-right py-3 font-medium">#</th>
                        <th class="text-right py-3 font-medium">المنطقة</th>
                        <th class="text-right py-3 font-medium">المدينة</th>
                        <th class="text-center py-3 font-medium">عقارات</th>
                        <th class="text-center py-3 font-medium">إيجار / بيع</th>
                        <th class="text-left py-3 font-medium">متوسط $/م²</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($by_district as $i => $row)
                        <tr class="border-b border-white/5 hover:bg-white/5 transition">
                            <td class="py-4 text-white/30">{{ $i+1 }}</td>
                            <td class="py-4 font-bold">{{ $row['district'] }}</td>
                            <td class="py-4 text-white/50">{{ $row['city'] }}</td>
                            <td class="py-4 text-center">{{ $row['listings'] }}</td>
                            <td class="py-4 text-center text-xs"><span class="text-sky-300">{{ $row['rent_count'] }}</span> / <span class="text-gold">{{ $row['sale_count'] }}</span></td>
                            <td class="py-4 text-left font-display font-extrabold gold-text">${{ number_format($row['price_per_meter']) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-10 text-center text-white/40">لا توجد بيانات كافية بعد.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Hottest --}}
    @if($hottest->count())
    <div data-aos="fade-up">
        <h2 class="font-display font-black text-2xl mb-6 mt-6">🔥 الأكثر طلباً الآن</h2>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-7 pb-10">
            @foreach($hottest as $house)
                @include('partials.property-card', ['house' => $house])
            @endforeach
        </div>
    </div>
    @endif
</section>

@push('scripts')
<script>
    Chart.defaults.color = 'rgba(255,255,255,.55)';
    Chart.defaults.font.family = 'Tajawal';
    Chart.defaults.borderColor = 'rgba(255,255,255,.06)';

    const gradient = (ctx, c1, c2) => { const g = ctx.createLinearGradient(0,0,0,300); g.addColorStop(0,c1); g.addColorStop(1,c2); return g; };

    // Trend
    const tctx = document.getElementById('trendChart').getContext('2d');
    new Chart(tctx, {
        type:'line',
        data:{ labels:@json($trend['labels']), datasets:[{
            label:'سعر المتر ($)', data:@json($trend['price_per_meter']),
            borderColor:'#d4af37', borderWidth:3, tension:.4, fill:true,
            backgroundColor:gradient(tctx,'rgba(212,175,55,.35)','rgba(212,175,55,0)'),
            pointBackgroundColor:'#d4af37', pointRadius:4, pointHoverRadius:7
        }]},
        options:{ plugins:{legend:{display:false}}, scales:{ y:{ticks:{callback:v=>'$'+v}} }, maintainAspectRatio:true }
    });

    // Type doughnut
    new Chart(document.getElementById('typeChart'), {
        type:'doughnut',
        data:{ labels:['بيع','إيجار'], datasets:[{ data:[{{ $type_split['sale'] }},{{ $type_split['rent'] }}],
            backgroundColor:['#d4af37','#38bdf8'], borderWidth:0, hoverOffset:10 }]},
        options:{ cutout:'68%', plugins:{legend:{display:false}} }
    });

    // City bar
    new Chart(document.getElementById('cityChart'), {
        type:'bar',
        data:{ labels:@json(collect($by_city)->pluck('city')), datasets:[{
            label:'$/م²', data:@json(collect($by_city)->pluck('price_per_meter')),
            backgroundColor:'rgba(212,175,55,.75)', borderRadius:8, barThickness:30 }]},
        options:{ plugins:{legend:{display:false}}, scales:{ y:{ticks:{callback:v=>'$'+v}} } }
    });

    // Supply/Demand
    new Chart(document.getElementById('sdChart'), {
        type:'bar',
        data:{ labels:@json($supply_demand['labels']), datasets:[
            { label:'العرض', data:@json($supply_demand['supply']), backgroundColor:'rgba(16,185,129,.8)', borderRadius:6 },
            { label:'الطلب', data:@json($supply_demand['demand']), backgroundColor:'rgba(212,175,55,.8)', borderRadius:6 },
        ]},
        options:{ plugins:{legend:{position:'bottom'}}, scales:{ x:{stacked:false}, y:{beginAtZero:true} } }
    });
</script>
@endpush
@endsection
