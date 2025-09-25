@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- العنوان ومسار التنقل --}}
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-hands-praying text-primary mr-2"></i>
                        الحمد لله
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('home') }}">لوحة التحكم</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">الحمد لله</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('alhamdulillah.details', ['year' => $year, 'month' => $month]) }}"
                       class="btn btn-info">
                        <i class="fas fa-list mr-1"></i> تفاصيل المصروفات
                    </a>
                    <form method="POST" action="{{ route('alhamdulillah.logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="fas fa-sign-out-alt mr-1"></i> خروج
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('components.alerts')

    {{-- فلترة السنة والشهر --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter mr-2"></i>
                فلترة البيانات
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('alhamdulillah.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="year" class="form-label">السنة</label>
                    <select name="year" id="year" class="form-control">
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="month" class="form-label">الشهر</label>
                    <select name="month" id="month" class="form-control">
                        @foreach($months as $m => $monthName)
                            <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>{{ $monthName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search mr-1"></i> عرض البيانات
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- إحصائيات الحاويات --}}
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                عدد الحاويات - {{ $months[$month] }} {{ $year }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($containerStats['monthly_count']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                إجمالي الحاويات - {{ $year }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($containerStats['yearly_count']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                متوسط شهري
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($containerStats['yearly_count'] / 12, 1) }}
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

    {{-- إعداد المصروفات --}}
    @if(!$expenseData)
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-cog mr-2"></i>
                إعداد المصروفات للشهر
            </h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('alhamdulillah.setup-expense') }}">
                @csrf
                <input type="hidden" name="year" value="{{ $year }}">
                <input type="hidden" name="month" value="{{ $month }}">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="amount_per_container" class="form-label">المبلغ لكل حاوية (ر.س)</label>
                            <input type="number"
                                   class="form-control @error('amount_per_container') is-invalid @enderror"
                                   id="amount_per_container"
                                   name="amount_per_container"
                                   step="0.01"
                                   min="0"
                                   required>
                            @error('amount_per_container')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="notes" class="form-label">ملاحظات</label>
                            <textarea class="form-control"
                                      id="notes"
                                      name="notes"
                                      rows="3"
                                      placeholder="ملاحظات إضافية..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>عدد الحاويات:</strong> {{ number_format($containerStats['monthly_count']) }} حاوية
                    <br>
                    <strong>المبلغ الإجمالي المتوقع:</strong> <span id="total-amount-preview">0</span> ر.س
                </div>

                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save mr-1"></i>
                    إعداد المصروفات
                </button>
            </form>
        </div>
    </div>
    @endif

    {{-- بيانات المصروفات --}}
    @if($expenseData)
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-money-bill-wave mr-2"></i>
                بيانات المصروفات - {{ $months[$month] }} {{ $year }}
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="text-center p-3 bg-light rounded">
                        <h5 class="text-primary">{{ number_format($expenseData->container_count) }}</h5>
                        <small class="text-muted">عدد الحاويات</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center p-3 bg-light rounded">
                        <h5 class="text-success">{{ number_format($expenseData->amount_per_container, 2) }}</h5>
                        <small class="text-muted">المبلغ لكل حاوية (ر.س)</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center p-3 bg-light rounded">
                        <h5 class="text-info">{{ number_format($expenseData->total_amount, 2) }}</h5>
                        <small class="text-muted">المبلغ الإجمالي (ر.س)</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center p-3 bg-light rounded">
                        <h5 class="text-warning">{{ number_format($expenseData->spent_amount, 2) }}</h5>
                        <small class="text-muted">المبلغ المصروف (ر.س)</small>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="alert alert-success">
                        <h6 class="alert-heading">
                            <i class="fas fa-check-circle mr-2"></i>
                            المبلغ المتبقي
                        </h6>
                        <h4 class="mb-0">{{ number_format($expenseData->remaining_amount, 2) }} ر.س</h4>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="fas fa-percentage mr-2"></i>
                            نسبة الصرف
                        </h6>
                        <h4 class="mb-0">{{ number_format(($expenseData->spent_amount / $expenseData->total_amount) * 100, 1) }}%</h4>
                    </div>
                </div>
            </div>

            {{-- إضافة مصروف جديد --}}
            <div class="mt-4">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>ملاحظة:</strong> المبلغ المصروف يتم حسابه تلقائياً من إجمالي أسعار الحاويات لنفس الشهر والسنة.
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- إحصائيات شهرية للسنة --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-chart-bar mr-2"></i>
                إحصائيات شهرية - {{ $year }}
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($months as $m => $monthName)
                    @php
                        $monthData = $containerStats['monthly_stats']->get($m);
                        $count = $monthData ? $monthData->count : 0;
                    @endphp
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card border-left-info h-100">
                            <div class="card-body p-3">
                                <div class="text-center">
                                    <h6 class="text-muted mb-1">{{ $monthName }}</h6>
                                    <h4 class="text-primary mb-0">{{ number_format($count) }}</h4>
                                    <small class="text-muted">حاوية</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // حساب المبلغ الإجمالي المتوقع
    document.getElementById('amount_per_container').addEventListener('input', function() {
        const amountPerContainer = parseFloat(this.value) || 0;
        const containerCount = {{ $containerStats['monthly_count'] }};
        const totalAmount = amountPerContainer * containerCount;

        document.getElementById('total-amount-preview').textContent = totalAmount.toLocaleString('ar-SA', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    });
</script>
@endpush

@push('styles')
<style>
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }
    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }

    .card {
        border-radius: 15px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
    }
</style>
@endpush
@endsection
