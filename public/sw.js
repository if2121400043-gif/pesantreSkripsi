// ============================================================
// Service Worker — PP Nurul Furqon
// Version: v10-20260801-1507 (Strict Network-First & Auto-Bust PWA Cache)
// ============================================================

const CACHE_VERSION = 'v10-20260801-1507';
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
    console.log(`[SW ${CACHE_VERSION}] Installing new Service Worker...`);
    event.waitUntil(
        caches.open(CACHE_NAME_STATIC).then(cache => {
            return Promise.all(
                PRECACHE_ASSETS.map(asset =>
                    cache.add(asset).catch(err => {
                        console.warn('[SW] Failed to pre-cache:', asset, err);
                    })
                )
            );
        }).then(() => {
            console.log(`[SW ${CACHE_VERSION}] Skip waiting and activate immediately.`);
            return self.skipWaiting(); // Force active immediately
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
            console.log(`[SW ${CACHE_VERSION}] Claiming all active clients immediately.`);
            return self.clients.claim(); // Take control of all pages right now
        })
    );
});

// ── Fetch: Zero-Cache for HTML/Portal, Pure Network-First ──
self.addEventListener('fetch', event => {
    // Only intercept GET requests
    if (event.request.method !== 'GET') return;

    const url = new URL(event.request.url);

    // Skip cross-origin requests
    if (url.origin !== self.location.origin) return;

    const isNavigationRequest = event.request.mode === 'navigate';
    const acceptsHtml = event.request.headers.get('accept')?.includes('text/html');

    // ── Strategy 0: NEVER cache authentication & dynamic portal routes ──
    if (
        url.pathname.startsWith('/login') ||
        url.pathname.startsWith('/logout') ||
        url.pathname.startsWith('/register') ||
        url.pathname.startsWith('/portal') ||
        url.pathname.startsWith('/bendahara') ||
        url.pathname.startsWith('/panitia-psb') ||
        url.pathname.startsWith('/admin')
    ) {
        // ALWAYS fetch live from network; fall back to offline page if network is down
        if (isNavigationRequest || acceptsHtml) {
            event.respondWith(
                fetch(event.request, { cache: 'no-store' })
                    .catch(() => caches.match('/offline'))
            );
            return;
        }
    }

    // ── Strategy 1: Network-First for HTML navigation ──
    if (isNavigationRequest || acceptsHtml) {
        event.respondWith(
            fetch(event.request)
                .catch(() => caches.match('/offline'))
        );
        return;
    }

    // ── Strategy 2: Cache-First for Vite build assets (hashed filenames only) ──
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

    // ── Default: Network-First for images/assets ──
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
