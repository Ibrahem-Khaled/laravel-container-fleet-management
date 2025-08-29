<div class="modal fade" id="showDec{{ $dec->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تفاصيل البيان: {{ $dec->statement_number }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <div class="row">
                    {{-- معلومات أساسية --}}
                    <div class="col-lg-4 mb-3">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>رقم البيان</span><span>{{ $dec->statement_number }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>العميل</span><span>{{ $dec->client?->name ?? '-' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>مكتب التخليص</span><span>{{ $dec->clearanceOffice?->name ?? '-' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>تاريخ الانتهاء</span>
                                <span>{{ $dec->expire_date ? \Illuminate\Support\Carbon::parse($dec->expire_date)->format('Y-m-d') : '-' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>الوزن</span><span>{{ $dec->weight ?? '-' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>الحالة</span>
                                @php
                                    $statusBadge = match ($dec->statement_status) {
                                        'cleared', 'released' => 'success',
                                        'pending' => 'warning',
                                        'canceled', 'rejected' => 'secondary',
                                        default => 'info',
                                    };
                                @endphp
                                <span class="badge badge-{{ $statusBadge }}">{{ $dec->statement_status ?? '-' }}</span>
                            </li>
                        </ul>
                    </div>

                    {{-- الحاويات — مجمّعة حسب الحالة في جداول منفصلة --}}
                    <div class="col-lg-8 mb-3">
                        @php
                            // نجمع الحاويات حسب الحالة
                            $groups = $dec->containers->groupBy(fn($c) => $c->status ?: 'غير مُحدد');

                            // ماب بسيط لاختيار لون البادج/العناوين لكل حالة
                            $badgeFor = function ($s) {
                                $k = mb_strtolower((string) $s);
                                return match (true) {
                                    str_contains($k, 'محمل') || str_contains($k, 'load') => 'success',
                                    str_contains($k, 'تفريغ') || str_contains($k, 'unload') => 'warning',
                                    str_contains($k, 'نقل') || str_contains($k, 'transit') => 'info',
                                    str_contains($k, 'فارغ') || str_contains($k, 'empty') => 'secondary',
                                    str_contains($k, 'حجز') || str_contains($k, 'hold') => 'dark',
                                    default => 'primary',
                                };
                            };
                        @endphp

                        @forelse($groups as $status => $items)
                            <div class="card mb-3">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <span class="font-weight-bold">
                                        الحاويات — الحالة: {{ $status }}
                                    </span>
                                    <span class="badge badge-{{ $badgeFor($status) }}">{{ $items->count() }}</span>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive" style="max-height: 260px; overflow:auto;">
                                        <table class="table table-sm table-striped mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="width:60px">#</th>
                                                    <th>رقم الحاوية</th>
                                                    <th>الحجم</th>
                                                    <th>الحالة</th>
                                                    {{-- أضف أعمدة أخرى حسب جدولك (مثلاً: الموقع/ملاحظة) --}}
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($items as $i => $c)
                                                    <tr>
                                                        <td>{{ $i + 1 }}</td>
                                                        <td>{{ $c->number ?? '-' }}</td>
                                                        <td>{{ $c->size ?? '-' }}</td>
                                                        <td><span
                                                                class="badge badge-{{ $badgeFor($c->status) }}">{{ $c->status ?? '-' }}</span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-info mb-0">لا يوجد حاويات.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-light" data-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>
