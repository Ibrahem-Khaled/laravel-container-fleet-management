@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        {{-- عنوان ومسار --}}
        <div class="row">
            <div class="col-12">
                <h1 class="h3 mb-0 text-gray-800">إدارة العُهد</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">لوحة التحكم</a></li>
                        <li class="breadcrumb-item active">العُهد</li>
                    </ol>
                </nav>
            </div>
        </div>

        @include('components.alerts')

        {{-- إحصائيات --}}
        <div class="row mb-4">
            <x-stats-card icon="fas fa-lock-open" title="عهد مفتوحة" :value="$openCount" color="success" />
            <x-stats-card icon="fas fa-lock" title="عهد مغلقة" :value="$closedCount" color="secondary" />
            <x-stats-card icon="fas fa-coins" title="إجمالي الرصيد الافتتاحي" :value="number_format($totalOpeningBalance, 2)" color="primary" />
            <x-stats-card icon="fas fa-wallet" title="إجمالي الرصيد الحالي" :value="number_format($totalCurrentBalance, 2)" color="info" />
        </div>

        <div class="row mb-4">
            <x-stats-card icon="fas fa-user-tag" title="أدوار لديها عهد" :value="$roles->count()" color="warning" />
            <x-stats-card icon="fas fa-file-invoice-dollar" title="إجمالي الحركات" :value="$totalTransactions" color="dark" />
            <x-stats-card icon="fas fa-chart-line" title="الفرق في الرصيد" :value="number_format($totalCurrentBalance - $totalOpeningBalance, 2)" color="danger" />
            <x-stats-card icon="fas fa-percentage" title="نسبة التغيير" :value="number_format($totalOpeningBalance > 0 ? (($totalCurrentBalance - $totalOpeningBalance) / $totalOpeningBalance) * 100 : 0, 2) . '%'" color="success" />
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">قائمة العهد</h6>
                <button class="btn btn-primary" data-toggle="modal" data-target="#createAccountModal">
                    <i class="fas fa-plus"></i> إضافة عهدة
                </button>
            </div>

            <div class="card-body">

                {{-- تبويب الأدوار --}}
                <ul class="nav nav-tabs mb-4">
                    <li class="nav-item">
                        <a class="nav-link {{ empty($roleId) ? 'active' : '' }}"
                            href="{{ route('custody-accounts.index', array_filter(['status' => $status !== 'all' ? $status : null])) }}">
                            الكل
                        </a>
                    </li>
                    @foreach ($roles as $r)
                        <li class="nav-item">
                            <a class="nav-link {{ (int) $roleId === (int) $r->id ? 'active' : '' }}"
                                href="{{ route('custody-accounts.index', array_filter(['role_id' => $r->id, 'status' => $status !== 'all' ? $status : null])) }}">
                                {{ $r->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>

                {{-- فلاتر --}}
                <form action="{{ route('custody-accounts.index') }}" method="GET" class="mb-4">
                    <div class="form-row">
                        <div class="col-md-3 mb-2">
                            <select name="status" class="form-control">
                                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>الكل (الحالة)</option>
                                <option value="open" {{ $status === 'open' ? 'selected' : '' }}>مفتوحة</option>
                                <option value="closed"{{ $status === 'closed' ? 'selected' : '' }}>مغلقة</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <input type="text" name="search" class="form-control" value="{{ $search }}"
                                placeholder="ابحث باسم/بريد/هاتف صاحب العهدة...">
                        </div>
                        <div class="col-md-3 mb-2">
                            <button class="btn btn-primary btn-block"><i class="fas fa-search"></i> بحث</button>
                        </div>
                    </div>
                    @if (!empty($roleId))
                        <input type="hidden" name="role_id" value="{{ $roleId }}">
                    @endif
                </form>

                {{-- الجدول --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>صاحب العهدة</th>
                                <th>الدور</th>
                                <th>افتتاحي</th>
                                <th>الرصيد الحالي</th>
                                <th>عدد الحركات</th>
                                <th>الحالة</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($accounts as $account)
                                <tr>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $account->owner->avatar ? asset('storage/' . $account->owner->avatar) : asset('img/default-avatar.png') }}"
                                                class="rounded-circle mr-2" width="40" height="40" alt="">
                                            <div>
                                                <div class="font-weight-bold">{{ $account->owner->name }}</div>
                                                <div class="text-muted small">{{ $account->owner->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">{{ $account->owner->role->name ?? '—' }}</td>

                                    <td class="align-middle">{{ number_format($account->opening_balance, 2) }}</td>
                                    <td class="align-middle">{{ number_format($account->currentBalance(), 2) }}</td>
                                    <td class="align-middle">
                                        <span class="badge badge-info">
                                            {{ $account->dailyTransactions()->count() }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <span
                                            class="badge badge-{{ $account->status === 'open' ? 'success' : 'secondary' }}">
                                            {{ $account->status === 'open' ? 'مفتوحة' : 'مغلقة' }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <a class="btn btn-sm btn-circle btn-info"
                                            href="{{ route('custody-accounts.show', $account) }}" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-circle btn-primary" data-toggle="modal"
                                            data-target="#editAccountModal{{ $account->id }}" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-circle btn-danger" data-toggle="modal"
                                            data-target="#deleteAccountModal{{ $account->id }}" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                        @include('dashboard.custody.modals.edit-account', [
                                            'account' => $account,
                                        ])
                                        @include('dashboard.custody.modals.delete-account', [
                                            'account' => $account,
                                        ])
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">لا توجد عهد</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center">
                    {{ $accounts->links() }}
                </div>
            </div>
        </div>
    </div>

    @include('dashboard.custody.modals.create-account')
@endsection

@push('scripts')
    <script>
        $('.custom-file-input').on('change', function() {
            var f = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(f);
        });
        $('[data-toggle="tooltip"]').tooltip();
    </script>
@endpush
