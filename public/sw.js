// ============================================================
// Service Worker — PP Nurul Furqon
// Version: v11-20260802-0845 (Android Chrome & iOS Safari Compliant)
// ============================================================

const CACHE_VERSION = 'v11-20260802-0845';
const CACHE_NAME_STATIC = `pp-nurul-furqon-static-${CACHE_VERSION}`;
const CACHE_NAME_DYNAMIC = `pp-nurul-furqon-dynamic-${CACHE_VERSION}`;

// Core files to pre-cache ONLY for offline fallback
const PRECACHE_ASSETS = [
    '/offline',
    '/manifest.json',
    '/favicon.ico',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
    '/icons/apple-touch-icon.png'
];

// ── Install: Pre-cache core offline assets & force immediate activation ──
self.addEventListener('install', event => {
    console.log(`[SW ${CACHE_VERSION}] Installing Service Worker...`);
    event.waitUntil(
        caches.open(CACHE_NAME_STATIC).then(cache => {
            return Promise.all(
                PRECACHE_ASSETS.map(asset =>
                    cache.add(asset).catch(err => {
                        console.warn('[SW] Pre-cache skip:', asset, err);
                    })
                )
            );
        }).then(() => {
            console.log(`[SW ${CACHE_VERSION}] Skip waiting triggered.`);
            return self.skipWaiting();
        })
    );
});

// ── Activate: Purge ALL old caches & claim all clients ──
self.addEventListener('activate', event => {
    console.log(`[SW ${CACHE_VERSION}] Activating & purging stale caches...`);
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(name => {
                    if (name !== CACHE_NAME_STATIC && name !== CACHE_NAME_DYNAMIC) {
                        console.log('[SW] Deleting obsolete cache:', name);
                        return caches.delete(name);
                    }
                })
            );
        }).then(() => {
            console.log(`[SW ${CACHE_VERSION}] Claiming active clients.`);
            return self.clients.claim();
        })
    );
});

// ── Fetch: Clean Network-First for Navigation & Android Chrome Safe ──
self.addEventListener('fetch', event => {
    // Only intercept GET requests
    if (event.request.method !== 'GET') return;

    const url = new URL(event.request.url);

    // Skip cross-origin requests
    if (url.origin !== self.location.origin) return;

    const isNavigationRequest = event.request.mode === 'navigate';
    const acceptsHtml = event.request.headers.get('accept')?.includes('text/html');

    // ── Strategy 1: Network-First for HTML navigation (No custom RequestInit options to prevent Android Chrome TypeError) ──
    if (isNavigationRequest || acceptsHtml) {
        event.respondWith(
            fetch(event.request)
                .catch(() => caches.match('/offline'))
        );
        return;
    }

    // ── Strategy 2: Cache-First for Vite build assets (hashed filenames) ──
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

    // ── Strategy 3: Network-First for images & static assets with offline fallback ──
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
                .catch(() => caches.match(event.request))
        );
        return;
    }
});

// ── Message Listener ──
self.addEventListener('message', event => {
    if (event.data === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});
