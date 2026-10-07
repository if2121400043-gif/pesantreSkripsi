<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('Beranda')) — {{ $pesantren?->nama ?? 'Pesantren' }}</title>
    <meta name="description" content="@yield('meta_description', ($pesantren?->nama ?? 'Pesantren') . ' — ' . __('Lembaga Pendidikan Islam'))">

    {{-- PWA Meta Tags --}}
    <meta name="theme-color" content="#1e3a5f">
    <meta name="application-name" content="PP Nurul Furqon">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="PP Nurul Furqon">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192x192.png">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    <link rel="mask-icon" href="/icons/icon-192x192.png" color="#1e3a5f">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Dark Mode Script (Prevent FOUC) -->
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <style>
        *, *::before, *::after { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Navbar glass - works for both light and dark mode */
        .navbar-glass {
            background: linear-gradient(110deg, #162f52 0%, #1e3a5f 48%, #315b91 100%) !important;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        html.dark .navbar-glass {
            background: linear-gradient(110deg, #0f172a 0%, #162f52 52%, #1e3a5f 100%) !important;
        }
        .navbar-register-button {
            background:
                radial-gradient(circle at 88% 12%, rgba(255, 244, 194, 0.24), transparent 28%),
                linear-gradient(118deg, #7b5315 0%, #b98122 38%, #d6a63c 68%, #a86f1b 100%) !important;
            color: #fff8e7 !important;
            text-transform: uppercase;
        }
        .navbar-register-button:hover {
            filter: brightness(1.08);
        }

        /* Language Dropdown Animation */
        #langDropdown {
            transition: all 0.2s ease-in-out;
            transform-origin: top right;
        }

        /* Secondary gold footer */
        .footer-gold {
            background:
                radial-gradient(circle at 88% 12%, rgba(255, 244, 194, 0.24), transparent 28%),
                linear-gradient(118deg, #7b5315 0%, #b98122 38%, #d6a63c 68%, #a86f1b 100%);
            color: #fff8e7;
        }
        .footer-gold .text-white { color: #fffdf5 !important; }
        .footer-gold .text-surface-300,
        .footer-gold .text-surface-400 { color: rgba(255, 248, 231, 0.82) !important; }
        .footer-gold .text-surface-500 { color: rgba(255, 248, 231, 0.68) !important; }
        .footer-gold .border-surface-800\/60 { border-color: rgba(255, 248, 231, 0.24) !important; }
        .footer-gold .bg-surface-900 { background-color: rgba(92, 57, 10, 0.34) !important; }
        .footer-gold .bg-surface-800 { background-color: rgba(92, 57, 10, 0.46) !important; }
        .footer-gold .hover\:bg-surface-700:hover { background-color: rgba(74, 45, 8, 0.62) !important; }
        .footer-gold .border-surface-700 { border-color: rgba(255, 248, 231, 0.28) !important; }
        .footer-gold .text-primary-400,
        .footer-gold .text-accent-500 { color: #fff0ae !important; }
    </style>
    @stack('styles')
</head>
<body class="bg-surface-50 text-surface-900 dark:bg-surface-950 dark:text-surface-200 antialiased transition-colors duration-300 selection:bg-primary-500 selection:text-white overflow-x-hidden">

    @if(empty($hideNavbar))
    {{-- ═══════════ NAVBAR ═══════════ --}}
    <nav class="fixed top-0 inset-x-0 z-50 navbar-glass border-b border-surface-200/50 dark:border-surface-800/80 transition-all duration-300" id="mainNavbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">

                {{-- Logo --}}
                <a href="{{ route('frontend.home') }}" class="flex items-center gap-3 shrink-0 group">
                    <img src="{{ asset('images/logo-pesantren.webp') }}?v={{ time() }}" alt="Logo" class="w-12 h-12 object-contain bg-white rounded-2xl shadow-lg shadow-primary-500/30 p-0.5 group-hover:shadow-primary-500/50 transition-all duration-300 transform group-hover:-translate-y-0.5">
                    <div class="hidden sm:block leading-tight">
                        <span class="block font-extrabold text-white text-[17px] tracking-tight uppercase">{{ $pesantren?->nama ?? 'Pondok Pesantren Nurul Furqon' }}</span>
                        <span class="block text-[11px] text-primary-200 font-bold uppercase tracking-widest mt-0.5">{{ __('Lembaga Pendidikan Islam') }}</span>
                    </div>
                </a>

                {{-- Desktop Menu --}}
                <div class="hidden lg:flex items-center gap-2">
                    <a href="{{ route('frontend.home') }}" class="px-4 py-2 text-sm font-semibold rounded-xl transition-all duration-300 {{ request()->routeIs('frontend.home') ? 'text-white bg-white/20' : 'text-primary-100 hover:text-white hover:bg-white/10' }}">{{ __('Beranda') }}</a>

                    <a href="{{ route('frontend.profil') }}" class="px-4 py-2 text-sm font-semibold rounded-xl transition-all duration-300 {{ request()->routeIs('frontend.profil') ? 'text-white bg-white/20' : 'text-primary-100 hover:text-white hover:bg-white/10' }}">{{ __('Tentang Kami') }}</a>

                    <a href="{{ route('frontend.publikasi') }}" class="px-4 py-2 text-sm font-semibold rounded-xl transition-all duration-300 {{ request()->routeIs('frontend.publikasi*') || request()->routeIs('frontend.berita*') ? 'text-white bg-white/20' : 'text-primary-100 hover:text-white hover:bg-white/10' }}">{{ __('Publikasi') }}</a>

                    <a href="#" class="px-4 py-2 text-sm font-semibold rounded-xl transition-all duration-300 text-primary-100 hover:text-white hover:bg-white/10">{{ __('Kurikulum') }}</a>
                </div>

                {{-- Action Area (Theme, Lang, CTA) --}}
                <div class="hidden lg:flex items-center gap-3">
                    <a href="/psb" class="navbar-register-button inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold rounded-xl shadow-lg shadow-secondary-500/25 hover:shadow-secondary-500/40 transition-all duration-300 hover:-translate-y-0.5 border border-secondary-400">
                        <x-icon name="graduation-cap" size="w-4 h-4" />
                        {{ __('Pendaftaran Santri Baru') }}
                    </a>

                    <div class="h-6 w-px bg-white/20 mx-1"></div>
                    {{-- Language Switcher --}}
                    <div class="relative">
                        <button id="langBtn" class="flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-primary-100 hover:text-white rounded-xl hover:bg-white/10 transition-colors">
                            <x-icon name="globe" size="w-4 h-4" />
                            <span>{{ strtoupper(app()->getLocale()) }}</span>
                            <x-icon name="chevron-down" size="w-3 h-3" />
                        </button>
                        {{-- Dropdown --}}
                        <div id="langDropdown" class="absolute right-0 mt-2 w-32 bg-white dark:bg-surface-900 rounded-xl shadow-dropdown border border-surface-100 dark:border-surface-800 opacity-0 invisible transform scale-95 z-50">
                            <div class="p-1.5 space-y-1">
                                <a href="{{ route('lang.switch', 'id') }}" class="block px-3 py-2 text-sm font-medium rounded-lg hover:bg-primary-50 dark:hover:bg-surface-800 text-surface-700 dark:text-surface-300 hover:text-primary-700 dark:hover:text-primary-400 transition-colors {{ app()->getLocale() == 'id' ? 'bg-primary-50/50 dark:bg-surface-800/50 text-primary-700 dark:text-primary-400' : '' }}">🇮🇩 Indonesia</a>
                                <a href="{{ route('lang.switch', 'en') }}" class="block px-3 py-2 text-sm font-medium rounded-lg hover:bg-primary-50 dark:hover:bg-surface-800 text-surface-700 dark:text-surface-300 hover:text-primary-700 dark:hover:text-primary-400 transition-colors {{ app()->getLocale() == 'en' ? 'bg-primary-50/50 dark:bg-surface-800/50 text-primary-700 dark:text-primary-400' : '' }}">🇬🇧 English</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Mobile Hamburger --}}
                <div class="flex items-center gap-2 lg:hidden">

                    <button id="mobileMenuBtn" class="p-2 text-primary-100 hover:text-white rounded-xl hover:bg-white/10 transition-colors">
                        <x-icon name="menu" size="w-6 h-6" class="block" id="menuIconOpen" />
                        <x-icon name="x" size="w-6 h-6 hidden" id="menuIconClose" />
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Dropdown --}}
        <div id="mobileMenu" class="hidden lg:hidden bg-white/95 dark:bg-surface-950/95 backdrop-blur-xl border-t border-surface-100 dark:border-surface-800 shadow-xl absolute w-full">
            <div class="max-w-7xl mx-auto px-4 py-5 space-y-2">
                <a href="{{ route('frontend.home') }}" class="block px-4 py-3 rounded-xl text-sm font-bold {{ request()->routeIs('frontend.home') ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400' : 'text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-800' }}">{{ __('Beranda') }}</a>
                <a href="{{ route('frontend.profil') }}" class="block px-4 py-3 rounded-xl text-sm font-bold {{ request()->routeIs('frontend.profil') ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400' : 'text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-800' }}">{{ __('Tentang Kami') }}</a>
                <a href="{{ route('frontend.publikasi') }}" class="block px-4 py-3 rounded-xl text-sm font-bold {{ request()->routeIs('frontend.publikasi*') || request()->routeIs('frontend.berita*') ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400' : 'text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-800' }}">{{ __('Publikasi') }}</a>
                <a href="#" class="block px-4 py-3 rounded-xl text-sm font-bold text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-800">{{ __('Kurikulum') }}</a>

                <div class="pt-2 flex justify-between items-center px-4">
                    <span class="text-xs font-bold text-surface-500 uppercase">Bahasa / Language</span>
                    <div class="flex gap-2">
                        <a href="{{ route('lang.switch', 'id') }}" class="px-3 py-1.5 text-xs font-bold rounded-lg border {{ app()->getLocale() == 'id' ? 'border-primary-500 text-primary-600 bg-primary-50 dark:bg-primary-900/20' : 'border-surface-200 dark:border-surface-700 text-surface-600 dark:text-surface-400' }}">ID</a>
                        <a href="{{ route('lang.switch', 'en') }}" class="px-3 py-1.5 text-xs font-bold rounded-lg border {{ app()->getLocale() == 'en' ? 'border-primary-500 text-primary-600 bg-primary-50 dark:bg-primary-900/20' : 'border-surface-200 dark:border-surface-700 text-surface-600 dark:text-surface-400' }}">EN</a>
                    </div>
                </div>

                <div class="pt-4 flex flex-col gap-2">
                    @auth
                        @php
                            $userRedirect = auth()->user()->active_role->role->redirect_url ?? '/portal/beranda';
                        @endphp
                        <a href="{{ url($userRedirect) }}" class="block w-full text-center px-4 py-3 bg-primary-600 text-white font-extrabold rounded-xl shadow-md">Dashboard Saya</a>
                    @else
                        <a href="/login" class="block w-full text-center px-4 py-3 border border-surface-200 dark:border-surface-800 text-surface-700 dark:text-surface-300 font-bold rounded-xl">{{ __('Masuk') }}</a>
                    @endauth
                    <a href="/psb" class="navbar-register-button block w-full text-center px-4 py-3.5 font-bold rounded-xl shadow-lg shadow-secondary-500/20">{{ __('Pendaftaran Santri Baru') }}</a>
                </div>
            </div>
        </div>
    </nav>
    @endif

    {{-- ═══════════ MAIN ═══════════ --}}
    <main class="mobile-safe @if(empty($hideNavbar)) pt-[80px] @endif min-h-screen">
        @yield('content')
    </main>

    @if(empty($hideFooter))
    {{-- ═══════════ FOOTER ═══════════ --}}
    <footer class="footer-gold relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 relative z-10">
            <div class="grid pt-20-1 md:grid-cols-3 gap-8 lg:gap-12">
                {{-- Col 1: Deskripsi --}}
                <div class="flex flex-col items-center md:items-start text-center md:text-left">
                    <div class="flex items-center gap-3 mb-6">
                        <img src="{{ asset('images/logo-pesantren.webp') }}?v={{ time() }}" alt="Logo" class="w-12 h-12 object-contain bg-white rounded-2xl shadow-lg shadow-primary-500/20 p-0.5">
                        <span class="text-white font-extrabold text-[17px] tracking-tight uppercase">{{ $pesantren?->nama ?? 'Pondok Pesantren Nurul Furqon' }}</span>
                    </div>
                    <p class="text-surface-400 text-sm leading-relaxed font-medium">
                        {{ __('Lembaga Pendidikan Islam') }} terpadu yang menyeimbangkan ilmu agama, akademik, dan pembentukan akhlak santri di era modern.
                    </p>
                </div>

                {{-- Col 2: Kontak --}}
                <div class="flex flex-col items-center md:items-start text-center md:text-left">
                    <h4 class="text-white font-bold text-sm uppercase tracking-widest mb-6 border-b border-surface-800 pb-2 inline-block">{{ __('Hubungi Kami') }}</h4>
                    <ul class="space-y-4 text-sm font-medium text-surface-400">
                        @if($pesantren?->alamat)
                        <li class="flex items-start justify-center md:justify-start gap-3 group">
                            <div class="w-8 h-8 rounded-full bg-surface-900 flex items-center justify-center flex-shrink-0 group-hover:bg-primary-900 transition-colors text-primary-400">
                                <x-icon name="map-pin" size="w-4 h-4" />
                            </div>
                            <span class="pt-1.5">{{ $pesantren->alamat }}</span>
                        </li>
                        @endif
                        @if($pesantren?->telepon)
                        <li class="flex items-center justify-center md:justify-start gap-3 group">
                            <div class="w-8 h-8 rounded-full bg-surface-900 flex items-center justify-center shrink-0 group-hover:bg-primary-900 transition-colors text-primary-400">
                                <x-icon name="phone" size="w-4 h-4" />
                            </div>
                            <span>{{ $pesantren->telepon }}</span>
                        </li>
                        @endif
                        @if($pesantren?->email)
                        <li class="flex items-center justify-center md:justify-start gap-3 group">
                            <div class="w-8 h-8 rounded-full bg-surface-900 flex items-center justify-center shrink-0 group-hover:bg-primary-900 transition-colors text-primary-400">
                                <x-icon name="mail" size="w-4 h-4" />
                            </div>
                            <span>{{ $pesantren->email }}</span>
                        </li>
                        @endif
                    </ul>
                </div>

                {{-- Col 3: Akses Portal --}}
                <div class="flex flex-col items-center md:items-start text-center md:text-left">
                    <h4 class="text-white font-bold text-sm uppercase tracking-widest mb-6 border-b border-surface-800 pb-2 inline-block">{{ __('Akses Internal') }}</h4>
                    <div class="w-full max-w-[240px]">
                        @auth
                            @php
                                $userRedirect = auth()->user()->active_role->role->redirect_url ?? '/portal/beranda';
                            @endphp
                            <a href="{{ url($userRedirect) }}" class="inline-flex w-full items-center justify-center gap-2 px-4 py-3 bg-primary-600 hover:bg-primary-500 text-white text-sm font-bold rounded-xl transition-all duration-300 shadow-lg shadow-primary-600/20 hover:-translate-y-0.5">
                                <x-icon name="layout-dashboard" size="w-4 h-4" /> {{ __('Dashboard Saya') }}
                            </a>
                        @else
                            <a href="/login" class="inline-flex w-full items-center justify-center gap-2 px-4 py-3 bg-surface-800 hover:bg-surface-700 text-white text-sm font-bold rounded-xl transition-all duration-300 border border-surface-700 hover:border-surface-600 shadow-sm hover:-translate-y-0.5">
                                <x-icon name="log-in" size="w-4 h-4" /> {{ __('Masuk ke Portal') }}
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        {{-- Copyright --}}
        <div class="border-t border-surface-800/60 relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col md:flex-row justify-between items-center text-xs font-medium text-surface-500">
                <p>&copy; {{ date('Y') }} <span class="text-surface-300 uppercase">{{ $pesantren?->nama ?? 'Pondok Pesantren Nurul Furqon' }}</span>. {{ __('All rights reserved.') }}</p>
                <p class="mt-2 md:mt-0 flex items-center gap-1.5">
                    {{ __('Sistem Manajemen Pesantren') }} <x-icon name="sparkles" size="w-3 h-3" class="text-accent-500" />
                </p>
            </div>
        </div>
    </footer>
    @endif

    <script>
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        const menuIconOpen = document.getElementById('menuIconOpen');
        const menuIconClose = document.getElementById('menuIconClose');
        mobileMenuBtn.addEventListener('click', () => {
            const isHidden = mobileMenu.classList.toggle('hidden');
            menuIconOpen.classList.toggle('hidden', !isHidden);
            menuIconClose.classList.toggle('hidden', isHidden);
        });

        // Navbar scroll shadow
        window.addEventListener('scroll', () => {
            document.getElementById('mainNavbar').classList.toggle('navbar-scrolled', window.scrollY > 10);
        });

        // Language Dropdown Logic
        const langBtn = document.getElementById('langBtn');
        const langDropdown = document.getElementById('langDropdown');
        let isDropdownOpen = false;

        if (langBtn && langDropdown) {
            langBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                isDropdownOpen = !isDropdownOpen;
                if(isDropdownOpen) {
                    langDropdown.classList.remove('opacity-0', 'invisible', 'scale-95');
                    langDropdown.classList.add('opacity-100', 'visible', 'scale-100');
                } else {
                    langDropdown.classList.add('opacity-0', 'invisible', 'scale-95');
                    langDropdown.classList.remove('opacity-100', 'visible', 'scale-100');
                }
            });

            document.addEventListener('click', (e) => {
                if (isDropdownOpen && !langDropdown.contains(e.target)) {
                    isDropdownOpen = false;
                    langDropdown.classList.add('opacity-0', 'invisible', 'scale-95');
                    langDropdown.classList.remove('opacity-100', 'visible', 'scale-100');
                }
            });
        }

        // Dark Mode Logic
        let themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        let themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
        let themeToggleDarkIconMobile = document.getElementById('theme-toggle-dark-icon-mobile');
        let themeToggleLightIconMobile = document.getElementById('theme-toggle-light-icon-mobile');

        // Change the icons inside the button based on previous settings
        function updateIcons() {
            if (document.documentElement.classList.contains('dark')) {
                if(themeToggleLightIcon) themeToggleLightIcon.classList.remove('hidden');
                if(themeToggleDarkIcon) themeToggleDarkIcon.classList.add('hidden');
                if(themeToggleLightIconMobile) themeToggleLightIconMobile.classList.remove('hidden');
                if(themeToggleDarkIconMobile) themeToggleDarkIconMobile.classList.add('hidden');
            } else {
                if(themeToggleLightIcon) themeToggleLightIcon.classList.add('hidden');
                if(themeToggleDarkIcon) themeToggleDarkIcon.classList.remove('hidden');
                if(themeToggleLightIconMobile) themeToggleLightIconMobile.classList.add('hidden');
                if(themeToggleDarkIconMobile) themeToggleDarkIconMobile.classList.remove('hidden');
            }
        }
        updateIcons();

        function toggleTheme() {
            // toggle icons inside button
            if(themeToggleDarkIcon) themeToggleDarkIcon.classList.toggle('hidden');
            if(themeToggleLightIcon) themeToggleLightIcon.classList.toggle('hidden');
            if(themeToggleDarkIconMobile) themeToggleDarkIconMobile.classList.toggle('hidden');
            if(themeToggleLightIconMobile) themeToggleLightIconMobile.classList.toggle('hidden');

            // if set via local storage previously
            if (localStorage.getItem('color-theme')) {
                if (localStorage.getItem('color-theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                }
            } else {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            }
        }

        let themeToggleBtn = document.getElementById('theme-toggle');
        let themeToggleBtnMobile = document.getElementById('theme-toggle-mobile');

        if(themeToggleBtn) {
            themeToggleBtn.addEventListener('click', toggleTheme);
        }
        if(themeToggleBtnMobile) {
            themeToggleBtnMobile.addEventListener('click', toggleTheme);
        }
    </script>
    {{-- PWA Service Worker Registration --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('Service Worker registered', reg))
                    .catch(err => console.error('Service Worker registration failed', err));
            });
            navigator.serviceWorker.addEventListener('controllerchange', () => {
                window.location.reload();
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
