@extends('frontend.layouts.app')

@section('title', __('Penerimaan Santri Baru (PSB)'))

@section('content')
{{-- ═══ HERO HEADER ═══ --}}
<section class="relative overflow-hidden bg-gradient-to-br from-primary-900 via-primary-800 to-surface-900 dark:from-surface-950 dark:via-surface-900 dark:to-surface-950 text-white transition-colors duration-300">
    <div class="absolute inset-0 opacity-[0.05] dark:opacity-[0.02]" style="background-image:url('data:image/svg+xml,%3Csvg width=60 height=60 viewBox=%220 0 60 60%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22none%22 fill-rule=%22evenodd%22%3E%3Cg fill=%22%23ffffff%22 fill-opacity=%221%22%3E%3Cpath d=%22M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z%22/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>
    <div class="absolute top-0 left-0 w-[600px] h-[600px] bg-accent-500/15 rounded-full blur-[120px] pointer-events-none -translate-y-1/2 -translate-x-1/4"></div>
    <div class="absolute bottom-0 right-0 w-[400px] h-[400px] bg-primary-500/15 rounded-full blur-[100px] pointer-events-none translate-y-1/3 translate-x-1/4"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 py-20 md:py-28 text-center">
        <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 dark:bg-surface-800/50 border border-white/20 dark:border-surface-700 text-primary-200 dark:text-primary-400 text-xs font-bold tracking-widest uppercase mb-6 backdrop-blur-sm">
            <i data-lucide="graduation-cap" class="w-3.5 h-3.5"></i>
            {{ __('Tahun Ajaran') }} {{ $tahunAktif->nama ?? '-' }}
        </span>
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold mb-4 tracking-tight text-white font-outfit">{{ __('Penerimaan Santri Baru') }}</h1>
        <p class="text-primary-100/80 dark:text-surface-400 text-lg max-w-2xl mx-auto leading-relaxed">{{ __('Mari bergabung dan menjadi bagian dari keluarga besar') }} {{ $pesantren->nama ?? 'Pesantren' }}.</p>
    </div>
    <div class="absolute bottom-0 left-0 right-0">
        <svg class="w-full h-16 sm:h-20 text-surface-50 dark:text-surface-950 fill-current" viewBox="0 0 1440 120" preserveAspectRatio="none">
            <path d="M0,64L80,69.3C160,75,320,85,480,80C640,75,800,53,960,48C1120,43,1280,53,1360,58.7L1440,64L1440,120L1360,120C1280,120,1120,120,960,120C800,120,640,120,480,120C320,120,160,120,80,120L0,120Z"></path>
        </svg>
    </div>
</section>

