<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Bendahara') — PP Nurul Furqon</title>
    <meta name="description" content="@yield('meta_description', 'Portal Pengelolaan Keuangan & Tagihan PP Nurul Furqon')">

    {{-- PWA Meta Tags --}}
    <meta name="theme-color" content="#0d9488">
    <meta name="application-name" content="PP Nurul Furqon">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="PP Nurul Furqon">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192x192.png">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest" defer></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="min-h-screen bg-surface-50 overflow-x-hidden text-surface-900 font-sans">
    {{-- Skip to content (accessibility) --}}
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:top-4 focus:left-4 bg-teal-600 text-white px-4 py-2 rounded-lg font-medium">
        Langsung ke konten
    </a>

    <div class="min-h-screen flex flex-col" id="app-bendahara-layout">
        {{-- Top Bar (Bendahara Brand & User Profile) --}}
        <header class="bg-teal-900 text-white border-b border-teal-800 sticky top-0 z-30 shadow-md">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
                
                {{-- Left: Brand Logo & Title --}}
                <a href="{{ route('bendahara.dashboard') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center text-amber-300 group-hover:scale-105 transition-transform">
                        <i data-lucide="wallet" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <div class="font-extrabold text-sm sm:text-base text-white font-heading tracking-wide">PORTAL BENDAHARA</div>
                        <div class="text-[0.65rem] text-teal-200 uppercase tracking-wider">PP Nurul Furqon</div>
                    </div>
                </a>

                {{-- Right: User Actions & Switch Role --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('bendahara.dashboard') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-xs font-semibold text-white transition-colors border border-white/10">
                        <i data-lucide="home" class="w-4 h-4"></i> Beranda
                    </a>

                    {{-- User Dropdown / Role Badge --}}
                    <div class="flex items-center gap-2 pl-2 border-l border-teal-800">
                        <a href="{{ route('akun.ganti-peran') }}" title="Ganti Peran / Switch Role" class="px-2.5 py-1.5 rounded-xl bg-amber-500/20 text-amber-300 hover:bg-amber-500/30 text-xs font-bold transition-colors border border-amber-500/30 flex items-center gap-1">
                            <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                            <span class="hidden md:inline">Ganti Peran</span>
                        </a>

                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" title="Keluar" class="p-2 rounded-xl bg-rose-500/20 text-rose-300 hover:bg-rose-500/30 text-xs font-bold transition-colors border border-rose-500/30 flex items-center justify-center">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </header>

        {{-- Flash Messages --}}
        <div class="max-w-6xl mx-auto w-full px-4 sm:px-6 mt-4">
            @if(session('success'))
                <div class="p-4 mb-4 rounded-2xl bg-success-50 border border-success-200 text-success-800 flex items-center justify-between text-xs font-medium shadow-sm">
                    <div class="flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-5 h-5 text-success-600"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-success-600 hover:text-success-900"><i data-lucide="x" class="w-4 h-4"></i></button>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 mb-4 rounded-2xl bg-danger-50 border border-danger-200 text-danger-800 flex items-center justify-between text-xs font-medium shadow-sm">
                    <div class="flex items-center gap-2">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-danger-600"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-danger-600 hover:text-danger-900"><i data-lucide="x" class="w-4 h-4"></i></button>
                </div>
            @endif
        </div>

        {{-- Main Content Container (Centered max-w-6xl) --}}
        <main class="flex-1 max-w-6xl w-full mx-auto px-4 sm:px-6 py-6" id="main-content">
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="bg-white border-t border-surface-200 py-6 mt-auto">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 text-center text-xs text-surface-500">
                &copy; {{ date('Y') }} PP Nurul Furqon — Portal Pengelolaan Keuangan Bendahara.
            </div>
        </footer>
    </div>

    {{-- Toast Container Partial --}}
    @include('partials.toast-container')

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>
