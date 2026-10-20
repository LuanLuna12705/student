<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Web Sự Kiện - Phenikaa University') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900">
    <div class="min-h-screen flex flex-col justify-between bg-cover bg-center bg-no-repeat relative" style="background-image: url('{{ asset('images/background1.jpg') }}');">
        <!-- Lớp phủ mờ bảo đảm độ tương phản như login/register -->
        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-[2px]"></div>

        <!-- KHỐI NỘI DUNG CHÍNH (HERO GLASSMORPHISM CARD) -->
        <main class="relative z-10 flex-1 flex items-center justify-center px-4 py-8">
            <div class="w-full max-w-4xl bg-white/90 backdrop-blur-md shadow-2xl border border-white/40 rounded-3xl p-6 sm:p-10 text-center">
                <!-- Logo trung tâm -->
                <div class="inline-flex items-center justify-center mb-5">
                    <img src="{{ asset('images/logo.png') }}"
                         alt="Logo Phenikaa University"
                         class="h-20 sm:h-24 w-auto object-contain rounded-2xl bg-white/95 p-3 shadow-lg border border-gray-100">
                </div>

                <!-- Tag thương hiệu -->
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-100 text-[#233871]">
                        🎓 Đại học Phenikaa · Cổng Thông Tin Hoạt Động
                    </span>
                </div>

                <!-- Tiêu đề chính -->
                <h1 class="mt-3 text-2xl sm:text-4xl font-extrabold text-gray-900 tracking-tight leading-snug">
                    Hệ thống quản lý câu lạc bộ và hoạt động sinh viên
                </h1>

                <!-- Phụ đề mô tả -->
                <p class="mt-3 text-sm sm:text-base text-gray-600 max-w-2xl mx-auto leading-relaxed">
                    Nền tảng trực tuyến kết nối sinh viên với các sự kiện ngoại khóa, phong trào học thuật, đăng ký câu lạc bộ và theo dõi kết quả rèn luyện trong suốt khóa học.
                </p>

                <!-- Nút thao tác nhanh (CTA) -->
                <div class="mt-6 flex flex-wrap justify-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-[#233871] text-white font-semibold text-sm shadow-lg hover:bg-blue-900 transition-all transform hover:-translate-y-0.5">
                            <span>Vào trang quản lý (Dashboard)</span>
                            <span>&rarr;</span>
                        </a>
                        <a href="{{ route('events.index') }}"
                           class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white text-gray-800 border border-gray-200 font-semibold text-sm shadow-sm hover:bg-gray-50 transition">
                            <span>📅 Đăng ký sự kiện</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center gap-2 px-7 py-3 rounded-xl bg-[#233871] text-white font-semibold text-sm sm:text-base shadow-lg hover:bg-blue-900 transition-all transform hover:-translate-y-0.5">
                            <span>🔑 Đăng nhập ngay</span>
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                               class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white text-gray-800 border border-gray-300 font-semibold text-sm sm:text-base shadow-sm hover:bg-gray-50 transition-all transform hover:-translate-y-0.5">
                                <span>✍️ Đăng ký tài khoản</span>
                            </a>
                        @endif
                    @endauth
                </div>

                <!-- Lưới tính năng nổi bật phong cách thẻ kính mờ -->
                <div class="mt-8 pt-8 border-t border-gray-200/80 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-left">
                    <div class="p-4 rounded-2xl bg-white/70 border border-gray-100 hover:bg-white hover:shadow-md transition">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-xl mb-3 shadow-inner">📅</div>
                        <h3 class="font-bold text-sm text-gray-900">Sự kiện &amp; Hội thảo</h3>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">Cập nhật lịch tổ chức, đăng ký giữ chỗ và xem các sự kiện sắp diễn ra.</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-white/70 border border-gray-100 hover:bg-white hover:shadow-md transition">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl mb-3 shadow-inner">🏆</div>
                        <h3 class="font-bold text-sm text-gray-900">Câu lạc bộ sinh viên</h3>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">Gửi đơn gia nhập CLB học thuật, thể thao, văn hóa nghệ thuật và tình nguyện.</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-white/70 border border-gray-100 hover:bg-white hover:shadow-md transition">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl mb-3 shadow-inner">📊</div>
                        <h3 class="font-bold text-sm text-gray-900">Điểm ngoại khóa</h3>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">Tra cứu thông tin cá nhân và tổng kết các hoạt động đã tham gia theo học kỳ.</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-white/70 border border-gray-100 hover:bg-white hover:shadow-md transition">
                        <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-xl mb-3 shadow-inner">📰</div>
                        <h3 class="font-bold text-sm text-gray-900">Tin tức &amp; Thông báo</h3>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">Nắm bắt kịp thời các thông báo chính thức và phong trào thi đua của trường.</p>
                    </div>
                </div>
            </div>
        </main>

        <!-- FOOTER DƯỚI CÙNG -->
        <footer class="relative z-10 w-full px-4 py-4 text-center text-xs text-white/80 drop-shadow">
            <p>&copy; {{ date('Y') }} Trường Đại học Phenikaa · Hệ thống Quản lý Sự kiện &amp; Câu lạc bộ Sinh viên.</p>
        </footer>
    </div>
</body>
</html>
