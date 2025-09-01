@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-1 text-gray-800">الكشف الشهري المفصل</h1>
                <p class="mb-0 text-muted">
                    للمكتب:
                    <span class="font-weight-bold">{{ $office->name }}</span>
                    |
                    التاريخ:
                    <span class="font-weight-bold">{{ $reportDate->translatedFormat('F Y') }}</span>
                </p>
            </div>
            <a href="{{ route('revenues.clearance.yearly', ['office' => $office->id, 'year' => $reportDate->year]) }}"
                class="btn btn-sm btn-outline-secondary shadow-sm">
                <i class="fas fa-calendar-alt fa-sm"></i> عرض التقرير السنوي
            </a>
        </div>

        <!-- Filters -->
        <div class="card shadow mb-4">
            <div class="card-body bg-light">
                <form action="{{ route('revenues.clearance.monthly', $office) }}" method="GET"
                    class="form-inline justify-content-center">
                    <div class="form-group mx-2">
                        <label for="month" class="mr-2">الشهر:</label>
                        <select name="month" id="month" class="form-control">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" @selected($m == $reportDate->month)>
                                    {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="form-group mx-2">
                        <label for="year" class="mr-2">السنة:</label>
                        <select name="year" id="year" class="form-control">
                            @for ($y = now()->year; $y >= 2020; $y--)
                                <option value="{{ $y }}" @selected($y == $reportDate->year)>{{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search fa-sm"></i> عرض الكشف
                    </button>
                </form>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="row">
            <x-stats-card icon="fas fa-file-invoice-dollar" title="إجمالي قيمة الإقرارات" :value="number_format($totalValue, 2)"
                color="primary" />
            <x-stats-card icon="fas fa-shipping-fast" title="إجمالي أسعار الحاويات المنقولة" :value="number_format($transportedContainersSum, 2)"
                color="warning" />
            <x-stats-card icon="fas fa-hand-holding-usd" title="إجمالي المبالغ المستلمة" :value="number_format($totalIncome, 2)"
                color="success" />
            <x-stats-card icon="fas fa-balance-scale-right" title="الصافي المستحق" :value="number_format($balance, 2)" :color="$balance > 0 ? 'danger' : 'info'" />
        </div>

        <!-- كتلة: تحديث جماعي لأسعار حاويات بيان محدد -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-warning">
                    <i class="fas fa-edit"></i> تحديث جماعي لأسعار الحاويات لبيان محدد
                </h6>
            </div>
            <div class="card-body">
                <form id="bulk-price-form" method="POST" action="#"
                    data-url-template="{{ route('revenues.clearance.bulk_containers_price', ['office' => $office->id, 'declaration' => '___DECL___']) }}">
                    @csrf

                    <div class="row g-2 align-items-end">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">اختر البيان الجمركي</label>
                            <select id="declSelect" class="form-control" {{ $declarations->isEmpty() ? 'disabled' : '' }}
                                dir="rtl">
                                <option value="">— اختر بيانًا —</option>
                                @foreach ($declarations as $dec)
                                    <option value="{{ $dec->id }}">
                                        رقم: {{ $dec->statement_number ?? $dec->id }}
                                        | اسم العميل: {{ optional($dec->client)->name ?? '—' }}
                                        | عدد الحاويات: {{ $dec->containers->count() }}
                                        | إجمالي أسعار الحاويات: {{ number_format($dec->calculated_price, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mb-2">
                            <label class="form-label">سعر موحّد لكل الحاويات</label>
                            <input id="bulk-price" type="number" name="price" class="form-control" min="0"
                                step="1" placeholder="مثال: 1500" disabled required>
                        </div>

                        <div class="col-md-3 mb-2">
                            <button id="bulk-submit" class="btn btn-warning w-100" disabled>
                                <i class="fas fa-save"></i> تطبيق التحديث الجماعي
                            </button>
                        </div>
                    </div>
                </form>

                <script>
                    (function() {
                        const form = document.getElementById('bulk-price-form');
                        const select = document.getElementById('declSelect');
                        const price = document.getElementById('bulk-price');
                        const submit = document.getElementById('bulk-submit');
                        const tpl = form.dataset.urlTemplate;

                        function sync() {
                            const id = select.value;
                            if (id) {
                                form.action = tpl.replace('___DECL___', id);
                                price.disabled = false;
                                submit.disabled = false;
                            } else {
                                form.action = '#';
                                price.disabled = true;
                                submit.disabled = true;
                            }
                        }

                        select.addEventListener('change', sync);
                        sync();
                    })();
                </script>
            </div>
        </div>

        <!-- Main Content: Declarations & Payments -->
        <div class="row">
            <!-- Declarations Column -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">البيانات الجمركية ({{ $declarations->count() }})</h6>
                    </div>

                    <div class="card-body p-0">
                        <div id="declarationsAccordion">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped mb-0 text-center">
                                    <thead>
                                        <tr>
                                            <th>رقم البيان</th>
                                            <th>العميل</th>
                                            <th>عدد الحاويات</th>
                                            <th>إجمالي أسعار الحاويات</th>
                                            <th >إجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($declarations as $declaration)
                                            <!-- صف العناوين المختصرة لكل بيان -->
                                            <tr>
                                                <td>
                                                    {{ $declaration->statement_number ?? ($declaration->declaration_number ?? $declaration->id) }}
                                                </td>
                                                <td>{{ optional($declaration->client)->name ?? '—' }}</td>
                                                <td>{{ $declaration->containers->count() }}</td>
                                                <td>{{ number_format($declaration->calculated_price, 2) }}</td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-outline-primary" type="button"
                                                        data-toggle="collapse" data-target="#dec-{{ $declaration->id }}"
                                                        aria-expanded="false" aria-controls="dec-{{ $declaration->id }}">
                                                        <i class="fas fa-box-open"></i> عرض الحاويات
                                                    </button>
                                                </td>
                                            </tr>

                                            <!-- صف التفاصيل (Collapse) لكل بيان -->
                                            <tr class="bg-light">
                                                <td colspan="6" class="p-0">
                                                    <div id="dec-{{ $declaration->id }}" class="collapse"
                                                        data-parent="#declarationsAccordion">
                                                        <div class="p-3">

                                                            <!-- نموذج تحديث جماعي سريع لهذا البيان -->
                                                            @if ($declaration->containers->isNotEmpty())
                                                                <div
                                                                    class="alert alert-light d-flex align-items-center justify-content-between">
                                                                    <div>
                                                                        <i class="fas fa-boxes"></i>
                                                                        تحديث سعر كل الحاويات في هذا البيان:
                                                                    </div>
                                                                    <form method="POST"
                                                                        action="{{ route('revenues.clearance.bulk_containers_price', ['office' => $office->id, 'declaration' => $declaration->id]) }}"
                                                                        class="form-inline">
                                                                        @csrf
                                                                        <input type="number" name="price"
                                                                            min="0" step="1"
                                                                            class="form-control form-control-sm mr-2"
                                                                            placeholder="سعر موحّد" required>
                                                                        <button class="btn btn-sm btn-warning">
                                                                            <i class="fas fa-sync"></i> تحديث الكل
                                                                        </button>
                                                                    </form>
                                                                </div>

                                                                <!-- جدول الحاويات -->
                                                                <h6 class="mb-3">الحاويات
                                                                    ({{ $declaration->containers->count() }})
                                                                    :</h6>
                                                                <div class="table-responsive">
                                                                    <table class="table table-sm table-striped text-center">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>#</th>
                                                                                <th>رقم الحاوية</th>
                                                                                <th>الحجم</th>
                                                                                <th>الحالة</th>
                                                                                <th>تاريخ النقل</th>
                                                                                <th>السعر</th>
                                                                                <th>تعديل</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach ($declaration->containers as $container)
                                                                                <tr>
                                                                                    <td>{{ $container->id }}</td>
                                                                                    <td>{{ $container->number ?? $container->container_number }}
                                                                                    </td>
                                                                                    <td>{{ $container->size }}</td>
                                                                                    <td>{{ $container->status }}</td>
                                                                                    <td>{{ optional($container->transfer_date)->format('Y-m-d') }}
                                                                                    </td>
                                                                                    <td>{{ (int) ($container->price ?? 0) }}
                                                                                    </td>
                                                                                    <td>
                                                                                        <form method="POST"
                                                                                            action="{{ route('revenues.clearance.update_container_price', ['office' => $office->id, 'container' => $container->id]) }}"
                                                                                            class="d-flex align-items-center ">
                                                                                            @csrf
                                                                                            @method('PATCH')
                                                                                            <input type="number"
                                                                                                name="price"
                                                                                                value="{{ (int) ($container->price ?? 0) }}"
                                                                                                min="0"
                                                                                                step="1"
                                                                                                class="form-control form-control-sm"
                                                                                                style="width: 120px;">
                                                                                            <button
                                                                                                class="btn btn-sm btn-primary ml-2">
                                                                                                <i class="fas fa-save"></i>
                                                                                            </button>
                                                                                        </form>
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            @else
                                                                <p class="text-center text-muted m-0">لا توجد حاويات مرتبطة
                                                                    بهذا البيان.</p>
                                                            @endif

                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted p-4">
                                                    لا توجد بيانات جمركية لهذا الشهر.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Payments Column -->
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-success">المبالغ المستلمة
                            ({{ $incomeTransactions->count() }})</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @forelse($incomeTransactions as $transaction)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong
                                            class="d-block">{{ number_format($transaction->total_amount, 2) }}</strong>
                                        <small
                                            class="text-muted">{{ $transaction->created_at->format('Y-m-d H:i') }}</small>
                                    </div>
                                    <i class="fas fa-check-circle text-success"></i>
                                </li>
                            @empty
                                <li class="list-group-item text-center text-muted">لا توجد دفعات مسجلة لهذا الشهر.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
