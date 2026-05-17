<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="httpclient.de — The modern, open-source HTTP client for developers. Build, test, and share API requests with ease.">

        <title>{{ $title ?? config('app.name', 'httpclient.de') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&family=jetbrains-mono:400,500" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-[#0a0a0f] text-gray-100">
        <!-- Navigation -->
        <nav class="fixed top-0 w-full z-50 border-b border-white/5 bg-[#0a0a0f]/80 backdrop-blur-xl">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center space-x-8">
                        <!-- Logo -->
                        <a href="/" class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-700 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <span class="font-bold text-lg text-white tracking-tight">httpclient<span class="text-blue-400">.de</span></span>
                        </a>

                        <!-- Nav Links -->
                        <div class="hidden sm:flex items-center space-x-6">
                            <a href="{{ route('website.about') }}" class="text-sm text-gray-400 hover:text-white transition-colors duration-200">About</a>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Star on GitHub -->
                        <a href="https://github.com/httpclient-de/httpclient.de" target="_blank" rel="noopener noreferrer"
                           class="hidden sm:inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-300 bg-white/5 border border-white/10 rounded-lg hover:bg-white/10 hover:border-white/20 transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                            </svg>
                            Star on GitHub
                        </a>
                        <!-- Open App CTA -->
                        <a href="{{ appUrl('/') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-500 transition-colors duration-200 shadow-lg shadow-blue-500/20">
                            Open App
                            <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="pt-16">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="border-t border-white/5 bg-[#0a0a0f]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                    <div class="flex items-center space-x-2">
                        <div class="w-6 h-6 bg-gradient-to-br from-blue-500 to-blue-700 rounded-md flex items-center justify-center">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <span class="text-sm text-gray-500">httpclient.de</span>
                    </div>
                    <div class="flex items-center space-x-6 text-xs text-gray-600">
                        <span>Developed by</span>
                        <a href="https://mtex.dev" target="_blank" rel="noopener" class="hover:text-gray-400 transition-colors">MTEX.dev</a>
                        <a href="https://xpsystems.eu" target="_blank" rel="noopener" class="hover:text-gray-400 transition-colors">XPSYSTEMS</a>
                        <a href="https://ternis-edv.de" target="_blank" rel="noopener" class="hover:text-gray-400 transition-colors">ternis-edv.de</a>
                    </div>
                    <div class="flex items-center space-x-4 text-xs text-gray-600">
                        <a href="https://github.com/httpclient-de/httpclient.de" target="_blank" rel="noopener" class="hover:text-gray-400 transition-colors">GitHub</a>
                        <span>&copy; {{ date('Y') }}</span>
                    </div>
                </div>
            </div>
        </footer>
    </body>
</html>
