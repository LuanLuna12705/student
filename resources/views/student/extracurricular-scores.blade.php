<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tra cứu hoạt động
        </h2>
    </x-slot>

    @php
        $studentDetails = [
            ['icon' => '🎓', 'label' => 'Ngành:', 'value' => data_get($student, 'major') ?: 'Chưa cập nhật'],
            ['icon' => '👤', 'label' => 'Họ tên:', 'value' => $student->name],
            ['icon' => '▤', 'label' => 'Mã số:', 'value' => data_get($student, 'student_code') ?: 'Chưa cập nhật'],
            ['icon' => '▦', 'label' => 'Ngày sinh:', 'value' => $student->date_of_birth?->format('d/m/Y') ?: 'Chưa cập nhật'],
            ['icon' => '⚧', 'label' => 'Giới tính:', 'value' => data_get($student, 'gender') ?: 'Chưa cập nhật'],
            ['icon' => '▦', 'label' => 'Lớp:', 'value' => data_get($student, 'class_name') ?: 'Chưa cập nhật'],
        ];
    @endphp

    <div class="mx-auto grid max-w-6xl grid-cols-1 gap-5 py-3 sm:px-2 lg:grid-cols-[minmax(0,3fr)_minmax(0,7fr)]">
        <div class="space-y-5">
            <section class="w-full overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <h1 class="bg-blue-700 px-5 py-3 text-base font-semibold text-white">
                Thông tin sinh viên
            </h1>

            <dl class="space-y-3 p-5 sm:p-6">
                @foreach ($studentDetails as $detail)
                    <div class="grid grid-cols-[24px_minmax(0,1fr)] items-start gap-x-2 text-sm">
                        <span class="row-span-2 pt-0.5 text-center text-gray-500" aria-hidden="true">{{ $detail['icon'] }}</span>
                        <dt class="text-xs text-gray-500">{{ $detail['label'] }}</dt>
                        <dd class="min-w-0 break-words font-semibold text-gray-900">{{ $detail['value'] }}</dd>
                    </div>
                @endforeach
            </dl>
            </section>

            <section class="w-full rounded-xl border border-blue-100 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-blue-700">HOẠT ĐỘNG SINH VIÊN</p>
                    <h2 class="mt-1 text-lg font-bold text-gray-900">Điểm ngoại khóa</h2>
                    <p class="mt-1 text-sm text-gray-500">Hệ thống chưa có dữ liệu điểm được xác nhận.</p>
                </div>
                <div class="min-w-20 rounded-lg bg-blue-50 px-4 py-3 text-center">
                    <span class="block text-xs font-medium text-blue-700">Điểm</span>
                    <span class="mt-1 block text-xl font-bold text-blue-900">—</span>
                </div>
            </div>
            <p class="mt-4 border-t border-gray-100 pt-3 text-sm text-gray-500">
                Đăng ký sự kiện không đồng nghĩa với đã tham dự, vì vậy hệ thống không tự quy đổi điểm.
            </p>
            </section>
        </div>

        <section class="w-full overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="bg-blue-700 px-5 py-3 text-white">
                <h2 class="text-base font-semibold">Sự kiện đã đăng ký</h2>
                <p class="mt-1 text-xs text-blue-100">Danh sách sự kiện theo từng kỳ học</p>
            </div>

            <div class="space-y-3 p-4 sm:p-5">
                @forelse ($eventsBySemester as $semester => $events)
                    <section class="rounded-lg border border-gray-200 p-4">
                        <h3 class="text-sm font-semibold text-gray-800">{{ $semester }}</h3>
                        <ul class="mt-3 space-y-3">
                            @foreach ($events as $event)
                                <li class="border-t border-gray-100 pt-3 first:border-0 first:pt-0">
                                    <p class="text-sm font-semibold text-gray-900">{{ $event->title }}</p>
                                    <p class="mt-1 text-sm text-gray-600">
                                        {{ $event->event_date->locale('vi')->isoFormat('DD/MM/YYYY [lúc] HH:mm') }}
                                        · {{ $event->location }}
                                    </p>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @empty
                    <p class="text-sm text-gray-500">Bạn chưa đăng ký sự kiện nào.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>