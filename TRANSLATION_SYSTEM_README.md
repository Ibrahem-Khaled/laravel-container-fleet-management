# نظام الترجمة للحاويات والنظام

## نظرة عامة

تم إنشاء نظام ترجمة شامل لترجمة حالات الحاويات وجميع العناصر الأخرى في النظام إلى العربية. النظام مصمم بطريقة احترافية وقابلة للتوسع.

## الملفات المضافة

### 1. ContainerStatusHelper.php
```php
// الموقع: app/Support/ContainerStatusHelper.php
// الوظيفة: ترجمة حالات الحاويات وإدارة الألوان والأيقونات
```

### 2. TranslationHelper.php
```php
// الموقع: app/Support/TranslationHelper.php
// الوظيفة: ترجمة جميع العناصر في النظام
```

### 3. BladeTranslationDirectives.php
```php
// الموقع: app/Support/BladeTranslationDirectives.php
// الوظيفة: توجيهات Blade المخصصة للترجمة
```

### 4. translation-helper.js
```javascript
// الموقع: public/assets/js/translation-helper.js
// الوظيفة: ترجمة في JavaScript
```

## حالات الحاويات المدعومة

| الحالة (EN) | الترجمة (AR) | اللون | الأيقونة | الوصف |
|-------------|--------------|-------|----------|--------|
| `wait` | في الانتظار | warning | fas fa-clock | الحاوية في انتظار المعالجة أو النقل |
| `transport` | في النقل | info | fas fa-truck | الحاوية في طريقها إلى الوجهة المحددة |
| `done` | مكتملة | success | fas fa-check-circle | تم تسليم الحاوية بنجاح |
| `rent` | مؤجرة | primary | fas fa-hand-holding-usd | الحاوية مؤجرة لعميل معين |
| `storage` | في التخزين | secondary | fas fa-warehouse | الحاوية مخزنة في المستودع |

## كيفية الاستخدام

### 1. في Blade Templates

#### استخدام التوجيهات المخصصة:
```blade
<!-- ترجمة حالة الحاوية -->
@containerStatus($container->status)

<!-- ترجمة حجم الحاوية -->
@containerSize($container->size)

<!-- ترجمة نوع المعاملة -->
@transactionType($transaction->type)

<!-- ترجمة طريقة الدفع -->
@paymentMethod($transaction->method)

<!-- عرض حالة الحاوية مع Badge -->
@containerStatusBadge($container->status)

<!-- عرض حالة الحاوية مع Tooltip -->
@containerStatusTooltip($container->status)
```

#### استخدام Helper Classes:
```blade
<!-- في Controller أو Blade -->
{{ \App\Support\TranslationHelper::containerStatus($container->status) }}
{{ \App\Support\ContainerStatusHelper::getStatusColor($container->status) }}
{{ \App\Support\ContainerStatusHelper::getStatusIcon($container->status) }}
```

### 2. في Controllers

```php
use App\Support\TranslationHelper;
use App\Support\ContainerStatusHelper;

class ContainerController extends Controller
{
    public function index()
    {
        $containers = Container::all();
        
        // ترجمة الحالات
        $containers->each(function ($container) {
            $container->status_ar = TranslationHelper::containerStatus($container->status);
            $container->status_color = ContainerStatusHelper::getStatusColor($container->status);
            $container->status_icon = ContainerStatusHelper::getStatusIcon($container->status);
        });
        
        return view('containers.index', compact('containers'));
    }
}
```

### 3. في JavaScript

```javascript
// ترجمة حالة الحاوية
const statusAr = TranslationHelper.containerStatus('wait'); // "في الانتظار"

// ترجمة حجم الحاوية
const sizeAr = TranslationHelper.containerSize('20'); // "20 قدم"

// إنشاء Badge للحالة
const badge = TranslationHelper.createStatusBadge('transport'); 
// '<span class="badge badge-info"><i class="fas fa-truck"></i> في النقل</span>'

// ترجمة تلقائية لجدول
TranslationHelper.translateTableStatuses('.containers-table', 0);
```

### 4. في Forms

```blade
<select name="status" class="form-control">
    @foreach(\App\Support\ContainerStatusHelper::getStatusesWithTranslations() as $status)
        <option value="{{ $status['key'] }}">{{ $status['value'] }}</option>
    @endforeach
</select>
```

## إضافة ترجمات جديدة

### 1. إضافة حالة جديدة للحاويات:

