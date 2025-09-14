<div class="modal fade" id="deleteAccountModal{{ $account->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" action="{{ route('custody-accounts.destroy', $account) }}" method="POST">
            @csrf @method('DELETE')
            <div class="modal-header">
                <h5 class="modal-title text-danger">حذف العهدة</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">هل أنت متأكد من حذف عهدة <strong>{{ $account->owner->name }}</strong>؟</div>
            <div class="modal-footer">
                <button class="btn btn-danger">تأكيد الحذف</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
            </div>
        </form>
    </div>
</div>
