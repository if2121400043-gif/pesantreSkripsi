@php
    $notifications = [];
    $homeUrl = '/';
    $user = auth()->user();
    
    $activeTahun = \App\Models\TahunPelajaran::where('is_active', true)->first();
    $activeTaNama = $activeTahun?->nama ?? null;
    
    if ($user) {
        $roleName = $user->active_role->role->nama ?? '';
        $homeUrl = $user->active_role->role->redirect_url ?? '/';

        if ($roleName === 'WALI_SANTRI') {
            $anakIds = \App\Models\HubunganKeluarga::where('keluarga_id', $user->orang_id)
                ->pluck('orang_id');
                
            $pesertaDidikIds = \App\Models\PesertaDidik::whereIn('orang_id', $anakIds)->pluck('id');

            $recentPembayaran = \App\Models\Pembayaran::whereHas('tagihan', function($q) use ($pesertaDidikIds) {
                    $q->whereIn('peserta_didik_id', $pesertaDidikIds);
                })
                ->with(['tagihan.pesertaDidik.orang', 'tagihan.komponenBiaya'])
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();

            foreach ($recentPembayaran as $p) {
                $notifications[] = [
                    'icon' => 'wallet',
                    'icon_bg' => 'bg-success-50 text-success-600',
                    'title' => 'Pembayaran Diterima',
                    'message' => 'Pembayaran Rp ' . number_format($p->jumlah, 0, ',', '.') . ' untuk ' . ($p->tagihan->komponenBiaya->nama ?? 'SPP') . ' ananda ' . ($p->tagihan->pesertaDidik->orang->nama_lengkap ?? '') . ' telah diverifikasi.',
                    'time' => $p->created_at->diffForHumans(),
                    'link' => route('portal.beranda') . '?tab=tagihan',
                    'time_stamp' => $p->created_at,
                ];
            }

            $recentPelanggaran = \App\Models\CatatanPelanggaran::whereIn('peserta_didik_id', $pesertaDidikIds)
                ->with(['pesertaDidik.orang', 'jenisPelanggaran'])
                ->orderBy('created_at', 'desc')
                ->limit(2)
                ->get();

            foreach ($recentPelanggaran as $p) {
                $notifications[] = [
                    'icon' => 'alert-triangle',
                    'icon_bg' => 'bg-danger-50 text-danger-600',
                    'title' => 'Catatan Pelanggaran',
                    'message' => 'Ananda ' . ($p->pesertaDidik->orang->nama_lengkap ?? '') . ' mencatat pelanggaran: ' . ($p->jenisPelanggaran->nama ?? 'Tata Tertib') . ' (+' . ($p->jenisPelanggaran->poin ?? 0) . ' poin).',
                    'time' => $p->created_at->diffForHumans(),
                    'link' => route('portal.beranda') . '?tab=kedisiplinan',
                    'time_stamp' => $p->created_at,
                ];
            }
        } elseif ($roleName === 'BENDAHARA') {
            $recentPembayaran = \App\Models\Pembayaran::with(['tagihan.pesertaDidik.orang', 'tagihan.komponenBiaya'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            foreach ($recentPembayaran as $p) {
                $notifications[] = [
                    'icon' => 'wallet',
                    'icon_bg' => 'bg-success-50 text-success-600',
                    'title' => 'Pembayaran Baru',
                    'message' => 'Pembayaran Rp ' . number_format($p->jumlah, 0, ',', '.') . ' dari ' . ($p->tagihan->pesertaDidik->orang->nama_lengkap ?? 'Santri') . ' (' . ($p->tagihan->komponenBiaya->nama ?? 'SPP') . ') telah diinput.',
                    'time' => $p->created_at->diffForHumans(),
                    'link' => url('/bendahara/dashboard'),
                    'time_stamp' => $p->created_at,
                ];
            }
        } else {
            $recentCalon = \App\Models\CalonSantri::orderBy('created_at', 'desc')->limit(3)->get();
            foreach ($recentCalon as $c) {
                $notifications[] = [
                    'icon' => 'user-plus',
                    'icon_bg' => 'bg-primary-50 text-primary-600',
                    'title' => 'Pendaftaran PSB',
                    'message' => 'Calon santri baru terdaftar: ' . $c->nama_lengkap,
                    'time' => $c->created_at->diffForHumans(),
                    'link' => url('/admin/peserta-didik'),
                    'time_stamp' => $c->created_at,
                ];
            }

            $recentPembayaran = \App\Models\Pembayaran::with(['tagihan.pesertaDidik.orang'])
                ->orderBy('created_at', 'desc')
                ->limit(2)
                ->get();

            foreach ($recentPembayaran as $p) {
                $notifications[] = [
                    'icon' => 'wallet',
                    'icon_bg' => 'bg-success-50 text-success-600',
                    'title' => 'Pembayaran SPP',
                    'message' => 'Pembayaran Rp ' . number_format($p->jumlah, 0, ',', '.') . ' dari ' . ($p->tagihan->pesertaDidik->orang->nama_lengkap ?? 'Santri'),
                    'time' => $p->created_at->diffForHumans(),
                    'link' => route('admin.tagihan.index'),
                    'time_stamp' => $p->created_at,
                ];
            }
        }
    }

    usort($notifications, function($a, $b) {
        $ta = $a['time_stamp']?->timestamp ?? 0;
        $tb = $b['time_stamp']?->timestamp ?? 0;
        return $tb <=> $ta;
    });

    $notifications = array_slice($notifications, 0, 5);
@endphp

{{-- Top Bar --}}
<header class="sticky top-0 z-30 bg-white/80 backdrop-blur-lg border-b border-surface-100" id="topbar">
    <div class="flex items-center justify-between h-14 sm:h-16 px-3 sm:px-4 lg:px-8">
        {{-- Left: Hamburger + Breadcrumb --}}
        <div class="flex items-center gap-2 sm:gap-4 min-w-0">
            {{-- Mobile menu toggle --}}
            <button type="button"
                    class="p-2 rounded-lg text-surface-500 hover:bg-surface-100 hover:text-surface-700 transition-colors md:hidden"
                    id="btn-toggle-sidebar"
                    aria-label="Toggle menu">
                <i data-lucide="menu" class="w-5 h-5"></i>
            </button>

            {{-- Breadcrumb --}}
            <nav aria-label="Breadcrumb" class="hidden sm:flex items-center gap-1.5 text-sm min-w-0">
                @if(($roleName ?? '') === 'SUPER_ADMIN')
                    <a href="{{ url($homeUrl) }}" class="text-surface-400 hover:text-primary-600 transition-colors">
                        <i data-lucide="home" class="w-4 h-4"></i>
                    </a>
                @endif
                @hasSection('breadcrumb')
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-surface-300"></i>
                    @yield('breadcrumb')
                @endif
            </nav>
        </div>

        {{-- Right: Search, Notifications, User Menu --}}
        <div class="flex items-center gap-1.5 sm:gap-2 min-w-0">
            {{-- Global Search Button --}}
            <button type="button"
                    class="flex items-center justify-center sm:justify-start gap-2 px-2.5 sm:px-3 py-1.5 rounded-lg bg-surface-100 text-surface-400 text-sm hover:bg-surface-200 transition-colors"
                    id="btn-global-search"
                    aria-label="Buka pencarian">
                <i data-lucide="search" class="w-4 h-4 text-surface-500"></i>
                <span class="hidden lg:inline text-surface-600">Cari...</span>
                <kbd class="hidden lg:inline-flex items-center gap-0.5 px-1.5 py-0.5 text-[0.625rem] font-medium bg-white rounded border border-surface-200 text-surface-400 shadow-2xs">
                    Ctrl K
                </kbd>
            </button>

            {{-- Install PWA Button (topbar integration) --}}
            <button id="btn-install-pwa" type="button" class="hidden flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-bold hover:bg-emerald-100 transition-colors" title="Install Aplikasi ke HP">
                <i data-lucide="download" class="w-3.5 h-3.5"></i>
                <span class="hidden sm:inline">Install App</span>
            </button>

            {{-- Tahun Pelajaran Active --}}
            @if($activeTaNama)
            <div class="hidden md:flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-800 text-xs font-semibold border border-emerald-100" id="active-ta">
                <i data-lucide="calendar" class="w-3.5 h-3.5 text-emerald-600"></i>
                <span>TA {{ $activeTaNama }}</span>
            </div>
            @endif
            {{-- Notifications Wrapper --}}
            <div class="relative" id="notification-wrapper">
                <button type="button"
                        class="relative p-2 rounded-lg text-surface-500 hover:bg-surface-100 hover:text-surface-700 transition-colors"
                        id="btn-notifications"
                        aria-label="Notifikasi">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                    @if(count($notifications) > 0)
                        <span class="absolute top-1 right-1 w-2 h-2 bg-danger-500 rounded-full animate-pulse" id="notification-dot"></span>
                    @endif
                </button>

                {{-- Notification Dropdown --}}
                <div id="notification-dropdown"
                     class="hidden absolute right-0 top-12 z-50 w-80 bg-white rounded-2xl shadow-2xl border border-surface-200 animate-scale-in overflow-hidden">
                    <div class="px-4 py-3 border-b border-surface-100 flex items-center justify-between bg-surface-50/50">
                        <h3 class="font-bold text-sm text-surface-900 font-heading">Notifikasi</h3>
                        @if(count($notifications) > 0)
                            <span class="px-2 py-0.5 text-xs font-bold bg-emerald-100 text-emerald-800 rounded-full">{{ count($notifications) }} baru</span>
                        @endif
                    </div>
                    
                    <div class="max-h-72 overflow-y-auto divide-y divide-surface-100">
                        @forelse($notifications as $notif)
                            <a href="{{ $notif['link'] }}" class="flex gap-3 px-4 py-3 hover:bg-surface-50 transition-colors">
                                <div class="w-8 h-8 rounded-xl {{ $notif['icon_bg'] }} flex items-center justify-center shrink-0 shadow-2xs">
                                    <i data-lucide="{{ $notif['icon'] }}" class="w-4 h-4"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold text-surface-800 truncate">{{ $notif['title'] }}</p>
                                    <p class="text-xs text-surface-600 mt-0.5 break-words leading-relaxed">{{ $notif['message'] }}</p>
                                    <p class="text-[0.65rem] text-surface-400 mt-1 flex items-center gap-1">
                                        <i data-lucide="clock" class="w-3 h-3"></i>
                                        <span>{{ $notif['time'] }}</span>
                                    </p>
                                </div>
                            </a>
                        @empty
                            <div class="px-4 py-8 text-center text-surface-500">
                                <i data-lucide="bell-off" class="w-8 h-8 mx-auto mb-2 text-surface-300"></i>
                                <p class="text-xs font-medium">Tidak ada notifikasi baru</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- User Menu Wrapper --}}
            <div class="relative z-50" id="user-menu-wrapper">
                <button type="button"
                        class="flex items-center gap-2 p-1.5 pr-2.5 rounded-xl hover:bg-surface-100 transition-colors border border-transparent hover:border-surface-200"
                        id="btn-user-menu"
                        aria-label="Menu pengguna">
                    <div class="w-8 h-8 rounded-lg bg-emerald-700 text-white flex items-center justify-center text-xs font-bold shadow-2xs">
                        {{ strtoupper(substr(auth()->user()->orang->nama_lengkap ?? auth()->user()->username ?? 'A', 0, 1)) }}
                    </div>
                    <span class="hidden md:inline text-xs font-bold text-surface-800">{{ auth()->user()->orang->nama_lengkap ?? auth()->user()->username ?? 'Pengguna' }}</span>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-surface-400 hidden md:inline"></i>
                </button>

                {{-- User Dropdown --}}
                <div id="user-dropdown"
                     class="hidden absolute right-0 top-12 z-50 w-60 bg-white rounded-2xl shadow-2xl border border-surface-200 animate-scale-in overflow-hidden">
                    <div class="px-4 py-3 border-b border-surface-100 bg-surface-50/50">
                        <p class="text-xs font-bold text-surface-900 font-heading">{{ auth()->user()->orang->nama_lengkap ?? auth()->user()->username ?? 'Pengguna' }}</p>
                        <p class="text-[0.7rem] text-surface-500 truncate mt-0.5">{{ auth()->user()->email ?? auth()->user()->username }}</p>
                    </div>
                    <div class="py-1 text-xs font-medium">
                        <a href="{{ route('akun.profil') }}" class="flex items-center gap-2.5 px-4 py-2 text-surface-700 hover:bg-emerald-50 hover:text-emerald-800 transition-colors">
                            <i data-lucide="user" class="w-4 h-4 text-surface-400"></i> Profil Saya
                        </a>
                        <a href="{{ route('akun.ganti-password') }}" class="flex items-center gap-2.5 px-4 py-2 text-surface-700 hover:bg-emerald-50 hover:text-emerald-800 transition-colors">
                            <i data-lucide="key" class="w-4 h-4 text-surface-400"></i> Ganti Password
                        </a>
                        <a href="{{ route('akun.ganti-peran') }}" class="flex items-center gap-2.5 px-4 py-2 text-surface-700 hover:bg-emerald-50 hover:text-emerald-800 transition-colors">
                            <i data-lucide="repeat" class="w-4 h-4 text-surface-400"></i> Ganti Peran
                        </a>
                    </div>
                    <div class="border-t border-surface-100 py-1">
                        <form method="POST" action="{{ url('/logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-xs font-bold text-danger-600 hover:bg-danger-50 transition-colors">
                                <i data-lucide="log-out" class="w-4 h-4 text-danger-500"></i> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

