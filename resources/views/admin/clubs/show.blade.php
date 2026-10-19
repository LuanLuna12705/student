<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Chi tiết câu lạc bộ
        </h2>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6 py-5 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-7">
            <div class="flex flex-wrap items-start justify-between gap-4 border-b border-gray-100 pb-5">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-blue-700">Câu lạc bộ</p>
                    <h1 class="mt-1 text-2xl font-bold text-gray-900">{{ $club->name }}</h1>
                    @if ($club->description)
                        <p class="mt-2 text-sm text-gray-600">{{ $club->description }}</p>
                    @endif
                </div>
                <a href="{{ route('admin.clubs.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    Quay lại danh sách
                </a>
            </div>

            <form method="POST" action="{{ route('admin.clubs.leader', $club) }}" class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-end">
                @csrf
                @method('PATCH')
                <div class="flex-1">
                    <label for="leader_id" class="mb-1 block text-sm font-medium text-gray-700">Trưởng CLB</label>
                    <select id="leader_id" name="leader_id" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                        <option value="">Chưa phân công</option>
                        @foreach ($clubLeaders as $clubLeader)
                            <option value="{{ $clubLeader->id }}" @selected($club->leader_id === $clubLeader->id)>{{ $clubLeader->name }} — {{ $clubLeader->email }}</option>
                        @endforeach
                    </select>
                    @error('leader_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
                    Lưu trưởng CLB
                </button>
            </form>

            <div class="mt-8 grid gap-6 lg:grid-cols-2">
                <section>
                    <h2 class="text-lg font-semibold text-gray-900">Thành viên đã duyệt ({{ $club->approvedMembers->count() }})</h2>
                    <ul class="mt-3 divide-y divide-gray-100 rounded-lg border border-gray-200">
                        @forelse ($club->approvedMembers as $member)
                            <li class="flex flex-wrap justify-between gap-2 px-4 py-3">
                                <span class="font-medium text-gray-800">{{ $member->name }}</span>
                                <span class="text-sm text-gray-500">{{ $member->email }}</span>
                            </li>
                        @empty
                            <li class="px-4 py-6 text-center text-sm text-gray-500">Chưa có thành viên được duyệt.</li>
                        @endforelse
                    </ul>
                </section>

                <section>
                    <h2 class="text-lg font-semibold text-gray-900">Đơn đang chờ ({{ $club->pendingApplications->count() }})</h2>
                    <ul class="mt-3 divide-y divide-gray-100 rounded-lg border border-gray-200">
                        @forelse ($club->pendingApplications as $applicant)
                            <li class="flex flex-wrap justify-between gap-2 px-4 py-3">
                                <span class="font-medium text-gray-800">{{ $applicant->name }}</span>
                                <span class="text-sm text-gray-500">{{ $applicant->email }}</span>
                            </li>
                        @empty
                            <li class="px-4 py-6 text-center text-sm text-gray-500">Không có đơn chờ duyệt.</li>
                        @endforelse
                    </ul>
                </section>
            </div>
        </section>
    </div>
</x-app-layout>
