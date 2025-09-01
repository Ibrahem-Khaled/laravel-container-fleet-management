@extends('layouts.app')

@section('content')
    {{-- نوصي بإضافة dir="rtl" و lang="ar" في ملف layout.app الرئيسي لدعم كامل للغة العربية --}}
    {{-- <html lang="ar" dir="rtl"> --}}

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">تفاصيل مكتب: {{ $clearance_office->name }}</h1>
            <a href="{{ route('clearance-offices.index') }}" class="btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50 ml-1"></i> العودة لقائمة المكاتب
            </a>
        </div>

        <div class="row">
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-info-circle mr-2"></i>بطاقة معلومات المكتب
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <img class="img-fluid rounded-circle" style="width: 150px; height: 150px; object-fit: cover;"
                                src="{{ $clearance_office->avatar ? asset('storage/' . $clearance_office->avatar) : asset('img/default-avatar.png') }}"
                                alt="صورة المكتب {{ $clearance_office->name }}">
                        </div>

                        {{-- تفاصيل المكتب --}}
                        <div class="px-2">
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <h6 class="font-weight-bold text-muted mb-0">الاسم:</h6>
                                <span class="text-dark">{{ $clearance_office->name }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <h6 class="font-weight-bold text-muted mb-0">البريد الإلكتروني:</h6>
                                <span class="text-dark">{{ $clearance_office->email ?? 'غير متوفر' }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <h6 class="font-weight-bold text-muted mb-0">الهاتف:</h6>
                                <span class="text-dark">{{ $clearance_office->phone ?? 'غير متوفر' }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <h6 class="font-weight-bold text-muted mb-0">الرقم التشغيلي:</h6>
                                <span class="text-dark">{{ $clearance_office->operational_number ?? 'غير متوفر' }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-2">
                                <h6 class="font-weight-bold text-muted mb-0">الحالة:</h6>
                                <span
                                    class="badge badge-pill px-3 py-1 badge-{{ $clearance_office->is_active ? 'success' : 'danger' }}">
                                    {{ $clearance_office->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-file-invoice mr-2"></i>البيانات الجمركية المسجلة
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover text-right"> {{-- text-right لدعم العربية --}}
                                <thead>
                                    <tr>
                                        <th>رقم البيان</th>
                                        <th>العميل</th>
                                        <th>عدد الحاويات</th>
                                        <th>تاريخ الإنشاء</th>
                                        <th class="text-center">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($clearance_office->customsDeclarations as $declaration)
                                        <tr>
                                            <td class="font-weight-bold align-middle">{{ $declaration->statement_number }}
                                            </td>
                                            <td class="align-middle">{{ $declaration->client->name ?? 'غير محدد' }}</td>
                                            <td class="align-middle">
                                                <span
                                                    class="badge badge-info px-2">{{ $declaration->containers->count() }}</span>
                                            </td>
                                            <td class="align-middle">{{ $declaration->created_at->format('Y-m-d') }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('customs.containers', $declaration) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye fa-sm"></i> عرض الحاويات
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        {{-- رسالة محسنة في حالة عدم وجود بيانات --}}
                                        <tr>
                                            <td colspan="5" class="text-center py-5">
                                                <div class="py-3">
                                                    <i class="fas fa-folder-open fa-3x text-gray-400 mb-3"></i>
                                                    <h5 class="text-gray-600">لا توجد بيانات جمركية مسجلة</h5>
                                                    <p class="text-muted small">لم يتم إضافة أي بيانات جمركية تابعة لهذا
                                                        المكتب حتى الآن.</p>
                                                </div>
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
