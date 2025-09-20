<?php

namespace App\Support;

class TranslationHelper
{
    /**
     * ترجمة حالات الحاويات
     */
    public static function containerStatus(string $status): string
    {
        return ContainerStatusHelper::translateStatus($status);
    }

    /**
     * ترجمة أحجام الحاويات
     */
    public static function containerSize(string $size): string
    {
        $sizes = [
            '20' => '20 قدم',
            '40' => '40 قدم',
            'box' => 'صندوق'
        ];
        return $sizes[$size] ?? $size;
    }

    /**
     * ترجمة أنواع المعاملات
     */
    public static function transactionType(string $type): string
    {
        $types = [
            'income' => 'وارد',
            'expense' => 'منصرف'
        ];
        return $types[$type] ?? $type;
    }

    /**
     * ترجمة طرق الدفع
     */
    public static function paymentMethod(string $method): string
    {
        $methods = [
            'cash' => 'نقدي',
            'bank' => 'بنكي',
            'transfer' => 'تحويل'
        ];
        return $methods[$method] ?? $method;
    }

    /**
     * ترجمة أنواع المستخدمين
     */
    public static function userRole(string $role): string
    {
        $roles = [
            'admin' => 'مدير',
            'super_admin' => 'مدير عام',
            'client' => 'عميل',
            'clearance_office' => 'مكتب تخليص',
            'partner' => 'شريك',
            'employee' => 'موظف',
            'driver' => 'سائق'
        ];
        return $roles[$role] ?? $role;
    }

    /**
     * ترجمة أنواع السيارات
     */
    public static function carType(string $type): string
    {
        $types = [
            'truck' => 'شاحنة',
            'van' => 'فان',
            'pickup' => 'بيك أب',
            'trailer' => 'مقطورة'
        ];
        return $types[$type] ?? $type;
    }

    /**
     * ترجمة حالة السيارات
     */
    public static function carStatus(string $status): string
    {
        $statuses = [
            'active' => 'نشطة',
            'inactive' => 'غير نشطة',
            'maintenance' => 'في الصيانة',
            'rented' => 'مؤجرة'
        ];
        return $statuses[$status] ?? $status;
    }

    /**
     * ترجمة حالة الحسابات
     */
    public static function accountStatus(string $status): string
    {
        $statuses = [
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'suspended' => 'معلق',
            'closed' => 'مغلق'
        ];
        return $statuses[$status] ?? $status;
    }

    /**
     * ترجمة أنواع التربات
     */
    public static function tipType(string $type): string
    {
        $types = [
            'tip' => 'إكرامية',
            'bonus' => 'مكافأة',
            'reward' => 'مكافأة',
            'gift' => 'هدية'
        ];
        return $types[$type] ?? $type;
    }

    /**
     * ترجمة حالة الضرائب
     */
    public static function taxStatus(string $status): string
    {
        $statuses = [
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'exempt' => 'معفى'
        ];
        return $statuses[$status] ?? $status;
    }

    /**
     * ترجمة حالة الشركاء
     */
    public static function partnerStatus(string $status): string
    {
        $statuses = [
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'pending' => 'في الانتظار',
            'suspended' => 'معلق'
        ];
        return $statuses[$status] ?? $status;
    }

    /**
     * ترجمة حالة الأرباح
     */
    public static function profitStatus(string $status): string
    {
        $statuses = [
            'pending' => 'في الانتظار',
            'calculated' => 'محسوبة',
            'distributed' => 'موزعة',
            'paid' => 'مدفوعة'
        ];
        return $statuses[$status] ?? $status;
    }

    /**
     * ترجمة حالة الحاويات مع اللون
     */
    public static function containerStatusWithColor(string $status): array
    {
        return [
            'text' => self::containerStatus($status),
            'color' => ContainerStatusHelper::getStatusColor($status),
            'icon' => ContainerStatusHelper::getStatusIcon($status)
        ];
    }

    /**
     * ترجمة حالة الحاويات مع الوصف
     */
    public static function containerStatusWithDescription(string $status): array
    {
        return [
            'text' => self::containerStatus($status),
            'description' => ContainerStatusHelper::getStatusDescription($status),
            'color' => ContainerStatusHelper::getStatusColor($status),
            'icon' => ContainerStatusHelper::getStatusIcon($status)
        ];
    }
}
