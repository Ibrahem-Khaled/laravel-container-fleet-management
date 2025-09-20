<?php

namespace App\Support;

class ContainerStatusHelper
{
    /**
     * ترجمة حالات الحاويات إلى العربية
     */
    public static function getStatusTranslations(): array
    {
        return [
            'wait' => 'في الانتظار',
            'transport' => 'في النقل',
            'done' => 'مكتملة',
            'rent' => 'مؤجرة',
            'storage' => 'في التخزين'
        ];
    }

    /**
     * ترجمة حالة واحدة إلى العربية
     */
    public static function translateStatus(string $status): string
    {
        $translations = self::getStatusTranslations();
        return $translations[$status] ?? $status;
    }

    /**
     * الحصول على جميع الحالات المتاحة
     */
    public static function getAvailableStatuses(): array
    {
        return array_keys(self::getStatusTranslations());
    }

    /**
     * الحصول على الحالات مع ترجماتها
     */
    public static function getStatusesWithTranslations(): array
    {
        $statuses = [];
        foreach (self::getStatusTranslations() as $key => $value) {
            $statuses[] = [
                'key' => $key,
                'value' => $value,
                'label' => $value
            ];
        }
        return $statuses;
    }

    /**
     * الحصول على لون الحالة (للاستخدام في الواجهة)
     */
    public static function getStatusColor(string $status): string
    {
        $colors = [
            'wait' => 'warning',
            'transport' => 'info',
            'done' => 'success',
            'rent' => 'primary',
            'storage' => 'secondary'
        ];
        return $colors[$status] ?? 'secondary';
    }

    /**
     * الحصول على أيقونة الحالة
     */
    public static function getStatusIcon(string $status): string
    {
        $icons = [
            'wait' => 'fas fa-clock',
            'transport' => 'fas fa-truck',
            'done' => 'fas fa-check-circle',
            'rent' => 'fas fa-hand-holding-usd',
            'storage' => 'fas fa-warehouse'
        ];
        return $icons[$status] ?? 'fas fa-question-circle';
    }

    /**
     * الحصول على وصف مفصل للحالة
     */
    public static function getStatusDescription(string $status): string
    {
        $descriptions = [
            'wait' => 'الحاوية في انتظار المعالجة أو النقل',
            'transport' => 'الحاوية في طريقها إلى الوجهة المحددة',
            'done' => 'تم تسليم الحاوية بنجاح',
            'rent' => 'الحاوية مؤجرة لعميل معين',
            'storage' => 'الحاوية مخزنة في المستودع'
        ];
        return $descriptions[$status] ?? 'حالة غير محددة';
    }

    /**
     * التحقق من صحة الحالة
     */
    public static function isValidStatus(string $status): bool
    {
        return in_array($status, self::getAvailableStatuses());
    }

    /**
     * الحصول على الحالات التالية الممكنة للحالة الحالية
     */
    public static function getNextPossibleStatuses(string $currentStatus): array
    {
        $workflow = [
            'wait' => ['transport', 'storage', 'rent'],
            'transport' => ['done', 'storage'],
            'done' => ['rent', 'storage'],
            'rent' => ['wait', 'storage'],
            'storage' => ['wait', 'transport', 'rent']
        ];
        return $workflow[$currentStatus] ?? [];
    }

    /**
     * التحقق من إمكانية الانتقال من حالة إلى أخرى
     */
    public static function canTransitionTo(string $fromStatus, string $toStatus): bool
    {
        return in_array($toStatus, self::getNextPossibleStatuses($fromStatus));
    }
}
