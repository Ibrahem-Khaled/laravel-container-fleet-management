{{-- resources/views/employees/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="h4 mb-3">الموظفون</h1>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>الموظف</th>
                            <th>الراتب الشهري</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $u)
                            <tr>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $u->avatar ?? 'https://via.placeholder.com/40' }}"
                                            class="rounded-circle mr-2" width="40" height="40" alt="">
                                        <div>
                                            <div class="font-weight-bold">{{ $u->name }}</div>
                                            <div class="text-muted small">{{ $u->phone }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">{{ number_format($u->salary, 2) }}</td>
                                <td class="align-middle">
                                    <a class="btn btn-primary btn-sm" href="{{ route('expenses.employees.show', $u) }}">
                                        عرض التفاصيل
                                    </a>
                                    <a class="btn btn-secondary btn-sm"
                                        href="{{ route('expenses.employees.tips', $u) }}">
                                        عرض التربات
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection
