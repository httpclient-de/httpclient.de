<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Layout('layouts.website')]
#[Title('About — httpclient.de')]
class extends Component
{
    //
};
?>

<div>
    {{-- Hero --}}
    <section class="pt-24 pb-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl sm:text-5xl font-bold text-white tracking-tight">
                About <span class="text-blue-400">httpclient.de</span>
            </h1>
            <p class="mt-6 text-lg text-gray-400 max-w-2xl mx-auto leading-relaxed">
                A modern, open-source HTTP client built by developers, for developers.
            </p>
        </div>
    </section>

    {{-- Content --}}
    <section class="pb-24">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
            {{-- Mission --}}
            <div>
                <h2 class="text-2xl font-bold text-white mb-4">Our Mission</h2>
                <div class="prose prose-invert prose-gray max-w-none">
                    <p class="text-gray-400 leading-relaxed">
                        httpclient.de is a free, open-source API testing tool that runs in your browser.
                        No downloads, no subscriptions, no tracking. We believe developer tools should be
                        fast, private, and accessible to everyone.
                    </p>
                    <p class="text-gray-400 leading-relaxed mt-4">
                        Built with Laravel and Livewire, httpclient.de is a progressive web app (PWA) that
                        works offline and can be installed on any device. It's the HTTP client you'd build
                        for yourself — because we did.
                    </p>
                </div>
            </div>

            {{-- Tech Stack --}}
            <div>
                <h2 class="text-2xl font-bold text-white mb-6">Tech Stack</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    @foreach([
                        ['Laravel 13', 'Backend framework'],
                        ['Livewire & Volt', 'Reactive components'],
                        ['Tailwind CSS 4', 'Utility-first styling'],
                        ['SQLite / PostgreSQL', 'Database'],
                        ['Service Worker', 'Offline support'],
                        ['Guzzle HTTP', 'Proxy engine'],
                    ] as [$tech, $desc])
                        <div class="p-4 rounded-xl bg-white/[0.02] border border-white/5">
                            <div class="text-sm font-semibold text-white">{{ $tech }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $desc }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Team --}}
            <div>
                <h2 class="text-2xl font-bold text-white mb-6">Developed By</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    @foreach([
                        ['MTEX.dev', 'https://mtex.dev', 'Core development & architecture'],
                        ['XPSYSTEMS', 'https://xpsystems.eu', 'Infrastructure & deployment'],
                        ['ternis-edv.de', 'https://ternis-edv.de', 'Design & quality assurance'],
                    ] as [$name, $url, $role])
                        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                           class="group p-6 rounded-xl bg-white/[0.02] border border-white/5 hover:border-white/15 transition-all duration-300">
                            <div class="text-base font-bold text-white group-hover:text-blue-400 transition-colors">{{ $name }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $role }}</div>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- CTA --}}
            <div class="text-center pt-8">
                <a href="{{ appUrl('/') }}" class="inline-flex items-center px-8 py-4 text-base font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-500 transition-all duration-300 shadow-lg shadow-blue-500/25">
                    Try httpclient.de
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>
</div>
