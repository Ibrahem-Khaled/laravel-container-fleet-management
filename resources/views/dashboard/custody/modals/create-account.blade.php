<div class="modal fade" id="createAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" action="{{ route('custody-accounts.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">إضافة عهدة</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>المستخدم</label>
                    <select name="user_id" class="form-control" required>
                        @foreach (\App\Models\User::orderBy('name')->get() as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->role->description }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>رصيد افتتاحي</label>
                    <input type="number" step="0.01" name="opening_balance" class="form-control" value="0">
                </div>
                <div class="form-group">
                    <label>ملاحظات</label>
                    <textarea name="notes" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary">حفظ</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
            </div>
        </form>
    </div>
</div>
