@extends('layouts.app')

@push('styles')
    <style>
        /* لمسات تصميم */
        .gradient-card {
            background: linear-gradient(135deg, #eef2ff 0%, #e0f2fe 100%);
            border: 0;
            box-shadow: 0 10px 20px rgba(0, 0, 0, .06);
        }

        .stats-card .card-body {
            padding: 1rem 1.25rem;
        }

        .table thead.thead-light th {
            background: #f8fafc;
            border-bottom: 2px solid #e5e7eb;
            font-weight: 700;
        }

        .badge.rounded-pill {
            border-radius: 50rem;
            padding: .4rem .6rem;
        }

        .modal-header {
            background: #0ea5e9;
            color: #fff;
        }

        .modal-footer {
            background: #f9fafb;
        }

        .nav-tabs .nav-link.active {
            background: #0ea5e9;
            color: #fff;
            border-color: #0ea5e9 #0ea5e9 #fff;
        }

        .nav-tabs .nav-link {
            font-weight: 600;
        }

        .card.position-relative .stretched-link {
            z-index: 1;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">

        {{-- العنوان ومسار التنقل --}}
        <div class="row">
            <div class="col-12">
                <h1 class="h3 mb-0 text-gray-800">تدفق الحاويات حسب الحالة</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">لوحة التحكم</a></li>
                        <li class="breadcrumb-item active" aria-current="page">الحاويات</li>
                    </ol>
                </nav>
            </div>
        </div>

        @include('components.alerts')

        {{-- إحصائيات (كروت قابلة للضغط) --}}
        <div class="row mb-4">
            @php
                $badge = [
                    'wait' => 'secondary',
                    'transport' => 'info',
                    'done' => 'success',
                    'rent' => 'warning',
                    'storage' => 'dark',
                ];
            @endphp
            @foreach ($statuses as $s)
                <div class="col-xl-2 col-md-4 mb-3">
                    <div class="card gradient-card stats-card shadow h-100 position-relative">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <div
                                    class="text-xs font-weight-bold text-{{ $badge[$s] ?? 'primary' }} text-uppercase mb-1">
                                    {{ $statusMap[$s] }}
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats[$s] ?? 0 }}</div>
                            </div>
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                        <a class="stretched-link" href="{{ route('containers.flow.index', ['status' => $s]) }}"></a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">قائمة الحاويات</h6>
                {{-- لا يوجد زر "رحلة جديدة" --}}
            </div>

            <div class="card-body">

                {{-- تبويبات الحالات + عدد الحاويات --}}
                <ul class="nav nav-tabs mb-4">
                    @foreach ($statuses as $s)
                        <li class="nav-item">
                            <a class="nav-link {{ $selectedStatus === $s ? 'active' : '' }}"
                                href="{{ route('containers.flow.index', array_filter(['status' => $s, 'search' => $search])) }}">
                                {{ $statusMap[$s] }} ({{ $stats[$s] ?? 0 }})
                            </a>
                        </li>
                    @endforeach
                </ul>

                {{-- البحث --}}
                <form action="{{ route('containers.flow.index') }}" method="GET" class="mb-4">
                    <input type="hidden" name="status" value="{{ $selectedStatus }}">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control"
                            placeholder="ابحث برقم الحاوية أو الاتجاه أو البيان/العميل/المكتب..."
                            value="{{ $search }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i> بحث
                            </button>
                        </div>
                    </div>
                </form>

                {{-- الجدول --}}
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>رقم الحاوية</th>
                                <th>المقاس</th>
                                <th>الحالة</th>
                                <th>البيان الجمركي</th>
                                <th>العميل</th>
                                <th>مكتب التخليص</th>
                                <th>اتجاه/وصف</th>
                                <th>تاريخ النقل</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($containers as $container)
                                @php
                                    // خريطة الانتقالات المسموح بها
                                    $allowedTransitions = [
                                        'wait' => ['transport'],
                                        'transport' => ['storage', 'done'],
                                        'storage' => ['wait'],
                                        'done' => ['wait'],
                                        'rent' => ['wait'],
                                    ];
                                    $allowedForThis = $allowedTransitions[$container->status] ?? ['transport'];
                                @endphp
                                <tr>
                                    <td class="font-weight-bold">{{ $container->number }}</td>
                                    <td><span class="badge badge-info rounded-pill">{{ $container->size }}</span></td>
                                    <td>
                                        @php $cls = $badge[$container->status] ?? 'primary'; @endphp
                                        <span
                                            class="badge badge-{{ $cls }}">{{ $statusMap[$container->status] ?? $container->status }}</span>
                                    </td>

                                    {{-- البيان الجمركي --}}
                                    <td>
                                        @php $cd = $container->customs; @endphp
                                        @if ($cd)
                                            <span class="badge badge-light">{{ $cd->statement_number }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- العميل --}}
                                    <td>{{ $cd?->client?->name ?: '—' }}</td>

                                    {{-- مكتب التخليص --}}
                                    <td>{{ $cd?->clearanceOffice?->name ?: '—' }}</td>

                                    <td>{{ \Illuminate\Support\Str::limit($container->direction, 40) ?: '-' }}</td>
                                    <td>{{ optional($container->transfer_date)->format('Y-m-d H:i') }}</td>

                                    <td class="text-nowrap">
                                        <button class="btn btn-sm btn-outline-secondary" data-toggle="modal"
                                            data-target="#flowModal{{ $container->id }}"
                                            title="تغيير الحالة + إنشاء/حذف Tip حسب القاعدة">
                                            <i class="fas fa-random"></i>
                                        </button>

                                        {{-- مودال الحركة لهذه الحاوية --}}
                                        <div class="modal fade" id="flowModal{{ $container->id }}" tabindex="-1"
                                            role="dialog" aria-labelledby="flowModalLabel{{ $container->id }}"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <form action="{{ route('containers.flow.change', $container) }}"
                                                    method="POST" class="modal-content">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">تغيير حالة الحاوية:
                                                            {{ $container->number }}</h5>
                                                        <button type="button" class="close"
                                                            data-dismiss="modal"><span>&times;</span></button>
                                                    </div>

                                                    <div class="modal-body">
                                                        {{-- ملخص البيان (اختياري/جميل) --}}
                                                        @if ($cd)
                                                            <div class="alert alert-light border mb-3">
                                                                <div class="d-flex flex-wrap align-items-center">
                                                                    <i class="fas fa-file-invoice mr-2"></i>
                                                                    <strong class="mr-2">بيان:</strong>
                                                                    <span
                                                                        class="badge badge-secondary mr-3">{{ $cd->statement_number }}</span>

                                                                    <strong class="mr-2">العميل:</strong>
                                                                    <span
                                                                        class="mr-3">{{ $cd->client->name ?? '—' }}</span>

                                                                    <strong class="mr-2">مكتب التخليص:</strong>
                                                                    <span>{{ $cd->clearanceOffice->name ?? '—' }}</span>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        {{-- اختيار الحالة الجديدة (حسب المسموح فقط) --}}
                                                        <div class="form-row">
                                                            <div class="form-group col-md-4">
                                                                <label>الحالة الجديدة</label>
                                                                <select name="new_status"
                                                                    class="form-control new-status-select" required>
                                                                    @foreach ($allowedForThis as $s)
                                                                        <option value="{{ $s }}">
                                                                            {{ $statusMap[$s] }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        {{-- حقول Tip (مطلوبة في كل الحالات عدا "انتظار") --}}
                                                        <input type="hidden" name="container_id"
                                                            value="{{ $container->id }}">

                                                        <div class="form-row">
                                                            <div class="form-group col-md-6">
                                                                <label>السائق</label>
                                                                <select name="driver_id" class="form-control driver-select"
                                                                    required>
                                                                    <option value="">— اختر السائق —</option>
                                                                    @foreach ($drivers as $d)
                                                                        <option value="{{ $d->id }}">
                                                                            {{ $d->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="form-group col-md-6">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-between">
                                                                    <label class="mb-0">السيارة</label>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input show-all-cars"
                                                                            type="checkbox"
                                                                            id="show_all_{{ $container->id }}">
                                                                        <label class="form-check-label"
                                                                            for="show_all_{{ $container->id }}">
                                                                            عرض كل السيارات
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <select name="car_id" class="form-control car-select"
                                                                    required
                                                                    data-cars-url-base="{{ url('/system/containers/flow/drivers') }}">
                                                                    {{-- قائمة كل السيارات (نسخة أساس) --}}
                                                                    @foreach ($cars as $car)
                                                                        @php $carTxt = $car->number ?? ('Car #'.$car->id); @endphp
                                                                        <option value="{{ $car->id }}">
                                                                            {{ $carTxt }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="form-row">
                                                            <div class="form-group col-md-4">
                                                                <label>قيمة Tip (افتراضي 20)</label>
                                                                <input type="number" name="price" class="form-control"
                                                                    min="0" value="20">
                                                            </div>
                                                            <div class="form-group col-md-8 d-flex align-items-end">
                                                                <small class="text-muted">
                                                                    * الانتقالات المسموح بها: انتظار → نقل، نقل → (تخزين/تم
                                                                    التسليم)، تخزين/تم التسليم → انتظار. عند الرجوع إلى
                                                                    "انتظار" يتم حذف الـTip الأخير للحالة السابقة.
                                                                </small>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light"
                                                            data-dismiss="modal">إلغاء</button>
                                                        <button type="submit" class="btn btn-secondary">تنفيذ</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">لا توجد حاويات مطابقة</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- الترقيم --}}
                <div class="d-flex justify-content-center">
                    {{ $containers->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // حفظ نسخة الخيارات الكاملة لكل car-select عند التحميل
        $(function() {
            $('.car-select').each(function() {
                $(this).data('allOptions', $(this).html());
            });
        });

        // تفعيل التولتيب (لو موجود)
        $(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });

        // عند إظهار أي مودال: اضبط الحقول حسب الحالة الافتراضية
        $(document).on('shown.bs.modal', '.modal', function() {
            const $modal = $(this);
            toggleTipFields($modal);
        });

        // عند تغيير الحالة الجديدة في المودال
        $(document).on('change', '.new-status-select', function() {
            const $modal = $(this).closest('.modal-content');
            toggleTipFields($modal);
        });

        function toggleTipFields($modal) {
            const newStatus = $modal.find('.new-status-select').val();
            const needsTip = (newStatus !== 'wait');

            const $driver = $modal.find('.driver-select');
            const $carSel = $modal.find('.car-select');
            const $price = $modal.find('input[name="price"]');
            const $showAll = $modal.find('.show-all-cars');

            if (needsTip) {
                $driver.prop('required', true).prop('disabled', false);
                $carSel.prop('required', true).prop('disabled', false);
                $price.prop('disabled', false);
            } else {
                // رجوع لانتظار: لا Tip
                $driver.prop('required', false).prop('disabled', true).val('');
                $carSel.prop('required', false).prop('disabled', true).val('');
                $price.prop('disabled', true);
                $showAll.prop('checked', false);
            }
        }

        // عند اختيار السائق: اجلب سياراته (لو "عرض كل السيارات" غير مفعّل) وبشرط أن الحالة ليست "انتظار"
        $(document).on('change', '.driver-select', function() {
            const driverId = $(this).val();
            const $modal = $(this).closest('.modal-content');
            const $carSelect = $modal.find('.car-select');
            const showAll = $modal.find('.show-all-cars').is(':checked');
            const newStatus = $modal.find('.new-status-select').val();

            if (newStatus === 'wait') return;
            if (!driverId || showAll) return;

            $carSelect.prop('disabled', true);
            const baseUrl = $carSelect.data('cars-url-base'); // /containers/flow/drivers
            $.get(`${baseUrl}/${driverId}/cars`)
                .done(function(res) {
                    $carSelect.empty();
                    (res.cars || []).forEach(function(c) {
                        const label = c.number ?? ('Car #' + c.id);
                        $carSelect.append(`<option value="${c.id}">${label}</option>`);
                    });
                })
                .always(function() {
                    $carSelect.prop('disabled', false);
                });
        });

        // تبديل "عرض كل السيارات": يعيد كل الخيارات الأصلية فورًا أو يعيد الفلترة
        $(document).on('change', '.show-all-cars', function() {
            const $modal = $(this).closest('.modal-content');
            const $driverSel = $modal.find('.driver-select');
            const $carSelect = $modal.find('.car-select');
            const newStatus = $modal.find('.new-status-select').val();

            if (newStatus === 'wait') return; // لا Tip

            if ($(this).is(':checked')) {
                const allHtml = $carSelect.data('allOptions');
                if (allHtml) $carSelect.html(allHtml);
                $carSelect.prop('disabled', false);
            } else {
                $driverSel.trigger('change'); // أعد الفلترة حسب السائق المختار
            }
        });
    </script>
@endpush
