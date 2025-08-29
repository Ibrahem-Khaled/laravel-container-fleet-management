<div class="modal fade" id="addChangeModal{{ $car->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('car_change_oils.store') }}" method="POST">
                @csrf
                <input type="hidden" name="car_id" value="{{ $car->id }}">
                <input type="hidden" name="is_oil_change" value="1">

                <div class="modal-header">
                    <h5 class="modal-title">تثبيت تغيير زيت — {{ $car->number }}</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>قراءة العداد وقت التغيير</label>
                        <input type="number" name="km" class="form-control"
                            min="{{ optional($car->lastOilChange)->km_before ?? 0 }}" required>
                    </div>
                    <div class="form-group">
                        <label>تاريخ التغيير</label>
                        <input type="date" name="date" value="{{ now()->toDateString() }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>دورة الزيت الحالية</label>
                        <input type="number" value="{{ $car->oil_change_number ?? 0 }}" class="form-control" disabled>
                        <small class="text-muted">يمكن تغييرها من صفحة السيارة عند الحاجة.</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-dark">
                        <i class="fas fa-oil-can"></i> تثبيت التغيير
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
