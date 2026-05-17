# httpclient.de

A fully functional, standalone HTTP client built with Laravel 13, Livewire (Volt), and NativePHP.

## Features

- **No Auth Required**: Standalone application designed for local and web use without mandatory authentication.
- **NativePHP Integration**: Runs as a native desktop application using Electron.
- **Livewire Volt**: Modern, single-file components for a reactive UI.
- **UUIDs**: All database records use UUIDs for primary keys.
- **Activity Logging**: Comprehensive change tracking using Spatie ActivityLog.
- **Landing Page**: Integrated marketing landing page for web users (automatically bypassed in NativePHP).

## Requirements

- PHP 8.3+
- Node.js & NPM
- SQLite (default)

## Installation

```bash
composer install
npm install
npm run build
php artisan migrate
```

## Running the App

### Web
```bash
php artisan serve
```

### Desktop (NativePHP)
```bash
php artisan native:serve
```

## Architecture

- **Routes**: `/` is the landing page for web. `/app` is the main HTTP client.
- **Models**: Located in `app/Models`, using `HasUuids` and `LogsActivity`.
- **UI**: Built with Tailwind CSS and Livewire Volt.
