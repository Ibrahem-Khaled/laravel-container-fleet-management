@extends('layouts.app')

@section('title', 'حسابات العهد')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h2 class="h5 mb-0">حسابات العُهد</h2>
    <button class="btn btn-primary btn-soft" data-toggle="modal" data-target="#modalCreate">
        إنشاء حساب
    </button>
</div>

<div class="card card-soft p-0">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="text-right">#</th>
                    <th class="text-right">المستخدم</th>
                    <th class="text-right">الرصيد الحالي</th>
                    <th class="text-right">الحالة</th>
                    <th class="text-right">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($accounts as $acc)
                    <tr>
                        <td>{{ $acc->id }}</td>
                        <td>
                            <div class="font-weight-600">{{ optional($acc->user)->name ?? '—' }}</div>
                            <div class="text-muted small">#{{ $acc->user_id }}</div>
                        </td>
                        <td class="text-monospace">
                            {{ number_format($acc->currentBalance(), 2) }}
                        </td>
                        <td>
                            @php $active = !$acc->deleted_at; @endphp
                            <span class="badge badge-{{ $active ? 'success' : 'secondary' }} badge-dot">
                                {{ $active ? 'نشط' : 'مغلق' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('custody.accounts.show', $acc) }}" class="btn btn-sm btn-outline-primary">تفاصيل</a>

                            {{-- زر تعديل يفتح نفس مودال التعديل الموحد ويملأ الحقول عبر data-* --}}
                            <button
                                class="btn btn-sm btn-outline-secondary js-edit"
                                data-toggle="modal" data-target="#modalEdit"
                                data-id="{{ $acc->id }}"
                                data-notes="{{ $acc->notes }}"
                                data-opening="{{ $acc->opening_balance }}"
                            >تعديل</button>

                            <form class="d-inline" action="{{ route('custody.accounts.destroy', $acc) }}" method="POST" onsubmit="return confirm('هل تريد الحذف؟');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">حذف</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-5">لا توجد حسابات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $accounts->links() }}
</div>

{{-- ========== Modal: Create Account ========== --}}
<div class="modal fade" id="modalCreate" tabindex="-1" role="dialog" aria-labelledby="modalCreateLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="modalCreateLabel" class="modal-title">إنشاء حساب عهدة</h5>
        <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('custody.accounts.store') }}" method="POST" novalidate>
        @csrf
        <input type="hidden" name="_form" value="create_account">
        <div class="modal-body">
            <div class="form-group">
                <label>المستخدم</label>
                <select name="user_id" class="form-control" required>
                    <option value="">— اختر —</option>
                    @foreach(\App\Models\User::orderBy('name')->get(['id','name']) as $u)
                        <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
                @error('user_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>الرصيد الافتتاحي</label>
                <input type="number" step="0.01" name="opening_balance" class="form-control" value="{{ old('opening_balance', 0) }}">
                @error('opening_balance')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>ملاحظات</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                @error('notes')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary btn-soft">حفظ</button>
            <button type="button" class="btn btn-light" data-dismiss="modal">إلغاء</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ========== Modal: Edit Account (موحّد) ========== --}}
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="modalEditLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="modalEditLabel" class="modal-title">تعديل حساب</h5>
        <button type="button" class="close ml-0" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <form id="formEdit" method="POST" novalidate>
        @csrf @method('PUT')
        <input type="hidden" name="_form" value="edit_account">
        <div class="modal-body">
            <div class="form-group">
                <label>الرصيد الافتتاحي</label>
                <input id="editOpening" type="number" step="0.01" name="opening_balance" class="form-control" value="">
                @error('opening_balance')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>ملاحظات</label>
                <textarea id="editNotes" name="notes" class="form-control" rows="3"></textarea>
                @error('notes')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary btn-soft">تحديث</button>
            <button type="button" class="btn btn-light" data-dismiss="modal">إلغاء</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    // تعبئة مودال التعديل ديناميكياً
    $('.js-edit').on('click', function () {
        var id      = $(this).data('id');
        var opening = $(this).data('opening');
        var notes   = $(this).data('notes') || '';

        $('#editOpening').val(opening);
        $('#editNotes').val(notes);

        var action = "{{ route('custody.accounts.update', ':id') }}".replace(':id', id);
        $('#formEdit').attr('action', action);
    });

    // عند وجود أخطاء تحقق، افتح المودال المناسب بناءً على old('_form')
    @if($errors->any())
        @if(old('_form') === 'create_account')
            $('#modalCreate').modal('show');
        @elseif(old('_form') === 'edit_account')
            $('#modalEdit').modal('show');
        @endif
    @endif
});
</script>
@endpush
