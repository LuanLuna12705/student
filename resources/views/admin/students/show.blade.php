<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Chi tiết sự kiện đã đăng ký
        </h2>
    </x-slot>

    <div class="mx-auto max-w-6xl py-6 sm:px-6 lg:px-8">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-3 border-b border-gray-200 pb-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-medium uppercase tracking-[0.15em] text-blue-600">Sinh viên</p>
                    <h1 class="mt-1 text-2xl font-bold text-gray-900">{{ $student->name }}</h1>
                </div>

                <a href="{{ route('admin.students.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Quay lại danh sách
                </a>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-2">
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Email</p>
                    <p class="mt-2 font-medium text-gray-900">{{ $student->email }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Vai trò</p>
                    <p class="mt-2 font-medium text-gray-900">{{ match ($student->role) {
                        'admin' => 'Quản trị viên',
                        'club_leader' => 'Trưởng CLB',
                        default => 'Sinh viên',
                    } }}</p>
                </div>
            </div>

            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-900">Danh sách sự kiện đã đăng ký</h2>

                @php
                    $registrations = $student->eventRegistrations()->with('event')->get();
                @endphp

                @if ($registrations->isEmpty())
                    <div class="mt-4 rounded-lg border border-dashed border-gray-300 bg-gray-50 p-6 text-center text-gray-500">
                        Sinh viên này chưa đăng ký sự kiện nào.
                    </div>
                @else
                    <div class="mt-4 space-y-4">
                        @foreach ($registrations as $registration)
                            @php($event = $registration->event)
                            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $event?->title ?? 'Sự kiện đã xóa' }}</h3>
                                        <p class="mt-1 text-sm text-gray-600">{{ $event?->location ?? 'Không có địa điểm' }}</p>
                                    </div>
                                    <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-blue-700">
                                        {{ $registration->status }}
                                    </span>
                                </div>
                                <div class="mt-4 grid gap-3 text-sm text-gray-600 md:grid-cols-3">
                                    <div>
                                        <p class="font-medium text-gray-500">Ngày diễn ra</p>
                                        <p class="mt-1">{{ $event?->event_date?->format('d/m/Y') ?? 'Không rõ' }}</p>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-500">Hạn đăng ký</p>
                                        <p class="mt-1">{{ $event?->registration_deadline?->format('d/m/Y') ?? 'Không rõ' }}</p>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-500">Trạng thái đăng ký</p>
                                        <p class="mt-1">{{ $registration->status ?: 'Đã đăng ký' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
