@extends('layouts.app')

@section('title', 'الصفحة الرئيسية')

@section('content')

    {{-- فلاتر --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>من تاريخ</label>
                        <input type="date" name="start_date" value="{{ $filters['start'] }}" class="form-control">
                    </div>
                    <div class="form-group col-md-3">
                        <label>إلى تاريخ</label>
                        <input type="date" name="end_date" value="{{ $filters['end'] }}" class="form-control">
                    </div>
                    <div class="form-group col-md-3">
                        <label>حالة الحاوية</label>
                        <select name="status" class="custom-select">
                            <option value="">الكل</option>
                            @foreach (['wait', 'transport', 'done', 'rent', 'storage'] as $st)
                                <option value="{{ $st }}" @if ($filters['status'] === $st) selected @endif>
                                    {{ $st }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label>بحث (رقم/اتجاه الحاوية)</label>
                        <input type="text" name="q" value="{{ $filters['term'] }}" class="form-control"
                            placeholder="أدخل كلمة بحث">
                    </div>
                </div>
                <div class="text-left">
                    <button class="btn btn-primary">تطبيق الفلاتر</button>
                </div>
            </form>
        </div>
    </div>

    {{-- KPIs رئيسية باستخدام المكوّن --}}
    <div class="row">

        <x-stats-card icon="fas fa-arrow-up" title="إجمالي الإيرادات" :value="$kpis['income']" color="success" />

        <x-stats-card icon="fas fa-arrow-down" title="إجمالي المصروفات" :value="$kpis['expense']" color="danger" />

        <x-stats-card icon="fas fa-percent" title="إجمالي الضرائب" :value="$kpis['tax']" color="warning" />

        <x-stats-card icon="fas fa-balance-scale" title="الرصيد" :value="$kpis['balance']" color="info" />
    </div>

    {{-- KPIs إضافية: العملاء + إجماليات بنك/كاش + فروق + صوافي --}}
    <div class="row">

        <x-stats-card icon="fas fa-users" title="عدد العملاء" :value="$stats['clients_count']" color="success" />

        <x-stats-card icon="fas fa-university" title="الوارد (بنك)" :value="$stats['income_bank']" color="primary" />

        <x-stats-card icon="fas fa-wallet" title="الوارد (كاش)" :value="$stats['income_cash']" color="primary" />

        <x-stats-card icon="fas fa-exchange-alt" title="فرق الوارد (بنك - كاش)" :value="$stats['income_diff']" color="secondary" />
    </div>

    <div class="row">

        <x-stats-card icon="fas fa-university" title="المنصرف (بنك)" :value="$stats['expense_bank']" color="danger" />

        <x-stats-card icon="fas fa-wallet" title="المنصرف (كاش)" :value="$stats['expense_cash']" color="danger" />

        <x-stats-card icon="fas fa-exchange-alt" title="فرق المنصرف (بنك - كاش)" :value="$stats['expense_diff']" color="secondary" />

        <x-stats-card icon="fas fa-calculator" title="الصافي الكلي" :value="$stats['net_total']" color="dark" />
    </div>

    {{-- ملخصات إضافية سريعة --}}
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title text-muted mb-2">إجمالي تكلفة التحويلات</h6>
                    <div class="kpi-value">{{ number_format($transferSum, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-8 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title">عدد الحاويات حسب الحالة</h6>
                    <div class="row">
                        @foreach (['wait', 'transport', 'done', 'rent', 'storage'] as $st)
                            <div class="col-6 col-sm-4 col-md-2 mb-2">
                                <div class="border rounded p-2 text-center">
                                    <div class="small text-muted">{{ $st }}</div>
                                    <div class="font-weight-bold">{{ $byStatus[$st] ?? 0 }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- مكاتب التخليص الجمركي + عدد الحاويات غير المُسعَّرة --}}
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-3">مكاتب التخليص الجمركي</h5>

            <div class="table-responsive">
                <table class="table table-sm table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th>المكتب</th>
                            <th class="text-center">إجمالي الحاويات</th>
                            <th class="text-center text-danger">غير المُسعَّرة</th>
                            <th class="text-center">نسبة غير المُسعَّرة</th>
                            <th class="text-left">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($officesStats as $row)
                            @php
                                $total = (int) $row->containers_total;
                                $unp = (int) $row->containers_unpriced;
                                $pct = $total > 0 ? round(($unp / $total) * 100, 1) : 0;
                            @endphp
                            <tr>
                                <td class="font-weight-bold">{{ $row->name }}</td>
                                <td class="text-center">{{ $total }}</td>
                                <td class="text-center text-danger font-weight-bold">{{ $unp }}</td>
                                <td class="text-center">{{ $pct }}%</td>
                                <td class="text-left">
                                    {{-- مثال روابط (حدّد الروت المناسب لديك) --}}
                                    <a href="{{ url('/containers?office_id=' . $row->id . '&priced=0') }}"
                                        class="btn btn-sm btn-outline-danger">
                                        عرض غير المُسعَّرة
                                    </a>
                                    <a href="{{ url('/containers?office_id=' . $row->id) }}"
                                        class="btn btn-sm btn-outline-secondary">
                                        كل الحاويات
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">لا توجد بيانات لعرضها.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

@endsection
