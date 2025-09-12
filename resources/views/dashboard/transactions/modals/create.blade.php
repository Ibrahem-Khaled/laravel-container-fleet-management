<div class="modal fade" id="createTransactionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('transactions.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">إضافة حركة مالية</h5><button type="button" class="close"
                        data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>نوع الحركة</label>
                            <select class="form-control transaction-type-select" name="type" required>
                                <option value="" selected disabled>-- اختر النوع --</option>
                                <option value="income">وارد</option>
                                <option value="expense">منصرف</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>طريقة الدفع</label>
                            <select class="form-control" name="method" required>
                                <option value="cash">نقدي</option>
                                <option value="bank">بنكي</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>المبلغ الإجمالي (شامل الضريبة)</label>
                            <input type="number" step="0.01" class="form-control" name="total_amount" required
                                placeholder="أدخل المبلغ الإجمالي">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>هل بضريبة</label>
                            <select class="form-control" name="tax_value" required>
                                <option value="0">لا</option>
                                <option value="15">نعم</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>مرتبطة بـ (اختياري)</label>
                            <select class="form-control transactionable-type-select" name="transactionable_key">
                                <option value="">-- لا يوجد --</option>
                                @foreach ($transactionable_config as $key => $config)
                                    <option value="{{ $key }}"
                                        data-contexts="{{ implode(' ', $config['contexts']) }}">
                                        {{ $config['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>السجل المحدد</label>
                            <select class="form-control transactionable-id-select" name="transactionable_id" disabled>
                                <option value="">-- اختر النوع أولاً --</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>ملاحظات</label>
                        <textarea class="form-control" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>
