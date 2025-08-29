<div class="modal fade" id="addReadingModal{{ $car->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('car_change_oils.store') }}" method="POST">
                @csrf
                <input type="hidden" name="car_id" value="{{ $car->id }}">
                <input type="hidden" name="is_oil_change" value="0">

                <div class="modal-header">
                    <h5 class="modal-title">إضافة قراءة عدّاد — {{ $car->number }}</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>القراءة الجديدة (كم)</label>
                        <input type="number" name="km" class="form-control" min="{{ $car->odometer ?? 0 }}"
                            required>
                        <small class="text-muted">لا يسمح بأقل من {{ number_format($car->odometer ?? 0) }}.</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> حفظ القراءة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
