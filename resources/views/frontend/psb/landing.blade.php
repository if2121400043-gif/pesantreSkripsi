@extends('frontend.layouts.app')

@section('title', __('Penerimaan Santri Baru (PSB)'))

@section('content')



{{-- ═══ HERO SECTION ═══ --}}
<section class="relative overflow-hidden bg-surface-900 dark:bg-surface-950 text-white min-h-[85vh] flex items-center justify-center">
    {{-- Decorative patterns & Glows --}}
    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
    <div class="absolute top-1/4 left-1/4 w-[600px] h-[600px] bg-primary-500/20 rounded-full blur-[120px] pointer-events-none -translate-y-1/2 -translate-x-1/4"></div>
    <div class="absolute bottom-1/4 right-1/4 w-[500px] h-[500px] bg-secondary-500/20 rounded-full blur-[100px] pointer-events-none translate-y-1/4 translate-x-1/4"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-gradient-to-b from-surface-900/50 via-surface-900/80 to-surface-950/90 pointer-events-none z-0"></div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center py-24 w-full">
        {{-- Badge --}}
        <div class="flex justify-center mb-10">
            <span class="inline-flex items-center gap-3 px-6 py-2.5 rounded-full bg-white/5 border border-white/10 text-primary-200 text-sm font-black tracking-[0.2em] uppercase backdrop-blur-xl shadow-2xl shadow-primary-500/20">
                <div class="w-2 h-2 rounded-full bg-primary-400 animate-pulse"></div>
                {{ __('Penerimaan Santri Baru') }}
            </span>
        </div>

        {{-- Main Title --}}
        <h1 class="text-5xl md:text-6xl lg:text-7xl font-black mb-6 tracking-tight text-white font-heading leading-[1.1]">
            {{ __('Mari Bergabung') }} <br />
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-400 via-secondary-300 to-primary-200">{{ __('Bersama Kami') }}</span>
        </h1>

        {{-- Pesantren Name & Motto --}}
        <p class="text-xl md:text-2xl font-bold text-primary-100 mb-4 max-w-3xl mx-auto">
            {{ $pesantren->nama ?? 'Pondok Pesantren Nurul Furqon' }}
        </p>
        <p class="text-base text-surface-400 max-w-2xl mx-auto font-medium leading-relaxed mb-12">
            Mendidik generasi Qur'ani yang cerdas, mandiri, dan berakhlakul karimah. Wujudkan masa depan gemilang dengan landasan ilmu agama yang kuat.
        </p>

        {{-- CTA Buttons --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
            @if($isPsbBuka ?? true)
                <a href="{{ route('frontend.psb.daftar') }}" class="inline-flex items-center gap-3 px-10 py-5 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-400 hover:to-primary-500 text-white font-black rounded-2xl shadow-[0_0_40px_rgba(var(--color-primary-500),0.4)] hover:shadow-[0_0_60px_rgba(var(--color-primary-500),0.6)] transition-all duration-500 text-lg hover:-translate-y-1 uppercase tracking-widest relative overflow-hidden group">
                    <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-500 ease-out"></div>
                    <i data-lucide="pen-tool" class="w-6 h-6 relative z-10"></i>
                    <span class="relative z-10">{{ __('Daftar Sekarang') }}</span>
                </a>
            @endif
            
            <a href="#alur-pendaftaran" class="inline-flex items-center gap-3 px-10 py-5 bg-surface-800/50 hover:bg-surface-800 border border-surface-700 hover:border-surface-600 text-white font-bold rounded-2xl shadow-xl backdrop-blur-md transition-all duration-300 text-lg hover:-translate-y-1">
                <i data-lucide="info" class="w-6 h-6 text-primary-400"></i>
                {{ __('Informasi PSB') }}
            </a>
        </div>
    </div>

    {{-- Bottom Curve --}}
    <div class="absolute bottom-0 left-0 right-0 z-20 translate-y-[1px]">
        <svg class="w-full h-16 sm:h-24 lg:h-32 text-surface-50 dark:text-surface-950 fill-current" viewBox="0 0 1440 120" preserveAspectRatio="none">
            <path d="M0,120 L1440,120 L1440,64 C1120,120 320,120 0,64 Z"></path>
        </svg>
    </div>
</section>

{{-- ═══ ALUR PENDAFTARAN ONLINE ═══ --}}
<section id="alur-pendaftaran" class="py-24 sm:py-32 bg-surface-50 dark:bg-surface-950 relative overflow-hidden">
    {{-- Decorative Background --}}
    <div class="absolute top-1/2 right-0 w-[500px] h-[500px] bg-primary-500/5 rounded-full blur-3xl -translate-y-1/2 pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        {{-- Section Header --}}
        <div class="text-center mb-20 max-w-3xl mx-auto">
            <span class="inline-flex items-center gap-2 py-1.5 px-4 rounded-full bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 font-bold text-xs uppercase tracking-widest mb-6 border border-primary-100 dark:border-primary-800/50 shadow-sm">
                <i data-lucide="git-merge" class="w-3.5 h-3.5"></i> {{ __('Proses Mudah') }}
            </span>
            <h2 class="text-4xl md:text-5xl font-black text-surface-900 dark:text-white mb-6 tracking-tight">
                {{ __('Alur Pendaftaran Online') }}
            </h2>
            <p class="text-lg text-surface-500 dark:text-surface-400 font-medium">
                {{ __('Hanya butuh 5 langkah mudah untuk bergabung menjadi bagian dari santri kami. Sistem online kami siap melayani 24/7.') }}
            </p>
        </div>

        {{-- Steps Grid --}}
        <div class="relative">
            {{-- Connecting Line for Desktop --}}
            <div class="hidden lg:block absolute top-[52px] left-0 w-full h-[2px] bg-gradient-to-r from-surface-200 via-primary-300 to-surface-200 dark:from-surface-800 dark:via-primary-700 dark:to-surface-800 z-0"></div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-8 lg:gap-6 relative z-10">
                {{-- Step 1 --}}
                <div class="group bg-white dark:bg-surface-900 rounded-3xl border border-surface-200/80 dark:border-surface-800 p-8 shadow-xl hover:shadow-2xl hover:shadow-primary-500/10 hover:-translate-y-2 transition-all duration-500 text-center flex flex-col items-center">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/50 dark:to-primary-800/30 text-primary-600 dark:text-primary-400 flex items-center justify-center font-black text-2xl mb-8 shadow-inner border border-primary-200/50 dark:border-primary-700/50 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500 relative bg-white dark:bg-surface-900">
                        1
                        <div class="absolute -top-3 -right-3 w-8 h-8 rounded-full bg-primary-500 text-white flex items-center justify-center shadow-lg shadow-primary-500/40">
                            <i data-lucide="user-plus" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-black text-surface-900 dark:text-white mb-3 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">{{ __('Pembuatan Akun') }}</h3>
                    <p class="text-sm text-surface-500 dark:text-surface-400 leading-relaxed font-medium">{{ __('Isi identitas awal dan dapatkan Nomor Registrasi unik pendaftaran Anda.') }}</p>
                </div>

                {{-- Step 2 --}}
                <div class="group bg-white dark:bg-surface-900 rounded-3xl border border-surface-200/80 dark:border-surface-800 p-8 shadow-xl hover:shadow-2xl hover:shadow-secondary-500/10 hover:-translate-y-2 transition-all duration-500 text-center flex flex-col items-center lg:mt-8">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-secondary-50 to-secondary-100 dark:from-secondary-900/50 dark:to-secondary-800/30 text-secondary-600 dark:text-secondary-400 flex items-center justify-center font-black text-2xl mb-8 shadow-inner border border-secondary-200/50 dark:border-secondary-700/50 group-hover:scale-110 group-hover:-rotate-3 transition-transform duration-500 relative bg-white dark:bg-surface-900">
                        2
                        <div class="absolute -top-3 -right-3 w-8 h-8 rounded-full bg-secondary-500 text-white flex items-center justify-center shadow-lg shadow-secondary-500/40">
                            <i data-lucide="file-edit" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-black text-surface-900 dark:text-white mb-3 group-hover:text-secondary-600 dark:group-hover:text-secondary-400 transition-colors">{{ __('Lengkapi Data') }}</h3>
                    <p class="text-sm text-surface-500 dark:text-surface-400 leading-relaxed font-medium">{{ __('Lengkapi formulir data diri santri dan data orang tua/wali dengan lengkap.') }}</p>
                </div>

                {{-- Step 3 --}}
                <div class="group bg-white dark:bg-surface-900 rounded-3xl border border-surface-200/80 dark:border-surface-800 p-8 shadow-xl hover:shadow-2xl hover:shadow-accent-500/10 hover:-translate-y-2 transition-all duration-500 text-center flex flex-col items-center">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-accent-50 to-accent-100 dark:from-accent-900/50 dark:to-accent-800/30 text-accent-600 dark:text-accent-400 flex items-center justify-center font-black text-2xl mb-8 shadow-inner border border-accent-200/50 dark:border-accent-700/50 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500 relative bg-white dark:bg-surface-900">
                        3
                        <div class="absolute -top-3 -right-3 w-8 h-8 rounded-full bg-accent-500 text-white flex items-center justify-center shadow-lg shadow-accent-500/40">
                            <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-black text-surface-900 dark:text-white mb-3 group-hover:text-accent-600 dark:group-hover:text-accent-400 transition-colors">{{ __('Unggah Berkas') }}</h3>
                    <p class="text-sm text-surface-500 dark:text-surface-400 leading-relaxed font-medium">{{ __('Scan dan unggah berkas persyaratan seperti KK, Akta, dan Ijazah terakhir.') }}</p>
                </div>

                {{-- Step 4 --}}
                <div class="group bg-white dark:bg-surface-900 rounded-3xl border border-surface-200/80 dark:border-surface-800 p-8 shadow-xl hover:shadow-2xl hover:shadow-success-500/10 hover:-translate-y-2 transition-all duration-500 text-center flex flex-col items-center lg:mt-8">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-success-50 to-success-100 dark:from-success-900/50 dark:to-success-800/30 text-success-600 dark:text-success-400 flex items-center justify-center font-black text-2xl mb-8 shadow-inner border border-success-200/50 dark:border-success-700/50 group-hover:scale-110 group-hover:-rotate-3 transition-transform duration-500 relative bg-white dark:bg-surface-900">
                        4
                        <div class="absolute -top-3 -right-3 w-8 h-8 rounded-full bg-success-500 text-white flex items-center justify-center shadow-lg shadow-success-500/40">
                            <i data-lucide="credit-card" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-black text-surface-900 dark:text-white mb-3 group-hover:text-success-600 dark:group-hover:text-success-400 transition-colors">{{ __('Pembayaran') }}</h3>
                    <p class="text-sm text-surface-500 dark:text-surface-400 leading-relaxed font-medium">{{ __('Lakukan pembayaran biaya pendaftaran via transfer ke rekening pesantren.') }}</p>
                </div>

                {{-- Step 5 --}}
                <div class="group bg-white dark:bg-surface-900 rounded-3xl border border-surface-200/80 dark:border-surface-800 p-8 shadow-xl hover:shadow-2xl hover:shadow-primary-500/10 hover:-translate-y-2 transition-all duration-500 text-center flex flex-col items-center">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/50 dark:to-primary-800/30 text-primary-600 dark:text-primary-400 flex items-center justify-center font-black text-2xl mb-8 shadow-inner border border-primary-200/50 dark:border-primary-700/50 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500 relative bg-white dark:bg-surface-900">
                        5
                        <div class="absolute -top-3 -right-3 w-8 h-8 rounded-full bg-primary-500 text-white flex items-center justify-center shadow-lg shadow-primary-500/40">
                            <i data-lucide="printer" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-black text-surface-900 dark:text-white mb-3 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">{{ __('Cetak Bukti') }}</h3>
                    <p class="text-sm text-surface-500 dark:text-surface-400 leading-relaxed font-medium">{{ __('Cetak bukti pendaftaran untuk dibawa saat verifikasi di pondok pesantren.') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══ SYARAT PENDAFTARAN ═══ --}}
<section class="py-24 sm:py-32 bg-white dark:bg-surface-900 border-y border-surface-200/50 dark:border-surface-800/50 relative overflow-hidden">
    <div class="absolute -left-48 top-1/2 -translate-y-1/2 w-96 h-96 bg-secondary-500/10 rounded-full blur-[100px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-20 items-center">
            {{-- Left: Title & Description --}}
            <div class="lg:col-span-5 text-center lg:text-left">
                <span class="inline-flex items-center gap-2 py-1.5 px-4 rounded-full bg-secondary-50 dark:bg-secondary-900/30 text-secondary-600 dark:text-secondary-400 font-bold text-xs uppercase tracking-widest mb-6 border border-secondary-100 dark:border-secondary-800/50 shadow-sm">
                    <i data-lucide="file-check" class="w-3.5 h-3.5"></i> {{ __('Persiapan Berkas') }}
                </span>
                <h2 class="text-4xl md:text-5xl font-black text-surface-900 dark:text-white mb-6 tracking-tight leading-[1.1]">
                    {{ __('Syarat Pendaftaran') }}
                </h2>
                <p class="text-lg text-surface-500 dark:text-surface-400 leading-relaxed font-medium mb-8">
                    {{ __('Siapkan berkas-berkas berikut untuk melengkapi persyaratan pendaftaran santri baru di Pondok Pesantren Nurul Furqon.') }}
                </p>
                <div class="hidden lg:block w-32 h-2 bg-gradient-to-r from-secondary-500 to-primary-500 rounded-full"></div>
            </div>

            {{-- Right: Requirements List --}}
            <div class="lg:col-span-7">
                @php
                    $syarat = [
                        ['icon' => 'file-text', 'text' => 'Photo Copy Akta Kelahiran Peserta Didik'],
                        ['icon' => 'credit-card', 'text' => 'Photo Copy KTP orang tua/wali — sebanyak 3 lembar'],
                        ['icon' => 'users', 'text' => 'Photo Copy Kartu Keluarga (KK) — sebanyak 3 lembar'],
                        ['icon' => 'graduation-cap', 'text' => 'Photo Copy STL/SKHUN/Ijazah — sebanyak 3 lembar'],
                        ['icon' => 'activity', 'text' => 'Surat Keterangan Sehat dari Fasilitas Kesehatan'],
                    ];
                @endphp

                <div class="space-y-4">
                    @foreach($syarat as $index => $item)
                        <div class="flex items-center gap-5 bg-surface-50/50 dark:bg-surface-800/50 rounded-2xl p-5 border border-surface-200 dark:border-surface-700/50 hover:bg-white dark:hover:bg-surface-800 hover:shadow-xl hover:shadow-secondary-500/5 hover:-translate-x-2 transition-all duration-300 group">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-secondary-50 to-secondary-100 dark:from-secondary-900/40 dark:to-secondary-800/20 text-secondary-600 dark:text-secondary-400 flex items-center justify-center flex-shrink-0 group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300 shadow-inner">
                                <i data-lucide="{{ $item['icon'] }}" class="w-6 h-6"></i>
                            </div>
                            <span class="text-base font-bold text-surface-800 dark:text-surface-200">{{ __($item['text']) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══ ALUR PENYERAHAN SANTRI ═══ --}}
<section class="py-24 sm:py-32 bg-surface-50 dark:bg-surface-950 relative overflow-hidden">
    <div class="absolute right-0 bottom-0 w-[600px] h-[600px] bg-accent-500/5 rounded-full blur-[100px] pointer-events-none translate-y-1/3"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        {{-- Section Header --}}
        <div class="text-center mb-20 max-w-3xl mx-auto">
            <span class="inline-flex items-center gap-2 py-1.5 px-4 rounded-full bg-accent-50 dark:bg-accent-900/30 text-accent-600 dark:text-accent-400 font-bold text-xs uppercase tracking-widest mb-6 border border-accent-100 dark:border-accent-800/50 shadow-sm">
                <i data-lucide="luggage" class="w-3.5 h-3.5"></i> {{ __('Penyerahan Santri') }}
            </span>
            <h2 class="text-4xl md:text-5xl font-black text-surface-900 dark:text-white mb-6 tracking-tight">
                {{ __('Alur Kedatangan Santri') }}
            </h2>
            <p class="text-lg text-surface-500 dark:text-surface-400 font-medium">
                {{ __('Tahapan yang harus dilalui santri baru saat pertama kali tiba di pondok pesantren.') }}
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 lg:gap-12 relative">
            {{-- Connecting Line --}}
            <div class="hidden md:block absolute top-[52px] left-1/6 right-1/6 h-[2px] bg-gradient-to-r from-surface-200 via-accent-300 to-surface-200 dark:from-surface-800 dark:via-accent-700 dark:to-surface-800 z-0 border-t border-dashed border-surface-300 dark:border-surface-600"></div>

            {{-- Step 1 --}}
            <div class="relative bg-white dark:bg-surface-900 rounded-3xl border border-surface-200/80 dark:border-surface-800 p-10 shadow-xl hover:shadow-2xl hover:shadow-primary-500/10 hover:-translate-y-2 transition-all duration-500 text-center z-10">
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-primary-500 to-primary-600 text-white flex items-center justify-center font-black text-3xl mx-auto mb-8 shadow-lg shadow-primary-500/30 rotate-3 hover:rotate-0 transition-transform">1</div>
                <h3 class="text-xl font-black text-surface-900 dark:text-white mb-4">{{ __('Konfirmasi Kedatangan') }}</h3>
                <p class="text-base text-surface-500 dark:text-surface-400 leading-relaxed font-medium">{{ __('Menyerahkan bukti pendaftaran online dan verifikasi berkas fisik kepada panitia penerimaan di kantor pesantren.') }}</p>
            </div>

            {{-- Step 2 --}}
            <div class="relative bg-white dark:bg-surface-900 rounded-3xl border border-surface-200/80 dark:border-surface-800 p-10 shadow-xl hover:shadow-2xl hover:shadow-secondary-500/10 hover:-translate-y-2 transition-all duration-500 text-center z-10 md:mt-12">
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-secondary-500 to-secondary-600 text-white flex items-center justify-center font-black text-3xl mx-auto mb-8 shadow-lg shadow-secondary-500/30 -rotate-3 hover:rotate-0 transition-transform">2</div>
                <h3 class="text-xl font-black text-surface-900 dark:text-white mb-4">{{ __('Ikrar Santri & Wali') }}</h3>
                <p class="text-base text-surface-500 dark:text-surface-400 leading-relaxed font-medium">{{ __('Pembacaan ikrar kesediaan mematuhi tata tertib pesantren dan serah terima santri dari wali kepada pengasuh.') }}</p>
            </div>

            {{-- Step 3 --}}
            <div class="relative bg-white dark:bg-surface-900 rounded-3xl border border-surface-200/80 dark:border-surface-800 p-10 shadow-xl hover:shadow-2xl hover:shadow-accent-500/10 hover:-translate-y-2 transition-all duration-500 text-center z-10">
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-accent-500 to-accent-600 text-white flex items-center justify-center font-black text-3xl mx-auto mb-8 shadow-lg shadow-accent-500/30 rotate-3 hover:rotate-0 transition-transform">3</div>
                <h3 class="text-xl font-black text-surface-900 dark:text-white mb-4">{{ __('Masuk Asrama') }}</h3>
                <p class="text-base text-surface-500 dark:text-surface-400 leading-relaxed font-medium">{{ __('Santri diantar menuju asrama/kamar yang telah ditentukan untuk memulai kehidupan dan kegiatan di pesantren.') }}</p>
            </div>
        </div>
    </div>
</section>

{{-- ═══ INFORMASI PELAYANAN PENDAFTARAN ═══ --}}
<section class="py-24 sm:py-32 bg-white dark:bg-surface-900 border-t border-surface-200/50 dark:border-surface-800/50 relative overflow-hidden">
    <div class="absolute left-1/2 top-0 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-primary-500/5 to-transparent blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        {{-- Section Header --}}
        <div class="text-center mb-16 sm:mb-20 max-w-3xl mx-auto">
            <span class="inline-flex items-center gap-2 py-1.5 px-4 rounded-full bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 font-bold text-xs uppercase tracking-widest mb-6 border border-primary-100 dark:border-primary-800/50 shadow-sm">
                <i data-lucide="headset" class="w-3.5 h-3.5"></i> {{ __('Pusat Bantuan') }}
            </span>
            <h2 class="text-4xl md:text-5xl font-black text-surface-900 dark:text-white mb-6 tracking-tight">
                {{ __('Informasi Pelayanan') }}
            </h2>
            <p class="text-lg text-surface-500 dark:text-surface-400 font-medium">
                {{ __('Hubungi panitia penerimaan santri baru untuk informasi lebih lanjut mengenai pendaftaran.') }}
            </p>
        </div>

        {{-- 4 Info Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">

            {{-- Card 1 --}}
            <div class="bg-surface-50/80 dark:bg-surface-800/50 rounded-3xl border border-surface-200/80 dark:border-surface-700/80 p-8 shadow-lg hover:shadow-2xl hover:shadow-primary-500/10 hover:-translate-y-1 transition-all duration-300 group">
                <div class="flex flex-col sm:flex-row items-start gap-6">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/50 dark:to-primary-800/30 text-primary-600 dark:text-primary-400 flex items-center justify-center flex-shrink-0 shadow-inner group-hover:scale-110 group-hover:rotate-6 transition-transform">
                        <i data-lucide="calendar-days" class="w-8 h-8"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-surface-900 dark:text-white mb-4">
                            {{ __('Status Pendaftaran') }}
                        </h3>
                        <div class="space-y-3 text-base text-surface-600 dark:text-surface-400 font-medium">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)] animate-pulse"></div>
                                <span class="text-surface-900 dark:text-white font-bold">{{ __('Sedang Dibuka') }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <i data-lucide="building" class="w-4 h-4 text-surface-400"></i>
                                <span>{{ __('Kantor Pendaftaran Terpadu') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2 --}}
            <div class="bg-surface-50/80 dark:bg-surface-800/50 rounded-3xl border border-surface-200/80 dark:border-surface-700/80 p-8 shadow-lg hover:shadow-2xl hover:shadow-secondary-500/10 hover:-translate-y-1 transition-all duration-300 group">
                <div class="flex flex-col sm:flex-row items-start gap-6">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-secondary-50 to-secondary-100 dark:from-secondary-900/50 dark:to-secondary-800/30 text-secondary-600 dark:text-secondary-400 flex items-center justify-center flex-shrink-0 shadow-inner group-hover:scale-110 group-hover:-rotate-6 transition-transform">
                        <i data-lucide="file-check-2" class="w-8 h-8"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-surface-900 dark:text-white mb-4">
                            {{ __('Verifikasi Berkas') }}
                        </h3>
                        <div class="space-y-3 text-base text-surface-600 dark:text-surface-400 font-medium">
                            <div class="flex items-center gap-3">
                                <i data-lucide="calendar" class="w-4 h-4 text-surface-400"></i>
                                <span>{{ __('Mengikuti Jadwal Panitia') }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <i data-lucide="building" class="w-4 h-4 text-surface-400"></i>
                                <span>{{ __('Sekretariat') }} {{ $pesantren->nama ?? 'Nurul Furqon' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 3 --}}
            <div class="bg-surface-50/80 dark:bg-surface-800/50 rounded-3xl border border-surface-200/80 dark:border-surface-700/80 p-8 shadow-lg hover:shadow-2xl hover:shadow-accent-500/10 hover:-translate-y-1 transition-all duration-300 group">
                <div class="flex flex-col sm:flex-row items-start gap-6">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-accent-50 to-accent-100 dark:from-accent-900/50 dark:to-accent-800/30 text-accent-600 dark:text-accent-400 flex items-center justify-center flex-shrink-0 shadow-inner group-hover:scale-110 group-hover:rotate-6 transition-transform">
                        <i data-lucide="clock" class="w-8 h-8"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-surface-900 dark:text-white mb-4">
                            {{ __('Waktu Pelayanan') }}
                        </h3>
                        <div class="space-y-3 text-base text-surface-600 dark:text-surface-400 font-medium">
                            <div class="flex items-center gap-3">
                                <i data-lucide="sun" class="w-4 h-4 text-accent-500"></i>
                                <span><strong class="text-surface-900 dark:text-white">{{ __('Pagi') }}:</strong> 08.00 - 12.00 WIB</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <i data-lucide="sunset" class="w-4 h-4 text-accent-500"></i>
                                <span><strong class="text-surface-900 dark:text-white">{{ __('Siang') }}:</strong> 13.00 - 16.00 WIB</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 4 --}}
            <div class="bg-surface-50/80 dark:bg-surface-800/50 rounded-3xl border border-surface-200/80 dark:border-surface-700/80 p-8 shadow-lg hover:shadow-2xl hover:shadow-primary-500/10 hover:-translate-y-1 transition-all duration-300 group">
                <div class="flex flex-col sm:flex-row items-start gap-6">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/50 dark:to-primary-800/30 text-primary-600 dark:text-primary-400 flex items-center justify-center flex-shrink-0 shadow-inner group-hover:scale-110 group-hover:-rotate-6 transition-transform">
                        <i data-lucide="map-pin" class="w-8 h-8"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-surface-900 dark:text-white mb-4">
                            {{ __('Pusat Informasi') }}
                        </h3>
                        <div class="space-y-3 text-base text-surface-600 dark:text-surface-400 font-medium">
                            <div class="flex items-start gap-3">
                                <i data-lucide="navigation" class="w-4 h-4 text-primary-500 mt-1 shrink-0"></i>
                                <span class="leading-relaxed">{{ $pesantren->alamat ?? 'Jl. KH. Zaini Mun\'im, Desa Timu, Kecamatan Tomia Timur, Wakatobi - Sulawesi Tenggara' }}</span>
                            </div>
                            @if($pesantren->telepon ?? false)
                            <div class="flex items-center gap-3">
                                <i data-lucide="phone" class="w-4 h-4 text-primary-500"></i>
                                <span class="text-surface-900 dark:text-white font-bold">{{ $pesantren->telepon }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- CTA Bottom --}}
        <div class="mt-16 text-center">
            @if($isPsbBuka ?? true)
                <a href="{{ route('frontend.psb.daftar') }}" class="inline-flex items-center gap-3 px-8 py-4 bg-surface-900 dark:bg-white text-white dark:text-surface-900 font-black rounded-2xl shadow-xl hover:shadow-2xl hover:scale-105 active:scale-95 transition-all duration-300 uppercase tracking-widest text-sm">
                    {{ __('Mulai Pendaftaran') }}
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            @endif
        </div>
    </div>
</section>
@endsection
