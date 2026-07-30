<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — PP Nurul Furqon</title>
    <meta name="description" content="@yield('meta_description', 'Sistem Manajemen Pondok Pesantren Nurul Furqon')">

    {{-- PWA Meta Tags --}}
    <meta name="theme-color" media="(prefers-color-scheme: light)" content="#065f46">
    <meta name="theme-color" media="(prefers-color-scheme: dark)" content="#022c22">
    <meta name="theme-color" content="#065f46">
    <meta name="application-name" content="PP Nurul Furqon">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="PP Nurul Furqon">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192x192.png">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/icons/apple-touch-icon.png">
    <link rel="mask-icon" href="/icons/icon-192x192-maskable.png" color="#065f46">

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
                    const scrollPos = activeItem.offsetTop - (sidebarNav.clientHeight / 2) + (activeItem.clientHeight / 2);
                    sidebarNav.scrollTop = scrollPos > 0 ? scrollPos : 0;
                }
            }

            // === SIDEBAR MOBILE TOGGLE ===
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const btnOpen = document.getElementById('btn-toggle-sidebar');
            const btnClose = document.getElementById('btn-close-sidebar');

            function openSidebar() {
                if (sidebar) {
                    sidebar.classList.remove('-translate-x-full');
                }
                if (overlay) {
                    overlay.classList.remove('hidden');
                }
                document.body.classList.add('overflow-hidden', 'md:overflow-auto');
            }

            function closeSidebar() {
                if (sidebar) {
                    sidebar.classList.add('-translate-x-full');
                }
                if (overlay) {
                    overlay.classList.add('hidden');
                }
                document.body.classList.remove('overflow-hidden', 'md:overflow-auto');
            }

            const toggleMobileSidebar = (e) => {
                if (e) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                if (sidebar && sidebar.classList.contains('-translate-x-full')) {
                    openSidebar();
                } else {
                    closeSidebar();
                }
            };

            if (btnOpen) {
                btnOpen.addEventListener('click', toggleMobileSidebar);
            }
            if (btnClose) {
                btnClose.addEventListener('click', (e) => {
                    e.preventDefault();
                    closeSidebar();
                });
            }
            if (overlay) {
                overlay.addEventListener('click', closeSidebar);
            }

            // Auto-close sidebar on mobile when tapping a menu link
            if (sidebar) {
                sidebar.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', () => {
                        if (window.innerWidth < 768) {
                            closeSidebar();
                        }
                    });
                });
            }

            // === TOPBAR DROPDOWNS ===
            const btnNotif = document.getElementById('btn-notifications');
            const notifDrop = document.getElementById('notification-dropdown');
            const btnUser = document.getElementById('btn-user-menu');
            const userDrop = document.getElementById('user-dropdown');
            const btnSearch = document.getElementById('btn-global-search');
            const searchModal = document.getElementById('global-search-modal');
            const btnCloseSearch = document.getElementById('btn-close-search');

            if (btnNotif && notifDrop) btnNotif.addEventListener('click', (e) => { e.stopPropagation(); if (userDrop) userDrop.classList.add('hidden'); notifDrop.classList.toggle('hidden'); });
            if (btnUser && userDrop) btnUser.addEventListener('click', (e) => { e.stopPropagation(); if (notifDrop) notifDrop.classList.add('hidden'); userDrop.classList.toggle('hidden'); });

            if (btnSearch && searchModal) btnSearch.addEventListener('click', () => { searchModal.classList.remove('hidden'); });
            if (btnCloseSearch && searchModal) btnCloseSearch.addEventListener('click', () => { searchModal.classList.add('hidden'); });
            if (searchModal) searchModal.addEventListener('click', (e) => { if (e.target === searchModal) searchModal.classList.add('hidden'); });

            // Close dropdowns on outside click
            document.addEventListener('click', (e) => {
                if (notifDrop && btnNotif && !btnNotif.contains(e.target)) notifDrop.classList.add('hidden');
                if (userDrop && btnUser && !btnUser.contains(e.target)) userDrop.classList.add('hidden');
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') { e.preventDefault(); if (searchModal) searchModal.classList.remove('hidden'); }
                if (e.key === 'Escape') {
                    if (searchModal) searchModal.classList.add('hidden');
                    if (notifDrop) notifDrop.classList.add('hidden');
                    if (userDrop) userDrop.classList.add('hidden');
                    if (window.innerWidth < 768) closeSidebar();
                }
            });
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

        // Global UX Improvement: Auto Disable Submit Buttons & Show Spinner on Form Submit
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (form.getAttribute('data-no-spinner') === 'true') return;
            
            const btn = form.querySelector('button[type="submit"]');
            if (btn && !btn.disabled) {
                btn.dataset.originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.classList.add('opacity-75', 'cursor-not-allowed');
                btn.innerHTML = `
                    <svg class="animate-spin h-4 w-4 text-current inline mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Memproses...</span>
                `;
            }
        });
    </script>
    @includeIf('partials.pwa-install-banner')
    @includeIf('partials.pwa-network-toast')
    @includeIf('partials.toast-container')
    @includeIf('partials.shortcuts-modal')
    @stack('scripts')

    {{-- Global Live Search Modal (Direct child of body to guarantee full viewport overlay & fixed centering) --}}
    <div id="global-search-modal" class="hidden fixed inset-0 z-[100] flex items-start justify-center p-4 sm:p-6 md:pt-16 bg-surface-900/60 backdrop-blur-sm transition-all animate-fade-in">
        <div class="w-full max-w-2xl rounded-2xl bg-white shadow-2xl border border-surface-200 overflow-hidden flex flex-col max-h-[85vh]">
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-surface-100 bg-surface-50/50">
                <div class="flex items-center gap-2">
                    <i data-lucide="search" class="w-4 h-4 text-emerald-600"></i>
                    <p class="text-xs font-bold text-surface-900 font-heading">Pencarian Menu & Modul</p>
                </div>
                <button type="button" id="btn-close-search" class="p-1 rounded-lg text-surface-400 hover:bg-surface-200 hover:text-surface-700 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="p-4 border-b border-surface-100">
                <div class="relative">
                    <input id="global-search-input" type="text" autocomplete="off" placeholder="Ketik nama menu (misal: santri, spp, berita, rombel, pegawai)..."
                           class="w-full pl-4 pr-10 py-2.5 rounded-xl border border-surface-200 text-surface-900 text-xs font-medium focus:outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600/20" />
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[0.65rem] font-bold text-surface-400 bg-surface-100 px-1.5 py-0.5 rounded">ESC</span>
                </div>
            </div>
            <div id="global-search-results" class="p-2 overflow-y-auto divide-y divide-surface-50 flex-1 min-h-[200px]">
                {{-- Results rendered dynamically via JS --}}
            </div>
            <div class="border-t border-surface-100 px-4 py-2.5 text-[0.7rem] text-surface-400 flex items-center justify-between bg-surface-50/30">
                <span>Tekan <kbd class="px-1 py-0.5 bg-white border rounded text-[0.65rem]">Enter</kbd> untuk membuka rute</span>
                <span>PP Nurul Furqon SIM</span>
            </div>
        </div>
    </div>
</body>
</html>
