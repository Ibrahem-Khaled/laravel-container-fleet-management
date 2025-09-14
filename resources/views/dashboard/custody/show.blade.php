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
                    <div class="h5 mb-0">الرصيد الحالي:
                        <span class="text-primary">{{ number_format($account->currentBalance(), 2) }}</span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    {{-- زر زيادة العهدة فقط --}}
                    <button class="btn btn-success mr-2" data-toggle="modal" data-target="#issueModal">
                        <i class="fas fa-plus-circle"></i> زيادة العهدة
                    </button>

                    {{-- تعديل بيانات العهدة كما هو --}}
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

        {{-- جدول اليومية (عرض فقط) --}}
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
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($daily as $row)
                            <tr>
                                <td>{{ $row->id }}</td>
                                <td class="small text-monospace">
                                    {{ class_basename($row->transactionable_type) }}#{{ $row->transactionable_id }}
                                </td>
                                <td>{{ $row->type === 'income' ? 'وارد' : 'منصرف' }}</td>
                                <td>{{ $row->method === 'cash' ? 'نقدي' : 'بنك' }}</td>
                                <td>{{ number_format($row->amount, 2) }}</td>
                                <td>{{ number_format($row->tax_value, 2) }}</td>
                                <td>{{ number_format($row->total_amount, 2) }}</td>
                                <td>{{ $row->notes ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">لا توجد حركات</td>
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

    {{-- مودال: زيادة العهدة --}}
    <div class="modal fade" id="issueModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('custody-accounts.issue', $account) }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">سند صرف (زيادة العهدة)</h5>
                    <button type="button" class="close" data-dismiss="modal"
                        aria-label="إغلاق"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>المبلغ</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>العملة</label>
                        <input type="text" name="currency" maxlength="3" class="form-control" placeholder="SAR"
                            value="SAR">
                    </div>
                    <div class="form-group">
                        <label>الطريقة</label>
                        <select name="method" class="form-control">
                            <option value="cash" selected>نقدي</option>
                            <option value="bank">بنك</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>تاريخ الحركة</label>
                        <input type="datetime-local" name="occurred_at" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal" type="button">إلغاء</button>
                    <button class="btn btn-success" type="submit">حفظ</button>
                </div>
            </form>
        </div>
    </div>

    {{-- مودال تعديل بيانات العهدة (كما عندك) --}}
    @include('dashboard.custody.modals.edit-account', ['account' => $account])
@endsection
