<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&family=Tajawal:wght@200;300;400;500;700;800;900&display=swap"
        rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم')</title>
    @yield('styles')
    <style>
        body {
            font-family: "Tajawal", sans-serif !important;
        }

        /* إعدادات الطباعة */
        @media print {

            /* إخفاء العناصر غير المرغوب بها */
            .no-print,
            .sidebar,
            .navbar,
            button,
            footer {
                display: none !important;
            }

            /* إخفاء حقول الإدخال الأصلية */
            input,
            textarea,
            span,
            select {
                display: none !important;
            }

            /* عرض العناصر المحوّلة إلى سبان */
            .print-span {
                display: inline !important;
            }

            /* تنسيق الجدول مع المحاذاة الوسطية */
            table {
                width: 100% !important;
                border-collapse: collapse !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            table th,
            table td {
                border: 1px solid #dee2e6 !important;
                padding: .75rem !important;
                text-align: center !important;
                /* محاذاة أفقية */
                vertical-align: middle !important;
                /* محاذاة عمودية */
            }

            /* إذا كنت تستخدم .table-striped */
            .table-striped tbody tr:nth-of-type(odd) {
                background-color: #f9f9f9 !important;
            }

            /* ضبط حجم الصفحة وأبعادها */
            @page {
                margin: 1cm;
                size: landscape;
            }
        }

        @media screen {
            .no-print {
                display: inline-block;
            }
        }
    </style>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        @include('layouts.sidebar')

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                @include('layouts.header')
                @yield('actions') {{-- الأزرار الخاصة بكل صفحة --}}
                <div class="container-fluid" id="report-container">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <span class="h3 mb-0 text-gray-800">{{ Route::currentRouteName() }}</span>
                        <div>
                            <!-- زر الحفظ/طباعة -->
                            <button onclick="window.print()" class="btn btn-sm btn-primary shadow-sm no-print">
                                <i class="fas fa-print"></i> حفظ كـ PDF
                            </button>
                        </div>
                    </div>

                    @yield('content')
                </div>
                <!-- /.container-fluid -->

            </div>
            @include('layouts.footer')
        </div>
    </div>

    <!-- Scroll to Top -->
    <a class="scroll-to-top rounded no-print" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Core JS -->
    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('assets/js/sb-admin-2.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/chart.js/Chart.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts')
    <!-- تحويل حقول الإدخال والروابط إلى نص قبل الطباعة -->
    <script>
        window.addEventListener('beforeprint', function() {
            // 1. إزالة spans السابقة لتجنب التكرار
            document.querySelectorAll('.print-span').forEach(span => span.remove());

            // 2. استبدال حقول الإدخال والـ textarea والـ select بنص فقط، مع تخطي الحقول المخفية
            document.querySelectorAll(
                    '#report-container input, #report-container textarea, #report-container select')
                .forEach(function(el) {
                    // تجاهل الحقول المخفية نوعاً أو عبر CSS
                    if (el.type === 'hidden' || window.getComputedStyle(el).display === 'none') return;
                    let span = document.createElement('span');
                    span.className = 'print-span';
                    if (el.tagName === 'SELECT') {
                        span.textContent = el.options[el.selectedIndex]?.text || '';
                    } else {
                        span.textContent = el.value;
                    }
                    el.parentNode.insertBefore(span, el);
                    el.style.display = 'none';
                });

            // 3. استبدال الروابط داخل tbody فقط لتفادي تكرار الهيدر
            document.querySelectorAll('#report-container a').forEach(function(a) {
                if (a.closest('thead')) return;
                let span = document.createElement('span');
                span.className = 'print-span';
                span.textContent = a.textContent;
                a.parentNode.insertBefore(span, a);
                a.style.display = 'none';
            });
        });

        window.addEventListener('afterprint', function() {
            window.location.reload();
        });
    </script>
</body>

</html>
