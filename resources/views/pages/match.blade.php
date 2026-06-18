@extends('layouts.app')
@section('title', 'المطابقة الذكية — عقّار سوريا')

@section('content')
<section class="pt-32 pb-10 max-w-7xl mx-auto px-4 text-center">
    <div class="inline-flex items-center gap-2 glass-light rounded-full px-4 py-2 mb-6 text-xs text-gold" data-aos="fade-down">
        <span class="w-2 h-2 rounded-full bg-gold animate-pulse"></span> مدعوم بخوارزمية المطابقة
    </div>
    <h1 class="font-display font-black text-4xl md:text-6xl" data-aos="fade-up">حدّد ما تريد، <span class="gold-text">ودعنا نجده</span></h1>
    <p class="text-white/55 mt-4 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">
        أدخل مواصفاتك وستفلتر الخوارزمية آلاف العقارات في لحظة، مرتّبة حسب درجة التطابق. احفظ طلبك لتصلك إشعارات فورية عند توفّر عقار جديد.
    </p>
</section>

<section class="max-w-7xl mx-auto px-4" x-data="matcher()" x-init="search()">
    <div class="grid lg:grid-cols-3 gap-7">
        {{-- Criteria form --}}
        <div class="glass rounded-3xl p-7 h-fit lg:sticky lg:top-28" data-aos="fade-right">
            <h2 class="font-display font-bold text-lg mb-6">معايير البحث</h2>

            <div class="space-y-5">
                <div>
                    <label class="text-xs text-white/40 mb-2 block">نوع العقد</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" @click="form.type=''; search()" :class="form.type===''?'btn-gold':'glass-light text-white/60'" class="rounded-xl py-2.5 text-sm transition">الكل</button>
                        <button type="button" @click="form.type='rent'; search()" :class="form.type==='rent'?'btn-gold':'glass-light text-white/60'" class="rounded-xl py-2.5 text-sm transition">إيجار</button>
                        <button type="button" @click="form.type='sale'; search()" :class="form.type==='sale'?'btn-gold':'glass-light text-white/60'" class="rounded-xl py-2.5 text-sm transition">بيع</button>
                    </div>
                </div>

                <div>
                    <label class="text-xs text-white/40 mb-2 block">المدينة</label>
                    <select x-model="form.city_id" @change="form.district_id=''; search()" class="w-full bg-ink-800/70 border border-white/10 rounded-xl px-3 py-3 text-sm focus:border-gold outline-none">
                        <option value="">كل المدن</option>
                        @foreach($cities as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs text-white/40 mb-2 block">المنطقة</label>
                    <select x-model="form.district_id" @change="search()" class="w-full bg-ink-800/70 border border-white/10 rounded-xl px-3 py-3 text-sm focus:border-gold outline-none">
                        <option value="">كل المناطق</option>
                        @foreach($districts as $d)<option value="{{ $d->id }}" data-city="{{ $d->city_id }}" x-show="!form.city_id || form.city_id == '{{ $d->city_id }}'">{{ $d->name }} — {{ $d->city?->name }}</option>@endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs text-white/40 mb-2 block">الميزانية القصوى: <span class="text-gold font-bold" x-text="form.max_price ? '$'+(+form.max_price).toLocaleString() : 'بلا حد'"></span></label>
                    <input type="range" min="0" max="500000" step="5000" x-model="form.max_price" @input="searchDebounced()" class="w-full accent-gold">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-white/40 mb-2 block">غرف (الأدنى)</label>
                        <input type="number" min="1" x-model="form.min_rooms" @input="searchDebounced()" placeholder="أي" class="w-full bg-ink-800/70 border border-white/10 rounded-xl px-3 py-3 text-sm focus:border-gold outline-none">
                    </div>
                    <div>
                        <label class="text-xs text-white/40 mb-2 block">مساحة (الأدنى)</label>
                        <input type="number" min="0" x-model="form.min_area" @input="searchDebounced()" placeholder="م²" class="w-full bg-ink-800/70 border border-white/10 rounded-xl px-3 py-3 text-sm focus:border-gold outline-none">
                    </div>
                </div>
            </div>

            {{-- Save preference --}}
            <div class="mt-7 pt-6 border-t border-white/5">
                @auth
                    <form method="POST" action="{{ route('preferences.store') }}">
                        @csrf
                        <input type="hidden" name="city_id" :value="form.city_id">
                        <input type="hidden" name="district_id" :value="form.district_id">
                        <input type="hidden" name="type" :value="form.type">
                        <input type="hidden" name="min_rooms" :value="form.min_rooms">
                        <input type="hidden" name="max_price" :value="form.max_price">
                        <input type="hidden" name="min_area" :value="form.min_area">
                        <button class="btn-gold rounded-xl w-full py-3.5 text-sm shine">🔔 احفظ الطلب وتلقَّ إشعارات</button>
                    </form>
                @else
                    <a href="{{ route('register') }}" class="btn-gold rounded-xl w-full py-3.5 text-sm block text-center shine">🔔 سجّل لحفظ الطلب وتلقّي الإشعارات</a>
                @endauth
                <p class="text-white/30 text-[11px] text-center mt-3">عند توفّر عقار مطابق سيصلك إشعار فوري</p>
            </div>
        </div>

        {{-- Results --}}
        <div class="lg:col-span-2">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-display font-bold text-lg">
                    <span x-show="!loading">وُجد <span class="gold-text font-black" x-text="count"></span> عقار مطابق</span>
                    <span x-show="loading" class="text-white/50">جارٍ الفلترة...</span>
                </h2>
                <div x-show="loading" class="w-5 h-5 border-2 border-gold/30 border-t-gold rounded-full animate-spin"></div>
            </div>

            <template x-if="!loading && count===0">
                <div class="glass rounded-3xl p-14 text-center">
                    <div class="text-5xl mb-4">🔍</div>
                    <h3 class="font-display font-bold text-lg mb-2">لا توجد نتائج مطابقة</h3>
                    <p class="text-white/50">جرّب توسيع الميزانية أو تغيير المنطقة.</p>
                </div>
            </template>

            <div class="grid md:grid-cols-2 gap-6">
                <template x-for="h in results" :key="h.id">
                    <a :href="h.url" class="group glass rounded-3xl overflow-hidden card-hover block">
                        <div class="relative h-44 overflow-hidden">
                            <img :src="h.cover" class="reveal-img w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                            <div class="absolute top-3 left-3">
                                <div class="badge bg-gold text-ink font-black">تطابق <span x-text="h.score"></span>%</div>
                            </div>
                            <span class="absolute top-3 right-3 badge" :class="h.type==='rent'?'bg-sky-500/25 text-sky-200':'bg-gold/25 text-gold'" x-text="h.type==='rent'?'إيجار':'بيع'"></span>
                            <div class="absolute bottom-3 right-4 text-white">
                                <div class="font-display text-xl font-black">$<span x-text="(+h.price).toLocaleString()"></span></div>
                            </div>
                        </div>
                        <div class="p-5">
                            <h3 class="font-bold mb-1" x-text="h.title"></h3>
                            <p class="text-white/45 text-xs mb-3">📍 <span x-text="h.district"></span>، <span x-text="h.city"></span></p>
                            <div class="flex justify-between text-xs text-white/60 border-t border-white/5 pt-3">
                                <span>🛏 <span x-text="h.rooms"></span> غرف</span>
                                <span>📐 <span x-text="h.area"></span> م²</span>
                                <span class="gold-text font-bold">$<span x-text="(+h.ppm).toLocaleString()"></span>/م²</span>
                            </div>
                        </div>
                    </a>
                </template>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    function matcher(){
        return {
            form:{ type:'', city_id:'', district_id:'', max_price:'', min_rooms:'', min_area:'' },
            results:[], count:0, loading:false, _t:null,
            searchDebounced(){ clearTimeout(this._t); this._t=setTimeout(()=>this.search(),350); },
            async search(){
                this.loading=true;
                const p = new URLSearchParams();
                Object.entries(this.form).forEach(([k,v])=>{ if(v!=='' && v!==null) p.append(k,v); });
                try {
                    const r = await fetch('{{ route('match.preview') }}?'+p.toString());
                    const d = await r.json();
                    this.results = d.results; this.count = d.count;
                } catch(e){ this.results=[]; this.count=0; }
                this.loading=false;
            }
        }
    }
</script>
@endpush
@endsection
