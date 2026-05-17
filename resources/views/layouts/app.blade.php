<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="httpclient.de — API testing tool for developers.">

        <title>{{ $title ?? config('app.name', 'httpclient.de') }}</title>

        <!-- PWA -->
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#0a0a0f">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&family=jetbrains-mono:400,500" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900">
        <div class="min-h-screen flex flex-col">
            <nav class="bg-white border-b border-gray-200 shrink-0">
                <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-14">
                        <div class="flex items-center">
                            <!-- Logo -->
                            <a href="{{ appUrl('/') }}" class="shrink-0 flex items-center space-x-2">
                                <div class="w-7 h-7 bg-gradient-to-br from-blue-500 to-blue-700 rounded-lg flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                                <span class="font-bold text-base text-gray-900 tracking-tight">httpclient<span class="text-blue-600">.de</span></span>
                            </a>

                            <!-- Navigation Links -->
                            <div class="hidden sm:flex items-center ml-8 space-x-1">
                                <a href="{{ appUrl('/') }}"
                                   class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium {{ request()->is('app') ? 'bg-blue-50 text-blue-700' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }} transition duration-150">
                                    Client
                                </a>
                                @auth
                                    <a href="{{ appUrl('/dashboard') }}"
                                       class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium {{ request()->is('app/dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }} transition duration-150">
                                        Dashboard
                                    </a>
                                @endauth
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            <!-- Offline indicator -->
                            <div id="offline-indicator" class="hidden items-center px-2.5 py-1 text-xs font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-md">
                                <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                Offline
                            </div>

                            <!-- Star on GitHub -->
                            <a href="https://github.com/httpclient-de/httpclient.de" target="_blank" rel="noopener noreferrer"
                               class="hidden sm:inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-200 rounded-md hover:bg-gray-50 transition duration-150">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                                </svg>
                                Star
                            </a>

                            <!-- Back to website -->
                            <a href="{{ websiteUrl('/') }}" class="hidden sm:inline-flex items-center text-xs text-gray-400 hover:text-gray-600 transition duration-150">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Website
                            </a>

                            @auth
                                <div class="flex items-center space-x-2 pl-3 border-l border-gray-200">
                                    <span class="text-xs text-gray-500 font-medium">{{ auth()->user()->name }}</span>
                                    <form method="POST" action="{{ appUrl('/logout') }}">
                                        @csrf
                                        <button type="submit" class="text-xs text-gray-400 hover:text-gray-600 transition duration-150">Logout</button>
                                    </form>
                                </div>
                            @else
                                <div class="flex items-center space-x-2 pl-3 border-l border-gray-200">
                                    <a href="{{ appUrl('/login') }}" class="text-xs text-gray-500 hover:text-gray-700 transition duration-150">Login</a>
                                    <a href="{{ appUrl('/register') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition duration-150">Register</a>
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main class="flex-1">
                {{ $slot }}
            </main>
        </div>

        <!-- Service Worker -->
        <script>
            // Register service worker
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js')
                        .then(reg => console.log('SW registered:', reg.scope))
                        .catch(err => console.warn('SW registration failed:', err));
                });
            }

            // Offline indicator
            const offlineEl = document.getElementById('offline-indicator');
            function updateOnlineStatus() {
                if (offlineEl) {
                    offlineEl.style.display = navigator.onLine ? 'none' : 'flex';
                }
            }
            window.addEventListener('online', updateOnlineStatus);
            window.addEventListener('offline', updateOnlineStatus);
            updateOnlineStatus();
        </script>
    </body>
</html>
