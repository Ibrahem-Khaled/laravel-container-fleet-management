@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h4 mb-1">تفاصيل الموظف: {{ $user->name }}</h1>
                <div class="text-muted small">
                    الفترة: {{ $periodStart->format('Y-m-01') }} → {{ $periodEnd->format('Y-m-t') }}
                </div>
            </div>

            {{-- <form method="get" class="form-inline">
                <label class="mr-2">عدد الشهور:</label>
                <input type="number" class="form-control mr-2" name="months" value="{{ $monthsBack }}" min="1"
                    max="60">
                <button class="btn btn-secondary">تطبيق</button>
            </form> --}}
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>الشهر</th>
                            <th>الراتب</th>
                            <th>سحب اليومية</th>
                            <th>التربات</th>
                            <th>صافي الشهر</th>
                            <th>الرصيد المُرحّل</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($monthlyRows as $row)
                            <tr>
                                <td>{{ $row['ym'] }}</td>
                                <td>{{ number_format($row['salary'], 2) }}</td>
                                <td class="text-danger">{{ number_format($row['withdrawals'], 2) }}</td>
                                <td class="text-success">{{ number_format($row['tips'], 2) }}</td>
                                <td>{{ number_format($row['net'], 2) }}</td>
                                <td class="font-weight-bold">{{ number_format($row['carry'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light">
                        <tr class="font-weight-bold">
                            <td>الإجمالي</td>
                            <td>{{ number_format($totals['salary'], 2) }}</td>
                            <td class="text-danger">{{ number_format($totals['withdrawals'], 2) }}</td>
                            <td class="text-success">{{ number_format($totals['tips'], 2) }}</td>
                            <td>{{ number_format($totals['net'], 2) }}</td>
                            <td>{{ number_format($totals['carry'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
