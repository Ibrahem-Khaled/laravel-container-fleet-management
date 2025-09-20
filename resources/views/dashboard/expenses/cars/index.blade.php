@extends('layouts.app')

@section('title', 'مصروفات السيارات')

@section('styles')
<style>
    .rtl-support {
        direction: rtl;
        text-align: right;
    }
    .table th, .table td {
        text-align: center;
    }
    .text-right {
        text-align: right !important;
    }
    .text-left {
        text-align: left !important;
    }
    .badge {
        font-size: 0.8em;
    }
    .card-header h6 {
        font-weight: bold;
    }
    .car-card {
        transition: transform 0.2s;
    }
    .car-card:hover {
        transform: translateY(-2px);
    }
</style>
@endsection

@section('content')
<div class="row">
    <!-- فلتر السنة والشهر -->
    <div class="col-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">فلتر الفترة الزمنية</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('expenses.cars.index') }}" class="row">
                    <div class="col-md-4">
                        <label for="year" class="form-label">السنة</label>
                        <select name="year" id="year" class="form-control">
                            @for($i = now()->year - 2; $i <= now()->year + 1; $i++)
                                <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="month" class="form-label">الشهر</label>
                        <select name="month" id="month" class="form-control">
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                                    {{ \App\Http\Controllers\system\TaxController::getArabicMonthName($i) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> عرض البيانات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- الإحصائيات العامة -->
    <div class="col-12 mb-4">
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    إجمالي السيارات
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $stats['total_cars'] }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-car fa-2x text-gray-300"></i>
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
                                    سيارات بها مصروفات
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $stats['cars_with_expenses'] }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                    إجمالي المصروفات
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($stats['total_expenses'], 2) }} ر.س
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
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
                                    متوسط المصروفات للسيارة
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($stats['average_per_car'], 2) }} ر.س
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول السيارات -->
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    مصروفات السيارات - {{ \App\Http\Controllers\system\TaxController::getArabicMonthName($month) }} {{ $year }}
                </h6>
            </div>
            <div class="card-body">
                @if($cars->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>اسم السيارة</th>
                                    <th>رقم اللوحة</th>
                                    <th>الموديل</th>
                                    <th>عدد المعاملات</th>
                                    <th>إجمالي المصروفات</th>
                                    <th>طرق الدفع</th>
                                    <th>الفترة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cars as $index => $carData)
                                    <tr class="{{ $carData['month_stats']['total_transactions'] > 0 ? 'table-success' : '' }}">
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td class="text-right font-weight-bold">{{ $carData['car']->type_car }}</td>
                                        <td class="text-center">{{ $carData['car']->number ?? '-' }}</td>
                                        <td class="text-center">{{ $carData['car']->model_car ?? '-' }}</td>
                                        <td class="text-center">
                                            @if($carData['month_stats']['total_transactions'] > 0)
                                                <span class="badge badge-primary">{{ $carData['month_stats']['total_transactions'] }}</span>
                                            @else
                                                <span class="text-muted">0</span>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            @if($carData['month_stats']['total_transactions'] > 0)
                                                <span class="font-weight-bold text-success">
                                                    {{ number_format($carData['month_stats']['total_amount'], 2) }} ر.س
                                                </span>
                                            @else
                                                <span class="text-muted">لا توجد مصروفات</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($carData['month_stats']['total_transactions'] > 0)
                                                @foreach($carData['month_stats']['methods'] as $method)
                                                    <span class="badge {{ $method == 'cash' ? 'badge-warning' : 'badge-info' }}">
                                                        {{ $method == 'cash' ? 'نقدي' : 'بنكي' }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($carData['month_stats']['total_transactions'] > 0)
                                                <small class="text-muted">
                                                    {{ $carData['month_stats']['first_date']->format('m/d') }} - {{ $carData['month_stats']['last_date']->format('m/d') }}
                                                </small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($carData['month_stats']['total_transactions'] > 0)
                                                <a href="{{ route('expenses.cars.show', ['car' => $carData['car']->id, 'year' => $year, 'month' => $month]) }}"
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> التفاصيل
                                                </a>
                                            @else
                                                <span class="text-muted">
                                                    <i class="fas fa-car"></i>
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="thead-light">
                                <tr>
                                    <th colspan="5" class="text-right">الإجمالي:</th>
                                    <th class="text-center">
                                        <span class="badge badge-primary">{{ $stats['total_transactions'] }}</span>
                                    </th>
                                    <th class="text-right font-weight-bold text-success">
                                        {{ number_format($stats['total_expenses'], 2) }} ر.س
                                    </th>
                                    <th colspan="3"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-car fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد سيارات في النظام</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- ملخص الفترة -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">ملخص الفترة</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold text-primary">تفاصيل الفترة:</h6>
                        <ul class="list-unstyled">
                            <li><strong>الشهر:</strong> {{ \App\Http\Controllers\system\TaxController::getArabicMonthName($month) }} {{ $year }}</li>
                            <li><strong>من:</strong> {{ $monthStart->format('Y-m-d') }}</li>
                            <li><strong>إلى:</strong> {{ $monthEnd->format('Y-m-d') }}</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold text-success">ملخص المصروفات:</h6>
                        <ul class="list-unstyled">
                            <li><strong>إجمالي السيارات:</strong> {{ $stats['total_cars'] }}</li>
                            <li><strong>السيارات بها مصروفات:</strong> {{ $stats['cars_with_expenses'] }}</li>
                            <li><strong>السيارات بدون مصروفات:</strong> {{ $stats['cars_without_expenses'] }}</li>
                            <li><strong>إجمالي المصروفات:</strong> {{ number_format($stats['total_expenses'], 2) }} ر.س</li>
                            <li><strong>متوسط المصروفات للسيارة:</strong> {{ number_format($stats['average_per_car'], 2) }} ر.س</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // تحديث الصفحة عند تغيير السنة أو الشهر
    document.getElementById('year').addEventListener('change', function() {
        this.form.submit();
    });

    document.getElementById('month').addEventListener('change', function() {
        this.form.submit();
    });
</script>
@endsection
