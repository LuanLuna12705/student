<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="flex items-center gap-4 rounded-xl border border-gray-200 bg-gray-50 p-4">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="h-16 w-16 rounded-full object-cover ring-2 ring-blue-200">
            <div>
                <p class="text-sm text-gray-500">Ảnh đại diện</p>
                <p class="text-base font-semibold text-gray-900">{{ $user->name }}</p>
            </div>
        </div>

        <div>
            <x-input-label for="avatar" :value="__('Avatar')" />
            <input id="avatar" name="avatar" type="file" accept="image/*" class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200" />
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="student_code" value="Mã sinh viên" />
            <x-text-input id="student_code" name="student_code" type="text" class="mt-1 block w-full" :value="old('student_code', $user->student_code)" maxlength="32" />
            <x-input-error class="mt-2" :messages="$errors->get('student_code')" />
        </div>

        <div>
            <x-input-label for="major" value="Ngành" />
            <x-text-input id="major" name="major" type="text" class="mt-1 block w-full" :value="old('major', $user->major)" maxlength="255" />
            <x-input-error class="mt-2" :messages="$errors->get('major')" />
        </div>

        <div>
            <x-input-label for="class_name" value="Lớp" />
            <x-text-input id="class_name" name="class_name" type="text" class="mt-1 block w-full" :value="old('class_name', $user->class_name)" maxlength="255" />
            <x-input-error class="mt-2" :messages="$errors->get('class_name')" />
        </div>

        <div>
            <x-input-label for="date_of_birth" value="Ngày sinh" />
            <x-text-input id="date_of_birth" name="date_of_birth" type="date" class="mt-1 block w-full" :value="old('date_of_birth', $user->date_of_birth?->format('Y-m-d'))" />
            <x-input-error class="mt-2" :messages="$errors->get('date_of_birth')" />
        </div>

        <div>
            <x-input-label for="gender" value="Giới tính" />
            <select id="gender" name="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Chọn giới tính</option>
                @foreach (['Nam', 'Nữ', 'Khác'] as $gender)
                    <option value="{{ $gender }}" @selected(old('gender', $user->gender) === $gender)>{{ $gender }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('gender')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
