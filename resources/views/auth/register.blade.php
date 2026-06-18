@extends('layouts.app')
@section('title', 'إنشاء حساب — عقّار سوريا')

@section('content')
<section class="min-h-screen flex items-center justify-center px-4 pt-28 pb-16">
    <div class="w-full max-w-xl" x-data="{ role: '{{ old('role','buyer') }}' }" data-aos="zoom-in">
        <div class="text-center mb-8">
            <h1 class="font-display font-black text-3xl md:text-4xl">انضمّ إلى <span class="gold-text">عقّار سوريا</span></h1>
            <p class="text-white/50 mt-2 text-sm">اختر نوع حسابك وابدأ خلال دقيقة</p>
        </div>

        {{-- Role switch --}}
        <div class="grid grid-cols-2 gap-3 mb-6">
            <button type="button" @click="role='buyer'" :class="role==='buyer' ? 'glass border-gold' : 'glass-light'"
                    class="rounded-2xl p-5 text-right transition">
                <div class="text-2xl mb-2">🔍</div>
                <div class="font-bold">مشترٍ / باحث</div>
                <div class="text-xs text-white/40 mt-1">ابحث واحصل على مطابقات فورية</div>
            </button>
            <button type="button" @click="role='office'" :class="role==='office' ? 'glass border-gold' : 'glass-light'"
                    class="rounded-2xl p-5 text-right transition">
                <div class="text-2xl mb-2">🏢</div>
                <div class="font-bold">مكتب عقاري</div>
                <div class="text-xs text-white/40 mt-1">انشر عقاراتك وأدر مكتبك</div>
            </button>
        </div>

        <form method="POST" action="{{ route('register') }}" class="glass rounded-3xl p-8 space-y-5">
            @csrf
            <input type="hidden" name="role" :value="role">

            <div>
                <label class="text-xs text-white/40 mb-2 block" x-text="role==='office' ? 'اسم المكتب العقاري' : 'الاسم الكامل'"></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full bg-ink-800/70 border border-white/10 rounded-xl px-4 py-3 text-sm focus:border-gold outline-none">
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs text-white/40 mb-2 block">البريد الإلكتروني</label>
                    <input type="email" name="email" value="{{ old('email') }}" required dir="ltr"
                           class="w-full bg-ink-800/70 border border-white/10 rounded-xl px-4 py-3 text-sm focus:border-gold outline-none">
                </div>
                <div>
                    <label class="text-xs text-white/40 mb-2 block">كلمة المرور</label>
                    <input type="password" name="password" required dir="ltr"
                           class="w-full bg-ink-800/70 border border-white/10 rounded-xl px-4 py-3 text-sm focus:border-gold outline-none">
                </div>
            </div>
            <div>
                <label class="text-xs text-white/40 mb-2 block">تأكيد كلمة المرور</label>
                <input type="password" name="password_confirmation" required dir="ltr"
                       class="w-full bg-ink-800/70 border border-white/10 rounded-xl px-4 py-3 text-sm focus:border-gold outline-none">
            </div>

            {{-- Office-only fields --}}
            <div x-show="role==='office'" x-cloak class="space-y-5 pt-2 border-t border-white/5">
                <div>
                    <label class="text-xs text-white/40 mb-2 block">عنوان المكتب</label>
                    <input type="text" name="address" value="{{ old('address') }}"
                           class="w-full bg-ink-800/70 border border-white/10 rounded-xl px-4 py-3 text-sm focus:border-gold outline-none">
                </div>
                <div>
                    <label class="text-xs text-white/40 mb-2 block">المنطقة</label>
                    <select name="district_id" class="w-full bg-ink-800/70 border border-white/10 rounded-xl px-4 py-3 text-sm focus:border-gold outline-none">
                        <option value="">اختر المنطقة</option>
                        @foreach($districts as $d)<option value="{{ $d->id }}" @selected(old('district_id')==$d->id)>{{ $d->name }} — {{ $d->city?->name }}</option>@endforeach
                    </select>
                </div>
            </div>

            <button class="btn-gold rounded-xl w-full py-3.5 text-sm shine">
                <span x-text="role==='office' ? 'سجّل مكتبي العقاري 🏢' : 'إنشاء حسابي 🚀'"></span>
            </button>

            <p class="text-center text-sm text-white/50">
                لديك حساب؟ <a href="{{ route('login') }}" class="text-gold font-bold">سجّل الدخول</a>
            </p>
        </form>
    </div>
</section>
@endsection
