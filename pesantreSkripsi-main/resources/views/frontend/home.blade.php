@extends('frontend.layouts.app')

@section('content')
{{-- ═══ HERO ═══ --}}
<section class="relative overflow-hidden bg-gradient-to-br from-primary-900 via-primary-800 to-surface-900 dark:from-surface-950 dark:via-surface-900 dark:to-surface-950 text-white transition-colors duration-300">
    {{-- Decorative Background --}}
    <div class="absolute inset-0 opacity-[0.05] dark:opacity-[0.02]" style="background-image:url('data:image/svg+xml,%3Csvg width=60 height=60 viewBox=%220 0 60 60%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22none%22 fill-rule=%22evenodd%22%3E%3Cg fill=%22%23ffffff%22 fill-opacity=%221%22%3E%3Cpath d=%22M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z%22/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>
    
    {{-- Glow effect --}}
    <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-secondary-500/20 rounded-full blur-[120px] pointer-events-none -translate-y-1/2 translate-x-1/3"></div>
    <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-primary-500/20 rounded-full blur-[100px] pointer-events-none translate-y-1/3 -translate-x-1/4"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 py-24 md:py-36">
        <div class="max-w-3xl animate-fade-in">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 dark:bg-surface-800/50 border border-white/20 dark:border-surface-700 text-primary-200 dark:text-primary-400 text-xs font-bold tracking-widest uppercase mb-8 backdrop-blur-sm">
                @if($isPsbBuka)
                    <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-accent-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-accent-500"></span></span>
                    {{ __('Pendaftaran Siswa Baru Telah Dibuka') }}
                @else
                    <span class="relative flex h-2 w-2"><span class="relative inline-flex rounded-full h-2 w-2 bg-surface-500"></span></span>
                    {{ __('Pendaftaran Sudah Selesai') }}
                @endif
            </span>
            <h1 class="text-4xl sm:text-5xl lg:text-7xl font-extrabold leading-[1.1] tracking-tight mb-6 text-white">
                {{ __('Membangun Generasi') }}<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-accent-400 via-secondary-300 to-accent-200">{{ __("Qur'ani & Mandiri") }}</span>
            </h1>
            <p class="text-lg text-primary-100/90 dark:text-surface-300 max-w-xl mb-10 leading-relaxed font-medium">
                {{ $pesantren?->nama ?? 'Pesantren Nurul Furqon' }} {{ __('adalah lembaga pendidikan Islam terpadu yang menyeimbangkan ilmu agama, akademik, dan pembentukan akhlak santri.') }}
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('frontend.psb') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-white dark:bg-accent-500 text-primary-900 dark:text-surface-950 font-bold rounded-xl hover:bg-primary-50 dark:hover:bg-accent-400 transition-all shadow-[0_10px_30px_rgba(0,0,0,0.15)] dark:shadow-[0_0_30px_rgba(217,119,6,0.2)] hover:-translate-y-1 hover:shadow-xl text-sm">
                    <i data-lucide="graduation-cap" class="w-5 h-5"></i> {{ __('Daftar PSB Sekarang') }}
                </a>
                <a href="{{ route('frontend.profil') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-white/5 dark:bg-surface-800/50 text-white border border-white/20 dark:border-surface-700 font-bold rounded-xl hover:bg-white/10 dark:hover:bg-surface-700/50 backdrop-blur-sm transition-all text-sm group">
                    {{ __('Kenali Kami') }} <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>
    </div>
    
    {{-- Wave divider --}}
    <div class="absolute bottom-0 left-0 right-0">
        <svg class="w-full h-16 sm:h-24 lg:h-32 text-surface-50 dark:text-surface-950 fill-current" viewBox="0 0 1440 120" preserveAspectRatio="none">
            <path d="M0,64L80,69.3C160,75,320,85,480,80C640,75,800,53,960,48C1120,43,1280,53,1360,58.7L1440,64L1440,120L1360,120C1280,120,1120,120,960,120C800,120,640,120,480,120C320,120,160,120,80,120L0,120Z"></path>
        </svg>
    </div>
</section>

