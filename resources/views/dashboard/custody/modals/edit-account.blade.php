<div class="modal fade" id="editAccountModal{{ $account->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" action="{{ route('custody-accounts.update', $account) }}" method="POST">
            @csrf @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">تعديل عهدة</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>الرصيد الافتتاحي</label>
                    <input type="number" step="0.01" name="opening_balance" class="form-control"
                        value="{{ $account->opening_balance }}">
                </div>
                <div class="form-group">
                    <label>الحالة</label>
                    <select name="status" class="form-control">
                        <option value="open" {{ $account->status === 'open' ? 'selected' : '' }}>مفتوحة</option>
                        <option value="closed"{{ $account->status === 'closed' ? 'selected' : '' }}>مغلقة</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>ملاحظات</label>
                    <textarea name="notes" class="form-control" rows="3">{{ $account->notes }}</textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary">حفظ</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
            </div>
        </form>
    </div>
</div>
