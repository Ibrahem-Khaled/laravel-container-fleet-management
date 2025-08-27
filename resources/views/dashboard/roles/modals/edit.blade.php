<!-- Edit Role Modal -->
<div class="modal fade" id="editRoleModal{{ $role->id }}" tabindex="-1" role="dialog"
    aria-labelledby="editRoleModalLabel{{ $role->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRoleModalLabel{{ $role->id }}">تعديل الدور: {{ $role->name }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('roles.update', $role->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name{{ $role->id }}">اسم الدور (باللغة الإنجليزية)</label>
                        <input type="text"
                            class="form-control @error('name', 'update' . $role->id) is-invalid @enderror"
                            id="name{{ $role->id }}" name="name" value="{{ old('name', $role->name) }}" required>
                        @error('name', 'update' . $role->id)
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="description{{ $role->id }}">الوصف (باللغة العربية)</label>
                        <input type="text"
                            class="form-control @error('description', 'update' . $role->id) is-invalid @enderror"
                            id="description{{ $role->id }}" name="description"
                            value="{{ old('description', $role->description) }}">
                        @error('description', 'update' . $role->id)
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
