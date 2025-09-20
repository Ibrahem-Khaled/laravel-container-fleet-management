@extends('layouts.app')

@section('title', 'تفاصيل المعاملات الضريبية')

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
</style>
@endsection

@section('content')
<div class="row">
    <!-- زر العودة -->
    <div class="col-12 mb-3">
        <a href="{{ route('taxes.index', ['year' => $year, 'quarter' => $quarter]) }}" class="back-link">
            <i class="fas fa-arrow-right"></i> العودة إلى صفحة الضرائب
        </a>
    </div>

    <!-- معلومات العلاقة -->
    <div class="col-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle"></i> تفاصيل المعاملات الضريبية
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold text-primary">معلومات العلاقة:</h6>
                        <ul class="list-unstyled">
                            <li><strong>العلاقة:</strong> {{ $transactionableName }}</li>
                            <li><strong>الشهر:</strong> {{ \App\Http\Controllers\system\TaxController::getArabicMonthName($month) }} {{ $year }}</li>
                            <li><strong>الفترة:</strong> {{ $monthStart->format('Y-m-d') }} إلى {{ $monthEnd->format('Y-m-d') }}</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold text-success">الإحصائيات:</h6>
                        <ul class="list-unstyled">
                            <li><strong>عدد المعاملات:</strong> {{ $stats['total_transactions'] }}</li>
                            <li><strong>إجمالي المبلغ الأساسي:</strong> {{ number_format($stats['total_base_amount'], 2) }} ر.س</li>
                            <li><strong>إجمالي الضريبة:</strong> {{ number_format($stats['total_tax_amount'], 2) }} ر.س</li>
                            <li><strong>إجمالي المبلغ:</strong> {{ number_format($stats['total_amount'], 2) }} ر.س</li>
                            <li><strong>طرق الدفع:</strong>
                                @foreach($stats['methods'] as $method)
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

    <!-- جدول المعاملات -->
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-list"></i> قائمة المعاملات التفصيلية
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
                                    <th colspan="4" class="text-right">الإجمالي:</th>
                                    <th class="text-right">{{ number_format($stats['total_base_amount'], 2) }} ر.س</th>
                                    <th></th>
                                    <th class="text-right text-success">{{ number_format($stats['total_tax_amount'], 2) }} ر.س</th>
                                    <th class="text-right font-weight-bold">{{ number_format($stats['total_amount'], 2) }} ر.س</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد معاملات ضريبية لهذه العلاقة في هذا الشهر</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- ملخص إضافي -->
@if($transactions->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">ملخص إضافي</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h6 class="font-weight-bold text-primary">التوزيع اليومي:</h6>
                        @php
                            $dailyStats = $transactions->groupBy(function($transaction) {
                                return $transaction->created_at->format('Y-m-d');
                            })->map(function($group) {
                                return [
                                    'count' => $group->count(),
                                    'total' => $group->sum('total_amount'),
                                    'tax' => $group->sum(function($t) { return $t->total_amount - $t->amount; })
                                ];
                            });
                        @endphp
                        @foreach($dailyStats as $date => $dayStats)
                            <div class="mb-2">
                                <strong>{{ $date }}:</strong>
                                {{ $dayStats['count'] }} معاملة -
                                {{ number_format($dayStats['total'], 2) }} ر.س
                                <small class="text-muted">(ضريبة: {{ number_format($dayStats['tax'], 2) }} ر.س)</small>
                            </div>
                        @endforeach
                    </div>
                    <div class="col-md-4">
                        <h6 class="font-weight-bold text-success">التوزيع حسب طريقة الدفع:</h6>
                        @php
                            $methodStats = $transactions->groupBy('method')->map(function($group) {
                                return [
                                    'count' => $group->count(),
                                    'total' => $group->sum('total_amount'),
                                    'tax' => $group->sum(function($t) { return $t->total_amount - $t->amount; })
                                ];
                            });
                        @endphp
                        @foreach($methodStats as $method => $methodData)
                            <div class="mb-2">
                                <strong>{{ $method == 'cash' ? 'نقدي' : 'بنكي' }}:</strong>
                                {{ $methodData['count'] }} معاملة -
                                {{ number_format($methodData['total'], 2) }} ر.س
                                <small class="text-muted">(ضريبة: {{ number_format($methodData['tax'], 2) }} ر.س)</small>
                            </div>
                        @endforeach
                    </div>
                    <div class="col-md-4">
                        <h6 class="font-weight-bold text-info">معدلات الضرائب:</h6>
                        @php
                            $taxRates = $transactions->groupBy('tax_value')->map(function($group) {
                                return $group->count();
                            });
                        @endphp
                        @foreach($taxRates as $rate => $count)
                            <div class="mb-2">
                                <strong>{{ $rate }}%:</strong> {{ $count }} معاملة
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
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
