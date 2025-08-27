@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">تفاصيل مكتب: {{ $clearance_office->name }}</h1>
            <a href="{{ route('clearance-offices.index') }}" class="btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> العودة لقائمة المكاتب
            </a>
        </div>

        <div class="row">
            <!-- Office Info Card -->
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">بطاقة معلومات المكتب</h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <img class="img-fluid rounded-circle" style="width: 150px; height: 150px; object-fit: cover;"
                                src="{{ $clearance_office->avatar ? asset('storage/' . $clearance_office->avatar) : asset('img/default-avatar.png') }}"
                                alt="{{ $clearance_office->name }}">
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>الاسم:</strong> {{ $clearance_office->name }}</li>
                            <li class="list-group-item"><strong>البريد:</strong> {{ $clearance_office->email ?? '-' }}</li>
                            <li class="list-group-item"><strong>الهاتف:</strong> {{ $clearance_office->phone ?? '-' }}</li>
                            <li class="list-group-item"><strong>الرقم التشغيلي:</strong>
                                {{ $clearance_office->operational_number ?? '-' }}</li>
                            <li class="list-group-item"><strong>الحالة:</strong>
                                <span class="badge badge-{{ $clearance_office->is_active ? 'success' : 'danger' }}">
                                    {{ $clearance_office->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Customs Declarations List -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">البيانات الجمركية المسجلة</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>رقم البيان</th>
                                        <th>العميل</th>
                                        <th>عدد الحاويات</th>
                                        <th>تاريخ الإنشاء</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($clearance_office->customsDeclarations as $declaration)
                                        <tr>
                                            <td><strong>{{ $declaration->statement_number }}</strong></td>
                                            <td>{{ $declaration->client->name ?? 'غير محدد' }}</td>
                                            <td><span
                                                    class="badge badge-info">{{ $declaration->containers->count() }}</span>
                                            </td>
                                            <td>{{ $declaration->created_at->format('Y-m-d') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4">لا توجد بيانات جمركية لهذا المكتب.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
