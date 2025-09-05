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

        {{-- بطاقات ملخصة --}}
        <div class="row mb-4">
                <x-stats-card icon="fas fa-wallet" title="الرصيد الحالي" :value="number_format($currentBalance, 2)"
                    color="{{ $currentBalance >= 0 ? 'success' : 'danger' }}" />
            <div class="col-xl-3 col-md-6 mb-4">
                <a class="btn btn-info" href="{{ route('partners.profit.index') }}"><i class="fas fa-coins"></i> توزيع
                    الأرباح</a>
            </div>
        </div>

        {{-- إضافة حركة --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">إضافة حركة</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('partners.movements.store', $partner) }}" method="POST" class="row">
                    @csrf
                    <div class="col-md-3 mb-2">
                        <label>النوع</label>
                        <select name="type" class="form-control" required>
                            <option value="deposit">إيداع</option>
                            <option value="withdrawal">سحب</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label>المبلغ</label>
                        <input name="amount" type="number" step="0.01" min="0.01" class="form-control" required>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label>تاريخ التنفيذ</label>
                        <input name="occurred_at" type="datetime-local" class="form-control" required>
                    </div>
                    <div class="col-md-9 mb-2">
                        <label>ملاحظات (اختياري)</label>
                        <input name="notes" class="form-control">
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary"><i class="fas fa-plus"></i> إضافة</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- سجل الحركات --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">سجل الحركات</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>التاريخ</th>
                                <th>النوع</th>
                                <th>المبلغ</th>
                                <th>ملاحظات</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($movements as $mv)
                                <tr>
                                    <td>{{ $mv->occurred_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $mv->type === 'deposit' ? 'success' : 'danger' }}">
                                            {{ $mv->type === 'deposit' ? 'إيداع' : 'سحب' }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($mv->amount, 2) }}</td>
                                    <td>{{ $mv->notes ?? '-' }}</td>
                                    <td>
                                        <form action="{{ route('partners.movements.destroy', [$partner, $mv]) }}"
                                            method="POST" onsubmit="return confirm('حذف الحركة؟');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">لا توجد حركات</td>
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
@endsection
