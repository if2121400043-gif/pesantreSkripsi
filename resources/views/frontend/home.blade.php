@extends('frontend.layouts.app')

@section('content')
@php
    $heroSlides = [
        asset('images/hero-background.jpg'),
        asset('images/hero-background-2.jpg'),
        asset('images/hero-background-3.jpg'),
        asset('images/kegiatan-pesantren-800.webp'),
    ];
@endphp

{{-- ═══ HERO ═══ --}}
<section id="heroSlider" class="hero-slider relative overflow-hidden text-white transition-colors duration-300 min-h-[70vh] sm:min-h-[74vh] lg:min-h-[83vh]" aria-roledescription="carousel" aria-label="{{ __('Galeri pesantren') }}">
    <div class="hero-slider-track absolute inset-0 flex" data-hero-track>
        @foreach($heroSlides as $index => $slide)
            <div class="hero-slider-slide h-full w-full shrink-0" data-hero-slide>
                <img src="{{ $slide }}" alt="{{ __('Kegiatan pesantren') }} {{ $index + 1 }}" class="h-full w-full object-cover object-center" @if($index > 0) loading="lazy" @endif>
            </div>
        @endforeach
    </div>
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-950/95 via-primary-900/80 to-primary-900/45"></div>
    </div>
    {{-- Decorative Background --}}
    <div class="absolute inset-0 opacity-[0.05] dark:opacity-[0.02]" style="background-image:url('data:image/svg+xml,%3Csvg width=60 height=60 viewBox=%220 0 60 60%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22none%22 fill-rule=%22evenodd%22%3E%3Cg fill=%22%23ffffff%22 fill-opacity=%221%22%3E%3Cpath d=%22M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z%22/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>

    {{-- Glow effect --}}
    <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-secondary-500/20 rounded-full blur-[120px] pointer-events-none -translate-y-1/2 translate-x-1/3"></div>
    <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-primary-500/20 rounded-full blur-[100px] pointer-events-none translate-y-1/3 -translate-x-1/4"></div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 py-20 sm:py-24 md:py-28 lg:py-32 flex items-center min-h-[70vh] sm:min-h-[74vh] lg:min-h-[83vh]">
        <div class="max-w-3xl animate-fade-in">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 dark:bg-surface-800/50 border border-white/20 dark:border-surface-700 text-primary-200 dark:text-primary-400 text-xs font-bold tracking-widest uppercase mb-6 backdrop-blur-sm">
                @if($isPsbBuka)
                    <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-accent-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-accent-500"></span></span>
                    {{ __('Pendaftaran Santri Baru Telah Dibuka') }}
                @else
                    <span class="relative flex h-2 w-2"><span class="relative inline-flex rounded-full h-2 w-2 bg-surface-500"></span></span>
                    {{ __('Pendaftaran Sudah Selesai') }}
                @endif
            </span>
            <h1 class="text-3xl sm:text-4xl lg:text-6xl font-extrabold leading-[1.1] tracking-tight mb-4 text-white">
                {{ __('Membangun Generasi') }}<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-accent-400 via-secondary-300 to-accent-200">{{ __("Qur'ani & Mandiri") }}</span>
            </h1>
            <p class="text-base sm:text-lg text-primary-100/90 dark:text-surface-300 max-w-xl mb-8 leading-relaxed font-medium">
                {{ $pesantren?->nama ?? 'Pesantren Nurul Furqon' }} {{ __('adalah lembaga pendidikan Islam terpadu yang menyeimbangkan ilmu agama, akademik, dan pembentukan akhlak santri.') }}
            </p>
        </div>
    </div>
    <div class="absolute inset-x-0 bottom-20 sm:bottom-24 z-20 flex items-center justify-center gap-4" data-hero-controls>
        <button type="button" class="hero-slider-arrow" data-hero-prev aria-label="{{ __('Gambar sebelumnya') }}">
            <x-icon name="arrow-left" size="w-5 h-5" />
        </button>
        <div class="hero-slider-dots flex items-center gap-2.5 px-3 py-2.5" role="tablist" aria-label="{{ __('Navigasi gambar') }}">
            @foreach($heroSlides as $index => $slide)
                <button type="button" class="hero-slider-dot {{ $index === 0 ? 'is-active' : '' }}" data-hero-dot="{{ $index }}" role="tab" aria-label="{{ __('Tampilkan gambar') }} {{ $index + 1 }}" aria-selected="{{ $index === 0 ? 'true' : 'false' }}"></button>
            @endforeach
        </div>
        <button type="button" class="hero-slider-arrow" data-hero-next aria-label="{{ __('Gambar berikutnya') }}">
            <x-icon name="arrow-right" size="w-5 h-5" />
        </button>
    </div>
    {{-- Wave divider --}}
    <div class="absolute bottom-0 left-0 right-0">
        <svg class="w-full h-10 sm:h-16 lg:h-20 text-surface-50 dark:text-surface-950 fill-current" viewBox="0 0 1440 120" preserveAspectRatio="none">
            <path d="M0,64L80,69.3C160,75,320,85,480,80C640,75,800,53,960,48C1120,43,1280,53,1360,58.7L1440,64L1440,120L1360,120C1280,120,1120,120,960,120C800,120,640,120,480,120C320,120,160,120,80,120L0,120Z"></path>
        </svg>
    </div>
