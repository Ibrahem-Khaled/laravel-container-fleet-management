<!-- Delete Office Modal -->
<div class="modal fade" id="deleteOfficeModal{{ $office->id }}" tabindex="-1" role="dialog"
    aria-labelledby="deleteOfficeModalLabel{{ $office->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteOfficeModalLabel{{ $office->id }}">تأكيد الحذف</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من رغبتك في حذف مكتب "<strong>{{ $office->name }}</strong>"؟
                <br>
                <small class="text-warning">سيتم نقل المكتب إلى الأرشيف ويمكن استعادته لاحقاً.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                <form action="{{ route('clearance-offices.destroy', $office->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">نعم، احذف</button>
                </form>
            </div>
        </div>
    </div>
</div>
