<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'عقّار سوريا — المنصة الذكية لإدارة وتسويق العقارات')</title>
    <meta name="description" content="عقّار سوريا: مطابقة ذكية بين المشترين والعقارات، تحليل أسعار السوق لكل منطقة، وأتمتة العقود. ثورة رقمية للمكاتب العقارية في سوريا.">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

    {{-- Tailwind (Play CDN) with bespoke theme --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Tajawal', 'sans-serif'],
                        display: ['Cairo', 'sans-serif'],
                    },
                    colors: {
                        emerald2: { DEFAULT: '#0f3d2e', deep: '#0a2a20', dark: '#081b15' },
                        gold: { DEFAULT: '#d4af37', light: '#f0d27a', soft: '#e8c766' },
                        ink: { DEFAULT: '#0a0f0d', 800: '#0d1612', 700: '#11201a' },
                        cream: '#f7f5ef',
                    },
                    boxShadow: {
                        gold: '0 10px 40px -10px rgba(212,175,55,.45)',
                        glass: '0 8px 32px rgba(0,0,0,.37)',
                    },
                    keyframes: {
                        float: { '0%,100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-18px)' } },
                        shimmer: { '100%': { transform: 'translateX(-100%)' } },
                    },
                    animation: { float: 'float 7s ease-in-out infinite' },
                }
            }
        }
    </script>

    {{-- AOS scroll animations --}}
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <style>
        :root { --gold:#d4af37; --emerald:#0f3d2e; }
        body { background:#070b09; color:#e8ece9; overflow-x:hidden; }
        ::selection { background: var(--gold); color:#0a0f0d; }

        /* Aurora / mesh background */
        .aurora { position:fixed; inset:0; z-index:-2; overflow:hidden; }
        .aurora span { position:absolute; border-radius:50%; filter:blur(90px); opacity:.5; }
        .aurora .b1 { width:520px;height:520px;background:#0f6b4a; top:-120px; right:-80px; animation:float 12s ease-in-out infinite; }
        .aurora .b2 { width:460px;height:460px;background:#b8902a; bottom:-140px; left:-100px; animation:float 15s ease-in-out infinite reverse; opacity:.28; }
        .aurora .b3 { width:380px;height:380px;background:#0a3f6b; top:40%; left:35%; animation:float 18s ease-in-out infinite; opacity:.22; }
        .grain { position:fixed; inset:0; z-index:-1; opacity:.035; pointer-events:none;
            background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='3'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E"); }

        .glass { background:rgba(18,28,23,.55); backdrop-filter:blur(16px); border:1px solid rgba(255,255,255,.08); }
        .glass-light { background:rgba(255,255,255,.04); backdrop-filter:blur(10px); border:1px solid rgba(255,255,255,.07); }
        .gold-text { background:linear-gradient(120deg,#f0d27a,#d4af37,#b8902a,#f0d27a); -webkit-background-clip:text; background-clip:text; color:transparent; background-size:200% auto; }
        .gold-line { height:2px; background:linear-gradient(90deg,transparent,var(--gold),transparent); }
        .btn-gold { background:linear-gradient(120deg,#f0d27a,#d4af37); color:#0a0f0d; font-weight:700; transition:.3s; box-shadow:0 8px 26px -8px rgba(212,175,55,.6); }
        .btn-gold:hover { transform:translateY(-2px); box-shadow:0 14px 34px -8px rgba(212,175,55,.75); }
        .btn-ghost { border:1px solid rgba(212,175,55,.45); color:#f0d27a; transition:.3s; }
        .btn-ghost:hover { background:rgba(212,175,55,.12); border-color:var(--gold); }

        .card-hover { transition:transform .4s cubic-bezier(.2,.8,.2,1), box-shadow .4s; }
        .card-hover:hover { transform:translateY(-8px); box-shadow:0 24px 50px -18px rgba(0,0,0,.7), 0 0 0 1px rgba(212,175,55,.25); }

        .reveal-img { transition:transform .8s cubic-bezier(.2,.8,.2,1); }
        .group:hover .reveal-img { transform:scale(1.08); }

        .nav-link { position:relative; }
        .nav-link::after { content:''; position:absolute; bottom:-6px; right:0; width:0; height:2px; background:var(--gold); transition:.3s; }
        .nav-link:hover::after, .nav-link.active::after { width:100%; }

        .shine { position:relative; overflow:hidden; }
        .shine::before { content:''; position:absolute; top:0; left:-150%; width:60%; height:100%;
            background:linear-gradient(120deg,transparent,rgba(255,255,255,.25),transparent); transform:skewX(-20deg); }
        .shine:hover::before { animation:shineMove .9s; }
        @keyframes shineMove { 100%{ left:150%; } }

        .marquee { display:flex; gap:3rem; animation:scrollx 28s linear infinite; }
        @keyframes scrollx { to { transform:translateX(50%); } }

        ::-webkit-scrollbar { width:10px; } ::-webkit-scrollbar-track { background:#0a0f0d; }
        ::-webkit-scrollbar-thumb { background:linear-gradient(#d4af37,#0f3d2e); border-radius:10px; }

        [x-cloak] { display:none !important; }
        .badge { font-size:11px; padding:3px 10px; border-radius:99px; font-weight:700; }
    </style>
    @stack('head')
</head>
<body class="font-sans antialiased">
    <div class="aurora"><span class="b1"></span><span class="b2"></span><span class="b3"></span></div>
    <div class="grain"></div>

    @include('partials.navbar')

    {{-- Flash messages --}}
    @if(session('success') || session('error') || $errors->any())
        <div x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,6000)" x-cloak
             class="fixed top-24 left-1/2 -translate-x-1/2 z-[60] w-[92%] max-w-md">
            @if(session('success'))
                <div class="glass rounded-2xl px-5 py-4 border-r-4 border-emerald-400 flex items-center gap-3 shadow-glass">
                    <span class="text-emerald-400 text-xl">✓</span>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            @endif
            @if(session('error'))
                <div class="glass rounded-2xl px-5 py-4 border-r-4 border-red-400 flex items-center gap-3 shadow-glass">
                    <span class="text-red-400 text-xl">!</span><p class="text-sm">{{ session('error') }}</p>
                </div>
            @endif
            @if($errors->any())
                <div class="glass rounded-2xl px-5 py-4 border-r-4 border-amber-400 shadow-glass">
                    <ul class="text-sm space-y-1">@foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach</ul>
                </div>
            @endif
        </div>
    @endif

    <main>@yield('content')</main>

    @include('partials.footer')

    {{-- Scripts --}}
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        AOS.init({ duration:800, easing:'ease-out-cubic', once:true, offset:60 });

        // Animated count-up for any [data-count] element.
        function countUp(el){
            const target = +el.dataset.count, dur = 1600, start = performance.now();
            const fmt = el.dataset.fmt === '1';
            function tick(now){
                const p = Math.min((now-start)/dur, 1);
                const eased = 1-Math.pow(1-p,3);
                const val = Math.floor(eased*target);
                el.textContent = fmt ? val.toLocaleString('en-US') : val;
                if(p<1) requestAnimationFrame(tick); else el.textContent = fmt ? target.toLocaleString('en-US') : target;
            }
            requestAnimationFrame(tick);
        }
        document.querySelectorAll('[data-count]').forEach(el=>{
            new IntersectionObserver((e,o)=>{ if(e[0].isIntersecting){ countUp(el); o.disconnect(); } },{threshold:.4}).observe(el);
        });
    </script>
    @stack('scripts')
</body>
</html>
