@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        {{-- العنوان + breadcrumb --}}
        <div class="row">
            <div class="col-12">
                <h1 class="h3 mb-0 text-gray-800">توزيع أرباح الشركاء</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">لوحة التحكم</a></li>
                        <li class="breadcrumb-item active" aria-current="page">توزيع الأرباح</li>
                    </ol>
                </nav>
            </div>
        </div>

        @include('components.alerts')

        {{-- اختيار الفترة --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">اختر الفترة</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('partners.profit.index') }}" method="GET" class="row">
                    <div class="col-md-2 mb-2">
                        <label>السنة</label>
                        <input type="number" class="form-control" name="year" value="{{ $year }}" min="2000"
                            max="2100">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label>الشهر</label>
                        <input type="number" class="form-control" name="month" value="{{ $month }}" min="1"
                            max="12">
                    </div>
                    <div class="col-md-3 d-flex align-items-end mb-2">
                        <button class="btn btn-primary"><i class="fas fa-filter"></i> تطبيق</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- بطاقات ملخصة --}}
        <div class="row mb-4">
            <x-stats-card icon="fas fa-coins" title="صافي ربح الشهر" :value="number_format($net, 2)"
                color="{{ $net >= 0 ? 'success' : 'danger' }}" />
            @if ($run)
                <x-stats-card icon="fas fa-lock" title="حالة التشغيل" :value="$run->status"
                    color="{{ $run->status === 'locked' ? 'info' : 'warning' }}" />
            @endif
        </div>

        {{-- تشغيل التوزيع --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">تشغيل توزيع الأرباح</h6>
                <form action="{{ route('partners.profit.run') }}" method="POST"
                    onsubmit="return confirm('تشغيل توزيع أرباح هذا الشهر؟');">
                    @csrf
                    <input type="hidden" name="year" value="{{ $year }}">
                    <input type="hidden" name="month" value="{{ $month }}">
                    <button class="btn btn-success" {{ $net == 0 ? 'disabled' : '' }}>
                        <i class="fas fa-play"></i> تشغيل
                    </button>
                </form>
            </div>
            <div class="card-body">
                <p class="mb-0 text-muted">
                    يتم التوزيع <strong>نسبيًا</strong> حسب <strong>(الرصيد × الأيام)</strong> داخل الشهر؛ الإيداعات لا
                    تشارك قبل تاريخها.
                </p>
            </div>
        </div>

        {{-- جدول نتائج التوزيع --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">نتيجة التوزيع</h6>
            </div>
            <div class="card-body">
                @php
                    $hasRun = $run && $run->allocations && $run->allocations->isNotEmpty();
                    $totalShare = $hasRun ? $run->allocations->sum('share_amount') : 0;
                @endphp

                @if (!$hasRun)
                    <div class="alert alert-info mb-0">لا توجد تخصيصات بعد لهذه الفترة.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>الشريك</th>
                                    <th>وزن (رصيد × أيام)</th>
                                    <th>متوسط الرصيد</th>
                                    <th>نصيبه من الربح</th>
                                    <th>النسبة %</th> {{-- ✅ عمود النسبة --}}
                                    <th>إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($run->allocations as $row)
                                    @php
                                        $percent = $totalShare > 0 ? ($row->share_amount / $totalShare) * 100 : 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $row->partner->name }}</td>
                                        <td>{{ number_format($row->weight_capital_days, 4) }}</td>
                                        <td>{{ number_format($row->avg_balance_during_period, 4) }}</td>
                                        <td>
                                            <span class="badge badge-{{ $row->share_amount >= 0 ? 'success' : 'danger' }}">
                                                {{ number_format($row->share_amount, 2) }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($percent, 2) }}%</td> {{-- ✅ عرض النسبة --}}
                                        <td>
                                            <a class="btn btn-sm btn-info"
                                                href="{{ route('partners.movements.index', $row->partner_id) }}">
                                                <i class="fas fa-list"></i> حركات الشريك
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="table-secondary font-weight-bold">
                                    <td>الإجمالي</td>
                                    <td>{{ number_format($run->allocations->sum('weight_capital_days'), 4) }}</td>
                                    <td></td>
                                    <td>{{ number_format($totalShare, 2) }}</td>
                                    <td>100.00%</td> {{-- ✅ مجموع النِّسب --}}
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection
