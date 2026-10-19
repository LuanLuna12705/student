<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased" x-data="{ sidebarOpen: window.innerWidth >= 768, profileMenuOpen: false }">
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

            <div class="hidden md:flex flex-1 max-w-md mx-6">
                <div class="relative w-full">
                    <input type="text" placeholder="Tìm kiếm thông tin..." class="w-full rounded-full border border-white/10 bg-white/10 py-1.5 pl-4 pr-10 text-sm text-white placeholder:text-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <button type="button" class="absolute right-3 top-2 text-blue-100 hover:text-white">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center space-x-3">
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