/**
 * httpclient.de — Service Worker
 *
 * Strategies:
 *  - Static assets (CSS, JS, fonts, images): Cache-first
 *  - Navigation requests: Network-first with offline fallback
 *  - Livewire/API requests: Network-only (no caching)
 */

const CACHE_VERSION = 'httpclient-v2';
const STATIC_CACHE = `${CACHE_VERSION}-static`;
const PAGES_CACHE = `${CACHE_VERSION}-pages`;

const OFFLINE_URL = '/app/offline';

const PRECACHE_ASSETS = [
    '/manifest.json',
    '/favicon.ico',
    OFFLINE_URL,
];

// ─── Install ────────────────────────────────────────────────────────
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then((cache) => cache.addAll(PRECACHE_ASSETS))
            .then(() => self.skipWaiting())
    );
});

// ─── Activate ───────────────────────────────────────────────────────
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) =>
                Promise.all(
                    keys
                        .filter((key) => key !== STATIC_CACHE && key !== PAGES_CACHE)
                        .map((key) => caches.delete(key))
                )
            )
            .then(() => self.clients.claim())
    );
});

// ─── Fetch ──────────────────────────────────────────────────────────
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET requests
    if (request.method !== 'GET') return;

    // Skip Livewire update requests (POST-based, but also internal polling)
    if (url.pathname.includes('livewire')) return;

    // Skip browser-sync / HMR in dev
    if (url.pathname.includes('hot') || url.pathname.includes('@vite')) return;

    // Navigation requests: Network-first with offline fallback
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    // Cache successful navigation responses
                    if (response.ok) {
                        const clone = response.clone();
                        caches.open(PAGES_CACHE).then((cache) => {
                            cache.put(request, clone);
                        });
                    }
                    return response;
                })
                .catch(() =>
                    caches.match(request)
                        .then((cached) => cached || caches.match(OFFLINE_URL))
                )
        );
        return;
    }

    // Static assets: Cache-first
    if (isStaticAsset(url)) {
        event.respondWith(
            caches.match(request).then((cached) => {
                if (cached) return cached;

                return fetch(request).then((response) => {
                    if (response.ok) {
                        const clone = response.clone();
                        caches.open(STATIC_CACHE).then((cache) => {
                            cache.put(request, clone);
                        });
                    }
                    return response;
                });
            })
        );
        return;
    }

    // Everything else: Network-only
    event.respondWith(fetch(request));
});

// ─── Helpers ────────────────────────────────────────────────────────
function isStaticAsset(url) {
    const staticExtensions = [
        '.css', '.js', '.woff', '.woff2', '.ttf', '.otf',
        '.png', '.jpg', '.jpeg', '.gif', '.svg', '.ico', '.webp',
        '.json',
    ];
    return staticExtensions.some((ext) => url.pathname.endsWith(ext));
}