{{-- ═══ STATS ═══ --}}
<section class="relative z-10 -mt-16 sm:-mt-24 lg:-mt-32 pb-20">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white/80 dark:bg-surface-900/80 backdrop-blur-xl rounded-3xl shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] dark:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.5)] border border-white/50 dark:border-surface-800/50 p-6 sm:p-10 animate-fade-in" style="animation-delay: 0.2s;">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 sm:gap-0 sm:divide-x divide-surface-200 dark:divide-surface-800 text-center">
                <div class="flex flex-col items-center gap-3 py-2 group">
                    <div class="w-14 h-14 rounded-2xl bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 flex items-center justify-center group-hover:scale-110 transition-transform"><i data-lucide="users" class="w-6 h-6"></i></div>
                    <span class="text-4xl font-extrabold text-surface-900 dark:text-white">{{ number_format($totalSantri) }}</span>
                    <span class="text-xs font-bold text-surface-500 dark:text-surface-400 uppercase tracking-widest">{{ __('Santri Aktif') }}</span>
                </div>
                <div class="flex flex-col items-center gap-3 py-2 group">
                    <div class="w-14 h-14 rounded-2xl bg-secondary-50 dark:bg-secondary-900/30 text-secondary-600 dark:text-secondary-400 flex items-center justify-center group-hover:scale-110 transition-transform"><i data-lucide="book-open" class="w-6 h-6"></i></div>
                    <span class="text-4xl font-extrabold text-surface-900 dark:text-white">{{ number_format($totalPegawai) }}</span>
                    <span class="text-xs font-bold text-surface-500 dark:text-surface-400 uppercase tracking-widest">{{ __('Tenaga Pendidik') }}</span>
                </div>
                <div class="flex flex-col items-center gap-3 py-2 group">
                    <div class="w-14 h-14 rounded-2xl bg-accent-50 dark:bg-accent-900/30 text-accent-600 dark:text-accent-400 flex items-center justify-center group-hover:scale-110 transition-transform"><i data-lucide="building" class="w-6 h-6"></i></div>
                    <span class="text-4xl font-extrabold text-surface-900 dark:text-white">{{ number_format($totalRombel) }}</span>
                    <span class="text-xs font-bold text-surface-500 dark:text-surface-400 uppercase tracking-widest">{{ __('Rombongan Belajar') }}</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══ PROGRAM UNGGULAN ═══ --}}
