@extends('layouts.app')

@section('content')
    <div class="container-fluid" dir="rtl">
        {{-- عنوان الصفحة ومسار التنقل --}}
        <div class="row">
            <div class="col-12">
                <h1 class="h3 mb-0 text-gray-800">إدارة الزيت</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home') }}">لوحة التحكم</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">متابعة تغيير الزيت</li>
                    </ol>
                </nav>
            </div>
        </div>

        {{-- تنبيهات عامة --}}
        @include('components.alerts')

        {{-- كروت الإحصائيات --}}
        <div class="row mb-4">
            <x-stats-card icon="fas fa-car" title="إجمالي السيارات" :value="$totalCars" color="primary" />
            <x-stats-card icon="fas fa-check-circle" title="سليمة" :value="$healthyCnt" color="success" />
            <x-stats-card icon="fas fa-exclamation-triangle" title="تحتاج قريباً" :value="$dueCnt" color="warning" />
            <x-stats-card icon="fas fa-times-circle" title="متجاوزة" :value="$overdueCnt" color="danger" />
        </div>

        {{-- بطاقة القائمة --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">قائمة السيارات</h6>
                {{-- زر ثابت لشرح سريع/مساعدة (اختياري) --}}
                <button class="btn btn-outline-info" data-toggle="tooltip"
                    title="يتم الحساب تلقائياً من عداد السيارة وآخر تغيير">
                    <i class="fas fa-info-circle"></i>
                </button>
            </div>

            <div class="card-body">

                {{-- تبويبات الحالات --}}
                @php
                    $tabs = [
                        'all' => ['label' => 'الكل', 'count' => $totalCars],
                        'healthy' => ['label' => 'سليمة', 'count' => $healthyCnt],
                        'due' => ['label' => 'تحتاج قريباً', 'count' => $dueCnt],
                        'overdue' => ['label' => 'متجاوزة', 'count' => $overdueCnt],
                    ];
                @endphp

                <ul class="nav nav-tabs mb-4">
                    @foreach ($tabs as $key => $meta)
                        <li class="nav-item">
                            <a class="nav-link {{ ($selectedStatus ?? 'all') === $key ? 'active' : '' }}"
                                href="{{ route('car_change_oils.index', array_filter(['q' => $q ?? null, 'status' => $key !== 'all' ? $key : null])) }}">
                                {{ $meta['label'] }}
                                <span
                                    class="badge badge-{{ $key === 'healthy' ? 'success' : ($key === 'due' ? 'warning' : ($key === 'overdue' ? 'danger' : 'secondary')) }}">
                                    {{ $meta['count'] }}
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>

                {{-- نموذج البحث --}}
                <form action="{{ route('car_change_oils.index') }}" method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="hidden" name="status" value="{{ $selectedStatus ?? 'all' }}">
                        {{-- <input type="text" name="q" class="form-control"
                            placeholder="ابحث برقم السيارة / اسم السائق / النوع..." value="{{ $q }}"> --}}
                        <div class="input-group-append">
                            {{-- <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i> بحث
                            </button> --}}
                            @if (!empty($q))
                                <a href="{{ route('car_change_oils.index', ['status' => $selectedStatus !== 'all' ? $selectedStatus : null]) }}"
                                    class="btn btn-outline-secondary">
                                    مسح
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                {{-- الجدول --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>الرقم</th>
                                <th>السائق</th>
                                <th>النوع</th>
                                <th>العداد الحالي</th>
                                <th>آخر تغيير (تاريخ / قراءة)</th>
                                <th>دورة الزيت (كم)</th>
                                <th>الباقي (كم)</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cars as $car)
                                @php
                                    $remaining = $car->remaining_km; // قد يكون null عند عدم تثبيت تغيير بعد
                                    $statusText = 'سليم';
                                    $badgeClass = 'success';
                                    if (is_null($remaining)) {
                                        $statusText = 'لم يُثبّت تغيير';
                                        $badgeClass = 'secondary';
                                    } elseif ($remaining <= 0) {
                                        $statusText = 'متجاوز';
                                        $badgeClass = 'danger';
                                    } elseif ($remaining <= 1000) {
                                        $statusText = 'قريب';
                                        $badgeClass = 'warning';
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $car->number }}</td>
                                    <td>{{ $car->driver?->name ?? '-' }}</td>
                                    <td>{{ $car->type_car ?? '-' }}</td>
                                    <td>{{ number_format($car->odometer ?? 0) }}</td>
                                    <td>
                                        @if ($car->lastOilChange)
                                            {{ optional($car->lastOilChange->date)->format('Y-m-d') ?? '-' }}
                                            /
                                            {{ number_format($car->lastOilChange->km_before) }} كم
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ number_format($car->oil_change_number ?? 0) }}</td>
                                    <td>{{ $remaining !== null ? number_format($remaining) : '-' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $badgeClass }}">{{ $statusText }}</span>
                                    </td>
                                    <td class="text-center">
                                        {{-- عرض --}}
                                        <button type="button" class="btn btn-sm btn-circle btn-info" data-toggle="modal"
                                            data-target="#showCarModal{{ $car->id }}" data-toggle="tooltip"
                                            title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        {{-- إضافة قراءة عدّاد --}}
                                        <button type="button" class="btn btn-sm btn-circle btn-success" data-toggle="modal"
                                            data-target="#addReadingModal{{ $car->id }}" data-toggle="tooltip"
                                            title="إضافة قراءة عدّاد">
                                            <i class="fas fa-tachometer-alt"></i>
                                        </button>

                                        {{-- تثبيت تغيير زيت --}}
                                        <button type="button" class="btn btn-sm btn-circle btn-dark" data-toggle="modal"
                                            data-target="#addChangeModal{{ $car->id }}" data-toggle="tooltip"
                                            title="تثبيت تغيير زيت">
                                            <i class="fas fa-oil-can"></i>
                                        </button>

                                        {{-- تضمين المودالات لكل سيارة --}}
                                        @include('dashboard.car_change_oils.modals.show', ['car' => $car])
                                        @include('dashboard.car_change_oils.modals.add_reading', [
                                            'car' => $car,
                                        ])
                                        @include('dashboard.car_change_oils.modals.add_change', [
                                            'car' => $car,
                                        ])
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">لا توجد سيارات مطابقة.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- الترقيم --}}
                <div class="d-flex justify-content-center">
                    {{ $cars->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // تفعيل التولتيب
        $(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
