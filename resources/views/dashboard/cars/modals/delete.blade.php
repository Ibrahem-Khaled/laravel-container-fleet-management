<div class="modal fade" id="deleteCarModal{{ $car->id }}" tabindex="-1" role="dialog"
    aria-labelledby="deleteCarModalLabel{{ $car->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCarModalLabel{{ $car->id }}">تأكيد الحذف</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من رغبتك في حذف السيارة ذات اللوحة رقم <strong>{{ $car->number }}</strong>؟
            </div>
            <div class="modal-footer">
                <form action="{{ route('cars.destroy', $car->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">نعم، حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>
