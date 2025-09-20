@extends('layouts.app')

@section('title', 'تفاصيل مصروفات السيارة')

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
    .back-link {
        color: #007bff;
        text-decoration: none;
    }
    .back-link:hover {
        color: #0056b3;
        text-decoration: underline;
    }
    .month-link {
        color: #28a745;
        text-decoration: none;
    }
    .month-link:hover {
        color: #1e7e34;
        text-decoration: underline;
    }
</style>
@endsection

@section('content')
<div class="row">
    <!-- زر العودة -->
    <div class="col-12 mb-3">
        <a href="{{ route('expenses.cars.index', ['year' => $year, 'month' => $month]) }}" class="back-link">
            <i class="fas fa-arrow-right"></i> العودة إلى مصروفات السيارات
        </a>
    </div>

    <!-- معلومات السيارة -->
    <div class="col-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-car"></i> تفاصيل مصروفات السيارة
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold text-primary">معلومات السيارة:</h6>
                        <ul class="list-unstyled">
                            <li><strong>اسم السيارة:</strong> {{ $car->name }}</li>
                            @if($car->plate_number)
                                <li><strong>رقم اللوحة:</strong> {{ $car->plate_number }}</li>
                            @endif
                            @if($car->model)
                                <li><strong>الموديل:</strong> {{ $car->model }}</li>
                            @endif
                            @if($car->year)
                                <li><strong>سنة الصنع:</strong> {{ $car->year }}</li>
                            @endif
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold text-success">إحصائيات الشهر:</h6>
                        <ul class="list-unstyled">
                            <li><strong>الشهر:</strong> {{ \App\Http\Controllers\system\TaxController::getArabicMonthName($month) }} {{ $year }}</li>
                            <li><strong>عدد المعاملات:</strong> {{ $monthStats['total_transactions'] }}</li>
                            <li><strong>إجمالي المصروفات:</strong> {{ number_format($monthStats['total_amount'], 2) }} ر.س</li>
                            <li><strong>إجمالي الضرائب:</strong> {{ number_format($monthStats['total_tax_amount'], 2) }} ر.س</li>
                            <li><strong>طرق الدفع:</strong>
                                @foreach($monthStats['methods'] as $method)
                                    <span class="badge {{ $method == 'cash' ? 'badge-warning' : 'badge-info' }}">
                                        {{ $method == 'cash' ? 'نقدي' : 'بنكي' }}
                                    </span>
                                @endforeach
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- الأشهر السابقة -->
    @if(count($previousMonths) > 0)
    <div class="col-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">
                    <i class="fas fa-history"></i> الأشهر السابقة
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($previousMonths as $prevMonth)
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('expenses.cars.show', ['car' => $car->id, 'year' => $prevMonth['year'], 'month' => $prevMonth['month']]) }}"
                               class="month-link">
                                <div class="card border-left-info">
                                    <div class="card-body py-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            {{ $prevMonth['month_name'] }}
                                        </div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                                            {{ number_format($prevMonth['total_expenses'], 2) }} ر.س
                                        </div>
                                        <div class="text-xs text-muted">
                                            {{ $prevMonth['transactions_count'] }} معاملة
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- جدول معاملات اليومية المنصرف -->
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-list"></i> معاملات اليومية المنصرف للسيارة
                </h6>
            </div>
            <div class="card-body">
                @if($transactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>التاريخ</th>
                                    <th>الوقت</th>
                                    <th>النوع</th>
                                    <th>طريقة الدفع</th>
                                    <th>المبلغ الأساسي</th>
                                    <th>نسبة الضريبة</th>
                                    <th>مبلغ الضريبة</th>
                                    <th>الإجمالي</th>
                                    <th>ملاحظات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $index => $transaction)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td class="text-center">{{ $transaction->created_at->format('Y-m-d') }}</td>
                                        <td class="text-center">{{ $transaction->created_at->format('H:i:s') }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-danger">منصرف</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $transaction->method == 'cash' ? 'badge-warning' : 'badge-info' }}">
                                                {{ $transaction->method == 'cash' ? 'نقدي' : 'بنكي' }}
                                            </span>
                                        </td>
                                        <td class="text-right">{{ number_format($transaction->amount, 2) }} ر.س</td>
                                        <td class="text-center">{{ $transaction->tax_value }}%</td>
                                        <td class="text-right text-success">{{ number_format($transaction->total_amount - $transaction->amount, 2) }} ر.س</td>
                                        <td class="text-right font-weight-bold">{{ number_format($transaction->total_amount, 2) }} ر.س</td>
                                        <td class="text-right">{{ $transaction->notes ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="thead-light">
                                <tr>
                                    <th colspan="5" class="text-right">الإجمالي:</th>
                                    <th class="text-right">{{ number_format($monthStats['total_amount'] - $monthStats['total_tax_amount'], 2) }} ر.س</th>
                                    <th></th>
                                    <th class="text-right text-success">{{ number_format($monthStats['total_tax_amount'], 2) }} ر.س</th>
                                    <th class="text-right font-weight-bold">{{ number_format($monthStats['total_amount'], 2) }} ر.س</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- ملخص إضافي للمعاملات -->
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card border-left-primary">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        إجمالي المعاملات
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $monthStats['total_transactions'] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-left-success">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        إجمالي المبلغ الأساسي
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($monthStats['total_amount'] - $monthStats['total_tax_amount'], 2) }} ر.س
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-left-info">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        إجمالي الضرائب
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($monthStats['total_tax_amount'], 2) }} ر.س
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد معاملات منصرف لهذه السيارة في هذا الشهر</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // إضافة تأثيرات بصرية للجدول
    document.addEventListener('DOMContentLoaded', function() {
        // تمييز الصفوف عند التمرير
        const rows = document.querySelectorAll('tbody tr');
        rows.forEach((row, index) => {
            if (index % 2 === 0) {
                row.style.backgroundColor = '#f8f9fa';
            }
        });
    });
</script>
@endsection
