<?php

namespace App\Support;

use Illuminate\Support\Facades\Blade;

class BladeTranslationDirectives
{
    /**
     * تسجيل التوجيهات المخصصة
     */
    public static function register()
    {
        // توجيه لترجمة حالة الحاوية
        Blade::directive('containerStatus', function ($expression) {
            return "<?php echo App\Support\TranslationHelper::containerStatus($expression); ?>";
        });

        // توجيه لترجمة حجم الحاوية
        Blade::directive('containerSize', function ($expression) {
            return "<?php echo App\Support\TranslationHelper::containerSize($expression); ?>";
        });

        // توجيه لترجمة نوع المعاملة
        Blade::directive('transactionType', function ($expression) {
            return "<?php echo App\Support\TranslationHelper::transactionType($expression); ?>";
        });

        // توجيه لترجمة طريقة الدفع
        Blade::directive('paymentMethod', function ($expression) {
            return "<?php echo App\Support\TranslationHelper::paymentMethod($expression); ?>";
        });

        // توجيه لترجمة دور المستخدم
        Blade::directive('userRole', function ($expression) {
            return "<?php echo App\Support\TranslationHelper::userRole($expression); ?>";
        });

        // توجيه لترجمة نوع السيارة
        Blade::directive('carType', function ($expression) {
            return "<?php echo App\Support\TranslationHelper::carType($expression); ?>";
        });

        // توجيه لترجمة حالة السيارة
        Blade::directive('carStatus', function ($expression) {
            return "<?php echo App\Support\TranslationHelper::carStatus($expression); ?>";
        });

        // توجيه لترجمة حالة الحساب
        Blade::directive('accountStatus', function ($expression) {
            return "<?php echo App\Support\TranslationHelper::accountStatus($expression); ?>";
        });

        // توجيه لترجمة نوع التربات
        Blade::directive('tipType', function ($expression) {
            return "<?php echo App\Support\TranslationHelper::tipType($expression); ?>";
        });

        // توجيه لترجمة حالة الضرائب
        Blade::directive('taxStatus', function ($expression) {
            return "<?php echo App\Support\TranslationHelper::taxStatus($expression); ?>";
        });

        // توجيه لترجمة حالة الشريك
        Blade::directive('partnerStatus', function ($expression) {
            return "<?php echo App\Support\TranslationHelper::partnerStatus($expression); ?>";
        });

        // توجيه لترجمة حالة الأرباح
        Blade::directive('profitStatus', function ($expression) {
            return "<?php echo App\Support\TranslationHelper::profitStatus($expression); ?>";
        });

        // توجيه لعرض حالة الحاوية مع اللون
        Blade::directive('containerStatusBadge', function ($expression) {
            return "<?php
                \$statusData = App\Support\TranslationHelper::containerStatusWithColor($expression);
                echo '<span class=\"badge badge-' . \$statusData['color'] . '\"><i class=\"' . \$statusData['icon'] . '\"></i> ' . \$statusData['text'] . '</span>';
            ?>";
        });

        // توجيه لعرض حالة الحاوية مع الوصف
        Blade::directive('containerStatusTooltip', function ($expression) {
            return "<?php
                \$statusData = App\Support\TranslationHelper::containerStatusWithDescription($expression);
                echo '<span class=\"badge badge-' . \$statusData['color'] . '\" title=\"' . \$statusData['description'] . '\"><i class=\"' . \$statusData['icon'] . '\"></i> ' . \$statusData['text'] . '</span>';
            ?>";
        });
    }
}
