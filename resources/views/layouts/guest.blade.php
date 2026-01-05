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
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-primary-600 via-primary-700 to-primary-900 relative overflow-hidden">
            
            <!-- Decorative Elements -->
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
                <div class="absolute -top-[10%] -left-[10%] w-[40rem] h-[40rem] rounded-full bg-secondary-500/10 blur-3xl animate-pulse"></div>
                <div class="absolute bottom-[10%] -right-[10%] w-[30rem] h-[30rem] rounded-full bg-primary-400/20 blur-3xl animate-pulse" style="animation-delay: 2s"></div>
            </div>

            <div class="z-10 mb-6 transition-transform duration-300 hover:scale-105">
                <a href="/">
                    <x-application-logo class="w-24 h-24 fill-current text-white drop-shadow-lg" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-2 px-8 py-8 bg-white/95 backdrop-blur-md shadow-2xl overflow-hidden sm:rounded-2xl border border-white/20 relative z-10 transition-all duration-300">
                {{ $slot }}
            </div>
            
            <div class="mt-8 text-primary-100 text-sm z-10">
                &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}
            </div>
        </div>
    </body>
</html>
