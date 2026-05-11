# Roadmap: httpclient.de

**Project:** httpclient.de
**Developed by:** MTEX.dev, XPSYSTEMS.eu/.de, ternis-edv.de

## Milestone 1: Foundation (COMPLETED)
- [x] Initialize Laravel 13 application.
- [x] Configure global UUIDs and SoftDeletes for User model.
- [x] Install and configure Livewire (Volt-style).
- [x] Integrate Tailwind CSS 4.
- [x] Implement Custom Authentication (Login, Register) without starter kits.
- [x] Support for Guest Users (initial session logic).
- [x] Basic Layout and Navigation.
- [x] Initialize Git repository with initial commits.

## Milestone 2: Identity & Multi-tenancy (IN PROGRESS)
- [ ] Implement `Organization` models and multi-tenancy structure.
- [ ] Setup `spatie/laravel-activitylog` and `owen-it/laravel-auditing` on models.
- [ ] Integrate Laravel Socialite for Google & GitHub (manual implementation).
- [ ] Scaffold MTEX.dev OAuth provider.

## Milestone 3: The HTTP Client Engine
- [ ] Build the proxy backend service to execute requests (Guzzle).
- [ ] Create models for saving Requests, Collections, and Environments.
- [ ] Develop the user interface for building and testing API requests using Livewire.

## Milestone 4: PWA & Offline Support
- [ ] Integrate Service Worker (`sw.js`) and Web App Manifest.
- [ ] Ensure offline availability of the application shell.
- [ ] Implement offline request queuing (sync when online).

## Milestone 5: Polish & Launch
- [ ] Comprehensive Testing (Unit & Feature).
- [ ] UI/UX refinements with Tailwind CSS.
- [ ] Beta release for MTEX.dev and XPSYSTEMS users.
