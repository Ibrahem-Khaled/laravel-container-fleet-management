@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">إدارة الحركات المالية اليومية</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">لوحة التحكم</a></li>
                    <li class="breadcrumb-item active" aria-current="page">الحركات المالية</li>
                </ol>
            </nav>
        </div>

        @include('components.alerts')

        <div class="row mb-4">
            <x-stats-card title="إجمالي الوارد" count="{{ number_format($stats['total_income'], 2) }}"
                icon="fas fa-arrow-up" color="success" />
            <x-stats-card title="إجمالي المنصرف" count="{{ number_format($stats['total_expense'], 2) }}"
                icon="fas fa-arrow-down" color="danger" />
            <x-stats-card title="الرصيد الصافي" count="{{ number_format($stats['net_balance'], 2) }}" icon="fas fa-wallet"
                color="primary" />
            <x-stats-card title="عدد الحركات" count="{{ $stats['transactions_count'] }}" icon="fas fa-file-invoice-dollar"
                color="info" />
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">قائمة الحركات المالية</h6>
                <button class="btn btn-primary" data-toggle="modal" data-target="#createTransactionModal">
                    <i class="fas fa-plus fa-sm"></i> إضافة حركة جديدة
                </button>
            </div>
            <div class="card-body">

                <div class="row mb-3">
                    <div class="col-md-8">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a class="nav-link {{ !request('type') && !request('filter') ? 'active' : '' }}"
                                    href="{{ route('transactions.index') }}">الكل</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request('type') == 'income' ? 'active' : '' }}"
                                    href="{{ route('transactions.index', ['type' => 'income']) }}">الوارد</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request('type') == 'expense' ? 'active' : '' }}"
                                    href="{{ route('transactions.index', ['type' => 'expense']) }}">المنصرف</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request('filter') == 'taxable' ? 'active' : '' }}"
                                    href="{{ route('transactions.index', ['filter' => 'taxable']) }}">الفواتير الضريبية</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <form action="{{ route('transactions.index') }}" method="GET">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="ابحث بالملاحظات..."
                                    value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-primary" type="submit"><i
                                            class="fas fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>النوع</th>
                                <th>المبلغ الإجمالي</th>
                                <th>الضريبة</th>
                                <th>مرتبطة بـ</th>
                                <th>تاريخ الإنشاء</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->id }}</td>
                                    <td>
                                        @if ($transaction->type == 'income')
                                            <span class="badge badge-success">وارد</span>
                                        @else
                                            <span class="badge badge-danger">منصرف</span>
                                        @endif
                                    </td>
                                    <td class="font-weight-bold text-monospace">
                                        {{ number_format($transaction->total_amount, 2) }} $</td>
                                    <td>
                                        @if ($transaction->tax_value > 0)
                                            @php
                                                $taxAmountValue = $transaction->total_amount - $transaction->amount;
                                            @endphp
                                            <span class="badge badge-light">{{ number_format($taxAmountValue, 2) }}
                                                $</span>
                                            <small class="text-muted">({{ (float) $transaction->tax_value }}%)</small>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($transaction->transactionable)
                                            @php
                                                $configKey = '';
                                                if ($transaction->transactionable instanceof App\Models\User) {
                                                    $configKey = 'user_role_' . $transaction->transactionable->role_id;
                                                } else {
                                                    $configKey = $transaction->transactionable_type;
                                                }
                                                $config = $transactionable_config[$configKey] ?? null;
                                            @endphp

                                            @if ($config)
                                                <span class="badge badge-info">{{ $config['name'] }}</span>
                                                <span
                                                    class="small text-muted">{{ $transaction->transactionable->{$config['display_column']} }}</span>
                                            @else
                                                <span class="text-danger small">سجل غير معرف</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $transaction->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-circle btn-info" title="عرض التفاصيل"
                                            data-toggle="modal"
                                            data-target="#showTransactionModal{{ $transaction->id }}"><i
                                                class="fas fa-eye"></i></button>
                                        <button class="btn btn-sm btn-circle btn-primary" title="تعديل" data-toggle="modal"
                                            data-target="#editTransactionModal{{ $transaction->id }}"><i
                                                class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-circle btn-danger" title="حذف" data-toggle="modal"
                                            data-target="#deleteTransactionModal{{ $transaction->id }}"><i
                                                class="fas fa-trash"></i></button>

                                        @include(
                                            'dashboard.transactions.modals.show',
                                            compact('transaction', 'transactionable_config'))
                                        @include(
                                            'dashboard.transactions.modals.edit',
                                            compact('transaction', 'transactionable_config'))
                                        @include(
                                            'dashboard.transactions.modals.delete',
                                            compact('transaction'))
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">لا توجد حركات مالية لعرضها حاليًا.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center pt-3">
                    {{ $transactions->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    @include('dashboard.transactions.modals.create', compact('transactionable_config'))

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // كود لجلب السجلات عند اختيار نوع مرتبط به
            $(document).on('change', '.transactionable-type-select', function() {
                var selectedKey = $(this).val();
                var targetSelect = $(this).closest('.modal-body').find('.transactionable-id-select');
                targetSelect.html('<option value="">... جاري التحميل ...</option>').prop('disabled', true);

                if (!selectedKey) {
                    targetSelect.html('<option value="">-- يرجى اختيار النوع أولاً --</option>');
                    return;
                }
                $.ajax({
                    url: '{{ route('transactions.get_records') }}',
                    type: 'GET',
                    data: {
                        key: selectedKey
                    },
                    success: function(data) {
                        targetSelect.prop('disabled', false).html(
                            '<option value="">-- اختر السجل --</option>');
                        $.each(data, function(index, record) {
                            targetSelect.append($('<option>', {
                                value: record.id,
                                text: record.text
                            }));
                        });
                    },
                    error: function() {
                        targetSelect.html('<option value="">! حدث خطأ في التحميل --</option>');
                    }
                });
            });

            // دالة لفلترة قائمة "مرتبطة بـ" بناءً على نوع الحركة
            function filterTransactionableOptions(typeSelect) {
                const selectedType = $(typeSelect).val();
                const modalBody = $(typeSelect).closest('.modal-body');
                const transactionableSelect = modalBody.find('.transactionable-type-select');
                const transactionableIdSelect = modalBody.find('.transactionable-id-select');

                const currentSelectedValue = transactionableSelect.val();
                transactionableSelect.find('option').not('[value=""]').hide();

                if (selectedType) {
                    transactionableSelect.find('option[data-contexts*="' + selectedType + '"]').show();
                } else {
                    transactionableSelect.find('option').show();
                }

                if (transactionableSelect.find('option[value="' + currentSelectedValue + '"]:visible').length ===
                    0) {
                    transactionableSelect.val('');
                    transactionableIdSelect.html('<option value="">-- اختر النوع أولاً --</option>').prop(
                        'disabled', true);
                }
            }

            // عند تغيير "نوع الحركة" (وارد/منصرف)
            $(document).on('change', '.transaction-type-select', function() {
                filterTransactionableOptions(this);
            });

            // عند فتح أي مودال، قم بتشغيل الفلتر للتأكد من الحالة الصحيحة (لمودال التعديل)
            $('.modal').on('shown.bs.modal', function() {
                const typeSelect = $(this).find('.transaction-type-select');
                if (typeSelect.length) {
                    filterTransactionableOptions(typeSelect);
                }
            });
        });
    </script>
@endpush