```php
// في ContainerStatusHelper.php
public static function getStatusTranslations(): array
{
    return [
        'wait' => 'في الانتظار',
        'transport' => 'في النقل',
        'done' => 'مكتملة',
        'rent' => 'مؤجرة',
        'storage' => 'في التخزين',
        'new_status' => 'حالة جديدة', // إضافة هنا
    ];
}
```

### 2. إضافة ترجمة جديدة:

```php
// في TranslationHelper.php
public static function newElementType(string $type): string
{
    $types = [
        'type1' => 'النوع الأول',
        'type2' => 'النوع الثاني',
    ];
    return $types[$type] ?? $type;
}
```

### 3. إضافة توجيه Blade جديد:

```php
// في BladeTranslationDirectives.php
Blade::directive('newElementType', function ($expression) {
    return "<?php echo App\Support\TranslationHelper::newElementType($expression); ?>";
});
```

## الميزات المتقدمة

### 1. سير العمل (Workflow):
```php
// الحصول على الحالات التالية الممكنة
$nextStatuses = ContainerStatusHelper::getNextPossibleStatuses('wait');
// ['transport', 'storage', 'rent']

// التحقق من إمكانية الانتقال
$canTransition = ContainerStatusHelper::canTransitionTo('wait', 'transport');
// true
```

### 2. التحقق من صحة الحالة:
```php
$isValid = ContainerStatusHelper::isValidStatus('wait'); // true
$isValid = ContainerStatusHelper::isValidStatus('invalid'); // false
```

### 3. الحصول على وصف مفصل:
```php
$description = ContainerStatusHelper::getStatusDescription('wait');
// "الحاوية في انتظار المعالجة أو النقل"
```

## التخصيص

### 1. تغيير الألوان:
```php
// في ContainerStatusHelper.php
public static function getStatusColor(string $status): string
{
    $colors = [
        'wait' => 'warning',    // تغيير هنا
        'transport' => 'info',
        'done' => 'success',
        'rent' => 'primary',
        'storage' => 'secondary'
    ];
    return $colors[$status] ?? 'secondary';
}
```

### 2. تغيير الأيقونات:
```php
// في ContainerStatusHelper.php
public static function getStatusIcon(string $status): string
{
    $icons = [
        'wait' => 'fas fa-clock',        // تغيير هنا
        'transport' => 'fas fa-truck',
        'done' => 'fas fa-check-circle',
        'rent' => 'fas fa-hand-holding-usd',
        'storage' => 'fas fa-warehouse'
    ];
    return $icons[$status] ?? 'fas fa-question-circle';
}
```

## أمثلة عملية

### 1. جدول الحاويات:
```blade
<table class="table">
    <thead>
        <tr>
            <th>رقم الحاوية</th>
            <th>الحالة</th>
            <th>الحجم</th>
        </tr>
    </thead>
    <tbody>
        @foreach($containers as $container)
            <tr>
                <td>{{ $container->number }}</td>
                <td>@containerStatusBadge($container->status)</td>
                <td>@containerSize($container->size)</td>
            </tr>
        @endforeach
    </tbody>
</table>
```

### 2. إحصائيات الحاويات:
```blade
<div class="row">
    @foreach(['wait', 'transport', 'done', 'rent', 'storage'] as $status)
        <div class="col-md-2">
            <div class="card">
                <div class="card-body text-center">
                    <i class="{{ \App\Support\ContainerStatusHelper::getStatusIcon($status) }} fa-2x text-{{ \App\Support\ContainerStatusHelper::getStatusColor($status) }}"></i>
                    <h5>@containerStatus($status)</h5>
                    <p>{{ $stats[$status] ?? 0 }}</p>
                </div>
            </div>
        </div>
    @endforeach
</div>
```

### 3. فلتر الحاويات:
```blade
<select name="status" class="form-control">
    <option value="">جميع الحالات</option>
    @foreach(\App\Support\ContainerStatusHelper::getStatusesWithTranslations() as $status)
        <option value="{{ $status['key'] }}" {{ request('status') == $status['key'] ? 'selected' : '' }}>
            {{ $status['value'] }}
        </option>
    @endforeach
</select>
```

## الدعم والمساعدة

لأي استفسارات أو مشاكل في استخدام نظام الترجمة، يرجى مراجعة هذا الدليل أو التواصل مع فريق التطوير.

---

**ملاحظة**: هذا النظام مصمم ليكون مرناً وقابلاً للتوسع. يمكن إضافة ترجمات جديدة بسهولة دون الحاجة لتعديل الكود الأساسي.
