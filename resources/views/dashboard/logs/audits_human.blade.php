@extends('layouts.app')

@section('content')
    <div class="container-fluid" dir="rtl">
        <div class="d-flex align-items-center justify-content-between mt-4 mb-4">
            <div class="d-flex align-items-center">
                <div class="bg-primary rounded-circle p-3 text-white ml-3">
                    <i class="fas fa-history fa-lg"></i>
                </div>
                <div>
                    <h1 class="h3 mb-0 font-weight-bold">سجلّ التدقيق</h1>
                    <p class="text-muted mb-0 small">مراقبة وتتبع جميع العمليات في النظام</p>
                </div>
            </div>
            <a href="{{ route('logs.audits') }}" class="btn btn-outline-primary btn-sm shadow-sm">
                <i class="fas fa-redo-alt ml-1"></i>
                إعادة الضبط
            </a>
        </div>

        {{-- فلاتر محسنة --}}
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-header bg-gradient-primary text-white">
                <h6 class="mb-0 font-weight-bold">
                    <i class="fas fa-filter ml-2"></i>
                    فلاتر البحث
                </h6>
            </div>
            <div class="card-body">
                <form method="GET">
                    <div class="row">
                        <div class="col-lg-2 col-md-4 mb-3">
                            <label class="small font-weight-bold text-primary">ID المستخدم</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0">
                                        <i class="fas fa-user text-muted"></i>
                                    </span>
                                </div>
                                <input type="text" name="user_id" value="{{ request('user_id') }}"
                                       class="form-control border-left-0" placeholder="مثال: 12">
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-4 mb-3">
                            <label class="small font-weight-bold text-primary">نوع الموديل</label>
                            <select name="auditable_type" class="form-control custom-select">
                                <option value="">— جميع الأنواع —</option>
                                @php
                                    $subjectLabels = config('log_labels.subjects');
                                @endphp
                                @foreach ($auditableTypes as $t)
                                    <option value="{{ $t }}" @selected(request('auditable_type') === $t)>
                                        {{ $subjectLabels[$t] ?? class_basename($t) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-4 mb-3">
                            <label class="small font-weight-bold text-primary">الحدث</label>
                            <select name="event" class="form-control custom-select">
                                <option value="">— جميع الأحداث —</option>
                                @foreach ($events as $e)
                                    <option value="{{ $e }}" @selected(request('event') === $e)>
                                        {{ match ($e) {'created' => 'إنشاء','updated' => 'تحديث','deleted' => 'حذف','restored' => 'استعادة',default => $e} }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-4 mb-3">
                            <label class="small font-weight-bold text-primary">من</label>
                            <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                        </div>

                        <div class="col-lg-2 col-md-4 mb-3">
                            <label class="small font-weight-bold text-primary">إلى</label>
                            <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                        </div>

                        <div class="col-lg-1 col-md-4 mb-3 d-flex align-items-end">
                            <button class="btn btn-gradient-primary btn-block shadow-sm font-weight-bold">
                                <i class="fas fa-search ml-1"></i>
                                تطبيق
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- الجدول المحسن --}}
        <div class="card shadow border-0">
            <div class="card-header bg-white border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 font-weight-bold text-dark">
                        <i class="fas fa-table ml-2 text-primary"></i>
                        سجلات التدقيق
                    </h6>
                    <span class="badge badge-primary badge-pill">{{ $audits->total() ?? count($audits) }} سجل</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="thead-dark">
                            <tr class="text-right">
                                <th class="font-weight-bold" style="min-width: 160px;">
                                    <i class="fas fa-clock ml-2"></i>التاريخ والوقت
                                </th>
                                <th class="font-weight-bold" style="min-width: 140px;">
                                    <i class="fas fa-user ml-2"></i>المستخدم
                                </th>
                                <th class="font-weight-bold" style="min-width: 100px;">
                                    <i class="fas fa-bolt ml-2"></i>الحدث
                                </th>
                                <th class="font-weight-bold" style="min-width: 200px;">
                                    <i class="fas fa-database ml-2"></i>الموديل
                                </th>
                                <th class="font-weight-bold text-center" style="width: 100px;">
                                    <i class="fas fa-code-branch ml-2"></i>التغييرات
                                </th>
                                <th class="font-weight-bold" style="min-width: 280px;">
                                    <i class="fas fa-info-circle ml-2"></i>معلومات إضافية
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($audits as $au)
                                @php
                                    $event = $au->event;
                                    $badge = [
                                        'created' => ['success', 'fas fa-plus'],
                                        'updated' => ['info', 'fas fa-edit'],
                                        'deleted' => ['danger', 'fas fa-trash'],
                                        'restored' => ['warning', 'fas fa-undo'],
                                    ][$event] ?? ['secondary', 'fas fa-question'];

                                    $diffId = 'diff-' . $au->id;
                                    $metaId = 'meta-' . $au->id;
                                    $old = $au->old_values ?? [];
                                    $new = $au->new_values ?? [];
                                    $ip = $au->ip_address ?? null;
                                    $ua = $au->user_agent ?? null;
                                @endphp

                                <tr class="audit-row">
                                    <td class="align-middle">
                                        <div class="font-weight-bold text-dark">{{ $au->created_at->format('Y-m-d') }}</div>
                                        <div class="small text-primary font-weight-bold">{{ $au->created_at->format('H:i:s') }}</div>
                                        <div class="small text-muted">{{ $au->created_at->diffForHumans() }}</div>
                                    </td>

                                    <td class="align-middle">
                                        @if($au->user)
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary rounded-circle p-2 text-white ml-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-user" style="font-size: 12px;"></i>
                                                </div>
                                                <div>
                                                    <div class="font-weight-bold text-dark">{{ $au->user->name }}</div>
                                                    <div class="small text-muted">#{{ $au->user_id }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">
                                                <i class="fas fa-user-slash ml-1"></i>
                                                {{ $au->user_id ? 'مستخدم محذوف #' . $au->user_id : 'غير معروف' }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="align-middle">
                                        <span class="badge badge-{{ $badge[0] }} badge-lg px-3 py-2">
                                            <i class="{{ $badge[1] }} ml-1"></i>
                                            {{ match ($event) {'created' => 'إنشاء','updated' => 'تحديث','deleted' => 'حذف','restored' => 'استعادة',default => $event} }}
                                        </span>
                                    </td>

                                    <td class="align-middle">
                                        <div class="font-weight-bold text-dark">
                                            {{ \App\Support\LogHumanizer::subjectLabel($au->auditable_type, $au->auditable_id) }}
                                        </div>
                                        <div class="small text-muted">{{ class_basename($au->auditable_type) }}</div>
                                    </td>

                                    <td class="align-middle text-center">
                                        @php $hasDiff = !empty($old) || !empty($new); @endphp
                                        @if ($hasDiff)
                                            <button class="btn btn-outline-primary btn-sm shadow-sm" type="button"
                                                    data-toggle="collapse" data-target="#{{ $diffId }}"
                                                    aria-expanded="false" aria-controls="{{ $diffId }}">
                                                <i class="fas fa-eye ml-1"></i>
                                                عرض التغييرات
                                            </button>
                                        @else
                                            <span class="text-muted">
                                                <i class="fas fa-minus"></i>
                                            </span>
                                        @endif
                                    </td>

                                    <td class="align-middle">
                                        <button class="btn btn-outline-info btn-sm shadow-sm" type="button"
                                                data-toggle="collapse" data-target="#{{ $metaId }}"
                                                aria-expanded="false" aria-controls="{{ $metaId }}">
                                            <i class="fas fa-info-circle ml-1"></i>
                                            عرض التفاصيل
                                        </button>
                                    </td>
                                </tr>

                                {{-- صف التغييرات --}}
                                @if ($hasDiff)
                                    <tr class="collapse-row">
                                        <td colspan="6" class="p-0 border-top-0">
                                            <div id="{{ $diffId }}" class="collapse">
                                                <div class="bg-light border-top p-4">
                                                    <h6 class="font-weight-bold text-dark mb-3">
                                                        <i class="fas fa-code-branch text-primary ml-2"></i>
                                                        مقارنة التغييرات
                                                    </h6>
                                                    @php
                                                        $translatedOld = \App\Support\LogHumanizer::translateValues($old);
                                                        $translatedNew = \App\Support\LogHumanizer::translateValues($new);
                                                    @endphp
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="card border-danger">
                                                                <div class="card-header bg-danger text-white">
                                                                    <h6 class="mb-0 font-weight-bold">
                                                                        <i class="fas fa-minus-circle ml-2"></i>
                                                                        القيم القديمة
                                                                    </h6>
                                                                </div>
                                                                <div class="card-body p-0">
                                                                    <div class="json-container">
                                                                        @if(empty($translatedOld))
                                                                            <div class="p-3 text-muted text-center">
                                                                                <i class="fas fa-info-circle ml-2"></i>
                                                                                لا توجد قيم قديمة
                                                                            </div>
                                                                        @else
                                                                            @foreach($translatedOld as $key => $value)
                                                                                <div class="json-item border-bottom">
                                                                                    <div class="json-key">{{ $key }}</div>
                                                                                    <div class="json-value old-value">{{ is_array($value) || is_object($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value }}</div>
                                                                                </div>
                                                                            @endforeach
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="card border-success">
                                                                <div class="card-header bg-success text-white">
                                                                    <h6 class="mb-0 font-weight-bold">
                                                                        <i class="fas fa-plus-circle ml-2"></i>
                                                                        القيم الجديدة
                                                                    </h6>
                                                                </div>
                                                                <div class="card-body p-0">
                                                                    <div class="json-container">
                                                                        @if(empty($translatedNew))
                                                                            <div class="p-3 text-muted text-center">
                                                                                <i class="fas fa-info-circle ml-2"></i>
                                                                                لا توجد قيم جديدة
                                                                            </div>
                                                                        @else
                                                                            @foreach($translatedNew as $key => $value)
                                                                                <div class="json-item border-bottom">
                                                                                    <div class="json-key">{{ $key }}</div>
                                                                                    <div class="json-value new-value">{{ is_array($value) || is_object($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value }}</div>
                                                                                </div>
                                                                            @endforeach
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif

                                {{-- صف المعلومات الإضافية --}}
                                <tr class="collapse-row">
                                    <td colspan="6" class="p-0 border-top-0">
                                        <div id="{{ $metaId }}" class="collapse">
                                            <div class="bg-info-light border-top p-4">
                                                <h6 class="font-weight-bold text-dark mb-3">
                                                    <i class="fas fa-info-circle text-info ml-2"></i>
                                                    معلومات تفصيلية
                                                </h6>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="card border-info">
                                                            <div class="card-header bg-info text-white">
                                                                <h6 class="mb-0 font-weight-bold">
                                                                    <i class="fas fa-network-wired ml-2"></i>
                                                                    معلومات الشبكة
                                                                </h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="mb-2">
                                                                    <strong class="text-info">عنوان IP:</strong>
                                                                    <div class="mt-1">
                                                                        @if($ip)
                                                                            <span class="badge badge-outline-info">{{ $ip }}</span>
                                                                        @else
                                                                            <span class="text-muted">غير متوفر</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="card border-info">
                                                            <div class="card-header bg-info text-white">
                                                                <h6 class="mb-0 font-weight-bold">
                                                                    <i class="fas fa-desktop ml-2"></i>
                                                                    معلومات المتصفح
                                                                </h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="mb-2">
                                                                    <strong class="text-info">User Agent:</strong>
                                                                    <div class="mt-1">
                                                                        @if($ua)
                                                                            <div class="user-agent-info p-2 bg-light border rounded">
                                                                                <div class="font-monospace small" style="word-break: break-all; direction: ltr; text-align: left;">
                                                                                    {{ $ua }}
                                                                                </div>
                                                                                @php
                                                                                    // استخراج معلومات المتصفح بشكل مبسط
                                                                                    $browser = 'غير معروف';
                                                                                    $os = 'غير معروف';

                                                                                    if (strpos($ua, 'Chrome') !== false) $browser = 'Google Chrome';
                                                                                    elseif (strpos($ua, 'Firefox') !== false) $browser = 'Mozilla Firefox';
                                                                                    elseif (strpos($ua, 'Safari') !== false) $browser = 'Safari';
                                                                                    elseif (strpos($ua, 'Edge') !== false) $browser = 'Microsoft Edge';

                                                                                    if (strpos($ua, 'Windows') !== false) $os = 'Windows';
                                                                                    elseif (strpos($ua, 'Mac') !== false) $os = 'macOS';
                                                                                    elseif (strpos($ua, 'Linux') !== false) $os = 'Linux';
                                                                                    elseif (strpos($ua, 'Android') !== false) $os = 'Android';
                                                                                    elseif (strpos($ua, 'iPhone') !== false || strpos($ua, 'iPad') !== false) $os = 'iOS';
                                                                                @endphp
                                                                                <div class="mt-2 d-flex flex-wrap">
                                                                                    <span class="badge badge-primary ml-2 mb-1">
                                                                                        <i class="fas fa-globe ml-1"></i>{{ $browser }}
                                                                                    </span>
                                                                                    <span class="badge badge-success mb-1">
                                                                                        <i class="fas fa-laptop ml-1"></i>{{ $os }}
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        @else
                                                                            <span class="text-muted">غير متوفر</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- معلومات إضافية من التدقيق --}}
                                                <div class="row mt-3">
                                                    <div class="col-md-12">
                                                        <div class="card border-secondary">
                                                            <div class="card-header bg-secondary text-white">
                                                                <h6 class="mb-0 font-weight-bold">
                                                                    <i class="fas fa-cog ml-2"></i>
                                                                    معلومات النظام
                                                                </h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-3">
                                                                        <strong class="text-secondary">معرف التدقيق:</strong>
                                                                        <div class="mt-1">
                                                                            <span class="badge badge-outline-secondary">#{{ $au->id }}</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <strong class="text-secondary">نوع الكائن:</strong>
                                                                        <div class="mt-1">
                                                                            <span class="badge badge-outline-secondary">{{ class_basename($au->auditable_type) }}</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <strong class="text-secondary">معرف الكائن:</strong>
                                                                        <div class="mt-1">
                                                                            <span class="badge badge-outline-secondary">#{{ $au->auditable_id }}</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <strong class="text-secondary">الطابع الزمني:</strong>
                                                                        <div class="mt-1 font-monospace small">
                                                                            {{ $au->created_at->format('Y-m-d H:i:s') }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td class="text-center py-5" colspan="6">
                                        <div class="text-muted">
                                            <i class="fas fa-search fa-3x mb-3"></i>
                                            <h5>لا توجد سجلات</h5>
                                            <p>لم يتم العثور على أي سجلات تدقيق تطابق المعايير المحددة</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- ترقيم محسن --}}
                @if (method_exists($audits, 'hasPages') && $audits->hasPages())
                    <div class="card-footer bg-white border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                عرض {{ $audits->firstItem() }} إلى {{ $audits->lastItem() }} من أصل {{ $audits->total() }} سجل
                            </div>
                            <div>
                                {{ $audits->onEachSide(1)->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- الأنماط المخصصة --}}
    <style>
        .json-container {
            max-height: 300px;
            overflow-y: auto;
            background: #fff;
        }

        .json-item {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e9ecef;
        }

        .json-item:last-child {
            border-bottom: none;
        }

        .json-key {
            font-weight: bold;
            color: #495057;
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
            direction: rtl;
            text-align: right;
        }

        .json-value {
            font-family: 'Courier New', monospace;
            font-size: 0.8rem;
            padding: 0.5rem;
            border-radius: 0.25rem;
            word-break: break-word;
            direction: ltr;
            text-align: left;
        }

        .old-value {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .new-value {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .user-agent-info {
            background: #f8f9fa !important;
        }

        .collapse-row {
            background: transparent !important;
        }

        .custom-select:focus,
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.875rem;
            }

            .json-container {
                max-height: 200px;
            }

            .audit-row:hover {
                transform: none;
            }
        }
    </style>
@endsection
