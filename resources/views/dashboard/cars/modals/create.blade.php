<div class="modal fade" id="createCarModal" tabindex="-1" role="dialog" aria-labelledby="createCarModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCarModalLabel">إضافة سيارة جديدة</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('cars.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @if ($errors->any() && !old('_method'))
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
                            <label for="number">رقم اللوحة</label>
                            <input type="text" class="form-control" id="number" name="number"
                                value="{{ old('number') }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="serial_number">الرقم التسلسلي</label>
                            <input type="number" class="form-control" id="serial_number" name="serial_number"
                                value="{{ old('serial_number') }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="type">نوع المركبة</label>
                            <select class="form-control" id="type" name="type" required>
                                <option value="transfer" {{ old('type') == 'transfer' ? 'selected' : '' }}>نقل</option>
                                <option value="private" {{ old('type') == 'private' ? 'selected' : '' }}>خاص</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="driver_id">السائق</label>
                            <select class="form-control" id="driver_id" name="driver_id">
                                <option value="">-- اختر سائق --</option>
                                @foreach ($availableDrivers as $driver)
                                    <option value="{{ $driver->id }}"
                                        {{ old('driver_id') == $driver->id ? 'selected' : '' }}>{{ $driver->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="type_car">نوع السيارة (ماركة)</label>
                            <input type="text" class="form-control" id="type_car" name="type_car"
                                value="{{ old('type_car') }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="model_car">موديل السيارة (سنة)</label>
                            <input type="number" class="form-control" id="model_car" name="model_car"
                                value="{{ old('model_car') }}" placeholder="YYYY">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="license_expire">تاريخ انتهاء الرخصة</label>
                            <input type="date" class="form-control" id="license_expire" name="license_expire"
                                value="{{ old('license_expire') }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="scan_expire">تاريخ انتهاء الفحص</label>
                            <input type="date" class="form-control" id="scan_expire" name="scan_expire"
                                value="{{ old('scan_expire') }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="card_run_expire">تاريخ انتهاء بطاقة التشغيل</label>
                            <input type="date" class="form-control" id="card_run_expire" name="card_run_expire"
                                value="{{ old('card_run_expire') }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="insurance_expire">تاريخ انتهاء التأمين</label>
                            <input type="date" class="form-control" id="insurance_expire" name="insurance_expire"
                                value="{{ old('insurance_expire') }}">
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="oil_change_number">عداد تغيير الزيت القادم (كم)</label>
                            <input type="number" class="form-control" id="oil_change_number" name="oil_change_number"
                                value="{{ old('oil_change_number', 10000) }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>
