<div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">تعديل بيانات: {{ $user->name }}</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group"><label>الاسم الكامل</label><input type="text" name="name"
                                class="form-control" value="{{ $user->name }}" required></div>
                        <div class="col-md-6 form-group"><label>الدور</label>
                            <select name="role_id" class="form-control" required>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}"
                                        {{ $user->role_id == $role->id ? 'selected' : '' }}>{{ $role->description }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group"><label>البريد الإلكتروني</label><input type="email"
                                name="email" class="form-control" value="{{ $user->email }}"></div>
                        <div class="col-md-6 form-group"><label>رقم الهاتف</label><input type="text" name="phone"
                                class="form-control" value="{{ $user->phone }}" required></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group"><label>كلمة مرور جديدة (اختياري)</label><input type="password"
                                name="password" class="form-control" placeholder="اتركه فارغاً لعدم التغيير"></div>
                        <div class="col-md-6 form-group"><label>تأكيد كلمة المرور</label><input type="password"
                                name="password_confirmation" class="form-control"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group"><label>تغيير الصورة الرمزية</label>
                            <div class="custom-file"><input type="file" name="avatar"
                                    class="custom-file-input"><label class="custom-file-label">اختر صورة
                                    جديدة...</label></div>
                        </div>
                        <div class="col-md-6 form-group"><label>الحالة</label>
                            <select name="is_active" class="form-control" required>
                                <option value="1" {{ $user->is_active ? 'selected' : '' }}>نشط</option>
                                <option value="0" {{ !$user->is_active ? 'selected' : '' }}>غير نشط</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                    <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                </div>
            </form>
        </div>
    </div>
</div>
