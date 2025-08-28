@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        {{-- عنوان الصفحة ومسار التنقل --}}
        <div class="row">
            <div class="col-12">
                <h1 class="h3 mb-0 text-gray-800">إدارة السيارات</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home') }}">لوحة التحكم</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">السيارات</li>
                    </ol>
                </nav>
            </div>
        </div>

        @include('components.alerts')

        {{-- إحصائيات السيارات --}}
        <div class="row mb-4">
            <x-stats-card icon="fas fa-car" title="إجمالي السيارات" :value="$stats['total']" color="primary" />
            <x-stats-card icon="fas fa-shuttle-van" title="سيارات النقل" :value="$stats['transfer']" color="info" />
            <x-stats-card icon="fas fa-car-side" title="سيارات خاصة" :value="$stats['private']" color="success" />
            <x-stats-card icon="fas fa-user-tie" title="سيارات بسائق" :value="$stats['with_driver']" color="warning" />
        </div>

        {{-- بطاقة قائمة السيارات --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">قائمة السيارات</h6>
                <button class="btn btn-primary" data-toggle="modal" data-target="#createCarModal">
                    <i class="fas fa-plus"></i> إضافة سيارة
                </button>
            </div>
            <div class="card-body">
                {{-- تبويب الأنواع --}}
                <ul class="nav nav-tabs mb-4">
                    <li class="nav-item">
                        <a class="nav-link {{ $selectedType === 'all' ? 'active' : '' }}"
                            href="{{ route('cars.index') }}">الكل</a>
                    </li>
                    @foreach ($types as $type)
                        <li class="nav-item">
                            <a class="nav-link {{ $selectedType === $type ? 'active' : '' }}"
                                href="{{ route('cars.index', ['type' => $type]) }}">
                                {{ $type === 'transfer' ? 'نقل' : 'خاص' }}
                            </a>
                        </li>
                    @endforeach
                </ul>

                {{-- نموذج البحث --}}
                <form action="{{ route('cars.index') }}" method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control"
                            placeholder="ابحث بنوع السيارة، الموديل، الرقم التسلسلي، رقم اللوحة أو اسم السائق..."
                            value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i> بحث
                            </button>
                        </div>
                    </div>
                </form>

                {{-- جدول السيارات --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>رقم اللوحة</th>
                                <th>النوع</th>
                                <th>نوع السيارة</th>
                                <th>الموديل</th>
                                <th>السائق</th>
                                <th>انتهاء الرخصة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cars as $car)
                                <tr>
                                    <td><span class="badge badge-secondary p-2">{{ $car->number }}</span></td>
                                    <td>{{ $car->type === 'transfer' ? 'نقل' : 'خاص' }}</td>
                                    <td>{{ $car->type_car ?? '-' }}</td>
                                    <td>{{ $car->model_car ?? '-' }}</td>
                                    <td>{{ $car->driver->name ?? 'غير معين' }}</td>
                                    <td>{{ $car->license_expire ? \Carbon\Carbon::parse($car->license_expire)->format('Y-m-d') : '-' }}
                                    </td>
                                    <td>
                                        {{-- زر عرض --}}
                                        <button type="button" class="btn btn-sm btn-circle btn-info" data-toggle="modal"
                                            data-target="#showCarModal{{ $car->id }}" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        {{-- زر تعديل --}}
                                        <button type="button" class="btn btn-sm btn-circle btn-primary" data-toggle="modal"
                                            data-target="#editCarModal{{ $car->id }}" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        {{-- زر حذف --}}
                                        <button type="button" class="btn btn-sm btn-circle btn-danger" data-toggle="modal"
                                            data-target="#deleteCarModal{{ $car->id }}" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                        {{-- تضمين المودالات لكل سيارة --}}
                                        @include('dashboard.cars.modals.show', ['car' => $car])
                                        @include('dashboard.cars.modals.edit', [
                                            'car' => $car,
                                            'availableDrivers' => $availableDrivers,
                                        ])
                                        @include('dashboard.cars.modals.delete', ['car' => $car])
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">لا توجد سيارات لعرضها</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- الترقيم --}}
                <div class="d-flex justify-content-center">
                    {{ $cars->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- مودال إضافة سيارة (ثابت) --}}
    @include('dashboard.cars.modals.create', ['availableDrivers' => $availableDrivers])
@endsection

@push('scripts')
    <script>
        // إظهار المودال مجدداً في حال وجود أخطاء تحقق
        @if ($errors->any())
            @if (old('_method') === 'PUT')
                $('#editCarModal{{ old('id') }}').modal('show');
            @else
                $('#createCarModal').modal('show');
            @endif
        @endif
    </script>
@endpush
