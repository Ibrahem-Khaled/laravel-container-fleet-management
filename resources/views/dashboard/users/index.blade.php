@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        {{-- عنوان الصفحة ومسار التنقل --}}
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">إدارة المستخدمين</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">لوحة التحكم</a></li>
                    <li class="breadcrumb-item active" aria-current="page">المستخدمين</li>
                </ol>
            </nav>
        </div>

        @include('components.alerts')

        {{-- إحصائيات المستخدمين --}}
        <div class="row mb-4">
            <x-stats-card icon="fas fa-users" title="إجمالي المستخدمين" :count="$stats['totalUsers']" color="primary" />
            <x-stats-card icon="fas fa-user-check" title="المستخدمون النشطون" :count="$stats['activeUsers']" color="success" />
            <x-stats-card icon="fas fa-user-tag" title="عدد الأدوار" :count="$stats['rolesCount']" color="info" />
            <x-stats-card icon="fas fa-sync-alt" title="آخر تحديث" :count="date('h:i A')" color="warning" />
        </div>

        {{-- بطاقة قائمة المستخدمين --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">قائمة المستخدمين</h6>
                <button class="btn btn-primary" data-toggle="modal" data-target="#createUserModal">
                    <i class="fas fa-plus fa-sm"></i> إضافة مستخدم جديد
                </button>
            </div>
            <div class="card-body">
                {{-- تبويب الأدوار --}}
                <ul class="nav nav-pills mb-3">
                    <li class="nav-item">
                        <a class="nav-link {{ !request('role') ? 'active' : '' }}" href="{{ route('users.index') }}">
                            الكل <span class="badge badge-light ml-1">{{ $stats['totalUsers'] }}</span>
                        </a>
                    </li>
                    @foreach ($roles as $role)
                        <li class="nav-item">
                            <a class="nav-link {{ request('role') == $role->id ? 'active' : '' }}"
                                href="{{ route('users.index', ['role' => $role->id]) }}">
                                {{ $role->description }}
                                <span class="badge badge-light ml-1">{{ $stats['roleCounts'][$role->id] ?? 0 }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>

                {{-- نموذج البحث --}}
                <form action="{{ route('users.index') }}" method="GET" class="mb-4">
                    @if (request('role'))
                        <input type="hidden" name="role" value="{{ request('role') }}">
                    @endif
                    <div class="input-group">
                        <input type="text" name="search" class="form-control"
                            placeholder="ابحث بالاسم أو البريد أو الهاتف..." value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-outline-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>

                {{-- جدول المستخدمين --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%">
                        <thead class="thead-light">
                            <tr>
                                <th>المستخدم</th>
                                <th>الدور</th>
                                <th>بيانات الاتصال</th>
                                <th>الحالة</th>
                                <th>تاريخ الانضمام</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://placehold.co/40x40/EBF4FF/76859A?text=' . mb_substr($user->name, 0, 1) }}"
                                                alt="{{ $user->name }}" class="rounded-circle mr-2" width="40"
                                                height="40">
                                            <span class="font-weight-bold">{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td><span class="badge badge-pill badge-info">{{ $user->role->description }}</span>
                                    </td>
                                    <td>
                                        <div class="small">
                                            @if ($user->email)
                                                <i class="fas fa-envelope fa-fw text-muted"></i> {{ $user->email }}<br>
                                            @endif
                                            @if ($user->phone)
                                                <i class="fas fa-phone fa-fw text-muted"></i> {{ $user->phone }}
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $user->is_active ? 'success' : 'danger' }}">
                                            {{ $user->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </td>
                                    <td>{{ $user?->created_at?->format('Y-m-d') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-circle btn-info" data-toggle="modal"
                                            data-target="#showUserModal{{ $user->id }}" title="عرض التفاصيل"><i
                                                class="fas fa-eye"></i></button>
                                        <button class="btn btn-sm btn-circle btn-primary" data-toggle="modal"
                                            data-target="#editUserModal{{ $user->id }}" title="تعديل"><i
                                                class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-circle btn-danger" data-toggle="modal"
                                            data-target="#deleteUserModal{{ $user->id }}" title="حذف"><i
                                                class="fas fa-trash"></i></button>

                                        @include('dashboard.users.modals.show')
                                        @include('dashboard.users.modals.edit')
                                        @include('dashboard.users.modals.delete')
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">لا توجد نتائج مطابقة لبحثك.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center pt-3">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>

    @include('dashboard.users.modals.create')
@endsection

@push('scripts')
    <script>
        // لعرض اسم الملف المختار في حقول رفع الملفات
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
    </script>
@endpush
