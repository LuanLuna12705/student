<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased" x-data="{ sidebarOpen: window.innerWidth >= 768, profileMenuOpen: false, mobileSearchOpen: false }">
    <div class="min-h-screen flex flex-col">

        <header class="bg-[#233871] text-white flex items-center justify-between px-4 py-2.5 shadow-md sticky top-0 z-50">
            <div class="flex items-center space-x-3">
                <button @click="sidebarOpen = !sidebarOpen"
                        class="flex items-center space-x-1.5 rounded-lg p-1.5 text-sm font-medium text-white transition hover:bg-blue-800 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <span class="hidden sm:inline">Menu</span>
                </button>

                <a href="{{ route('dashboard') }}" title="Về trang chủ Sinh viên" class="flex items-center transition hover:opacity-90">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Phenikaa University" class="h-12 w-20 object-contain">
                </a>
            </div>

            <!-- THANH TÌM KIẾM TOÀN DIỆN (DESKTOP) -->
            <div class="hidden md:flex flex-1 max-w-lg mx-6 relative"
                 x-data="{
                     query: '{{ request('q', '') }}',
                     isOpen: false,
                     isLoading: false,
                     results: null,
                     async search() {
                         const q = this.query.trim();
                         if (q.length < 2) {
                             this.results = null;
                             this.isOpen = false;
                             return;
                         }
                         this.isLoading = true;
                         this.isOpen = true;
                         try {
                             const res = await fetch(`{{ route('search.index') }}?q=${encodeURIComponent(q)}&format=json`, {
                                 headers: { 'Accept': 'application/json' }
                             });
                             if (res.ok) {
                                 this.results = await res.json();
                             }
                         } catch (e) {
                             console.error('Lỗi tìm kiếm:', e);
                         } finally {
                             this.isLoading = false;
                         }
                     },
                     clear() {
                         this.query = '';
                         this.results = null;
                         this.isOpen = false;
                     }
                 }"
                 @click.outside="isOpen = false"
                 @keydown.escape.window="isOpen = false">

                <form action="{{ route('search.index') }}" method="GET" class="w-full" style="margin: 0; position: relative;">
                    <div style="position: relative; width: 100%; display: flex; align-items: center;">
                        <input type="text"
                               name="q"
                               x-model="query"
                               @input.debounce.250ms="search()"
                               @focus="if (query.trim().length >= 2) isOpen = true"
                               placeholder="Tìm kiếm thông tin (sự kiện, tin tức, CLB...)"
                               autocomplete="off"
                               class="w-full rounded-full border border-white/20 bg-white/10 text-sm text-white placeholder:text-blue-100 focus:bg-white/20 focus:border-white/40 focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
                               style="width: 100%; height: 38px; line-height: 38px; padding-left: 16px; padding-right: 52px; box-sizing: border-box;">

                        <div style="position: absolute; right: 14px; top: 0; bottom: 0; height: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; pointer-events: auto;">
                            <button type="button"
                                    x-show="query.length > 0"
                                    @click="clear()"
                                    class="text-blue-200 hover:text-white transition focus:outline-none"
                                    style="background: none; border: none; padding: 0; margin: 0; cursor: pointer; display: flex; align-items: center; justify-content: center;"
                                    title="Xóa">
                                <svg style="width: 16px; height: 16px; display: block;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                            <div x-show="isLoading" style="display: none; align-items: center; justify-content: center;">
                                <svg class="animate-spin text-blue-200" style="width: 16px; height: 16px; display: block;" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </div>
                            <button type="submit"
                                    class="text-blue-100 hover:text-white transition focus:outline-none"
                                    style="background: none; border: none; padding: 0; margin: 0; cursor: pointer; display: flex; align-items: center; justify-content: center;"
                                    title="Tìm kiếm">
                                <svg style="width: 18px; height: 18px; display: block;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </button>
                        </div>
                    </div>
                </form>

                <!-- DROPDOWN KẾT QUẢ TÌM KIẾM TRỰC TIẾP (LIVE DROPDOWN) -->
                <div x-show="isOpen && query.trim().length >= 2"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1 scale-98"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-1 scale-98"
                     class="absolute left-0 right-0 top-full mt-2 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-2xl z-50 text-gray-800"
                     style="display: none;">

                    <!-- Đang tải -->
                    <div x-show="isLoading && !results" class="p-6 text-center text-sm text-gray-500">
                        <svg class="mx-auto h-5 w-5 animate-spin text-blue-600 mb-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Đang tìm kiếm thông tin...
                    </div>

                    <!-- Danh sách kết quả -->
                    <div x-show="results" class="max-h-[70vh] overflow-y-auto divide-y divide-gray-100">

                        <!-- Khi không có kết quả nào -->
                        <template x-if="results && results.total === 0">
                            <div class="p-6 text-center">
                                <span class="text-2xl">🔍</span>
                                <p class="mt-2 text-sm font-semibold text-gray-800">Không tìm thấy kết quả phù hợp</p>
                                <p class="mt-1 text-xs text-gray-500">Thử tìm kiếm với từ khóa khác như "Sự kiện", "Thông báo", "CLB"...</p>
                            </div>
                        </template>

                        <!-- Lối tắt điều hướng -->
                        <template x-if="results && results.navigation && results.navigation.length > 0">
                            <div class="p-3">
                                <p class="px-2 text-[11px] font-bold uppercase tracking-wider text-gray-400">⚡ Chức năng nhanh</p>
                                <div class="mt-1 space-y-1">
                                    <template x-for="item in results.navigation.slice(0, 3)" :key="item.url">
                                        <a :href="item.url" class="flex items-center justify-between rounded-lg px-2.5 py-1.5 text-xs transition hover:bg-blue-50">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span x-text="item.icon" class="text-sm"></span>
                                                <span class="font-semibold text-gray-800 truncate" x-text="item.title"></span>
                                                <span class="text-[10px] text-gray-400 truncate" x-text="item.description"></span>
                                            </div>
                                            <span class="text-blue-600 text-xs font-bold">›</span>
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- Sự kiện -->
                        <template x-if="results && results.events && results.events.length > 0">
                            <div class="p-3">
                                <div class="flex items-center justify-between px-2">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">📅 Sự kiện</p>
                                    <span class="rounded bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-700" x-text="results.events.length"></span>
                                </div>
                                <div class="mt-1 space-y-1">
                                    <template x-for="ev in results.events.slice(0, 3)" :key="ev.id">
                                        <a :href="ev.url" class="flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-xs transition hover:bg-blue-50">
                                            <img :src="ev.image_url" :alt="ev.title" class="h-8 w-12 rounded object-cover shrink-0">
                                            <div class="min-w-0 flex-1">
                                                <p class="font-semibold text-gray-900 truncate" x-text="ev.title"></p>
                                                <p class="text-[11px] text-gray-500 truncate">
                                                    <span x-text="ev.time"></span> · <span x-text="ev.location"></span>
                                                </p>
                                            </div>
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- Tin tức -->
                        <template x-if="results && results.announcements && results.announcements.length > 0">
                            <div class="p-3">
                                <div class="flex items-center justify-between px-2">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">📰 Tin tức & Thông báo</p>
                                    <span class="rounded bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-700" x-text="results.announcements.length"></span>
                                </div>
                                <div class="mt-1 space-y-1">
                                    <template x-for="an in results.announcements.slice(0, 3)" :key="an.id">
                                        <a :href="an.url" class="block rounded-lg px-2.5 py-2 text-xs transition hover:bg-blue-50">
                                            <div class="flex items-center justify-between gap-2">
                                                <p class="font-semibold text-gray-900 truncate" x-text="an.title"></p>
                                                <span class="text-[10px] text-gray-400 shrink-0" x-text="an.published_at"></span>
                                            </div>
                                            <p class="mt-0.5 text-[11px] text-gray-500 line-clamp-1" x-text="an.body"></p>
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- Câu lạc bộ -->
                        <template x-if="results && results.clubs && results.clubs.length > 0">
                            <div class="p-3">
                                <div class="flex items-center justify-between px-2">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">🏆 Câu lạc bộ</p>
                                    <span class="rounded bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-700" x-text="results.clubs.length"></span>
                                </div>
                                <div class="mt-1 space-y-1">
                                    <template x-for="cl in results.clubs.slice(0, 3)" :key="cl.id">
                                        <a :href="cl.url" class="flex items-center justify-between rounded-lg px-2.5 py-2 text-xs transition hover:bg-blue-50">
                                            <div class="min-w-0">
                                                <p class="font-semibold text-gray-900 truncate" x-text="cl.name"></p>
                                                <p class="text-[11px] text-gray-500 truncate" x-text="cl.description || 'Câu lạc bộ sinh viên'"></p>
                                            </div>
                                            <span class="text-[11px] font-semibold text-blue-700 shrink-0">Xem →</span>
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- Sinh viên (Dành cho Admin) -->
                        <template x-if="results && results.students && results.students.length > 0">
                            <div class="p-3">
                                <div class="flex items-center justify-between px-2">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">👥 Sinh viên & Tài khoản</p>
                                    <span class="rounded bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-700" x-text="results.students.length"></span>
                                </div>
                                <div class="mt-1 space-y-1">
                                    <template x-for="st in results.students.slice(0, 3)" :key="st.id">
                                        <a :href="st.url" class="flex items-center gap-2 rounded-lg px-2.5 py-2 text-xs transition hover:bg-blue-50">
                                            <img :src="st.avatar" :alt="st.name" class="h-6 w-6 rounded-full object-cover shrink-0">
                                            <div class="min-w-0 flex-1">
                                                <p class="font-semibold text-gray-900 truncate" x-text="st.name"></p>
                                                <p class="text-[10px] text-gray-500 truncate" x-text="st.email + (st.student_code ? ' · MSSV: ' + st.student_code : '')"></p>
                                            </div>
                                            <span class="rounded bg-blue-100 px-1.5 py-0.5 text-[9px] font-semibold text-blue-800 shrink-0" x-text="st.role"></span>
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </template>

                    </div>

                    <!-- Footer xem toàn bộ kết quả -->
                    <template x-if="results && results.total > 0">
                        <a :href="`{{ route('search.index') }}?q=${encodeURIComponent(query)}`"
                           class="flex items-center justify-between border-t border-gray-100 bg-gray-50 px-4 py-2.5 text-xs font-semibold text-blue-700 hover:bg-blue-100/70 transition">
                            <span>Xem tất cả kết quả cho “<span x-text="query"></span>”</span>
                            <span>→</span>
                        </a>
                    </template>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <!-- Nút tìm kiếm mobile -->
                <button type="button"
                        @click="mobileSearchOpen = !mobileSearchOpen"
                        class="md:hidden relative rounded-full p-1.5 text-white transition hover:bg-blue-800"
                        title="Tìm kiếm">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>

                <button type="button" class="relative rounded-full p-1.5 text-white transition hover:bg-blue-800">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v1.341C7.67 7.165 6 9.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                </button>

                <div class="relative" x-data="{ open: false }">
                    <button type="button" @click="open = !open" class="flex items-center gap-2 rounded-full px-2 py-1.5 transition hover:bg-white/10">
                        <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="h-8 w-8 rounded-full object-cover ring-2 ring-white/20">
                        <span class="hidden text-sm font-semibold sm:inline">{{ Auth::user()->name }}</span>
                        <svg class="h-4 w-4 text-blue-100" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
                    </button>

                    <div x-show="open" x-transition @click.outside="open = false" class="absolute right-0 mt-2 w-52 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 transition hover:bg-gray-100">
                            <span>👤</span>
                            <span>Hồ sơ cá nhân</span>
                        </a>
                        <div class="border-t border-gray-100 px-2 py-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-2 py-2 text-left text-sm text-red-600 transition hover:bg-red-50">
                                    <span>🚪</span>
                                    <span>Đăng xuất</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- THANH TÌM KIẾM TRÊN MOBILE -->
        <div x-show="mobileSearchOpen"
             x-transition
             style="display: none;"
             class="md:hidden border-b border-blue-900 bg-[#1d305f] px-4 py-3 sticky top-[53px] z-40 shadow-md">
            <form action="{{ route('search.index') }}" method="GET" class="w-full" style="margin: 0; position: relative;">
                <div style="position: relative; width: 100%; display: flex; align-items: center;">
                    <input type="text"
                           name="q"
                           value="{{ request('q', '') }}"
                           placeholder="Tìm kiếm thông tin (sự kiện, tin tức, CLB...)"
                           class="w-full rounded-full border border-white/20 bg-white/10 text-sm text-white placeholder:text-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-400"
                           style="width: 100%; height: 38px; line-height: 38px; padding-left: 16px; padding-right: 42px; box-sizing: border-box;">
                    <div style="position: absolute; right: 14px; top: 0; bottom: 0; display: flex; align-items: center; justify-content: center;">
                        <button type="submit"
                                class="text-blue-100 hover:text-white transition focus:outline-none"
                                style="background: none; border: none; padding: 0; margin: 0; cursor: pointer; display: flex; align-items: center; justify-content: center;"
                                title="Tìm kiếm">
                            <svg style="width: 18px; height: 18px; display: block;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="relative flex flex-1">

            <aside x-show="sidebarOpen"
                   x-transition:enter="transition ease-out duration-200"
                   x-transition:enter-start="-translate-x-full opacity-0"
                   x-transition:enter-end="translate-x-0 opacity-100"
                   x-transition:leave="transition ease-in duration-150"
                   x-transition:leave-start="translate-x-0 opacity-100"
                   x-transition:leave-end="-translate-x-full opacity-0"
                   class="absolute inset-y-0 left-0 z-40 w-64 flex-shrink-0 min-h-[calc(100vh-53px)] bg-[#1b2a4a] text-gray-300 shadow-lg md:relative md:inset-auto md:z-auto">
                <nav class="space-y-1 p-3 text-sm font-medium">

                    <a href="{{ route('dashboard') }}" class="flex items-center justify-between rounded-lg bg-blue-800 px-3 py-2.5 font-semibold text-white">
                        <div class="flex items-center space-x-3">
                            <span>🏠</span>
                            <span>Trang chủ</span>
                        </div>
                    </a>

                    <a href="{{ route('announcements.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg hover:bg-blue-900/50 hover:text-white transition">
                        <div class="flex items-center space-x-3">
                            <span>📰</span>
                            <span>Tin tức & Thông báo</span>
                        </div>
                    </a>

                    <div x-data="{ activitiesMenuOpen: {{ request()->routeIs('activities.*') || request()->routeIs('events.joined') ? 'true' : 'false' }} }">
                        <button type="button"
                                @click="activitiesMenuOpen = !activitiesMenuOpen"
                                :aria-expanded="activitiesMenuOpen.toString()"
                                aria-controls="activities-submenu"
                                class="flex w-full items-center justify-between rounded-lg px-3 py-2.5 text-left transition {{ request()->routeIs('activities.*') || request()->routeIs('events.joined') ? 'bg-blue-900/50 text-white' : 'hover:bg-blue-900/50 hover:text-white' }}">
                            <span class="flex items-center space-x-3">
                                <span>🎓</span>
                                <span>Góc hoạt động</span>
                            </span>
                            <span x-text="activitiesMenuOpen ? '⌄' : '›'" aria-hidden="true"></span>
                        </button>

                        <div id="activities-submenu"
                             x-show="activitiesMenuOpen"
                             x-transition
                             class="ml-5 mt-1 space-y-1 border-l border-blue-700 pl-3">
                            <a href="{{ route('events.joined') }}"
                               class="block rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('events.joined') ? 'bg-blue-800 text-white' : 'hover:bg-blue-900/50 hover:text-white' }}">
                                Sự kiện đang đăng ký
                            </a>
                            <a href="{{ route('activities.scores') }}"
                               class="block rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('activities.scores') ? 'bg-blue-800 text-white' : 'hover:bg-blue-900/50 hover:text-white' }}">
                                Tra cứu hoạt động
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('events.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg hover:bg-blue-900/50 hover:text-white transition">
                        <div class="flex items-center space-x-3">
                            <span>📅</span>
                            <span>Đăng ký Sự kiện</span>
                        </div>
                        <span class="text-xs">›</span>
                    </a>

                    <a href="{{ route('events.calendar') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg hover:bg-blue-900/50 hover:text-white transition">
                        <div class="flex items-center space-x-3">
                            <span>🗓️</span>
                            <span>Lịch sự kiện</span>
                        </div>
                        <span class="text-xs">›</span>
                    </a>

                    <div x-data="{ clubsMenuOpen: {{ request()->routeIs('clubs.*') ? 'true' : 'false' }} }">
                        <button type="button"
                                @click="clubsMenuOpen = !clubsMenuOpen"
                                :aria-expanded="clubsMenuOpen.toString()"
                                aria-controls="club-submenu"
                                class="flex w-full items-center justify-between rounded-lg px-3 py-2.5 text-left transition {{ request()->routeIs('clubs.*') ? 'bg-blue-900/50 text-white' : 'hover:bg-blue-900/50 hover:text-white' }}">
                            <span class="flex items-center space-x-3">
                                <span>🏆</span>
                                <span>Câu lạc bộ Sinh viên</span>
                            </span>
                            <span x-text="clubsMenuOpen ? '⌄' : '›'" aria-hidden="true"></span>
                        </button>

                        <div id="club-submenu"
                             x-show="clubsMenuOpen"
                             x-transition
                             class="ml-5 mt-1 space-y-1 border-l border-blue-700 pl-3">
                            <a href="{{ route('clubs.index') }}"
                               class="block rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('clubs.index') ? 'bg-blue-800 text-white' : 'hover:bg-blue-900/50 hover:text-white' }}">
                                Câu lạc bộ của tôi
                            </a>
                            <a href="{{ route('clubs.register') }}"
                               class="block rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('clubs.register') ? 'bg-blue-800 text-white' : 'hover:bg-blue-900/50 hover:text-white' }}">
                                Đăng ký câu lạc bộ
                            </a>
                        </div>
                    </div>

                    @can('manage-students')
                        <div x-data="{ studentManagementOpen: {{ request()->routeIs('admin.students.*', 'admin.administrators.*', 'admin.clubs.*') ? 'true' : 'false' }} }">
                            <button type="button"
                                    @click="studentManagementOpen = !studentManagementOpen"
                                    :aria-expanded="studentManagementOpen.toString()"
                                    aria-controls="student-management-submenu"
                                    class="flex w-full items-center justify-between rounded-lg px-3 py-2.5 text-left text-sm transition {{ request()->routeIs('admin.students.*', 'admin.administrators.*', 'admin.clubs.*') ? 'bg-blue-900/50 text-white' : 'hover:bg-blue-900/50 hover:text-white' }}">
                                <span class="flex items-center gap-3">
                                    <span>👥</span>
                                    <span>Quản lý sinh viên</span>
                                </span>
                                <span x-text="studentManagementOpen ? '⌄' : '›'" aria-hidden="true"></span>
                            </button>

                            <div id="student-management-submenu"
                                 x-show="studentManagementOpen"
                                 x-transition
                                 class="ml-5 mt-1 space-y-1 border-l border-blue-700 pl-3">
                                <a href="{{ route('admin.students.index') }}"
                                   class="block rounded-lg px-3 py-1.5 text-xs transition {{ request()->routeIs('admin.students.*') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-900/50 hover:text-white' }}">
                                    Danh sách sinh viên
                                </a>
                                <a href="{{ route('admin.administrators.index') }}"
                                   class="block rounded-lg px-3 py-1.5 text-xs transition {{ request()->routeIs('admin.administrators.*') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-900/50 hover:text-white' }}">
                                    Danh sách quản trị viên
                                </a>
                                <a href="{{ route('admin.clubs.index') }}"
                                   class="block rounded-lg px-3 py-1.5 text-xs transition {{ request()->routeIs('admin.clubs.*') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-900/50 hover:text-white' }}">
                                    Danh sách câu lạc bộ
                                </a>
                            </div>
                        </div>
                    @endcan

                </nav>
            </aside>

            <!-- NỘI DUNG CHÍNH (CONTENT) -->
            <main class="min-w-0 flex-1 overflow-y-auto bg-slate-100 p-4 sm:p-6">
                {{ $slot }}
            </main>

        </div>
    </div>
</body>
</html>