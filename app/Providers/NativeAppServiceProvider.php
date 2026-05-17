<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class NativeAppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Guard against the classes not being available if the package installation failed
        if (class_exists('Native\Laravel\Facades\Window')) {
            \Native\Laravel\Facades\Window::open()
                ->width(1200)
                ->height(800)
                ->showDevTools(false)
                ->url(route('app.home'));
        }
    }
}
