<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    {{-- الشعار --}}
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('home') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">لوحة التحكم</div>
    </a>

    <hr class="sidebar-divider my-0">

    {{-- العنوان --}}
    <div class="sidebar-heading">
        الادارات
    </div>

    {{-- أدوات الذكاء الاصطناعي (مثال رابط placeholder) --}}
    <li class="nav-item {{ request()->is('ai-tools*') ? 'active' : '' }}">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-robot"></i>
            <span>ادوات تحليل الذكاء الاصطناعي</span>
        </a>
    </li>

    {{-- ادارة الصلاحيات --}}
    <li class="nav-item {{ request()->routeIs('roles.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('roles.index') }}">
            <i class="fas fa-fw fa-lock"></i>
            <span>ادارة الصلاحيات</span>
        </a>
    </li>

    {{-- ادارة الموظفين --}}
    <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('users.index') }}">
            <i class="fas fa-fw fa-users"></i>
            <span>ادارة الموظفين</span>
        </a>
    </li>

    {{-- ادارة السيارات --}}
    @php
        $carsOpen = request()->routeIs('cars.*') || request()->routeIs('car_change_oils.*');
    @endphp
    <li class="nav-item {{ $carsOpen ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCars"
           aria-expanded="{{ $carsOpen ? 'true' : 'false' }}" aria-controls="collapseCars">
            <i class="fas fa-fw fa-car"></i>
            <span>ادارة السيارات</span>
        </a>
        <div id="collapseCars" class="collapse {{ $carsOpen ? 'show' : '' }}" aria-labelledby="headingCars" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->routeIs('cars.*') ? 'active' : '' }}" href="{{ route('cars.index') }}">عرض السيارات</a>
                <a class="collapse-item {{ request()->routeIs('car_change_oils.*') ? 'active' : '' }}" href="{{ route('car_change_oils.index') }}">ادارة الزيوت وصيانات</a>
            </div>
        </div>
    </li>

    {{-- ادارة التشغيل --}}
    @php
        $opsOpen = request()->routeIs('containers.flow.*') || request()->routeIs('customs.*');
    @endphp
    <li class="nav-item {{ $opsOpen ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseOperations"
           aria-expanded="{{ $opsOpen ? 'true' : 'false' }}" aria-controls="collapseOperations">
            <i class="fas fa-fw fa-car"></i>
            <span>ادارة التشغيل</span>
        </a>
        <div id="collapseOperations" class="collapse {{ $opsOpen ? 'show' : '' }}" aria-labelledby="headingCars" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->routeIs('containers.flow.*') ? 'active' : '' }}" href="{{ route('containers.flow.index') }}">ادارة الحاويات</a>
                <a class="collapse-item {{ request()->routeIs('customs.*') ? 'active' : '' }}" href="{{ route('customs.index') }}">الحجوزات</a>
            </div>
        </div>
    </li>

    {{-- الادارة العامة للشركة --}}
    @php
        $companyOpen = request()->routeIs('partners.*') || request()->routeIs('company.finance');
    @endphp
    <li class="nav-item {{ $companyOpen ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#company"
           aria-expanded="{{ $companyOpen ? 'true' : 'false' }}" aria-controls="company">
            <i class="fas fa-fw fa-folder"></i>
            <span>الادارة العامة للشركة</span>
        </a>
        <div id="company" class="collapse {{ $companyOpen ? 'show' : '' }}" aria-labelledby="headingOperations" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->routeIs('partners.*') ? 'active' : '' }}" href="{{ route('partners.index') }}">ادارة شؤون الشركة والشركاء</a>
                <a class="collapse-item {{ request()->routeIs('company.finance') ? 'active' : '' }}" href="{{ route('company.finance') }}">حسابات الشركة</a>
            </div>
        </div>
    </li>

    {{-- ادارة العملاء --}}
    @php
        $clientsOpen = request()->routeIs('clearance-offices.*') || request()->is('clients/reports*');
    @endphp
    <li class="nav-item {{ $clientsOpen ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#customs"
           aria-expanded="{{ $clientsOpen ? 'true' : 'false' }}" aria-controls="customs">
            <i class="fas fa-fw fa-users"></i>
            <span>ادارة العملاء</span>
        </a>
        <div id="customs" class="collapse {{ $clientsOpen ? 'show' : '' }}" aria-labelledby="headingOperations" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->routeIs('clearance-offices.*') ? 'active' : '' }}" href="{{ route('clearance-offices.index') }}">اضافة بيان جمركي</a>
                <a class="collapse-item {{ request()->is('clients/reports*') ? 'active' : '' }}" href="#">التقارير المالية لعملاء</a>
            </div>
        </div>
    </li>

    {{-- الادارة المالية --}}
    @php
        $finOpen = request()->routeIs('transactions.*') || request()->routeIs('expenses.*') || request()->routeIs('revenues.*');
    @endphp
    <li class="nav-item {{ $finOpen ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseFinancial"
           aria-expanded="{{ $finOpen ? 'true' : 'false' }}" aria-controls="collapseFinancial">
            <i class="fas fa-fw fa-dollar-sign"></i>
            <span>الادارة المالية</span>
        </a>
        <div id="collapseFinancial" class="collapse {{ $finOpen ? 'show' : '' }}" aria-labelledby="headingFinancial" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">

                <a class="collapse-item {{ request()->routeIs('transactions.*') ? 'active' : '' }}" href="{{ route('transactions.index') }}">اليومية</a>
                <a class="collapse-item {{ request()->routeIs('custody-accounts.*') ? 'active' : '' }}" href="{{ route('custody-accounts.index') }}">حسابات العهد</a>

                {{-- المصروفات --}}
                <a class="collapse-item {{ request()->routeIs('expenses.*') ? 'active' : '' }}" href="{{ route('expenses.employees.index') }}">
                    رواتب الموظفين
                </a>

                {{-- الايرادات --}}
                <a class="collapse-item {{ request()->routeIs('revenues.clearance.index') ? 'active' : '' }}" href="{{ route('revenues.clearance.index') }}">
                    مكتب التخليص جمركي
                </a>

                {{-- أمثلة لعناصر متداخلة أخرى (Placeholder) --}}
                {{-- <a class="collapse-item" href="#">مصروفات تشغيل</a>
                <a class="collapse-item" href="#">مصروفات ادارية</a>
                <a class="collapse-item" href="#">حركة البيع والشراء</a>
                <a class="collapse-item" href="#">ايجار معدات</a>
                <a class="collapse-item" href="#">ايجار مكاتب</a> --}}

            </div>
        </div>
    </li>

    {{-- ادارة التدقيقات --}}
    <li class="nav-item {{ request()->routeIs('logs.audits') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('logs.audits') }}">
            <i class="fas fa-fw fa-file-alt"></i>
            <span>ادارة التدقيقات</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

    <script>
        function checkPassword(event) {
            event.preventDefault();
            var password = prompt("ادخل كلمة المرور");
            if (password === '1234') {
                window.location.href = event.currentTarget.href;
            } else {
                alert("خطأ في كلمة المرور");
            }
            return false;
        }
    </script>
</ul>
