<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Câu lạc bộ của tôi
        </h2>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6 py-6 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <section class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm sm:p-8">
            <div class="flex flex-wrap items-start justify-between gap-5">
                <div>
                    <p class="text-sm font-semibold text-blue-700">CÂU LẠC BỘ SINH VIÊN</p>
                    <h1 class="mt-2 text-2xl font-bold text-gray-900">
                        {{ $isClubLeader ? 'Quản lý câu lạc bộ của tôi' : 'Câu lạc bộ của tôi' }}
                    </h1>
                    <p class="mt-2 max-w-2xl text-sm text-gray-600">
                        {{ $isClubLeader ? 'Xem thành viên và xét duyệt đơn đăng ký vào câu lạc bộ bạn phụ trách.' : 'Theo dõi trạng thái các đơn đăng ký câu lạc bộ của bạn.' }}
                    </p>
                </div>
                @unless ($isClubLeader)
                    <a href="{{ route('clubs.register') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-800">
                        <span aria-hidden="true">+</span>
                        Đăng ký câu lạc bộ
                    </a>
                @endunless
            </div>
        </section>

        @if ($isClubLeader)
            @forelse ($managedClubs as $club)
                <section class="space-y-6 rounded-xl border border-gray-100 bg-white p-6 shadow-sm sm:p-8">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 pb-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">{{ $club->name }}</h2>
                            @if ($club->description)
                                <p class="mt-1 text-sm text-gray-600">{{ $club->description }}</p>
                            @endif
                        </div>
                        <span class="rounded-full bg-blue-50 px-3 py-1 text-sm font-semibold text-blue-700">
                            {{ $club->approved_members_count }} thành viên
                        </span>
                    </div>

                    <div class="grid gap-6 lg:grid-cols-2">
                        <section>
                            <h3 class="text-lg font-semibold text-gray-900">Đơn chờ duyệt ({{ $club->pendingApplications->count() }})</h3>
                            <ul class="mt-3 divide-y divide-gray-100 rounded-lg border border-gray-200">
                                @forelse ($club->pendingApplications as $applicant)
                                    <li class="space-y-3 p-4">
                                        <div class="flex flex-wrap items-start justify-between gap-3">
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ $applicant->name }}</p>
                                                <p class="text-sm text-gray-500">{{ $applicant->email }}</p>
                                            </div>
                                            <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">Chờ duyệt</span>
                                        </div>
                                        <dl class="grid gap-2 text-sm text-gray-600 sm:grid-cols-2">
                                            <div><dt class="inline font-medium">Điện thoại: </dt><dd class="inline">{{ $applicant->pivot->phone }}</dd></div>
                                            <div><dt class="inline font-medium">Giới tính: </dt><dd class="inline">{{ $applicant->pivot->gender }}</dd></div>
                                            @if ($applicant->pivot->cohort)
                                                <div><dt class="inline font-medium">Khóa: </dt><dd class="inline">{{ $applicant->pivot->cohort }}</dd></div>
                                            @endif
                                            @if ($applicant->pivot->facebook_url)
                                                <div class="break-all"><dt class="inline font-medium">Facebook: </dt><dd class="inline">{{ $applicant->pivot->facebook_url }}</dd></div>
                                            @endif
                                        </dl>
                                        <div class="flex gap-2">
                                            <form method="POST" action="{{ route('clubs.applications.approve', [$club, $applicant]) }}">
                                                @csrf
                                                <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700">Duyệt</button>
                                            </form>
                                            <form method="POST" action="{{ route('clubs.applications.reject', [$club, $applicant]) }}">
                                                @csrf
                                                <button type="submit" class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100">Từ chối</button>
                                            </form>
                                        </div>
                                    </li>
                                @empty
                                    <li class="px-4 py-6 text-center text-sm text-gray-500">Chưa có đơn đăng ký mới.</li>
                                @endforelse
                            </ul>
                        </section>

                        <section>
                            <h3 class="text-lg font-semibold text-gray-900">Thành viên ({{ $club->approved_members_count }})</h3>
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
                    </div>
                </section>
            @empty
                <section class="rounded-xl border border-dashed border-gray-300 bg-white px-5 py-10 text-center text-sm text-gray-500">
                    Bạn chưa được phân công phụ trách câu lạc bộ nào. Vui lòng liên hệ quản trị viên.
                </section>
            @endforelse
        @else
            <section class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm sm:p-8">
                <h2 class="text-lg font-semibold text-gray-900">Đơn đăng ký của bạn</h2>
                <ul class="mt-4 divide-y divide-gray-100 rounded-lg border border-gray-200">
                    @forelse ($clubs as $club)
                        <li class="flex flex-wrap items-center justify-between gap-3 px-4 py-4">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $club->name }}</p>
                                <p class="mt-1 text-sm text-gray-500">Trưởng CLB: {{ $club->leader?->name ?? 'Chưa phân công' }}</p>
                            </div>
                            @switch($club->pivot->status)
                                @case('approved')
                                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Đã duyệt</span>
                                    @break
                                @case('rejected')
                                    <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">Đã từ chối</span>
                                    @break
                                @default
                                    <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">Chờ duyệt</span>
                            @endswitch
                        </li>
                    @empty
                        <li class="px-4 py-8 text-center text-sm text-gray-500">
                            Bạn chưa gửi đơn vào câu lạc bộ nào.
                            <a href="{{ route('clubs.register') }}" class="font-semibold text-blue-700 underline">Xem danh sách và đăng ký</a>
                        </li>
                    @endforelse
                </ul>
            </section>
        @endif
    </div>
</x-app-layout>
