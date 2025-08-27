<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">إضافة مستخدم جديد</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group"><label>الاسم الكامل</label><input type="text" name="name"
                                class="form-control" required></div>
                        <div class="col-md-6 form-group"><label>الدور</label>
                            <select name="role_id" class="form-control" required>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->description }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group"><label>البريد الإلكتروني (اختياري)</label><input type="email"
                                name="email" class="form-control"></div>
                        <div class="col-md-6 form-group"><label>رقم الهاتف</label><input type="text" name="phone"
                                class="form-control" required></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group"><label>كلمة المرور</label><input type="password"
                                name="password" class="form-control" required></div>
                        <div class="col-md-6 form-group"><label>تأكيد كلمة المرور</label><input type="password"
                                name="password_confirmation" class="form-control" required></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group"><label>الصورة الرمزية (اختياري)</label>
                            <div class="custom-file"><input type="file" name="avatar"
                                    class="custom-file-input"><label class="custom-file-label">اختر صورة...</label>
                            </div>
                        </div>
                        <div class="col-md-6 form-group"><label>الحالة</label>
                            <select name="is_active" class="form-control" required>
                                <option value="1">نشط</option>
                                <option value="0">غير نشط</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                    <button type="submit" class="btn btn-primary">حفظ المستخدم</button>
                </div>
            </form>
        </div>
    </div>
</div>
