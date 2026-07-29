@php
    $notifications = [];
    $homeUrl = '/';
    $user = auth()->user();
    
    if ($user) {
        $roleName = $user->active_role->role->nama ?? '';
        $homeUrl = $user->active_role->role->redirect_url ?? '/';

        if ($roleName === 'WALI_SANTRI') {
            // Wali Santri - Get notifications for their children
            $anakIds = \App\Models\HubunganKeluarga::where('keluarga_id', $user->orang_id)
                ->pluck('orang_id');
                
            $pesertaDidikIds = \App\Models\PesertaDidik::whereIn('orang_id', $anakIds)->pluck('id');

            // 1. Recent payments
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

            // 2. Recent violations
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

            // 3. Recent achievements
            $recentPrestasi = \App\Models\CatatanPrestasi::whereIn('peserta_didik_id', $pesertaDidikIds)
                ->with(['pesertaDidik.orang'])
                ->orderBy('created_at', 'desc')
                ->limit(2)
                ->get();

            foreach ($recentPrestasi as $pr) {
                $notifications[] = [
                    'icon' => 'award',
                    'icon_bg' => 'bg-primary-50 text-primary-600',
                    'title' => 'Prestasi Santri',
                    'message' => 'Alhamdulillah, ananda ' . ($pr->pesertaDidik->orang->nama_lengkap ?? '') . ' meraih prestasi: ' . $pr->judul . '.',
                    'time' => $pr->created_at->diffForHumans(),
                    'link' => route('portal.beranda') . '?tab=kedisiplinan',
                    'time_stamp' => $pr->created_at,
                ];
            }

        } elseif ($roleName === 'BENDAHARA') {
            // Bendahara - Get recent payments
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

        } elseif ($roleName === 'PANITIA_PSB') {
            // Panitia PSB - Get recent registrations
            $recentCalon = \App\Models\CalonSantri::orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            foreach ($recentCalon as $c) {
                $notifications[] = [
                    'icon' => 'user-plus',
                    'icon_bg' => 'bg-primary-50 text-primary-600',
                    'title' => 'Pendaftaran PSB',
                    'message' => 'Calon santri baru terdaftar: ' . $c->nama_lengkap . ' (' . ($c->no_pendaftaran) . ').',
                    'time' => $c->created_at->diffForHumans(),
                    'link' => url('/panitia-psb/dashboard'),
                    'time_stamp' => $c->created_at,
                ];
            }

        } else {
            // SUPER_ADMIN / default - Show a mix of recent registrations, payments, and updated users
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

            // User Updates
            $recentUpdatedUsers = \App\Models\User::whereColumn('updated_at', '>', 'created_at')
                ->orderBy('updated_at', 'desc')
                ->limit(3)
                ->get();

            foreach ($recentUpdatedUsers as $u) {
                $notifications[] = [
                    'icon' => 'user-cog',
                    'icon_bg' => 'bg-warning-50 text-warning-600',
                    'title' => 'User Diperbarui',
                    'message' => 'Akun "' . $u->username . '" baru saja diperbarui.',
                    'time' => $u->updated_at->diffForHumans(),
                    'link' => route('admin.users.index'),
                    'time_stamp' => $u->updated_at,
                ];
            }
        }
    }

    // Sort notifications by timestamp/order descending
    usort($notifications, function($a, $b) {
        $ta = $a['time_stamp']?->timestamp ?? 0;
        $tb = $b['time_stamp']?->timestamp ?? 0;
        return $tb <=> $ta;
    });

    // Slice to maximum 5 notifications
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
            {{-- Global Search --}}
            <button type="button"
                    class="flex items-center justify-center sm:justify-start gap-2 px-2.5 sm:px-3 py-1.5 rounded-lg bg-surface-100 text-surface-400 text-sm hover:bg-surface-200 transition-colors"
                    id="btn-global-search"
                    aria-label="Buka pencarian">
                <i data-lucide="search" class="w-4 h-4"></i>
                <span class="hidden lg:inline">Cari...</span>
                <kbd class="hidden lg:inline-flex items-center gap-0.5 px-1.5 py-0.5 text-[0.625rem] font-medium bg-white rounded border border-surface-200 text-surface-400">
                    Ctrl K
                </kbd>
            </button>

            {{-- Install PWA Button (hidden until available) --}}
            <button id="btn-install-pwa" class="hidden items-center gap-2 px-2.5 sm:px-3 py-1.5 rounded-lg bg-surface-100 text-surface-600 text-sm hover:bg-surface-200 transition-colors" title="Install Aplikasi">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span class="hidden lg:inline">Pasang Aplikasi</span>
            </button>

            {{-- Tahun Pelajaran Active --}}
            <div class="hidden md:flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-accent-50 text-accent-700 text-xs font-semibold" id="active-ta">
                <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                <span>TA {{ date('Y') }}/{{ date('Y') + 1 }}</span>
            </div>

            {{-- Notifications --}}
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

                {{-- Notification dropdown --}}
                <div id="notification-dropdown"
                     class="hidden absolute right-0 top-12 z-50 w-80 bg-white rounded-xl shadow-dropdown border border-surface-100 animate-scale-in overflow-hidden">
                    <div class="px-4 py-3 border-b border-surface-100 flex items-center justify-between">
                        <h3 class="font-semibold text-sm text-surface-900">Notifikasi</h3>
                        @if(count($notifications) > 0)
                            <span class="badge badge-accent">{{ count($notifications) }} baru</span>
                        @endif
                    </div>
                    
                    <div class="max-h-72 overflow-y-auto divide-y divide-surface-50">
                        @forelse($notifications as $notif)
                            <a href="{{ $notif['link'] }}" class="flex gap-3 px-4 py-3 hover:bg-surface-50 transition-colors">
                                <div class="w-8 h-8 rounded-full {{ $notif['icon_bg'] }} flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="{{ $notif['icon'] }}" class="w-4 h-4"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-surface-800 truncate">{{ $notif['title'] }}</p>
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
                                <p class="text-sm font-medium">Tidak ada notifikasi baru</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- User Menu --}}
            <div class="relative z-50" id="user-menu-wrapper">
                <button type="button"
                        class="flex items-center gap-2 p-1.5 pr-3 rounded-lg hover:bg-surface-100 transition-colors"
                        id="btn-user-menu"
                        aria-label="Menu pengguna">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-secondary-600 flex items-center justify-center text-white text-xs font-bold">
                        {{ substr(auth()->user()->orang->nama_lengkap ?? auth()->user()->username ?? 'A', 0, 1) }}
                    </div>
                    <span class="hidden md:inline text-sm font-medium text-surface-700">{{ auth()->user()->orang->nama_lengkap ?? auth()->user()->username ?? 'Pengguna' }}</span>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-surface-400 hidden md:inline"></i>
                </button>

                {{-- User dropdown --}}
                <div id="user-dropdown"
                     class="hidden absolute right-0 top-12 z-50 w-56 bg-white rounded-xl shadow-dropdown border border-surface-100 animate-scale-in overflow-hidden">
                    <div class="px-4 py-3 border-b border-surface-100">
                        <p class="text-sm font-semibold text-surface-900">{{ auth()->user()->orang->nama_lengkap ?? auth()->user()->username ?? 'Pengguna' }}</p>
                        <p class="text-xs text-surface-400">{{ auth()->user()->email ?? auth()->user()->username }}</p>
                    </div>
                    <div class="py-1">
                        <a href="{{ route('akun.profil') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-surface-600 hover:bg-surface-50 transition-colors">
                            <i data-lucide="user" class="w-4 h-4"></i> Profil Saya
                        </a>
                        <a href="{{ route('akun.ganti-password') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-surface-600 hover:bg-surface-50 transition-colors">
                            <i data-lucide="key" class="w-4 h-4"></i> Ganti Password
                        </a>
                        <a href="{{ route('akun.ganti-peran') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-surface-600 hover:bg-surface-50 transition-colors">
                            <i data-lucide="repeat" class="w-4 h-4"></i> Ganti Peran
                        </a>
                    </div>
                    <div class="border-t border-surface-100 py-1">
                        <form method="POST" action="{{ url('/logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-sm text-danger-500 hover:bg-danger-50 transition-colors">
                                <i data-lucide="log-out" class="w-4 h-4"></i> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</header>

