@extends('layouts.app')

@section('title', 'تربات الموظف - ' . $user->name)

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
        width: 60px;
        height: 60px;
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
    .tip-amount {
        color: #17a2b8;
        font-weight: bold;
    }
    .filter-form {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .filter-form .form-control {
        margin-bottom: 10px;
    }
    .tip-type-badge {
        font-size: 0.75em;
        padding: 4px 8px;
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
                    <i class="fas fa-gift"></i> تربات الموظف
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2 text-center">
                        <img src="{{ $user->avatar ?? 'https://via.placeholder.com/60x60/007bff/ffffff?text=' . substr($user->name, 0, 1) }}"
                             class="employee-avatar mb-3"
                             alt="{{ $user->name }}"
                             onerror="this.src='https://via.placeholder.com/60x60/007bff/ffffff?text=' + this.alt.charAt(0)">
                    </div>
                    <div class="col-md-10">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="font-weight-bold text-primary">معلومات الموظف:</h6>
                                <ul class="list-unstyled">
                                    <li><strong>اسم الموظف:</strong> {{ $user->name }}</li>
                                    <li><strong>رقم الهاتف:</strong> {{ $user->phone ?? 'غير محدد' }}</li>
                                    <li><strong>الراتب الشهري:</strong> {{ number_format($user->salary, 2) }} ر.س</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="font-weight-bold text-success">ملخص التربات:</h6>
                                <ul class="list-unstyled">
                                    <li><strong>إجمالي التربات:</strong> {{ number_format($totalPrice, 2) }} ر.س</li>
                                    <li><strong>عدد التربات:</strong> {{ $tipsCount }}</li>
                                    <li><strong>متوسط التربات:</strong> {{ $tipsCount > 0 ? number_format($totalPrice / $tipsCount, 2) : '0.00' }} ر.س</li>
                                    <li><strong>الفترة:</strong> {{ \Illuminate\Support\Carbon::parse($from)->format('Y-m-d') }} إلى {{ \Illuminate\Support\Carbon::parse($to)->format('Y-m-d') }}</li>
                                    @if(\Illuminate\Support\Carbon::parse($from)->isCurrentMonth() && \Illuminate\Support\Carbon::parse($to)->isCurrentMonth())
                                        <li><span class="badge badge-success">الشهر الحالي</span></li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- فلتر البحث -->
    <div class="col-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">
                    <i class="fas fa-filter"></i> فلتر البحث
                </h6>
            </div>
            <div class="card-body">
                <form method="get" class="filter-form">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="from" class="form-label">من تاريخ</label>
                            <input type="date" name="from" id="from" class="form-control"
                                value="{{ request('from') ?? \Illuminate\Support\Carbon::parse($from)->toDateString() }}">
                        </div>
                        <div class="col-md-3">
                            <label for="to" class="form-label">إلى تاريخ</label>
                            <input type="date" name="to" id="to" class="form-control"
                                value="{{ request('to') ?? \Illuminate\Support\Carbon::parse($to)->toDateString() }}">
                        </div>
                        <div class="col-md-2">
                            <label for="container_id" class="form-label">رقم الحاوية</label>
                            <input type="number" name="container_id" id="container_id" class="form-control"
                                placeholder="رقم الحاوية" value="{{ request('container_id') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="car_id" class="form-label">رقم السيارة</label>
                            <input type="number" name="car_id" id="car_id" class="form-control"
                                placeholder="رقم السيارة" value="{{ request('car_id') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="type" class="form-label">نوع التربات</label>
                            <input type="text" name="type" id="type" class="form-control"
                                placeholder="نوع التربات" value="{{ request('type') }}">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> تطبيق الفلتر
                            </button>
                            <a href="{{ route('expenses.employees.tips', $user) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> إلغاء الفلتر
                            </a>
                            <a href="{{ route('expenses.employees.tips', $user) }}" class="btn btn-success">
                                <i class="fas fa-calendar"></i> الشهر الحالي
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="col-12 mb-4">
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    إجمالي التربات
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($totalPrice, 2) }} ر.س
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
                                    عدد التربات
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $tipsCount }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-list fa-2x text-gray-300"></i>
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
                                    متوسط التربات
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $tipsCount > 0 ? number_format($totalPrice / $tipsCount, 2) : '0.00' }} ر.س
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
                                    أعلى تربة
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $tips->count() > 0 ? number_format($tips->max('price'), 2) : '0.00' }} ر.س
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-crown fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول التربات -->
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-list"></i> قائمة التربات التفصيلية
                </h6>
            </div>
            <div class="card-body">
                @if($tips->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>التاريخ</th>
                                    <th>الوقت</th>
                                    <th>اسم العميل</th>
                                    <th>رقم الحاوية</th>
                                    <th>حجم الحاوية</th>
                                    <th>رقم السيارة</th>
                                    <th>نوع التربات</th>
                                    <th>مبلغ التربات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tips as $index => $tip)
                                    <tr>
                                        <td class="text-center">{{ $tips->firstItem() + $index }}</td>
                                        <td class="text-center">{{ $tip->created_at->format('Y-m-d') }}</td>
                                        <td class="text-center">{{ $tip->created_at->format('H:i:s') }}</td>
                                        <td class="text-right">
                                            {{ optional(optional(optional($tip->container)->customs)->client)->name ?? 'غير محدد' }}
                                        </td>
                                        <td class="text-center">
                                            @if ($tip->container)
                                                {{ $tip->container->number ?? $tip->container->id }}
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($tip->container)
                                                <span class="badge badge-info">{{ $tip->container->size ?? 'غير محدد' }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($tip->car)
                                                {{ $tip->car->number ?? $tip->car->id }}
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($tip->type)
                                                <span class="badge tip-type-badge badge-secondary">{{ $tip->type }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-right tip-amount">{{ number_format($tip->price, 2) }} ر.س</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="thead-light">
                                <tr>
                                    <th colspan="8" class="text-right">الإجمالي:</th>
                                    <th class="text-right tip-amount">{{ number_format($totalPrice, 2) }} ر.س</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $tips->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-gift fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد تربات في الفترة المحددة</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- ملخص إضافي -->
@if($tips->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">ملخص إضافي</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h6 class="font-weight-bold text-primary">توزيع التربات:</h6>
                        @php
                            $dailyTips = $tips->groupBy(function($tip) {
                                return $tip->created_at->format('Y-m-d');
                            })->map(function($group) {
                                return [
                                    'count' => $group->count(),
                                    'total' => $group->sum('price')
                                ];
                            })->take(5);
                        @endphp
                        @foreach($dailyTips as $date => $dayStats)
                            <div class="mb-2">
                                <strong>{{ $date }}:</strong>
                                {{ $dayStats['count'] }} تربة -
                                {{ number_format($dayStats['total'], 2) }} ر.س
                            </div>
                        @endforeach
                    </div>
                    <div class="col-md-4">
                        <h6 class="font-weight-bold text-success">توزيع حسب نوع التربات:</h6>
                        @php
                            $typeStats = $tips->groupBy('type')->map(function($group) {
                                return [
                                    'count' => $group->count(),
                                    'total' => $group->sum('price')
                                ];
                            });
                        @endphp
                        @foreach($typeStats as $type => $typeData)
                            <div class="mb-2">
                                <strong>{{ $type ?: 'غير محدد' }}:</strong>
                                {{ $typeData['count'] }} تربة -
                                {{ number_format($typeData['total'], 2) }} ر.س
                            </div>
                        @endforeach
                    </div>
                    <div class="col-md-4">
                        <h6 class="font-weight-bold text-info">إحصائيات الحاويات:</h6>
                        @php
                            $containerStats = $tips->whereNotNull('container_id')->groupBy('container.size')->map(function($group) {
                                return $group->count();
                            });
                        @endphp
                        @foreach($containerStats as $size => $count)
                            <div class="mb-2">
                                <strong>{{ $size ?: 'غير محدد' }}:</strong> {{ $count }} تربة
                            </div>
                        @endforeach
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