</section>

@push('styles')
<style>
    .hero-slider-track {
        transition: transform 700ms cubic-bezier(.22, .61, .36, 1);
        will-change: transform;
    }
    .hero-slider-slide {
        min-width: 100%;
    }
    .hero-slider-arrow {
        display: grid;
        width: 44px;
        height: 44px;
        place-items: center;
        border: 1px solid rgba(255, 255, 255, .45);
        border-radius: 999px;
        background: rgba(15, 23, 42, .48);
        box-shadow: 0 8px 24px rgba(15, 23, 42, .25);
        color: #fff;
        line-height: 1;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        transition: background-color .2s ease, border-color .2s ease, transform .2s ease;
    }
    .hero-slider-arrow:hover {
        background: #b98122;
        border-color: #f5d98a;
        transform: scale(1.08);
    }
    .hero-slider-arrow:focus-visible,
    .hero-slider-dot:focus-visible {
        outline: 2px solid #f5d98a;
        outline-offset: 3px;
    }
    .hero-slider-dots {
        border: 1px solid rgba(255, 255, 255, .2);
        border-radius: 999px;
        background: rgba(15, 23, 42, .32);
        box-shadow: 0 8px 24px rgba(15, 23, 42, .18);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }
    .hero-slider-dot {
        width: 9px;
        height: 9px;
        padding: 0;
        border: 0;
        border-radius: 999px;
        background: rgba(255, 255, 255, .45);
        transition: width .25s ease, background-color .25s ease;
    }
    .hero-slider-dot.is-active {
        width: 26px;
        background: #f5d98a;
    }
    @media (prefers-reduced-motion: reduce) {
        .hero-slider-track,
        .hero-slider-arrow,
        .hero-slider-dot {
            transition: none;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const slider = document.getElementById('heroSlider');
        const track = slider?.querySelector('[data-hero-track]');
        const slides = slider?.querySelectorAll('[data-hero-slide]');
        const dots = slider?.querySelectorAll('[data-hero-dot]');
        const previous = slider?.querySelector('[data-hero-prev]');
        const next = slider?.querySelector('[data-hero-next]');

        if (!slider || !track || !slides?.length) return;

        let current = 0;
        let timer;
        let touchStartX = 0;

        const showSlide = (index) => {
            current = (index + slides.length) % slides.length;
            track.style.transform = `translateX(-${current * 100}%)`;
            dots?.forEach((dot, dotIndex) => {
                const active = dotIndex === current;
                dot.classList.toggle('is-active', active);
                dot.setAttribute('aria-selected', active ? 'true' : 'false');
            });
        };

        const restartTimer = () => {
            window.clearInterval(timer);
            timer = window.setInterval(() => showSlide(current + 1), 4000);
        };

        previous?.addEventListener('click', () => {
            showSlide(current - 1);
            restartTimer();
        });
        next?.addEventListener('click', () => {
            showSlide(current + 1);
            restartTimer();
        });
        dots?.forEach((dot) => {
            dot.addEventListener('click', () => {
                showSlide(Number(dot.dataset.heroDot));
                restartTimer();
            });
        });
        slider.addEventListener('mouseenter', () => window.clearInterval(timer));
        slider.addEventListener('mouseleave', restartTimer);
        slider.addEventListener('touchstart', (event) => {
            touchStartX = event.changedTouches[0].clientX;
        }, { passive: true });
        slider.addEventListener('touchend', (event) => {
            const distance = event.changedTouches[0].clientX - touchStartX;
            if (Math.abs(distance) > 50) showSlide(current + (distance < 0 ? 1 : -1));
            restartTimer();
        }, { passive: true });

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) window.clearInterval(timer);
            else restartTimer();
        });

        restartTimer();
    });
