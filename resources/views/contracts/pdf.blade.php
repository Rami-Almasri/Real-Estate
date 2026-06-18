@php
    // dompdf reads local fonts via file:// URLs with forward slashes.
    $fontReg  = 'file://' . str_replace('\\', '/', storage_path('fonts/Amiri-Regular.ttf'));
    $fontBold = 'file://' . str_replace('\\', '/', storage_path('fonts/Amiri-Bold.ttf'));
    $house    = $contract->house;
    $office    = $contract->office;
    $district = $house->district;
    $city     = $district?->city;
    $isSale   = $contract->type === 'sale';
    $cycles   = ['once' => 'دفعة واحدة', 'monthly' => 'شهري', 'quarterly' => 'ربع سنوي', 'yearly' => 'سنوي'];
@endphp
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    @font-face { font-family: 'amiri'; font-weight: normal; src: url('{{ $fontReg }}') format('truetype'); }
    @font-face { font-family: 'amiri'; font-weight: bold; src: url('{{ $fontBold }}') format('truetype'); }
    * { font-family: 'amiri', sans-serif; }
    @page { margin: 28px 34px; }
    body { color: #1f2937; font-size: 13px; line-height: 1.9; }
    .sheet { border: 2px solid #0f3d2e; padding: 0; }
    .topbar { background: #0f3d2e; color: #d4af37; padding: 10px 18px; }
    .brand { font-size: 20px; font-weight: bold; }
    .brand-sub { color: #e6e9ef; font-size: 10px; }
    .ref { color: #d4af37; font-size: 11px; }
    .title { text-align: center; font-size: 19px; font-weight: bold; color: #0f3d2e; padding: 14px 0 4px; }
    .title-sub { text-align: center; color: #6b7280; font-size: 11px; padding-bottom: 10px; }
    .pad { padding: 6px 22px 14px; }
    .sec-h { background: #f0f4f1; border-right: 4px solid #d4af37; color: #0f3d2e;
             font-weight: bold; padding: 6px 10px; margin: 12px 0 6px; font-size: 13px; }
    table.kv { width: 100%; border-collapse: collapse; }
    table.kv td { padding: 5px 8px; border: 1px solid #e5e7eb; vertical-align: top; }
    table.kv td.k { background: #fafaf8; color: #6b7280; width: 28%; font-size: 11px; }
    table.kv td.v { font-weight: bold; color: #111827; }
    .terms { padding: 2px 10px; }
    .terms p { margin: 4px 0; }
    .amount-box { background: #0f3d2e; color: #fff; text-align: center; padding: 12px; margin-top: 8px; }
    .amount-box .a { color: #d4af37; font-size: 22px; font-weight: bold; }
    table.sign { width: 100%; margin-top: 26px; }
    table.sign td { width: 50%; text-align: center; padding-top: 8px; }
    .sign-line { border-top: 1px dashed #9ca3af; margin: 30px 18px 4px; }
    .foot { text-align: center; color: #9ca3af; font-size: 9px; padding: 10px; border-top: 1px solid #eee; margin-top: 10px; }
    .stamp { color: #0f3d2e; border: 2px solid #0f3d2e; border-radius: 50%; display: inline-block;
             padding: 6px 10px; font-size: 9px; font-weight: bold; }
</style>
</head>
<body>
<div class="sheet">
    <table style="width:100%; border-collapse:collapse;">
        <tr class="topbar">
            <td style="text-align:right;"><div class="brand">{{ $ar('عقّار سوريا') }}</div>
                <div class="brand-sub">{{ $ar('المنصة الذكية لإدارة العقارات') }} · AQAR SYRIA</div></td>
            <td style="text-align:left;"><div class="ref">{{ $contract->reference }}</div>
                <div class="brand-sub">{{ $contract->created_at?->format('Y-m-d') }}</div></td>
        </tr>
    </table>

    <div class="title">{{ $ar($isSale ? 'عقد بيع عقار' : 'عقد إيجار عقار') }}</div>
    <div class="title-sub">{{ $ar('محرّر بموجب المنصة ووفق الأصول القانونية المعتمدة') }}</div>

    <div class="pad">
        <p>{{ $ar('إنه في يوم ' . ($contract->start_date?->format('Y-m-d')) . ' تم الاتفاق بين الطرفين الموقّعين أدناه على ما يلي:') }}</p>

        <div class="sec-h">{{ $ar('الطرف الأول (المكتب العقاري الوسيط / المالك)') }}</div>
        <table class="kv">
            <tr><td class="k">{{ $ar('اسم المكتب') }}</td><td class="v">{{ $ar($office->name) }}</td>
                <td class="k">{{ $ar('العنوان') }}</td><td class="v">{{ $ar($office->address) }}</td></tr>
            <tr><td class="k">{{ $ar('المنطقة') }}</td><td class="v">{{ $ar(($district?->name ?? '-') . ' - ' . ($city?->name ?? '')) }}</td>
                <td class="k">{{ $ar('البريد') }}</td><td class="v" style="direction:ltr;">{{ $office->provider?->email ?? '-' }}</td></tr>
        </table>

        <div class="sec-h">{{ $ar($isSale ? 'الطرف الثاني (المشتري)' : 'الطرف الثاني (المستأجر)') }}</div>
        <table class="kv">
            <tr><td class="k">{{ $ar('الاسم الكامل') }}</td><td class="v">{{ $ar($contract->party_name) }}</td>
                <td class="k">{{ $ar('الرقم الوطني') }}</td><td class="v" style="direction:ltr;">{{ $contract->party_national_id ?: '—' }}</td></tr>
            <tr><td class="k">{{ $ar('رقم الهاتف') }}</td><td class="v" style="direction:ltr;">{{ $contract->party_phone ?: '—' }}</td>
                <td class="k">{{ $ar('الصفة') }}</td><td class="v">{{ $ar($isSale ? 'مشترٍ' : 'مستأجر') }}</td></tr>
        </table>

        <div class="sec-h">{{ $ar('بيانات العقار محل العقد') }}</div>
        <table class="kv">
            <tr><td class="k">{{ $ar('نوع العقد') }}</td><td class="v">{{ $ar($isSale ? 'بيع' : 'إيجار') }}</td>
                <td class="k">{{ $ar('المساحة') }}</td><td class="v">{{ $ar(rtrim(rtrim((string)$house->area,'0'),'.') . ' م²') }}</td></tr>
            <tr><td class="k">{{ $ar('عدد الغرف') }}</td><td class="v">{{ $ar($house->rooms . ' غرف') }}</td>
                <td class="k">{{ $ar('الطابق') }}</td><td class="v">{{ $ar('الطابق ' . $house->floor) }}</td></tr>
            <tr><td class="k">{{ $ar('الاتجاه') }}</td><td class="v">{{ $ar($house->direction) }}</td>
                <td class="k">{{ $ar('الموقع') }}</td><td class="v">{{ $ar(($district?->name ?? '') . ' - ' . ($city?->name ?? '')) }}</td></tr>
        </table>

        <div class="amount-box">
            <div>{{ $ar($isSale ? 'قيمة البيع الإجمالية' : 'بدل الإيجار') }}</div>
            <div class="a">{{ $ar(number_format((float)$contract->amount) . ' $') }}</div>
            @if(! $isSale)<div style="font-size:11px;">{{ $ar('دورة السداد: ' . ($cycles[$contract->payment_cycle] ?? '')) }}</div>@endif
        </div>

        <div class="sec-h">{{ $ar('الشروط والأحكام') }}</div>
        <div class="terms">
            <p>{{ $ar('1. أقرّ الطرف الثاني بمعاينته للعقار المعاينة النافية للجهالة وقبوله بحالته الراهنة.') }}</p>
            @if($isSale)
                <p>{{ $ar('2. انتقلت ملكية العقار إلى الطرف الثاني فور توقيع هذا العقد وسداد كامل القيمة.') }}</p>
                <p>{{ $ar('3. يتعهّد الطرف الأول بخلوّ العقار من أي حقوق أو رهون أو إشغالات للغير.') }}</p>
            @else
                <p>{{ $ar('2. مدة العقد سنة ميلادية تبدأ من ' . $contract->start_date?->format('Y-m-d') . ' وتنتهي في ' . $contract->end_date?->format('Y-m-d') . '.') }}</p>
                <p>{{ $ar('3. يلتزم المستأجر بسداد بدل الإيجار في موعده، وأول استحقاق بتاريخ ' . $contract->due_date?->format('Y-m-d') . '.') }}</p>
                <p>{{ $ar('4. لا يحق للمستأجر التنازل عن العقد أو تأجير العقار من الباطن إلا بموافقة خطية.') }}</p>
            @endif
            <p>{{ $ar('• تُسوّى أي نزاعات ودياً، وإلا تكون المحاكم المختصة في الجمهورية العربية السورية مرجعاً للفصل.') }}</p>
            @if($contract->notes)<p>{{ $ar('• ملاحظات: ' . $contract->notes) }}</p>@endif
        </div>

        <table class="sign">
            <tr><td><b>{{ $ar('الطرف الأول') }}</b><div class="sign-line"></div>{{ $ar($office->name) }}</td>
                <td><b>{{ $ar('الطرف الثاني') }}</b><div class="sign-line"></div>{{ $ar($contract->party_name) }}</td></tr>
            <tr><td colspan="2" style="text-align:center; padding-top:16px;">
                <span class="stamp">{{ $ar('ختم وتوثيق المنصة') }}</span></td></tr>
        </table>
    </div>

    <div class="foot">{{ $ar('وثيقة مولّدة إلكترونياً عبر منصة عقّار سوريا') }} — {{ $contract->reference }} — {{ now()->format('Y-m-d H:i') }}</div>
</div>
</body>
</html>
