<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — PP Nurul Furqon</title>
    <meta name="description" content="@yield('meta_description', 'Sistem Manajemen Pondok Pesantren Nurul Furqon')">

    {{-- PWA Meta Tags --}}
    <meta name="theme-color" content="#065f46">
    <meta name="application-name" content="PP Nurul Furqon">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="PP Nurul Furqon">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192x192.png">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/icons/icon-192x192.png">
    <link rel="mask-icon" href="/icons/icon-192x192.png" color="#065f46">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest" defer></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="min-h-screen bg-surface-50 overflow-x-hidden">
    {{-- Skip to content (accessibility) --}}
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:top-4 focus:left-4 bg-primary-600 text-white px-4 py-2 rounded-lg font-medium">
        Langsung ke konten
    </a>

    <div class="flex min-h-screen" id="app-layout">
        {{-- Sidebar Overlay (mobile) --}}
        <div id="sidebar-overlay"
             class="fixed inset-0 bg-black/50 z-40 hidden md:hidden transition-opacity duration-300">
        </div>

        {{-- Sidebar (hidden for portal routes and role-switch page) --}}
        @if(!request()->routeIs('portal.*') && !request()->routeIs('akun.ganti-peran'))
            @include('layouts.partials.sidebar')
        @endif

        {{-- Main Content Area --}}
        <div class="flex-1 min-w-0 w-full flex flex-col min-h-screen transition-all duration-300 {{ request()->routeIs('portal.*') || request()->routeIs('akun.ganti-peran') ? '' : 'md:ml-[var(--spacing-sidebar)]' }}" id="main-wrapper">
            {{-- Top Bar --}}
            @if(!request()->routeIs('akun.ganti-peran'))
                @include('layouts.partials.topbar')
            @endif

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mx-4 mt-4 lg:mx-8">
                    <x-alert type="success" :message="session('success')" dismissible />
                </div>
            @endif
            @if(session('error'))
                <div class="mx-4 mt-4 lg:mx-8">
                    <x-alert type="danger" :message="session('error')" dismissible />
                </div>
            @endif

            {{-- Page Content --}}
            <main id="main-content" class="mobile-safe flex-1 min-w-0 p-4 lg:p-8 animate-fade-in">
                {{-- Page Header --}}
                @hasSection('page_header')
                    <div class="mb-6">
                        @yield('page_header')
                    </div>
                @endif

                {{-- Page Body --}}
                @yield('content')
            </main>

            {{-- Footer --}}
            <footer class="py-4 px-4 lg:px-8 text-center text-sm text-surface-400 border-t border-surface-100">
                &copy; {{ date('Y') }} Pondok Pesantren Nurul Furqon. Semua hak dilindungi.
            </footer>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
            
            // Auto-scroll sidebar to active item
            const sidebarNav = document.getElementById('sidebar-nav');
            if (sidebarNav) {
                const activeItem = sidebarNav.querySelector('.active');
                if (activeItem) {
                    // Scroll to center the active item in the view
                    const scrollPos = activeItem.offsetTop - (sidebarNav.clientHeight / 2) + (activeItem.clientHeight / 2);
                    sidebarNav.scrollTop = scrollPos > 0 ? scrollPos : 0;
                }
            }
        });
    </script>
    <script>
        // Handle beforeinstallprompt for Android (Chrome) and show install button
        let deferredPrompt = null;
        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent the mini-infobar from appearing on mobile
            e.preventDefault();
            deferredPrompt = e;
            const installBtn = document.getElementById('btn-install-pwa');
            if (installBtn) {
                installBtn.classList.remove('hidden');
                installBtn.addEventListener('click', async () => {
                    installBtn.classList.add('hidden');
                    if (!deferredPrompt) return;
                    deferredPrompt.prompt();
                    const choiceResult = await deferredPrompt.userChoice;
                    deferredPrompt = null;
                });
            }
        });

        // iOS specific note: Safari does not fire beforeinstallprompt. Show hint if iOS.
        function isIos() {
            return /iphone|ipad|ipod/i.test(navigator.userAgent);
        }
        function isInStandaloneMode() {
            return ('standalone' in window.navigator) && (window.navigator.standalone);
        }
        document.addEventListener('DOMContentLoaded', () => {
            if (isIos() && !isInStandaloneMode()) {
                const installBtn = document.getElementById('btn-install-pwa');
                if (installBtn) {
                    installBtn.classList.remove('hidden');
                    installBtn.addEventListener('click', () => {
                        alert('Untuk memasang aplikasi di iPhone/iPad: ketuk tombol "Share" (ikon persegi dengan panah), lalu pilih "Add to Home Screen".');
                    });
                }
            }
        });
    </script>
    
    {{-- PWA Service Worker Registration with Update Detection --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js', { updateViaCache: 'none' })
                    .then(registration => {
                        console.log('[PWA] Service Worker registered', registration.scope);

                        // Check for SW updates periodically (every 60 seconds)
                        setInterval(() => {
                            registration.update().catch(() => {});
                        }, 60000);

                        // Listen for new SW waiting to activate
                        registration.addEventListener('updatefound', () => {
                            const newWorker = registration.installing;
                            if (!newWorker) return;

                            newWorker.addEventListener('statechange', () => {
                                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                    // New SW is ready — tell it to activate immediately
                                    console.log('[PWA] New Service Worker ready, activating...');
                                    newWorker.postMessage('SKIP_WAITING');
                                }
                            });
                        });
                    })
                    .catch(err => console.error('[PWA] SW registration failed', err));

                // Reload page when new SW takes control (ensures fresh content)
                let refreshing = false;
                navigator.serviceWorker.addEventListener('controllerchange', () => {
                    if (!refreshing) {
                        refreshing = true;
                        console.log('[PWA] New Service Worker active, reloading for fresh content...');
                        window.location.reload();
                    }
                });
            });
        }

        // Prevent BFCache (Back-Forward Cache) from showing stale pages on mobile
        window.addEventListener('pageshow', (event) => {
            if (event.persisted) {
                // Page was restored from BFCache — reload to get fresh data
                window.location.reload();
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
