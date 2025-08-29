<div class="modal fade" id="showCarModal{{ $car->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تفاصيل السيارة: {{ $car->type_car }} - {{ $car->number }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            @php
                $remaining = $car->remaining_km;
            @endphp
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>السائق</span>
                                <span>{{ $car->driver?->name ?? '-' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>الرقم</span>
                                <span>{{ $car->number }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>النوع</span>
                                <span>{{ $car->type_car ?? '-' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>العداد الحالي</span>
                                <span>{{ number_format($car->odometer ?? 0) }} كم</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>دورة الزيت</span>
                                <span>{{ number_format($car->oil_change_number ?? 0) }} كم</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6 mb-3">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>آخر تغيير (تاريخ)</span>
                                <span>{{ optional($car?->lastOilChange?->date)->format('Y-m-d') ?? '-' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>آخر تغيير (قراءة)</span>
                                <span>{{ $car->lastOilChange ? number_format($car->lastOilChange->km_before) . ' كم' : '-' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>الباقي</span>
                                <span>{{ $remaining !== null ? number_format($remaining) . ' كم' : '-' }}</span>
                            </li>
                            <li class="list-group-item">
                                {{-- شريط تقدّم بسيط --}}
                                @php
                                    $pct = $car->oil_progress_percent ?? 0;
                                    $bar = 'bg-success';
                                    if (is_null($remaining)) {
                                        $bar = 'bg-secondary';
                                    } elseif ($remaining <= 0) {
                                        $bar = 'bg-danger';
                                    } elseif ($remaining <= 500) {
                                        $bar = 'bg-warning';
                                    }
                                @endphp
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar {{ $bar }}" style="width: {{ $pct }}%">
                                    </div>
                                </div>
                                <small class="text-muted d-block mt-1">نسبة استهلاك دورة الزيت:
                                    {{ $pct }}%</small>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- (اختياري) جدول سجلات تاريخية إن رغبت --}}
                {{--
            <h6 class="mt-3">السجل التاريخي للتغييرات</h6>
            <table class="table table-sm">
                <thead><tr><th>التاريخ</th><th>القراءة وقت التغيير</th></tr></thead>
                <tbody>
                    @foreach ($car->oilChanges as $oc)
                        <tr>
                            <td>{{ optional($oc->date)->format('Y-m-d') ?? '-' }}</td>
                            <td>{{ number_format($oc->km_before) }} كم</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            --}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>
