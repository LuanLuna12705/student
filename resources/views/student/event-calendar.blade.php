<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Lịch sự kiện
        </h2>
    </x-slot>

    @php
        $weekdayMap = [1 => 'Thứ hai', 2 => 'Thứ ba', 3 => 'Thứ tư', 4 => 'Thứ năm', 5 => 'Thứ sáu', 6 => 'Thứ bảy', 7 => 'Chủ nhật'];
        $calendarEventsData = [];

        foreach ($calendarDays as $dayItem) {
            $dateString = $dayItem->toDateString();
            $dayEvents = $eventsByDate->get($dateString, collect());

            if ($dayEvents->isNotEmpty()) {
                $calendarEventsData[$dateString] = [
                    'label' => $weekdayMap[$dayItem->dayOfWeekIso] . ', ngày ' . $dayItem->format('d/m/Y'),
                    'events' => $dayEvents->map(function ($event) {
                        return [
                            'id' => $event->id,
                            'title' => $event->title,
                            'description' => $event->description,
                            'location' => $event->location,
                            'time' => $event->event_date->format('H:i'),
                            'event_date' => $event->event_date->format('d/m/Y H:i'),
                            'deadline' => $event->registration_deadline ? $event->registration_deadline->format('d/m/Y H:i') : null,
                            'max_participants' => $event->max_participants,
                            'status' => $event->status,
                            'image_url' => $event->image_url ?: 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=80',
                        ];
                    })->values()->all(),
                ];
            }
        }
    @endphp

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8"
         x-data="{
             calendarData: @js($calendarEventsData),
             modalOpen: false,
             selectedDateLabel: '',
             selectedEvents: [],
             showDayEvents(dateKey) {
                 if (this.calendarData[dateKey]) {
                     this.selectedDateLabel = this.calendarData[dateKey].label;
                     this.selectedEvents = this.calendarData[dateKey].events;
                     this.modalOpen = true;
                 }
             },
             closeModal() {
                 this.modalOpen = false;
             }
         }"
         x-init="$watch('modalOpen', value => {
             if (value) {
                 document.body.classList.add('overflow-y-hidden');
             } else {
                 document.body.classList.remove('overflow-y-hidden');
             }
         })"
         @keydown.escape.window="closeModal()">

        <section class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-100 px-4 py-4 sm:px-6">
                <div>
                    <h1 class="text-xl font-bold text-gray-800">{{ $monthLabel }}</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ $eventsByDate->flatten()->count() }} sự kiện trong tháng</p>
                </div>

                <nav class="flex items-center gap-2" aria-label="Điều hướng lịch">
                    <a href="{{ route('events.calendar', ['month' => $previousMonth->month, 'year' => $previousMonth->year]) }}"
                       class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 text-gray-700 transition hover:bg-gray-50"
                       aria-label="Tháng trước" title="Tháng trước">
                        <span aria-hidden="true">←</span>
                    </a>
                    <a href="{{ route('events.calendar') }}"
                       class="rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Hôm nay
                    </a>
                    <a href="{{ route('events.calendar', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
                       class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 text-gray-700 transition hover:bg-gray-50"
                       aria-label="Tháng sau" title="Tháng sau">
                        <span aria-hidden="true">→</span>
                    </a>
                </nav>
            </div>

            <div class="grid grid-cols-7 border-b border-gray-100 bg-gray-50 text-center text-xs font-semibold uppercase text-gray-500 sm:text-sm">
                @foreach (['Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy', 'Chủ nhật'] as $weekday)
                    <div class="px-1 py-3">{{ $weekday }}</div>
                @endforeach
            </div>

            <div class="grid grid-cols-7">
                @foreach ($calendarDays as $day)
                    @php
                        $dayKey = $day->toDateString();
                        $dateEvents = $eventsByDate->get($dayKey, collect());
                        $isCurrentMonth = $day->month === $monthStart->month;
                        $hasEvents = $dateEvents->isNotEmpty();
                    @endphp

                    <div class="min-h-24 border-b border-r border-gray-100 p-1.5 sm:min-h-32 sm:p-2 transition-colors duration-150 {{ $isCurrentMonth ? 'bg-white' : 'bg-gray-50 text-gray-400' }} {{ $hasEvents ? 'cursor-pointer hover:bg-blue-50/50 group' : '' }}"
                         @if ($hasEvents)
                             @click="showDayEvents('{{ $dayKey }}')"
                             title="Bấm để xem chi tiết sự kiện ngày {{ $day->format('d/m/Y') }}"
                             role="button"
                             tabindex="0"
                             @keydown.enter="showDayEvents('{{ $dayKey }}')"
                         @endif>
                        <div class="flex items-center justify-between">
                            @if ($hasEvents)
                                <span class="inline-flex items-center gap-1 rounded bg-blue-100/70 px-1.5 py-0.5 text-[10px] font-semibold text-blue-700 group-hover:bg-blue-200/80 transition-colors">
                                    <span class="h-1.5 w-1.5 rounded-full bg-blue-600"></span>
                                    <span>{{ $dateEvents->count() }}</span>
                                </span>
                            @else
                                <span></span>
                            @endif

                            <span class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-semibold {{ $day->isToday() ? 'bg-blue-600 text-white' : '' }}">
                                {{ $day->day }}
                            </span>
                        </div>

                        <div class="mt-1 space-y-1">
                            @foreach ($dateEvents as $event)
                                <div class="truncate rounded bg-blue-50 px-1.5 py-1 text-[10px] font-medium text-blue-800 transition-colors group-hover:bg-blue-100 group-hover:text-blue-900 sm:text-xs"
                                     title="{{ $event->title }} · {{ $event->event_date->format('H:i') }}">
                                    <span class="hidden sm:inline">{{ $event->event_date->format('H:i') }} </span>{{ $event->title }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($eventsByDate->isEmpty())
                <p class="px-4 py-5 text-center text-sm text-gray-500 sm:px-6">
                    Chưa có sự kiện nào trong tháng này.
                </p>
            @endif
        </section>

        <!-- Khung mô tả sự kiện (Modal Popup) -->
        <div x-show="modalOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-gray-900/60 p-4 backdrop-blur-sm sm:p-6"
             style="display: none;"
             @click.self="closeModal()"
             role="dialog"
             aria-modal="true">

            <div x-show="modalOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-3"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-3"
                 class="relative w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl border border-gray-100 my-8">

                <!-- Header Modal -->
                <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4 sm:px-6 bg-slate-50/70">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-700 shadow-sm">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base sm:text-lg font-bold text-gray-900 leading-tight" x-text="selectedDateLabel"></h3>
                            <p class="text-xs text-gray-500 mt-0.5" x-text="selectedEvents.length + ' sự kiện diễn ra'"></p>
                        </div>
                    </div>

                    <button type="button"
                            @click="closeModal()"
                            class="rounded-lg p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 focus:outline-none"
                            aria-label="Đóng">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Danh sách mô tả sự kiện -->
                <div class="max-h-[70vh] overflow-y-auto p-5 sm:p-6 space-y-6 divide-y divide-gray-100">
                    <template x-for="(event, index) in selectedEvents" :key="event.id || index">
                        <div class="pt-6 first:pt-0 space-y-4">
                            <!-- Tiêu đề & Giờ diễn ra -->
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span class="inline-flex items-center gap-1 rounded-md bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-800 ring-1 ring-inset ring-blue-700/10">
                                    <svg class="h-3.5 w-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span x-text="event.time"></span>
                                </span>
                                <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/10">
                                    Sắp diễn ra
                                </span>
                            </div>

                            <h4 class="text-lg font-bold text-gray-900 leading-snug" x-text="event.title"></h4>

                            <!-- Ảnh sự kiện -->
                            <template x-if="event.image_url">
                                <div class="overflow-hidden rounded-xl border border-gray-100 bg-gray-50 shadow-inner max-h-48 sm:max-h-56">
                                    <img :src="event.image_url" :alt="event.title" class="w-full h-48 sm:h-56 object-cover" loading="lazy">
                                </div>
                            </template>

                            <!-- Bảng thông tin vắn tắt -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 rounded-xl bg-gray-50/80 p-3.5 border border-gray-100 text-xs sm:text-sm">
                                <div class="flex items-start gap-2.5">
                                    <span class="text-base select-none">📅</span>
                                    <div class="min-w-0">
                                        <span class="block text-xs font-medium text-gray-500">Thời gian bắt đầu</span>
                                        <span class="font-semibold text-gray-800" x-text="event.event_date"></span>
                                    </div>
                                </div>

                                <div class="flex items-start gap-2.5">
                                    <span class="text-base select-none">📍</span>
                                    <div class="min-w-0">
                                        <span class="block text-xs font-medium text-gray-500">Địa điểm</span>
                                        <span class="font-semibold text-gray-800 truncate" x-text="event.location || 'Chưa cập nhật'"></span>
                                    </div>
                                </div>

                                <template x-if="event.deadline">
                                    <div class="flex items-start gap-2.5">
                                        <span class="text-base select-none">⏰</span>
                                        <div class="min-w-0">
                                            <span class="block text-xs font-medium text-gray-500">Hạn chót đăng ký</span>
                                            <span class="font-semibold text-gray-800" x-text="event.deadline"></span>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="event.max_participants">
                                    <div class="flex items-start gap-2.5">
                                        <span class="text-base select-none">👥</span>
                                        <div class="min-w-0">
                                            <span class="block text-xs font-medium text-gray-500">Giới hạn số lượng</span>
                                            <span class="font-semibold text-gray-800" x-text="event.max_participants + ' sinh viên'"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Mô tả chi tiết sự kiện -->
                            <div>
                                <span class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5 flex items-center gap-1.5">
                                    <span>📝</span> Mô tả chi tiết
                                </span>
                                <div class="rounded-xl border border-gray-100 bg-white p-4 text-sm leading-relaxed text-gray-700 shadow-sm whitespace-pre-line"
                                     x-text="event.description || 'Chưa có thông tin mô tả chi tiết cho sự kiện này.'">
                                </div>
                            </div>

                            <!-- Nút liên kết chuyển đến trang đăng ký sự kiện -->
                            <div class="flex items-center justify-end pt-2">
                                <a href="{{ route('events.index') }}"
                                   class="inline-flex items-center gap-1.5 rounded-lg bg-blue-700 px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-800">
                                    <span>Xem chi tiết tại Đăng ký sự kiện</span>
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Footer Modal -->
                <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50 px-5 py-3 sm:px-6">
                    <span class="text-xs text-gray-500 hidden sm:inline">Bấm phím <kbd class="px-1.5 py-0.5 rounded border border-gray-300 bg-white font-mono text-[10px]">ESC</kbd> hoặc nhấp ra ngoài để đóng</span>
                    <button type="button"
                            @click="closeModal()"
                            class="w-full sm:w-auto rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>