{{-- Global Search Modal — placed OUTSIDE header to escape backdrop-blur stacking context --}}
<div id="global-search-modal" class="hidden fixed inset-0 z-[60] flex items-start justify-center px-4 pt-24 pb-8 bg-black/40 backdrop-blur-sm">
    <div class="w-full max-w-2xl rounded-[2rem] bg-white shadow-2xl border border-surface-200 overflow-hidden">
        <div class="flex items-center justify-between px-4 py-4 border-b border-surface-100">
            <div>
                <p class="text-sm font-semibold text-surface-900">Pencarian Global</p>
                <p class="text-xs text-surface-500">Tekan Esc untuk menutup atau klik di luar.</p>
            </div>
            <button type="button" id="btn-close-search" class="p-2 rounded-lg text-surface-500 hover:bg-surface-100">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <div class="p-4">
            <label for="global-search-input" class="sr-only">Cari</label>
            <div class="relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-surface-400"></i>
                <input id="global-search-input" type="text" autocomplete="off" placeholder="Cari menu, data, atau modul..."
                       class="w-full pl-11 pr-4 py-3 rounded-2xl border border-surface-200 text-surface-900 text-sm focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20" />
            </div>
        </div>
        <div class="border-t border-surface-100 px-4 py-3 text-sm text-surface-500">
            Hasil pencarian belum diimplementasikan. Ketik di atas untuk melihat hasil sampel.
        </div>
    </div>
</div>
