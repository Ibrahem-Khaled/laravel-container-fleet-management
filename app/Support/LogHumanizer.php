<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache; // <-- أضف هذا
use Illuminate\Support\Facades\Http;


class LogHumanizer
{
    public static function subjectLabel(?string $class, ?int $id): string
    {
        $map = config('log_labels.subjects', []);
        $label = $map[$class] ?? class_basename((string)$class);
        return trim($label . ' #' . ($id ?? '—'));
    }

    public static function fieldLabel(string $key): string
    {
        $map = config('log_labels.fields', []);
        return $map[$key] ?? $key;
    }

    public static function fmt($key, $value)
    {
        if (is_bool($value)) return $value ? 'نعم' : 'لا';
        if (is_null($value)) return '—';

        if (preg_match('/_at$|date/i', $key) && $value) {
            try {
                return Carbon::parse($value)->diffForHumans();
            } catch (\Throwable $e) {
            }
        }

        if (is_numeric($value) && strlen((string)$value) >= 4) {
            return number_format((float)$value, 0, '.', ',');
        }

        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        return (string)$value;
    }

    public static function title(?string $who, string $event, string $subjectLabel): string
    {
        $who = $who ?: 'مستخدم النظام';
        $verb = match ($event) {
            'created'  => 'أنشأ',
            'deleted'  => 'حذف',
            'restored' => 'استعاد',
            default    => 'حدّث',
        };
        return "{$who} {$verb} {$subjectLabel}";
    }

    public static function lines(array $oldValues, array $newValues): array
    {
        $lines = [];
        $ignore = ['updated_at', 'remember_token', 'password'];

        foreach ($newValues as $key => $new) {
            if (in_array($key, $ignore, true)) continue;
            $old = Arr::get($oldValues, $key);
            if ($old === $new) continue;

            $label = self::fieldLabel($key);
            $lines[] = "غيَّر «{$label}» من «" . self::fmt($key, $old) . "» إلى «" . self::fmt($key, $new) . "»";
        }
        return $lines ?: ['— لا تغييرات مهمّة —'];
    }

    public static function translateValues(array $values): array
    {
        $fieldMap = config('log_labels.fields', []);
        $relationsMap = config('log_labels.relations', []);
        $translated = [];

        foreach ($values as $key => $value) {
            // تجاهل القيم الفارغة إلا إذا كانت صفر (للحالات مثل is_active=false)
            if (is_null($value)) continue;

            // تحقق إذا كان الحقل معرّفاً كعلاقة (مثل client_id)
            if (array_key_exists($key, $relationsMap)) {
                [$modelClass, $displayColumn] = $relationsMap[$key];

                // ابحث عن الموديل المرتبط في قاعدة البيانات
                $relatedModel = $modelClass::find($value);

                // إذا وجدنا الموديل، استخدم الاسم المحدد للعرض
                // إذا لم نجده (ربما حُذف)، نعرض الرقم التعريفي كخيار احتياطي
                $displayValue = $relatedModel ? $relatedModel->{$displayColumn} : "معرف غير موجود: {$value}";
            } else {
                // إذا لم يكن علاقة، قم بتنسيق القيمة بالطريقة العادية
                $displayValue = self::fmt($key, $value);
            }

            // ترجمة اسم الحقل (المفتاح)
            $translatedKey = $fieldMap[$key] ?? $key;

            $translated[$translatedKey] = $displayValue;
        }

        return $translated;
    }

    public static function getLocationFromIp(?string $ip): array
    {
        if (!$ip || in_array($ip, ['127.0.0.1', '::1'])) {
            return []; // لا تبحث عن IPs محلية
        }

        // استخدام الكاش لتجنب تكرار الطلبات لنفس الـ IP
        $cacheKey = "ip_location_{$ip}";

        return Cache::remember($cacheKey, now()->addMonth(), function () use ($ip) {
            try {
                // استخدام خدمة مجانية وموثوقة (لا تحتاج مفتاح API)
                $response = Http::get("http://ip-api.com/json/{$ip}?fields=status,message,country,countryCode,regionName,city,isp");

                if ($response->successful() && $response->json('status') === 'success') {
                    return $response->json();
                }
            } catch (\Throwable $e) {
                // في حال حدوث أي خطأ في الاتصال، لا تفعل شيئاً
            }
            return []; // إرجاع مصفوفة فارغة في حال الفشل
        });
    }
}
