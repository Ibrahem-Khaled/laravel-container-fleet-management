@extends('layouts.app')

@section('title', 'حسابات العهد')

@section('content')
    <div class="bg-white rounded-2xl shadow p-4 grid gap-4 max-w-xl">
        <div class="text-sm text-gray-600">الرصيد المتوقع الآن: <span
                class="font-semibold">{{ number_format($expected, 2) }}</span></div>

        <form method="POST" action="{{ route('custody.counts.store', $custody_account) }}" class="grid gap-4">
            @csrf
            <input type="hidden" name="total_expected" value="{{ $expected }}" />

            <label class="grid gap-1">
                <span>الإجمالي المعدود فعليًا</span>
                <input type="number" step="0.01" name="total_counted" class="border rounded-lg p-2" required />
            </label>

            <label class="grid gap-1">
                <span>ملاحظات</span>
                <textarea name="notes" class="border rounded-lg p-2"></textarea>
            </label>

            <div class="flex gap-3 justify-end">
                <a href="{{ route('custody.accounts.show', $custody_account) }}"
                    class="px-4 py-2 rounded-xl bg-gray-200">رجوع</a>
                <button class="px-4 py-2 rounded-xl bg-emerald-600 text-white">اعتماد الجرد</button>
            </div>
        </form>
    </div>
@endsection
