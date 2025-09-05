@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        {{-- العنوان + breadcrumb --}}
        <div class="row">
            <div class="col-12">
                <h1 class="h3 mb-0 text-gray-800">الشركاء</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">لوحة التحكم</a></li>
                        <li class="breadcrumb-item active" aria-current="page">الشركاء</li>
                    </ol>
                </nav>
            </div>
        </div>

        @include('components.alerts')

        {{-- الأدوات --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">إدارة الشركاء</h6>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary mr-2" data-toggle="modal" data-target="#createPartnerModal">
                        <i class="fas fa-user-plus"></i> إضافة شريك
                    </button>
                    @php $me = auth()->user(); @endphp
                    <form action="{{ route('partners.attach.me') }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-success mr-2"
                            {{ !$me || !$me->role || $me->role->name !== 'partner' || $me->partner ? 'disabled' : '' }}>
                            <i class="fas fa-user-check"></i> إضافة نفسي كشريك
                        </button>
                    </form>
                    <a class="btn btn-info mr-2" href="{{ route('partners.profit.index') }}">
                        <i class="fas fa-coins"></i> توزيع الأرباح
                    </a>
                </div>
            </div>

            <div class="card-body">
                {{-- بحث --}}
                <form action="{{ route('partners.index') }}" method="GET" class="mb-3">
                    <div class="input-group">
                        <input name="search" class="form-control" placeholder="ابحث بالاسم أو المستخدم..."
                            value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary"><i class="fas fa-search"></i> بحث</button>
                        </div>
                    </div>
                </form>

                {{-- جدول الشركاء --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>الاسم</th>
                                <th>المستخدم</th>
                                <th>الدور</th>
                                <th>الحركات</th>
                                <th>الرصيد الحالي</th>
                                <th>النسبة</th> {{-- عمود النسبة --}}
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($partners as $p)
                                <tr>
                                    <td>
                                        {{ $p->name }}
                                        @unless ($p->is_active)
                                            <span class="badge badge-secondary">غير نشط</span>
                                        @endunless
                                    </td>
                                    <td>{{ optional($p->user)->name ?? '-' }} <small
                                            class="text-muted">{{ optional($p->user)->email }}</small></td>
                                    <td>{{ optional($p->user->role)->name ?? '-' }}</td>
                                    <td>{{ $p->movements_count }}</td>
                                    <td>{{ number_format($p->currentBalance(), 2) }}</td>
                                    <td>{{ number_format($p->percentage, 2) }}%</td>

                                    <td>
                                        <a href="{{ route('partners.movements.index', $p) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-list"></i> الحركات
                                        </a>

                                        {{-- تعديل --}}
                                        <button class="btn btn-sm btn-primary" data-toggle="modal"
                                            data-target="#editPartner{{ $p->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        {{-- حذف --}}
                                        <form action="{{ route('partners.destroy', $p) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('حذف الشريك؟');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                        </form>

                                        {{-- مودال تعديل --}}
                                        <div class="modal fade" id="editPartner{{ $p->id }}">
                                            <div class="modal-dialog">
                                                <form class="modal-content" method="POST"
                                                    action="{{ route('partners.update', $p) }}">
                                                    @csrf @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">تعديل شريك</h5>
                                                        <button type="button" class="close"
                                                            data-dismiss="modal">&times;</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label>الاسم للعرض</label>
                                                            <input name="name" class="form-control"
                                                                value="{{ $p->name }}" required>
                                                        </div>
                                                        {{-- الأفضل منع تغيير المستخدم بعد الإنشاء، لكن لو حابب تسمح: --}}
                                                        <div class="form-group">
                                                            <label>المستخدم المرتبط (رول partner فقط)</label>
                                                            <input class="form-control"
                                                                value="{{ optional($p->user)->name }} — {{ optional($p->user)->email }}"
                                                                readonly>
                                                            <input type="hidden" name="user_id"
                                                                value="{{ $p->user_id }}">
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="form-check">
                                                                <input type="checkbox" class="form-check-input"
                                                                    name="is_active" id="is_active_{{ $p->id }}"
                                                                    {{ $p->is_active ? 'checked' : '' }}>
                                                                <label for="is_active_{{ $p->id }}"
                                                                    class="form-check-label">نشط</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button class="btn btn-secondary"
                                                            data-dismiss="modal">إلغاء</button>
                                                        <button class="btn btn-primary">حفظ</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">لا يوجد شركاء</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center">
                    {{ $partners->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- مودال إنشاء شريك --}}
    <div class="modal fade" id="createPartnerModal">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('partners.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">إضافة شريك</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>اختر المستخدم (رول: partner)</label>
                        <select name="user_id" class="form-control" required>
                            <option value="" disabled selected>— اختر —</option>
                            @forelse($eligibleUsers as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} — {{ $u->email }}</option>
                            @empty
                                <option value="" disabled>لا يوجد مستخدمون مؤهلون</option>
                            @endforelse
                        </select>
                        <small class="text-muted">تظهر هنا فقط حسابات برول partner وغير مضافة كشركاء.</small>
                    </div>

                    <div class="form-group">
                        <label>الاسم للعرض</label>
                        <input name="name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="is_active" id="is_active_new" checked>
                            <label for="is_active_new" class="form-check-label">نشط</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
@endsection
