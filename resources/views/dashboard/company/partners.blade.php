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

        {{-- ملخص رؤوس الأموال --}}
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    إجمالي رؤوس الأموال
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($totalCapital, 2) }} ر.س
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-coins fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    عدد الشركاء النشطين
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $partners->where('is_active', true)->count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    متوسط رأس المال
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $partners->count() > 0 ? number_format($totalCapital / $partners->count(), 2) : '0.00' }} ر.س
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    إجمالي الحركات
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $partners->sum('movements_count') }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                {{-- بحث وفلترة --}}
                <form action="{{ route('partners.index') }}" method="GET" class="mb-3">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="input-group">
                                <input name="search" class="form-control" placeholder="ابحث بالاسم أو المستخدم أو البريد الإلكتروني..."
                                    value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button class="btn btn-primary">
                                        <i class="fas fa-search"></i> بحث
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="btn-group w-100" role="group">
                                <a href="{{ route('partners.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-list"></i> الكل
                                </a>
                                <a href="{{ route('partners.index', ['filter' => 'active']) }}"
                                   class="btn btn-outline-success {{ request('filter') == 'active' ? 'active' : '' }}">
                                    <i class="fas fa-check-circle"></i> النشطين
                                </a>
                                <a href="{{ route('partners.index', ['filter' => 'inactive']) }}"
                                   class="btn btn-outline-warning {{ request('filter') == 'inactive' ? 'active' : '' }}">
                                    <i class="fas fa-times-circle"></i> غير النشطين
                                </a>
                            </div>
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
                                <th>رأس المال الحالي</th>
                                <th>النسبة من الإجمالي</th>
                                <th>الحالة</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($partners as $p)
                                <tr>
                                    <td>
                                        <strong>{{ $p->name }}</strong>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ optional($p->user)->name ?? '-' }}</strong>
                                            @if(optional($p->user)->email)
                                                <br><small class="text-muted">{{ $p->user->email }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ optional($p->user->role)->description ?? optional($p->user->role)->name ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">{{ $p->movements_count }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="font-weight-bold {{ $p->currentBalance() >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($p->currentBalance(), 2) }} ر.س
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress mr-2" style="width: 60px; height: 20px;">
                                                <div class="progress-bar {{ $p->percentage > 0 ? 'bg-primary' : 'bg-secondary' }}"
                                                     style="width: {{ min($p->percentage, 100) }}%"></div>
                                            </div>
                                            <span class="font-weight-bold">{{ number_format($p->percentage, 1) }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($p->is_active)
                                            <span class="badge badge-success">نشط</span>
                                        @else
                                            <span class="badge badge-secondary">غير نشط</span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('partners.movements.index', $p) }}" class="btn btn-sm btn-info" title="عرض حركات رأس المال">
                                                <i class="fas fa-coins"></i>
                                            </a>
                                            <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editPartner{{ $p->id }}" title="تعديل الشريك">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('partners.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('حذف الشريك؟');">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-danger" title="حذف الشريك"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </div>

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
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-users fa-3x mb-3"></i>
                                            <p>لا يوجد شركاء مسجلين</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- معلومات الصفحة --}}
                <div class="row mt-3">
                    <div class="col-md-6">
                        <small class="text-muted">
                            عرض {{ $partners->firstItem() ?? 0 }} إلى {{ $partners->lastItem() ?? 0 }} من أصل {{ $partners->total() }} شريك
                        </small>
                    </div>
                    <div class="col-md-6 text-right">
                        <small class="text-muted">
                            الصفحة {{ $partners->currentPage() }} من {{ $partners->lastPage() }}
                        </small>
                    </div>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $partners->appends(request()->query())->links() }}
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
