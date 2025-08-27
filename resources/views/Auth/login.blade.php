<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - Samer Nomer</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Cairo", sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Animated Background */
        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 20s infinite linear;
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            left: 20%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            left: 70%;
            animation-delay: 4s;
        }

        .shape:nth-child(4) {
            width: 100px;
            height: 100px;
            left: 80%;
            animation-delay: 6s;
        }

        @keyframes float {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }

            10% {
                opacity: 1;
            }

            90% {
                opacity: 1;
            }

            100% {
                transform: translateY(-100px) rotate(360deg);
                opacity: 0;
            }
        }

        /* Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 1rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .navbar-brand {
            font-size: 1.8rem;
            font-weight: 800;
            color: white !important;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease;
        }

        .navbar-brand:hover {
            transform: scale(1.05);
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #ff6b6b, #4ecdc4);
            transition: width 0.3s ease;
        }

        .nav-link:hover::before {
            width: 100%;
        }

        /* Main Container */
        .main-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 100px 20px 20px;
        }

        .login-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            width: 100%;
            max-width: 1000px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 1s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Left Side - Form */
        .form-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 60px 50px;
            position: relative;
            overflow: hidden;
        }

        .form-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(102, 126, 234, 0.1), transparent);
            animation: rotate 10s linear infinite;
        }

        @keyframes rotate {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .form-content {
            position: relative;
            z-index: 2;
        }

        .welcome-text {
            text-align: center;
            margin-bottom: 40px;
        }

        .welcome-title {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            animation: glow 2s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from {
                filter: drop-shadow(0 0 5px rgba(102, 126, 234, 0.3));
            }

            to {
                filter: drop-shadow(0 0 20px rgba(102, 126, 234, 0.6));
            }
        }

        .welcome-subtitle {
            color: #666;
            font-size: 1rem;
            font-weight: 400;
        }

        /* Form Styling */
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 0.95rem;
        }

        .input-wrapper {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 15px 20px 15px 50px;
            border: 2px solid rgba(102, 126, 234, 0.2);
            border-radius: 12px;
            font-size: 1rem;
            font-family: inherit;
            background: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
            outline: none;
        }

        .form-input:focus {
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 20px rgba(102, 126, 234, 0.2);
            transform: translateY(-2px);
        }

        .input-icon {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #667eea;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .form-input:focus+.input-icon {
            color: #4f46e5;
            transform: translateY(-50%) scale(1.1);
        }

        /* Checkbox */
        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
        }

        .custom-checkbox {
            width: 20px;
            height: 20px;
            border: 2px solid #667eea;
            border-radius: 4px;
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .custom-checkbox.checked {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-color: #667eea;
        }

        .custom-checkbox.checked::after {
            content: '✓';
            position: absolute;
            color: white;
            font-size: 12px;
            font-weight: bold;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .checkbox-label {
            color: #666;
            font-weight: 500;
            cursor: pointer;
        }

        /* Button */
        .login-button {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 1.1rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .login-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .login-button:hover::before {
            left: 100%;
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .login-button:active {
            transform: translateY(0);
        }

        /* Right Side - Visual */
        .visual-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .visual-content {
            text-align: center;
            color: white;
            z-index: 2;
            position: relative;
        }

        .visual-icon {
            font-size: 5rem;
            margin-bottom: 30px;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        .visual-title {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 15px;
        }

        .visual-text {
            font-size: 1.1rem;
            opacity: 0.9;
            line-height: 1.6;
        }

        /* Decorative Elements */
        .visual-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 15s linear infinite reverse;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .login-wrapper {
                grid-template-columns: 1fr;
                max-width: 400px;
            }

            .visual-section {
                display: none;
            }

            .form-section {
                padding: 40px 30px;
            }

            .welcome-title {
                font-size: 2rem;
            }

            .navbar-brand {
                font-size: 1.5rem;
            }
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <!-- Animated Background -->
    <div class="animated-bg">
        <div class="floating-shapes">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
        </div>
    </div>

    <!-- Navbar -->
    {{-- <nav class="navbar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center w-100">
                <a class="navbar-brand" href="#">Samer Nomer</a>
                <a class="nav-link" href="#">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    تسجيل الدخول
                </a>
            </div>
        </div>
    </nav> --}}

    <!-- Main Content -->
    <div class="main-container">
        <div class="login-wrapper">
            <!-- Form Section -->
            <div class="form-section">
                <div class="form-content">
                    <div class="welcome-text">
                        <h1 class="welcome-title">مرحباً بعودتك</h1>
                        <p class="welcome-subtitle">أدخل بياناتك للوصول إلى حسابك</p>
                        @include('components.alerts')
                    </div>
                    <form id="loginForm" action="{{ route('login.custom') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="phone">رقم الهاتف</label>
                            <div class="input-wrapper">
                                <input type="tel" class="form-input" id="phone" name="phone"
                                    placeholder="أدخل رقم هاتفك" required>
                                <i class="fas fa-phone input-icon"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="password">كلمة المرور</label>
                            <div class="input-wrapper">
                                <input type="password" class="form-input" id="password" name="password"
                                    placeholder="أدخل كلمة المرور" required>
                                <i class="fas fa-lock input-icon"></i>
                            </div>
                        </div>

                        <div class="checkbox-wrapper">
                            <div class="custom-checkbox" id="rememberCheckbox"></div>
                            <label class="checkbox-label" for="rememberCheckbox">تذكرني</label>
                        </div>

                        <button type="submit" class="login-button" id="loginBtn">
                            <span class="btn-text">تسجيل الدخول</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Visual Section -->
            <div class="visual-section">
                <div class="visual-content">
                    <div class="visual-icon">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h2 class="visual-title">ابدأ رحلتك معنا</h2>
                    <p class="visual-text">
                        انضم إلى آلاف المستخدمين الذين يثقون بخدماتنا المتطورة
                        واستمتع بتجربة فريدة ومميزة
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Checkbox functionality
        const checkbox = document.getElementById('rememberCheckbox');
        let isChecked = true;
        checkbox.classList.add('checked');

        checkbox.addEventListener('click', function() {
            isChecked = !isChecked;
            if (isChecked) {
                this.classList.add('checked');
            } else {
                this.classList.remove('checked');
            }
        });

        // Input focus effects
        const inputs = document.querySelectorAll('.form-input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });

            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.background = 'rgba(255, 255, 255, 0.95)';
                navbar.style.backdropFilter = 'blur(20px)';
            } else {
                navbar.style.background = 'rgba(255, 255, 255, 0.1)';
                navbar.style.backdropFilter = 'blur(10px)';
            }
        });

        // Add more floating shapes dynamically
        function createFloatingShape() {
            const shape = document.createElement('div');
            shape.className = 'shape';
            shape.style.width = Math.random() * 100 + 50 + 'px';
            shape.style.height = shape.style.width;
            shape.style.left = Math.random() * 100 + '%';
            shape.style.animationDelay = Math.random() * 10 + 's';
            shape.style.animationDuration = (Math.random() * 10 + 15) + 's';

            document.querySelector('.floating-shapes').appendChild(shape);

            setTimeout(() => {
                shape.remove();
            }, 25000);
        }

        // Generate new shapes periodically
        setInterval(createFloatingShape, 3000);
    </script>
</body>

</html>
