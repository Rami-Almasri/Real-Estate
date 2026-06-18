@extends('layouts.app')
@section('title', 'باقات المكاتب العقارية — عقّار سوريا')

@section('content')
<section class="pt-32 pb-12 max-w-7xl mx-auto px-4 text-center">
    <span class="text-gold text-sm font-bold tracking-widest" data-aos="fade-down">للمكاتب العقارية</span>
    <h1 class="font-display font-black text-4xl md:text-6xl mt-3" data-aos="fade-up">حوّل مكتبك إلى <span class="gold-text">قوة رقمية</span></h1>
    <p class="text-white/55 mt-5 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">
        اشتراك واحد يمنحك المطابقة الذكية، تحليل أسعار السوق، وأتمتة العقود. ابدأ اليوم واترك دفاتر الورق للأبد.
    </p>
</section>

<section class="max-w-7xl mx-auto px-4">
    <div class="grid lg:grid-cols-3 gap-7 items-stretch">
        @foreach($plans as $plan)
            @php $popular = $plan['popular'] ?? false; @endphp
            <div class="relative glass rounded-[2rem] p-8 flex flex-col {{ $popular ? 'lg:-mt-4 lg:mb-4' : '' }}"
                 style="{{ $popular ? 'border-color:rgba(212,175,55,.5);box-shadow:0 20px 60px -20px rgba(212,175,55,.4)' : '' }}"
                 data-aos="fade-up" data-aos-delay="{{ $loop->index*100 }}">
                @if($popular)
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 btn-gold rounded-full px-5 py-1.5 text-xs font-bold shine">الأكثر طلباً</div>
                @endif

                <div class="mb-6">
                    <div class="w-12 h-12 rounded-2xl grid place-items-center text-2xl mb-4"
                         style="background:{{ $plan['color'] }}22;border:1px solid {{ $plan['color'] }}55">
                        {{ ['basic'=>'🏢','pro'=>'🚀','elite'=>'👑'][$plan['key']] ?? '💎' }}
                    </div>
                    <h3 class="font-display font-black text-2xl">{{ $plan['name'] }}</h3>
                    <p class="text-white/40 text-sm mt-1">{{ $plan['tagline'] }}</p>
                </div>

                <div class="mb-6">
                    <span class="font-display text-5xl font-black gold-text">${{ $plan['price'] }}</span>
                    <span class="text-white/40 text-sm">/ شهرياً</span>
                </div>

                <div class="text-sm text-white/70 mb-6 pb-6 border-b border-white/5">
                    <span class="text-gold font-bold">{{ $plan['listing_limit'] ? $plan['listing_limit'].' عقار' : 'عقارات غير محدودة' }}</span>
                </div>

                <ul class="space-y-3 text-sm text-white/65 flex-1 mb-8">
                    @foreach($plan['features'] as $f)
                        <li class="flex items-start gap-3"><span class="text-gold mt-0.5">✓</span><span>{{ $f }}</span></li>
                    @endforeach
                </ul>

                @auth
                    @if(auth()->user()->isOffice())
                        <form method="POST" action="{{ route('dashboard.subscription.subscribe', $plan['key']) }}">@csrf
                            <button class="{{ $popular ? 'btn-gold' : 'btn-ghost' }} rounded-2xl w-full py-4 text-sm">اشترك الآن</button>
                        </form>
                    @else
                        <a href="{{ route('register') }}" class="{{ $popular ? 'btn-gold' : 'btn-ghost' }} rounded-2xl w-full py-4 text-sm block text-center">سجّل مكتبك</a>
                    @endif
                @else
                    <a href="{{ route('register') }}" class="{{ $popular ? 'btn-gold' : 'btn-ghost' }} rounded-2xl w-full py-4 text-sm block text-center">ابدأ الآن</a>
                @endauth
            </div>
        @endforeach
    </div>
</section>

{{-- Value props --}}
<section class="max-w-7xl mx-auto px-4 mt-24">
    <div class="text-center mb-14" data-aos="fade-up">
        <h2 class="font-display font-black text-3xl md:text-4xl">لماذا المكاتب الكبيرة تنتقل إلينا؟</h2>
        <div class="gold-line w-24 mx-auto mt-5"></div>
    </div>
    <div class="grid md:grid-cols-3 gap-7">
        @foreach([
            ['📈','تعرف «بكم تبيع»','بيانات حقيقية لمتوسط سعر المتر في كل منطقة، فلا تبيع بأقل من قيمته ولا تبالغ فتخسر الزبون.'],
            ['🎯','أكثر الطلبات رواجاً','اعرف ما يبحث عنه المشترون فعلاً، ووجّه استثمارك ومخزونك نحو الطلب الحقيقي.'],
            ['⏱️','وفّر ساعات يومياً','عقود تُكتب في ثوانٍ بدل ساعات، وتنبيهات تلقائية بمواعيد الاستحقاق والتجديد.'],
        ] as $v)
            <div class="glass rounded-3xl p-8 card-hover" data-aos="fade-up">
                <div class="text-4xl mb-4">{{ $v[0] }}</div>
                <h3 class="font-display font-bold text-xl mb-3">{{ $v[1] }}</h3>
                <p class="text-white/55 text-sm leading-7">{{ $v[2] }}</p>
            </div>
        @endforeach
    </div>
</section>
@endsection
