<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('home') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">لوحة التحكم</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->


    <!-- Heading -->
    <div class="sidebar-heading">
        الادارات
    </div>

    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-robot"></i>
            <span>ادوات تحليل الذكاء الاصطناعي</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('roles.index') }}">
            <i class="fas fa-fw fa-lock"></i>
            <span>ادارة الصلاحيات</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('users.index') }}">
            <i class="fas fa-fw fa-users"></i>
            <span>ادارة الموظفين</span>
        </a>
    </li>


    {{-- ادارة السيارات --}}
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCars"
            aria-expanded="true" aria-controls="collapseCars">
            <i class="fas fa-fw fa-car"></i>
            <span>ادارة السيارات</span>
        </a>
        <div id="collapseCars" class="collapse" aria-labelledby="headingCars" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('cars.index') }}">عرض السيارات</a>
                <a class="collapse-item" href="{{ route('car_change_oils.index') }}">ادارة الزيوت وصيانات</a>
            </div>
        </div>
    </li>


    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseOperations"
            aria-expanded="true" aria-controls="collapseOperations">
            <i class="fas fa-fw fa-car"></i>
            <span>ادارة التشغيل</span>
        </a>
        <div id="collapseOperations" class="collapse" aria-labelledby="headingCars" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('containers.flow.index') }}">ادارة الحاويات</a>
                <a class="collapse-item" href="{{ route('customs.index') }}">الحجوزات</a>
            </div>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#company" aria-expanded="true"
            aria-controls="company">
            <i class="fas fa-fw fa-folder"></i>
            <span>الادارة العامة للشركة</span>
        </a>
        <div id="company" class="collapse" aria-labelledby="headingOperations" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="#">ادارة شؤون الشركة والشركاء</a>
                <a class="collapse-item" href="#">حسابات الشركة</a>
            </div>
        </div>
    </li>


    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#customs" aria-expanded="true"
            aria-controls="customs">
            <i class="fas fa-fw fa-users"></i>
            <span>ادارة العملاء</span>
        </a>
        <div id="customs" class="collapse" aria-labelledby="headingOperations" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('clearance-offices.index') }}">اضافة بيان جمركي</a>
                <a class="collapse-item" href="#">التقارير المالية لعملاء</a>
            </div>
        </div>
    </li>


    <!-- Nav Item - Financial Management -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseFinancial"
            aria-expanded="true" aria-controls="collapseFinancial">
            <i class="fas fa-fw fa-dollar-sign"></i>
            <span>الادارة المالية</span>
        </a>
        <div id="collapseFinancial" class="collapse" aria-labelledby="headingFinancial"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">

                <a class="collapse-item" href="{{ route('transactions.index') }}">اليومية</a>

                <a class="collapse-item" href="#" data-toggle="collapse" data-target="#collapseSubFinancial">
                    الماليات <i class="fas fa-fw fa-chevron-down"></i>
                </a>
                <div id="collapseSubFinancial" class="collapse">
                    <div class="bg-white py-2 collapse-inner rounded">

                        <a class="collapse-item" href="#" data-toggle="collapse"
                            data-target="#collapseExpenses">
                            المصروفات <i class="fas fa-fw fa-chevron-down"></i>
                        </a>
                        <div id="collapseExpenses" class="collapse">
                            <div class="bg-white py-2 collapse-inner rounded" style="margin-right: 15px;">
                                {{-- استبدل # بالروابط الصحيحة --}}
                                <a class="collapse-item" href="#">مصروفات تشغيل</a>
                                <a class="collapse-item" href="#">مصروفات ادارية</a>
                                <a class="collapse-item" href="#">رواتب الموظفين</a>
                            </div>
                        </div>

                        <a class="collapse-item" href="#" data-toggle="collapse"
                            data-target="#collapseRevenues">
                            الايرادات <i class="fas fa-fw fa-chevron-down"></i>
                        </a>
                        <div id="collapseRevenues" class="collapse">
                            <div class="bg-white py-2 collapse-inner rounded" style="margin-right: 15px;">
                                {{-- استبدل # بالروابط الصحيحة --}}
                                <a class="collapse-item" href="{{ route('revenues.clearance.index') }}">مكتب التخليص جمركي</a>
                                <a class="collapse-item" href="#">حركة البيع والشراء</a>
                            </div>
                        </div>

                        <a class="collapse-item" href="#" data-toggle="collapse" data-target="#collapseRents">
                            الايجارات <i class="fas fa-fw fa-chevron-down"></i>
                        </a>
                        <div id="collapseRents" class="collapse">
                            <div class="bg-white py-2 collapse-inner rounded" style="margin-right: 15px;">
                                {{-- استبدل # بالروابط الصحيحة --}}
                                <a class="collapse-item" href="#">ايجار معدات</a>
                                <a class="collapse-item" href="#">ايجار مكاتب</a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </li>

    <!-- Nav Item - Employee Management -->
    {{-- <li class="nav-item">
        <a class="nav-link" href="{{ route('getEmployee') }}">
            <i class="fas fa-fw fa-users"></i>
            <span>ادارة الموظفين</span>
        </a>
    </li>

    <!-- Nav Item - Flatbeds Management -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('flatbeds.index') }}">
            <i class="fas fa-fw fa-truck"></i>
            <span>ادارة السطحات</span>
        </a>
    </li>

    <!-- Nav Item - Thanks God -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('thanks.god') }}" onclick="return checkPassword(event);">
            <i class="fas fa-fw fa-pray"></i>
            <span>الحمد لله</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('car_change_oils.index') }}">
            <i class="fas fa-fw fa-oil-can"></i>
            <span>ادارة غيار الزيت</span>
        </a>
    </li>

    @if (Auth::user()->role == 'superAdmin')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('passwords.index') }}">
                <i class="fas fa-fw fa-lock"></i>
                <span>ادارة كلمات المرور</span>
            </a>
        </li>
    @endif --}}

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

    <!-- Script for Password Check -->
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
