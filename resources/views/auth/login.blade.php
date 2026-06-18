@extends('layouts.app')
@section('title', 'تسجيل الدخول — عقّار سوريا')

@section('content')
<section class="min-h-screen flex items-center justify-center px-4 pt-28 pb-16">
    <div class="w-full max-w-md" data-aos="zoom-in">
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-3 mb-6">
                <div class="w-12 h-12 rounded-xl grid place-items-center text-gold text-xl font-black"
                     style="background:linear-gradient(135deg,#0f3d2e,#081b15);border:1px solid rgba(212,175,55,.4)">ع</div>
            </a>
            <h1 class="font-display font-black text-3xl">أهلاً بعودتك 👋</h1>
            <p class="text-white/50 mt-2 text-sm">سجّل دخولك لمتابعة عقاراتك ومطابقاتك</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="glass rounded-3xl p-8 space-y-5">
            @csrf
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
            <label class="flex items-center gap-2 text-sm text-white/50">
                <input type="checkbox" name="remember" class="accent-gold"> تذكّرني
            </label>
            <button class="btn-gold rounded-xl w-full py-3.5 text-sm shine">تسجيل الدخول</button>

            <p class="text-center text-sm text-white/50">
                ليس لديك حساب؟ <a href="{{ route('register') }}" class="text-gold font-bold">أنشئ حساباً</a>
            </p>
        </form>

        <div class="glass-light rounded-2xl p-4 mt-5 text-xs text-white/50 leading-6">
            💡 حساب تجريبي للمكتب: <span class="text-gold" dir="ltr">office@gmail.com</span> / <span class="text-gold" dir="ltr">111111</span>
        </div>
    </div>
</section>
@endsection
