<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    //
};
?>

<div class="text-center">
    <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
        The Modern <span class="text-blue-600">HTTP Client</span>
    </h1>
    <p class="mt-3 max-w-md mx-auto text-base text-gray-500 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
        Build, test, and share your API requests with ease. Designed for developers who love simplicity and speed.
    </p>
    <div class="mt-10 flex justify-center space-x-6">
        @guest
            <a href="/register" class="px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 md:py-4 md:text-lg md:px-10">
                Get Started
            </a>
            <a href="/login" class="px-8 py-3 border border-transparent text-base font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 md:py-4 md:text-lg md:px-10">
                Sign In
            </a>
        @else
            <a href="/dashboard" class="px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 md:py-4 md:text-lg md:px-10">
                Go to Dashboard
            </a>
        @endguest
    </div>

    <div class="mt-20 grid grid-cols-1 gap-8 md:grid-cols-3">
        <div class="p-6 bg-white rounded-lg shadow-sm">
            <div class="text-blue-600 mb-4">
                <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <h3 class="text-lg font-bold">Fast & Lightweight</h3>
            <p class="mt-2 text-gray-600 text-sm">Lightning fast response times and a clean, focused interface.</p>
        </div>
        <div class="p-6 bg-white rounded-lg shadow-sm">
            <div class="text-blue-600 mb-4">
                <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <h3 class="text-lg font-bold">Collaborative</h3>
            <p class="mt-2 text-gray-600 text-sm">Share collections and environments with your team seamlessly.</p>
        </div>
        <div class="p-6 bg-white rounded-lg shadow-sm">
            <div class="text-blue-600 mb-4">
                <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path></svg>
            </div>
            <h3 class="text-lg font-bold">Offline Ready</h3>
            <p class="mt-2 text-gray-600 text-sm">PWA support means you can work even when you're disconnected.</p>
        </div>
    </div>
</div>
