<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Volt;

Volt::route('/', 'home')->name('home');
Volt::route('/login', 'auth.login')->name('login')->middleware('guest');
Volt::route('/register', 'auth.register')->name('register')->middleware('guest');
Volt::route('/dashboard', 'dashboard')->name('dashboard')->middleware('auth');
Volt::route('/http-client', 'http-client')->name('http-client');

Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect('/');
})->name('logout');
