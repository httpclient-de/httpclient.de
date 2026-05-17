<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Volt;

/*
|--------------------------------------------------------------------------
| Website Routes (httpclient.de / www.httpclient.de)
|--------------------------------------------------------------------------
|
| Public-facing marketing website. No auth required.
|
*/

Volt::route('/', 'website.landing')->name('website.home');
Volt::route('/about', 'website.about')->name('website.about');

/*
|--------------------------------------------------------------------------
| App Routes (app.httpclient.de / /app in dev)
|--------------------------------------------------------------------------
|
| The PWA HTTP client application. Auth routes + protected routes.
|
*/

Route::prefix('app')->group(function () {
    // App home — the HTTP client
    Volt::route('/', 'http-client')->name('app.home');

    // Dashboard
    Volt::route('/dashboard', 'dashboard')->name('dashboard');

    // Offline fallback (for service worker)
    Route::get('/offline', function () {
        return view('app.offline');
    })->name('app.offline');
});
