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
        if (app()->runningInConsole()) {
            return;
        }

        // Only attempt to open a window if we are actually running in NativePHP
        if (! env('NATIVEPHP_RUNNING')) {
            return;
        }

        // Guard against the classes not being available if the package installation failed
        if (class_exists('Native\Laravel\Facades\Window')) {
            \Native\Laravel\Facades\Window::open()
                ->width(1200)
                ->height(800)
                ->showDevTools(false)
                ->rememberState()
                ->url(route('app.home'));
        }
    }
}