</script>
@endpush

{{-- ═══ PROGRAM UNGGULAN ═══ --}}
<section class="py-20 sm:py-28 relative bg-surface-50 dark:bg-surface-950 overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-primary-500/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-secondary-500/10 rounded-full blur-3xl translate-y-1/3 -translate-x-1/4 pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-2xl mx-auto mb-16 sm:mb-20">
            <span class="inline-flex items-center gap-2 py-1.5 px-4 rounded-full bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 font-bold text-xs uppercase tracking-widest mb-6 border border-primary-100 dark:border-primary-800/50 shadow-sm">
                <i data-lucide="star" class="w-3.5 h-3.5"></i> {{ __('Keunggulan Kami') }}
            </span>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-surface-900 dark:text-white mb-6 tracking-tight">{{ __('Program Unggulan') }}</h2>
            <p class="text-surface-500 dark:text-surface-400 text-lg leading-relaxed font-medium">Membangun generasi unggul melalui perpaduan pendidikan Islam yang komprehensif dan modern.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
            {{-- Tahfidzul Qur'an --}}
            <div class="group relative bg-white dark:bg-surface-900 rounded-3xl border border-surface-100 dark:border-surface-800 p-8 sm:p-10 text-center hover:shadow-2xl hover:shadow-primary-500/10 hover:-translate-y-2 transition-all duration-500 overflow-hidden z-10">
                <div class="absolute inset-0 bg-gradient-to-br from-primary-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 -z-10"></div>
                <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/40 dark:to-primary-800/20 text-primary-600 dark:text-primary-400 flex items-center justify-center mx-auto mb-8 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500 shadow-inner">
                    <i data-lucide="book-open-check" class="w-12 h-12"></i>
                </div>
                <h3 class="text-xl font-black text-surface-900 dark:text-white mb-4 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">{{ __("Tahfidzul Qur'an") }}</h3>
                <p class="text-surface-500 dark:text-surface-400 leading-relaxed font-medium">{{ __("Program hafalan Al-Qur'an bersanad dengan metode yang terbukti efektif dan menyenangkan.") }}</p>
            </div>

            {{-- Tahsinul Qur'an --}}
            <div class="group relative bg-white dark:bg-surface-900 rounded-3xl border border-surface-100 dark:border-surface-800 p-8 sm:p-10 text-center hover:shadow-2xl hover:shadow-secondary-500/10 hover:-translate-y-2 transition-all duration-500 overflow-hidden z-10 md:-translate-y-4">
                <div class="absolute inset-0 bg-gradient-to-br from-secondary-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 -z-10"></div>
                <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-secondary-50 to-secondary-100 dark:from-secondary-900/40 dark:to-secondary-800/20 text-secondary-600 dark:text-secondary-400 flex items-center justify-center mx-auto mb-8 group-hover:scale-110 group-hover:-rotate-3 transition-transform duration-500 shadow-inner">
                    <i data-lucide="shield-check" class="w-12 h-12"></i>
                </div>
                <h3 class="text-xl font-black text-surface-900 dark:text-white mb-4 group-hover:text-secondary-600 dark:group-hover:text-secondary-400 transition-colors">{{ __("Tahsinul Qur'an") }}</h3>
                <p class="text-surface-500 dark:text-surface-400 leading-relaxed font-medium">{{ __("Membina bacaan Al-Qur'an sesuai kaidah tajwid dan makharijul huruf dengan bimbingan ustadz bersanad.") }}</p>
            </div>

            {{-- Bi'ah Lughawiyah --}}
            <div class="group relative bg-white dark:bg-surface-900 rounded-3xl border border-surface-100 dark:border-surface-800 p-8 sm:p-10 text-center hover:shadow-2xl hover:shadow-success-500/10 hover:-translate-y-2 transition-all duration-500 overflow-hidden z-10">
                <div class="absolute inset-0 bg-gradient-to-br from-success-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 -z-10"></div>
                <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-success-50 to-success-100 dark:from-success-900/40 dark:to-success-800/20 text-success-600 dark:text-success-400 flex items-center justify-center mx-auto mb-8 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500 shadow-inner">
                    <i data-lucide="languages" class="w-12 h-12"></i>
                </div>
                <h3 class="text-xl font-black text-surface-900 dark:text-white mb-4 group-hover:text-success-600 dark:group-hover:text-success-400 transition-colors">{{ __("Bi'ah Lughawiyah") }}</h3>
                <p class="text-surface-500 dark:text-surface-400 leading-relaxed font-medium">{{ __("Lingkungan berbahasa Arab dan Inggris aktif untuk membentuk santri yang mampu berkomunikasi global.") }}</p>
            </div>
        </div>
    </div>
</section>

{{-- ═══ UNIT PENDIDIKAN ═══ --}}
<section class="py-20 sm:py-28 bg-surface-100/50 dark:bg-surface-900/30 relative border-y border-surface-200/50 dark:border-surface-800/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 sm:mb-20">
            <span class="inline-flex items-center gap-2 py-1.5 px-4 rounded-full bg-secondary-50 dark:bg-secondary-900/30 text-secondary-600 dark:text-secondary-400 font-bold text-xs uppercase tracking-widest mb-6 border border-secondary-100 dark:border-secondary-800/50 shadow-sm">
                <i data-lucide="graduation-cap" class="w-3.5 h-3.5"></i> {{ __('Pendidikan Formal') }}
            </span>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-surface-900 dark:text-white">{{ __('Unit Pendidikan') }}</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">
            {{-- Madrasah Aliyah --}}
            <div class="group relative bg-white dark:bg-surface-900 rounded-3xl border border-surface-200/80 dark:border-surface-800 p-8 sm:p-12 hover:shadow-2xl hover:border-secondary-300 dark:hover:border-secondary-700 transition-all duration-500 text-left overflow-hidden z-10 flex flex-col md:flex-row items-center gap-8">
                <div class="absolute -right-12 -top-12 w-48 h-48 bg-secondary-50 dark:bg-secondary-900/20 rounded-full blur-2xl group-hover:bg-secondary-100 dark:group-hover:bg-secondary-900/40 transition-colors duration-700 -z-10"></div>
                
                <div class="w-24 h-24 shrink-0 rounded-3xl bg-gradient-to-br from-secondary-500 to-secondary-600 text-white flex items-center justify-center shadow-lg shadow-secondary-500/30 group-hover:scale-110 group-hover:-rotate-3 transition-transform duration-500 relative z-10">
                    <i data-lucide="school" class="w-10 h-10"></i>
                </div>
                
                <div class="relative z-10">
                    <h3 class="text-2xl font-black text-surface-900 dark:text-white mb-3 group-hover:text-secondary-600 dark:group-hover:text-secondary-400 transition-colors">{{ __('Madrasah Aliyah') }}</h3>
                    <p class="text-surface-500 dark:text-surface-400 text-base leading-relaxed font-medium">{{ __('Jenjang pendidikan menengah atas setingkat SMA yang memadukan ilmu agama dan ilmu umum secara komprehensif.') }}</p>
                </div>
            </div>

            {{-- Madrasah Tsanawiyah --}}
            <div class="group relative bg-white dark:bg-surface-900 rounded-3xl border border-surface-200/80 dark:border-surface-800 p-8 sm:p-12 hover:shadow-2xl hover:border-primary-300 dark:hover:border-primary-700 transition-all duration-500 text-left overflow-hidden z-10 flex flex-col md:flex-row items-center gap-8">
                <div class="absolute -right-12 -bottom-12 w-48 h-48 bg-primary-50 dark:bg-primary-900/20 rounded-full blur-2xl group-hover:bg-primary-100 dark:group-hover:bg-primary-900/40 transition-colors duration-700 -z-10"></div>
                
                <div class="w-24 h-24 shrink-0 rounded-3xl bg-gradient-to-br from-primary-500 to-primary-600 text-white flex items-center justify-center shadow-lg shadow-primary-500/30 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500 relative z-10">
                    <i data-lucide="book-open" class="w-10 h-10"></i>
                </div>
                
                <div class="relative z-10">
                    <h3 class="text-2xl font-black text-surface-900 dark:text-white mb-3 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">{{ __('Madrasah Tsanawiyah') }}</h3>
                    <p class="text-surface-500 dark:text-surface-400 text-base leading-relaxed font-medium">{{ __('Jenjang pendidikan menengah pertama setingkat SMP dengan kurikulum terpadu antara pendidikan agama dan umum.') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══ KEGIATAN HARIAN ═══ --}}
<section class="py-20 sm:py-28 relative bg-surface-50 dark:bg-surface-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 sm:mb-20">
            <span class="inline-flex items-center gap-2 py-1.5 px-4 rounded-full bg-accent-50 dark:bg-accent-900/30 text-accent-600 dark:text-accent-400 font-bold text-xs uppercase tracking-widest mb-6 border border-accent-100 dark:border-accent-800/50 shadow-sm">
                <i data-lucide="activity" class="w-3.5 h-3.5"></i> {{ __('Aktivitas') }}
            </span>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-surface-900 dark:text-white tracking-tight">{{ __('Kegiatan Harian') }}</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            {{-- Kegiatan 1: Sholat Berjamaah --}}
            <div class="group relative bg-white dark:bg-surface-900 rounded-3xl border border-surface-100 dark:border-surface-800 overflow-hidden shadow-md hover:shadow-2xl hover:shadow-primary-500/10 transition-all duration-500 hover:-translate-y-2">
                <div class="aspect-[16/10] overflow-hidden bg-surface-200 dark:bg-surface-800 relative">
                    <img src="{{ asset('images/kegiatan-pesantren-800.webp') }}" alt="{{ __('Sholat Berjamaah') }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" loading="lazy">
                    <div class="absolute inset-0 bg-gradient-to-t from-surface-900/80 via-transparent to-transparent opacity-60 group-hover:opacity-40 transition-opacity"></div>
                </div>
                <div class="p-8">
                    <h3 class="text-xl font-black text-surface-900 dark:text-white mb-3">{{ __('Sholat Berjamaah') }}</h3>
                    <p class="text-base text-surface-500 dark:text-surface-400 leading-relaxed font-medium">{{ __('Seluruh santri menunaikan sholat lima waktu berjamaah di masjid pesantren sebagai fondasi kedisiplinan dan ketakwaan.') }}</p>
                </div>
            </div>

            {{-- Kegiatan 2: Halaqoh Al-Qur'an --}}
            <div class="group relative bg-white dark:bg-surface-900 rounded-3xl border border-surface-100 dark:border-surface-800 overflow-hidden shadow-md hover:shadow-2xl hover:shadow-secondary-500/10 transition-all duration-500 hover:-translate-y-2">
                <div class="aspect-[16/10] overflow-hidden bg-surface-200 dark:bg-surface-800 relative">
                    <img src="{{ asset('images/kegiatan-pesantren-800.webp') }}" alt="{{ __('Halaqoh Al-Qur\'an') }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" loading="lazy">
                    <div class="absolute inset-0 bg-gradient-to-t from-surface-900/80 via-transparent to-transparent opacity-60 group-hover:opacity-40 transition-opacity"></div>
                </div>
                <div class="p-8">
                    <h3 class="text-xl font-black text-surface-900 dark:text-white mb-3">{{ __("Halaqoh Al-Qur'an") }}</h3>
                    <p class="text-base text-surface-500 dark:text-surface-400 leading-relaxed font-medium">{{ __("Lingkaran belajar Al-Qur'an untuk menghafal, murojaah, dan memperbaiki bacaan bersama ustadz pembimbing.") }}</p>
                </div>
            </div>

            {{-- Kegiatan 3: Kegiatan Belajar Mengajar --}}
            <div class="group relative bg-white dark:bg-surface-900 rounded-3xl border border-surface-100 dark:border-surface-800 overflow-hidden shadow-md hover:shadow-2xl hover:shadow-accent-500/10 transition-all duration-500 hover:-translate-y-2">
                <div class="aspect-[16/10] overflow-hidden bg-surface-200 dark:bg-surface-800 relative">
                    <img src="{{ asset('images/kegiatan-pesantren-800.webp') }}" alt="{{ __('Kegiatan Belajar Mengajar') }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" loading="lazy">
                    <div class="absolute inset-0 bg-gradient-to-t from-surface-900/80 via-transparent to-transparent opacity-60 group-hover:opacity-40 transition-opacity"></div>
                </div>
                <div class="p-8">
                    <h3 class="text-xl font-black text-surface-900 dark:text-white mb-3">{{ __('Kegiatan Belajar Mengajar') }}</h3>
                    <p class="text-base text-surface-500 dark:text-surface-400 leading-relaxed font-medium">{{ __('Proses pembelajaran formal dengan kurikulum terpadu yang memadukan pendidikan agama dan umum.') }}</p>
                </div>
            </div>

            {{-- Kegiatan 4: Muhadatsah --}}
            <div class="group relative bg-white dark:bg-surface-900 rounded-3xl border border-surface-100 dark:border-surface-800 overflow-hidden shadow-md hover:shadow-2xl hover:shadow-primary-500/10 transition-all duration-500 hover:-translate-y-2">
                <div class="aspect-[16/10] overflow-hidden bg-surface-200 dark:bg-surface-800 relative">
                    <img src="{{ asset('images/kegiatan-pesantren-800.webp') }}" alt="{{ __('Muhadatsah') }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" loading="lazy">
                    <div class="absolute inset-0 bg-gradient-to-t from-surface-900/80 via-transparent to-transparent opacity-60 group-hover:opacity-40 transition-opacity"></div>
                </div>
                <div class="p-8">
                    <h3 class="text-xl font-black text-surface-900 dark:text-white mb-3">{{ __('Muhadatsah') }}</h3>
                    <p class="text-base text-surface-500 dark:text-surface-400 leading-relaxed font-medium">{{ __('Praktik percakapan harian menggunakan bahasa Arab dan Inggris untuk melatih kemampuan komunikasi santri.') }}</p>
                </div>
            </div>

            {{-- Kegiatan 5: Olahraga & Ekstrakurikuler --}}
            <div class="group relative bg-white dark:bg-surface-900 rounded-3xl border border-surface-100 dark:border-surface-800 overflow-hidden shadow-md hover:shadow-2xl hover:shadow-secondary-500/10 transition-all duration-500 hover:-translate-y-2">
                <div class="aspect-[16/10] overflow-hidden bg-surface-200 dark:bg-surface-800 relative">
                    <img src="{{ asset('images/kegiatan-pesantren-800.webp') }}" alt="{{ __('Olahraga & Ekstrakurikuler') }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" loading="lazy">
                    <div class="absolute inset-0 bg-gradient-to-t from-surface-900/80 via-transparent to-transparent opacity-60 group-hover:opacity-40 transition-opacity"></div>
                </div>
                <div class="p-8">
                    <h3 class="text-xl font-black text-surface-900 dark:text-white mb-3">{{ __('Olahraga & Ekstrakurikuler') }}</h3>
                    <p class="text-base text-surface-500 dark:text-surface-400 leading-relaxed font-medium">{{ __('Kegiatan olahraga dan pengembangan bakat santri melalui berbagai program ekstrakurikuler yang tersedia.') }}</p>
                </div>
            </div>

            {{-- Kegiatan 6: Kajian Kitab Kuning --}}
            <div class="group relative bg-white dark:bg-surface-900 rounded-3xl border border-surface-100 dark:border-surface-800 overflow-hidden shadow-md hover:shadow-2xl hover:shadow-accent-500/10 transition-all duration-500 hover:-translate-y-2">
                <div class="aspect-[16/10] overflow-hidden bg-surface-200 dark:bg-surface-800 relative">
                    <img src="{{ asset('images/kegiatan-pesantren-800.webp') }}" alt="{{ __('Kajian Kitab Kuning') }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" loading="lazy">
                    <div class="absolute inset-0 bg-gradient-to-t from-surface-900/80 via-transparent to-transparent opacity-60 group-hover:opacity-40 transition-opacity"></div>
                </div>
                <div class="p-8">
                    <h3 class="text-xl font-black text-surface-900 dark:text-white mb-3">{{ __('Kajian Kitab Kuning') }}</h3>
                    <p class="text-base text-surface-500 dark:text-surface-400 leading-relaxed font-medium">{{ __('Pembelajaran kitab-kitab klasik Islam dengan metode sorogan dan bandongan sebagai tradisi pesantren.') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══ PUBLIKASI ═══ --}}
<section class="py-20 sm:py-28 bg-surface-100/50 dark:bg-surface-900/30 border-t border-surface-200/50 dark:border-surface-800/50 relative overflow-hidden">
    <!-- Glow effects -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-[600px] bg-gradient-to-r from-primary-500/5 via-secondary-500/5 to-accent-500/5 rounded-full blur-[100px] pointer-events-none -z-10"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16 sm:mb-20">
            <span class="inline-flex items-center gap-2 py-1.5 px-4 rounded-full bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 font-bold text-xs uppercase tracking-widest mb-6 border border-primary-100 dark:border-primary-800/50 shadow-sm">
                <i data-lucide="newspaper" class="w-3.5 h-3.5"></i> {{ __('Berita & Kegiatan') }}
            </span>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-surface-900 dark:text-white tracking-tight">{{ __('Publikasi Terbaru') }}</h2>
        </div>

        @php($beritaTampil = $berita_terbaru->take(2))
        @if($beritaTampil->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">
            @foreach($beritaTampil as $berita)
            <article class="bg-white dark:bg-surface-900 rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl border border-surface-100 dark:border-surface-800 transition-all duration-500 group flex flex-col hover:-translate-y-2">
                <div class="aspect-[16/9] overflow-hidden bg-surface-200 dark:bg-surface-800 relative">
                    @if($berita->gambar_cover)
                        <img src="{{ Storage::url($berita->gambar_cover) }}" alt="{{ $berita->judul }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" loading="lazy">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-surface-300 dark:text-surface-600 bg-surface-50 dark:bg-surface-800">
                            <i data-lucide="image" class="w-16 h-16 opacity-50"></i>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-surface-900/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    @if($berita->is_pinned)
                        <div class="absolute top-4 left-4 z-10">
                            <span class="px-3 py-1.5 bg-accent-500/90 backdrop-blur-md text-white text-xs font-black rounded-xl shadow-sm flex items-center gap-1.5 border border-white/10">
                                <i data-lucide="pin" class="w-3.5 h-3.5"></i> {{ __('Disematkan') }}
                            </span>
                        </div>
                    @endif
                </div>
                <div class="p-8 flex flex-col flex-grow relative z-10 bg-white dark:bg-surface-900">
                    <span class="text-[11px] text-surface-400 font-black tracking-widest uppercase mb-4 flex items-center gap-2">
                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i> {{ $berita->tanggal_format }}
                    </span>
                    <h3 class="text-xl sm:text-2xl font-black text-surface-900 dark:text-white mb-4 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors line-clamp-2 leading-tight">
                        <a href="{{ route('frontend.berita.show', $berita->slug) }}">{{ $berita->judul }}</a>
                    </h3>
                    <p class="text-surface-500 dark:text-surface-400 text-base line-clamp-3 mb-6 flex-grow leading-relaxed font-medium">{{ $berita->ringkasan ?? Str::limit(strip_tags($berita->konten), 120) }}</p>
                    
                    <a href="{{ route('frontend.berita.show', $berita->slug) }}" class="inline-flex items-center gap-2 text-primary-600 dark:text-primary-400 font-bold text-sm group/l uppercase tracking-wider">
                        {{ __('Baca Selengkapnya') }} 
                        <div class="w-8 h-8 rounded-full bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center group-hover/l:bg-primary-100 dark:group-hover/l:bg-primary-800/50 transition-colors">
                            <i data-lucide="arrow-right" class="w-4 h-4 group-hover/l:translate-x-1 transition-transform"></i>
                        </div>
                    </a>
                </div>
            </article>
            @endforeach
        </div>

        {{-- Link to all publications --}}
        <div class="text-center mt-16">
            <a href="{{ route('frontend.publikasi') }}" class="inline-flex items-center gap-3 px-8 py-4 bg-surface-900 dark:bg-white text-white dark:text-surface-900 font-black rounded-2xl shadow-xl hover:shadow-2xl hover:scale-105 active:scale-95 transition-all duration-300 group">
                <i data-lucide="newspaper" class="w-5 h-5"></i>
                {{ __('Lihat Semua Publikasi') }}
            </a>
        </div>
        @else
        <div class="text-center py-20 bg-white/50 dark:bg-surface-900/50 backdrop-blur-sm rounded-3xl border-2 border-dashed border-surface-300 dark:border-surface-700 max-w-3xl mx-auto">
            <div class="w-20 h-20 bg-surface-100 dark:bg-surface-800 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-inner text-surface-400 dark:text-surface-500">
                <i data-lucide="newspaper" class="w-10 h-10"></i>
            </div>
            <h3 class="text-xl font-black text-surface-900 dark:text-white mb-2">{{ __('Belum Ada Berita') }}</h3>
            <p class="text-surface-500 dark:text-surface-400 text-base font-medium">{{ __('Berita dan kegiatan akan segera ditambahkan oleh admin.') }}</p>
        </div>
        @endif
    </div>
</section>

@endsection
