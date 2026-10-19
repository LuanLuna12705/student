<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $title }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <section class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            <div class="flex items-center gap-4">
                <div class="text-4xl" aria-hidden="true">{{ $icon }}</div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">{{ $title }}</h1>
                    <p class="mt-1 text-sm text-gray-600">{{ $description }}</p>
                </div>
            </div>

            <p class="mt-8 border-t border-gray-100 pt-6 text-sm text-gray-500">
                Nội dung mục này đang được cập nhật.
            </p>
        </section>
    </div>
</x-app-layout>