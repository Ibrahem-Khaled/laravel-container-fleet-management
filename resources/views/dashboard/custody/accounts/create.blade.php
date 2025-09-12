@extends('layouts.app')

@section('title', 'حسابات العهد')

@section('content')
    <form method="POST" action="{{ route('custody.accounts.store') }}"
        class="bg-white rounded-2xl shadow p-4 grid gap-4 max-w-xl">
        @csrf
        <label class="grid gap-1">
            <span>المستخدم</span>
            <select name="user_id" class="border rounded-lg p-2">
                @foreach ($users as $u)
                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
            </select>
        </label>

        <label class="grid gap-1">
            <span>الرصيد الافتتاحي</span>
            <input type="number" step="0.01" name="opening_balance" class="border rounded-lg p-2" />
        </label>

        <label class="grid gap-1">
            <span>ملاحظات</span>
            <textarea name="notes" class="border rounded-lg p-2"></textarea>
        </label>

        <div class="flex gap-3 justify-end">
            <a href="{{ route('custody.accounts.index') }}" class="px-4 py-2 rounded-xl bg-gray-200">إلغاء</a>
            <button class="px-4 py-2 rounded-xl bg-indigo-600 text-white">حفظ</button>
        </div>
    </form>
@endsection
