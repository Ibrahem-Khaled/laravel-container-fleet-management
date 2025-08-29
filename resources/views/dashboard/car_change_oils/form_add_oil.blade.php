<form action="{{ route('car_change_oils.store') }}" method="POST">
    @csrf
    <div class="modal-header">
        <h5 class="modal-title" id="addOilChangeModalLabel">إضافة بيانات تغيير الزيت</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <input type="hidden" name="car_id" id="car_id" value="{{ $car->id }}">
        <div class="form-group">
            <label for="km">عدد الكيلومترات:</label>
            <input type="number" name="km" id="km" class="form-control" required>
        </div>
        @if ($date != null)
            <div class="form-group">
                <label for="date">التاريخ:</label>
                <input type="date" name="date" id="date" class="form-control" required>
            </div>
        @endif
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
        <button type="submit" class="btn btn-primary">إضافة</button>
    </div>
</form>
