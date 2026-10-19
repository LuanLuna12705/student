<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Sự kiện đang đăng ký</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-5 py-5 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <section class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-4 border-b border-gray-100 px-5 py-4">
                <div>
                    <h1 class="text-lg font-bold text-gray-900">Danh sách sự kiện bạn đã đăng ký</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ $events->count() }} sự kiện</p>
                </div>
                <a href="{{ route('events.index') }}" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Xem tất cả sự kiện</a>
            </div>

            <div class="grid gap-4 p-5 sm:grid-cols-2">
                @forelse ($events as $event)
                    @php
                        $eventImage = $event->image_url ?: 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=80';
                    @endphp
                    <article class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                        <img src="{{ $eventImage }}" alt="{{ $event->title }}" class="h-40 w-full object-cover" loading="lazy">
                        <div class="p-5">
                            <h2 class="text-base font-bold text-gray-900">{{ $event->title }}</h2>
                            <p class="mt-2 text-sm leading-6 text-gray-600">{{ $event->description }}</p>

                            <dl class="mt-4 grid grid-cols-2 gap-x-4 gap-y-3 text-xs text-gray-600">
                                <div>
                                    <dt class="font-semibold text-gray-500">Thời gian</dt>
                                    <dd class="mt-1">{{ $event->event_date->format('d/m/Y H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold text-gray-500">Địa điểm</dt>
                                    <dd class="mt-1">{{ $event->location }}</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold text-gray-500">Hạn đăng ký</dt>
                                    <dd class="mt-1">{{ $event->registration_deadline->format('d/m/Y H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold text-gray-500">Số chỗ</dt>
                                    <dd class="mt-1">{{ $event->registered_users_count }}/{{ $event->max_participants }}</dd>
                                </div>
                            </dl>

                            <form method="POST" action="{{ route('events.unregister', $event) }}" class="mt-4">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-100">
                                    Hủy đăng ký
                                </button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-lg border border-dashed border-gray-300 px-6 py-12 text-center text-sm text-gray-500">
                        Bạn chưa đăng ký sự kiện nào.
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
