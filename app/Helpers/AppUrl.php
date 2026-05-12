<?php

if (!function_exists('appUrl')) {
    /**
     * Generate a URL for the app section.
     *
     * In development: returns /app{$path}
     * In production (app.httpclient.de): returns {$path} directly since the subdomain handles it.
     */
    function appUrl(string $path = '/'): string
    {
        $path = '/' . ltrim($path, '/');

        if (app()->environment('production') && config('app.app_subdomain')) {
            $scheme = request()->getScheme();
            $subdomain = config('app.app_subdomain');
            $domain = config('app.domain');
            return "{$scheme}://{$subdomain}.{$domain}{$path}";
        }

        // Dev: prefix with /app
        return '/app' . ($path === '/' ? '' : $path);
    }
}

if (!function_exists('websiteUrl')) {
    /**
     * Generate a URL for the website section.
     *
     * In development: returns {$path} directly.
     * In production: returns https://httpclient.de{$path}
     */
    function websiteUrl(string $path = '/'): string
    {
        $path = '/' . ltrim($path, '/');

        if (app()->environment('production') && config('app.domain')) {
            $scheme = request()->getScheme();
            $domain = config('app.domain');
            return "{$scheme}://{$domain}{$path}";
        }

        return $path;
    }
}
