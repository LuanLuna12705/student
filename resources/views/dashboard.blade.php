<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Trang chủ Sinh viên') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        <!-- Banner Chào mừng -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-6 text-white shadow-lg flex justify-between items-center">
            <div>
                <p class="text-blue-100 text-sm font-medium">
                    {{ \Carbon\Carbon::now()->locale('vi')->isoFormat('dddd, [ngày] DD/MM/YYYY') }}
                </p>
                <h1 class="text-3xl font-bold mt-1">Chào buổi học, {{ Auth::user()->name }}! 👋</h1>
                <p class="text-blue-200 mt-1 text-sm">Chào mừng bạn đến với Hệ thống Đăng ký Sự kiện & Câu lạc bộ Sinh viên.</p>
            </div>
            <div class="hidden md:block text-6xl opacity-80">
                🎓
            </div>
        </div>

        <!-- Quick Access Cards (Lối tắt nhanh) -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <a href="{{ route('events.index') }}" class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition flex items-center space-x-4">
                <div class="p-3 bg-blue-100 text-blue-600 rounded-lg text-2xl">📅</div>
                <div>
                    <div class="text-xs text-gray-500 uppercase font-bold">Sự kiện</div>
                    <div class="text-sm font-semibold text-gray-800">Đăng ký sự kiện</div>
                </div>
            </a>

            <a href="{{ route('clubs.index') }}" class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition flex items-center space-x-4">
                <div class="p-3 bg-amber-100 text-amber-600 rounded-lg text-2xl">🏆</div>
                <div>
                    <div class="text-xs text-gray-500 uppercase font-bold">Câu lạc bộ</div>
                    <div class="text-sm font-semibold text-gray-800">Câu lạc bộ của tôi</div>
                </div>
            </a>

            <a href="{{ route('events.joined') }}" class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition flex items-center space-x-4">
                <div class="p-3 bg-emerald-100 text-emerald-600 rounded-lg text-2xl">📋</div>
                <div>
                    <div class="text-xs text-gray-500 uppercase font-bold">Cá nhân</div>
                    <div class="text-sm font-semibold text-gray-800">Sự kiện đã tham gia</div>
                </div>
            </a>

            <a href="{{ route('announcements.index') }}" class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition flex items-center space-x-4">
                <div class="p-3 bg-rose-100 text-rose-600 rounded-lg text-2xl">🔔</div>
                <div>
                    <div class="text-xs text-gray-500 uppercase font-bold">Thông báo</div>
                    <div class="text-sm font-semibold text-gray-800">Tin tức mới nhất</div>
                </div>
            </a>
        </div>

        <!-- Section: SỰ KIỆN SẮP TỚI -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-700 uppercase text-sm tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 bg-blue-600 rounded-full"></span> SỰ KIỆN SẮP TỚI
                </h3>
                <a href="{{ route('events.index') }}" class="text-xs text-blue-600 hover:underline font-semibold">Xem tất cả &rarr;</a>
            </div>
            
            <div class="grid gap-4 p-5 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($upcomingEvents as $event)
                    <article class="rounded-lg border border-gray-200 p-4">
                        <h4 class="font-semibold text-gray-900">{{ $event->title }}</h4>
                        <p class="mt-2 text-sm text-gray-600">{{ $event->location }}</p>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ $event->event_date->locale('vi')->isoFormat('dddd, DD/MM/YYYY [lúc] HH:mm') }}
                        </p>
                        <p class="mt-2 text-xs text-gray-500">
                            {{ $event->registered_users_count }} / {{ $event->max_participants }} người đã đăng ký
                        </p>
                    </article>
                @empty
                    <p class="col-span-full py-2 text-center text-sm text-gray-500">
                        Hiện chưa có sự kiện mới nào được mở đăng ký.
                    </p>
                @endforelse
            </div>
        </div>

    </div>
</x-app-layout>