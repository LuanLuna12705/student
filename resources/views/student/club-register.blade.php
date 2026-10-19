<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Đăng ký câu lạc bộ
        </h2>
    </x-slot>

    <div class="mx-auto max-w-3xl space-y-5 py-5 sm:px-6 lg:px-8">
        <section class="rounded-xl border border-blue-100 bg-white p-5 shadow-sm sm:p-7">
            <p class="text-sm font-semibold text-blue-700">PHENIKAA UNIVERSITY</p>
            <h1 class="mt-2 text-2xl font-bold text-gray-900">Gửi đơn đăng ký câu lạc bộ</h1>
            <p class="mt-2 text-sm text-gray-600">Đơn sẽ được gửi đến trưởng câu lạc bộ để xét duyệt. Bạn có thể theo dõi kết quả tại “Câu lạc bộ của tôi”.</p>
        </section>

        <form method="POST" action="{{ route('clubs.register.store') }}" class="space-y-5 rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-7">
            @csrf

            <div>
                <p class="text-sm font-medium text-gray-700">Sinh viên đăng ký</p>
                <p class="mt-1 text-sm text-gray-900">{{ Auth::user()->name }} — {{ Auth::user()->email }}</p>
            </div>

            <div>
                <label for="club_id" class="mb-1 block text-sm font-medium text-gray-800">Câu lạc bộ <span class="text-red-600">*</span></label>
                <select id="club_id" name="club_id" required class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-200">
                    <option value="">Chọn câu lạc bộ</option>
                    @foreach ($availableClubs as $club)
                        <option value="{{ $club->id }}" @selected(old('club_id') == $club->id)>{{ $club->name }}</option>
                    @endforeach
                </select>
                @error('club_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @if ($availableClubs->isEmpty())
                    <p class="mt-2 text-sm text-gray-500">Bạn đã đăng ký hoặc là thành viên của tất cả câu lạc bộ hiện có.</p>
                @endif
            </div>

            <div>
                <label for="phone" class="mb-1 block text-sm font-medium text-gray-800">Số điện thoại liên hệ <span class="text-red-600">*</span></label>
                <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" required maxlength="32" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-200">
                @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="facebook_url" class="mb-1 block text-sm font-medium text-gray-800">Link Facebook (không bắt buộc)</label>
                <input id="facebook_url" name="facebook_url" type="url" value="{{ old('facebook_url') }}" placeholder="https://facebook.com/..." class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-200">
                @error('facebook_url')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <fieldset>
                <legend class="text-sm font-medium text-gray-800">Giới tính <span class="text-red-600">*</span></legend>
                <div class="mt-2 flex flex-wrap gap-4">
                    @foreach (['Nam', 'Nữ', 'Khác'] as $gender)
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="radio" name="gender" value="{{ $gender }}" @checked(old('gender') === $gender) required class="border-gray-300 text-blue-700 focus:ring-blue-600">
                            {{ $gender }}
                        </label>
                    @endforeach
                </div>
                @error('gender')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </fieldset>

            <div>
                <label for="cohort" class="mb-1 block text-sm font-medium text-gray-800">Khóa đang theo học (không bắt buộc)</label>
                <select id="cohort" name="cohort" class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-200">
                    <option value="">Chọn khóa</option>
                    @foreach (['K16', 'K17', 'K18', 'K19', 'K20', 'Sinh viên không thuộc ĐH Phenikaa'] as $cohort)
                        <option value="{{ $cohort }}" @selected(old('cohort') === $cohort)>{{ $cohort }}</option>
                    @endforeach
                </select>
                @error('cohort')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-wrap justify-between gap-3 border-t border-gray-100 pt-5">
                <a href="{{ route('clubs.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Hủy</a>
                <button type="submit" @disabled($availableClubs->isEmpty()) class="rounded-lg bg-blue-700 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-800 disabled:cursor-not-allowed disabled:opacity-50">
                    Gửi đơn đăng ký
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
