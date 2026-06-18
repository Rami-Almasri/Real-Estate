@extends('layouts.app')
@section('title', 'نشر عقار جديد — لوحة المكتب')

@section('content')
<div class="max-w-7xl mx-auto px-4 pt-28 pb-16">
    <div class="grid lg:grid-cols-4 gap-6">
        @include('partials.dashboard-sidebar')

        <div class="lg:col-span-3 space-y-6">

            {{-- Header --}}
            <div class="glass rounded-3xl p-7" data-aos="fade-up">
                <h1 class="font-display font-black text-2xl md:text-3xl">نشر عقار جديد 🚀</h1>
                <div class="gold-line w-24 mt-3 mb-4"></div>
                <div class="glass-light rounded-2xl px-4 py-3 flex items-center gap-3 text-sm text-white/65">
                    <span class="text-xl">⚡</span>
                    بمجرد النشر، يُطلق النظام مطابقة فورية ويُشعر المشترين المهتمين الذين تطابق تفضيلاتهم مع عقارك.
                </div>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('dashboard.listings.store') }}" class="glass rounded-3xl p-7" data-aos="fade-up">
                @csrf

                @php
                    $inputClass = 'w-full bg-ink-800/60 border border-white/10 rounded-xl px-4 py-3 text-sm focus:border-gold outline-none text-white/90 placeholder-white/30 transition';
                    $labelClass = 'block text-xs text-white/55 mb-2';
                    $errClass = 'text-red-300 text-xs mt-1';
                @endphp

                <div class="grid md:grid-cols-2 gap-5">

                    {{-- Title --}}
                    <div class="md:col-span-2">
                        <label class="{{ $labelClass }}">عنوان العقار <span class="text-white/30">(اختياري)</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" placeholder="مثال: شقة فاخرة بإطلالة"
                               class="{{ $inputClass }}">
                        @error('title')<p class="{{ $errClass }}">{{ $message }}</p>@enderror
                    </div>

                    {{-- Description --}}
                    <div class="md:col-span-2">
                        <label class="{{ $labelClass }}">الوصف <span class="text-white/30">(اختياري)</span></label>
                        <textarea name="description" rows="4" placeholder="تفاصيل العقار، المميزات، الموقع..."
                                  class="{{ $inputClass }} resize-none">{{ old('description') }}</textarea>
                        @error('description')<p class="{{ $errClass }}">{{ $message }}</p>@enderror
                    </div>

                    {{-- District --}}
                    <div>
                        <label class="{{ $labelClass }}">المنطقة</label>
                        <select name="district_id" class="{{ $inputClass }}">
                            <option value="" disabled {{ old('district_id') ? '' : 'selected' }}>اختر المنطقة</option>
                            @foreach($districts->groupBy(fn($d) => $d->city?->name) as $cityName => $group)
                                <optgroup label="{{ $cityName }}">
                                    @foreach($group as $district)
                                        <option value="{{ $district->id }}" {{ (string) old('district_id') === (string) $district->id ? 'selected' : '' }}>
                                            {{ $district->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('district_id')<p class="{{ $errClass }}">{{ $message }}</p>@enderror
                    </div>

                    {{-- Type --}}
                    <div>
                        <label class="{{ $labelClass }}">نوع العرض</label>
                        <select name="type" class="{{ $inputClass }}">
                            <option value="rent" {{ old('type') === 'rent' ? 'selected' : '' }}>إيجار</option>
                            <option value="sale" {{ old('type') === 'sale' ? 'selected' : '' }}>بيع</option>
                        </select>
                        @error('type')<p class="{{ $errClass }}">{{ $message }}</p>@enderror
                    </div>

                    {{-- Rooms --}}
                    <div>
                        <label class="{{ $labelClass }}">عدد الغرف</label>
                        <input type="number" name="rooms" value="{{ old('rooms') }}" min="0" placeholder="3" class="{{ $inputClass }}">
                        @error('rooms')<p class="{{ $errClass }}">{{ $message }}</p>@enderror
                    </div>

                    {{-- Floor --}}
                    <div>
                        <label class="{{ $labelClass }}">الطابق</label>
                        <input type="number" name="floor" value="{{ old('floor') }}" placeholder="2" class="{{ $inputClass }}">
                        @error('floor')<p class="{{ $errClass }}">{{ $message }}</p>@enderror
                    </div>

                    {{-- Area --}}
                    <div>
                        <label class="{{ $labelClass }}">المساحة (م²)</label>
                        <input type="number" name="area" value="{{ old('area') }}" step="0.1" min="0" placeholder="120" class="{{ $inputClass }}">
                        @error('area')<p class="{{ $errClass }}">{{ $message }}</p>@enderror
                    </div>

                    {{-- Direction --}}
                    <div>
                        <label class="{{ $labelClass }}">الاتجاه</label>
                        <input type="text" name="direction" value="{{ old('direction') }}" placeholder="شمالي" class="{{ $inputClass }}">
                        @error('direction')<p class="{{ $errClass }}">{{ $message }}</p>@enderror
                    </div>

                    {{-- Price --}}
                    <div>
                        <label class="{{ $labelClass }}">السعر (دولار)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gold font-bold">$</span>
                            <input type="number" name="price" value="{{ old('price') }}" min="0" placeholder="50000" class="{{ $inputClass }} pl-8">
                        </div>
                        @error('price')<p class="{{ $errClass }}">{{ $message }}</p>@enderror
                    </div>

                    {{-- Cover image --}}
                    <div class="md:col-span-2">
                        <label class="{{ $labelClass }}">رابط صورة الغلاف <span class="text-white/30">(اختياري)</span></label>
                        <input type="url" name="cover_image" value="{{ old('cover_image') }}" placeholder="رابط صورة" class="{{ $inputClass }}">
                        @error('cover_image')<p class="{{ $errClass }}">{{ $message }}</p>@enderror
                    </div>

                    {{-- Featured --}}
                    <div class="md:col-span-2">
                        <label class="flex items-center gap-3 glass-light rounded-2xl px-4 py-3 cursor-pointer">
                            <input type="checkbox" name="featured" value="1" {{ old('featured') ? 'checked' : '' }}
                                   class="w-5 h-5 rounded accent-gold">
                            <span class="text-sm">⭐ عقار مميّز <span class="text-white/40">(يظهر في المقدّمة)</span></span>
                        </label>
                        @error('featured')<p class="{{ $errClass }}">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mt-7 pt-6 border-t border-white/10 flex flex-wrap items-center justify-between gap-4">
                    <p class="text-xs text-white/40 flex items-center gap-2">
                        🔔 سيتم إشعار المشترين المطابقين فور النشر.
                    </p>
                    <button type="submit" class="btn-gold rounded-2xl px-7 py-3 text-sm shine">
                        🚀 نشر العقار وإطلاق المطابقة
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
