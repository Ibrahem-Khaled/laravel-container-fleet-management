@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- العنوان ومسار التنقل --}}
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-list text-primary mr-2"></i>
                        تفاصيل المصروفات - {{ $expenseData->month_name }} {{ $year }}
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('home') }}">لوحة التحكم</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('alhamdulillah.index') }}">الحمد لله</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">تفاصيل المصروفات</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('alhamdulillah.index', ['year' => $year, 'month' => $month]) }}"
                       class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> العودة
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

    {{-- ملخص المصروفات --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                عدد الحاويات
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($expenseData->container_count) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
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
                                المبلغ الإجمالي
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($expenseData->total_amount, 2) }} ر.س
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
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                المبلغ المصروف
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($expenseData->spent_amount, 2) }} ر.س
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-credit-card fa-2x text-gray-300"></i>
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
                                المبلغ المتبقي
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($expenseData->remaining_amount, 2) }} ر.س
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-piggy-bank fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- تفاصيل المصروفات --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-receipt mr-2"></i>
                تفاصيل المصروفات
            </h6>
        </div>
        <div class="card-body">
            @if($expenseData->notes)
                <div class="row">
                    <div class="col-12">
                        <h6 class="font-weight-bold mb-3">سجل المصروفات:</h6>
                        <div class="bg-light p-3 rounded">
                            <pre class="mb-0" style="white-space: pre-wrap; font-family: inherit;">{{ $expenseData->notes }}</pre>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center text-muted py-4">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p>لا توجد ملاحظات إضافية</p>
                </div>
            @endif
        </div>
    </div>

    {{-- معلومات إضافية --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-info-circle mr-2"></i>
                معلومات إضافية
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td class="font-weight-bold">المبلغ لكل حاوية:</td>
                            <td>{{ number_format($expenseData->amount_per_container, 2) }} ر.س</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">تاريخ الإنشاء:</td>
                            <td>{{ $expenseData->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">آخر تحديث:</td>
                            <td>{{ $expenseData->updated_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="progress mb-3" style="height: 25px;">
                        <div class="progress-bar bg-success"
                             role="progressbar"
                             style="width: {{ ($expenseData->spent_amount / $expenseData->total_amount) * 100 }}%"
                             aria-valuenow="{{ ($expenseData->spent_amount / $expenseData->total_amount) * 100 }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                            {{ number_format(($expenseData->spent_amount / $expenseData->total_amount) * 100, 1) }}%
                        </div>
                    </div>
                    <small class="text-muted">
                        نسبة الصرف من إجمالي المبلغ المخصص
                    </small>
                </div>
            </div>

            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>ملاحظة:</strong> المبلغ المصروف يتم حسابه تلقائياً من إجمالي أسعار الحاويات لنفس الشهر والسنة.
            </div>
        </div>
    </div>
</div>

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

    .progress {
        border-radius: 10px;
    }

    .progress-bar {
        border-radius: 10px;
    }
</style>
@endpush
@endsection
