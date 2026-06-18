<?php

/**
 * Subscription tiers sold to real-estate offices.
 * Prices are in USD/month (the de-facto pricing currency for SY real estate SaaS).
 */
return [

    'basic' => [
        'key'           => 'basic',
        'name'          => 'الأساسية',
        'name_en'       => 'Basic',
        'price'         => 29,
        'duration_days' => 30,
        'listing_limit' => 15,
        'tagline'       => 'لبداية رقمية لمكتبك',
        'color'         => '#64748b',
        'features'      => [
            'حتى 15 عقار منشور',
            'إدارة العقارات والصور',
            'استقبال طلبات المشترين',
            'عقود إيجار/بيع PDF',
        ],
    ],

    'pro' => [
        'key'           => 'pro',
        'name'          => 'الاحترافية',
        'name_en'       => 'Pro',
        'price'         => 79,
        'duration_days' => 30,
        'listing_limit' => 60,
        'tagline'       => 'الأكثر طلباً للمكاتب النشطة',
        'color'         => '#0ea5e9',
        'popular'       => true,
        'features'      => [
            'حتى 60 عقار منشور',
            'خوارزمية المطابقة الذكية',
            'لوحة تحليل السوق وأسعار المتر',
            'تنبيهات استحقاق العقود',
            'إشعارات فورية للمشترين المطابقين',
        ],
    ],

    'elite' => [
        'key'           => 'elite',
        'name'          => 'النخبة',
        'name_en'       => 'Elite',
        'price'         => 149,
        'duration_days' => 30,
        'listing_limit' => null, // unlimited
        'tagline'       => 'هيمنة كاملة على السوق',
        'color'         => '#d4af37',
        'features'      => [
            'عقارات غير محدودة',
            'كل مزايا الباقة الاحترافية',
            'أولوية الظهور في نتائج البحث',
            'تحليلات متقدمة واتجاهات الأسعار',
            'شارة "مكتب موثّق" الذهبية',
            'دعم مخصص على مدار الساعة',
        ],
    ],

];
