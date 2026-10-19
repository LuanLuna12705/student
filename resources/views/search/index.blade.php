<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Tìm kiếm thông tin
        </h2>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6 py-6 sm:px-6 lg:px-8"
         x-data="{ activeTab: 'all' }">

        <!-- Khung tìm kiếm chính -->
        <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm sm:p-8">
            <div class="max-w-3xl">
                <p class="text-xs font-bold uppercase tracking-wider text-blue-700">PHENIKAA UNIVERSITY · TÌM KIẾM TOÀN DIỆN</p>
                <h1 class="mt-2 text-2xl font-bold text-gray-900 sm:text-3xl">
                    @if ($query !== '')
                        Kết quả cho: <span class="text-blue-700 font-extrabold">“{{ $query }}”</span>
                    @else
                        Tìm kiếm trong hệ thống
                    @endif
                </h1>
                <p class="mt-2 text-sm text-gray-600">
                    Tra cứu nhanh sự kiện, tin tức & thông báo, câu lạc bộ, tài khoản sinh viên và các chức năng trong toàn trường.
                </p>

                <form method="GET" action="{{ route('search.index') }}" class="mt-6 flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <input type="text"
                               name="q"
                               value="{{ $query }}"
                               placeholder="Nhập tên sự kiện, tin tức, câu lạc bộ, sinh viên..."
                               class="w-full rounded-xl border border-gray-300 bg-gray-50/50 py-3 pl-11 pr-4 text-sm text-gray-900 placeholder:text-gray-400 focus:border-blue-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition shadow-inner">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-700 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800">
                        <span>Tìm kiếm</span>
                    </button>
                </form>
            </div>

            <!-- Tabs phân loại kết quả -->
            @if ($query !== '')
                <div class="mt-8 flex flex-wrap gap-2 border-t border-gray-100 pt-5 text-xs sm:text-sm">
                    <button type="button"
                            @click="activeTab = 'all'"
                            :class="activeTab === 'all' ? 'bg-blue-700 text-white font-semibold shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 font-medium'"
                            class="rounded-lg px-3.5 py-1.5 transition">
                        Tất cả ({{ $totalCount }})
                    </button>

                    @if ($events->isNotEmpty())
                        <button type="button"
                                @click="activeTab = 'events'"
                                :class="activeTab === 'events' ? 'bg-blue-700 text-white font-semibold shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 font-medium'"
                                class="rounded-lg px-3.5 py-1.5 transition">
                            📅 Sự kiện ({{ $events->count() }})
                        </button>
                    @endif

                    @if ($announcements->isNotEmpty())
                        <button type="button"
                                @click="activeTab = 'announcements'"
                                :class="activeTab === 'announcements' ? 'bg-blue-700 text-white font-semibold shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 font-medium'"
                                class="rounded-lg px-3.5 py-1.5 transition">
                            📰 Tin tức ({{ $announcements->count() }})
                        </button>
                    @endif

                    @if ($clubs->isNotEmpty())
                        <button type="button"
                                @click="activeTab = 'clubs'"
                                :class="activeTab === 'clubs' ? 'bg-blue-700 text-white font-semibold shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 font-medium'"
                                class="rounded-lg px-3.5 py-1.5 transition">
                            🏆 Câu lạc bộ ({{ $clubs->count() }})
                        </button>
                    @endif

                    @if ($students->isNotEmpty())
                        <button type="button"
                                @click="activeTab = 'students'"
                                :class="activeTab === 'students' ? 'bg-blue-700 text-white font-semibold shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 font-medium'"
                                class="rounded-lg px-3.5 py-1.5 transition">
                            👥 Sinh viên ({{ $students->count() }})
                        </button>
                    @endif

                    @if ($navigation->isNotEmpty())
                        <button type="button"
                                @click="activeTab = 'navigation'"
                                :class="activeTab === 'navigation' ? 'bg-blue-700 text-white font-semibold shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 font-medium'"
                                class="rounded-lg px-3.5 py-1.5 transition">
                            ⚡ Chức năng ({{ $navigation->count() }})
                        </button>
                    @endif
                </div>
            @endif
        </section>

        <!-- KHI CHƯA NHẬP TỪ KHÓA -->
        @if ($query === '')
            <div class="rounded-2xl border border-dashed border-gray-200 bg-white p-10 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-2xl">
                    🔍
                </div>
                <h3 class="mt-4 text-base font-bold text-gray-900">Hãy nhập từ khóa để tìm kiếm</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Bạn có thể tìm kiếm theo tên sự kiện, bài viết tin tức, tên câu lạc bộ hoặc chức năng trong hệ thống.
                </p>
                <div class="mt-6 flex flex-wrap justify-center gap-2">
                    <span class="text-xs text-gray-400 self-center">Gợi ý tìm kiếm:</span>
                    <a href="{{ route('search.index', ['q' => 'Sự kiện']) }}" class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-100 transition">Sự kiện</a>
                    <a href="{{ route('search.index', ['q' => 'Câu lạc bộ']) }}" class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-100 transition">Câu lạc bộ</a>
                    <a href="{{ route('search.index', ['q' => 'Thông báo']) }}" class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-100 transition">Thông báo</a>
                    <a href="{{ route('search.index', ['q' => 'Điểm ngoại khóa']) }}" class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-100 transition">Điểm ngoại khóa</a>
                </div>
            </div>
        @elseif ($totalCount === 0)
            <!-- KHI KHÔNG TÌM THẤY KẾT QUẢ -->
            <div class="rounded-2xl border border-gray-100 bg-white p-12 text-center shadow-sm">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50 text-3xl">
                    🕵️
                </div>
                <h3 class="mt-4 text-lg font-bold text-gray-900">Không tìm thấy kết quả nào cho “{{ $query }}”</h3>
                <p class="mx-auto mt-2 max-w-md text-sm text-gray-500">
                    Hãy thử kiểm tra lại chính tả, sử dụng từ khóa ngắn hơn hoặc tìm kiếm theo danh mục cụ thể.
                </p>
                <div class="mt-6 flex justify-center gap-3">
                    <a href="{{ route('events.index') }}" class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
                        Tất cả sự kiện
                    </a>
                    <a href="{{ route('announcements.index') }}" class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
                        Tin tức & Thông báo
                    </a>
                    <a href="{{ route('clubs.index') }}" class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
                        Câu lạc bộ
                    </a>
                </div>
            </div>
        @else
            <!-- KẾT QUẢ TÌM KIẾM -->
            <div class="space-y-6">

                <!-- 1. CHỨC NĂNG HỆ THỐNG / LỐI TẮT -->
                @if ($navigation->isNotEmpty())
                    <section x-show="activeTab === 'all' || activeTab === 'navigation'" class="space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">⚡</span>
                            <h2 class="text-base font-bold text-gray-900">Lối tắt & Chức năng hệ thống ({{ $navigation->count() }})</h2>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($navigation as $item)
                                <a href="{{ $item['url'] }}"
                                   class="group flex items-start gap-3.5 rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:border-blue-300 hover:shadow-md">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-xl group-hover:bg-blue-100 transition">
                                        {{ $item['icon'] }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between gap-1">
                                            <h3 class="text-sm font-bold text-gray-900 group-hover:text-blue-700 transition truncate">
                                                {{ $item['title'] }}
                                            </h3>
                                            <span class="text-[10px] font-semibold uppercase text-gray-400 shrink-0">{{ $item['category'] }}</span>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500 line-clamp-2 leading-relaxed">
                                            {{ $item['description'] }}
                                        </p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif

                <!-- 2. SỰ KIỆN -->
                @if ($events->isNotEmpty())
                    <section x-show="activeTab === 'all' || activeTab === 'events'" class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-lg">📅</span>
                                <h2 class="text-base font-bold text-gray-900">Sự kiện ({{ $events->count() }})</h2>
                            </div>
                            <a href="{{ route('events.index', ['search' => $query]) }}" class="text-xs font-semibold text-blue-700 hover:underline">
                                Xem tất cả tại Đăng ký sự kiện →
                            </a>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($events as $event)
                                <article class="flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition hover:shadow-md">
                                    <div class="relative h-36 w-full bg-gray-100 overflow-hidden">
                                        <img src="{{ $event['image_url'] }}" alt="{{ $event['title'] }}" class="h-full w-full object-cover" loading="lazy">
                                        <div class="absolute bottom-2 left-2 rounded-md bg-black/60 px-2 py-0.5 text-[11px] font-semibold text-white backdrop-blur-sm">
                                            {{ $event['time'] }}
                                        </div>
                                    </div>
                                    <div class="flex flex-1 flex-col p-4">
                                        <h3 class="text-sm font-bold text-gray-900 line-clamp-2 leading-snug">
                                            {{ $event['title'] }}
                                        </h3>
                                        <p class="mt-1.5 flex items-center gap-1.5 text-xs text-gray-500">
                                            <span>📍</span>
                                            <span class="truncate">{{ $event['location'] ?? 'Chưa cập nhật' }}</span>
                                        </p>
                                        <p class="mt-2 text-xs text-gray-600 line-clamp-2 leading-relaxed">
                                            {{ $event['description'] }}
                                        </p>
                                        <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-end">
                                            <a href="{{ $event['url'] }}" class="rounded-lg bg-blue-700 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-800 transition">
                                                Xem sự kiện
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif

                <!-- 3. TIN TỨC & THÔNG BÁO -->
                @if ($announcements->isNotEmpty())
                    <section x-show="activeTab === 'all' || activeTab === 'announcements'" class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-lg">📰</span>
                                <h2 class="text-base font-bold text-gray-900">Tin tức & Thông báo ({{ $announcements->count() }})</h2>
                            </div>
                            <a href="{{ route('announcements.index', ['q' => $query]) }}" class="text-xs font-semibold text-blue-700 hover:underline">
                                Xem tất cả tin tức →
                            </a>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach ($announcements as $item)
                                <article class="flex flex-col rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="inline-block rounded-md bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-700">
                                            {{ $item['category'] ?? 'Thông báo' }}
                                        </span>
                                        <span class="text-xs text-gray-400">{{ $item['published_at'] }}</span>
                                    </div>
                                    <h3 class="mt-2.5 text-base font-bold text-gray-900 leading-snug">
                                        {{ $item['title'] }}
                                    </h3>
                                    <p class="mt-2 text-xs sm:text-sm text-gray-600 line-clamp-3 leading-relaxed">
                                        {{ $item['body'] }}
                                    </p>
                                    <div class="mt-4 pt-3 border-t border-gray-100 flex justify-end">
                                        <a href="{{ $item['url'] }}" class="text-xs font-semibold text-blue-700 hover:text-blue-900 transition flex items-center gap-1">
                                            <span>Xem chi tiết</span>
                                            <span>→</span>
                                        </a>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif

                <!-- 4. CÂU LẠC BỘ -->
                @if ($clubs->isNotEmpty())
                    <section x-show="activeTab === 'all' || activeTab === 'clubs'" class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-lg">🏆</span>
                                <h2 class="text-base font-bold text-gray-900">Câu lạc bộ sinh viên ({{ $clubs->count() }})</h2>
                            </div>
                            <a href="{{ route('clubs.index') }}" class="text-xs font-semibold text-blue-700 hover:underline">
                                Xem trang CLB →
                            </a>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($clubs as $club)
                                <div class="flex flex-col justify-between rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-lg">🏆</span>
                                            <h3 class="text-sm font-bold text-gray-900 leading-snug truncate">
                                                {{ $club['name'] }}
                                            </h3>
                                        </div>
                                        @if ($club['leader_name'])
                                            <p class="mt-2 text-xs text-gray-500">
                                                Trưởng CLB: <span class="font-medium text-gray-800">{{ $club['leader_name'] }}</span>
                                            </p>
                                        @endif
                                        <p class="mt-2 text-xs text-gray-600 line-clamp-2 leading-relaxed">
                                            {{ $club['description'] ?? 'Chưa có thông tin mô tả chi tiết.' }}
                                        </p>
                                    </div>
                                    <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
                                        <a href="{{ route('clubs.register') }}" class="text-xs font-semibold text-blue-700 hover:underline">
                                            Đăng ký tham gia
                                        </a>
                                        <a href="{{ route('clubs.index') }}" class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-100 transition">
                                            Xem CLB
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                <!-- 5. SINH VIÊN (DÀNH CHO ADMIN) -->
                @if ($students->isNotEmpty())
                    <section x-show="activeTab === 'all' || activeTab === 'students'" class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-lg">👥</span>
                                <h2 class="text-base font-bold text-gray-900">Tài khoản & Sinh viên ({{ $students->count() }})</h2>
                            </div>
                            @can('manage-students')
                                <a href="{{ route('admin.students.index', ['search' => $query]) }}" class="text-xs font-semibold text-blue-700 hover:underline">
                                    Xem tại Danh sách sinh viên →
                                </a>
                            @endcan
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($students as $student)
                                <div class="flex items-center justify-between rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:shadow-md">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <img src="{{ $student['avatar'] }}" alt="{{ $student['name'] }}" class="h-10 w-10 rounded-full object-cover ring-2 ring-gray-100 shrink-0">
                                        <div class="min-w-0">
                                            <h3 class="text-sm font-bold text-gray-900 truncate">{{ $student['name'] }}</h3>
                                            <p class="text-xs text-gray-500 truncate">{{ $student['email'] }}</p>
                                            <div class="mt-1 flex items-center gap-2 text-[10px]">
                                                <span class="rounded bg-blue-50 px-1.5 py-0.5 font-semibold text-blue-700">{{ $student['role'] }}</span>
                                                @if ($student['student_code'])
                                                    <span class="text-gray-400">MSSV: {{ $student['student_code'] }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ $student['url'] }}" class="rounded-lg border border-gray-200 bg-gray-50 px-2.5 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-100 transition shrink-0 ml-2">
                                        Chi tiết
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

            </div>
        @endif

    </div>
</x-app-layout>
