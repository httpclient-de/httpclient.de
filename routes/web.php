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

    // Auth routes (guests only)
    Volt::route('/login', 'auth.login')->name('login')->middleware('guest');
    Volt::route('/register', 'auth.register')->name('register')->middleware('guest');

    // Protected routes
    Volt::route('/dashboard', 'dashboard')->name('dashboard')->middleware('auth');

    // Logout
    Route::post('/logout', function () {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect(appUrl('/'));
    })->name('logout');

    // Offline fallback (for service worker)
    Route::get('/offline', function () {
        return view('app.offline');
    })->name('app.offline');
});
