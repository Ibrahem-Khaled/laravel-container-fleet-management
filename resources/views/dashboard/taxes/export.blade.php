@extends('layouts.app')

@section('title', 'تقرير الضرائب - تصدير')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    تقرير الضرائب - {{ $quarterDates['name'] }} {{ $year }}
                </h6>
            </div>
            <div class="card-body">
                <!-- ملخص الفترة -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold text-primary">تفاصيل الفترة:</h6>
                        <ul class="list-unstyled">
                            <li><strong>الفترة:</strong> {{ $quarterDates['name'] }} {{ $year }}</li>
                            <li><strong>من:</strong> {{ $quarterDates['start']->format('Y-m-d') }}</li>
                            <li><strong>إلى:</strong> {{ $quarterDates['end']->format('Y-m-d') }}</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold text-success">ملخص الضرائب:</h6>
                        <ul class="list-unstyled">
                            <li><strong>إجمالي الواردات:</strong> {{ number_format($stats['total_revenue'], 2) }} ر.س</li>
                            <li><strong>الضرائب المحصلة:</strong> {{ number_format($stats['total_collected_tax'], 2) }} ر.س</li>
                            <li><strong>الضرائب المدفوعة:</strong> {{ number_format($stats['total_paid_tax'], 2) }} ر.س</li>
                            <li><strong>الفرق:</strong>
                                <span class="{{ $stats['tax_difference'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($stats['tax_difference'], 2) }} ر.س
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- الضرائب المحصلة -->
                <div class="mb-4">
                    <h6 class="font-weight-bold text-primary">الضرائب المحصلة</h6>
                    @if(count($collectedTaxes) > 0)
                        @foreach($collectedTaxes as $tax)
                            <div class="mb-3">
                                <h6 class="font-weight-bold text-primary">
                                    {{ $tax['office']->name }}
                                    @if($tax['office']->operational_number)
                                        - رقم التشغيل: {{ $tax['office']->operational_number }}
                                    @endif
                                    - إجمالي الواردات: {{ number_format($tax['total_revenue'], 2) }} ر.س
                                    - الضريبة: {{ number_format($tax['tax_amount'], 2) }} ر.س
                                </h6>

                                @if(count($tax['monthly_details']) > 0)
                                    <div class="ml-3">
                                        <h6 class="font-weight-bold text-info">التفاصيل الشهرية:</h6>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>الشهر</th>
                                                        <th>واردات الحاويات</th>
                                                        <th>واردات المعاملات</th>
                                                        <th>إجمالي الواردات</th>
                                                        <th>نسبة الضريبة</th>
                                                        <th>مبلغ الضريبة</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($tax['monthly_details'] as $monthDetail)
                                                        <tr>
                                                            <td>{{ $monthDetail['month_name'] }}</td>
                                                            <td class="text-right">{{ number_format($monthDetail['container_revenue'], 2) }}</td>
                                                            <td class="text-right">{{ number_format($monthDetail['transaction_revenue'], 2) }}</td>
                                                            <td class="text-right">{{ number_format($monthDetail['total_revenue'], 2) }}</td>
                                                            <td class="text-center">{{ $monthDetail['tax_rate'] }}%</td>
                                                            <td class="text-right">{{ number_format($monthDetail['tax_amount'], 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot class="thead-light">
                                                    <tr>
                                                        <th colspan="3" class="text-right">إجمالي المكتب:</th>
                                                        <th class="text-right">{{ number_format($tax['total_revenue'], 2) }}</th>
                                                        <th class="text-center">{{ $tax['tax_rate'] }}%</th>
                                                        <th class="text-right">{{ number_format($tax['tax_amount'], 2) }}</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                @else
                                    <div class="ml-3">
                                        <p class="text-muted">لا توجد واردات شهرية في هذه الفترة</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        <!-- إجمالي جميع المكاتب -->
                        <div class="alert alert-primary">
                            <h6 class="font-weight-bold mb-2">إجمالي جميع المكاتب</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>إجمالي الواردات:</strong> {{ number_format($stats['total_revenue'], 2) }} ر.س
                                </div>
                                <div class="col-md-4">
                                    <strong>إجمالي الضرائب:</strong> {{ number_format($stats['total_collected_tax'], 2) }} ر.س
                                </div>
                                <div class="col-md-4">
                                    <strong>عدد المكاتب:</strong> {{ $stats['offices_count'] }}
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-muted">لا توجد مكاتب مفعلة الضرائب في هذه الفترة</p>
                    @endif
                </div>

                <!-- الضرائب المدفوعة -->
                <div class="mb-4">
                    <h6 class="font-weight-bold text-success">الضرائب المدفوعة (منصرف فقط)</h6>
                    @if(count($stats['monthly_data']) > 0)
                        @foreach($stats['monthly_data'] as $monthData)
                            <div class="mb-3">
                                <h6 class="font-weight-bold text-primary">
                                    {{ $monthData['month_name'] }} - {{ $monthData['total_transactions_count'] }} معاملة - {{ number_format($monthData['month_total_tax'], 2) }} ر.س
                                </h6>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>العلاقة</th>
                                                <th>عدد المعاملات</th>
                                                <th>طرق الدفع</th>
                                                <th>المبلغ الأساسي</th>
                                                <th>الضريبة</th>
                                                <th>الإجمالي</th>
                                                <th>الفترة</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($monthData['grouped_transactions'] as $group)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('taxes.details', [
                                                            'year' => $year,
                                                            'quarter' => $quarter,
                                                            'transactionable_type' => $group['transactionable_type'],
                                                            'transactionable_id' => $group['transactionable_id'],
                                                            'month' => $monthData['month_number']
                                                        ]) }}" class="text-decoration-none">
                                                            <strong class="text-primary">{{ $group['transactionable_name'] }}</strong>
                                                            <i class="fas fa-external-link-alt fa-sm text-muted"></i>
                                                        </a>
                                                    </td>
                                                    <td class="text-center">{{ $group['transactions_count'] }}</td>
                                                    <td class="text-center">{{ implode(', ', array_map(function($method) { return $method == 'cash' ? 'نقدي' : 'بنكي'; }, $group['methods'])) }}</td>
                                                    <td class="text-right">{{ number_format($group['total_base_amount'], 2) }}</td>
                                                    <td class="text-right">{{ number_format($group['total_tax_amount'], 2) }}</td>
                                                    <td class="text-right">{{ number_format($group['total_amount'], 2) }}</td>
                                                    <td class="text-center">{{ $group['first_date']->format('m/d') }} - {{ $group['last_date']->format('m/d') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="thead-light">
                                            <tr>
                                                <th colspan="3" class="text-right">إجمالي الشهر:</th>
                                                <th class="text-right">{{ number_format($monthData['grouped_transactions']->sum('total_base_amount'), 2) }}</th>
                                                <th class="text-right">{{ number_format($monthData['month_total_tax'], 2) }}</th>
                                                <th class="text-right">{{ number_format($monthData['month_total_amount'], 2) }}</th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">لا توجد معاملات ضريبية مدفوعة في هذه الفترة</p>
                    @endif
                </div>

                <!-- ملاحظات -->
                <div class="mt-4">
                    <h6 class="font-weight-bold text-info">ملاحظات:</h6>
                    <ul class="list-unstyled">
                        <li>• الضرائب المحصلة: هي الضرائب المستحقة على المكاتب الجمركية بناءً على إجمالي الواردات</li>
                        <li>• واردات الحاويات: إجمالي أسعار الحاويات المنقولة للمكتب في الشهر</li>
                        <li>• واردات المعاملات: إجمالي المعاملات المالية الواردة للمكتب في الشهر</li>
                        <li>• الضرائب المدفوعة: تشمل فقط المعاملات من نوع "منصرف" التي تحتوي على ضريبة أكبر من الصفر</li>
                        <li>• المعاملات مجمعة حسب العلاقة (مكتب تخليص، سيارة، حاوية، عهدة، إلخ)</li>
                        <li>• عدد المعاملات: يوضح كم معاملة تمت لنفس العلاقة في الشهر</li>
                        <li>• طرق الدفع: تظهر جميع طرق الدفع المستخدمة للعلاقة (نقدي، بنكي، أو كلاهما)</li>
                        <li>• الفترة: توضح أول وآخر تاريخ للمعاملات في الشهر</li>
                        <li>• المبلغ الأساسي: هو المبلغ قبل إضافة الضريبة</li>
                        <li>• الضريبة: هي المبلغ المحسوب كنسبة من المبلغ الأساسي</li>
                        <li>• الإجمالي: هو مجموع المبلغ الأساسي + الضريبة</li>
                        <li>• الفرق الإيجابي يعني أن هناك ضرائب محصلة أكثر من المدفوعة</li>
                        <li>• الفرق السلبي يعني أن هناك ضرائب مدفوعة أكثر من المحصلة</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
