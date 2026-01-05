<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'EPI-OSS') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
             <!-- Tailwind CDN for fallback if vite not built, but we assume vite is used -->
             <script src="https://cdn.tailwindcss.com"></script>
        @endif
    </head>
    <body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 flex flex-col min-h-screen">
        
        <!-- Navigation -->
        <header class="w-full bg-white dark:bg-gray-800 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex items-center">
                        <a href="/" class="text-xl font-bold text-primary-600">
                            EPI-OSS
                        </a>
                    </div>
                    
                    @if (Route::has('login'))
                        <nav class="flex items-center gap-4">
                            @auth
                                <a
                                    href="{{ url('/dashboard') }}"
                                    class="text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
                                >
                                    {{ __('Dashboard') }}
                                </a>
                            @else
                                <a
                                    href="{{ route('login') }}"
                                    class="text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
                                >
                                    {{ __('Log in') }}
                                </a>

                                @if (Route::has('register'))
                                    <a
                                        href="{{ route('register') }}"
                                        class="text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
                                    >
                                        {{ __('Register') }}
                                    </a>
                                @endif
                            @endauth
                        </nav>
                    @endif
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow flex items-center justify-center p-6">
            <div class="max-w-3xl w-full text-center space-y-8">
                
                <!-- Hero Section -->
                <div class="space-y-4">
                    <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                        {{ __('Welcome Back') }} <span class="text-primary-600">EPI-OSS</span>
                    </h1>
                    <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                        {{ __('Laravel has an incredibly rich ecosystem.') }}
                        {{ __('We suggest starting with the following.') }}
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 md:py-4 md:text-lg md:px-10 transition duration-150 ease-in-out shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            {{ __('Dashboard') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 md:py-4 md:text-lg md:px-10 transition duration-150 ease-in-out shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            {{ __('Log in') }}
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-primary-700 bg-primary-100 hover:bg-primary-200 md:py-4 md:text-lg md:px-10 transition duration-150 ease-in-out">
                                {{ __('Register') }}
                            </a>
                        @endif
                    @endauth
                </div>

                <!-- Features Grid (Optional / Placeholder) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12 text-left">
                    <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center text-primary-600 mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                        <h3 class="font-semibold text-lg mb-2">{{ __('Orders') }}</h3>
                        <p class="text-gray-500 text-sm">{{ __('Join us and start managing your orders') }}</p>
                    </div>
                    <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="w-10 h-10 bg-secondary-100 rounded-lg flex items-center justify-center text-secondary-600 mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <h3 class="font-semibold text-lg mb-2">{{ __('Distributors') }}</h3>
                        <p class="text-gray-500 text-sm">{{ __('Official Distributor') }}</p>
                    </div>
                    <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center text-green-600 mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="font-semibold text-lg mb-2">{{ __('Administration') }}</h3>
                        <p class="text-gray-500 text-sm">{{ __('Central Price List') }}</p>
                    </div>
                </div>

            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <p class="text-center text-sm text-gray-500">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}
                </p>
            </div>
        </footer>
    </body>
</html>
