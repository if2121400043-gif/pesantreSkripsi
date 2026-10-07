@extends('frontend.layouts.app')

@section('title', __('Tentang Kami'))

@section('content')
@php
    $panca = [
        ['judul' => 'Kesadaran Beragama', 'isi' => 'Memahami dan mengamalkan ajaran agama secara benar.', 'color' => 'primary'],
        ['judul' => 'Kesadaran Berilmu', 'isi' => 'Menyadari pentingnya mencari dan mengembangkan ilmu.', 'color' => 'secondary'],
        ['judul' => 'Kesadaran Bermasyarakat', 'isi' => 'Peduli dan memberi manfaat bagi masyarakat luas.', 'color' => 'accent'],
        ['judul' => 'Kesadaran Berbangsa dan Bernegara', 'isi' => 'Setia pada tanah air serta taat pada aturan kehidupan berbangsa.', 'color' => 'success'],
        ['judul' => 'Kesadaran Berorganisasi', 'isi' => 'Mampu bekerja sama, memimpin, dan mengelola organisasi.', 'color' => 'primary'],
    ];
    $trilogi = [
        ['judul' => 'Memperhatikan Kewajiban Fardlu ‘Ain', 'isi' => 'Menjalankan ibadah wajib yang menjadi tanggungan utama seorang Muslim secara istiqamah.', 'icon' => 'moon-star'],
        ['judul' => 'Mawas Diri dengan Meninggalkan Dosa-dosa Besar', 'isi' => 'Menjauhi perbuatan maksiat atau dosa besar yang dapat merusak akidah dan akhlak.', 'icon' => 'shield-alert'],
        ['judul' => 'Berakhlak Baik Kepada Allah dan Makhluk', 'isi' => 'Menjaga etika dan keluhuran budi kepada Allah SWT, sesama makhluk, serta lingkungan.', 'icon' => 'heart-handshake'],
    ];
    $fasilitas = [
        ['nama' => 'Masjid Pusat Ibadah', 'ikon' => 'moon', 'color' => 'primary'],
        ['nama' => 'Ruang Kelas Nyaman', 'ikon' => 'book-open', 'color' => 'secondary'],
        ['nama' => 'Asrama Putra & Putri', 'ikon' => 'home', 'color' => 'accent'],
        ['nama' => 'Sarana Olahraga', 'ikon' => 'trophy', 'color' => 'success'],
    ];
    $struktur = [
        ['nama' => 'Pimpinan Pesantren', 'jabatan' => 'Pengasuh & Penanggung Jawab', 'ikon' => 'user-check'],
        ['nama' => 'Direktur Pendidikan', 'jabatan' => 'Pengelola Program', 'ikon' => 'graduation-cap'],
        ['nama' => 'Kepala Pengasuhan', 'jabatan' => 'Pembina Kedisiplinan', 'ikon' => 'shield'],
        ['nama' => 'Administrasi', 'jabatan' => 'Pelayanan Pesantren', 'ikon' => 'briefcase'],
    ];
@endphp

