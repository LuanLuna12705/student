<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Danh sách quản trị viên
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

        <section class="space-y-5 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div>
                <h1 class="text-lg font-bold text-gray-900">Tạo tài khoản quản trị</h1>
                <p class="mt-1 text-sm text-gray-500">Tài khoản mới sẽ được thêm trực tiếp vào danh sách quản trị viên.</p>
            </div>

            <form method="POST" action="{{ route('admin.administrators.store') }}" class="grid gap-4 rounded-lg bg-gray-50 p-4 sm:grid-cols-2">
                @csrf
                <div>
                    <label for="administrator-name" class="mb-1 block text-sm font-medium text-gray-700">Họ và tên</label>
                    <input id="administrator-name" name="name" type="text" value="{{ old('name') }}" required maxlength="255" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="administrator-email" class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                    <input id="administrator-email" name="email" type="email" value="{{ old('email') }}" required maxlength="255" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="administrator-password" class="mb-1 block text-sm font-medium text-gray-700">Mật khẩu</label>
                    <input id="administrator-password" name="password" type="password" required minlength="6" autocomplete="new-password" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800 sm:w-auto">
                        Thêm quản trị viên
                    </button>
                </div>
            </form>
        </section>

        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-5 py-4">
                <h1 class="text-lg font-bold text-gray-900">Các tài khoản quản trị</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $administrators->total() }} tài khoản</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                        <tr>
                            <th scope="col" class="px-5 py-3">Họ và tên</th>
                            <th scope="col" class="px-5 py-3">Email</th>
                            <th scope="col" class="px-5 py-3">Ngày tạo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse ($administrators as $administrator)
                            <tr>
                                <td class="px-5 py-3 font-medium text-gray-900">{{ $administrator->name }}</td>
                                <td class="px-5 py-3">{{ $administrator->email }}</td>
                                <td class="px-5 py-3">{{ $administrator->created_at?->format('d/m/Y') ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-10 text-center text-gray-500">Chưa có tài khoản quản trị nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($administrators->hasPages())
                <div class="border-t border-gray-100 px-5 py-4">{{ $administrators->links() }}</div>
            @endif
        </section>
    </div>
</x-app-layout>
