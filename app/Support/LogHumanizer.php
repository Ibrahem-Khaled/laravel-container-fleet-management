<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Arr;

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
        $translated = [];

        foreach ($values as $key => $value) {
            // ترجمة اسم الحقل (المفتاح)
            $translatedKey = $fieldMap[$key] ?? $key;

            // تنسيق القيمة (مثل تحويل true إلى "نعم"، وتنسيق الأرقام)
            // نستخدم نفس دالة fmt التي أنشأناها سابقاً
            $formattedValue = self::fmt($key, $value);

            $translated[$translatedKey] = $formattedValue;
        }

        return $translated;
    }
}
