{{-- مودال إنشاء أمر نقل --}}
<div class="modal fade" id="createTransferModal" tabindex="-1" role="dialog" aria-labelledby="createTransferModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="createTransferModalLabel" class="modal-title">إضافة أمر نقل لحاوية</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="إغلاق"><span
                        aria-hidden="true">&times;</span></button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label>الحاوية</label>
                    <input type="text" id="container_search" class="form-control"
                        placeholder="اكتب رقم/اتجاه الحاوية...">
                    <input type="hidden" id="container_id">
                    <div id="container_suggestions" class="list-group mt-1"
                        style="display:none;max-height:220px;overflow:auto;"></div>
                    <small class="text-muted">اكتب للبحث… ثم اختر من القائمة.</small>
                </div>

                <div class="form-group">
                    <label>السعر/القيمة</label>
                    <input type="number" step="0.01" min="0" class="form-control" id="transfer_price"
                        placeholder="مثال: 2500.00" value="34.5">
                </div>

                <div class="form-group">
                    <label>ملاحظة (اختياري)</label>
                    <select class="form-control" id="transfer_note">
                        <option value="">اختر ملاحظة...</option>
                        <option value="فارغ">فارغ</option>
                        <option value="محمل">محمل</option>
                    </select>
                </div>

                <div id="summary_box" class="border rounded p-2" style="display:none;">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">إجمالي أوامر النقل الحالية:</span>
                        <strong id="current_sum" class="text-monospace">0.00</strong>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <span class="text-muted">الإجمالي بعد الإضافة:</span>
                        <strong id="sum_after" class="text-monospace">0.00</strong>
                    </div>
                    <div class="mt-2 small text-muted">آخر 5 أوامر:</div>
                    <ul id="last_orders" class="small mb-0"></ul>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" id="save_transfer_btn" class="btn btn-primary" disabled>حفظ الأمر</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
            </div>
        </div>
    </div>
</div>
