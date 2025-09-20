<!-- Edit Office Modal -->
<div class="modal fade" id="editOfficeModal{{ $office->id }}" tabindex="-1" role="dialog"
    aria-labelledby="editOfficeModalLabel{{ $office->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editOfficeModalLabel{{ $office->id }}">تعديل مكتب: {{ $office->name }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('clearance-offices.update', $office->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit-name-{{ $office->id }}">اسم المكتب <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit-name-{{ $office->id }}"
                                    name="name" value="{{ old('name', $office->name) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit-operational_number-{{ $office->id }}">الرقم التشغيلي</label>
                                <input type="number" class="form-control"
                                    id="edit-operational_number-{{ $office->id }}" name="operational_number"
                                    value="{{ old('operational_number', $office->operational_number) }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit-email-{{ $office->id }}">البريد الإلكتروني</label>
                                <input type="email" class="form-control" id="edit-email-{{ $office->id }}"
                                    name="email" value="{{ old('email', $office->email) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit-phone-{{ $office->id }}">رقم الهاتف</label>
                                <input type="text" class="form-control" id="edit-phone-{{ $office->id }}"
                                    name="phone" value="{{ old('phone', $office->phone) }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit-is_active-{{ $office->id }}">الحالة <span
                                        class="text-danger">*</span></label>
                                <select class="form-control" id="edit-is_active-{{ $office->id }}" name="is_active"
                                    required>
                                    <option value="1"
                                        {{ old('is_active', $office->is_active) == 1 ? 'selected' : '' }}>نشط</option>
                                    <option value="0"
                                        {{ old('is_active', $office->is_active) == 0 ? 'selected' : '' }}>غير نشط
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit-tax_enabled-{{ $office->id }}">تفعيل الضرائب <span
                                        class="text-danger">*</span></label>
                                <select class="form-control" id="edit-tax_enabled-{{ $office->id }}" name="tax_enabled"
                                    required>
                                    <option value="1"
                                        {{ old('tax_enabled', $office->tax_enabled) == 1 ? 'selected' : '' }}>مفعلة</option>
                                    <option value="0"
                                        {{ old('tax_enabled', $office->tax_enabled) == 0 ? 'selected' : '' }}>معطلة
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>تغيير شعار المكتب (Avatar)</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input"
                                        id="edit-avatar-{{ $office->id }}" name="avatar">
                                    <label class="custom-file-label" for="edit-avatar-{{ $office->id }}">اختر ملف
                                        جديد...</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                </div>
            </form>
        </div>
    </div>
</div>
