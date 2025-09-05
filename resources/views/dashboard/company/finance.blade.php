@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        {{-- العنوان ومسار التنقل --}}
        <div class="row">
            <div class="col-12">
                <h1 class="h3 mb-0 text-gray-800">تقرير أرباح الشركة السنوي</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home') }}">لوحة التحكم</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">تقرير الأرباح</li>
                    </ol>
                </nav>
            </div>
        </div>

        @include('components.alerts')

        {{-- فلاتر --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">تصفية حسب السنة</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('company.finance') }}" method="GET" class="row">
                    <div class="col-md-3 mb-2">
                        <label class="form-label">السنة</label>
                        <select name="year" class="form-control">
                            @foreach ($years as $y)
                                <option value="{{ $y }}" {{ (int) $year === (int) $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end mb-2">
                        <button class="btn btn-primary">
                            <i class="fas fa-filter"></i> تطبيق
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- بطاقات الإحصائيات --}}
        <div class="row mb-4">

            <x-stats-card icon="fas fa-arrow-down" title="إجمالي المنصرف" :value="number_format($totalExpense, 2)" color="danger" />

            <x-stats-card icon="fas fa-arrow-up" title="إجمالي الوارد" :value="number_format($totalIncome, 2)" color="success" />

            <x-stats-card icon="fas fa-balance-scale" title="صافي الربح للسنة" :value="number_format($netProfit, 2)"
                color="{{ $netProfit >= 0 ? 'primary' : 'warning' }}" />

            <x-stats-card icon="fas fa-receipt" title="إجمالي الضرائب" :value="number_format($totalTax, 2)" color="info" />
        </div>

        {{-- بطاقات الضرائب المفصّلة --}}
        <div class="row mb-4">

            <x-stats-card icon="fas fa-file-invoice-dollar" title="ضرائب الوارد" :value="number_format($totalTaxIncome, 2)" color="success" />

            <x-stats-card icon="fas fa-file-invoice" title="ضرائب المنصرف" :value="number_format($totalTaxExpense, 2)" color="danger" />
        </div>
        {{-- جدول شهري تفصيلي --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">تفاصيل الشهر</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>الشهر</th>
                                <th>الوارد</th>
                                <th>المنصرف</th>
                                <th>صافي الشهر</th>
                                <th>الصافي التراكمي</th>
                                <th>ضرائب الوارد</th>
                                <th>ضرائب المنصرف</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($monthlyDetails as $row)
                                <tr>
                                    <td>{{ $row['month_name'] }}</td>
                                    <td>{{ number_format($row['income'], 2) }}</td>
                                    <td>{{ number_format($row['expense'], 2) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $row['net'] >= 0 ? 'success' : 'danger' }}">
                                            {{ number_format($row['net'], 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge badge-{{ $row['cumulative_net'] >= 0 ? 'primary' : 'warning' }}">
                                            {{ number_format($row['cumulative_net'], 2) }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($row['tax_income'], 2) }}</td>
                                    <td>{{ number_format($row['tax_expense'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">لا توجد بيانات لهذه السنة</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- تفصيل حسب الفئات --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">تفصيل حسب الفئات</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>الفئة</th>
                                <th>الوارد</th>
                                <th>المنصرف</th>
                                <th>الصافي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($byCategory as $row)
                                <tr>
                                    <td>{{ $row['category'] }}</td>
                                    <td>{{ number_format($row['income'], 2) }}</td>
                                    <td>{{ number_format($row['expense'], 2) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $row['net'] >= 0 ? 'success' : 'danger' }}">
                                            {{ number_format($row['net'], 2) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">لا توجد بيانات لهذه السنة</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- الشارت الشهري (Bar للوارد/المنصرف + Line للصافي التراكمي) --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">الوارد والمنصرف شهرياً + الصافي التراكمي
                    ({{ $year }})
                </h6>
            </div>
            <div class="card-body">
                <canvas id="comboChart" height="120"></canvas>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const labels = ['ينا', 'فبر', 'مار', 'أبر', 'ماي', 'يون', 'يول', 'أغس', 'سبت', 'أكت', 'نوف', 'ديس'];

        const incomeData = @json(array_values($monthlyIncome));
        const expenseData = @json(array_values($monthlyExpense));
        const cumulativeData = @json(array_values($cumulativeNet));

        const ctx = document.getElementById('comboChart').getContext('2d');

        // شارت مخلوط: Bar للوارد/المنصرف + Line للصافي التراكمي
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                        label: 'الوارد',
                        type: 'bar',
                        data: incomeData
                    },
                    {
                        label: 'المنصرف',
                        type: 'bar',
                        data: expenseData
                    },
                    {
                        label: 'الصافي التراكمي',
                        type: 'line',
                        data: cumulativeData,
                        tension: 0.3,
                        yAxisID: 'y2'
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (v) => new Intl.NumberFormat('ar-EG', {
                                maximumFractionDigits: 0
                            }).format(v)
                        },
                        title: {
                            display: true,
                            text: 'وارد/منصرف'
                        }
                    },
                    y2: {
                        beginAtZero: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false
                        },
                        ticks: {
                            callback: (v) => new Intl.NumberFormat('ar-EG', {
                                maximumFractionDigits: 0
                            }).format(v)
                        },
                        title: {
                            display: true,
                            text: 'الصافي التراكمي'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                const val = ctx.parsed.y ?? 0;
                                return `${ctx.dataset.label}: ${new Intl.NumberFormat('ar-EG', {minimumFractionDigits:2}).format(val)}`;
                            }
                        }
                    },
                    legend: {
                        position: 'top'
                    }
                }
            }
        });
    </script>
@endpush