<section class="py-24 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-20">
            <span class="inline-block py-1 px-3 rounded-full bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 font-bold text-xs uppercase tracking-widest mb-4 border border-primary-100 dark:border-primary-800">{{ __('Keunggulan Kami') }}</span>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-surface-900 dark:text-white mb-6">{{ __('Program Unggulan') }}</h2>
            <p class="text-surface-600 dark:text-surface-400 text-lg leading-relaxed">{{ __('Kurikulum terpadu yang membentuk santri berakhlak mulia, berilmu, dan siap bersaing di era global.') }}</p>
        </div>
        @php
            $programs = [
                ['icon' => 'book-open-check', 'color' => 'primary', 'title' => __('Tahfidz Al-Qur\'an'), 'desc' => __('Program hafalan Al-Qur\'an bersanad dengan metode yang terbukti efektif dan menyenangkan.')],
                ['icon' => 'rocket', 'color' => 'secondary', 'title' => __('Kewirausahaan'), 'desc' => __('Menumbuhkan jiwa kewirausahaan dan kemandirian santri dalam melihat peluang inovasi.')],
                ['icon' => 'shield-check', 'color' => 'accent', 'title' => __('Kepemimpinan'), 'desc' => __('Melatih kemampuan memimpin, berorganisasi, dan mempengaruhi orang lain secara positif.')],
                ['icon' => 'languages', 'color' => 'success', 'title' => __('Bahasa Asing'), 'desc' => __('Membentuk santri yang mampu berbahasa Arab dan Inggris secara aktif maupun pasif.')],
            ];
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($programs as $p)
            <div class="group bg-white dark:bg-surface-900 rounded-3xl border border-surface-200 dark:border-surface-800 p-8 hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] dark:hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.5)] hover:border-{{ $p['color'] }}-300 dark:hover:border-{{ $p['color'] }}-700 transition-all duration-300 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-{{ $p['color'] }}-50 dark:bg-{{ $p['color'] }}-900/20 rounded-bl-full -z-10 group-hover:scale-125 transition-transform duration-500"></div>
                <div class="w-14 h-14 rounded-2xl bg-{{ $p['color'] }}-100 dark:bg-{{ $p['color'] }}-900/40 text-{{ $p['color'] }}-600 dark:text-{{ $p['color'] }}-400 flex items-center justify-center mb-6 group-hover:-translate-y-2 transition-transform duration-300">
                    <i data-lucide="{{ $p['icon'] }}" class="w-7 h-7"></i>
                </div>
                <h3 class="text-xl font-bold text-surface-900 dark:text-white mb-3">{{ $p['title'] }}</h3>
                <p class="text-surface-600 dark:text-surface-400 text-sm leading-relaxed">{{ $p['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══ TENTANG PESANTREN ═══ --}}
<section class="py-24 bg-surface-100 dark:bg-surface-900/50 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row items-center gap-16 lg:gap-24">
            <div class="w-full lg:w-5/12 relative">
                <div class="aspect-[4/5] rounded-[2rem] overflow-hidden shadow-2xl shadow-primary-900/20 relative group">
                    <div class="absolute inset-0 bg-primary-900/20 group-hover:bg-transparent transition-colors duration-500 z-10"></div>
                    <picture>
                        <source srcset="{{ asset('images/kegiatan-pesantren-800.webp') }} 800w, {{ asset('images/kegiatan-pesantren-1200.webp') }} 1200w" type="image/webp">
                        <source srcset="{{ asset('images/kegiatan-pesantren-800.jpg') }} 800w, {{ asset('images/kegiatan-pesantren-1200.jpg') }} 1200w" type="image/jpeg">
                        <img src="{{ asset('images/kegiatan-pesantren-800.webp') }}" alt="Kegiatan Pesantren" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" loading="lazy" decoding="async">
                    </picture>
                </div>
                <div class="absolute -bottom-8 -right-8 sm:-bottom-10 sm:-right-10 bg-gradient-to-br from-primary-600 to-secondary-600 text-white p-8 rounded-3xl shadow-xl shadow-primary-600/30 border border-white/10 z-20">
                    <div class="text-4xl font-extrabold mb-1">{{ ($pesantren?->tahun_berdiri) ? date('Y') - $pesantren?->tahun_berdiri : '10' }}+</div>
                    <div class="text-primary-100 text-sm font-bold uppercase tracking-widest">{{ __('Tahun Berdiri') }}</div>
                </div>
            </div>
            <div class="w-full lg:w-7/12 mt-10 lg:mt-0">
                <span class="inline-flex items-center gap-2 text-primary-600 dark:text-primary-400 font-bold text-xs uppercase tracking-widest mb-4">
                    <span class="w-8 h-px bg-primary-600 dark:bg-primary-400"></span> {{ __('Selamat Datang di') }}
                </span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-surface-900 dark:text-white mb-8 leading-[1.1]">{{ $pesantren?->nama ?? 'Pesantren Nurul Furqon' }}</h2>
                <p class="text-surface-600 dark:text-surface-300 leading-relaxed mb-10 text-lg font-medium">
                    {{ Str::limit(strip_tags($pesantren?->visi ?? 'Lembaga pendidikan Islam terpadu yang menyeimbangkan ilmu agama, akademik, dan teknologi. Kami berdedikasi mencetak generasi yang berakhlak mulia, berilmu, serta siap bersaing di era global.'), 300) }}
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-12">
                    @php $features = [['icon'=>'check-circle','t'=>__('Kurikulum Terpadu'),'d'=>__('Perpaduan kurikulum nasional & pesantren')],['icon'=>'check-circle','t'=>__('Tenaga Ahli'),'d'=>__('Pengajar berpengalaman & bersertifikasi')],['icon'=>'check-circle','t'=>__('Fasilitas Lengkap'),'d'=>__('Asrama nyaman, masjid & ruang belajar')],['icon'=>'check-circle','t'=>__('Lingkungan Islami'),'d'=>__('Membentuk karakter santri berakhlak')]]; @endphp
                    @foreach($features as $f)
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="{{ $f['icon'] }}" class="w-5 h-5 text-primary-600 dark:text-primary-400"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-surface-900 dark:text-white text-[15px] mb-1">{{ $f['t'] }}</h4>
                            <p class="text-surface-500 dark:text-surface-400 text-sm leading-snug">{{ $f['d'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('frontend.profil') }}" class="inline-flex items-center gap-3 px-8 py-3.5 bg-white dark:bg-surface-800 text-primary-700 dark:text-primary-400 border border-surface-200 dark:border-surface-700 font-bold rounded-xl hover:bg-surface-50 dark:hover:bg-surface-700 shadow-sm transition-all group">
                    {{ __('Selengkapnya tentang kami') }} <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ═══ UNIT PENDIDIKAN ═══ --}}
@if($lembagas->count() > 0)
<section class="py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-end gap-6 mb-16">
            <div class="max-w-2xl">
                <span class="inline-block py-1 px-3 rounded-full bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 font-bold text-xs uppercase tracking-widest mb-4 border border-primary-100 dark:border-primary-800">{{ __('Jenjang Pendidikan') }}</span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-surface-900 dark:text-white">{{ __('Unit Pendidikan') }}</h2>
            </div>
            <p class="text-surface-500 dark:text-surface-400 max-w-md text-right hidden md:block">
                {{ __('Pendidikan Islam terpadu yang membina generasi dari berbagai jenjang, mengantarkan santri menuju kesuksesan dunia akhirat.') }}
            </p>
        </div>
        
        @php $lembagaColors = ['primary','secondary','accent','success','info']; @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($lembagas as $i => $lembaga)
            @php $c = $lembagaColors[$i % count($lembagaColors)]; @endphp
            <div class="bg-white dark:bg-surface-900 rounded-[2rem] border border-surface-200 dark:border-surface-800 p-8 hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] dark:hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.5)] hover:border-{{ $c }}-300 dark:hover:border-{{ $c }}-700 transition-all duration-300 group">
                <div class="flex justify-between items-start mb-8">
                    <div class="w-16 h-16 rounded-2xl bg-{{ $c }}-50 dark:bg-{{ $c }}-900/30 text-{{ $c }}-600 dark:text-{{ $c }}-400 flex items-center justify-center group-hover:scale-110 group-hover:rotate-3 transition-transform">
                        <i data-lucide="school" class="w-8 h-8"></i>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-surface-50 dark:bg-surface-800 flex items-center justify-center text-surface-400 group-hover:bg-{{ $c }}-500 group-hover:text-white transition-colors">
                        <i data-lucide="arrow-up-right" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="text-surface-400 dark:text-surface-500 text-xs font-bold uppercase tracking-widest mb-2">{{ $lembaga->jenjang ?? __('Pendidikan Formal') }}</p>
                <h3 class="text-2xl font-extrabold text-surface-900 dark:text-white mb-4 group-hover:text-{{ $c }}-600 dark:group-hover:text-{{ $c }}-400 transition-colors">{{ $lembaga->singkatan ?? $lembaga->nama }}</h3>
                <p class="text-surface-500 dark:text-surface-400 text-sm leading-relaxed">{{ $lembaga->nama }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═══ BERITA TERBARU ═══ --}}
<section class="py-24 bg-surface-50 dark:bg-surface-900/30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-6 mb-16">
            <div>
                <span class="inline-block py-1 px-3 rounded-full bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 font-bold text-xs uppercase tracking-widest mb-4 border border-primary-100 dark:border-primary-800">{{ __('Pembaruan') }}</span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-surface-900 dark:text-white">{{ __('Berita & Kegiatan') }}</h2>
            </div>
            <a href="{{ route('frontend.berita') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 rounded-xl text-primary-600 dark:text-primary-400 font-bold hover:bg-surface-100 dark:hover:bg-surface-700 transition-colors group">
                {{ __('Lihat Semua') }} <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($berita_terbaru as $berita)
            <article class="bg-white dark:bg-surface-900 rounded-3xl overflow-hidden shadow-sm hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] dark:shadow-none border border-surface-100 dark:border-surface-800 transition-all duration-300 group flex flex-col">
                <div class="aspect-[4/3] overflow-hidden bg-surface-100 dark:bg-surface-800 relative">
                    @if($berita->gambar_cover)
                        <img src="{{ Storage::url($berita->gambar_cover) }}" alt="{{ $berita->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-surface-300 dark:text-surface-600"><i data-lucide="image" class="w-16 h-16"></i></div>
                    @endif
                    <div class="absolute top-4 left-4">
                        <span class="px-3 py-1 bg-white/90 dark:bg-surface-900/90 backdrop-blur-md text-primary-700 dark:text-primary-400 text-xs font-bold rounded-lg shadow-sm">
                            {{ $berita->kategori->nama ?? 'Umum' }}
                        </span>
                    </div>
                </div>
                <div class="p-8 flex flex-col flex-grow">
                    <span class="text-xs text-surface-400 font-bold tracking-wider uppercase mb-3 flex items-center gap-2">
                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i> {{ $berita->tanggal_format }}
                    </span>
                    <h3 class="text-xl font-bold text-surface-900 dark:text-white mb-3 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors line-clamp-2 leading-snug">
                        <a href="{{ route('frontend.berita.show', $berita->slug) }}">{{ $berita->judul }}</a>
                    </h3>
                    <p class="text-surface-500 dark:text-surface-400 text-sm line-clamp-3 mb-6 flex-grow leading-relaxed">{{ $berita->ringkasan ?? Str::limit(strip_tags($berita->konten), 120) }}</p>
                    
                    <a href="{{ route('frontend.berita.show', $berita->slug) }}" class="text-primary-600 dark:text-primary-400 font-bold text-sm flex items-center gap-2 group/l">
                        {{ __('Jelajahi Artikel') }} <i data-lucide="arrow-right" class="w-4 h-4 group-hover/l:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </article>
            @empty
            <div class="col-span-full text-center py-20 bg-white dark:bg-surface-900 rounded-[2rem] border border-dashed border-surface-300 dark:border-surface-700">
                <div class="w-20 h-20 bg-surface-50 dark:bg-surface-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="newspaper" class="w-10 h-10 text-surface-300 dark:text-surface-500"></i>
                </div>
                <h3 class="text-xl font-bold text-surface-900 dark:text-white mb-2">{{ __('Belum Ada Berita') }}</h3>
                <p class="text-surface-500 dark:text-surface-400">{{ __('Berita dan kegiatan akan segera ditambahkan.') }}</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

{{-- ═══ CTA FINAL ═══ --}}
<section class="relative py-32 overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-primary-900 via-secondary-900 to-surface-950"></div>
    <div class="absolute inset-0 opacity-[0.05]" style="background-image:url('data:image/svg+xml,%3Csvg width=60 height=60 viewBox=%220 0 60 60%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22none%22 fill-rule=%22evenodd%22%3E%3Cg fill=%22%23ffffff%22 fill-opacity=%221%22%3E%3Cpath d=%22M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z%22/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>
    
    {{-- Decorative circles --}}
    <div class="absolute top-0 right-1/4 w-[400px] h-[400px] bg-accent-500/20 rounded-full blur-[100px] pointer-events-none -translate-y-1/2"></div>
    <div class="absolute bottom-0 left-1/4 w-[400px] h-[400px] bg-primary-500/30 rounded-full blur-[100px] pointer-events-none translate-y-1/2"></div>

    <div class="max-w-4xl mx-auto px-4 relative z-10 text-center">
        <h2 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold mb-8 leading-[1.1] text-white">
            {{ __('Mari Membangun Generasi Rabbani Bersama') }}
        </h2>
        <p class="text-primary-100/90 text-lg sm:text-xl mb-12 max-w-2xl mx-auto leading-relaxed">
            {{ __('Wujudkan masa depan gemilang bagi putra-putri Anda. Pendaftaran santri baru telah dibuka untuk tahun ajaran ini.') }}
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('frontend.psb') }}" class="inline-flex items-center gap-2 bg-accent-500 text-surface-950 font-extrabold px-10 py-5 rounded-xl shadow-[0_0_30px_rgba(217,119,6,0.3)] hover:shadow-[0_0_40px_rgba(217,119,6,0.5)] hover:bg-accent-400 transition-all hover:-translate-y-1">
                <i data-lucide="edit-3" class="w-5 h-5"></i> {{ __('Daftar Sekarang') }}
            </a>
        </div>
    </div>
</section>
@endsection