{{-- ═══ HERO SECTION ═══ --}}
<section class="relative overflow-hidden bg-surface-900 dark:bg-surface-950 text-white min-h-[60vh] flex items-center justify-center pt-20">
    {{-- Decorative patterns & Glows --}}
    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
    <div class="absolute top-1/4 right-1/4 w-[500px] h-[500px] bg-primary-500/20 rounded-full blur-[120px] pointer-events-none -translate-y-1/2 translate-x-1/4"></div>
    <div class="absolute bottom-1/4 left-1/4 w-[400px] h-[400px] bg-secondary-500/20 rounded-full blur-[100px] pointer-events-none translate-y-1/4 -translate-x-1/4"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-surface-900/40 via-surface-900/80 to-surface-950/90 pointer-events-none z-0"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full py-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                {{-- Badge --}}
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 border border-white/20 text-primary-300 text-xs font-bold tracking-[0.2em] uppercase mb-6 backdrop-blur-md">
                    <i data-lucide="info" class="w-4 h-4"></i>
                    {{ __('Selayang Pandang') }}
                </span>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black mb-6 tracking-tight text-white font-heading leading-tight">
                    {{ __('Tentang Kami') }}<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-400 to-secondary-300">{{ $pesantren->nama ?? 'Pondok Pesantren Nurul Furqon' }}</span>
                </h1>
                
                <p class="text-lg text-surface-300 max-w-xl font-medium leading-relaxed mb-8 border-l-4 border-primary-500 pl-4">
                    {{ Str::limit(strip_tags($pesantren?->sejarah ?? __('Mengenal lebih dekat perjalanan, nilai, dan lingkungan pendidikan pesantren kami.')), 150) }}
                </p>
            </div>
            
            <div class="relative hidden lg:block group">
                <div class="absolute inset-0 bg-gradient-to-tr from-primary-500/30 to-secondary-500/30 rounded-3xl blur-2xl transform group-hover:scale-105 transition-transform duration-500"></div>
                <img src="{{ asset('images/kegiatan-pesantren-1200.webp') }}" onerror="this.src='https://images.unsplash.com/photo-1542810634-71277d95dcbb?q=80&w=1200&auto=format&fit=crop'" alt="{{ __('Kegiatan pesantren') }}" class="relative rounded-3xl shadow-2xl border border-white/10 object-cover w-full h-[350px] transform group-hover:-translate-y-2 transition-transform duration-500">
            </div>
        </div>
    </div>

    {{-- Bottom Curve --}}
    <div class="absolute bottom-0 left-0 right-0 z-20 translate-y-[1px]">
        <svg class="w-full h-12 md:h-20 lg:h-28 text-surface-50 dark:text-surface-950 fill-current" viewBox="0 0 1440 120" preserveAspectRatio="none">
            <path d="M0,120 L1440,120 L1440,64 C1120,120 320,120 0,64 Z"></path>
        </svg>
    </div>
</section>

{{-- ═══ SEJARAH & SELAYANG PANDANG ═══ --}}
<section class="py-20 bg-surface-50 dark:bg-surface-950 relative">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="bg-white dark:bg-surface-900 rounded-3xl p-8 md:p-12 shadow-xl border border-surface-200/60 dark:border-surface-800/60">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-black text-surface-900 dark:text-white font-heading">{{ __('Sejarah Pesantren') }}</h2>
                <div class="w-24 h-1.5 bg-gradient-to-r from-primary-500 to-secondary-500 mx-auto mt-4 rounded-full"></div>
            </div>
            <div class="prose prose-lg dark:prose-invert max-w-none text-surface-600 dark:text-surface-400 prose-headings:font-heading prose-headings:font-bold prose-a:text-primary-600 dark:prose-a:text-primary-400">
                {!! $pesantren?->sejarah ?? '<p class="text-center italic">' . __('Belum ada informasi sejarah pesantren.') . '</p>' !!}
            </div>
        </div>
    </div>
</section>

