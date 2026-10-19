@props([
    'name',
    'title',
    'submitText' => 'Xác nhận',
    'submitClass' => 'bg-blue-600 hover:bg-blue-500',
    'form' => null,
    'submitType' => 'button',
])

<x-modal name="{{ $name }}" :show="false" focusable>
    <div class="p-6">
        <h2 class="text-lg font-semibold text-gray-900">{{ $title }}</h2>

        <div class="mt-3 text-sm text-gray-600">
            {{ $slot }}
        </div>

        <div class="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-4">
            <button type="button" x-on:click="$dispatch('close-modal', '{{ $name }}')" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50">
                Hủy
            </button>

            @if ($form)
                <button type="submit" form="{{ $form }}" x-on:click="$dispatch('close-modal', '{{ $name }}')" class="rounded-lg {{ $submitClass }} px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition">
                    {{ $submitText }}
                </button>
            @else
                <button type="{{ $submitType }}" class="rounded-lg {{ $submitClass }} px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition">
                    {{ $submitText }}
                </button>
            @endif
        </div>
    </div>
</x-modal>
