@extends('layouts.app')

@section('title', 'إدارة الضرائب')

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
</style>
@endsection

@section('content')
<div class="row">
    <!-- فلتر السنة والربع -->
    <div class="col-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">فلتر الفترة الزمنية</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('taxes.index') }}" class="row">
                    <div class="col-md-4">
                        <label for="year" class="form-label">السنة</label>
                        <select name="year" id="year" class="form-control">
                            @for($i = now()->year - 2; $i <= now()->year + 1; $i++)
                                <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="quarter" class="form-label">الربع</label>
                        <select name="quarter" id="quarter" class="form-control">
                            <option value="1" {{ $quarter == 1 ? 'selected' : '' }}>الربع الأول (يناير - مارس)</option>
                            <option value="2" {{ $quarter == 2 ? 'selected' : '' }}>الربع الثاني (أبريل - يونيو)</option>
                            <option value="3" {{ $quarter == 3 ? 'selected' : '' }}>الربع الثالث (يوليو - سبتمبر)</option>
                            <option value="4" {{ $quarter == 4 ? 'selected' : '' }}>الربع الرابع (أكتوبر - ديسمبر)</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary mr-2">
                            <i class="fas fa-search"></i> عرض البيانات
                        </button>
                        <a href="{{ route('taxes.export', ['year' => $year, 'quarter' => $quarter]) }}" class="btn btn-success">
                            <i class="fas fa-file-export"></i> تصدير التقرير
                        </a>
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
                                    إجمالي الضرائب المحصلة
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($stats['total_collected_tax'], 2) }} ر.س
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
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    إجمالي الضرائب المدفوعة
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($stats['total_paid_tax'], 2) }} ر.س
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
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    الفرق (محصل - مدفوع)
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800 {{ $stats['tax_difference'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($stats['tax_difference'], 2) }} ر.س
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-balance-scale fa-2x text-gray-300"></i>
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
                                    عدد المكاتب المفعلة
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $stats['offices_count'] }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-building fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- الضرائب المحصلة -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    الضرائب المحصلة - {{ $quarterDates['name'] }} {{ $year }}
                </h6>
            </div>
            <div class="card-body">
                @if(count($collectedTaxes) > 0)
                    @foreach($collectedTaxes as $tax)
                        <div class="mb-4">
                            <!-- معلومات المكتب الأساسية -->
                            <div class="card border-left-primary mb-3">
                                <div class="card-body py-2">
                                    <div class="row align-items-center">
                                        <div class="col-md-4">
                                            <h6 class="font-weight-bold text-primary mb-1">
                                                <i class="fas fa-building"></i> {{ $tax['office']->name }}
                                            </h6>
                                            @if($tax['office']->operational_number)
                                                <small class="text-muted">رقم التشغيل: {{ $tax['office']->operational_number }}</small>
                                            @endif
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <small class="text-muted">إجمالي الواردات</small><br>
                                            <strong>{{ number_format($tax['total_revenue'], 2) }} ر.س</strong>
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <small class="text-muted">نسبة الضريبة</small><br>
                                            <strong>{{ $tax['tax_rate'] }}%</strong>
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <small class="text-muted">مبلغ الضريبة</small><br>
                                            <strong class="{{ $tax['tax_amount'] > 0 ? 'text-success' : 'text-muted' }}">
                                                {{ number_format($tax['tax_amount'], 2) }} ر.س
                                            </strong>
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <small class="text-muted">حالة الضريبة</small><br>
                                            @if($tax['tax_enabled'])
                                                <span class="badge badge-success">مفعل</span>
                                            @else
                                                <span class="badge badge-secondary">معطل</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- التفاصيل الشهرية -->
                            @if(count($tax['monthly_details']) > 0)
                                <div class="ml-3">
                                    <h6 class="font-weight-bold text-info mb-2">
                                        <i class="fas fa-calendar-alt"></i> التفاصيل الشهرية
                                    </h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
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
                                                        <td class="font-weight-bold">{{ $monthDetail['month_name'] }}</td>
                                                        <td class="text-right">{{ number_format($monthDetail['container_revenue'], 2) }} ر.س</td>
                                                        <td class="text-right">{{ number_format($monthDetail['transaction_revenue'], 2) }} ر.س</td>
                                                        <td class="text-right font-weight-bold">{{ number_format($monthDetail['total_revenue'], 2) }} ر.س</td>
                                                        <td class="text-center">{{ $monthDetail['tax_rate'] }}%</td>
                                                        <td class="text-right">
                                                            <span class="font-weight-bold {{ $monthDetail['tax_amount'] > 0 ? 'text-success' : 'text-muted' }}">
                                                                {{ number_format($monthDetail['tax_amount'], 2) }} ر.س
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="thead-light">
                                                <tr>
                                                    <th colspan="3" class="text-right">إجمالي المكتب:</th>
                                                    <th class="text-right">{{ number_format($tax['total_revenue'], 2) }} ر.س</th>
                                                    <th class="text-center">{{ $tax['tax_rate'] }}%</th>
                                                    <th class="text-right">{{ number_format($tax['tax_amount'], 2) }} ر.س</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            @else
                                <div class="ml-3">
                                    <p class="text-muted">
                                        <i class="fas fa-info-circle"></i> لا توجد واردات شهرية في هذه الفترة
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endforeach

                    <!-- إجمالي جميع المكاتب -->
                    <div class="alert alert-primary">
                        <h6 class="font-weight-bold mb-2">
                            <i class="fas fa-calculator"></i> إجمالي جميع المكاتب
                        </h6>
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
                    <div class="text-center py-4">
                        <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد مكاتب مفعلة الضرائب في هذه الفترة</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- الضرائب المدفوعة -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    الضرائب المدفوعة (منصرف فقط) - {{ $quarterDates['name'] }} {{ $year }}
                </h6>
            </div>
            <div class="card-body">
                @if(count($stats['monthly_data']) > 0)
                    @foreach($stats['monthly_data'] as $monthData)
                        <div class="mb-4">
                            <h6 class="font-weight-bold text-primary mb-3">
                                <i class="fas fa-calendar-alt"></i> {{ $monthData['month_name'] }}
                                <span class="badge badge-info">{{ $monthData['total_transactions_count'] }} معاملة</span>
                                <span class="badge badge-success">{{ number_format($monthData['month_total_tax'], 2) }} ر.س</span>
                            </h6>

                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
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
                                                    @if(count($group['notes']) > 0)
                                                        <br><small class="text-muted">{{ implode(', ', $group['notes']) }}</small>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-primary">{{ $group['transactions_count'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    @foreach($group['methods'] as $method)
                                                        <span class="badge {{ $method == 'cash' ? 'badge-warning' : 'badge-info' }}">
                                                            {{ $method == 'cash' ? 'نقدي' : 'بنكي' }}
                                                        </span>
                                                    @endforeach
                                                </td>
                                                <td class="text-right">{{ number_format($group['total_base_amount'], 2) }}</td>
                                                <td class="text-right text-success">{{ number_format($group['total_tax_amount'], 2) }}</td>
                                                <td class="text-right font-weight-bold">{{ number_format($group['total_amount'], 2) }}</td>
                                                <td class="text-center">
                                                    <small>
                                                        {{ $group['first_date']->format('m/d') }} - {{ $group['last_date']->format('m/d') }}
                                                    </small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="thead-light">
                                        <tr>
                                            <th colspan="3" class="text-right">إجمالي الشهر:</th>
                                            <th class="text-right">{{ number_format($monthData['grouped_transactions']->sum('total_base_amount'), 2) }}</th>
                                            <th class="text-right text-success">{{ number_format($monthData['month_total_tax'], 2) }}</th>
                                            <th class="text-right font-weight-bold">{{ number_format($monthData['month_total_amount'], 2) }}</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endforeach

                    <!-- إجمالي الربع -->
                    <div class="alert alert-info">
                        <h6 class="font-weight-bold mb-2">
                            <i class="fas fa-calculator"></i> إجمالي الربع
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>إجمالي الضرائب:</strong> {{ number_format($stats['total_paid_tax'], 2) }} ر.س
                            </div>
                            <div class="col-md-6">
                                <strong>عدد المعاملات:</strong> {{ $stats['transactions_count'] }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد معاملات ضريبية مدفوعة في هذه الفترة</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- ملخص الفترة -->
<div class="row">
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
                            <li><strong>عدد المعاملات الضريبية:</strong> {{ $stats['transactions_count'] }}</li>
                            <li><strong>الفرق:</strong>
                                <span class="{{ $stats['tax_difference'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($stats['tax_difference'], 2) }} ر.س
                                </span>
                            </li>
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
    // تحديث الصفحة عند تغيير السنة أو الربع
    document.getElementById('year').addEventListener('change', function() {
        this.form.submit();
    });

    document.getElementById('quarter').addEventListener('change', function() {
        this.form.submit();
    });
</script>
@endsection
