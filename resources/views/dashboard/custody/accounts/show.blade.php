@extends('layouts.app')

@section('title', 'تفاصيل حساب عهدة #'.$custody_account->id)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h2 class="h5 mb-1">حساب عهدة #{{ $custody_account->id }}</h2>
        <div class="text-muted">المستخدم: {{ optional($custody_account->user)->name ?? '—' }}</div>
    </div>
    <div class="btn-group">
        <button class="btn btn-primary btn-soft" data-toggle="modal" data-target="#modalEntry">تسجيل حركة</button>
        <button class="btn btn-outline-primary btn-soft" data-toggle="modal" data-target="#modalCount">جرد نقدية</button>
        <a href="{{ route('custody.accounts.index') }}" class="btn btn-light">رجوع</a>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <div class="card card-soft p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted">الرصيد الحالي</div>
                    <div class="h3 mb-0 text-monospace">{{ number_format($balance, 2) }}</div>
                </div>
                <span class="badge badge-{{ $balance>=0?'success':'danger' }} badge-dot">
                    {{ $balance>=0?'دائن':'مدين' }}
                </span>
            </div>
            @if($custody_account->notes)
                <hr><div class="small text-muted">{{ $custody_account->notes }}</div>
            @endif
        </div>
    </div>
    <div class="col-md-8 mb-3">
        <div class="card card-soft">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-right">#</th>
                            <th class="text-right">التاريخ</th>
                            <th class="text-right">الوصف</th>
                            <th class="text-right">مدين</th>
                            <th class="text-right">دائن</th>
                            <th class="text-right">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($entries as $e)
                            <tr>
                                <td>{{ $e->id }}</td>
                                <td>{{ \Illuminate\Support\Carbon::parse($e->occurred_at)->format('Y-m-d H:i') }}</td>
                                <td>{{ $e->description }}</td>
                                <td class="text-monospace">{{ $e->debit ? number_format($e->debit,2) : '—' }}</td>
                                <td class="text-monospace">{{ $e->credit ? number_format($e->credit,2) : '—' }}</td>
                                <td>
                                    <form action="{{ route('custody.entries.destroy', [$custody_account, $e]) }}" method="POST" onsubmit="return confirm('حذف الحركة؟');" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-5">لا توجد حركات بعد</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">{{ $entries->links() }}</div>
    </div>
</div>

{{-- ========== Modal: تسجيل حركة ========== --}}
<div class="modal fade" id="modalEntry" tabindex="-1" role="dialog" aria-labelledby="modalEntryLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="modalEntryLabel" class="modal-title">تسجيل حركة</h5>
        <button type="button" class="close ml-0" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <form action="{{ route('custody.entries.store', $custody_account) }}" method="POST" novalidate>
        @csrf
        <input type="hidden" name="_form" value="create_entry">
        <div class="modal-body">
            <div class="form-group">
                <label>التاريخ</label>
                <input type="datetime-local" name="occurred_at" class="form-control" value="{{ old('occurred_at', now()->format('Y-m-d\TH:i')) }}" required>
                @error('occurred_at')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>الوصف</label>
                <input type="text" name="description" class="form-control" value="{{ old('description') }}" required>
                @error('description')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>مدين</label>
                    <input type="number" step="0.01" name="debit" class="form-control" value="{{ old('debit', 0) }}">
                    @error('debit')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="form-group col-md-6">
                    <label>دائن</label>
                    <input type="number" step="0.01" name="credit" class="form-control" value="{{ old('credit', 0) }}">
                    @error('credit')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary btn-soft">حفظ الحركة</button>
            <button type="button" class="btn btn-light" data-dismiss="modal">إلغاء</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ========== Modal: جرد نقدية ========== --}}
<div class="modal fade" id="modalCount" tabindex="-1" role="dialog" aria-labelledby="modalCountLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="modalCountLabel" class="modal-title">اعتماد جرد نقدية</h5>
        <button type="button" class="close ml-0" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <form action="{{ route('custody.counts.store', $custody_account) }}" method="POST" novalidate>
        @csrf
        <input type="hidden" name="_form" value="cash_count">
        {{-- المتوقع يتم حسابه في السيرفر: --}}
        <input type="hidden" name="total_expected" value="{{ $balance }}">
        <div class="modal-body">
            <div class="alert alert-info">
                الرصيد المتوقع: <strong class="text-monospace">{{ number_format($balance, 2) }}</strong>
            </div>
            <div class="form-group">
                <label>المحصي فعليًا</label>
                <input type="number" step="0.01" name="total_counted" class="form-control" value="{{ old('total_counted') }}" required>
                @error('total_counted')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>ملاحظات</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                @error('notes')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary btn-soft">اعتماد الجرد</button>
            <button type="button" class="btn btn-light" data-dismiss="modal">إلغاء</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(function(){
    // فتح المودال الصحيح لو فيه أخطاء تحقق
    @if($errors->any())
        @if(old('_form') === 'create_entry')
            $('#modalEntry').modal('show');
        @elseif(old('_form') === 'cash_count')
            $('#modalCount').modal('show');
        @endif
    @endif
});
</script>
@endpush
