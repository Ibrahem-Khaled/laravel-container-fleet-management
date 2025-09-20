@extends('layouts.app')

@section('title', 'رواتب الموظفين')

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
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }
    .employee-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .employee-details {
        text-align: right;
    }
    .employee-name {
        font-weight: bold;
        margin-bottom: 2px;
    }
    .employee-phone {
        font-size: 0.85em;
        color: #6c757d;
    }
</style>
@endsection

@section('content')
<div class="row">
    <!-- العنوان والإحصائيات -->
    <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-users"></i> رواتب الموظفين
            </h1>
            <div class="text-muted">
                إجمالي الموظفين: <span class="badge badge-primary">{{ $users->total() }}</span>
            </div>
        </div>
    </div>

    <!-- إحصائيات عامة -->
    <div class="col-12 mb-4">
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    إجمالي الموظفين
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $users->total() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                    إجمالي الرواتب الشهرية
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($users->sum('salary'), 2) }} ر.س
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
                                    متوسط الراتب
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $users->count() > 0 ? number_format($users->avg('salary'), 2) : '0.00' }} ر.س
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
                                    أعلى راتب
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $users->count() > 0 ? number_format($users->max('salary'), 2) : '0.00' }} ر.س
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

    <!-- جدول الموظفين -->
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-list"></i> قائمة الموظفين
                </h6>
            </div>
            <div class="card-body">
                @if($users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>صورة الموظف</th>
                                    <th>اسم الموظف</th>
                                    <th>رقم الهاتف</th>
                                    <th>الراتب الشهري</th>
                                    <th>تاريخ التوظيف</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $index => $user)
                                    <tr>
                                        <td class="text-center">{{ $users->firstItem() + $index }}</td>
                                        <td class="text-center">
                                            <img src="{{ $user->avatar ?? 'https://via.placeholder.com/40x40/007bff/ffffff?text=' . substr($user->name, 0, 1) }}"
                                                 class="employee-avatar"
                                                 alt="{{ $user->name }}"
                                                 onerror="this.src='https://via.placeholder.com/40x40/007bff/ffffff?text=' + this.alt.charAt(0)">
                                        </td>
                                        <td class="text-right font-weight-bold">{{ $user->name }}</td>
                                        <td class="text-center">{{ $user->phone ?? '-' }}</td>
                                        <td class="text-right">
                                            <span class="font-weight-bold text-success">
                                                {{ number_format($user->salary, 2) }} ر.س
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            {{ $user->created_at ? $user->created_at->format('Y-m-d') : '-' }}
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('expenses.employees.show', $user) }}"
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> التفاصيل
                                                </a>
                                                <a href="{{ route('expenses.employees.tips', $user) }}"
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-gift"></i> التربات
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="thead-light">
                                <tr>
                                    <th colspan="4" class="text-right">الإجمالي:</th>
                                    <th class="text-right font-weight-bold text-success">
                                        {{ number_format($users->sum('salary'), 2) }} ر.س
                                    </th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $users->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد موظفين في النظام</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- ملخص إضافي -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">ملخص الرواتب</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold text-primary">إحصائيات الرواتب:</h6>
                        <ul class="list-unstyled">
                            <li><strong>إجمالي الموظفين:</strong> {{ $users->total() }}</li>
                            <li><strong>إجمالي الرواتب الشهرية:</strong> {{ number_format($users->sum('salary'), 2) }} ر.س</li>
                            <li><strong>متوسط الراتب:</strong> {{ $users->count() > 0 ? number_format($users->avg('salary'), 2) : '0.00' }} ر.س</li>
                            <li><strong>أعلى راتب:</strong> {{ $users->count() > 0 ? number_format($users->max('salary'), 2) : '0.00' }} ر.س</li>
                            <li><strong>أقل راتب:</strong> {{ $users->count() > 0 ? number_format($users->min('salary'), 2) : '0.00' }} ر.س</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold text-success">توزيع الرواتب:</h6>
                        @php
                            $salaryRanges = [
                                'أقل من 3000' => $users->where('salary', '<', 3000)->count(),
                                '3000 - 5000' => $users->whereBetween('salary', [3000, 5000])->count(),
                                '5000 - 8000' => $users->whereBetween('salary', [5000, 8000])->count(),
                                'أكثر من 8000' => $users->where('salary', '>', 8000)->count(),
                            ];
                        @endphp
                        @foreach($salaryRanges as $range => $count)
                            <div class="mb-2">
                                <strong>{{ $range }} ر.س:</strong> {{ $count }} موظف
                                @if($users->count() > 0)
                                    <small class="text-muted">({{ number_format(($count / $users->count()) * 100, 1) }}%)</small>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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
        const avatars = document.querySelectorAll('.employee-avatar');
        avatars.forEach(avatar => {
            avatar.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.1)';
                this.style.transition = 'transform 0.2s';
            });

            avatar.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });
    });
</script>
@endsection
