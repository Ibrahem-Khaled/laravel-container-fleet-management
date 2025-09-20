@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        {{-- Page Header --}}
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-1 text-gray-800">سجل الضرائب</h1>
                <p class="mb-0 text-muted">
                    للمكتب: <strong>{{ $clearance_office->name }}</strong>
                </p>
            </div>
            <div>
                <a href="{{ route('clearance-offices.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-right"></i> العودة لقائمة المكاتب
                </a>
                <a href="{{ route('clearance-offices.show', $clearance_office->id) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-eye"></i> عرض تفاصيل المكتب
                </a>
            </div>
        </div>

        @include('components.alerts')

        {{-- Current Tax Status --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-info-circle"></i> الحالة الحالية للضرائب
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center p-3 border rounded">
                                    <h5 class="text-{{ $clearance_office->tax_enabled ? 'success' : 'danger' }}">
                                        {{ $clearance_office->tax_enabled ? 'مفعلة' : 'معطلة' }}
                                    </h5>
                                    <small class="text-muted">حالة الضرائب الحالية</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 border rounded">
                                    <h5 class="text-primary">15%</h5>
                                    <small class="text-muted">نسبة الضريبة</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 border rounded">
                                    <h5 class="text-info">{{ $taxHistory->count() }}</h5>
                                    <small class="text-muted">عدد التغييرات</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tax History Table --}}
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-history"></i> سجل تغييرات الضرائب
                </h6>
            </div>
            <div class="card-body">
                @if($taxHistory->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>حالة الضرائب</th>
                                    <th>نسبة الضريبة</th>
                                    <th>تاريخ البداية</th>
                                    <th>تاريخ النهاية</th>
                                    <th>المدة</th>
                                    <th>المستخدم</th>
                                    <th>ملاحظات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($taxHistory as $index => $history)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <span class="badge badge-{{ $history->tax_enabled ? 'success' : 'danger' }} badge-lg">
                                                {{ $history->tax_enabled ? 'مفعلة' : 'معطلة' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="font-weight-bold">{{ $history->tax_rate }}%</span>
                                        </td>
                                        <td>
                                            <span class="text-primary">
                                                {{ $history->effective_from->format('Y-m-d H:i') }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($history->effective_to)
                                                <span class="text-warning">
                                                    {{ $history->effective_to->format('Y-m-d H:i') }}
                                                </span>
                                            @else
                                                <span class="text-success font-weight-bold">مستمر</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($history->effective_to)
                                                {{ $history->effective_from->diffInDays($history->effective_to) }} يوم
                                            @else
                                                {{ $history->effective_from->diffInDays(now()) }} يوم (مستمر)
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $history->changedBy->avatar ? asset('storage/' . $history->changedBy->avatar) : asset('img/default-avatar.png') }}"
                                                    alt="{{ $history->changedBy->name }}" class="rounded-circle mr-2" width="30" height="30">
                                                <span>{{ $history->changedBy->name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if($history->notes)
                                                <span class="text-muted">{{ $history->notes }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">لا يوجد سجل للضرائب</h5>
                        <p class="text-muted">لم يتم تسجيل أي تغييرات في الضرائب لهذا المكتب بعد.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Tax Statistics --}}
        @if($taxHistory->count() > 0)
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-success">
                                <i class="fas fa-chart-pie"></i> إحصائيات الضرائب
                            </h6>
                        </div>
                        <div class="card-body">
                            @php
                                $enabledPeriods = $taxHistory->where('tax_enabled', true);
                                $disabledPeriods = $taxHistory->where('tax_enabled', false);
                                $totalEnabledDays = $enabledPeriods->sum(function($period) {
                                    if ($period->effective_to) {
                                        return $period->effective_from->diffInDays($period->effective_to);
                                    } else {
                                        return $period->effective_from->diffInDays(now());
                                    }
                                });
                                $totalDisabledDays = $disabledPeriods->sum(function($period) {
                                    if ($period->effective_to) {
                                        return $period->effective_from->diffInDays($period->effective_to);
                                    } else {
                                        return $period->effective_from->diffInDays(now());
                                    }
                                });
                                $totalDays = $totalEnabledDays + $totalDisabledDays;
                            @endphp

                            <div class="row">
                                <div class="col-6">
                                    <div class="text-center p-2 border rounded">
                                        <h6 class="text-success">{{ $totalEnabledDays }} يوم</h6>
                                        <small class="text-muted">ضرائب مفعلة</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-2 border rounded">
                                        <h6 class="text-danger">{{ $totalDisabledDays }} يوم</h6>
                                        <small class="text-muted">ضرائب معطلة</small>
                                    </div>
                                </div>
                            </div>

                            @if($totalDays > 0)
                                <div class="mt-3">
                                    <div class="progress">
                                        <div class="progress-bar bg-success" style="width: {{ ($totalEnabledDays / $totalDays) * 100 }}%">
                                            {{ round(($totalEnabledDays / $totalDays) * 100, 1) }}%
                                        </div>
                                        <div class="progress-bar bg-danger" style="width: {{ ($totalDisabledDays / $totalDays) * 100 }}%">
                                            {{ round(($totalDisabledDays / $totalDays) * 100, 1) }}%
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-info">
                                <i class="fas fa-info-circle"></i> معلومات إضافية
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-calendar text-primary"></i>
                                    <strong>أول تفعيل:</strong>
                                    {{ $taxHistory->where('tax_enabled', true)->last()?->effective_from?->format('Y-m-d') ?? 'لم يتم التفعيل بعد' }}
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-calendar text-warning"></i>
                                    <strong>آخر تغيير:</strong>
                                    {{ $taxHistory->first()?->effective_from?->format('Y-m-d H:i') ?? 'لا يوجد' }}
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-user text-info"></i>
                                    <strong>آخر مستخدم:</strong>
                                    {{ $taxHistory->first()?->changedBy?->name ?? 'غير محدد' }}
                                </li>
                                <li>
                                    <i class="fas fa-sync text-success"></i>
                                    <strong>عدد التغييرات:</strong>
                                    {{ $taxHistory->count() }} مرة
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // تفعيل tooltips
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
