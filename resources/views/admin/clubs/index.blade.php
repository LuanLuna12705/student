<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Quản lý sinh viên và câu lạc bộ
        </h2>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6 py-5 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <section aria-label="Thống kê câu lạc bộ" class="grid gap-4 sm:grid-cols-2">
            <article class="rounded-xl border border-blue-100 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Tổng số câu lạc bộ</p>
                <p data-stat-value="{{ $clubCount }}" class="mt-2 text-3xl font-bold text-blue-700">{{ number_format($clubCount) }}</p>
            </article>
            <article class="rounded-xl border border-emerald-100 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Câu lạc bộ đã có trưởng clb</p>
                <p data-stat-value="{{ $assignedLeaderCount }}" class="mt-2 text-3xl font-bold text-emerald-700">{{ number_format($assignedLeaderCount) }}</p>
            </article>
        </section>

        <section class="space-y-5 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div>
                <h1 class="text-lg font-bold text-gray-900">Danh sách câu lạc bộ</h1>
                <p class="mt-1 text-sm text-gray-500">Tên khởi tạo chỉ mang tính tham khảo; quản trị viên có thể bổ sung câu lạc bộ phù hợp với trường.</p>
            </div>

            <form method="POST" action="{{ route('admin.clubs.store') }}" class="grid gap-3 rounded-lg bg-gray-50 p-4 sm:grid-cols-[minmax(0,1fr)_minmax(0,2fr)_auto] sm:items-end">
                @csrf
                <div>
                    <label for="club-name" class="mb-1 block text-sm font-medium text-gray-700">Tên câu lạc bộ</label>
                    <input id="club-name" name="name" value="{{ old('name') }}" required maxlength="255" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="club-description" class="mb-1 block text-sm font-medium text-gray-700">Mô tả (không bắt buộc)</label>
                    <input id="club-description" name="description" value="{{ old('description') }}" maxlength="5000" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
                    Thêm câu lạc bộ
                </button>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                        <tr>
                            <th scope="col" class="px-4 py-3">Câu lạc bộ</th>
                            <th scope="col" class="px-4 py-3">Trưởng CLB</th>
                            <th scope="col" class="px-4 py-3 text-center">Số thành viên</th>
                            <th scope="col" class="px-4 py-3 text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse ($clubs as $club)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900">
                                    <span class="block">{{ $club->name }}</span>
                                    @if ($club->description)
                                        <span class="mt-1 block text-xs font-normal text-gray-500">{{ $club->description }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $club->leader?->name ?? 'Chưa phân công' }}</td>
                                <td class="px-4 py-3 text-center font-semibold">{{ $club->approved_members_count }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.clubs.show', $club) }}" class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-100">
                                            Xem chi tiết
                                        </a>
                                        <form method="POST" action="{{ route('admin.clubs.destroy', $club) }}" onsubmit="return confirm('Xóa câu lạc bộ này và các đơn đăng ký thành viên?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100">
                                                Xóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center text-gray-500">Chưa có câu lạc bộ nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($clubs->hasPages())
                <div class="border-t border-gray-100 pt-4">{{ $clubs->links() }}</div>
            @endif
        </section>
    </div>
</x-app-layout>
