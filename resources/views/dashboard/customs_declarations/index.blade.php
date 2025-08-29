@extends('layouts.app')

@section('content')
    <div class="container-fluid" dir="rtl">
        {{-- عنوان الصفحة ومسار التنقل --}}
        <div class="row">
            <div class="col-12">
                <h1 class="h3 mb-0 text-gray-800">البيانات الجمركية</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">لوحة التحكم</a></li>
                        <li class="breadcrumb-item active" aria-current="page">البيانات الجمركية</li>
                    </ol>
                </nav>
            </div>
        </div>

        {{-- تنبيهات عامة --}}
        @include('components.alerts')

        {{-- كروت الإحصائيات --}}
        <div class="row mb-4">

            <x-stats-card icon="fas fa-file-invoice" title="إجمالي البيانات" :value="$totalCount" color="primary" />


            <x-stats-card icon="fas fa-hourglass-half" title="تنتهي خلال 7 أيام" :value="$expiringSoon" color="warning" />


            <x-stats-card icon="fas fa-exclamation-circle" title="منتهية" :value="$expiredCount" color="danger" />


            <x-stats-card icon="fas fa-weight-hanging" title="إجمالي الأوزان (طن)" :value="number_format($totalWeight, 2)" color="info" />

        </div>

        {{-- بطاقة القائمة --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">قائمة البيانات الجمركية</h6>
                <button class="btn btn-outline-info" data-toggle="tooltip" title="الحالات، البحث، والعلاقات تُجلب تلقائياً">
                    <i class="fas fa-info-circle"></i>
                </button>
            </div>

            <div class="card-body">
                {{-- تبويبات الحالات (ديناميكي) --}}
                <ul class="nav nav-tabs mb-4">
                    @php
                        $mapBadge = fn($s) => match ($s) {
                            'cleared', 'released' => 'success',
                            'pending' => 'warning',
                            'canceled', 'rejected' => 'secondary',
                            default => 'info',
                        };
                    @endphp

                    <li class="nav-item">
                        <a class="nav-link {{ ($selectedStatus ?? 'all') === 'all' ? 'active' : '' }}"
                            href="{{ route('customs.index', array_filter(['q' => $q ?: null])) }}">
                            الكل
                            <span class="badge badge-secondary">{{ $totalCount }}</span>
                        </a>
                    </li>

                    @foreach ($statuses as $st)
                        <li class="nav-item">
                            <a class="nav-link {{ $selectedStatus === $st ? 'active' : '' }}"
                                href="{{ route('customs.index', array_filter(['q' => $q ?: null, 'status' => $st])) }}">
                                {{ $st ?: 'غير مُحدد' }}
                                <span class="badge badge-{{ $mapBadge($st) }}">
                                    {{-- عدّاد بسيط لكل حالة (اختياري يمكن استبداله بكويري منفصل) --}}
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>

                {{-- نموذج البحث --}}
                <form action="{{ route('customs.index') }}" method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="hidden" name="status" value="{{ $selectedStatus ?? 'all' }}">
                        <input type="text" name="q" class="form-control"
                            placeholder="ابحث برقم البيان / العميل / مكتب التخليص..." value="{{ $q }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> بحث</button>
                            @if (!empty($q))
                                <a href="{{ route('customs.index', ['status' => $selectedStatus !== 'all' ? $selectedStatus : null]) }}"
                                    class="btn btn-outline-secondary">مسح</a>
                            @endif
                        </div>
                    </div>
                </form>

                {{-- الجدول --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>رقم البيان</th>
                                <th>الحاويات</th>
                                <th>الوزن</th>
                                <th>العميل</th>
                                <th>مكتب التخليص</th>
                                <th>تاريخ ارضية الجمرك</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($declarations as $dec)
                                @php
                                    $badge = $mapBadge($dec->statement_status);
                                @endphp
                                <tr>
                                    <td>{{ $dec->statement_number }}</td>
                                    <td><span class="badge badge-info">{{ $dec->containers_count }}</span></td>
                                    <td>{{ $dec->weight ?? '-' }}</td>
                                    <td>{{ $dec->client?->name ?? '-' }}</td>
                                    <td>{{ $dec->clearanceOffice?->name ?? '-' }}</td>
                                    <td>{{ $dec->expire_date ? \Illuminate\Support\Carbon::parse($dec->expire_date)->format('Y-m-d') : '-' }}
                                    </td>
                                    <td><span
                                            class="badge badge-{{ $badge }}">{{ $dec->statement_status ?? '-' }}</span>
                                    </td>
                                    <td class="text-center">
                                        {{-- عرض --}}
                                        <button type="button" class="btn btn-sm btn-circle btn-info" data-toggle="modal"
                                            data-target="#showDec{{ $dec->id }}" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        {{-- تضمين مودال العرض --}}
                                        @include('dashboard.customs_declarations.modals.show', [
                                            'dec' => $dec,
                                        ])
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">لا توجد بيانات مطابقة.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- الترقيم --}}
                <div class="d-flex justify-content-center">
                    {{ $declarations->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
