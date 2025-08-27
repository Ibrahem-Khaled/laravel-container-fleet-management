<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>شركة سمير نمير لنقل الحاويات</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', sans-serif;
            line-height: 1.6;
            overflow-x: hidden;
            background: #0a0a0a;
            color: #fff;
        }

        /* Navigation */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(10, 10, 10, 0.95);
            backdrop-filter: blur(20px);
            z-index: 1000;
            padding: 1rem 0;
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            background: rgba(0, 0, 0, 0.98);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .navbar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-brand {
            font-size: 2rem;
            font-weight: 900;
            background: linear-gradient(135deg, #00d4ff, #0066cc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .navbar-brand:hover {
            transform: scale(1.05);
            filter: brightness(1.2);
        }

        .navbar-nav {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-link {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            padding: 0.5rem 1rem;
        }

        .nav-link:hover {
            color: #00d4ff;
            transform: translateY(-2px);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(135deg, #00d4ff, #0066cc);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 100%;
        }

        /* Hero Section */
        .hero {
            height: 100vh;
            position: relative;
            background: radial-gradient(ellipse at center, #001122 0%, #000000 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="%23ffffff" stroke-width="0.1" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) translateX(0px);
            }

            25% {
                transform: translateY(-20px) translateX(10px);
            }

            50% {
                transform: translateY(0px) translateX(-10px);
            }

            75% {
                transform: translateY(20px) translateX(5px);
            }
        }

        .hero-content {
            text-align: center;
            z-index: 2;
            position: relative;
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 900;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #00d4ff, #ffffff, #0066cc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: glow 2s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from {
                filter: drop-shadow(0 0 20px rgba(0, 212, 255, 0.3));
            }

            to {
                filter: drop-shadow(0 0 40px rgba(0, 212, 255, 0.6));
            }
        }

        .hero-subtitle {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            color: #ccc;
            font-weight: 300;
        }

        .hero-description {
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto 3rem;
            color: #aaa;
            line-height: 1.8;
        }

        .cta-button {
            display: inline-block;
            padding: 1rem 2.5rem;
            background: linear-gradient(135deg, #00d4ff, #0066cc);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .cta-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s;
        }

        .cta-button:hover::before {
            left: 100%;
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(0, 212, 255, 0.3);
        }

        /* Floating Containers Animation */
        .floating-containers {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .container-icon {
            position: absolute;
            font-size: 3rem;
            color: rgba(0, 212, 255, 0.1);
            animation: floatContainer 15s linear infinite;
        }

        .container-icon:nth-child(1) {
            left: 10%;
            animation-delay: 0s;
        }

        .container-icon:nth-child(2) {
            left: 20%;
            animation-delay: -3s;
            font-size: 2rem;
        }

        .container-icon:nth-child(3) {
            left: 70%;
            animation-delay: -6s;
            font-size: 2.5rem;
        }

        .container-icon:nth-child(4) {
            left: 80%;
            animation-delay: -9s;
            font-size: 3.5rem;
        }

        .container-icon:nth-child(5) {
            left: 40%;
            animation-delay: -12s;
            font-size: 2rem;
        }

        @keyframes floatContainer {
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

        /* Features Section */
        .features {
            padding: 8rem 0;
            background: linear-gradient(135deg, #000000, #001122);
            position: relative;
        }

        .section-title {
            text-align: center;
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #00d4ff, #ffffff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .section-subtitle {
            text-align: center;
            color: #ccc;
            max-width: 600px;
            margin: 0 auto 4rem;
            font-size: 1.1rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 3rem;
            margin-top: 4rem;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2.5rem;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #00d4ff, #0066cc);
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .feature-card:hover::before {
            transform: translateX(0);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 60px rgba(0, 212, 255, 0.2);
            border-color: rgba(0, 212, 255, 0.3);
        }

        .feature-icon {
            font-size: 3rem;
            color: #00d4ff;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .feature-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #fff;
        }

        .feature-description {
            color: #ccc;
            line-height: 1.6;
        }

        /* Services Section */
        .services {
            padding: 8rem 0;
            background: #000000;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 3rem;
            margin-top: 4rem;
        }

        .service-card {
            background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(0, 102, 204, 0.1));
            border-radius: 20px;
            padding: 3rem;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .service-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .service-card:hover::after {
            opacity: 1;
        }

        .service-card:hover {
            transform: scale(1.02);
        }

        .service-number {
            font-size: 4rem;
            font-weight: 900;
            color: rgba(0, 212, 255, 0.3);
            position: absolute;
            top: 1rem;
            right: 2rem;
        }

        .service-title {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #00d4ff;
            position: relative;
            z-index: 2;
        }

        .service-description {
            color: #ccc;
            line-height: 1.6;
            position: relative;
            z-index: 2;
        }

        /* About Section */
        .about {
            padding: 8rem 0;
            background: linear-gradient(135deg, #001122, #000000);
        }

        .about-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            margin-top: 4rem;
        }

        .about-text {
            font-size: 1.1rem;
            color: #ccc;
            line-height: 1.8;
        }

        .about-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
        }

        .stat-card {
            text-align: center;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            border: 1px solid rgba(0, 212, 255, 0.2);
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 900;
            color: #00d4ff;
            display: block;
        }

        .stat-label {
            color: #ccc;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        /* Contact Section */
        .contact {
            padding: 8rem 0;
            background: #000000;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            margin-top: 4rem;
        }

        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .contact-item:hover {
            transform: translateX(10px);
            background: rgba(0, 212, 255, 0.1);
        }

        .contact-icon {
            font-size: 1.5rem;
            color: #00d4ff;
            width: 50px;
            text-align: center;
        }

        .contact-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-input {
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #00d4ff;
            box-shadow: 0 0 20px rgba(0, 212, 255, 0.2);
        }

        .form-input::placeholder {
            color: #999;
        }

        .submit-btn {
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #00d4ff, #0066cc);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 212, 255, 0.3);
        }

        /* Footer */
        .footer {
            background: #000000;
            padding: 2rem 0;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            color: #666;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .navbar-nav {
                display: none;
            }

            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1.2rem;
            }

            .features-grid,
            .services-grid {
                grid-template-columns: 1fr;
            }

            .about-content,
            .contact-grid {
                grid-template-columns: 1fr;
            }

            .container {
                padding: 0 1rem;
            }
        }

        /* Scroll animations */
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(50px);
            transition: all 0.6s ease;
        }

        .animate-on-scroll.animated {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="container">
            <div class="navbar-content">
                <a class="navbar-brand" href="#home">شركة سمير نمير</a>
                <ul class="navbar-nav">
                    <li><a href="#home" class="nav-link">الرئيسية</a></li>
                    <li><a href="{{ route('login') }}" class="nav-link">
                            @if (Auth::check())
                                {{ Auth::user()->name }}
                            @else
                                تسجيل الدخول
                            @endif
                        </a></li>
                    <li><a href="#features" class="nav-link">المميزات</a></li>
                    <li><a href="#services" class="nav-link">الخدمات</a></li>
                    <li><a href="#about" class="nav-link">من نحن</a></li>
                    <li><a href="#contact" class="nav-link">اتصل بنا</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="floating-containers">
            <i class="fas fa-shipping-fast container-icon"></i>
            <i class="fas fa-truck container-icon"></i>
            <i class="fas fa-ship container-icon"></i>
            <i class="fas fa-warehouse container-icon"></i>
            <i class="fas fa-boxes container-icon"></i>
        </div>
        <div class="hero-content">
            <h1 class="hero-title">شركة سمير نمير</h1>
            <p class="hero-subtitle">رائدة في نقل الحاويات والشحن البحري</p>
            <p class="hero-description">
                نوفر حلول شحن متكاملة وموثوقة بأحدث التقنيات وأعلى معايير الجودة والأمان.
                خبرة تزيد عن عقدين في خدمة عملائنا الكرام وتحقيق أهدافهم التجارية
            </p>
            <a href="#contact" class="cta-button">
                <i class="fas fa-rocket"></i> ابدأ رحلتك معنا
            </a>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <h2 class="section-title animate-on-scroll">مميزاتنا الفريدة</h2>
            <p class="section-subtitle animate-on-scroll">نتميز بخدمات عالية الجودة وحلول مبتكرة تلبي احتياجاتكم</p>

            <div class="features-grid">
                <div class="feature-card animate-on-scroll">
                    <i class="fas fa-shield-alt feature-icon"></i>
                    <h3 class="feature-title">أمان وموثوقية</h3>
                    <p class="feature-description">
                        نضمن وصول بضائعكم بأمان تام مع أنظمة تتبع متطورة وتأمين شامل على جميع الشحنات
                    </p>
                </div>

                <div class="feature-card animate-on-scroll">
                    <i class="fas fa-clock feature-icon"></i>
                    <h3 class="feature-title">سرعة في التنفيذ</h3>
                    <p class="feature-description">
                        التزام كامل بالمواعيد المحددة مع خدمة عملاء متاحة 24/7 لمتابعة شحناتكم
                    </p>
                </div>

                <div class="feature-card animate-on-scroll">
                    <i class="fas fa-globe feature-icon"></i>
                    <h3 class="feature-title">شبكة عالمية</h3>
                    <p class="feature-description">
                        شبكة واسعة من الشركاء حول العالم تضمن وصول بضائعكم لأي وجهة بكفاءة عالية
                    </p>
                </div>

                <div class="feature-card animate-on-scroll">
                    <i class="fas fa-dollar-sign feature-icon"></i>
                    <h3 class="feature-title">أسعار تنافسية</h3>
                    <p class="feature-description">
                        أفضل الأسعار في السوق مع خدمات مميزة وعروض خاصة للعملاء المميزين
                    </p>
                </div>

                <div class="feature-card animate-on-scroll">
                    <i class="fas fa-cogs feature-icon"></i>
                    <h3 class="feature-title">تقنيات متطورة</h3>
                    <p class="feature-description">
                        استخدام أحدث التقنيات في التتبع والإدارة لضمان الشفافية والكفاءة
                    </p>
                </div>

                <div class="feature-card animate-on-scroll">
                    <i class="fas fa-headset feature-icon"></i>
                    <h3 class="feature-title">دعم فني مميز</h3>
                    <p class="feature-description">
                        فريق دعم متخصص ومدرب لحل جميع استفساراتكم ومساعدتكم في كل خطوة
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services" id="services">
        <div class="container">
            <h2 class="section-title animate-on-scroll">خدماتنا المتميزة</h2>
            <p class="section-subtitle animate-on-scroll">مجموعة شاملة من خدمات الشحن والنقل البحري</p>

            <div class="services-grid">
                <div class="service-card animate-on-scroll">
                    <span class="service-number">01</span>
                    <h3 class="service-title">نقل الحاويات البحري</h3>
                    <p class="service-description">
                        خدمات نقل الحاويات عبر الخطوط البحرية العالمية بأمان وكفاءة عالية،
                        مع ضمان الحفاظ على البضائع وتسليمها في الوقت المحدد
                    </p>
                </div>

                <div class="service-card animate-on-scroll">
                    <span class="service-number">02</span>
                    <h3 class="service-title">الشحن الجوي السريع</h3>
                    <p class="service-description">
                        خدمات شحن جوي سريعة للبضائع العاجلة والحساسة، مع شبكة واسعة من
                        شركات الطيران العالمية المتخصصة في الشحن التجاري
                    </p>
                </div>

                <div class="service-card animate-on-scroll">
                    <span class="service-number">03</span>
                    <h3 class="service-title">التخليص الجمركي</h3>
                    <p class="service-description">
                        خدمات تخليص جمركي متكاملة مع فريق متخصص في الإجراءات الجمركية،
                        لضمان سرعة وسلاسة عبور البضائع عبر الحدود
                    </p>
                </div>

                <div class="service-card animate-on-scroll">
                    <span class="service-number">04</span>
                    <h3 class="service-title">التخزين والتوزيع</h3>
                    <p class="service-description">
                        مستودعات حديثة ومؤمنة لتخزين البضائع مع خدمات توزيع محلية شاملة،
                        وأنظمة إدارة مخزون متطورة لضمان الدقة والكفاءة
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about" id="about">
        <div class="container">
            <h2 class="section-title animate-on-scroll">من نحن</h2>
            <p class="section-subtitle animate-on-scroll">تاريخ حافل بالإنجازات والريادة في مجال الشحن والنقل</p>

            <div class="about-content">
                <div class="about-text animate-on-scroll">
                    <p>
                        شركة سمير نمير هي واحدة من أعرق وأهم شركات النقل البحري والشحن في المنطقة،
                        تأسست بروح الريادة والابتكار لتصبح الخيار الأول للعملاء الباحثين عن الجودة والموثوقية.
                    </p>
                    <p>
                        منذ تأسيسها، حرصت الشركة على بناء علاقات قوية مع عملائها وشركائها التجاريين،
                        وتقديم خدمات متميزة تتماشى مع أحدث المعايير العالمية في صناعة الشحن والنقل البحري.
                    </p>
                    <p>
                        نفخر بفريقنا المتخصص وخبرتنا الطويلة التي تمكننا من تقديم حلول مبتكرة
                        ومتكاملة تلبي احتياجات عملائنا وتحقق توقعاتهم وأهدافهم التجارية.
                    </p>
                </div>

                <div class="about-stats animate-on-scroll">
                    <div class="stat-card">
                        <span class="stat-number">20+</span>
                        <span class="stat-label">سنة خبرة</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number">500+</span>
                        <span class="stat-label">عميل راضٍ</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number">1000+</span>
                        <span class="stat-label">شحنة ناجحة</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number">50+</span>
                        <span class="stat-label">وجهة عالمية</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact" id="contact">
        <div class="container">
            <h2 class="section-title animate-on-scroll">تواصل معنا</h2>
            <p class="section-subtitle animate-on-scroll">نحن هنا لخدمتكم والإجابة على جميع استفساراتكم</p>

            <div class="contact-grid">
                <div class="contact-info">
                    <div class="contact-item animate-on-scroll">
                        <i class="fas fa-map-marker-alt contact-icon"></i>
                        <div>
                            <h4 style="color: #fff; margin-bottom: 0.5rem;">العنوان</h4>
                            <p style="color: #ccc; margin: 0;">شارع الكورنيش، الإسكندرية، مصر</p>
                        </div>
                    </div>

                    <div class="contact-item animate-on-scroll">
                        <i class="fas fa-phone contact-icon"></i>
                        <div>
                            <h4 style="color: #fff; margin-bottom: 0.5rem;">الهاتف</h4>
                            <p style="color: #ccc; margin: 0;">+20 123 456 7890</p>
                        </div>
                    </div>

                    <div class="contact-item animate-on-scroll">
                        <i class="fas fa-envelope contact-icon"></i>
                        <div>
                            <h4 style="color: #fff; margin-bottom: 0.5rem;">البريد الإلكتروني</h4>
                            <p style="color: #ccc; margin: 0;">info@samirnamir.com</p>
                        </div>
                    </div>

                    <div class="contact-item animate-on-scroll">
                        <i class="fas fa-clock contact-icon"></i>
                        <div>
                            <h4 style="color: #fff; margin-bottom: 0.5rem;">ساعات العمل</h4>
                            <p style="color: #ccc; margin: 0;">24/7 خدمة العملاء</p>
                        </div>
                    </div>
                </div>

                <form class="contact-form animate-on-scroll">
                    <div class="form-group">
                        <input type="text" class="form-input" placeholder="الاسم الكامل" required>
                    </div>

                    <div class="form-group">
                        <input type="email" class="form-input" placeholder="البريد الإلكتروني" required>
                    </div>

                    <div class="form-group">
                        <input type="tel" class="form-input" placeholder="رقم الهاتف" required>
                    </div>

                    <div class="form-group">
                        <select class="form-input" required>
                            <option value="">نوع الخدمة المطلوبة</option>
                            <option value="sea-freight">الشحن البحري</option>
                            <option value="air-freight">الشحن الجوي</option>
                            <option value="customs">التخليص الجمركي</option>
                            <option value="storage">التخزين والتوزيع</option>
                            <option value="consultation">استشارة</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <textarea class="form-input" rows="5" placeholder="تفاصيل الطلب أو الاستفسار" required></textarea>
                    </div>

                    <button type="submit" class="submit-btn">
                        <i class="fas fa-paper-plane"></i> إرسال الطلب
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 شركة سمير نمير لنقل الحاويات. جميع الحقوق محفوظة.</p>
        </div>
    </footer>

    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Animate elements on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            observer.observe(el);
        });

        // Counter animation for stats
        function animateCounters() {
            const counters = document.querySelectorAll('.stat-number');

            counters.forEach(counter => {
                const target = parseInt(counter.textContent.replace('+', ''));
                const increment = target / 100;
                let current = 0;

                const updateCounter = () => {
                    if (current < target) {
                        current += increment;
                        counter.textContent = Math.ceil(current) + '+';
                        requestAnimationFrame(updateCounter);
                    } else {
                        counter.textContent = target + '+';
                    }
                };

                // Start animation when element is visible
                const counterObserver = new IntersectionObserver(entries => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            updateCounter();
                            counterObserver.disconnect();
                        }
                    });
                });

                counterObserver.observe(counter);
            });
        }

        // Initialize counter animation
        animateCounters();

        // Form submission handling
        document.querySelector('.contact-form').addEventListener('submit', function(e) {
            e.preventDefault();

            // Animate submit button
            const submitBtn = this.querySelector('.submit-btn');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإرسال...';
            submitBtn.disabled = true;

            // Simulate form submission
            setTimeout(() => {
                submitBtn.innerHTML = '<i class="fas fa-check"></i> تم الإرسال بنجاح!';
                submitBtn.style.background = 'linear-gradient(135deg, #10b981, #059669)';

                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    submitBtn.style.background = 'linear-gradient(135deg, #00d4ff, #0066cc)';
                    this.reset();
                }, 2000);
            }, 1500);
        });

        // Add floating animation to containers
        function createFloatingElement() {
            const container = document.querySelector('.floating-containers');
            const icons = ['fas fa-shipping-fast', 'fas fa-truck', 'fas fa-ship', 'fas fa-warehouse', 'fas fa-boxes'];

            const element = document.createElement('i');
            element.className = icons[Math.floor(Math.random() * icons.length)] + ' container-icon';
            element.style.left = Math.random() * 100 + '%';
            element.style.fontSize = (Math.random() * 2 + 1.5) + 'rem';
            element.style.animationDuration = (Math.random() * 10 + 10) + 's';

            container.appendChild(element);

            setTimeout(() => {
                element.remove();
            }, 15000);
        }

        // Create floating elements periodically
        setInterval(createFloatingElement, 3000);

        // Parallax effect for hero section
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const parallax = document.querySelector('.hero::before');
            if (parallax) {
                const speed = scrolled * 0.5;
                parallax.style.transform = `translateY(${speed}px)`;
            }
        });

        // Add loading animation
        window.addEventListener('load', () => {
            document.body.style.opacity = '0';
            document.body.style.transition = 'opacity 0.5s ease';

            setTimeout(() => {
                document.body.style.opacity = '1';
            }, 100);
        });

        // Add cursor trail effect
        let mouseX = 0,
            mouseY = 0;
        let trailX = 0,
            trailY = 0;

        document.addEventListener('mousemove', (e) => {
            mouseX = e.clientX;
            mouseY = e.clientY;
        });

        function animateTrail() {
            trailX += (mouseX - trailX) * 0.1;
            trailY += (mouseY - trailY) * 0.1;

            // Create trail effect with CSS custom properties
            document.documentElement.style.setProperty('--mouse-x', trailX + 'px');
            document.documentElement.style.setProperty('--mouse-y', trailY + 'px');

            requestAnimationFrame(animateTrail);
        }

        animateTrail();
    </script>
</body>

</html>
