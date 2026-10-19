<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Lịch sự kiện
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                        $dateEvents = $eventsByDate->get($day->toDateString(), collect());
                        $isCurrentMonth = $day->month === $monthStart->month;
                    @endphp

                    <div class="min-h-24 border-b border-r border-gray-100 p-1.5 sm:min-h-32 sm:p-2 {{ $isCurrentMonth ? 'bg-white' : 'bg-gray-50 text-gray-400' }}">
                        <div class="flex justify-end">
                            <span class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-semibold {{ $day->isToday() ? 'bg-blue-600 text-white' : '' }}">
                                {{ $day->day }}
                            </span>
                        </div>

                        <div class="mt-1 space-y-1">
                            @foreach ($dateEvents as $event)
                                <div class="truncate rounded bg-blue-50 px-1.5 py-1 text-[10px] font-medium text-blue-800 sm:text-xs" title="{{ $event->title }} · {{ $event->event_date->format('H:i') }}">
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
    </div>
</x-app-layout>