{{-- Global Live Search Modal --}}
<div id="global-search-modal" class="hidden fixed inset-0 z-[70] flex items-start justify-center px-4 pt-16 sm:pt-24 pb-8 bg-black/50 backdrop-blur-md transition-all">
    <div class="w-full max-w-2xl rounded-2xl bg-white shadow-2xl border border-surface-200 overflow-hidden flex flex-col max-h-[80vh]">
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

{{-- Topbar Self-Contained JavaScript --}}
<script>
    (() => {
        // Navigation Index for Global Live Search
        const searchIndex = [
            { title: 'Dashboard Utama', category: 'Navigasi', url: '/admin/dashboard', icon: 'layout-dashboard' },
            { title: 'Data Peserta Didik / Santri', category: 'Kepesantrenan', url: '/admin/peserta-didik', icon: 'users' },
            { title: 'Data Pegawai & Guru', category: 'Kepegawaian', url: '/admin/pegawai', icon: 'briefcase' },
            { title: 'Pendaftaran Calon Santri (PSB)', category: 'PSB', url: '/admin/calon-santri', icon: 'user-plus' },
            { title: 'Gelombang PSB', category: 'PSB', url: '/admin/gelombang-psb', icon: 'layers' },
            { title: 'Rombongan Belajar (Rombel/Kelas)', category: 'Akademik', url: '/admin/rombel', icon: 'school' },
            { title: 'Mata Pelajaran (Mapel)', category: 'Akademik', url: '/admin/mata-pelajaran', icon: 'book-open' },
            { title: 'Jadwal Pelajaran', category: 'Akademik', url: '/admin/jadwal-pelajaran', icon: 'calendar' },
            { title: 'Presensi Kelas (Absensi)', category: 'Akademik', url: '/admin/presensi', icon: 'check-square' },
            { title: 'Penilaian & Input Nilai', category: 'Akademik', url: '/admin/penilaian', icon: 'file-text' },
            { title: 'Data Asrama & Kamar', category: 'Kepesantrenan', url: '/admin/asrama', icon: 'home' },
            { title: 'Penempatan Asrama Santri', category: 'Kepesantrenan', url: '/admin/penempatan-asrama', icon: 'map-pin' },
            { title: 'Data Lembaga Pendidikan', category: 'Pengaturan', url: '/admin/lembaga', icon: 'building' },
            { title: 'Komponen Biaya SPP', category: 'Keuangan', url: '/admin/komponen-biaya', icon: 'tag' },
            { title: 'Tagihan SPP & Keuangan', category: 'Keuangan', url: '/admin/tagihan', icon: 'wallet' },
            { title: 'Catatan Pelanggaran Kedisiplinan', category: 'Kedisiplinan', url: '/admin/catatan-pelanggaran', icon: 'alert-triangle' },
            { title: 'Catatan Prestasi Santri', category: 'Kedisiplinan', url: '/admin/catatan-prestasi', icon: 'award' },
            { title: 'Perizinan Keluar Santri', category: 'Kedisiplinan', url: '/admin/perizinan', icon: 'file-check' },
            { title: 'Berita Pesantren', category: 'Frontend', url: '/admin/berita', icon: 'newspaper' },
            { title: 'Galeri Media (Foto & Video)', category: 'Frontend', url: '/admin/media', icon: 'image' },
            { title: 'Broadcast WhatsApp Fonnte', category: 'Komunikasi', url: '/admin/broadcast-wa', icon: 'message-square' },
            { title: 'Data Wilayah (Provinsi/Kab/Kec/Desa)', category: 'Pengaturan', url: '/admin/wilayah', icon: 'globe' },
            { title: 'Konfigurasi Profil Pesantren', category: 'Pengaturan', url: '/admin/konfigurasi', icon: 'settings' },
            { title: 'Manajemen Pengguna & Role RBAC', category: 'Pengaturan', url: '/admin/users', icon: 'shield' },
        ];

        document.addEventListener('DOMContentLoaded', () => {
            // Elements
            const btnSidebar = document.getElementById('btn-toggle-sidebar');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            const btnNotif = document.getElementById('btn-notifications');
            const notifDropdown = document.getElementById('notification-dropdown');

            const btnUser = document.getElementById('btn-user-menu');
            const userDropdown = document.getElementById('user-dropdown');

            const btnSearch = document.getElementById('btn-global-search');
            const searchModal = document.getElementById('global-search-modal');
            const btnCloseSearch = document.getElementById('btn-close-search');
            const searchInput = document.getElementById('global-search-input');
            const searchResults = document.getElementById('global-search-results');

            // 1. Sidebar Toggle Mobile
            if (btnSidebar && sidebar) {
                btnSidebar.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const isOpen = !sidebar.classList.contains('-translate-x-full');
                    if (isOpen) {
                        sidebar.classList.add('-translate-x-full');
                        if (overlay) overlay.classList.add('hidden');
                    } else {
                        sidebar.classList.remove('-translate-x-full');
                        if (overlay) overlay.classList.remove('hidden');
                    }
                });
            }

            // 2. Notifications Dropdown Toggle
            if (btnNotif && notifDropdown) {
                btnNotif.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    if (userDropdown) userDropdown.classList.add('hidden');
                    notifDropdown.classList.toggle('hidden');
                });
            }

            // 3. User Menu Dropdown Toggle
            if (btnUser && userDropdown) {
                btnUser.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    if (notifDropdown) notifDropdown.classList.add('hidden');
                    userDropdown.classList.toggle('hidden');
                });
            }

            // Click outside dismiss for dropdowns
            document.addEventListener('click', (e) => {
                if (notifDropdown && !notifDropdown.contains(e.target) && !btnNotif?.contains(e.target)) {
                    notifDropdown.classList.add('hidden');
                }
                if (userDropdown && !userDropdown.contains(e.target) && !btnUser?.contains(e.target)) {
                    userDropdown.classList.add('hidden');
                }
            });

            // 4. Global Search Modal & Live Search Filter
            function renderSearchResults(query = '') {
                if (!searchResults) return;

                const q = query.trim().toLowerCase();
                const filtered = q === '' 
                    ? searchIndex.slice(0, 8) 
                    : searchIndex.filter(item => 
                        item.title.toLowerCase().includes(q) || 
                        item.category.toLowerCase().includes(q)
                      );

                if (filtered.length === 0) {
                    searchResults.innerHTML = `
                        <div class="p-6 text-center text-surface-500">
                            <i data-lucide="search-x" class="w-8 h-8 mx-auto mb-2 text-surface-300"></i>
                            <p class="text-xs font-semibold">Tidak ditemukan menu untuk "${query}"</p>
                        </div>
                    `;
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                    return;
                }

                searchResults.innerHTML = filtered.map((item, idx) => `
                    <a href="${item.url}" class="flex items-center justify-between px-3 py-2.5 rounded-xl hover:bg-emerald-50 hover:text-emerald-800 transition-colors group">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-surface-100 group-hover:bg-emerald-100 flex items-center justify-center shrink-0 text-surface-600 group-hover:text-emerald-700 transition-colors">
                                <i data-lucide="${item.icon}" class="w-4 h-4"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-surface-800 group-hover:text-emerald-900 truncate">${item.title}</p>
                                <p class="text-[0.65rem] text-surface-400 group-hover:text-emerald-600">${item.category}</p>
                            </div>
                        </div>
                        <i data-lucide="arrow-right" class="w-4 h-4 text-surface-300 group-hover:text-emerald-600 transition-colors shrink-0"></i>
                    </a>
                `).join('');

                if (typeof lucide !== 'undefined') lucide.createIcons();
            }

            function openSearchModal() {
                if (!searchModal) return;
                searchModal.classList.remove('hidden');
                if (searchInput) {
                    searchInput.value = '';
                    setTimeout(() => searchInput.focus(), 50);
                }
                renderSearchResults('');
            }

            function closeSearchModal() {
                if (!searchModal) return;
                searchModal.classList.add('hidden');
            }

            if (btnSearch) {
                btnSearch.addEventListener('click', (e) => {
                    e.preventDefault();
                    openSearchModal();
                });
            }

            if (btnCloseSearch) {
                btnCloseSearch.addEventListener('click', (e) => {
                    e.preventDefault();
                    closeSearchModal();
                });
            }

            if (searchModal) {
                searchModal.addEventListener('click', (e) => {
                    if (e.target === searchModal) closeSearchModal();
                });
            }

            if (searchInput) {
                searchInput.addEventListener('input', (e) => {
                    renderSearchResults(e.target.value);
                });
            }

            // Keyboard shortcut: Ctrl+K or Cmd+K
            document.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                    e.preventDefault();
                    openSearchModal();
                }
                if (e.key === 'Escape' && searchModal && !searchModal.classList.contains('hidden')) {
                    closeSearchModal();
                }
            });
        });
    })();
</script>
