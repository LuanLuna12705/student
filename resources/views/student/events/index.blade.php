<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Đăng ký sự kiện</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-5 py-5 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <section class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-100 px-5 py-4">
                <div>
                    <h1 class="text-lg font-bold text-gray-900">Đăng ký sự kiện</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ $events->total() }} sự kiện sắp diễn ra</p>
                </div>
                @can('manage-students')
                    <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'create-event-modal')" class="rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800">
                        Thêm sự kiện
                    </button>
                @endcan
            </div>

            <div class="border-b border-gray-100 bg-gray-50 px-5 py-4">
                <form method="GET" action="{{ route('events.index') }}" class="flex flex-col gap-3 md:flex-row md:items-end">
                    <div class="flex-1">
                        <label for="event-search" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Tìm kiếm</label>
                        <input id="event-search" name="search" type="text" value="{{ old('search', $search) }}" placeholder="Tên, địa điểm, mô tả..." class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                    </div>

                    <div class="w-full md:w-52">
                        <label for="event-status-filter" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Trạng thái</label>
                        <select id="event-status-filter" name="status" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Tất cả</option>
                            <option value="open" {{ $status === 'open' ? 'selected' : '' }}>Đang mở đăng ký</option>
                            <option value="closed" {{ $status === 'closed' ? 'selected' : '' }}>Đã đóng</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-500">
                            Tìm kiếm
                        </button>
                        @if ($search !== '' || $status !== 'all')
                            <a href="{{ route('events.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                                Xóa lọc
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="grid gap-4 p-5 sm:grid-cols-2">
                @forelse ($events as $event)
                    @php
                        $eventImage = $event->image_url ?: 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=80';
                    @endphp
                    <article class="flex min-h-[420px] flex-col overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                        <img src="{{ $eventImage }}" alt="{{ $event->title }}" class="h-40 w-full object-cover" loading="lazy">

                        <div class="flex flex-1 flex-col p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h2 class="text-base font-bold text-gray-900">{{ $event->title }}</h2>
                                    <p class="mt-2 text-sm leading-6 text-gray-600">{{ $event->description }}</p>
                                </div>
                                @can('manage-students')
                                    <div class="flex shrink-0 items-center gap-2">
                                        <button type="button" x-on:click="$dispatch('open-modal', 'edit-event-modal-{{ $event->id }}')" class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-[11px] font-semibold text-blue-700 transition hover:bg-blue-100">Sửa sự kiện</button>
                                        <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'delete-event-confirm-modal-{{ $event->id }}')" class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-[11px] font-semibold text-red-700 transition hover:bg-red-100">Xóa sự kiện</button>
                                    </div>
                                @endcan
                            </div>

                            <div class="mt-4">
                                @php
                                    $isRegistered = auth()->user()->registeredEvents()->whereKey($event->id)->exists();
                                    $canRegister = ! $isRegistered && $event->registration_deadline->isFuture() && $event->event_date->isFuture() && $event->registered_users_count < $event->max_participants;
                                @endphp

                                @if ($isRegistered)
                                    <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'unregister-event-confirm-modal-{{ $event->id }}')" class="w-full rounded-lg border border-gray-300 bg-gray-100 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-200">Hủy đăng ký</button>
                                @elseif ($canRegister)
                                    <form method="POST" action="{{ route('events.register', $event) }}">
                                        @csrf
                                        <button type="submit" class="w-full rounded-lg bg-blue-700 px-3 py-2 text-sm font-semibold text-white transition hover:bg-blue-800">Đăng ký sự kiện</button>
                                    </form>
                                @else
                                    <div class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-center text-sm font-medium text-gray-500">
                                        {{ $event->registered_users_count >= $event->max_participants ? 'Đã hết chỗ' : 'Hết hạn đăng ký' }}
                                    </div>
                                @endif
                            </div>

                            @if ($isRegistered)
                                <x-confirm-modal
                                    name="unregister-event-confirm-modal-{{ $event->id }}"
                                    title="Xác nhận hủy đăng ký"
                                    submit-text="Hủy đăng ký"
                                    submit-class="bg-gray-700 hover:bg-gray-600"
                                    form="unregister-event-form-{{ $event->id }}"
                                >
                                    Bạn có chắc chắn muốn hủy đăng ký sự kiện <span class="font-semibold text-gray-900">{{ $event->title }}</span> không?
                                </x-confirm-modal>

                                <form id="unregister-event-form-{{ $event->id }}" method="POST" action="{{ route('events.unregister', $event) }}" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @endif

                            <dl class="mt-4 grid grid-cols-2 gap-x-4 gap-y-3 border-t border-gray-100 pt-4 text-xs text-gray-600">
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
                        </div>
                    </article>

                    @can('manage-students')
                        <x-confirm-modal
                            name="delete-event-confirm-modal-{{ $event->id }}"
                            title="Xác nhận xóa sự kiện"
                            submit-text="Xóa"
                            submit-class="bg-red-600 hover:bg-red-500"
                            form="delete-event-form-{{ $event->id }}"
                        >
                            Bạn có chắc chắn muốn xóa sự kiện <span class="font-semibold text-gray-900">{{ $event->title }}</span> không?
                        </x-confirm-modal>

                        <form id="delete-event-form-{{ $event->id }}" method="POST" action="{{ route('events.destroy', $event) }}" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>

                        <x-modal name="edit-event-modal-{{ $event->id }}" :show="false" focusable>
                            <form method="POST" action="{{ route('events.update', $event) }}" enctype="multipart/form-data" class="space-y-4 p-6">
                                @csrf
                                @method('PATCH')
                                <h2 class="text-lg font-semibold text-gray-900">Chỉnh sửa sự kiện</h2>

                                <div>
                                    <label for="edit-event-title-{{ $event->id }}" class="mb-1 block text-sm font-medium text-gray-700">Tên sự kiện</label>
                                    <input id="edit-event-title-{{ $event->id }}" name="title" type="text" required maxlength="255" value="{{ old('title', $event->title) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">
                                    @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="edit-event-description-{{ $event->id }}" class="mb-1 block text-sm font-medium text-gray-700">Mô tả</label>
                                    <textarea id="edit-event-description-{{ $event->id }}" name="description" rows="3" required maxlength="10000" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">{{ old('description', $event->description) }}</textarea>
                                    @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-3">
                                    <div>
                                        <label for="edit-event-image-{{ $event->id }}" class="mb-1 block text-sm font-medium text-gray-700">Ảnh mới từ máy</label>
                                        <input id="edit-event-image-{{ $event->id }}" name="image" type="file" accept="image/*" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-blue-600 focus:ring-blue-600">
                                        @error('image') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="edit-event-image-url-{{ $event->id }}" class="mb-1 block text-sm font-medium text-gray-700">Hoặc link ảnh sự kiện</label>
                                        <input id="edit-event-image-url-{{ $event->id }}" name="image_url" type="url" maxlength="500" value="{{ old('image_url', $event->image_url) }}" placeholder="https://images.unsplash.com/..." class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">
                                        @error('image_url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label for="edit-event-location-{{ $event->id }}" class="mb-1 block text-sm font-medium text-gray-700">Địa điểm</label>
                                        <input id="edit-event-location-{{ $event->id }}" name="location" type="text" required maxlength="255" value="{{ old('location', $event->location) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">
                                        @error('location') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="edit-event-capacity-{{ $event->id }}" class="mb-1 block text-sm font-medium text-gray-700">Số chỗ</label>
                                        <input id="edit-event-capacity-{{ $event->id }}" name="max_participants" type="number" min="1" max="100000" required value="{{ old('max_participants', $event->max_participants) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">
                                        @error('max_participants') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="edit-event-date-{{ $event->id }}" class="mb-1 block text-sm font-medium text-gray-700">Thời gian diễn ra</label>
                                        <input id="edit-event-date-{{ $event->id }}" name="event_date" type="datetime-local" required value="{{ old('event_date', $event->event_date?->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">
                                        @error('event_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="edit-event-deadline-{{ $event->id }}" class="mb-1 block text-sm font-medium text-gray-700">Hạn đăng ký</label>
                                        <input id="edit-event-deadline-{{ $event->id }}" name="registration_deadline" type="datetime-local" required value="{{ old('registration_deadline', $event->registration_deadline?->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">
                                        @error('registration_deadline') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div class="flex justify-end gap-2 border-t border-gray-100 pt-4">
                                    <button type="button" x-on:click="$dispatch('close-modal', 'edit-event-modal-{{ $event->id }}')" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Hủy</button>
                                    <button type="submit" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Lưu thay đổi</button>
                                </div>
                            </form>
                        </x-modal>
                    @endcan
                @empty
                    <div class="col-span-full rounded-lg border border-dashed border-gray-300 px-6 py-12 text-center text-sm text-gray-500">
                        Chưa có sự kiện nào.
                    </div>
                @endforelse
            </div>

            @if ($events->hasPages())
                <div class="border-t border-gray-100 px-5 py-4">{{ $events->links() }}</div>
            @endif
        </section>
    </div>

    @can('manage-students')
        <x-modal name="create-event-modal" :show="false" focusable>
            <form method="POST" action="{{ route('events.store') }}" enctype="multipart/form-data" class="space-y-4 p-6">
                @csrf
                <h2 class="text-lg font-semibold text-gray-900">Thêm sự kiện</h2>

                <div>
                    <label for="event-title" class="mb-1 block text-sm font-medium text-gray-700">Tên sự kiện</label>
                    <input id="event-title" name="title" type="text" required maxlength="255" value="{{ old('title') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">
                    @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="event-description" class="mb-1 block text-sm font-medium text-gray-700">Mô tả</label>
                    <textarea id="event-description" name="description" rows="3" required maxlength="10000" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">{{ old('description') }}</textarea>
                    @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-3">
                    <div>
                        <label for="event-image" class="mb-1 block text-sm font-medium text-gray-700">Ảnh sự kiện từ máy</label>
                        <input id="event-image" name="image" type="file" accept="image/*" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-blue-600 focus:ring-blue-600">
                        @error('image') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="event-image-url" class="mb-1 block text-sm font-medium text-gray-700">Hoặc link ảnh sự kiện</label>
                        <input id="event-image-url" name="image_url" type="url" maxlength="500" value="{{ old('image_url') }}" placeholder="https://images.unsplash.com/..." class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">
                        @error('image_url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="event-location" class="mb-1 block text-sm font-medium text-gray-700">Địa điểm</label>
                        <input id="event-location" name="location" type="text" required maxlength="255" value="{{ old('location') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">
                        @error('location') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="event-capacity" class="mb-1 block text-sm font-medium text-gray-700">Số chỗ</label>
                        <input id="event-capacity" name="max_participants" type="number" min="1" max="100000" required value="{{ old('max_participants', 100) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">
                        @error('max_participants') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="event-date" class="mb-1 block text-sm font-medium text-gray-700">Thời gian diễn ra</label>
                        <input id="event-date" name="event_date" type="datetime-local" required value="{{ old('event_date') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">
                        @error('event_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="event-deadline" class="mb-1 block text-sm font-medium text-gray-700">Hạn đăng ký</label>
                        <input id="event-deadline" name="registration_deadline" type="datetime-local" required value="{{ old('registration_deadline') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">
                        @error('registration_deadline') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-2 border-t border-gray-100 pt-4">
                    <button type="button" x-on:click="$dispatch('close-modal', 'create-event-modal')" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Hủy</button>
                    <button type="submit" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Thêm sự kiện</button>
                </div>
            </form>
        </x-modal>
    @endcan
</x-app-layout>