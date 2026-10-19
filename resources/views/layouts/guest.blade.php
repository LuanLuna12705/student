<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-cover bg-center bg-no-repeat relative" style="background-image: url('{{ asset('images/background1.jpg') }}');">
            <div class="absolute inset-0 bg-slate-900/45"></div>

            <div class="relative z-10 flex flex-col items-center w-full">
                <div>
                    <a href="/" class="inline-flex items-center">
                        <img src="{{ asset('images/logo.png') }}"
                             alt="Logo Phenikaa University"
                             class="h-24 w-36 object-contain rounded-lg bg-white/80 p-2 shadow-lg">
                    </a>
                </div>

                <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white/85 backdrop-blur-sm shadow-xl border border-white/30 overflow-hidden sm:rounded-2xl">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
