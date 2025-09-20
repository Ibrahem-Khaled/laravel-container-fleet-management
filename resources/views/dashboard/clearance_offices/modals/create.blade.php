<!-- Create Office Modal -->
<div class="modal fade" id="createOfficeModal" tabindex="-1" role="dialog" aria-labelledby="createOfficeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createOfficeModalLabel">إضافة مكتب تخليص جديد</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('clearance-offices.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="create-name">اسم المكتب <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="create-name" name="name"
                                    value="{{ old('name') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="create-operational_number">الرقم التشغيلي</label>
                                <input type="number" class="form-control" id="create-operational_number"
                                    name="operational_number" value="{{ old('operational_number') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="create-email">البريد الإلكتروني</label>
                                <input type="email" class="form-control" id="create-email" name="email"
                                    value="{{ old('email') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="create-phone">رقم الهاتف</label>
                                <input type="text" class="form-control" id="create-phone" name="phone"
                                    value="{{ old('phone') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="create-is_active">الحالة <span class="text-danger">*</span></label>
                                <select class="form-control" id="create-is_active" name="is_active" required>
                                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>نشط
                                    </option>
                                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>غير نشط
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="create-tax_enabled">تفعيل الضرائب <span class="text-danger">*</span></label>
                                <select class="form-control" id="create-tax_enabled" name="tax_enabled" required>
                                    <option value="1" {{ old('tax_enabled', '1') == '1' ? 'selected' : '' }}>مفعلة
                                    </option>
                                    <option value="0" {{ old('tax_enabled') == '0' ? 'selected' : '' }}>معطلة
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>شعار المكتب (Avatar)</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="create-avatar" name="avatar">
                                    <label class="custom-file-label" for="create-avatar">اختر ملف...</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>
