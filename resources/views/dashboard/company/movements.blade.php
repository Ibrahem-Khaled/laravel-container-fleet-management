@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        {{-- العنوان + breadcrumb --}}
        <div class="row">
            <div class="col-12">
                <h1 class="h3 mb-0 text-gray-800">حركات رأس المال — {{ $partner->name }}</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">لوحة التحكم</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('partners.index') }}">الشركاء</a></li>
                        <li class="breadcrumb-item active" aria-current="page">الحركات</li>
                    </ol>
                </nav>
            </div>
        </div>

        @include('components.alerts')

        {{-- فلترة الشهر والسنة --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-filter mr-2"></i>
                            فلترة الأرباح المتاحة
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="row">
                            <div class="col-md-4">
                                <label class="form-label">السنة</label>
                                <select name="year" class="form-control">
                                    @for($y = now()->year; $y >= now()->year - 5; $y--)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">الشهر</label>
                                <select name="month" class="form-control">
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                            {{ ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'][$m-1] }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search mr-1"></i>
                                    تحديث البيانات
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- بطاقات ملخصة --}}
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-{{ $currentBalance >= 0 ? 'success' : 'danger' }} shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-{{ $currentBalance >= 0 ? 'success' : 'danger' }} text-uppercase mb-1">
                                    رأس المال الحالي
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($currentBalance, 2) }} ر.س
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-wallet fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    الأرباح المتاحة
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($withdrawalLimits['max_profit_withdrawal'], 2) }} ر.س
                                </div>
                                <small class="text-muted">
                                    النسبة: {{ number_format($withdrawalLimits['profit_data']['partner_percentage'], 2) }}%
                                </small>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-coins fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    الحد الأقصى للسحب
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($withdrawalLimits['max_total_withdrawal'], 2) }} ر.س
                                </div>
                                <small class="text-muted">
                                    (أرباح + رأس مال)
                                </small>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    إجمالي الوارد
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($withdrawalLimits['profit_data']['total_income'], 2) }} ر.س
                                </div>
                                <small class="text-muted">
                                    {{ ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'][$month-1] }} {{ $year }}
                                </small>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- تحذيرات مهمة --}}
        @if($withdrawalLimits['max_profit_withdrawal'] > 0)
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>معلومات مهمة:</strong>
                <ul class="mb-0 mt-2">
                    <li>يمكنك سحب حتى <strong>{{ number_format($withdrawalLimits['max_profit_withdrawal'], 2) }} ر.س</strong> من أرباحك المتاحة بدون تأثير على رأس المال</li>
                    <li>إذا سحبت أكثر من هذا المبلغ، سيتم السحب من رأس المال مما قد يؤثر على حصتك في الأرباح المستقبلية</li>
                    <li>النسبة الحالية من رأس المال: <strong>{{ number_format($withdrawalLimits['profit_data']['partner_percentage'], 2) }}%</strong></li>
                </ul>
            </div>
        @else
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>تحذير:</strong> لا توجد أرباح متاحة للشهر المحدد. أي سحب سيتم من رأس المال مباشرة.
            </div>
        @endif

        {{-- إضافة حركة --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-plus-circle mr-2"></i>
                    إضافة حركة جديدة لرأس المال
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('partners.movements.store', $partner) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label font-weight-bold">نوع الحركة</label>
                            <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                                <option value="">اختر نوع الحركة</option>
                                <option value="deposit" {{ old('type') == 'deposit' ? 'selected' : '' }}>
                                    <i class="fas fa-arrow-up text-success"></i> إيداع
                                </option>
                                <option value="withdrawal" {{ old('type') == 'withdrawal' ? 'selected' : '' }}>
                                    <i class="fas fa-arrow-down text-danger"></i> سحب
                                </option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label font-weight-bold">المبلغ (ر.س)</label>
                            <input name="amount" type="number" step="0.01" min="0.01"
                                   class="form-control @error('amount') is-invalid @enderror"
                                   value="{{ old('amount') }}"
                                   placeholder="0.00"
                                   max="{{ $withdrawalLimits['max_total_withdrawal'] }}"
                                   required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                الحد الأقصى: {{ number_format($withdrawalLimits['max_total_withdrawal'], 2) }} ر.س
                            </small>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label font-weight-bold">تاريخ التنفيذ</label>
                            <input name="occurred_at" type="datetime-local"
                                   class="form-control @error('occurred_at') is-invalid @enderror"
                                   value="{{ old('occurred_at', now()->format('Y-m-d\TH:i')) }}" required>
                            @error('occurred_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label font-weight-bold">الملاحظات</label>
                            <input name="notes" class="form-control @error('notes') is-invalid @enderror"
                                   value="{{ old('notes') }}"
                                   placeholder="وصف الحركة (اختياري)">
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus mr-2"></i>
                                إضافة الحركة
                            </button>
                            <button type="reset" class="btn btn-secondary ml-2">
                                <i class="fas fa-undo mr-2"></i>
                                إعادة تعيين
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- سجل الحركات --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-history mr-2"></i>
                    سجل حركات رأس المال
                </h6>
                <div class="btn-group">
                    <a href="{{ route('partners.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-right mr-1"></i>
                        العودة للشركاء
                    </a>
                    <a href="{{ route('partners.profit.index') }}" class="btn btn-sm btn-info">
                        <i class="fas fa-coins mr-1"></i>
                        توزيع الأرباح
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>التاريخ والوقت</th>
                                <th>نوع الحركة</th>
                                <th>المبلغ</th>
                                <th>الملاحظات</th>
                                <th>تاريخ الإنشاء</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($movements as $mv)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $mv->occurred_at->format('Y-m-d') }}</strong>
                                            <br><small class="text-muted">{{ $mv->occurred_at->format('H:i') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $mv->type === 'deposit' ? 'success' : 'danger' }} badge-lg">
                                            <i class="fas fa-{{ $mv->type === 'deposit' ? 'arrow-up' : 'arrow-down' }} mr-1"></i>
                                            {{ $mv->type === 'deposit' ? 'إيداع' : 'سحب' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="font-weight-bold {{ $mv->type === 'deposit' ? 'text-success' : 'text-danger' }}">
                                            {{ $mv->type === 'deposit' ? '+' : '-' }}{{ number_format($mv->amount, 2) }} ر.س
                                        </span>
                                    </td>
                                    <td>
                                        @if($mv->notes)
                                            <span class="text-truncate" style="max-width: 200px;" title="{{ $mv->notes }}">
                                                {{ $mv->notes }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $mv->created_at->format('Y-m-d H:i') }}</small>
                                    </td>
                                    <td>
                                        <form action="{{ route('partners.movements.destroy', [$partner, $mv]) }}"
                                            method="POST" onsubmit="return confirm('حذف الحركة؟');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger" title="حذف الحركة">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-exchange-alt fa-3x mb-3"></i>
                                            <p>لا توجد حركات مسجلة</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center">
                    {{ $movements->links() }}
                </div>
            </div>
        </div>

    </div>

    {{-- JavaScript للتحقق من حدود السحب --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.querySelector('select[name="type"]');
            const amountInput = document.querySelector('input[name="amount"]');
            const submitButton = document.querySelector('button[type="submit"]');

            const maxProfitWithdrawal = {{ $withdrawalLimits['max_profit_withdrawal'] }};
            const maxTotalWithdrawal = {{ $withdrawalLimits['max_total_withdrawal'] }};

            function updateAmountValidation() {
                const selectedType = typeSelect.value;
                const amount = parseFloat(amountInput.value) || 0;

                // إزالة التحذيرات السابقة
                const existingAlert = document.querySelector('.withdrawal-warning');
                if (existingAlert) {
                    existingAlert.remove();
                }

                if (selectedType === 'withdrawal' && amount > 0) {
                    if (amount > maxTotalWithdrawal) {
                        // تجاوز الحد الأقصى
                        amountInput.classList.add('is-invalid');
                        showWarning('المبلغ يتجاوز الحد الأقصى المسموح للسحب', 'danger');
                        submitButton.disabled = true;
                    } else if (amount > maxProfitWithdrawal) {
                        // سحب من رأس المال
                        amountInput.classList.remove('is-invalid');
                        showWarning('تحذير: هذا المبلغ يتجاوز أرباحك المتاحة وسيتم السحب من رأس المال', 'warning');
                        submitButton.disabled = false;
                    } else {
                        // سحب آمن من الأرباح
                        amountInput.classList.remove('is-invalid');
                        showWarning('ممتاز: يمكنك سحب هذا المبلغ من أرباحك المتاحة', 'success');
                        submitButton.disabled = false;
                    }
                } else {
                    amountInput.classList.remove('is-invalid');
                    submitButton.disabled = false;
                }
            }

            function showWarning(message, type) {
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type} withdrawal-warning mt-2`;
                alertDiv.innerHTML = `<i class="fas fa-${type === 'danger' ? 'exclamation-triangle' : type === 'warning' ? 'exclamation-circle' : 'check-circle'} mr-2"></i>${message}`;

                amountInput.parentNode.appendChild(alertDiv);
            }

            typeSelect.addEventListener('change', updateAmountValidation);
            amountInput.addEventListener('input', updateAmountValidation);
        });
    </script>
@endsection
