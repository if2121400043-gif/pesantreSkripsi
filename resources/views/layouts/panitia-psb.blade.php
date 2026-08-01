<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Panitia PSB') — PP Nurul Furqon</title>
    <meta name="description" content="@yield('meta_description', 'Portal Penerimaan Santri Baru PP Nurul Furqon')">

    {{-- PWA Meta Tags --}}
    <meta name="theme-color" content="#4c1d95">
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
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:top-4 focus:left-4 bg-purple-700 text-white px-4 py-2 rounded-lg font-medium">
        Langsung ke konten
    </a>

    <div class="min-h-screen flex flex-col" id="app-panitia-psb-layout">
        {{-- Top Bar (Panitia PSB Brand & User Profile) --}}
        <header class="sticky top-0 z-30 shadow-md border-b border-purple-900" style="background-color: #4c1d95 !important; color: #ffffff !important;">
            <div class="max-w-screen-xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
                
                {{-- Left: Brand Logo & Title --}}
                <a href="{{ route('panitia-psb.dashboard') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-white/15 border border-white/25 flex items-center justify-center text-amber-300 group-hover:scale-105 transition-transform">
                        <i data-lucide="user-plus" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <div class="font-extrabold text-sm sm:text-base font-heading tracking-wide" style="color: #ffffff !important;">PORTAL PANITIA PSB</div>
                        <div class="text-[0.65rem] uppercase tracking-wider font-semibold" style="color: #ddd6fe !important;">PP Nurul Furqon</div>
                    </div>
                </a>

                {{-- Right: User Actions & Switch Role --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('panitia-psb.dashboard') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/15 hover:bg-white/25 text-xs font-bold transition-colors border border-white/20" style="color: #ffffff !important;">
                        <i data-lucide="home" class="w-4 h-4"></i> Beranda
                    </a>

                    {{-- User Dropdown / Role Badge --}}
                    <div class="flex items-center gap-2 pl-2 border-l border-white/20">
                        <a href="{{ route('akun.ganti-peran') }}" title="Ganti Peran / Switch Role" class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition-colors border border-amber-300 shadow-sm flex items-center gap-1" style="color: #4c1d95 !important; background-color: #fbbf24 !important;">
                            <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                            <span class="hidden md:inline">Ganti Peran</span>
                        </a>

                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" title="Keluar" class="p-2 rounded-xl bg-rose-500 text-white hover:bg-rose-600 text-xs font-bold transition-colors shadow-sm flex items-center justify-center" style="color: #ffffff !important; background-color: #e11d48 !important;">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </header>

        {{-- Flash Messages --}}
        <div class="max-w-screen-xl mx-auto w-full px-4 sm:px-6 mt-4">
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

        {{-- Main Content Container (Centered max-w-screen-xl) --}}
        <main class="flex-1 max-w-screen-xl w-full mx-auto px-4 sm:px-6 py-6" id="main-content">
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="bg-white border-t border-surface-200 py-6 mt-auto">
            <div class="max-w-screen-xl mx-auto px-4 sm:px-6 text-center text-xs text-surface-500">
                &copy; {{ date('Y') }} PP Nurul Furqon — Portal Panitia Penerimaan Santri Baru (PSB).
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
