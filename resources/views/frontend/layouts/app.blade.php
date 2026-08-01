<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('Beranda')) — {{ $pesantren?->nama ?? 'Pesantren' }}</title>
    <meta name="description" content="@yield('meta_description', ($pesantren?->nama ?? 'Pesantren') . ' — ' . __('Lembaga Pendidikan Islam'))">
    
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
    <link rel="mask-icon" href="/icons/icon-192x192.png" color="#065f46">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

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
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        html.dark .navbar-glass {
            background: rgba(15, 23, 42, 0.85); /* surface-950 equivalent */
        }
        .navbar-scrolled {
            box-shadow: 0 4px 20px -2px rgba(0,0,0,0.05);
        }
        html.dark .navbar-scrolled {
            box-shadow: 0 4px 20px -2px rgba(0,0,0,0.5);
        }
        
        /* Language Dropdown Animation */
        #langDropdown {
            transition: all 0.2s ease-in-out;
            transform-origin: top right;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-surface-50 text-surface-900 dark:bg-surface-950 dark:text-surface-200 antialiased transition-colors duration-300 selection:bg-primary-500 selection:text-white overflow-x-hidden">

    {{-- ═══════════ NAVBAR ═══════════ --}}
    <nav class="fixed top-0 inset-x-0 z-50 navbar-glass border-b border-surface-200/50 dark:border-surface-800/80 transition-all duration-300" id="mainNavbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-[80px]">

                {{-- Logo --}}
                <a href="{{ route('frontend.home') }}" class="flex items-center gap-3 flex-shrink-0 group">
                    <img src="{{ asset('images/logo-pesantren.webp') }}?v={{ time() }}" alt="Logo" class="w-12 h-12 object-contain bg-white rounded-2xl shadow-lg shadow-primary-500/30 p-0.5 group-hover:shadow-primary-500/50 transition-all duration-300 transform group-hover:-translate-y-0.5">
                    <div class="hidden sm:block leading-tight">
                        <span class="block font-extrabold text-surface-900 dark:text-white text-[17px] tracking-tight">{{ $pesantren?->nama ?? 'Pesantren' }}</span>
                        <span class="block text-[11px] text-primary-600 dark:text-primary-400 font-bold uppercase tracking-widest mt-0.5">{{ __('Lembaga Pendidikan Islam') }}</span>
                    </div>
                </a>

                {{-- Desktop Menu --}}
                <div class="hidden lg:flex items-center gap-2">
                    <a href="{{ route('frontend.home') }}" class="px-4 py-2 text-sm font-semibold rounded-xl transition-all duration-300 {{ request()->routeIs('frontend.home') ? 'text-primary-700 bg-primary-50 dark:bg-primary-900/30 dark:text-primary-400' : 'text-surface-600 dark:text-surface-400 hover:text-surface-900 dark:hover:text-white hover:bg-surface-100 dark:hover:bg-surface-800' }}">{{ __('Beranda') }}</a>
                    
                    <a href="{{ route('frontend.profil') }}" class="px-4 py-2 text-sm font-semibold rounded-xl transition-all duration-300 {{ request()->routeIs('frontend.profil') ? 'text-primary-700 bg-primary-50 dark:bg-primary-900/30 dark:text-primary-400' : 'text-surface-600 dark:text-surface-400 hover:text-surface-900 dark:hover:text-white hover:bg-surface-100 dark:hover:bg-surface-800' }}">{{ __('Profil') }}</a>
                    
                    <a href="{{ route('frontend.berita') }}" class="px-4 py-2 text-sm font-semibold rounded-xl transition-all duration-300 {{ request()->routeIs('frontend.berita*') ? 'text-primary-700 bg-primary-50 dark:bg-primary-900/30 dark:text-primary-400' : 'text-surface-600 dark:text-surface-400 hover:text-surface-900 dark:hover:text-white hover:bg-surface-100 dark:hover:bg-surface-800' }}">{{ __('Berita') }}</a>
                </div>

                {{-- Action Area (Theme, Lang, CTA) --}}
                <div class="hidden lg:flex items-center gap-3">
                    
                    {{-- Language Switcher --}}
                    <div class="relative">
                        <button id="langBtn" class="flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-surface-600 dark:text-surface-400 hover:text-surface-900 dark:hover:text-white rounded-xl hover:bg-surface-100 dark:hover:bg-surface-800 transition-colors">
                            <i data-lucide="globe" class="w-4 h-4"></i>
                            <span>{{ strtoupper(app()->getLocale()) }}</span>
                            <i data-lucide="chevron-down" class="w-3 h-3"></i>
                        </button>
                        {{-- Dropdown --}}
                        <div id="langDropdown" class="absolute right-0 mt-2 w-32 bg-white dark:bg-surface-900 rounded-xl shadow-dropdown border border-surface-100 dark:border-surface-800 opacity-0 invisible transform scale-95 z-50">
                            <div class="p-1.5 space-y-1">
                                <a href="{{ route('lang.switch', 'id') }}" class="block px-3 py-2 text-sm font-medium rounded-lg hover:bg-primary-50 dark:hover:bg-surface-800 text-surface-700 dark:text-surface-300 hover:text-primary-700 dark:hover:text-primary-400 transition-colors {{ app()->getLocale() == 'id' ? 'bg-primary-50/50 dark:bg-surface-800/50 text-primary-700 dark:text-primary-400' : '' }}">🇮🇩 Indonesia</a>
                                <a href="{{ route('lang.switch', 'en') }}" class="block px-3 py-2 text-sm font-medium rounded-lg hover:bg-primary-50 dark:hover:bg-surface-800 text-surface-700 dark:text-surface-300 hover:text-primary-700 dark:hover:text-primary-400 transition-colors {{ app()->getLocale() == 'en' ? 'bg-primary-50/50 dark:bg-surface-800/50 text-primary-700 dark:text-primary-400' : '' }}">🇬🇧 English</a>
                            </div>
                        </div>
                    </div>

                    {{-- Dark Mode Toggle --}}
                    <button id="theme-toggle" type="button" class="text-surface-500 dark:text-surface-400 hover:bg-surface-100 dark:hover:bg-surface-800 focus:outline-none focus:ring-4 focus:ring-surface-200 dark:focus:ring-surface-700 rounded-xl text-sm p-2.5 transition-colors">
                        <i id="theme-toggle-dark-icon" data-lucide="moon" class="hidden w-4 h-4"></i>
                        <i id="theme-toggle-light-icon" data-lucide="sun" class="hidden w-4 h-4"></i>
                    </button>

                    <div class="h-6 w-px bg-surface-200 dark:bg-surface-800 mx-1"></div>

                    @auth
                        @php
                            $userRedirect = auth()->user()->active_role->role->redirect_url ?? '/portal/beranda';
                        @endphp
                        <a href="{{ url($userRedirect) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300 hover:bg-emerald-200 text-sm font-extrabold rounded-xl transition-all duration-300 shadow-sm">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                            <span>Dashboard Saya</span>
                        </a>
                    @else
                        <a href="/login" class="inline-flex items-center gap-1.5 px-4 py-2.5 text-surface-700 dark:text-surface-300 hover:text-primary-600 dark:hover:text-primary-400 text-sm font-bold transition-all duration-300">
                            <i data-lucide="log-in" class="w-4 h-4"></i>
                            {{ __('Masuk') }}
                        </a>
                    @endauth

                    <div class="h-6 w-px bg-surface-200 dark:bg-surface-800 mx-1"></div>

                    <a href="/psb" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary-600 to-secondary-600 hover:from-primary-500 hover:to-secondary-500 text-white text-sm font-bold rounded-xl shadow-lg shadow-primary-600/25 hover:shadow-primary-600/40 transition-all duration-300 hover:-translate-y-0.5">
                        <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                        {{ __('Daftar PSB') }}
                    </a>
                </div>

                {{-- Mobile Hamburger --}}
                <div class="flex items-center gap-2 lg:hidden">
                    <button id="theme-toggle-mobile" type="button" class="text-surface-500 dark:text-surface-400 hover:bg-surface-100 dark:hover:bg-surface-800 rounded-xl p-2 transition-colors">
                        <i id="theme-toggle-dark-icon-mobile" data-lucide="moon" class="hidden w-5 h-5"></i>
                        <i id="theme-toggle-light-icon-mobile" data-lucide="sun" class="hidden w-5 h-5"></i>
                    </button>
                    
                    <button id="mobileMenuBtn" class="p-2 text-surface-600 dark:text-surface-300 hover:text-surface-900 dark:hover:text-white rounded-xl hover:bg-surface-100 dark:hover:bg-surface-800 transition-colors">
                        <i data-lucide="menu" class="w-6 h-6" id="menuIconOpen"></i>
                        <i data-lucide="x" class="w-6 h-6 hidden" id="menuIconClose"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Dropdown --}}
        <div id="mobileMenu" class="hidden lg:hidden bg-white/95 dark:bg-surface-950/95 backdrop-blur-xl border-t border-surface-100 dark:border-surface-800 shadow-xl absolute w-full">
            <div class="max-w-7xl mx-auto px-4 py-5 space-y-2">
                <a href="{{ route('frontend.home') }}" class="block px-4 py-3 rounded-xl text-sm font-bold {{ request()->routeIs('frontend.home') ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400' : 'text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-800' }}">{{ __('Beranda') }}</a>
                <a href="{{ route('frontend.profil') }}" class="block px-4 py-3 rounded-xl text-sm font-bold {{ request()->routeIs('frontend.profil') ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400' : 'text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-800' }}">{{ __('Profil') }}</a>
                <a href="{{ route('frontend.berita') }}" class="block px-4 py-3 rounded-xl text-sm font-bold {{ request()->routeIs('frontend.berita*') ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400' : 'text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-800' }}">{{ __('Berita') }}</a>
                <a href="{{ route('frontend.psb') }}" class="block px-4 py-3 rounded-xl text-sm font-bold {{ request()->routeIs('frontend.psb*') ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400' : 'text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-800' }}">{{ __('Info PSB') }}</a>
                
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
                        <a href="{{ url($userRedirect) }}" class="block w-full text-center px-4 py-3 bg-emerald-600 text-white font-extrabold rounded-xl shadow-md">Dashboard Saya</a>
                    @else
                        <a href="/login" class="block w-full text-center px-4 py-3 border border-surface-200 dark:border-surface-800 text-surface-700 dark:text-surface-300 font-bold rounded-xl">{{ __('Masuk') }}</a>
                    @endauth
                    <a href="/psb" class="block w-full text-center px-4 py-3.5 bg-gradient-to-r from-primary-600 to-secondary-600 text-white font-bold rounded-xl shadow-lg shadow-primary-600/20">{{ __('Daftar PSB Sekarang') }}</a>
                </div>
            </div>
        </div>
    </nav>

    {{-- ═══════════ MAIN ═══════════ --}}
    <main class="mobile-safe pt-[80px] min-h-screen">
        @yield('content')
    </main>

    {{-- ═══════════ FOOTER ═══════════ --}}
    <footer class="bg-surface-950 dark:bg-[#0a0f1a] text-surface-300 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
                {{-- Col 1: About --}}
                <div class="lg:col-span-1">
                    <div class="flex items-center gap-3 mb-6">
                        <img src="{{ asset('images/logo-pesantren.webp') }}?v={{ time() }}" alt="Logo" class="w-12 h-12 object-contain bg-white rounded-2xl shadow-lg shadow-primary-500/20 p-0.5">
                        <span class="text-white font-extrabold text-[17px] tracking-tight">{{ $pesantren?->nama ?? 'Pesantren' }}</span>
                    </div>
                    <p class="text-surface-400 text-sm leading-relaxed font-medium">
                        {{ __('Lembaga Pendidikan Islam') }} terpadu yang menyeimbangkan ilmu agama, akademik, dan pembentukan akhlak santri di era modern.
                    </p>
                </div>

                {{-- Col 2: Pendidikan --}}
                <div>
                    <h4 class="text-white font-bold text-sm uppercase tracking-widest mb-6 border-b border-surface-800 pb-2 inline-block">{{ __('Pendidikan') }}</h4>
                    <ul class="space-y-3 text-sm font-medium">
                        <li><a href="{{ route('frontend.profil') }}" class="text-surface-400 hover:text-accent-400 transition-colors flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-accent-500"></span> {{ __('Profil Pesantren') }}</a></li>
                        <li><a href="{{ route('frontend.berita') }}" class="text-surface-400 hover:text-accent-400 transition-colors flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-accent-500"></span> {{ __('Berita & Kegiatan') }}</a></li>
                    </ul>
                </div>

                {{-- Col 3: Informasi --}}
                <div>
                    <h4 class="text-white font-bold text-sm uppercase tracking-widest mb-6 border-b border-surface-800 pb-2 inline-block">{{ __('Informasi') }}</h4>
                    <ul class="space-y-3 text-sm font-medium">
                        <li><a href="{{ route('frontend.psb') }}" class="text-surface-400 hover:text-accent-400 transition-colors flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-accent-500"></span> {{ __('Pendaftaran Santri Baru') }}</a></li>
                        <li><a href="{{ route('login') }}" class="text-surface-400 hover:text-accent-400 transition-colors flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-accent-500"></span> {{ __('Portal Login') }}</a></li>
                    </ul>
                </div>

                {{-- Col 4: Kontak --}}
                <div>
                    <h4 class="text-white font-bold text-sm uppercase tracking-widest mb-6 border-b border-surface-800 pb-2 inline-block">{{ __('Kontak') }}</h4>
                    <ul class="space-y-4 text-sm font-medium text-surface-400">
                        @if($pesantren?->alamat)
                        <li class="flex items-start gap-3 group">
                            <div class="w-8 h-8 rounded-full bg-surface-900 flex items-center justify-center flex-shrink-0 group-hover:bg-primary-900 transition-colors text-primary-400">
                                <i data-lucide="map-pin" class="w-4 h-4"></i>
                            </div>
                            <span class="pt-1.5">{{ $pesantren->alamat }}</span>
                        </li>
                        @endif
                        @if($pesantren?->telepon)
                        <li class="flex items-center gap-3 group">
                            <div class="w-8 h-8 rounded-full bg-surface-900 flex items-center justify-center flex-shrink-0 group-hover:bg-primary-900 transition-colors text-primary-400">
                                <i data-lucide="phone" class="w-4 h-4"></i>
                            </div>
                            <span>{{ $pesantren->telepon }}</span>
                        </li>
                        @endif
                        @if($pesantren?->email)
                        <li class="flex items-center gap-3 group">
                            <div class="w-8 h-8 rounded-full bg-surface-900 flex items-center justify-center flex-shrink-0 group-hover:bg-primary-900 transition-colors text-primary-400">
                                <i data-lucide="mail" class="w-4 h-4"></i>
                            </div>
                            <span>{{ $pesantren->email }}</span>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        {{-- Copyright --}}
        <div class="border-t border-surface-800/60 relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col md:flex-row justify-between items-center text-xs font-medium text-surface-500">
                <p>&copy; {{ date('Y') }} <span class="text-surface-300">{{ $pesantren?->nama ?? 'Pesantren' }}</span>. {{ __('All rights reserved.') }}</p>
                <p class="mt-2 md:mt-0 flex items-center gap-1.5">
                    {{ __('Sistem Manajemen Pesantren') }} <i data-lucide="sparkles" class="w-3 h-3 text-accent-500"></i>
                </p>
            </div>
        </div>
    </footer>

    <script>
        // Init icons
        lucide.createIcons();

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
        var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
        var themeToggleDarkIconMobile = document.getElementById('theme-toggle-dark-icon-mobile');
        var themeToggleLightIconMobile = document.getElementById('theme-toggle-light-icon-mobile');

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

        var themeToggleBtn = document.getElementById('theme-toggle');
        var themeToggleBtnMobile = document.getElementById('theme-toggle-mobile');

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
