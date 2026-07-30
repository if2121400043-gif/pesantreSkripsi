// ============================================================
// Service Worker — PP Nurul Furqon
// Version: v6 (network-first, no aggressive caching)
// ============================================================

const CACHE_VERSION = 'v8';
const CACHE_NAME_STATIC = `pp-nurul-furqon-static-${CACHE_VERSION}`;
const CACHE_NAME_DYNAMIC = `pp-nurul-furqon-dynamic-${CACHE_VERSION}`;

// Core files to pre-cache for offline fallback
const PRECACHE_ASSETS = [
    '/offline',
    '/manifest.json',
    '/favicon.ico',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
    '/icons/icon-192x192-maskable.png',
    '/icons/icon-512x512-maskable.png',
    '/icons/apple-touch-icon.png',
    '/images/logo-pesantren.webp'
];

// ── Install: Pre-cache core offline assets ──
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME_STATIC).then(cache => {
            console.log('[SW] Pre-caching offline assets...');
            return Promise.all(
                PRECACHE_ASSETS.map(asset =>
                    cache.add(asset).catch(err => {
                        console.warn('[SW] Failed to cache:', asset, err);
                    })
                )
            );
        }).then(() => self.skipWaiting()) // Immediately activate new SW
    );
});

// ── Activate: Clean up ALL old caches ──
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(name => name !== CACHE_NAME_STATIC && name !== CACHE_NAME_DYNAMIC)
                    .map(name => {
                        console.log('[SW] Deleting old cache:', name);
                        return caches.delete(name);
                    })
            );
        }).then(() => {
            console.log('[SW] Activated and claimed all clients');
            return self.clients.claim(); // Take control of all pages immediately
        })
    );
});

// ── Fetch: Smart caching strategies ──
self.addEventListener('fetch', event => {
    // Only handle GET requests
    if (event.request.method !== 'GET') return;

    const url = new URL(event.request.url);

    // Skip cross-origin requests (CDN, analytics, etc.) — let browser handle them
    if (url.origin !== self.location.origin) return;

    const isNavigationRequest = event.request.mode === 'navigate';
    const acceptsHtml = event.request.headers.get('accept')?.includes('text/html');

    // Detect AJAX / Inertia / Livewire requests — these MUST ALWAYS go to network
    const isInertia = event.request.headers.get('x-inertia');
    const isLivewire = event.request.headers.get('x-livewire');
    const isXHR = event.request.headers.get('x-requested-with') === 'XMLHttpRequest';

    // ── Strategy 0: Skip auth routes — NEVER cache login/logout/register ──
    if (url.pathname.startsWith('/login') || url.pathname.startsWith('/logout') || url.pathname.startsWith('/register') || url.pathname.startsWith('/psb/daftar')) {
        return;
    }

    // ── Strategy 1: Network-Only for API/AJAX/Inertia/Livewire ──
    if (isInertia || isLivewire || isXHR || url.pathname.startsWith('/api/') || url.pathname.includes('/api/')) {
        // Never cache API calls — always go to network
        return;
    }

    // ── Strategy 2: Network-First for HTML navigation ──
    // Always fetch from server; fall back to offline page if no network
    if (isNavigationRequest || acceptsHtml) {
        event.respondWith(
            fetch(event.request)
                .then(response => {
                    return response;
                })
                .catch(() => {
                    return caches.match('/offline');
                })
        );
        return;
    }

    // ── Strategy 3: Cache-First for Vite build assets (hashed filenames) ──
    // Vite assets have content hashes in filenames, so they're safe to cache long-term
    if (url.pathname.startsWith('/build/assets/')) {
        event.respondWith(
            caches.match(event.request).then(cachedResponse => {
                if (cachedResponse) return cachedResponse;

                return fetch(event.request).then(networkResponse => {
                    if (networkResponse && networkResponse.status === 200) {
                        const clone = networkResponse.clone();
                        caches.open(CACHE_NAME_DYNAMIC).then(cache => {
                            cache.put(event.request, clone);
                        });
                    }
                    return networkResponse;
                });
            })
        );
        return;
    }

    // ── Strategy 4: Network-First for images and other static assets ──
    // Try network first, fall back to cache for images/icons
    const isStaticAsset =
        url.pathname.endsWith('.png') ||
        url.pathname.endsWith('.jpg') ||
        url.pathname.endsWith('.jpeg') ||
        url.pathname.endsWith('.webp') ||
        url.pathname.endsWith('.svg') ||
        url.pathname.endsWith('.ico') ||
        url.pathname.endsWith('.woff2');

    if (isStaticAsset) {
        event.respondWith(
            fetch(event.request)
                .then(networkResponse => {
                    if (networkResponse && networkResponse.status === 200) {
                        const clone = networkResponse.clone();
                        caches.open(CACHE_NAME_DYNAMIC).then(cache => {
                            cache.put(event.request, clone);
                        });
                    }
                    return networkResponse;
                })
                .catch(() => {
                    return caches.match(event.request);
                })
        );
        return;
    }

    // ── Default: Network-only for everything else ──
    // Don't cache unknown requests — let browser handle normally
});

// ── Message handler: Allow pages to communicate with SW ──
self.addEventListener('message', event => {
    if (event.data === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});
