@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- العنوان ومسار التنقل --}}
    <div class="row">
        <div class="col-12">
            {{-- تنبيه توضيحي --}}
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>ملاحظة مهمة:</strong> الضريبة تُحسب على إجمالي أسعار الحاويات وأوامر النقل، وليس على المبلغ المستحق فقط.
                <br><strong>الصافي الإجمالي:</strong> الوارد من اليومية - (إجمالي الحاويات + أوامر النقل + الضرائب)
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-file-invoice-dollar text-primary mr-2"></i>
                        تقرير المستحقات المالية للعملاء
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('home') }}">لوحة التحكم</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="#">التقارير المالية</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">تقرير المستحقات</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-success" onclick="exportReport('excel')">
                        <i class="fas fa-file-excel mr-1"></i> تصدير Excel
                    </button>
                    <button class="btn btn-danger" onclick="exportReport('pdf')">
                        <i class="fas fa-file-pdf mr-1"></i> تصدير PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    @include('components.alerts')

    {{-- فلاتر --}}
    <div class="card shadow mb-4 border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="card-body text-white">
            <form method="GET" action="{{ route('reports.client-financial') }}" class="row align-items-end">
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">
                        <i class="fas fa-calendar-alt mr-1"></i> السنة
                    </label>
                    <select name="year" class="form-control">
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">
                        <i class="fas fa-calendar mr-1"></i> الشهر
                    </label>
                    <select name="month" class="form-control">
                        @foreach($months as $m => $monthName)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ $monthName }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-light btn-block">
                        <i class="fas fa-search mr-1"></i> عرض التقرير
                    </button>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('reports.client-financial') }}" class="btn btn-outline-light btn-block">
                        <i class="fas fa-refresh mr-1"></i> إعادة تعيين
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- إحصائيات سريعة --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                إجمالي المستحقات
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($totals['total_due_amount'], 2) }} ر.س
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                شامل الضرائب
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($totals['total_amount_with_tax'], 2) }} ر.س
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calculator fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-purple shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-purple text-uppercase mb-1">
                                الصافي الإجمالي
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($totals['total_net_amount_after_tax'], 2) }} ر.س
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                عدد المكاتب
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $totals['offices_count'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                إجمالي الضرائب
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($totals['total_tax_amount'], 2) }} ر.س
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- الجدول الرئيسي --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table mr-2"></i>
                تفاصيل المستحقات - {{ $months[$month] }} {{ $year }}
            </h6>
            <div class="d-flex gap-2">
                <span class="badge badge-success">{{ $totals['offices_with_tax'] }} مكاتب مفعلة الضرائب</span>
                <span class="badge badge-secondary">{{ $totals['offices_without_tax'] }} مكاتب غير مفعلة الضرائب</span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="financialReportTable">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">اسم المكتب</th>
                            <th class="text-center">رقم التشغيل</th>
                            <th class="text-center">إجمالي الحاويات</th>
                            <th class="text-center">أوامر النقل</th>
                            <th class="text-center">إجمالي الإيرادات</th>
                            <th class="text-center">الوارد من اليومية</th>
                            <th class="text-center">المبلغ المستحق</th>
                            <th class="text-center">حالة الضريبة</th>
                            <th class="text-center">مبلغ الضريبة<br><small class="text-muted">(على إجمالي الإيرادات)</small></th>
                            <th class="text-center">المجموع شامل الضريبة</th>
                            <th class="text-center">الصافي الإجمالي<br><small class="text-muted">(الوارد - الإجمالي شامل الضريبة)</small></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($officeReports as $index => $officeReport)
                            @php
                                $report = $officeReport['report'];
                                $office = $officeReport['office'];
                            @endphp
                            <tr class="{{ $report['due_amount'] > 0 ? 'table-success' : ($report['due_amount'] < 0 ? 'table-danger' : 'table-light') }}">
                                <td class="text-center font-weight-bold">{{ $index + 1 }}</td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-building text-primary mr-2"></i>
                                        <span class="font-weight-bold">{{ $office->name }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($office->operational_number)
                                        <span class="badge badge-info">{{ $office->operational_number }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="font-weight-bold text-primary">
                                        {{ number_format($report['container_revenue'], 2) }} ر.س
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="font-weight-bold text-info">
                                        {{ number_format($report['transfer_orders_revenue'], 2) }} ر.س
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="font-weight-bold text-success">
                                        {{ number_format($report['total_revenue'], 2) }} ر.س
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="font-weight-bold text-warning">
                                        {{ number_format($report['daily_income'], 2) }} ر.س
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="font-weight-bold {{ $report['due_amount'] > 0 ? 'text-success' : ($report['due_amount'] < 0 ? 'text-danger' : 'text-muted') }}">
                                        {{ number_format($report['due_amount'], 2) }} ر.س
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($report['tax_enabled'])
                                        <span class="badge badge-success">
                                            <i class="fas fa-check mr-1"></i>
                                            مفعل ({{ $report['tax_rate'] }}%)
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-times mr-1"></i>
                                            غير مفعل
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($report['tax_enabled'])
                                        <span class="font-weight-bold text-danger">
                                            {{ number_format($report['tax_amount'], 2) }} ر.س
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="font-weight-bold text-dark">
                                        {{ number_format($report['amount_with_tax'], 2) }} ر.س
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="font-weight-bold text-purple">
                                        {{ number_format($report['net_amount_after_tax'], 2) }} ر.س
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <br>
                                    لا توجد بيانات متاحة للفترة المحددة
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="thead-light">
                        <tr>
                            <th colspan="3" class="text-center font-weight-bold">الإجمالي</th>
                            <th class="text-center font-weight-bold text-primary">
                                {{ number_format($totals['total_container_revenue'], 2) }} ر.س
                            </th>
                            <th class="text-center font-weight-bold text-info">
                                {{ number_format($totals['total_transfer_orders_revenue'], 2) }} ر.س
                            </th>
                            <th class="text-center font-weight-bold text-success">
                                {{ number_format($totals['total_revenue'], 2) }} ر.س
                            </th>
                            <th class="text-center font-weight-bold text-warning">
                                {{ number_format($totals['total_daily_income'], 2) }} ر.س
                            </th>
                            <th class="text-center font-weight-bold text-success">
                                {{ number_format($totals['total_due_amount'], 2) }} ر.س
                            </th>
                            <th class="text-center">-</th>
                            <th class="text-center font-weight-bold text-danger">
                                {{ number_format($totals['total_tax_amount'], 2) }} ر.س
                            </th>
                            <th class="text-center font-weight-bold text-dark">
                                {{ number_format($totals['total_amount_with_tax'], 2) }} ر.س
                            </th>
                            <th class="text-center font-weight-bold text-purple">
                                {{ number_format($totals['total_net_amount_after_tax'], 2) }} ر.س
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- ملخص إضافي --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie mr-2"></i>
                        توزيع الإيرادات
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar mr-2"></i>
                        أعلى المكاتب مستحقاً
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach(collect($officeReports)->sortByDesc('report.due_amount')->take(5) as $index => $officeReport)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-building text-primary mr-2"></i>
                                    {{ $officeReport['office']->name }}
                                </div>
                                <span class="badge badge-primary badge-pill">
                                    {{ number_format($officeReport['report']['due_amount'], 2) }} ر.س
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    // رسم بياني لتوزيع الإيرادات
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['إجمالي الحاويات', 'أوامر النقل', 'الوارد من اليومية'],
            datasets: [{
                data: [
                    {{ $totals['total_container_revenue'] }},
                    {{ $totals['total_transfer_orders_revenue'] }},
                    {{ $totals['total_daily_income'] }}
                ],
                backgroundColor: [
                    '#4e73df',
                    '#1cc88a',
                    '#f6c23e'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });

    // تصدير التقرير
    function exportReport(format) {
        const url = new URL('{{ route("reports.client-financial.export") }}');
        url.searchParams.append('year', '{{ $year }}');
        url.searchParams.append('month', '{{ $month }}');
        url.searchParams.append('format', format);

        window.open(url.toString(), '_blank');
    }

    // تحسين الجدول
    $(document).ready(function() {
        $('#financialReportTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Arabic.json"
            },
            "pageLength": 25,
            "order": [[ 7, "desc" ]], // ترتيب حسب المبلغ المستحق
            "columnDefs": [
                { "orderable": false, "targets": [0] }
            ]
        });
    });
</script>
@endpush

@push('styles')
<style>
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }
    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }
    .border-left-purple {
        border-left: 0.25rem solid #6f42c1 !important;
    }

    .table-success {
        background-color: rgba(28, 200, 138, 0.1) !important;
    }
    .table-danger {
        background-color: rgba(231, 74, 59, 0.1) !important;
    }

    .card {
        border-radius: 15px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
    }

    .btn {
        border-radius: 10px;
        font-weight: 600;
    }

    .badge {
        border-radius: 8px;
        font-weight: 600;
    }
</style>
@endpush