{{-- ═══ VISI & MISI ═══ --}}
<section class="py-24 bg-white dark:bg-surface-900 relative overflow-hidden border-y border-surface-200/50 dark:border-surface-800/50">
    <div class="absolute top-1/2 left-0 w-96 h-96 bg-primary-500/5 rounded-full blur-[100px] pointer-events-none -translate-y-1/2"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-secondary-500/5 rounded-full blur-[100px] pointer-events-none translate-y-1/2"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16">
            <span class="inline-flex items-center gap-2 py-1.5 px-4 rounded-full bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 font-bold text-xs uppercase tracking-widest mb-4 border border-primary-100 dark:border-primary-800/50 shadow-sm">
                <i data-lucide="target" class="w-3.5 h-3.5"></i> {{ __('Arah Pendidikan') }}
            </span>
            <h2 class="text-4xl font-black text-surface-900 dark:text-white font-heading">{{ __('Visi & Misi') }}</h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            {{-- Visi --}}
            <div class="bg-gradient-to-br from-surface-50 to-white dark:from-surface-800/50 dark:to-surface-900 rounded-3xl p-10 border border-surface-200/80 dark:border-surface-700/80 shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                <div class="w-16 h-16 rounded-2xl bg-primary-100 dark:bg-primary-900/50 text-primary-600 dark:text-primary-400 flex items-center justify-center mb-6 shadow-inner group-hover:scale-110 group-hover:rotate-6 transition-transform">
                    <i data-lucide="eye" class="w-8 h-8"></i>
                </div>
                <h3 class="text-2xl font-black text-surface-900 dark:text-white mb-6 font-heading">{{ __('Visi') }}</h3>
                <div class="prose dark:prose-invert max-w-none text-surface-600 dark:text-surface-400 leading-relaxed font-medium">
                    {!! $pesantren?->visi ?? '<p>' . __('Belum diisi.') . '</p>' !!}
                </div>
            </div>

            {{-- Misi --}}
            <div class="bg-gradient-to-br from-primary-600 to-primary-800 rounded-3xl p-10 border border-primary-700 shadow-xl shadow-primary-900/20 hover:shadow-2xl hover:shadow-primary-900/30 hover:-translate-y-1 transition-all duration-300 text-white group relative overflow-hidden">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 rounded-2xl bg-white/10 text-white flex items-center justify-center mb-6 backdrop-blur-sm group-hover:scale-110 group-hover:-rotate-6 transition-transform border border-white/20">
                        <i data-lucide="rocket" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-2xl font-black mb-6 font-heading">{{ __('Misi') }}</h3>
                    <div class="prose prose-invert max-w-none text-primary-50 leading-relaxed font-medium">
                        {!! $pesantren?->misi ?? '<p>' . __('Belum diisi.') . '</p>' !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══ TRILOGI SANTRI ═══ --}}
<section class="py-24 bg-surface-50 dark:bg-surface-950 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16">
            <span class="inline-flex items-center gap-2 py-1.5 px-4 rounded-full bg-secondary-50 dark:bg-secondary-900/30 text-secondary-600 dark:text-secondary-400 font-bold text-xs uppercase tracking-widest mb-4 border border-secondary-100 dark:border-secondary-800/50 shadow-sm">
                <i data-lucide="book-heart" class="w-3.5 h-3.5"></i> {{ __('Pedoman Sikap') }}
            </span>
            <h2 class="text-4xl font-black text-surface-900 dark:text-white font-heading">{{ __('Trilogi Santri') }}</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($trilogi as $index => $item)
                <div class="bg-white dark:bg-surface-900 rounded-3xl p-8 border border-surface-200/80 dark:border-surface-800 shadow-lg hover:shadow-xl hover:border-secondary-300 dark:hover:border-secondary-700 hover:-translate-y-2 transition-all duration-300 group text-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-secondary-500/5 rounded-bl-[100px] -z-10 group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="w-20 h-20 mx-auto rounded-full bg-gradient-to-br from-secondary-50 to-secondary-100 dark:from-secondary-900/40 dark:to-secondary-800/20 flex items-center justify-center mb-6 shadow-inner group-hover:scale-110 transition-transform">
                        <i data-lucide="{{ $item['icon'] }}" class="w-10 h-10 text-secondary-500"></i>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-secondary-500 text-white font-bold flex items-center justify-center absolute top-6 left-6 shadow-md shadow-secondary-500/30">
                        {{ $index + 1 }}
                    </div>
                    <h3 class="text-xl font-bold text-surface-900 dark:text-white mb-4 leading-snug">{{ __($item['judul']) }}</h3>
                    <p class="text-surface-500 dark:text-surface-400 leading-relaxed font-medium text-sm">{{ __($item['isi']) }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══ PANCA KESADARAN ═══ --}}
