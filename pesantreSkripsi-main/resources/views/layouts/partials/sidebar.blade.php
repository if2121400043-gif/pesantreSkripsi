{{-- Sidebar Navigation --}}
<aside id="sidebar"
       class="fixed top-0 left-0 z-50 h-screen w-[85vw] max-w-[16rem] flex flex-col
              bg-gradient-to-b from-primary-900 via-primary-800 to-primary-950
              transition-all duration-300 ease-in-out
              -translate-x-full md:translate-x-0">

    {{-- Logo & Brand --}}
    <div class="flex items-center justify-between px-4 py-4 border-b border-white/10">
        <div class="flex items-center gap-3 min-w-0">
            <img src="{{ asset('images/logo-pesantren.webp') }}?v={{ time() }}"
                 alt="Logo Pesantren Nurul Furqon"
                 class="w-10 h-10 rounded-full ring-2 ring-accent-400/50 object-cover flex-shrink-0"
                 id="sidebar-logo">
            <div class="sidebar-text overflow-hidden">
                <h1 class="text-sm font-bold text-white truncate font-heading">Nurul Furqon</h1>
                <p class="text-[0.65rem] text-primary-300 truncate">Sistem Manajemen Pesantren</p>
            </div>
        </div>
        {{-- Close button (mobile only) --}}
        <button type="button"
                id="btn-close-sidebar"
                class="md:hidden p-1.5 rounded-lg text-primary-300 hover:text-white hover:bg-white/10 transition-colors"
                aria-label="Tutup menu">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-2.5 py-3 space-y-1" id="sidebar-nav">
        @php
            $roleName = auth()->user()->active_role->role->nama ?? '';
        @endphp

        @if($roleName === 'SUPER_ADMIN')
            {{-- Dashboard --}}
            <a href="{{ url('/admin/dashboard') }}"
               class="nav-item {{ request()->is('admin/dashboard') ? 'active' : '' }}"
               id="nav-dashboard">
                <i data-lucide="layout-dashboard" class="nav-icon"></i>
                <span class="sidebar-text">Dashboard</span>
            </a>
            {{-- Master Data --}}
            <details class="group cursor-pointer select-none" {{ request()->is('admin/wilayah*', 'admin/lembaga*', 'admin/tahun-pelajaran*', 'admin/asrama*', 'admin/penempatan-asrama*') ? 'open' : '' }}>
                <summary class="nav-item flex items-center justify-between [&::-webkit-details-marker]:hidden list-none">
                    <div class="flex items-center gap-3">
                        <i data-lucide="database" class="nav-icon"></i>
                        <span class="sidebar-text">Master Data</span>
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200 group-open:rotate-180 text-primary-300"></i>
                </summary>
                <div class="pl-4 pr-1 py-1 mt-1 space-y-1 ml-4 border-l border-white/10">
                    <a href="{{ url('/admin/wilayah') }}"
                       class="nav-sub-item {{ request()->is('admin/wilayah*') ? 'active' : '' }}">
                        <i data-lucide="map-pin" class="nav-icon"></i>
                        <span>Wilayah</span>
                    </a>
                    <a href="{{ url('/admin/lembaga') }}"
                       class="nav-sub-item {{ request()->is('admin/lembaga*') ? 'active' : '' }}">
                        <i data-lucide="landmark" class="nav-icon"></i>
                        <span>Pesantren & Lembaga</span>
                    </a>
                    <a href="{{ url('/admin/tahun-pelajaran') }}"
                       class="nav-sub-item {{ request()->is('admin/tahun-pelajaran*') ? 'active' : '' }}">
                        <i data-lucide="calendar-range" class="nav-icon"></i>
                        <span>Tahun Pelajaran</span>
                    </a>
                    <a href="{{ url('/admin/asrama') }}"
                       class="nav-sub-item {{ request()->is('admin/asrama*') ? 'active' : '' }}">
                        <i data-lucide="home" class="nav-icon"></i>
                        <span>Asrama & Kamar</span>
                    </a>
                    <a href="{{ url('/admin/penempatan-asrama') }}"
                       class="nav-sub-item {{ request()->is('admin/penempatan-asrama*') ? 'active' : '' }}">
                        <i data-lucide="bed" class="nav-icon"></i>
                        <span>Penempatan Kamar</span>
                    </a>
                </div>
            </details>

            {{-- PSB --}}
            <details class="group cursor-pointer select-none" {{ request()->is('admin/psb/gelombang*', 'admin/psb/calon-santri*') ? 'open' : '' }}>
                <summary class="nav-item flex items-center justify-between [&::-webkit-details-marker]:hidden list-none">
                    <div class="flex items-center gap-3">
                        <i data-lucide="graduation-cap" class="nav-icon"></i>
                        <span class="sidebar-text">PSB</span>
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200 group-open:rotate-180 text-primary-300"></i>
                </summary>
                <div class="pl-4 pr-1 py-1 mt-1 space-y-1 ml-4 border-l border-white/10">
                    <a href="{{ url('/admin/psb/gelombang') }}"
                       class="nav-sub-item {{ request()->is('admin/psb/gelombang*') ? 'active' : '' }}">
                        <i data-lucide="door-open" class="nav-icon"></i>
                        <span>Gelombang PSB</span>
                    </a>
                    <a href="{{ url('/admin/psb/calon-santri') }}"
                       class="nav-sub-item {{ request()->is('admin/psb/calon-santri*') ? 'active' : '' }}">
                        <i data-lucide="user-plus" class="nav-icon"></i>
                        <span>Calon Santri</span>
                    </a>
                </div>
            </details>

            {{-- Kepesantrenan --}}
            <details class="group cursor-pointer select-none" {{ request()->is('admin/orang*', 'admin/peserta-didik*', 'admin/pegawai*', 'admin/keluarga*') ? 'open' : '' }}>
                <summary class="nav-item flex items-center justify-between [&::-webkit-details-marker]:hidden list-none">
                    <div class="flex items-center gap-3">
                        <i data-lucide="users" class="nav-icon"></i>
                        <span class="sidebar-text">Kepesantrenan</span>
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200 group-open:rotate-180 text-primary-300"></i>
                </summary>
                <div class="pl-4 pr-1 py-1 mt-1 space-y-1 ml-4 border-l border-white/10">
                    <a href="{{ url('/admin/orang') }}"
                       class="nav-sub-item {{ request()->is('admin/orang*') ? 'active' : '' }}">
                        <i data-lucide="user-circle" class="nav-icon"></i>
                        <span>Data Orang (NIUP)</span>
                    </a>
                    <a href="{{ url('/admin/peserta-didik') }}"
                       class="nav-sub-item {{ request()->is('admin/peserta-didik*') ? 'active' : '' }}">
                        <i data-lucide="graduation-cap" class="nav-icon"></i>
                        <span>Peserta Didik</span>
                    </a>
                    <a href="{{ url('/admin/pegawai') }}"
                       class="nav-sub-item {{ request()->is('admin/pegawai*') ? 'active' : '' }}">
                        <i data-lucide="briefcase" class="nav-icon"></i>
                        <span>Pegawai</span>
                    </a>
                    <a href="{{ url('/admin/keluarga') }}"
                       class="nav-sub-item {{ request()->is('admin/keluarga*') ? 'active' : '' }}">
                        <i data-lucide="heart" class="nav-icon"></i>
                        <span>Keluarga & Wali</span>
                    </a>
                </div>
            </details>

            {{-- Akademik --}}
            <details class="group cursor-pointer select-none" {{ request()->is('admin/rombel*', 'admin/penempatan*', 'admin/mata-pelajaran*', 'admin/jadwal-pelajaran*', 'admin/penilaian*', 'admin/presensi*') ? 'open' : '' }}>
                <summary class="nav-item flex items-center justify-between [&::-webkit-details-marker]:hidden list-none">
                    <div class="flex items-center gap-3">
                        <i data-lucide="book-open" class="nav-icon"></i>
                        <span class="sidebar-text">Akademik</span>
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200 group-open:rotate-180 text-primary-300"></i>
                </summary>
                <div class="pl-4 pr-1 py-1 mt-1 space-y-1 ml-4 border-l border-white/10">
                    <a href="{{ url('/admin/rombel') }}"
                       class="nav-sub-item {{ request()->is('admin/rombel*') ? 'active' : '' }}">
                        <i data-lucide="folder-open" class="nav-icon"></i>
                        <span>Rombongan Belajar</span>
                    </a>
                    <a href="{{ url('/admin/penempatan') }}"
                       class="nav-sub-item {{ request()->is('admin/penempatan*') ? 'active' : '' }}">
                        <i data-lucide="clipboard-list" class="nav-icon"></i>
                        <span>Penempatan Santri</span>
                    </a>
                    <a href="{{ url('/admin/mata-pelajaran') }}"
                       class="nav-sub-item {{ request()->is('admin/mata-pelajaran*') ? 'active' : '' }}">
                        <i data-lucide="file-text" class="nav-icon"></i>
                        <span>Mata Pelajaran</span>
                    </a>
                    <a href="{{ url('/admin/jadwal-pelajaran') }}"
                       class="nav-sub-item {{ request()->is('admin/jadwal-pelajaran*') ? 'active' : '' }}">
                        <i data-lucide="calendar" class="nav-icon"></i>
                        <span>Jadwal Pelajaran</span>
                    </a>
                    <a href="{{ url('/admin/presensi') }}"
                       class="nav-sub-item {{ request()->is('admin/presensi*') ? 'active' : '' }}">
                        <i data-lucide="check-square" class="nav-icon"></i>
                        <span>Presensi Kelas</span>
                    </a>
                    <a href="{{ url('/admin/penilaian') }}"
                       class="nav-sub-item {{ request()->is('admin/penilaian*') ? 'active' : '' }}">
                        <i data-lucide="bar-chart-3" class="nav-icon"></i>
                        <span>Penilaian & Rapor</span>
                    </a>
                </div>
            </details>

            {{-- Keuangan --}}
            <details class="group cursor-pointer select-none" {{ request()->is('admin/komponen-biaya*', 'admin/tagihan*', 'admin/laporan-keuangan*') ? 'open' : '' }}>
                <summary class="nav-item flex items-center justify-between [&::-webkit-details-marker]:hidden list-none">
                    <div class="flex items-center gap-3">
                        <i data-lucide="wallet" class="nav-icon"></i>
                        <span class="sidebar-text">Keuangan</span>
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200 group-open:rotate-180 text-primary-300"></i>
                </summary>
                <div class="pl-4 pr-1 py-1 mt-1 space-y-1 ml-4 border-l border-white/10">
                    <a href="{{ url('/admin/komponen-biaya') }}"
                       class="nav-sub-item {{ request()->is('admin/komponen-biaya*') ? 'active' : '' }}">
                        <i data-lucide="coins" class="nav-icon"></i>
                        <span>Komponen Biaya</span>
                    </a>
                    <a href="{{ url('/admin/tagihan') }}"
                       class="nav-sub-item {{ request()->is('admin/tagihan*') ? 'active' : '' }}">
                        <i data-lucide="receipt" class="nav-icon"></i>
                        <span>Tagihan & Pembayaran</span>
                    </a>
                    <a href="{{ url('/admin/laporan-keuangan') }}"
                       class="nav-sub-item {{ request()->is('admin/laporan-keuangan*') ? 'active' : '' }}">
                        <i data-lucide="line-chart" class="nav-icon"></i>
                        <span>Laporan Keuangan</span>
                    </a>
                </div>
            </details>

            {{-- Kedisiplinan --}}
            <details class="group cursor-pointer select-none" {{ request()->is('admin/pelanggaran*', 'admin/prestasi*', 'admin/perizinan*') ? 'open' : '' }}>
                <summary class="nav-item flex items-center justify-between [&::-webkit-details-marker]:hidden list-none">
                    <div class="flex items-center gap-3">
                        <i data-lucide="shield-alert" class="nav-icon"></i>
                        <span class="sidebar-text">Kedisiplinan</span>
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200 group-open:rotate-180 text-primary-300"></i>
                </summary>
                <div class="pl-4 pr-1 py-1 mt-1 space-y-1 ml-4 border-l border-white/10">
                    <a href="{{ url('/admin/pelanggaran') }}"
                       class="nav-sub-item {{ request()->is('admin/pelanggaran*') ? 'active' : '' }}">
                        <i data-lucide="alert-triangle" class="nav-icon"></i>
                        <span>Poin Pelanggaran</span>
                    </a>
                    <a href="{{ url('/admin/prestasi') }}"
                       class="nav-sub-item {{ request()->is('admin/prestasi*') ? 'active' : '' }}">
                        <i data-lucide="trophy" class="nav-icon"></i>
                        <span>Catatan Prestasi</span>
                    </a>
                    <a href="{{ url('/admin/perizinan') }}"
                       class="nav-sub-item {{ request()->is('admin/perizinan*') ? 'active' : '' }}">
                        <i data-lucide="door-open" class="nav-icon"></i>
                        <span>Perizinan Keluar</span>
                    </a>
                </div>
            </details>

            {{-- Media & Hubungan --}}
            <div class="nav-section sidebar-text">Media & Hubungan</div>
            <a href="{{ url('/admin/berita') }}"
               class="nav-item {{ request()->is('admin/berita*') ? 'active' : '' }}"
               id="nav-berita">
                <i data-lucide="newspaper" class="nav-icon"></i>
                <span class="sidebar-text">Berita & Kegiatan</span>
            </a>
            <a href="{{ url('/admin/broadcast-wa') }}"
               class="nav-item {{ request()->is('admin/broadcast-wa*') ? 'active' : '' }}"
               id="nav-broadcast-wa">
                <svg class="nav-icon w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                <span class="sidebar-text">Broadcast WhatsApp</span>
            </a>

            {{-- Pengaturan --}}
            <details class="group cursor-pointer select-none" {{ request()->is('admin/users*', 'admin/roles*', 'admin/konfigurasi*') ? 'open' : '' }}>
                <summary class="nav-item flex items-center justify-between [&::-webkit-details-marker]:hidden list-none">
                    <div class="flex items-center gap-3">
                        <i data-lucide="settings" class="nav-icon"></i>
                        <span class="sidebar-text">Pengaturan</span>
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200 group-open:rotate-180 text-primary-300"></i>
                </summary>
                <div class="pl-4 pr-1 py-1 mt-1 space-y-1 ml-4 border-l border-white/10">
                    <a href="{{ url('/admin/users') }}"
                       class="nav-sub-item {{ request()->is('admin/users*') ? 'active' : '' }}">
                        <i data-lucide="lock" class="nav-icon"></i>
                        <span>Manajemen User</span>
                    </a>
                    <a href="{{ url('/admin/roles') }}"
                       class="nav-sub-item {{ request()->is('admin/roles*') ? 'active' : '' }}">
                        <i data-lucide="shield" class="nav-icon"></i>
                        <span>Role & Permission</span>
                    </a>
                    <a href="{{ url('/admin/konfigurasi') }}"
                       class="nav-sub-item {{ request()->is('admin/konfigurasi*') ? 'active' : '' }}">
                        <i data-lucide="sliders" class="nav-icon"></i>
                        <span>Konfigurasi</span>
                    </a>
                </div>
            </details>
            
        @elseif($roleName === 'SANTRI')
            {{-- Portal Wali Santri --}}
            <a href="{{ url('/portal/beranda') }}"
               class="nav-item {{ request()->is('portal/beranda') ? 'active' : '' }}">
                <i data-lucide="home" class="nav-icon"></i>
                <span class="sidebar-text">Beranda</span>
            </a>

            <div class="nav-section sidebar-text">Informasi Anak</div>
            
            <a href="{{ url('/portal/tagihan') }}"
               class="nav-item {{ request()->is('portal/tagihan*') ? 'active' : '' }}">
                <i data-lucide="receipt" class="nav-icon"></i>
                <span class="sidebar-text">Keuangan & Tagihan</span>
            </a>
            
            <a href="{{ url('/portal/presensi') }}"
               class="nav-item {{ request()->is('portal/presensi*') ? 'active' : '' }}">
                <i data-lucide="calendar-check" class="nav-icon"></i>
                <span class="sidebar-text">Kehadiran Kelas</span>
            </a>
            
            <a href="{{ url('/portal/kedisiplinan') }}"
               class="nav-item {{ request()->is('portal/kedisiplinan*') ? 'active' : '' }}">
                <i data-lucide="shield-alert" class="nav-icon"></i>
                <span class="sidebar-text">Kedisiplinan & Prestasi</span>
            </a>
            
            <div class="mt-8 px-4 sidebar-text">
                <div class="bg-primary-800/50 rounded-xl p-4 border border-primary-700/50 text-center">
                    <i data-lucide="heart-handshake" class="w-8 h-8 text-primary-400 mx-auto mb-2"></i>
                    <p class="text-[11px] text-primary-200 leading-relaxed">
                        Mari bersama memantau dan membimbing perkembangan ananda tercinta.
                    </p>
                </div>
            </div>
        @elseif($roleName === 'GURU')
            {{-- Portal Guru --}}
            <a href="{{ url('/guru/dashboard') }}"
               class="nav-item {{ request()->is('guru/dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard" class="nav-icon"></i>
                <span class="sidebar-text">Dashboard</span>
            </a>

            <div class="nav-section sidebar-text">Akademik & Kelas</div>
            
            <a href="{{ url('/guru/presensi') }}"
               class="nav-item {{ request()->is('guru/presensi*') ? 'active' : '' }}">
                <i data-lucide="check-square" class="nav-icon"></i>
                <span class="sidebar-text">Input Presensi</span>
            </a>
            
            <a href="{{ url('/guru/penilaian') }}"
               class="nav-item {{ request()->is('guru/penilaian*') ? 'active' : '' }}">
                <i data-lucide="bar-chart-3" class="nav-icon"></i>
                <span class="sidebar-text">Input Nilai Rapor</span>
            </a>
            
            <a href="{{ url('/guru/kedisiplinan') }}"
               class="nav-item {{ request()->is('guru/kedisiplinan*') ? 'active' : '' }}">
                <i data-lucide="shield-alert" class="nav-icon"></i>
                <span class="sidebar-text">Catat Kedisiplinan</span>
            </a>
        @elseif($roleName === 'BENDAHARA')
            {{-- Portal Bendahara Keuangan --}}
            <a href="{{ url('/bendahara/dashboard') }}"
               class="nav-item {{ request()->is('bendahara/dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard" class="nav-icon"></i>
                <span class="sidebar-text">Dashboard</span>
            </a>

            <div class="nav-section sidebar-text">Manajemen Keuangan</div>
            
            <a href="{{ url('/bendahara/komponen-biaya') }}"
               class="nav-item {{ request()->is('bendahara/komponen-biaya*') ? 'active' : '' }}">
                <i data-lucide="wallet" class="nav-icon"></i>
                <span class="sidebar-text">Komponen Biaya</span>
            </a>
            
            <a href="{{ url('/bendahara/tagihan') }}"
               class="nav-item {{ request()->is('bendahara/tagihan*') ? 'active' : '' }}">
                <i data-lucide="receipt" class="nav-icon"></i>
                <span class="sidebar-text">Tagihan & Pembayaran</span>
            </a>
            
            <a href="{{ url('/bendahara/laporan-keuangan') }}"
               class="nav-item {{ request()->is('bendahara/laporan-keuangan*') ? 'active' : '' }}">
                <i data-lucide="line-chart" class="nav-icon"></i>
                <span class="sidebar-text">Laporan Keuangan</span>
            </a>
        @elseif($roleName === 'PANITIA_PSB')
            {{-- Portal Panitia PSB --}}
            <a href="{{ url('/panitia-psb/dashboard') }}"
               class="nav-item {{ request()->is('panitia-psb/dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard" class="nav-icon"></i>
                <span class="sidebar-text">Dashboard</span>
            </a>

            <div class="nav-section sidebar-text">Penerimaan Santri Baru</div>
            
            <a href="{{ url('/panitia-psb/gelombang') }}"
               class="nav-item {{ request()->is('panitia-psb/gelombang*') ? 'active' : '' }}">
                <i data-lucide="door-open" class="nav-icon"></i>
                <span class="sidebar-text">Gelombang PSB</span>
            </a>
            
            <a href="{{ url('/panitia-psb/calon-santri') }}"
               class="nav-item {{ request()->is('panitia-psb/calon-santri*') ? 'active' : '' }}">
                <i data-lucide="user-plus" class="nav-icon"></i>
                <span class="sidebar-text">Calon Santri</span>
            </a>
        @endif
    </nav>

    {{-- User Footer --}}
    <div class="px-3 py-3 border-t border-white/10">
        @php
            $displayName = auth()->user()->orang->nama_lengkap ?? auth()->user()->username ?? 'Pengguna';
            $displayRole = auth()->user()->active_role->role->nama ?? 'Pengguna';
            $roleLabel = match($displayRole) {
                'SUPER_ADMIN' => 'Super Admin',
                'GURU' => 'Guru / Ustadz',
                'WALI_SANTRI' => 'Wali Santri',
                'PANITIA_PSB' => 'Panitia PSB',
                'BENDAHARA' => 'Bendahara',
                default => $displayRole,
            };
        @endphp
        <div class="flex items-center gap-3 px-2">
            <div class="w-8 h-8 rounded-full bg-accent-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                {{ substr($displayName, 0, 1) }}
            </div>
            <div class="sidebar-text overflow-hidden">
                <p class="text-sm font-medium text-white truncate">{{ $displayName }}</p>
                <p class="text-[0.65rem] text-primary-300 truncate">{{ $roleLabel }}</p>
            </div>
        </div>
    </div>
</aside>
