@extends('layouts.app')

@section('title', 'حسابات العهد')

@section('content')
    <form method="POST" action="{{ route('custody.entries.store', $custody_account) }}"
        class="bg-white rounded-2xl shadow p-4 grid gap-4 max-w-2xl">
        @csrf
        <label class="grid gap-1">
            <span>النوع</span>
            <select name="direction" class="border rounded-lg p-2">
                <option value="income">تحصيل (زيادة)</option>
                <option value="expense">مصروف (نقص)</option>
                <option value="issue">تسليم عهدة</option>
                <option value="return">توريد عهدة</option>
                <option value="transfer_in">تحويل وارد</option>
                <option value="transfer_out">تحويل صادر</option>
                <option value="adjustment">تسوية</option>
            </select>
        </label>

        <label class="grid gap-1">
            <span>المبلغ</span>
            <input type="number" step="0.01" name="amount" class="border rounded-lg p-2" required />
        </label>

        <label class="grid gap-1">
            <span>تاريخ الحركة</span>
            <input type="datetime-local" name="occurred_at" class="border rounded-lg p-2" />
        </label>

        <label class="grid gap-1">
            <span>ملاحظات</span>
            <textarea name="notes" class="border rounded-lg p-2"></textarea>
        </label>

        <div class="flex gap-3 justify-end">
            <a href="{{ route('custody.accounts.show', $custody_account) }}"
                class="px-4 py-2 rounded-xl bg-gray-200">رجوع</a>
            <button class="px-4 py-2 rounded-xl bg-indigo-600 text-white">حفظ</button>
        </div>
    </form>
@endsection
