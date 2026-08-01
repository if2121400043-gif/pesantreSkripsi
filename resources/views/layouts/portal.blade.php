<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Wali Santri') — PP Nurul Furqon</title>
    <meta name="description" content="@yield('meta_description', 'Portal Informasi & Pembayaran Wali Santri PP Nurul Furqon')">

    {{-- PWA Meta Tags --}}
    <meta name="theme-color" content="#065f46">
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
<body class="min-h-screen bg-surface-50 overflow-x-hidden text-surface-900 font-sans pb-20 md:pb-6">
    {{-- Skip to content (accessibility) --}}
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:top-4 focus:left-4 bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium">
        Langsung ke konten
    </a>

    <div class="min-h-screen flex flex-col" id="app-portal-layout">
        {{-- Top Bar (Wali Santri Brand & Profile) --}}
        <header class="sticky top-0 z-30 shadow-md border-b border-emerald-800" style="background-color: #065f46 !important; color: #ffffff !important;">
            <div class="max-w-screen-xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
                
                {{-- Left: Brand Logo & Title --}}
                <a href="{{ route('portal.beranda') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-white/15 border border-white/25 flex items-center justify-center text-amber-300 group-hover:scale-105 transition-transform">
                        <i data-lucide="user-check" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <div class="font-extrabold text-sm sm:text-base font-heading tracking-wide" style="color: #ffffff !important;">PORTAL WALI SANTRI</div>
                        <div class="text-[0.65rem] uppercase tracking-wider font-semibold" style="color: #d1fae5 !important;">PP Nurul Furqon</div>
                    </div>
                </a>

                {{-- Middle: Desktop Navigation Tabs --}}
                <nav class="hidden md:flex items-center gap-1.5 p-1 rounded-2xl border border-white/20 text-xs font-bold" style="background-color: rgba(255, 255, 255, 0.15) !important;">
                    <a href="{{ route('portal.beranda') }}" class="px-3.5 py-1.5 rounded-xl transition-all duration-200 flex items-center gap-1.5 {{ request()->routeIs('portal.beranda') ? 'bg-white text-emerald-900 font-black shadow-md' : 'text-white hover:bg-white/15' }}" style="{{ request()->routeIs('portal.beranda') ? 'color: #064e3b !important; background-color: #ffffff !important;' : 'color: #ffffff !important;' }}">
                        <i data-lucide="home" class="w-4 h-4"></i>
                        <span>Beranda</span>
                    </a>
                    <a href="{{ route('portal.tagihan') }}" class="px-3.5 py-1.5 rounded-xl transition-all duration-200 flex items-center gap-1.5 {{ request()->routeIs('portal.tagihan*') ? 'bg-white text-emerald-900 font-black shadow-md' : 'text-white hover:bg-white/15' }}" style="{{ request()->routeIs('portal.tagihan*') ? 'color: #064e3b !important; background-color: #ffffff !important;' : 'color: #ffffff !important;' }}">
                        <i data-lucide="wallet" class="w-4 h-4"></i>
                        <span>Tagihan & Bayar</span>
                    </a>
                    <a href="{{ route('portal.presensi') }}" class="px-3.5 py-1.5 rounded-xl transition-all duration-200 flex items-center gap-1.5 {{ request()->routeIs('portal.presensi*') ? 'bg-white text-emerald-900 font-black shadow-md' : 'text-white hover:bg-white/15' }}" style="{{ request()->routeIs('portal.presensi*') ? 'color: #064e3b !important; background-color: #ffffff !important;' : 'color: #ffffff !important;' }}">
                        <i data-lucide="calendar-check" class="w-4 h-4"></i>
                        <span>Presensi</span>
                    </a>
                    <a href="{{ route('portal.kedisiplinan') }}" class="px-3.5 py-1.5 rounded-xl transition-all duration-200 flex items-center gap-1.5 {{ request()->routeIs('portal.kedisiplinan*') ? 'bg-white text-emerald-900 font-black shadow-md' : 'text-white hover:bg-white/15' }}" style="{{ request()->routeIs('portal.kedisiplinan*') ? 'color: #064e3b !important; background-color: #ffffff !important;' : 'color: #ffffff !important;' }}">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                        <span>Kedisiplinan</span>
                    </a>
                </nav>

                {{-- Right: User Actions & Switch Role --}}
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('akun.ganti-peran') }}" title="Ganti Peran / Switch Role" class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition-colors border border-amber-300 shadow-sm flex items-center gap-1" style="color: #064e3b !important; background-color: #fbbf24 !important;">
                            <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                            <span class="hidden lg:inline">Ganti Peran</span>
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

        {{-- Main Content Container (Centered max-w-screen-xl with bottom padding for mobile bar) --}}
        <main class="flex-1 max-w-screen-xl w-full mx-auto px-4 sm:px-6 py-6 pb-20 md:pb-6" id="main-content">
            @yield('content')
        </main>

        {{-- Bulletproof 4-Column Grid Bottom Navigation Bar for Mobile Phones --}}
        <nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-surface-200 shadow-2xl h-16 px-1 grid grid-cols-4 items-center">
            
            {{-- Tab 1: Beranda --}}
            <a href="{{ route('portal.beranda') }}" class="flex flex-col items-center justify-center h-full w-full py-1 group relative">
                @if(request()->routeIs('portal.beranda'))
                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-emerald-700 text-white shadow-md transition-transform duration-200 group-active:scale-95">
                        <i data-lucide="home" class="w-4 h-4"></i>
                    </div>
                    <span class="text-[0.65rem] font-extrabold text-emerald-800 mt-1">Beranda</span>
                    <span class="absolute bottom-1 w-1 h-1 bg-emerald-600 rounded-full"></span>
                @else
                    <div class="flex items-center justify-center w-8 h-8 text-surface-400 group-hover:text-emerald-700 transition-colors">
                        <i data-lucide="home" class="w-4 h-4"></i>
                    </div>
                    <span class="text-[0.65rem] font-medium text-surface-500">Beranda</span>
                @endif
            </a>

            {{-- Tab 2: Tagihan --}}
            <a href="{{ route('portal.tagihan') }}" class="flex flex-col items-center justify-center h-full w-full py-1 group relative">
                @if(request()->routeIs('portal.tagihan*'))
                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-emerald-700 text-white shadow-md transition-transform duration-200 group-active:scale-95">
                        <i data-lucide="wallet" class="w-4 h-4"></i>
                    </div>
                    <span class="text-[0.65rem] font-extrabold text-emerald-800 mt-1">Tagihan</span>
                    <span class="absolute bottom-1 w-1 h-1 bg-emerald-600 rounded-full"></span>
                @else
                    <div class="flex items-center justify-center w-8 h-8 text-surface-400 group-hover:text-emerald-700 transition-colors">
                        <i data-lucide="wallet" class="w-4 h-4"></i>
                    </div>
                    <span class="text-[0.65rem] font-medium text-surface-500">Tagihan</span>
                @endif
            </a>

            {{-- Tab 3: Presensi --}}
            <a href="{{ route('portal.presensi') }}" class="flex flex-col items-center justify-center h-full w-full py-1 group relative">
                @if(request()->routeIs('portal.presensi*'))
                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-emerald-700 text-white shadow-md transition-transform duration-200 group-active:scale-95">
                        <i data-lucide="calendar-check" class="w-4 h-4"></i>
                    </div>
                    <span class="text-[0.65rem] font-extrabold text-emerald-800 mt-1">Presensi</span>
                    <span class="absolute bottom-1 w-1 h-1 bg-emerald-600 rounded-full"></span>
                @else
                    <div class="flex items-center justify-center w-8 h-8 text-surface-400 group-hover:text-emerald-700 transition-colors">
                        <i data-lucide="calendar-check" class="w-4 h-4"></i>
                    </div>
                    <span class="text-[0.65rem] font-medium text-surface-500">Presensi</span>
                @endif
            </a>

            {{-- Tab 4: Kedisiplinan --}}
            <a href="{{ route('portal.kedisiplinan') }}" class="flex flex-col items-center justify-center h-full w-full py-1 group relative">
                @if(request()->routeIs('portal.kedisiplinan*'))
                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-emerald-700 text-white shadow-md transition-transform duration-200 group-active:scale-95">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                    </div>
                    <span class="text-[0.65rem] font-extrabold text-emerald-800 mt-1">Poin</span>
                    <span class="absolute bottom-1 w-1 h-1 bg-emerald-600 rounded-full"></span>
                @else
                    <div class="flex items-center justify-center w-8 h-8 text-surface-400 group-hover:text-emerald-700 transition-colors">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                    </div>
                    <span class="text-[0.65rem] font-medium text-surface-500">Poin</span>
                @endif
            </a>

        </nav>

        {{-- Draggable WhatsApp Floating Action Button (FAB) --}}
        @php
            $rawPhone = \App\Models\Pesantren::first()->telepon ?? '6281234567890';
            $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);
            if (str_starts_with($cleanPhone, '0')) {
                $cleanPhone = '62' . substr($cleanPhone, 1);
            }
        @endphp

        <div id="draggable-wa" class="fixed bottom-20 right-4 sm:bottom-6 sm:right-6 z-50 touch-none cursor-grab active:cursor-grabbing">
            <a href="https://wa.me/{{ $cleanPhone }}?text=Halo%20Admin%20Pesantren%20Nurul%20Furqon,%20saya%20Wali%20Santri%20ingin%20bertanya" 
               target="_blank" 
               rel="noopener noreferrer" 
               id="wa-link"
               title="Bantuan WA Admin (Bisa digeser/pindah posisi)" 
               class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-[#25D366] hover:bg-[#20ba59] text-white shadow-2xl flex items-center justify-center border-2 border-white transform hover:scale-110 transition-transform">
                {{-- Official WhatsApp SVG Icon --}}
                <svg class="w-6 h-6 sm:w-7 sm:h-7 fill-current" viewBox="0 0 24 24">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-1.001 3.655 3.744-.988zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                </svg>
            </a>
        </div>

        {{-- Footer (Desktop) --}}
        <footer class="hidden md:block bg-white border-t border-surface-200 py-6 mt-auto">
            <div class="max-w-screen-xl mx-auto px-4 sm:px-6 text-center text-xs text-surface-500">
                &copy; {{ date('Y') }} PP Nurul Furqon — Portal Wali Santri.
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

            // Draggable WhatsApp Button Logic
            const waBtn = document.getElementById('draggable-wa');
            const waLink = document.getElementById('wa-link');

            if (waBtn && waLink) {
                let isDragging = false;
                let startX, startY, initialLeft, initialTop;
                let hasMoved = false;

                function onStart(e) {
                    isDragging = true;
                    hasMoved = false;
                    const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                    const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                    
                    startX = clientX;
                    startY = clientY;

                    const rect = waBtn.getBoundingClientRect();
                    initialLeft = rect.left;
                    initialTop = rect.top;

                    waBtn.style.bottom = 'auto';
                    waBtn.style.right = 'auto';
                    waBtn.style.left = initialLeft + 'px';
                    waBtn.style.top = initialTop + 'px';
                }

                function onMove(e) {
                    if (!isDragging) return;
                    const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                    const clientY = e.touches ? e.touches[0].clientY : e.clientY;

                    const deltaX = clientX - startX;
                    const deltaY = clientY - startY;

                    if (Math.abs(deltaX) > 5 || Math.abs(deltaY) > 5) {
                        hasMoved = true;
                    }

                    let newLeft = initialLeft + deltaX;
                    let newTop = initialTop + deltaY;

                    const maxLeft = window.innerWidth - waBtn.offsetWidth - 10;
                    const maxTop = window.innerHeight - waBtn.offsetHeight - 10;

                    newLeft = Math.max(10, Math.min(newLeft, maxLeft));
                    newTop = Math.max(10, Math.min(newTop, maxTop));

                    waBtn.style.left = newLeft + 'px';
                    waBtn.style.top = newTop + 'px';

                    if (e.cancelable) e.preventDefault();
                }

                function onEnd() {
                    isDragging = false;
                }

                waLink.addEventListener('click', function(e) {
                    if (hasMoved) {
                        e.preventDefault();
                    }
                });

                waBtn.addEventListener('mousedown', onStart);
                window.addEventListener('mousemove', onMove);
                window.addEventListener('mouseup', onEnd);

                waBtn.addEventListener('touchstart', onStart, { passive: false });
                window.addEventListener('touchmove', onMove, { passive: false });
                window.addEventListener('touchend', onEnd);
            }
        });
    </script>
</body>
</html>
