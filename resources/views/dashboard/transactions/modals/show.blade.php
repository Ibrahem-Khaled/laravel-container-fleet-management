<div class="modal fade" id="showTransactionModal{{ $transaction->id }}" tabindex="-1" role="dialog"
    aria-labelledby="showTransactionModalLabel{{ $transaction->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showTransactionModalLabel{{ $transaction->id }}">
                    <i class="fas fa-file-invoice-dollar mr-2"></i>تفاصيل الحركة المالية #{{ $transaction->id }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="p-3 mb-3 rounded" style="background-color: #f8f9fc;">
                    <h5 class="text-primary font-weight-bold">الملخص المالي</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-receipt text-muted mr-2"></i>المبلغ الأساسي (قبل الضريبة)</span>
                            <span class="font-weight-bold text-monospace">{{ number_format($transaction->amount, 2) }}
                                $</span>
                        </li>

                        @if ($transaction->tax_value > 0)
                            @php
                                $taxAmountValue = $transaction->total_amount - $transaction->amount;
                            @endphp
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span><i class="fas fa-percentage text-muted mr-2"></i>قيمة الضريبة المضافة</span>
                                <span class="font-weight-bold text-monospace">
                                    {{ number_format($taxAmountValue, 2) }} $
                                    <small class="text-muted">({{ (float) $transaction->tax_value }}%)</small>
                                </span>
                            </li>
                        @endif

                        <li
                            class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent border-top pt-3">
                            <span><i class="fas fa-wallet text-muted mr-2"></i>المبلغ الإجمالي</span>
                            @if ($transaction->type == 'income')
                                <h4 class="font-weight-bold text-success text-monospace mb-0">+
                                    {{ number_format($transaction->total_amount, 2) }} $</h4>
                            @else
                                <h4 class="font-weight-bold text-danger text-monospace mb-0">-
                                    {{ number_format($transaction->total_amount, 2) }} $</h4>
                            @endif
                        </li>
                    </ul>
                </div>
                <hr>
                <h5 class="text-primary font-weight-bold">تفاصيل الحركة</h5>
                <dl class="row">
                    <dt class="col-sm-3"><i class="fas fa-exchange-alt text-muted mr-2"></i>نوع الحركة</dt>
                    <dd class="col-sm-9">
                        @if ($transaction->type == 'income')
                            <span class="badge badge-success">وارد</span>
                        @else
                            <span class="badge badge-danger">منصرف</span>
                        @endif
                    </dd>
                    <dt class="col-sm-3"><i class="fas fa-money-check-alt text-muted mr-2"></i>طريقة الدفع</dt>
                    <dd class="col-sm-9">
                        @if ($transaction->method == 'cash')
                            <span class="badge badge-secondary">نقدي</span>
                        @else
                            <span class="badge badge-info">بنكي</span>
                        @endif
                    </dd>
                    <dt class="col-sm-3"><i class="fas fa-calendar-plus text-muted mr-2"></i>تاريخ الإنشاء</dt>
                    <dd class="col-sm-9">{{ $transaction->created_at->format('Y-m-d h:i A') }}</dd>
                </dl>
                <hr>
                <h5 class="text-primary font-weight-bold">البيانات المرتبطة</h5>
                <dl class="row">
                    <dt class="col-sm-3"><i class="fas fa-link text-muted mr-2"></i>مرتبطة بـ</dt>
                    <dd class="col-sm-9">
                        @if ($transaction->transactionable)
                            @php
                                $configKey = '';
                                if ($transaction->transactionable instanceof App\Models\User) {
                                    $configKey = 'user_role_' . $transaction->transactionable->role_id;
                                } else {
                                    $configKey = $transaction->transactionable_type;
                                }
                                $config = $transactionable_config[$configKey] ?? null;
                            @endphp
                            @if ($config)
                                <span class="badge badge-primary">{{ $config['name'] }}</span>
                                <strong
                                    class="mx-1">{{ $transaction->transactionable->{$config['display_column']} }}</strong>
                            @else
                                <span class="text-danger">سجل مرتبط غير معرف</span>
                            @endif
                        @else
                            <span class="text-muted">غير مرتبطة بأي سجل</span>
                        @endif
                    </dd>
                    <dt class="col-sm-3"><i class="fas fa-sticky-note text-muted mr-2"></i>ملاحظات</dt>
                    <dd class="col-sm-9">
                        @if ($transaction->notes)
                            <p class="mb-0 font-italic" style="white-space: pre-wrap;">{{ $transaction->notes }}</p>
                        @else
                            <span class="text-muted">لا توجد ملاحظات</span>
                        @endif
                    </dd>
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i
                        class="fas fa-times mr-1"></i>إغلاق</button>
            </div>
        </div>
    </div>
</div>
