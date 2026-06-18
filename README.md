<div align="center">

# 🏛️ عقّار سوريا · Aqar Syria

### المنصة العقارية الذكية — من دفاتر الورق إلى ذكاء السوق
**The smart real-estate platform for Syria — from paper notebooks to market intelligence.**

Laravel 12 · Tailwind · Alpine · GSAP · Chart.js · Leaflet · dompdf

</div>

---

## ✨ الفكرة

سوق العقارات في سوريا في حالة "فوران" لكنه عشوائي، والمكاتب الكبيرة ما زالت تعتمد على الورق.
**عقّار سوريا** يحوّل المكتب العقاري إلى منصة مدعومة بالبيانات، ويُباع للمكاتب باشتراك شهري (SaaS).

## 🔥 المزايا الأساسية (Core Features)

| الميزة | الوصف |
|---|---|
| ⚡ **خوارزمية المطابقة** | المشتري يحفظ مواصفاته (منطقة، نوع، سعر، غرف، مساحة)، والنظام يفلتر آلاف العقارات فوراً ويُنشئ إشعار مطابقة مع **درجة تطابق** عند نشر أي عقار جديد. |
| 📊 **لوحة تحليل السوق** | متوسط **سعر المتر** لكل منطقة ومدينة بناءً على بيانات حقيقية، اتجاه الأسعار آخر 6 أشهر، توزّع البيع/الإيجار، ومؤشر **العرض مقابل الطلب** — مرجع المستثمرين. |
| 📄 **أتمتة العقود** | توليد عقود إيجار/بيع **PDF احترافية بالعربية** (RTL + خط Amiri) خلال ثوانٍ، مع **تنبيهات استحقاق** ذكية (متأخر / يستحق قريباً). |
| 💎 **اشتراكات المكاتب** | ثلاث باقات (الأساسية/الاحترافية/النخبة) تتحكّم بحد العقارات وتفتح المزايا. هي طبقة الإيرادات. |

بالإضافة إلى: تصفّح وفلترة العقارات، صفحة عقار بخريطة Leaflet، تتبّع المشاهدات والتقييمات، طلبات المشترين (Leads) للمكتب، وواجهة عربية فاخرة (RTL) بحركات GSAP/AOS.

## 🏗️ المعمارية (Architecture)

- **Backend:** Laravel 12, طبقة خدمات (`app/Services`): `MatchingService`, `AnalyticsService`, `ContractService`, `SubscriptionService`.
- **Auth:** جلسات الويب (web guard) للواجهة + Sanctum للـ API. مستخدمون متعدّدو الأنماط (`userable`) — مكتب / مشترٍ / أدمن.
- **DB:** SQLite جاهزة فوراً (قابلة للتبديل إلى MySQL عبر `.env`).
- **PDF:** `barryvdh/laravel-dompdf` + `khaled.alshamaa/ar-php` لتشكيل الحروف العربية + خط Amiri مضمّن.
- **Frontend:** Blade + Tailwind (Play CDN) + Alpine.js + GSAP/ScrollTrigger + AOS + Chart.js + Leaflet.

نماذج جديدة: `Preference`, `MatchNotification`, `Contract` — وتحسينات على `House`, `Office`, `Subsbcribe`.

## 🚀 التشغيل (Setup)

```bash
composer install
npm install                 # اختياري (الأصول عبر CDN)
cp .env.example .env        # أو استخدم .env الجاهز (SQLite)
php artisan key:generate
php artisan migrate:fresh --seed   # ينشئ بيانات تجريبية غنية
php artisan storage:link
php artisan serve            # http://127.0.0.1:8000
```

> يتطلب توليد عقود الـPDF إضافة ar-php و dompdf (مثبّتة عبر composer). الخطوط في `storage/fonts`.

## 🔑 حسابات تجريبية (Demo Accounts)

| الدور | البريد | كلمة المرور |
|---|---|---|
| 🏢 مكتب عقاري (Elite) | `office@gmail.com` | `111111` |
| 🏢 مكتب (Pro) | `qasr@aqar.sy` | `111111` |
| 🔍 مشترٍ | `test@gmail.com` | `111111` |

## 🗺️ خريطة الصفحات

`/` الرئيسية · `/listings` العقارات · `/listings/{id}` تفاصيل عقار · `/market` تحليل السوق ·
`/match` المطابقة الذكية · `/pricing` الباقات · `/dashboard/*` لوحة المكتب (عقارات، طلبات، عقود، اشتراك) ·
`/account/matches` مطابقات المشتري.

## 📦 البيانات التجريبية

5 مكاتب · 64 عقاراً موزّعة على 5 مدن و13 منطقة · 9 مشترين بطلبات محفوظة · عقود بمواعيد استحقاق متنوّعة ·
آلاف المشاهدات والتقييمات — لتظهر اللوحات والتحليلات حيّة.

---

<div align="center">
صُمّم بشغف لسوق العقارات السوري 🇸🇾
</div>
