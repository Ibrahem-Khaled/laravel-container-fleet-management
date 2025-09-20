@extends('layouts.app')

@section('title', 'تفاصيل الموظف - ' . $user->name)

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
    .employee-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
    }
    .back-link {
        color: #007bff;
        text-decoration: none;
    }
    .back-link:hover {
        color: #0056b3;
        text-decoration: underline;
    }
    .month-name {
        font-weight: bold;
        color: #495057;
    }
    .salary-amount {
        color: #28a745;
        font-weight: bold;
    }
    .withdrawal-amount {
        color: #dc3545;
        font-weight: bold;
    }
    .tip-amount {
        color: #17a2b8;
        font-weight: bold;
    }
    .net-amount {
        color: #6c757d;
        font-weight: bold;
    }
    .balance-amount {
        color: #007bff;
        font-weight: bold;
    }
</style>
@endsection

@section('content')
<div class="row">
    <!-- زر العودة -->
    <div class="col-12 mb-3">
        <a href="{{ route('expenses.employees.index') }}" class="back-link">
            <i class="fas fa-arrow-right"></i> العودة إلى رواتب الموظفين
        </a>
    </div>

    <!-- معلومات الموظف -->
    <div class="col-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-user"></i> معلومات الموظف
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center">
                        <img src="{{ $user->avatar ?? 'https://via.placeholder.com/80x80/007bff/ffffff?text=' . substr($user->name, 0, 1) }}"
                             class="employee-avatar mb-3"
                             alt="{{ $user->name }}"
                             onerror="this.src='https://via.placeholder.com/80x80/007bff/ffffff?text=' + this.alt.charAt(0)">
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="font-weight-bold text-primary">البيانات الشخصية:</h6>
                                <ul class="list-unstyled">
                                    <li><strong>اسم الموظف:</strong> {{ $user->name }}</li>
                                    <li><strong>رقم الهاتف:</strong> {{ $user->phone ?? 'غير محدد' }}</li>
                                    <li><strong>البريد الإلكتروني:</strong> {{ $user->email ?? 'غير محدد' }}</li>
                                    <li><strong>تاريخ التوظيف:</strong> {{ $user->created_at ? $user->created_at->format('Y-m-d') : 'غير محدد' }}</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="font-weight-bold text-success">البيانات المالية:</h6>
                                <ul class="list-unstyled">
                                    <li><strong>الراتب الشهري:</strong> {{ number_format($user->salary, 2) }} ر.س</li>
                                    <li><strong>الفترة المشمولة:</strong> {{ $periodStart->format('Y-m-d') }} إلى {{ $periodEnd->format('Y-m-d') }}</li>
                                    <li><strong>عدد الأشهر:</strong> {{ count($monthlyRows) }} شهر</li>
                                    <li><strong>الرصيد الحالي:</strong> {{ number_format($totals['carry'], 2) }} ر.س</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="col-12 mb-4">
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    إجمالي الرواتب
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($totals['salary'], 2) }} ر.س
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
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    إجمالي السحوبات
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($totals['withdrawals'], 2) }} ر.س
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
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
                                    إجمالي التربات
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($totals['tips'], 2) }} ر.س
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-gift fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    الرصيد المُرحّل
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($totals['carry'], 2) }} ر.س
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-wallet fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول التفاصيل الشهرية -->
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-calendar-alt"></i> التفاصيل الشهرية
                </h6>
            </div>
            <div class="card-body">
                @if(count($monthlyRows) > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>الشهر</th>
                                    <th>الراتب</th>
                                    <th>سحب اليومية</th>
                                    <th>التربات</th>
                                    <th>صافي الشهر</th>
                                    <th>الرصيد المُرحّل</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthlyRows as $index => $row)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td class="text-center month-name">
                                            {{ \App\Http\Controllers\system\TaxController::getArabicMonthName(date('n', strtotime($row['ym'] . '-01'))) }} {{ date('Y', strtotime($row['ym'] . '-01')) }}
                                        </td>
                                        <td class="text-right salary-amount">{{ number_format($row['salary'], 2) }} ر.س</td>
                                        <td class="text-right withdrawal-amount">{{ number_format($row['withdrawals'], 2) }} ر.س</td>
                                        <td class="text-right tip-amount">{{ number_format($row['tips'], 2) }} ر.س</td>
                                        <td class="text-right net-amount">{{ number_format($row['net'], 2) }} ر.س</td>
                                        <td class="text-right balance-amount">{{ number_format($row['carry'], 2) }} ر.س</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="thead-light">
                                <tr class="font-weight-bold">
                                    <th colspan="2" class="text-right">الإجمالي:</th>
                                    <th class="text-right salary-amount">{{ number_format($totals['salary'], 2) }} ر.س</th>
                                    <th class="text-right withdrawal-amount">{{ number_format($totals['withdrawals'], 2) }} ر.س</th>
                                    <th class="text-right tip-amount">{{ number_format($totals['tips'], 2) }} ر.س</th>
                                    <th class="text-right net-amount">{{ number_format($totals['net'], 2) }} ر.س</th>
                                    <th class="text-right balance-amount">{{ number_format($totals['carry'], 2) }} ر.س</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد بيانات شهرية لهذا الموظف</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- ملخص إضافي -->
@if(count($monthlyRows) > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">ملخص إضافي</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h6 class="font-weight-bold text-primary">متوسطات شهرية:</h6>
                        <ul class="list-unstyled">
                            <li><strong>متوسط السحوبات:</strong> {{ number_format($totals['withdrawals'] / count($monthlyRows), 2) }} ر.س</li>
                            <li><strong>متوسط التربات:</strong> {{ number_format($totals['tips'] / count($monthlyRows), 2) }} ر.س</li>
                            <li><strong>متوسط الصافي:</strong> {{ number_format($totals['net'] / count($monthlyRows), 2) }} ر.س</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h6 class="font-weight-bold text-success">أفضل شهر:</h6>
                        @php
                            $bestMonth = collect($monthlyRows)->sortByDesc('net')->first();
                        @endphp
                        @if($bestMonth)
                            <ul class="list-unstyled">
                                <li><strong>الشهر:</strong> {{ \App\Http\Controllers\system\TaxController::getArabicMonthName(date('n', strtotime($bestMonth['ym'] . '-01'))) }} {{ date('Y', strtotime($bestMonth['ym'] . '-01')) }}</li>
                                <li><strong>الصافي:</strong> {{ number_format($bestMonth['net'], 2) }} ر.س</li>
                                <li><strong>التربات:</strong> {{ number_format($bestMonth['tips'], 2) }} ر.س</li>
                            </ul>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <h6 class="font-weight-bold text-danger">أسوأ شهر:</h6>
                        @php
                            $worstMonth = collect($monthlyRows)->sortBy('net')->first();
                        @endphp
                        @if($worstMonth)
                            <ul class="list-unstyled">
                                <li><strong>الشهر:</strong> {{ \App\Http\Controllers\system\TaxController::getArabicMonthName(date('n', strtotime($worstMonth['ym'] . '-01'))) }} {{ date('Y', strtotime($worstMonth['ym'] . '-01')) }}</li>
                                <li><strong>الصافي:</strong> {{ number_format($worstMonth['net'], 2) }} ر.س</li>
                                <li><strong>السحوبات:</strong> {{ number_format($worstMonth['withdrawals'], 2) }} ر.س</li>
                            </ul>
                        @endif
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

        // إضافة تأثير hover للصور
        const avatar = document.querySelector('.employee-avatar');
        if (avatar) {
            avatar.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.05)';
                this.style.transition = 'transform 0.2s';
            });

            avatar.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        }
    });
</script>
@endsection
