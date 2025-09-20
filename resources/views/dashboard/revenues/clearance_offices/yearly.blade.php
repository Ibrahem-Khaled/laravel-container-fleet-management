@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-1 text-gray-800">التقرير السنوي</h1>
                <p class="mb-0 text-muted">
                    للمكتب: <strong>{{ $office->name }}</strong> |
                    السنة: <strong>{{ $year }}</strong>
                </p>
            </div>
            <a href="{{ route('revenues.clearance.monthly', ['office' => $office->id, 'year' => $year, 'month' => now()->month]) }}"
                class="btn btn-sm btn-outline-primary">
                الرجوع للكشف الشهري
            </a>
        </div>

        <div class="card shadow mb-3">
            <div class="card-body bg-light">
                <form class="form-inline" method="GET" action="{{ route('revenues.clearance.yearly', $office->id) }}">
                    <label class="mr-2">السنة:</label>
                    <select name="year" class="form-control mr-2">
                        @for ($y = now()->year; $y >= 2020; $y--)
                            <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                        @endfor
                    </select>
                    <button class="btn btn-primary">عرض</button>
                </form>
            </div>
        </div>

        <div class="row mb-4">
            <x-stats-card icon="fas fa-shipping-fast" title="إجمالي أسعار الحاويات المنقولة" :value="number_format($yearTotals['transported'], 2)"
                color="warning" />
            <x-stats-card icon="fas fa-hand-holding-usd" title="إجمالي المبالغ المستلمة" :value="number_format($yearTotals['income'], 2)"
                color="success" />
            <x-stats-card icon="fas fa-balance-scale-right" title="صافي السنة" :value="number_format($yearTotals['balance'], 2)" :color="$yearTotals['balance'] > 0 ? 'danger' : 'info'" />
            <x-stats-card icon="fas fa-percentage" title="إجمالي الضرائب" :value="number_format($yearTotals['tax_amount'], 2)"
                color="primary" />
            <x-stats-card icon="fas fa-calculator" title="الإجمالي مع الضرائب" :value="number_format($yearTotals['total_with_tax'], 2)"
                color="info" />
        </div>

        <div class="card shadow">
            <div class="card-header">
                <strong>تفصيل شهري</strong>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-striped mb-0">
                    <thead>
                        <tr>
                            <th>الشهر</th>
                            <th>المنقول (حاويات)</th>
                            <th>الوارد (اليومية)</th>
                            <th>الصافي</th>
                            <th>الضرائب</th>
                            <th>الإجمالي مع الضرائب</th>
                            <th>حالة الضرائب</th>
                            <th>رابط الكشف</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $r)
                            <tr>
                                <td>{{ \Carbon\Carbon::create($year, $r['month'], 1)->translatedFormat('F') }}</td>
                                <td>{{ number_format($r['transported'], 2) }}</td>
                                <td>{{ number_format($r['income'], 2) }}</td>
                                <td>{{ number_format($r['balance'], 2) }}</td>
                                <td>
                                    <span class="text-warning font-weight-bold">
                                        {{ number_format($r['tax_calculation']['tax_amount'], 2) }}
                                    </span>
                                    <small class="text-muted">({{ $r['tax_calculation']['tax_rate'] }}%)</small>
                                </td>
                                <td>
                                    <span class="text-success font-weight-bold">
                                        {{ number_format($r['tax_calculation']['total_amount'], 2) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $r['tax_calculation']['tax_enabled'] ? 'success' : 'danger' }}">
                                        {{ $r['tax_calculation']['tax_enabled'] ? 'مفعلة' : 'معطلة' }}
                                    </span>
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-outline-secondary"
                                        href="{{ route('revenues.clearance.monthly', ['office' => $office->id, 'year' => $year, 'month' => $r['month']]) }}">
                                        عرض كشف {{ $r['month'] }}/{{ $year }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
