<div class="modal fade" id="editCarModal{{ $car->id }}" tabindex="-1" role="dialog"
    aria-labelledby="editCarModalLabel{{ $car->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCarModalLabel{{ $car->id }}">تعديل بيانات السيارة:
                    {{ $car->number }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('cars.update', $car->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $car->id }}">
                <div class="modal-body">
                    @if ($errors->any() && old('id') == $car->id)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="row">
                        <!-- الحقول -->
                        <div class="col-md-6 form-group">
                            <label for="number_{{ $car->id }}">رقم اللوحة</label>
                            <input type="text" class="form-control" id="number_{{ $car->id }}" name="number"
                                value="{{ old('number', $car->number) }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="serial_number_{{ $car->id }}">الرقم التسلسلي</label>
                            <input type="number" class="form-control" id="serial_number_{{ $car->id }}"
                                name="serial_number" value="{{ old('serial_number', $car->serial_number) }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="type_{{ $car->id }}">نوع المركبة</label>
                            <select class="form-control" id="type_{{ $car->id }}" name="type" required>
                                <option value="transfer" {{ old('type', $car->type) == 'transfer' ? 'selected' : '' }}>
                                    نقل</option>
                                <option value="private" {{ old('type', $car->type) == 'private' ? 'selected' : '' }}>
                                    خاص</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="driver_id_{{ $car->id }}">السائق</label>
                            <select class="form-control" id="driver_id_{{ $car->id }}" name="driver_id">
                                <option value="">-- غير معين --</option>
                                @if ($car->driver)
                                    <option value="{{ $car->driver_id }}" selected>{{ $car->driver->name }} (الحالي)
                                    </option>
                                @endif
                                @foreach ($availableDrivers as $driver)
                                    <option value="{{ $driver->id }}"
                                        {{ old('driver_id', $car->driver_id) == $driver->id ? 'selected' : '' }}>
                                        {{ $driver->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="type_car_{{ $car->id }}">نوع السيارة (ماركة)</label>
                            <input type="text" class="form-control" id="type_car_{{ $car->id }}"
                                name="type_car" value="{{ old('type_car', $car->type_car) }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="model_car_{{ $car->id }}">موديل السيارة (سنة)</label>
                            <input type="number" class="form-control" id="model_car_{{ $car->id }}"
                                name="model_car" value="{{ old('model_car', $car->model_car) }}" placeholder="YYYY">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="license_expire_{{ $car->id }}">تاريخ انتهاء الرخصة</label>
                            <input type="date" class="form-control" id="license_expire_{{ $car->id }}"
                                name="license_expire" value="{{ old('license_expire', $car->license_expire) }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="scan_expire_{{ $car->id }}">تاريخ انتهاء الفحص</label>
                            <input type="date" class="form-control" id="scan_expire_{{ $car->id }}"
                                name="scan_expire" value="{{ old('scan_expire', $car->scan_expire) }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="card_run_expire_{{ $car->id }}">تاريخ انتهاء بطاقة التشغيل</label>
                            <input type="date" class="form-control" id="card_run_expire_{{ $car->id }}"
                                name="card_run_expire" value="{{ old('card_run_expire', $car->card_run_expire) }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="insurance_expire_{{ $car->id }}">تاريخ انتهاء التأمين</label>
                            <input type="date" class="form-control" id="insurance_expire_{{ $car->id }}"
                                name="insurance_expire" value="{{ old('insurance_expire', $car->insurance_expire) }}">
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="oil_change_number_{{ $car->id }}">عداد تغيير الزيت القادم (كم)</label>
                            <input type="number" class="form-control" id="oil_change_number_{{ $car->id }}"
                                name="oil_change_number"
                                value="{{ old('oil_change_number', $car->oil_change_number) }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                    <button type="submit" class="btn btn-primary">تحديث</button>
                </div>
            </form>
        </div>
    </div>
</div>
