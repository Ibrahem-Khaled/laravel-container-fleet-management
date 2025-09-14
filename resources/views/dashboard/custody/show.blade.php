@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        {{-- عنوان --}}
        <div class="row">
            <div class="col-12">
                <h1 class="h3 mb-0 text-gray-800">عهدة: {{ $account->owner->name }}</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">لوحة التحكم</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('custody-accounts.index') }}">العُهد</a></li>
                        <li class="breadcrumb-item active">حركات اليومية</li>
                    </ol>
                </nav>
            </div>
        </div>

        @include('components.alerts')

        {{-- ملخص --}}
        <div class="card mb-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="h6 mb-1">افتتاحي: {{ number_format($account->opening_balance, 2) }}</div>
                    <div class="h5 mb-1">وارد: <span class="text-success">{{ number_format($sumIncome, 2) }}</span></div>
                    <div class="h5 mb-1">منصرف: <span class="text-danger">{{ number_format($sumExpense, 2) }}</span></div>
                    <div class="h5 mb-0">الرصيد الحالي: <span
                            class="text-primary">{{ number_format($account->currentBalance(), 2) }}</span></div>
                </div>
                <div>
                    <button class="btn btn-success" data-toggle="modal" data-target="#createDailyModal">
                        <i class="fas fa-plus"></i> إضافة حركة يومية
                    </button>
                    <button class="btn btn-secondary" data-toggle="modal"
                        data-target="#editAccountModal{{ $account->id }}">
                        <i class="fas fa-edit"></i> تعديل العهدة
                    </button>
                </div>
            </div>
        </div>

        {{-- فلتر النوع --}}
        <form action="{{ route('custody-accounts.show', $account) }}" method="GET" class="mb-3">
            <div class="form-row">
                <div class="col-md-4 mb-2">
                    <select name="type" class="form-control">
                        <option value="">كل الأنواع</option>
                        <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>وارد</option>
                        <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>منصرف</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <button class="btn btn-primary btn-block"><i class="fas fa-filter"></i> فلترة</button>
                </div>
            </div>
        </form>

        {{-- جدول اليومية --}}
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Transactionable</th>
                            <th>النوع</th>
                            <th>الطريقة</th>
                            <th>المبلغ</th>
                            <th>الضريبة</th>
                            <th>الإجمالي</th>
                            <th>ملاحظات</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($daily as $row)
                            <tr>
                                <td>{{ $row->id }}</td>
                                <td class="small text-monospace">
                                    {{ class_basename($row->transactionable_type) }}#{{ $row->transactionable_id }}</td>
                                <td>{{ $row->type === 'income' ? 'وارد' : 'منصرف' }}</td>
                                <td>{{ $row->method === 'cash' ? 'نقدي' : 'بنك' }}</td>
                                <td>{{ number_format($row->amount, 2) }}</td>
                                <td>{{ number_format($row->tax_value, 2) }}</td>
                                <td>{{ number_format($row->total_amount, 2) }}</td>
                                <td>{{ $row->notes ?? '-' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-circle btn-primary" data-toggle="modal"
                                        data-target="#editDailyModal{{ $row->id }}"><i
                                            class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-circle btn-danger" data-toggle="modal"
                                        data-target="#deleteDailyModal{{ $row->id }}"><i
                                            class="fas fa-trash"></i></button>

                                    @include('dashboard.custody.modals.edit-daily', ['row' => $row])
                                    @include('dashboard.custody.modals.delete-daily', ['row' => $row])
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">لا توجد حركات</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-center">
                    {{ $daily->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- مودالات عامة --}}
    @include('dashboard.custody.modals.edit-account', ['account' => $account])
    {{-- @include('dashboard.custody.modals.create-daily', ['account' => $account]) --}}
@endsection
