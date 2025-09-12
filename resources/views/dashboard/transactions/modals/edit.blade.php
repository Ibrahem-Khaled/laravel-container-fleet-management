<div class="modal fade" id="editTransactionModal{{ $transaction->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('transactions.update', $transaction->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">تعديل الحركة #{{ $transaction->id }}</h5><button type="button" class="close"
                        data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>نوع الحركة</label>
                            <select class="form-control transaction-type-select" name="type" required>
                                <option value="income" {{ $transaction->type == 'income' ? 'selected' : '' }}>وارد
                                </option>
                                <option value="expense" {{ $transaction->type == 'expense' ? 'selected' : '' }}>منصرف
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>طريقة الدفع</label>
                            <select class="form-control" name="method" required>
                                <option value="cash" {{ $transaction->method == 'cash' ? 'selected' : '' }}>نقدي
                                </option>
                                <option value="bank" {{ $transaction->method == 'bank' ? 'selected' : '' }}>بنكي
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>المبلغ الإجمالي (شامل الضريبة)</label>
                            <input type="number" step="0.01" class="form-control" name="total_amount"
                                value="{{ $transaction->total_amount }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>هل بضريبة</label>
                            <select class="form-control" name="tax_value" required>
                                <option value="0" {{ $transaction->tax_value == 0 ? 'selected' : '' }}>لا</option>
                                <option value="15" {{ $transaction->tax_value > 0 ? 'selected' : '' }}>نعم
                                </option>
                            </select>
                        </div>
                    </div>

                    @php
                        $selectedKey = '';
                        if ($transaction->transactionable) {
                            if ($transaction->transactionable instanceof App\Models\User) {
                                $selectedKey = 'user_role_' . $transaction->transactionable->role_id;
                            } else {
                                $selectedKey = $transaction->transactionable_type;
                            }
                        }
                    @endphp
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>مرتبطة بـ (اختياري)</label>
                            <select class="form-control transactionable-type-select" name="transactionable_key">
                                <option value="">-- لا يوجد --</option>
                                @foreach ($transactionable_config as $key => $config)
                                    <option value="{{ $key }}"
                                        data-contexts="{{ implode(' ', $config['contexts']) }}"
                                        {{ $selectedKey == $key ? 'selected' : '' }}>
                                        {{ $config['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>السجل المحدد</label>
                            <select class="form-control transactionable-id-select" name="transactionable_id">
                                @if ($transaction->transactionable_id)
                                    <option value="{{ $transaction->transactionable_id }}" selected>
                                        {{-- ملاحظة: هذا الاسم قد لا يكون دقيقاً إذا تم تغيير النوع، لكن الجافاسكربت ستجلب القائمة الصحيحة عند الحاجة --}}
                                        {{ optional($transaction->transactionable)->name ?? (optional($transaction->transactionable)->number ?? 'سجل #' . $transaction->transactionable_id) }}
                                    </option>
                                @else
                                    <option value="">-- اختر النوع أولاً --</option>
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>ملاحظات</label>
                        <textarea class="form-control" name="notes" rows="2">{{ $transaction->notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                    <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                </div>
            </form>
        </div>
    </div>
</div>