<section class="py-24 bg-white dark:bg-surface-900 border-y border-surface-200/50 dark:border-surface-800/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-flex items-center gap-2 py-1.5 px-4 rounded-full bg-accent-50 dark:bg-accent-900/30 text-accent-600 dark:text-accent-400 font-bold text-xs uppercase tracking-widest mb-4 border border-accent-100 dark:border-accent-800/50 shadow-sm">
                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i> {{ __('Nilai Karakter') }}
            </span>
            <h2 class="text-4xl font-black text-surface-900 dark:text-white font-heading">{{ __('Panca Kesadaran Santri') }}</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            @foreach($panca as $index => $item)
                <div class="bg-surface-50/50 dark:bg-surface-800/30 rounded-2xl p-6 border border-surface-100 dark:border-surface-800/50 hover:bg-white dark:hover:bg-surface-800 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 relative group">
                    <div class="text-4xl font-black text-surface-200/50 dark:text-surface-700/30 absolute right-4 top-4 pointer-events-none group-hover:scale-125 group-hover:-rotate-12 transition-transform duration-300">0{{ $index + 1 }}</div>
                    <div class="w-12 h-12 rounded-xl bg-{{ $item['color'] }}-100 dark:bg-{{ $item['color'] }}-900/30 flex items-center justify-center mb-5 text-{{ $item['color'] }}-600 dark:text-{{ $item['color'] }}-400">
                        <i data-lucide="check-circle" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-3 pr-6">{{ __($item['judul']) }}</h3>
                    <p class="text-surface-500 dark:text-surface-400 text-sm leading-relaxed font-medium">{{ __($item['isi']) }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══ FASILITAS & STRUKTUR ═══ --}}
<section class="py-24 bg-surface-50 dark:bg-surface-950 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
            
            {{-- Fasilitas --}}
            <div>
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 rounded-xl bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 flex items-center justify-center">
                        <i data-lucide="building-2" class="w-6 h-6"></i>
                    </div>
                    <h2 class="text-3xl font-black text-surface-900 dark:text-white font-heading">{{ __('Fasilitas') }}</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($fasilitas as $item)
                        <div class="flex items-center gap-4 bg-white dark:bg-surface-900 p-4 rounded-2xl border border-surface-200/60 dark:border-surface-800/60 shadow-sm hover:shadow-md transition-shadow group">
                            <div class="w-10 h-10 rounded-lg bg-{{ $item['color'] }}-50 dark:bg-{{ $item['color'] }}-900/20 text-{{ $item['color'] }}-500 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                <i data-lucide="{{ $item['ikon'] }}" class="w-5 h-5"></i>
                            </div>
                            <span class="font-semibold text-surface-700 dark:text-surface-300 text-sm">{{ __($item['nama']) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Struktur --}}
            <div>
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 rounded-xl bg-secondary-100 dark:bg-secondary-900/30 text-secondary-600 dark:text-secondary-400 flex items-center justify-center">
                        <i data-lucide="users" class="w-6 h-6"></i>
                    </div>
                    <h2 class="text-3xl font-black text-surface-900 dark:text-white font-heading">{{ __('Pengelola') }}</h2>
                </div>
                <div class="space-y-4">
                    @foreach($struktur as $item)
                        <div class="flex items-center gap-5 bg-white dark:bg-surface-900 p-4 rounded-2xl border border-surface-200/60 dark:border-surface-800/60 shadow-sm hover:translate-x-2 transition-transform group">
                            <div class="w-12 h-12 rounded-full bg-surface-100 dark:bg-surface-800 text-surface-500 dark:text-surface-400 flex items-center justify-center flex-shrink-0 group-hover:bg-secondary-500 group-hover:text-white transition-colors">
                                <i data-lucide="{{ $item['ikon'] }}" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-surface-900 dark:text-white">{{ __($item['nama']) }}</h4>
                                <p class="text-xs text-surface-500 dark:text-surface-400 font-medium mt-1">{{ __($item['jabatan']) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</section>
@endsection
