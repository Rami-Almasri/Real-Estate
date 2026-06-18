@php
    $u = auth()->user();
    $isOffice = $u?->isOffice();
    $unread = $u && ! $isOffice ? $u->matchNotifications()->where('is_read', false)->count() : 0;
@endphp
<nav x-data="{open:false, scrolled:false}" @scroll.window="scrolled = window.scrollY > 30"
     class="fixed top-0 inset-x-0 z-50 transition-all duration-300"
     :class="scrolled ? 'py-2' : 'py-4'">
    <div class="max-w-7xl mx-auto px-4">
        <div class="glass rounded-2xl px-5 py-3 flex items-center justify-between transition-all"
             :class="scrolled ? 'shadow-glass' : ''">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="relative w-11 h-11 rounded-xl grid place-items-center shine"
                     style="background:linear-gradient(135deg,#0f3d2e,#081b15);border:1px solid rgba(212,175,55,.4)">
                    <span class="text-gold text-xl font-black font-display">ع</span>
                    <span class="absolute -top-1 -left-1 w-3 h-3 rounded-full bg-gold animate-ping opacity-60"></span>
                </div>
                <div class="leading-tight">
                    <div class="font-display font-extrabold text-lg gold-text">عقّار سوريا</div>
                    <div class="text-[10px] text-white/40 tracking-widest">AQAR · SYRIA</div>
                </div>
            </a>

            {{-- Desktop links --}}
            <div class="hidden lg:flex items-center gap-8 text-sm font-medium text-white/80">
                <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active text-gold' : '' }}">الرئيسية</a>
                <a href="{{ route('listings.index') }}" class="nav-link {{ request()->routeIs('listings.*') ? 'active text-gold' : '' }}">العقارات</a>
                <a href="{{ route('market.index') }}" class="nav-link {{ request()->routeIs('market.*') ? 'active text-gold' : '' }}">تحليل السوق</a>
                <a href="{{ route('match.wizard') }}" class="nav-link {{ request()->routeIs('match.wizard') ? 'active text-gold' : '' }}">المطابقة الذكية</a>
                <a href="{{ route('pricing') }}" class="nav-link {{ request()->routeIs('pricing') ? 'active text-gold' : '' }}">للمكاتب</a>
            </div>

            {{-- Auth area --}}
            <div class="hidden lg:flex items-center gap-3">
                @guest
                    <a href="{{ route('login') }}" class="text-sm text-white/80 hover:text-gold transition">دخول</a>
                    <a href="{{ route('register') }}" class="btn-gold rounded-xl px-5 py-2.5 text-sm">ابدأ مجاناً</a>
                @else
                    @if($isOffice)
                        <a href="{{ route('dashboard.index') }}" class="btn-ghost rounded-xl px-4 py-2 text-sm">لوحة المكتب</a>
                    @else
                        <a href="{{ route('account.matches') }}" class="relative btn-ghost rounded-xl px-4 py-2 text-sm">
                            مطابقاتي
                            @if($unread)<span class="absolute -top-2 -left-2 bg-gold text-ink text-[10px] w-5 h-5 grid place-items-center rounded-full font-bold">{{ $unread }}</span>@endif
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">@csrf
                        <button class="text-sm text-white/50 hover:text-red-300 transition">خروج</button>
                    </form>
                @endguest
            </div>

            {{-- Mobile toggle --}}
            <button @click="open=!open" class="lg:hidden text-gold text-2xl w-10 h-10 grid place-items-center">
                <span x-show="!open">☰</span><span x-show="open" x-cloak>✕</span>
            </button>
        </div>

        {{-- Mobile menu --}}
        <div x-show="open" x-cloak x-transition class="lg:hidden mt-2 glass rounded-2xl p-5 space-y-3 text-sm">
            <a href="{{ route('home') }}" class="block py-2 border-b border-white/5">الرئيسية</a>
            <a href="{{ route('listings.index') }}" class="block py-2 border-b border-white/5">العقارات</a>
            <a href="{{ route('market.index') }}" class="block py-2 border-b border-white/5">تحليل السوق</a>
            <a href="{{ route('match.wizard') }}" class="block py-2 border-b border-white/5">المطابقة الذكية</a>
            <a href="{{ route('pricing') }}" class="block py-2 border-b border-white/5">للمكاتب العقارية</a>
            @guest
                <a href="{{ route('login') }}" class="block py-2">دخول</a>
                <a href="{{ route('register') }}" class="btn-gold rounded-xl px-4 py-2.5 text-center block mt-2">ابدأ مجاناً</a>
            @else
                <a href="{{ $isOffice ? route('dashboard.index') : route('account.matches') }}" class="btn-ghost rounded-xl px-4 py-2.5 text-center block">{{ $isOffice ? 'لوحة المكتب' : 'مطابقاتي' }}</a>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">@csrf<button class="w-full text-center py-2 text-red-300">تسجيل الخروج</button></form>
            @endguest
        </div>
    </div>
</nav>
