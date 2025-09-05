@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h4 mb-1">تفاصيل التربات — السائق: {{ $user->name }}</h1>
                <div class="text-muted small">
                    النطاق: {{ \Illuminate\Support\Carbon::parse($from)->format('Y-m-d') }}
                    → {{ \Illuminate\Support\Carbon::parse($to)->format('Y-m-d') }}
                </div>
            </div>
            <form method="get" class="form-inline">
                <input type="date" name="from" class="form-control mr-2"
                    value="{{ request('from') ?? \Illuminate\Support\Carbon::parse($from)->toDateString() }}">
                <input type="date" name="to" class="form-control mr-2"
                    value="{{ request('to') ?? \Illuminate\Support\Carbon::parse($to)->toDateString() }}">

                <input type="number" name="container_id" class="form-control mr-2" placeholder="ID الحاوية"
                    value="{{ request('container_id') }}">
                <input type="number" name="car_id" class="form-control mr-2" placeholder="ID العربية"
                    value="{{ request('car_id') }}">

                <input type="text" name="type" class="form-control mr-2" placeholder="نوع"
                    value="{{ request('type') }}">

                <button class="btn btn-secondary">تطبيق</button>
            </form>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">إجمالي التربات في النطاق</div>
                        <div class="h4 mb-0">{{ number_format($totalPrice, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">عدد السجلات</div>
                        <div class="h4 mb-0">{{ $tipsCount }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-striped mb-0 text-center">
                    <thead class="thead-light">
                        <tr>
                            <th>التاريخ</th>
                            <th>اسم العميل</th>
                            <th>رقم الحاوية</th>
                            <th>حجم الحاوية</th>
                            <th>رقم السيارة</th>
                            <th>النوع</th>
                            <th>السعر</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tips as $tip)
                            <tr>
                                <td>{{ $tip->created_at->format('Y-m-d H:i') }}</td>
                                <td>{{ optional(optional(optional($tip->container)->customs)->client)->name ?? '—' }}</td>
                                <td>
                                    @if ($tip->container)
                                        {{-- غيّر "number" لو عندك عمود آخر مثل code --}}
                                        {{ $tip->container->number ?? $tip->container->id }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($tip->container)
                                        {{ $tip->container->size ?? '—' }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($tip->car)
                                        {{-- غيّر "number" حسب سكيمتك --}}
                                        {{ $tip->car->number ?? $tip->car->id }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $tip->type ?? '—' }}</td>
                                <td class="font-weight-bold">{{ number_format($tip->price, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">لا توجد بيانات في النطاق المحدد.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $tips->links() }}
            </div>
        </div>
    </div>
@endsection