{{-- ═══ CONTENT SECTION ═══ --}}
<section class="py-16 bg-surface-50 dark:bg-surface-950 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @if($gelombangsAktif->count() > 0)
            @php $gelombangAktif = $gelombangsAktif->first(); @endphp
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
                
                {{-- KIRI: INFO PENDAFTARAN & PERSYARATAN (Col 7) --}}
                <div class="lg:col-span-7 space-y-8">
                    <!-- Selamat Datang Card -->
                    <div class="bg-white dark:bg-surface-900 p-8 rounded-3xl border border-surface-100 dark:border-surface-800 shadow-sm">
                        <span class="inline-block py-1 px-3 rounded-full bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 font-bold text-xs uppercase tracking-widest mb-4 border border-primary-100 dark:border-primary-800">{{ __('Penerimaan Santri Baru') }}</span>
                        <h2 class="text-3xl font-extrabold text-surface-900 dark:text-white mb-6 font-outfit">Selamat Datang di PP Nurul Furqon</h2>
                        <p class="text-surface-600 dark:text-surface-400 leading-relaxed mb-8">
                            Penerimaan Santri Baru (PSB) Pondok Pesantren Nurul Furqon merupakan jalur resmi pendaftaran calon santri baru tingkat Madrasah Tsanawiyah (MTs) dan Madrasah Aliyah (MA). Kami berkomitmen mencetak generasi qur'ani yang mandiri, berilmu, dan berakhlakul karimah.
                        </p>
                        <div class="flex flex-wrap gap-4">
                            <a href="#" class="inline-flex items-center gap-2 px-6 py-3 bg-red-600 hover:bg-red-500 text-white font-bold rounded-xl shadow-md hover:-translate-y-0.5 transition-all text-xs">
                                <i data-lucide="file-text" class="w-4 h-4"></i> Unduh Brosur Informasi
                            </a>
                            <a href="{{ route('frontend.profil') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow-md hover:-translate-y-0.5 transition-all text-xs">
                                <i data-lucide="book-open" class="w-4 h-4"></i> Profil Lulusan
                            </a>
                        </div>
                    </div>

                    <!-- Persyaratan & Berkas Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Persyaratan -->
                        <div class="bg-white dark:bg-surface-900 p-6 rounded-3xl border border-surface-100 dark:border-surface-800 shadow-sm">
                            <div class="w-10 h-10 bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded-lg flex items-center justify-center mb-4">
                                <i data-lucide="check-square" class="w-5 h-5"></i>
                            </div>
                            <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-3 font-outfit">{{ __('Persyaratan Pendaftaran') }}</h3>
                            <ul class="space-y-2 text-xs text-surface-600 dark:text-surface-400">
                                <li class="flex items-start gap-2"><i data-lucide="check" class="w-4 h-4 text-success-500 flex-shrink-0 mt-0.5"></i> Mengisi formulir online.</li>
                                <li class="flex items-start gap-2"><i data-lucide="check" class="w-4 h-4 text-success-500 flex-shrink-0 mt-0.5"></i> Membayar biaya pendaftaran.</li>
                                <li class="flex items-start gap-2"><i data-lucide="check" class="w-4 h-4 text-success-500 flex-shrink-0 mt-0.5"></i> Bersedia mematuhi tata tertib.</li>
                                <li class="flex items-start gap-2"><i data-lucide="check" class="w-4 h-4 text-success-500 flex-shrink-0 mt-0.5"></i> Mengikuti tes wawancara.</li>
                            </ul>
                        </div>

                        <!-- Berkas -->
                        <div class="bg-white dark:bg-surface-900 p-6 rounded-3xl border border-surface-100 dark:border-surface-800 shadow-sm">
                            <div class="w-10 h-10 bg-secondary-50 dark:bg-secondary-900/30 text-secondary-600 dark:text-secondary-400 rounded-lg flex items-center justify-center mb-4">
                                <i data-lucide="file-text" class="w-5 h-5"></i>
                            </div>
                            <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-3 font-outfit">{{ __('Berkas yang Disiapkan') }}</h3>
                            <ul class="space-y-2 text-xs text-surface-600 dark:text-surface-400">
                                <li class="flex items-start gap-2"><i data-lucide="file" class="w-4 h-4 text-primary-500 flex-shrink-0 mt-0.5"></i> Scan/Foto Kartu Keluarga (KK).</li>
                                <li class="flex items-start gap-2"><i data-lucide="file" class="w-4 h-4 text-primary-500 flex-shrink-0 mt-0.5"></i> Scan/Foto Akta Kelahiran.</li>
                                <li class="flex items-start gap-2"><i data-lucide="file" class="w-4 h-4 text-primary-500 flex-shrink-0 mt-0.5"></i> Scan/Foto Ijazah / SKL.</li>
                                <li class="flex items-start gap-2"><i data-lucide="image" class="w-4 h-4 text-primary-500 flex-shrink-0 mt-0.5"></i> Pas Foto terbaru ukuran 3x4.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="bg-[#04241d] text-white rounded-3xl shadow-xl overflow-hidden border border-emerald-800/50 p-6 md:p-8 relative">
                        <!-- Background patterns -->
                        <div class="absolute inset-0 opacity-[0.03] bg-[radial-gradient(#22c55e_1px,transparent_1px)] [background-size:12px_12px] pointer-events-none"></div>
                        <div class="absolute right-0 top-0 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none -translate-y-1/3"></div>

                        <!-- Header Gelombang -->
                        <div class="mb-6 relative z-10 flex justify-between items-center">
                            <div>
                                <span class="text-[9px] uppercase tracking-wider text-emerald-400 font-bold font-outfit">{{ __('Gelombang Saat Ini') }}</span>
                                <h3 class="text-xl font-bold font-outfit text-white leading-tight mt-1">{{ $gelombangAktif->nama }}</h3>
                            </div>
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-[0_0_8px_#10b981] animate-pulse"></span>
                        </div>

                        <!-- Area Hitung Mundur -->
                        <div class="bg-white/5 border border-white/10 rounded-2xl p-4 mb-6 relative z-10">
                            <span class="text-[10px] text-emerald-300 font-bold uppercase tracking-widest flex items-center gap-1.5 mb-3">
                                <i data-lucide="clock" class="w-3.5 h-3.5 animate-spin" style="animation-duration: 8s;"></i> {{ __('Sisa Waktu Pendaftaran') }}
                            </span>
                            <div class="grid grid-cols-4 gap-2">
                                <!-- Hari -->
                                <div class="bg-white/5 backdrop-blur-md rounded-xl p-2.5 border border-white/5 text-center">
                                    <span id="countdown-hari" class="block text-2xl md:text-3xl font-extrabold text-emerald-400 font-outfit">--</span>
                                    <span class="text-[8px] text-slate-400 font-bold tracking-wider uppercase">Hari</span>
                                </div>
                                <!-- Jam -->
                                <div class="bg-white/5 backdrop-blur-md rounded-xl p-2.5 border border-white/5 text-center">
                                    <span id="countdown-jam" class="block text-2xl md:text-3xl font-extrabold text-emerald-400 font-outfit">--</span>
                                    <span class="text-[8px] text-slate-400 font-bold tracking-wider uppercase">Jam</span>
                                </div>
                                <!-- Menit -->
                                <div class="bg-white/5 backdrop-blur-md rounded-xl p-2.5 border border-white/5 text-center">
                                    <span id="countdown-menit" class="block text-2xl md:text-3xl font-extrabold text-emerald-400 font-outfit">--</span>
                                    <span class="text-[8px] text-slate-400 font-bold tracking-wider uppercase">Menit</span>
                                </div>
                                <!-- Detik -->
                                <div class="bg-white/5 backdrop-blur-md rounded-xl p-2.5 border border-white/5 text-center">
                                    <span id="countdown-detik" class="block text-2xl md:text-3xl font-extrabold text-emerald-400 font-outfit">--</span>
                                    <span class="text-[8px] text-slate-400 font-bold tracking-wider uppercase">Detik</span>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Jadwal & Tanggal -->
                        <div class="space-y-3 mb-6 relative z-10">
                            <!-- Pendaftaran -->
                            <div class="bg-white/5 border border-white/5 rounded-xl p-3 flex gap-3 items-center">
                                <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="calendar" class="w-4 h-4"></i>
                                </div>
                                <div class="leading-tight text-[11px]">
                                    <p class="text-slate-400 font-bold uppercase tracking-wider">{{ __('Pendaftaran Online') }}</p>
                                    <p class="font-bold text-white text-xs mt-0.5">
                                        {{ \Carbon\Carbon::parse($gelombangAktif->tanggal_buka)->translatedFormat('d M') }} s.d {{ \Carbon\Carbon::parse($gelombangAktif->tanggal_tutup)->translatedFormat('d F Y') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Seleksi -->
                            <div class="bg-white/5 border border-white/5 rounded-xl p-3 flex gap-3 items-center">
                                <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="file-check" class="w-4 h-4"></i>
                                </div>
                                <div class="leading-tight text-[11px]">
                                    <p class="text-slate-400 font-bold uppercase tracking-wider">{{ __('Ujian Seleksi & Tes') }}</p>
                                    <p class="font-bold text-white text-xs mt-0.5">
                                        @if($gelombangAktif->tanggal_seleksi_awal)
                                            {{ \Carbon\Carbon::parse($gelombangAktif->tanggal_seleksi_awal)->translatedFormat('d M') }} s.d {{ \Carbon\Carbon::parse($gelombangAktif->tanggal_seleksi_akhir)->translatedFormat('d F Y') }}
                                        @else
                                            {{ __('Diinfokan via WhatsApp') }}
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <!-- Daftar Ulang -->
                            <div class="bg-white/5 border border-white/5 rounded-xl p-3 flex gap-3 items-center">
                                <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="clipboard-check" class="w-4 h-4"></i>
                                </div>
                                <div class="leading-tight text-[11px]">
                                    <p class="text-slate-400 font-bold uppercase tracking-wider">{{ __('Daftar Ulang Santri') }}</p>
                                    <p class="font-bold text-white text-xs mt-0.5">
                                        @if($gelombangAktif->tanggal_daftar_ulang_awal)
                                            {{ \Carbon\Carbon::parse($gelombangAktif->tanggal_daftar_ulang_awal)->translatedFormat('d M') }} s.d {{ \Carbon\Carbon::parse($gelombangAktif->tanggal_daftar_ulang_akhir)->translatedFormat('d F Y') }}
                                        @else
                                            {{ __('Setelah Pengumuman Lulus') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- CTA Button -->
                        <div class="relative z-10">
                            <a href="{{ route('frontend.psb.daftar', ['gelombang_id' => $gelombangAktif->id]) }}" class="block w-full py-4 text-center bg-yellow-500 hover:bg-yellow-400 text-slate-950 font-bold rounded-xl shadow-lg transition-all text-sm hover:-translate-y-0.5">
                                {{ __('Daftar Sekarang') }}
                            </a>
                        </div>
                    </div>
                </div>

            </div>

        @else
            {{-- Pendaftaran Ditutup --}}
            <div class="bg-white dark:bg-surface-900 p-12 rounded-3xl shadow-lg dark:shadow-none border border-surface-100 dark:border-surface-800 text-center transition-colors duration-300 relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-1 bg-surface-200 dark:bg-surface-800"></div>
                <div class="w-24 h-24 bg-surface-100 dark:bg-surface-800 text-surface-400 dark:text-surface-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i data-lucide="lock" class="w-10 h-10"></i>
                </div>
                <h2 class="text-3xl font-bold text-surface-900 dark:text-white mb-4">{{ __('Pendaftaran Sedang Ditutup') }}</h2>
                <p class="text-surface-600 dark:text-surface-400 text-lg max-w-lg mx-auto mb-8 leading-relaxed">{{ __('Saat ini belum ada gelombang pendaftaran santri baru yang aktif atau masa pendaftaran telah berakhir. Silakan kembali lagi nanti atau hubungi kontak kami untuk informasi lebih lanjut.') }}</p>
                <a href="{{ route('frontend.home') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-surface-800 dark:bg-surface-700 hover:bg-surface-900 dark:hover:bg-surface-600 text-white font-bold rounded-xl transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    {{ __('Kembali ke Beranda') }}
                </a>
            </div>
        @endif

    </div>
</section>
@endsection

@push('scripts')
@if($gelombangsAktif->count() > 0)
@php $gelombangAktif = $gelombangsAktif->first(); @endphp
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Ambil tanggal tutup dari model, format YYYY-MM-DDT23:59:59
        const targetDate = new Date("{{ $gelombangAktif->tanggal_tutup->format('Y-m-d') }}T23:59:59").getTime();

        const timer = setInterval(() => {
            const now = new Date().getTime();
            const distance = targetDate - now;

            // Jika hitung mundur selesai
            if (distance < 0) {
                clearInterval(timer);
                document.getElementById('countdown-hari').innerText = '0';
                document.getElementById('countdown-jam').innerText = '0';
                document.getElementById('countdown-menit').innerText = '0';
                document.getElementById('countdown-detik').innerText = '0';
                return;
            }

            // Hitung nilai waktu
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // Tampilkan hasil di HTML
            document.getElementById('countdown-hari').innerText = days;
            document.getElementById('countdown-jam').innerText = hours;
            document.getElementById('countdown-menit').innerText = minutes;
            document.getElementById('countdown-detik').innerText = seconds;
        }, 1000);
    });
</script>
@endif
@endpush
