@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-1 text-gray-800">تفاصيل حاويات البيان الجمركي</h1>
                <p class="mb-0 text-muted">
                    رقم البيان: <span
                        class="font-weight-bold text-primary">{{ $customs_declaration->declaration_number }}</span> |
                    تاريخه: <span
                        class="font-weight-bold">{{ optional($customs_declaration->declaration_date)->format('d/m/Y') }}</span>
                </p>
            </div>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-right fa-sm text-white-50"></i> رجوع
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">إجمالي الحاويات</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['containers_count'] }}</div>
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
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">القيمة الإجمالية
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($stats['total_value'], 2) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
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
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">حاويات تم نقلها</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['transported_count'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-truck-loading fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Containers Table Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">قائمة الحاويات</h6>
            </div>
            <div class="card-body">
                @if ($customs_declaration->containers->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>رقم الحاوية</th>
                                    {{-- <th>السعر</th> --}}
                                    <th>الحالة</th>
                                    <th>تاريخ النقل</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customs_declaration->containers as $container)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $container->number }}</td>
                                        {{-- <td>{{ number_format($container->price, 2) }}</td> --}}
                                        <td>
                                            @if ($container->status == 'done')
                                                <span class="badge badge-success">تم التسليم</span>
                                            @elseif($container->status == 'transport')
                                                <span class="badge badge-info">قيد النقل</span>
                                            @else
                                                <span class="badge badge-warning">في الانتظار</span>
                                            @endif
                                        </td>
                                        <td>{{ optional($container->transfer_date)->format('Y-m-d H:i') ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center p-5">
                        <i class="fas fa-box-open fa-3x text-gray-400 mb-3"></i>
                        <p class="text-muted">لا توجد حاويات مرتبطة بهذا البيان الجمركي حتى الآن.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
