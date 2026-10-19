<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Quản lý sinh viên
        </h2>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6 py-5 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <section aria-label="Thống kê tài khoản" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Tổng số tài khoản</p>
                <p data-stat-value="{{ $totalAccountCount }}" class="mt-2 text-3xl font-bold text-gray-800">{{ number_format($totalAccountCount) }}</p>
            </article>
            <article class="rounded-xl border border-blue-100 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Tài khoản sinh viên</p>
                <p data-stat-value="{{ $studentCount }}" class="mt-2 text-3xl font-bold text-blue-700">{{ number_format($studentCount) }}</p>
            </article>
            <article class="rounded-xl border border-amber-100 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Trưởng câu lạc bộ</p>
                <p data-stat-value="{{ $clubLeaderCount }}" class="mt-2 text-3xl font-bold text-amber-600">{{ number_format($clubLeaderCount) }}</p>
            </article>
            <article class="rounded-xl border border-violet-100 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Tài khoản quản trị</p>
                <p data-stat-value="{{ $adminCount }}" class="mt-2 text-3xl font-bold text-violet-700">{{ number_format($adminCount) }}</p>
            </article>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 px-5 py-4">
                <div>
                    <h1 class="text-lg font-bold text-gray-900">Danh sách sinh viên và trưởng CLB</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ $students->total() }} kết quả (không gồm quản trị viên)</p>
                </div>

                <div class="flex min-w-40 flex-col gap-2">
                    <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'create-student-modal')" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-500">
                        Thêm sinh viên
                    </button>
                    @if ($students->isNotEmpty())
                        <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'bulk-role-confirm-modal')" class="inline-flex items-center justify-center rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-300">
                            Lưu thay đổi
                        </button>
                    @endif
                </div>
            </div>

            <div class="border-b border-gray-100 bg-gray-50 px-5 py-4">
                <form method="GET" action="{{ route('admin.students.index') }}" class="flex flex-col gap-3 md:flex-row md:items-end">
                    <div class="flex-1">
                        <label for="student-search" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Tìm kiếm</label>
                        <input id="student-search" name="search" type="text" value="{{ old('search', $search) }}" placeholder="Tên hoặc email..." class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                    </div>

                    <div class="w-full md:w-52">
                        <label for="student-role-filter" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Vai trò</label>
                        <select id="student-role-filter" name="role" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                            <option value="all" {{ $roleFilter === 'all' ? 'selected' : '' }}>Tất cả</option>
                            <option value="student" {{ $roleFilter === 'student' ? 'selected' : '' }}>Sinh viên</option>
                            <option value="club_leader" {{ $roleFilter === 'club_leader' ? 'selected' : '' }}>Trưởng CLB</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-500">
                            Tìm kiếm
                        </button>
                        @if ($search !== '' || $roleFilter !== 'all')
                            <a href="{{ route('admin.students.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                                Xóa lọc
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <form id="bulk-role-form" method="POST" action="{{ route('admin.students.roles') }}" class="hidden">
                @csrf
                @method('PATCH')
            </form>

            <x-confirm-modal
                name="bulk-role-confirm-modal"
                title="Xác nhận lưu thay đổi vai trò"
                submit-text="Xác nhận"
                submit-class="bg-orange-500 hover:bg-orange-600"
                form="bulk-role-form"
            >
                Bạn có chắc chắn muốn cập nhật vai trò của các tài khoản đã được chỉnh sửa?
            </x-confirm-modal>

            <div class="overflow-x-auto">
                <table class="min-w-full table-fixed divide-y divide-gray-200 text-left text-sm">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                        <tr>
                            <th scope="col" class="w-[22%] px-5 py-3">Họ và tên</th>
                            <th scope="col" class="w-[27%] px-5 py-3">Email</th>
                            <th scope="col" class="w-[16%] px-5 py-3">Vai trò</th>
                            <th scope="col" class="w-[35%] px-5 py-3 text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse ($students as $student)
                            <tr>
                                <td class="whitespace-nowrap px-5 py-3 font-medium text-gray-900">{{ $student->name }}</td>
                                <td class="truncate px-5 py-3">{{ $student->email }}</td>
                                <td class="px-5 py-3">
                                    <select form="bulk-role-form" name="roles[{{ $student->id }}]" class="w-full rounded-lg border border-gray-300 bg-white px-2.5 py-2 text-sm text-gray-700 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                                                <option value="student" {{ $student->role === 'student' ? 'selected' : '' }}>Sinh viên</option>
                                                <option value="club_leader" {{ $student->role === 'club_leader' ? 'selected' : '' }}>Trưởng CLB</option>
                                            </select>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.students.show', $student) }}" class="inline-flex items-center justify-center rounded-lg border border-blue-200 bg-blue-50 px-2.5 py-2 text-xs font-semibold text-blue-700 shadow-sm transition hover:border-blue-300 hover:bg-blue-100">Xem chi tiết</a>
                                        <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'delete-student-confirm-modal-{{ $student->id }}')" class="inline-flex items-center justify-center rounded-lg border border-red-200 bg-red-50 px-2.5 py-2 text-xs font-semibold text-red-700 shadow-sm transition hover:border-red-300 hover:bg-red-100">Xóa</button>

                                        <x-confirm-modal
                                            name="delete-student-confirm-modal-{{ $student->id }}"
                                            title="Xác nhận xóa tài khoản"
                                            submit-text="Xóa"
                                            submit-class="bg-red-600 hover:bg-red-500"
                                            form="delete-student-form-{{ $student->id }}"
                                        >
                                            Bạn có chắc chắn muốn xóa tài khoản <span class="font-semibold text-gray-900">{{ $student->name }}</span> không?
                                        </x-confirm-modal>

                                        <form id="delete-student-form-{{ $student->id }}" method="POST" action="{{ route('admin.students.destroy', $student) }}" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-10 text-center text-gray-500">Chưa có sinh viên nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($students->hasPages())
                <div class="border-t border-gray-100 px-5 py-4">{{ $students->links() }}</div>
            @endif
        </section>
    </div>

    <x-modal name="create-student-modal" :show="false" focusable>
        <form method="POST" action="{{ route('admin.students.store') }}" class="p-6">
            @csrf

            <h2 class="text-lg font-semibold text-gray-900">Thêm sinh viên mới</h2>

            <div class="mt-4 space-y-4">
                <div>
                    <label for="student-name" class="mb-1 block text-sm font-medium text-gray-700">Họ và tên</label>
                    <input id="student-name" name="name" type="text" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                </div>

                <div>
                    <label for="student-email" class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                    <input id="student-email" name="email" type="email" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                </div>

                <div>
                    <label for="student-password" class="mb-1 block text-sm font-medium text-gray-700">Mật khẩu</label>
                    <input id="student-password" name="password" type="password" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-4">
                <button type="button" x-on:click="$dispatch('close-modal', 'create-student-modal')" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50">
                    Hủy
                </button>
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-500">
                    Tạo tài khoản
                </button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
