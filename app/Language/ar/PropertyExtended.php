<?php

// app/Language/ar/PropertyExtended.php
// Labels arabes pour Property Extended

return [
    // Sections
    'rooms' => 'الغرف والأبعاد',
    'options' => 'التجهيزات والخيارات',
    'location' => 'الموقع والقرب',
    'financial' => 'المالية والاستثمار',
    'costs' => 'التكاليف المقدرة',
    'orientation' => 'التوجيه والتعرض',
    'media' => 'وسائط متقدمة',
    
    // Room Types
    'room_types' => [
        'living_room' => 'صالون',
        'bedroom' => 'غرفة نوم',
        'kitchen' => 'مطبخ',
        'bathroom' => 'حمام',
        'toilet' => 'مرحاض',
        'office' => 'مكتب',
        'storage' => 'مخزن',
        'garage' => 'كراج',
        'balcony' => 'شرفة',
        'terrace' => 'تراس',
        'other' => 'أخرى'
    ],
    
    // Location Scores
    'location_scores' => [
        'proximity_to_schools' => 'القرب من المدارس',
        'proximity_to_transport' => 'القرب من النقل',
        'proximity_to_shopping' => 'القرب من المتاجر',
        'proximity_to_parks' => 'القرب من الحدائق',
        'proximity_to_healthcare' => 'القرب من الخدمات الصحية',
        'proximity_to_restaurants' => 'القرب من المطاعم',
        'proximity_to_entertainment' => 'القرب من الترفيه',
        'area_safety_score' => 'أمان المنطقة',
        'noise_level_score' => 'مستوى الضوضاء',
        'area_cleanliness_score' => 'نظافة المنطقة'
    ],
    
    // Financial
    'financial_labels' => [
        'gross_yield' => 'العائد الإجمالي',
        'net_yield' => 'العائد الصافي',
        'cap_rate' => 'معدل الرسملة',
        'roi_annual' => 'عائد الاستثمار السنوي',
        'payback_period' => 'فترة الاسترداد',
        'price_per_sqm' => 'السعر للمتر المربع',
        'cash_on_cash_return' => 'عائد النقد على النقد'
    ],
    
    // Orientation
    'orientations' => [
        'north' => 'شمال',
        'south' => 'جنوب',
        'east' => 'شرق',
        'west' => 'غرب',
        'northeast' => 'شمال شرق',
        'northwest' => 'شمال غرب',
        'southeast' => 'جنوب شرق',
        'southwest' => 'جنوب غرب'
    ],
    
    'sun_exposure' => [
        'full_sun' => 'شمس كاملة',
        'partial' => 'جزئي',
        'shaded' => 'مظلل'
    ],
    
    // Media Types
    'media_types' => [
        'floor_plan' => 'مخطط الطابق',
        '3d_render' => 'عرض ثلاثي الأبعاد',
        'video_tour' => 'جولة فيديو',
        'drone_photo' => 'صورة طائرة',
        'technical_plan' => 'خطة فنية',
        'document' => 'وثيقة',
        'other' => 'أخرى'
    ],
    
    // Messages
    'messages' => [
        'saved_successfully' => 'تم الحفظ بنجاح',
        'error_saving' => 'خطأ أثناء الحفظ',
        'deleted_successfully' => 'تم الحذف بنجاح',
        'permission_denied' => 'تم رفض الإذن',
        'invalid_data' => 'بيانات غير صالحة',
        'feature_disabled' => 'هذه الميزة معطلة لهذا النوع من العقارات'
    ],
    
    // Actions
    'actions' => [
        'add' => 'إضافة',
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'save' => 'حفظ',
        'cancel' => 'إلغاء',
        'upload' => 'تحميل',
        'download' => 'تنزيل',
        'calculate' => 'حساب',
        'analyze' => 'تحليل',
        'compare' => 'مقارنة',
        'export' => 'تصدير'
    ]
];
