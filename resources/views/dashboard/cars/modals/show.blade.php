<div class="modal fade" id="showCarModal{{ $car->id }}" tabindex="-1" role="dialog"
    aria-labelledby="showCarModalLabel{{ $car->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showCarModalLabel{{ $car->id }}">تفاصيل السيارة: {{ $car->number }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tr>
                        <th>رقم اللوحة</th>
                        <td>{{ $car->number }}</td>
                    </tr>
                    <tr>
                        <th>الرقم التسلسلي</th>
                        <td>{{ $car->serial_number }}</td>
                    </tr>
                    <tr>
                        <th>نوع المركبة</th>
                        <td>{{ $car->type === 'transfer' ? 'نقل' : 'خاص' }}</td>
                    </tr>
                    <tr>
                        <th>السائق</th>
                        <td>{{ $car->driver->name ?? 'غير معين' }}</td>
                    </tr>
                    <tr>
                        <th>نوع السيارة (ماركة)</th>
                        <td>{{ $car->type_car ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>الموديل (سنة)</th>
                        <td>{{ $car->model_car ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>انتهاء الرخصة</th>
                        <td>{{ $car->license_expire ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>انتهاء الفحص</th>
                        <td>{{ $car->scan_expire ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>انتهاء بطاقة التشغيل</th>
                        <td>{{ $car->card_run_expire ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>انتهاء التأمين</th>
                        <td>{{ $car->insurance_expire ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>عداد تغيير الزيت القادم</th>
                        <td>{{ $car->oil_change_number ? number_format($car->oil_change_number) . ' كم' : '-' }}</td>
                    </tr>
                    <tr>
                        <th>تاريخ الإضافة</th>
                        <td>{{ $car->created_at->format('Y-m-d h:i A') }}</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>
