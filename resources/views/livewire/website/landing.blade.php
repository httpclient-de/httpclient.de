<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Layout('layouts.website')]
#[Title('httpclient.de — The Modern HTTP Client for Developers')]
class extends Component
{
    public function mount()
    {
        if (class_exists('Native\Laravel\Facades\Window') || request()->header('X-NativePHP')) {
            return redirect()->route('app.home');
        }
    }
};
?>

<div>
    {{-- Hero Section --}}
    <section class="relative min-h-[90vh] flex items-center justify-center overflow-hidden">
        {{-- Background grid/glow effect --}}
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-[linear-gradient(rgba(59,130,246,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(59,130,246,0.03)_1px,transparent_1px)] bg-[size:64px_64px]"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-blue-500/10 rounded-full blur-[120px]"></div>
            <div class="absolute top-1/3 right-1/4 w-[300px] h-[300px] bg-indigo-500/5 rounded-full blur-[80px]"></div>
        </div>

        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            {{-- Badge --}}
            <div class="inline-flex items-center px-3 py-1 mb-8 text-xs font-medium text-blue-400 bg-blue-500/10 border border-blue-500/20 rounded-full">
                <span class="w-1.5 h-1.5 bg-blue-400 rounded-full mr-2 animate-pulse"></span>
                Open Source — Built with Laravel & Livewire
            </div>

            {{-- Headline --}}
            <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold tracking-tight">
                <span class="text-white">The Modern</span>
                <br>
                <span class="bg-gradient-to-r from-blue-400 via-blue-500 to-indigo-500 bg-clip-text text-transparent">HTTP Client</span>
            </h1>

            <p class="mt-6 text-lg sm:text-xl text-gray-400 max-w-2xl mx-auto leading-relaxed">
                Build, test, and debug your API requests with a lightning-fast, offline-ready developer tool. No bloat. No tracking. Just you and your APIs.
            </p>

            {{-- CTAs --}}
            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ appUrl('/') }}" class="group inline-flex items-center px-8 py-4 text-base font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-500 transition-all duration-300 shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 hover:-translate-y-0.5">
                    Launch App
                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
                <a href="https://github.com/httpclient-de/httpclient.de" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center px-8 py-4 text-base font-semibold text-gray-300 bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 hover:border-white/20 transition-all duration-300 hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                    </svg>
                    Star on GitHub
                </a>
            </div>

            {{-- Mock browser preview --}}
            <div class="mt-16 mx-auto max-w-4xl">
                <div class="rounded-xl border border-white/10 bg-white/5 backdrop-blur-sm overflow-hidden shadow-2xl shadow-black/50">
                    {{-- Browser chrome --}}
                    <div class="flex items-center px-4 py-3 bg-white/5 border-b border-white/5">
                        <div class="flex space-x-2">
                            <div class="w-3 h-3 rounded-full bg-red-500/60"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-500/60"></div>
                            <div class="w-3 h-3 rounded-full bg-green-500/60"></div>
                        </div>
                        <div class="flex-1 mx-4">
                            <div class="flex items-center bg-black/30 rounded-md px-3 py-1.5 text-xs text-gray-500 font-mono">
                                <svg class="w-3 h-3 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                                app.httpclient.de
                            </div>
                        </div>
                    </div>
                    {{-- App preview --}}
                    <div class="p-6 bg-gray-900/50">
                        <div class="flex gap-4">
                            {{-- Method select + URL bar --}}
                            <div class="flex-1 flex border border-white/10 rounded-lg overflow-hidden">
                                <div class="px-4 py-2.5 bg-white/5 text-green-400 text-sm font-bold border-r border-white/10">GET</div>
                                <div class="flex-1 px-4 py-2.5 text-gray-400 text-sm font-mono">https://api.example.com/v1/users</div>
                            </div>
                            <div class="px-6 py-2.5 bg-blue-600 rounded-lg text-white text-sm font-bold">SEND</div>
                        </div>
                        {{-- Response preview --}}
                        <div class="mt-4 rounded-lg bg-black/40 border border-white/5 p-4">
                            <div class="flex items-center space-x-4 text-xs font-medium mb-3">
                                <span class="text-green-400">STATUS: 200</span>
                                <span class="text-gray-500">TIME: 42ms</span>
                                <span class="text-gray-500">SIZE: 1.24KB</span>
                            </div>
                            <pre class="text-xs text-green-400/80 font-mono leading-relaxed"><code>{
  "data": [
    { "id": 1, "name": "Alice", "role": "admin" },
    { "id": 2, "name": "Bob", "role": "developer" }
  ],
  "meta": { "total": 2, "page": 1 }
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features Section --}}
    <section class="py-24 relative">
        <div class="absolute inset-0">
            <div class="absolute bottom-0 left-1/4 w-[400px] h-[400px] bg-blue-500/5 rounded-full blur-[100px]"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-white">Built for Developers</h2>
                <p class="mt-4 text-gray-400 text-lg max-w-2xl mx-auto">Everything you need to work with APIs, nothing you don't.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Feature 1 --}}
                <div class="group p-8 rounded-2xl bg-white/[0.02] border border-white/5 hover:border-white/10 hover:bg-white/[0.04] transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500/20 to-orange-500/20 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Fast & Lightweight</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">Lightning-fast response times and a clean, focused interface. No electron bloat — runs in your browser.</p>
                </div>

                {{-- Feature 2 --}}
                <div class="group p-8 rounded-2xl bg-white/[0.02] border border-white/5 hover:border-white/10 hover:bg-white/[0.04] transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500/20 to-indigo-500/20 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Collaborative</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">Share collections and environments with your team seamlessly. Real-time sync keeps everyone on the same page.</p>
                </div>

                {{-- Feature 3 --}}
                <div class="group p-8 rounded-2xl bg-white/[0.02] border border-white/5 hover:border-white/10 hover:bg-white/[0.04] transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Offline Ready</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">PWA support means you can work even when disconnected. Requests queue and sync automatically when you're back online.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Open Source CTA --}}
    <section class="py-24 relative">
        <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="p-12 rounded-2xl bg-gradient-to-br from-blue-500/10 to-indigo-500/10 border border-blue-500/20">
                <svg class="w-10 h-10 text-blue-400 mx-auto mb-6" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                </svg>
                <h2 class="text-2xl sm:text-3xl font-bold text-white mb-4">Open Source & Free Forever</h2>
                <p class="text-gray-400 mb-8 max-w-lg mx-auto">httpclient.de is fully open source. Star us on GitHub, contribute, or fork it for your own use.</p>
                <a href="https://github.com/httpclient-de/httpclient.de" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center px-6 py-3 text-sm font-semibold text-white bg-white/10 border border-white/20 rounded-xl hover:bg-white/15 transition-all duration-300">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                    </svg>
                    Star on GitHub
                </a>
            </div>
        </div>
    </section>
</div>
