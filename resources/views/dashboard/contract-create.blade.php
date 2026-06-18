@extends('layouts.app')
@section('title', 'إنشاء عقد جديد — لوحة المكتب')

@section('content')
@php
    $inputClass = 'w-full bg-ink-800/60 border border-white/10 rounded-xl px-4 py-3 text-sm focus:border-gold outline-none text-white/90 placeholder-white/30 transition';
    $labelClass = 'block text-xs text-white/55 mb-2';
    $errClass = 'text-red-300 text-xs mt-1';
    $housesData = $houses->mapWithKeys(fn($h) => [$h->id => ['price' => (int) $h->price, 'type' => $h->type]]);
@endphp
<div class="max-w-7xl mx-auto px-4 pt-28 pb-16">
    <div class="grid lg:grid-cols-4 gap-6">
        @include('partials.dashboard-sidebar')

        <div class="lg:col-span-3 space-y-6">

            {{-- Header --}}
            <div class="glass rounded-3xl p-7" data-aos="fade-up">
                <h1 class="font-display font-black text-2xl md:text-3xl">إنشاء عقد جديد 📄</h1>
                <div class="gold-line w-24 mt-3 mb-4"></div>
                <div class="glass-light rounded-2xl px-4 py-3 flex items-center gap-3 text-sm text-white/65">
                    <span class="text-xl">✨</span>
                    سيتم توليد ملف PDF عربي احترافي للعقد فور إنشائه، مع تتبّع تلقائي لمواعيد الاستحقاق.
                </div>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('dashboard.contracts.store') }}" class="glass rounded-3xl p-7"
                  data-aos="fade-up"
                  x-data="{
                      type: '{{ old('type', 'rent') }}',
                      amount: '{{ old('amount') }}',
                      cycle: '{{ old('payment_cycle', 'monthly') }}',
                      houses: {{ Illuminate\Support\Js::from($housesData) }},
                      onHouse(e){
                          const h = this.houses[e.target.value];
                          if(h){ this.amount = h.price; }
                      },
                      onType(){
                          if(this.type === 'sale'){ this.cycle = 'once'; }
                      }
                  }">
                @csrf

                <div class="grid md:grid-cols-2 gap-5">

                    {{-- House --}}
                    <div class="md:col-span-2">
                        <label class="{{ $labelClass }}">العقار</label>
                        <select name="house_id" class="{{ $inputClass }}" @change="onHouse($event)">
                            <option value="" disabled {{ old('house_id') ? '' : 'selected' }}>اختر العقار</option>
                            @foreach($houses as $house)
                                <option value="{{ $house->id }}" {{ (string) old('house_id') === (string) $house->id ? 'selected' : '' }}>
                                    {{ $house->title ?: 'عقار' }} — {{ $house->district?->name }} — ${{ number_format($house->price) }}
                                </option>
                            @endforeach
                        </select>
                        @error('house_id')<p class="{{ $errClass }}">{{ $message }}</p>@enderror
                    </div>

                    {{-- Type --}}
                    <div>
                        <label class="{{ $labelClass }}">نوع العقد</label>
                        <select name="type" class="{{ $inputClass }}" x-model="type" @change="onType()">
                            <option value="rent">إيجار</option>
                            <option value="sale">بيع</option>
                        </select>
                        @error('type')<p class="{{ $errClass }}">{{ $message }}</p>@enderror
                    </div>

                    {{-- Party name --}}
                    <div>
                        <label class="{{ $labelClass }}">اسم المستأجر/المشتري</label>
                        <input type="text" name="party_name" value="{{ old('party_name') }}" placeholder="الاسم الكامل" class="{{ $inputClass }}">
                        @error('party_name')<p class="{{ $errClass }}">{{ $message }}</p>@enderror
                    </div>

                    {{-- Party phone --}}
                    <div>
                        <label class="{{ $labelClass }}">رقم الهاتف <span class="text-white/30">(اختياري)</span></label>
                        <input type="text" name="party_phone" value="{{ old('party_phone') }}" placeholder="09xxxxxxxx" class="{{ $inputClass }}">
                        @error('party_phone')<p class="{{ $errClass }}">{{ $message }}</p>@enderror
                    </div>

                    {{-- National ID --}}
                    <div>
                        <label class="{{ $labelClass }}">الرقم الوطني <span class="text-white/30">(اختياري)</span></label>
                        <input type="text" name="party_national_id" value="{{ old('party_national_id') }}" placeholder="الرقم الوطني" class="{{ $inputClass }}">
                        @error('party_national_id')<p class="{{ $errClass }}">{{ $message }}</p>@enderror
                    </div>

                    {{-- Amount --}}
                    <div>
                        <label class="{{ $labelClass }}">قيمة العقد (دولار)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gold font-bold">$</span>
                            <input type="number" name="amount" min="0" placeholder="50000" class="{{ $inputClass }} pl-8" x-model="amount">
                        </div>
                        @error('amount')<p class="{{ $errClass }}">{{ $message }}</p>@enderror
                    </div>

                    {{-- Payment cycle --}}
                    <div x-show="type === 'rent'" x-transition>
                        <label class="{{ $labelClass }}">دورة الدفع</label>
                        <select name="payment_cycle" class="{{ $inputClass }}" x-model="cycle">
                            <option value="once">دفعة واحدة</option>
                            <option value="monthly">شهري</option>
                            <option value="quarterly">ربع سنوي</option>
                            <option value="yearly">سنوي</option>
                        </select>
                        @error('payment_cycle')<p class="{{ $errClass }}">{{ $message }}</p>@enderror
                    </div>
                    {{-- Hidden cycle mirror for sale (once) so it always submits --}}
                    <template x-if="type === 'sale'">
                        <input type="hidden" name="payment_cycle" :value="cycle">
                    </template>

                    {{-- Start date --}}
                    <div>
                        <label class="{{ $labelClass }}">تاريخ البداية</label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}" class="{{ $inputClass }}">
                        @error('start_date')<p class="{{ $errClass }}">{{ $message }}</p>@enderror
                    </div>

                    {{-- End date --}}
                    <div>
                        <label class="{{ $labelClass }}">تاريخ النهاية <span class="text-white/30">(اختياري)</span></label>
                        <input type="date" name="end_date" value="{{ old('end_date') }}" class="{{ $inputClass }}">
                        @error('end_date')<p class="{{ $errClass }}">{{ $message }}</p>@enderror
                    </div>

                    {{-- Notes --}}
                    <div class="md:col-span-2">
                        <label class="{{ $labelClass }}">ملاحظات <span class="text-white/30">(اختياري)</span></label>
                        <textarea name="notes" rows="3" placeholder="بنود إضافية أو ملاحظات على العقد..."
                                  class="{{ $inputClass }} resize-none">{{ old('notes') }}</textarea>
                        @error('notes')<p class="{{ $errClass }}">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mt-7 pt-6 border-t border-white/10 flex flex-wrap items-center justify-between gap-4">
                    <p class="text-xs text-white/40 flex items-center gap-2">
                        📑 سيتم توليد ملف PDF احترافي للعقد فور الإنشاء.
                    </p>
                    <button type="submit" class="btn-gold rounded-2xl px-7 py-3 text-sm shine">
                        📄 إنشاء العقد وتوليد PDF
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
