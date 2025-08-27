<div class="modal fade" id="deleteTransactionModal{{ $transaction->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">تأكيد الحذف</h5><button type="button" class="close"
                        data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">هل أنت متأكد من رغبتك في حذف هذه الحركة المالية؟</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">نعم، قم بالحذف</button>
                </div>
            </form>
        </div>
    </div>
</div>
