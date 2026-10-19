<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tin tức
        </h2>
    </x-slot>

    @php
        $activeCategory = request()->query('category', 'all');
        $categories = [
            'all' => 'Toàn bộ',
            'club-events' => 'Sự kiện câu lạc bộ',
            'volunteer-events' => 'Sự kiện tình nguyện',
            'general' => 'Tin khác',
        ];
    @endphp

    <div class="mx-auto max-w-7xl space-y-5 py-2 sm:px-2">
        @if (session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <section class="rounded-xl bg-white p-4 shadow-sm sm:p-5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h1 class="flex items-center gap-2 text-lg font-semibold text-gray-800">
                    <span class="text-blue-600" aria-hidden="true">▤</span>
                    Tin tức
                </h1>
                <div class="flex items-center gap-2">
                    @can('manage-students')
                        <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'create-announcement-modal')" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800">
                            Thêm tin tức
                        </button>
                    @endcan
                    <button type="button" title="Trợ giúp" aria-label="Trợ giúp" class="flex h-8 w-8 items-center justify-center rounded-full border border-blue-500 text-sm font-semibold text-blue-600 hover:bg-blue-50">?</button>
                </div>
            </div>

            <form method="GET" action="{{ route('announcements.index') }}" class="mt-4 grid grid-cols-1 gap-3 border-b border-dashed border-gray-200 pb-4 sm:grid-cols-[minmax(220px,1fr)_140px_140px_auto] sm:items-center">
                <label class="sr-only" for="announcement-search">Tìm kiếm thông tin</label>
                <input id="announcement-search" name="q" type="search" value="{{ request('q') }}" placeholder="Tìm kiếm thông tin" class="min-w-0 rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-800 placeholder:text-gray-500 focus:border-blue-600 focus:ring-blue-600">

                <label class="sr-only" for="date-from">Từ ngày</label>
                <input id="date-from" name="from" type="date" value="{{ request('from') }}" class="min-w-0 rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-700 focus:border-blue-600 focus:ring-blue-600">

                <label class="sr-only" for="date-to">Đến ngày</label>
                <input id="date-to" name="to" type="date" value="{{ request('to') }}" class="min-w-0 rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-700 focus:border-blue-600 focus:ring-blue-600">

                <button type="submit" class="rounded-lg bg-blue-700 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800">Tìm kiếm</button>
            </form>

            <nav class="flex flex-wrap gap-2 pt-3" aria-label="Loại tin tức">
                @foreach ($categories as $category => $label)
                    <a href="{{ route('announcements.index', ['category' => $category]) }}"
                       @if ($activeCategory === $category) aria-current="page" @endif
                       class="rounded-full px-4 py-2 text-sm font-medium transition {{ $activeCategory === $category ? 'bg-blue-700 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-blue-50 hover:text-blue-800' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>
        </section>

        <section class="space-y-3" aria-live="polite">
            @forelse ($announcements as $announcement)
                <article class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2 text-xs text-gray-500">
                                <span class="rounded-full bg-blue-50 px-2.5 py-1 font-semibold text-blue-700">{{ $categories[$announcement->category] ?? 'Tin khác' }}</span>
                                @if ($announcement->published_at)
                                    <time datetime="{{ $announcement->published_at->toISOString() }}">{{ $announcement->published_at->format('d/m/Y H:i') }}</time>
                                @endif
                            </div>
                            <h2 class="mt-3 text-lg font-semibold text-gray-900">{{ $announcement->title }}</h2>
                            <p class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-600">{{ $announcement->body }}</p>
                        </div>
                        @can('manage-students')
                            <form method="POST" action="{{ route('announcements.destroy', $announcement) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tin tức này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-100">Xóa tin tức</button>
                            </form>
                        @endcan
                    </div>
                </article>
            @empty
                <div class="flex min-h-56 items-center justify-center rounded-xl border border-dashed border-blue-200 bg-white p-6 text-center text-sm text-gray-500">
                    Hiện tại chưa có tin tức nào
                </div>
            @endforelse
        </section>

        @if ($announcements->hasPages())
            <div class="rounded-xl bg-white px-5 py-4 shadow-sm">{{ $announcements->links() }}</div>
        @endif
    </div>

    @can('manage-students')
        <x-modal name="create-announcement-modal" :show="false" focusable>
            <form method="POST" action="{{ route('announcements.store') }}" class="space-y-4 p-6">
                @csrf
                <h2 class="text-lg font-semibold text-gray-900">Đăng tin tức</h2>

                <div>
                    <label for="announcement-title" class="mb-1 block text-sm font-medium text-gray-700">Tiêu đề</label>
                    <input id="announcement-title" name="title" type="text" required maxlength="255" value="{{ old('title') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">
                    @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="announcement-category" class="mb-1 block text-sm font-medium text-gray-700">Danh mục</label>
                    <select id="announcement-category" name="category" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">
                        <option value="general">Tin khác</option>
                        <option value="club-events">Sự kiện câu lạc bộ</option>
                        <option value="volunteer-events">Sự kiện tình nguyện</option>
                    </select>
                    @error('category') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="announcement-body" class="mb-1 block text-sm font-medium text-gray-700">Nội dung</label>
                    <textarea id="announcement-body" name="body" rows="5" required maxlength="10000" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">{{ old('body') }}</textarea>
                    @error('body') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end gap-2 border-t border-gray-100 pt-4">
                    <button type="button" x-on:click="$dispatch('close-modal', 'create-announcement-modal')" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Hủy</button>
                    <button type="submit" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Đăng tin</button>
                </div>
            </form>
        </x-modal>
    @endcan
</x-app-layout>