<footer class="relative mt-28 border-t border-white/5">
    <div class="gold-line opacity-50"></div>
    <div class="max-w-7xl mx-auto px-4 py-14">
        <div class="grid md:grid-cols-4 gap-10">
            <div class="md:col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl grid place-items-center" style="background:linear-gradient(135deg,#0f3d2e,#081b15);border:1px solid rgba(212,175,55,.4)">
                        <span class="text-gold text-lg font-black font-display">ع</span>
                    </div>
                    <span class="font-display font-extrabold text-lg gold-text">عقّار سوريا</span>
                </div>
                <p class="text-white/50 text-sm leading-7 max-w-md">
                    المنصة الذكية التي تنقل المكاتب العقارية في سوريا من دفاتر الورق إلى عصر البيانات:
                    مطابقة آلية بين الطلب والعرض، تحليل أسعار المتر لكل منطقة، وأتمتة كاملة للعقود.
                </p>
                <div class="flex gap-3 mt-6">
                    @foreach(['ف','ت','إ','واتساب'] as $s)
                        <a href="#" class="w-10 h-10 rounded-xl glass-light grid place-items-center text-white/60 hover:text-gold hover:border-gold transition text-xs">{{ $s }}</a>
                    @endforeach
                </div>
            </div>
            <div>
                <h4 class="font-display font-bold text-gold mb-4 text-sm">المنصة</h4>
                <ul class="space-y-3 text-sm text-white/50">
                    <li><a href="{{ route('listings.index') }}" class="hover:text-gold transition">تصفح العقارات</a></li>
                    <li><a href="{{ route('market.index') }}" class="hover:text-gold transition">تحليل السوق</a></li>
                    <li><a href="{{ route('match.wizard') }}" class="hover:text-gold transition">المطابقة الذكية</a></li>
                    <li><a href="{{ route('pricing') }}" class="hover:text-gold transition">باقات المكاتب</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-display font-bold text-gold mb-4 text-sm">للمكاتب العقارية</h4>
                <ul class="space-y-3 text-sm text-white/50">
                    <li><a href="{{ route('register') }}" class="hover:text-gold transition">سجّل مكتبك</a></li>
                    <li><a href="{{ route('pricing') }}" class="hover:text-gold transition">الأسعار والباقات</a></li>
                    <li><span class="text-white/30">عقود PDF فورية</span></li>
                    <li><span class="text-white/30">تنبيهات الاستحقاق</span></li>
                </ul>
            </div>
        </div>
        <div class="mt-12 pt-6 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-3 text-xs text-white/30">
            <p>© {{ date('Y') }} عقّار سوريا — صُمّم بشغف لسوق العقارات السوري.</p>
            <p>دمشق · حلب · حمص · اللاذقية</p>
        </div>
    </div>
</footer>
