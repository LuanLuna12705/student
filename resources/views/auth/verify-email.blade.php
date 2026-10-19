<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Cảm ơn bạn đã đăng ký! Trước khi bắt đầu, bạn vui lòng xác thực địa chỉ email của mình bằng cách nhấp vào đường dẫn mà chúng tôi vừa gửi qua email nhé. Nếu bạn không nhận được email, chúng tôi rất sẵn lòng gửi lại cho bạn một email khác..') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('Một đường dẫn xác thực mới đã được gửi đến địa chỉ email mà bạn đã cung cấp trong hồ sơ của mình.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Gửi lại email xác thực') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
