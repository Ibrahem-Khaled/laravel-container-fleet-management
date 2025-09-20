@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        {{-- العنوان ومسار التنقل --}}
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">إيرادات مكاتب التخليص</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item active" aria-current="page">مكاتب التخليص</li>
                </ol>
            </nav>
        </div>

        @include('components.alerts')

        {{-- كروت الإحصاءات --}}
        <div class="row mb-4">
            <x-stats-card icon="fas fa-warehouse" title="عدد المكاتب" :value="$stats['offices_count']" color="primary" />
            <x-stats-card icon="fas fa-boxes" title="إجمالي الحاويات" :value="$stats['total_containers']" color="success" />
            <x-stats-card icon="fas fa-dollar-sign" title="إجمالي قيمة الحاويات" :value="number_format($stats['total_prices'] ?? 0, 2)" color="info" />
            <x-stats-card icon="fas fa-hand-holding-usd" title="إجمالي الوارد" :value="number_format($stats['total_income'] ?? 0, 2)" color="warning" />
            <x-stats-card icon="fas fa-file-invoice-dollar" title="إجمالي المطلوب" :value="number_format($stats['total_required'] ?? 0, 2)" color="danger" />
        </div>

        {{-- فلاتر وجدول المكاتب --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-wrap align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">قائمة المكاتب</h6>
                <form action="{{ route('revenues.clearance.index') }}" method="GET" class="form-inline">
                    <div class="form-group mr-2 mb-2">
                        <label for="start_date" class="sr-only">من</label>
                        <input type="date" id="start_date" name="start_date" class="form-control"
                            value="{{ $startDate }}">
                    </div>
                    <div class="form-group mr-2 mb-2">
                        <label for="end_date" class="sr-only">إلى</label>
                        <input type="date" id="end_date" name="end_date" class="form-control"
                            value="{{ $endDate }}">
                    </div>
                    <button type="submit" class="btn btn-primary mb-2">
                        <i class="fas fa-filter fa-sm"></i> تطبيق
                    </button>
                    <a href="{{ route('revenues.clearance.index') }}" class="btn btn-secondary mb-2 ml-2">
                        <i class="fas fa-undo fa-sm"></i> مسح
                    </a>
                </form>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th>المكتب</th>
                                <th>عدد الحاويات</th>
                                <th>أحدث تاريخ</th>
                                <th>إجمالي القيمة</th>
                                <th>الوارد</th>
                                <th>المطلوب</th>
                                <th>الضرائب</th>
                                <th>الإجمالي مع الضرائب</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($offices as $office)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $office->avatar ? asset('storage/' . $office->avatar) : asset('img/default-avatar.png') }}"
                                                alt="{{ $office->name }}" class="rounded-circle mr-2" width="40"
                                                height="40">
                                            <span>{{ $office->name ?? '—' }}</span>
                                        </div>
                                    </td>
                                    <td><span
                                            class="badge badge-pill badge-info px-2 py-1">{{ $office->containers_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        @if ($office->last_container_date)
                                            {{ \Illuminate\Support\Carbon::parse($office->last_container_date)->format('Y-m-d') }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ number_format($office->containers_total_price ?? 0, 2) }}</td>
                                    <td class="text-success font-weight-bold">
                                        {{ number_format($office->income_sum ?? 0, 2) }}</td>
                                    <td
                                        class="font-weight-bold {{ ($office->required_amount ?? 0) > 0 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format($office->required_amount ?? 0, 2) }}
                                    </td>
                                    <td>
                                        <span class="text-warning font-weight-bold">
                                            {{ number_format($office->tax_calculation['tax_amount'], 2) }}
                                        </span>
                                        <br>
                                        <small class="text-muted">
                                            <span class="badge badge-{{ $office->tax_calculation['tax_enabled'] ? 'success' : 'danger' }}">
                                                {{ $office->tax_calculation['tax_enabled'] ? 'مفعلة' : 'معطلة' }}
                                            </span>
                                            ({{ $office->tax_calculation['tax_rate'] }}%)
                                        </small>
                                    </td>
                                    <td class="text-success font-weight-bold">
                                        {{ number_format($office->tax_calculation['total_amount'], 2) }}
                                    </td>
                                    <td class="text-center">
                                        {{-- أزرار التقارير واضحة --}}
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('revenues.clearance.monthly', $office->id) }}"
                                               class="btn btn-sm btn-primary"
                                               title="الكشف الشهري">
                                                <i class="fas fa-calendar-alt"></i> شهري
                                            </a>
                                            <a href="{{ route('revenues.clearance.yearly', $office->id) }}"
                                               class="btn btn-sm btn-success"
                                               title="الكشف السنوي">
                                                <i class="fas fa-calendar"></i> سنوي
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">لا توجد بيانات تطابق الفلترة الحالية.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- الترقيم --}}
                <div class="d-flex justify-content-center mt-3">
                    {{ $offices->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // تفعيل tooltips
        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@endpush
