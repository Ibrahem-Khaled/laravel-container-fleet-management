@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        {{-- Page Heading & Breadcrumb --}}
        <div class="row">
            <div class="col-12">
                <h1 class="h3 mb-0 text-gray-800">إدارة الأدوار والصلاحيات</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">لوحة التحكم</a></li>
                        <li class="breadcrumb-item active" aria-current="page">الأدوار</li>
                    </ol>
                </nav>
            </div>
        </div>

        @include('components.alerts')

        {{-- Stats Cards --}}
        <div class="row mb-4">
                <x-stats-card icon="fas fa-user-tag" title="إجمالي الأدوار" :value="$stats['total_roles']" color="primary" />
                <x-stats-card icon="fas fa-user-shield" title="عدد المشرفين" :value="$stats['admins_count']" color="info" />
                <x-stats-card icon="fas fa-users" title="عدد العملاء" :value="$stats['clients_count']" color="success" />
        </div>

        {{-- Roles List Card --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">قائمة الأدوار</h6>
                <button class="btn btn-primary" data-toggle="modal" data-target="#createRoleModal">
                    <i class="fas fa-plus fa-sm"></i> إضافة دور جديد
                </button>
            </div>
            <div class="card-body">
                {{-- Tabs for Roles --}}
                <ul class="nav nav-tabs mb-4">
                    <li class="nav-item">
                        <a class="nav-link {{ !$selectedRole || $selectedRole === 'all' ? 'active' : '' }}"
                            href="{{ route('roles.index') }}">الكل</a>
                    </li>
                    @foreach ($allRoles as $roleName)
                        <li class="nav-item">
                            <a class="nav-link {{ $selectedRole === $roleName ? 'active' : '' }}"
                                href="{{ route('roles.index', ['role' => $roleName]) }}">
                                {{ $roleName }}
                            </a>
                        </li>
                    @endforeach
                </ul>

                {{-- Search Form --}}
                <form action="{{ route('roles.index') }}" method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="ابحث بالاسم أو الوصف..."
                            value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search fa-sm"></i> بحث
                            </button>
                        </div>
                    </div>
                </form>

                {{-- Roles Table --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>اسم الدور (Name)</th>
                                <th>الوصف</th>
                                <th>عدد المستخدمين</th>
                                <th>تاريخ الإنشاء</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $role)
                                <tr>
                                    <td>{{ $role->id }}</td>
                                    <td><span class="badge badge-pill badge-primary p-2">{{ $role->name }}</span></td>
                                    <td>{{ $role->description ?? '-' }}</td>
                                    <td>
                                        <span class="badge badge-pill badge-secondary p-2">
                                            <i class="fas fa-users mr-1"></i> {{ $role->users_count }}
                                        </span>
                                    </td>
                                    <td>{{ $role->created_at?->format('Y-m-d') }}</td>
                                    <td>
                                        <button class="btn btn-info btn-circle btn-sm" data-toggle="modal"
                                            data-target="#showRoleModal{{ $role->id }}" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-primary btn-circle btn-sm" data-toggle="modal"
                                            data-target="#editRoleModal{{ $role->id }}" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-circle btn-sm" data-toggle="modal"
                                            data-target="#deleteRoleModal{{ $role->id }}" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                        {{-- Include Modals for each role --}}
                                        @include('dashboard.roles.modals.show', ['role' => $role])
                                        @include('dashboard.roles.modals.edit', ['role' => $role])
                                        @include('dashboard.roles.modals.delete', ['role' => $role])
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">لا توجد أدوار لعرضها.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-center">
                    {{ $roles->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Include Create Modal (once) --}}
    @include('dashboard.roles.modals.create')
@endsection

@push('scripts')
    <script>
        // Script to automatically open modal if there are validation errors
        @if ($errors->hasBag('store'))
            $('#createRoleModal').modal('show');
        @endif

        @foreach ($roles as $role)
            @if ($errors->hasBag('update' . $role->id))
                $('#editRoleModal{{ $role->id }}').modal('show');
            @endif
        @endforeach
    </script>
@endpush
