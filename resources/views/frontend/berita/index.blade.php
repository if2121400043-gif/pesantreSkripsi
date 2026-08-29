@extends('frontend.layouts.app')

@section('title', __('Publikasi'))

@section('content')
{{-- ═══ HEADER SECTION ═══ --}}
<section class="bg-white dark:bg-surface-900 border-b border-surface-200 dark:border-surface-800 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
        {{-- Title --}}
        <h1 class="text-2xl sm:text-3xl font-extrabold text-surface-900 dark:text-white text-center mb-6">{{ __('PUBLIKASI') }}</h1>

        {{-- Search Bar --}}
        <form action="{{ route('frontend.publikasi') }}" method="GET" class="max-w-3xl mx-auto relative">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Cari berita atau pengumuman.....') }}" 
                   class="w-full pl-5 pr-12 py-3.5 rounded-2xl border border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800 text-sm text-surface-900 dark:text-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200">
            <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-surface-400 hover:text-primary-600 transition-colors">
                <i data-lucide="search" class="w-5 h-5"></i>
            </button>
        </form>

        {{-- Active search info --}}
        @if(request('q'))
        <div class="mt-4 flex items-center justify-center gap-2 text-sm text-surface-500 dark:text-surface-400">
            <span>{{ __('Hasil pencarian untuk') }}:</span>
            <span class="px-2.5 py-1 bg-surface-100 dark:bg-surface-800 text-surface-700 dark:text-surface-300 rounded-lg text-xs font-bold">
                "{{ request('q') }}"
            </span>
            <a href="{{ route('frontend.publikasi') }}" class="text-primary-600 dark:text-primary-400 font-bold text-xs hover:underline ml-1">{{ __('Reset') }}</a>
        </div>
        @endif
    </div>
</section>

{{-- ═══ TWO COLUMN LAYOUT: BERITA & PENGUMUMAN ═══ --}}
<section class="py-10 sm:py-14 bg-surface-50 dark:bg-surface-950 min-h-[50vh] transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">

            {{-- ═══ KOLOM BERITA ═══ --}}
            <div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-surface-900 dark:text-white mb-6 text-center">{{ __('Berita') }}</h2>
                <div class="space-y-6">
                    @forelse($beritas as $berita)
                    <article class="bg-white dark:bg-surface-900 rounded-2xl overflow-hidden border border-surface-100 dark:border-surface-800 hover:shadow-[0_15px_35px_-10px_rgba(0,0,0,0.08)] dark:hover:shadow-[0_10px_30px_-10px_rgba(0,0,0,0.5)] hover:-translate-y-0.5 transition-all duration-300 group flex flex-col sm:flex-row">
                        {{-- Text Content --}}
                        <div class="p-5 sm:p-6 flex flex-col flex-grow sm:w-1/2 order-2 sm:order-1">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="px-2.5 py-1 bg-info-50 dark:bg-info-900/30 text-info-700 dark:text-info-400 text-[11px] font-bold rounded-lg border border-info-200 dark:border-info-800">{{ __('Berita') }}</span>
                                <span class="text-xs text-surface-400 dark:text-surface-500 font-medium">{{ $berita->tanggal_format }}</span>
                            </div>
                            <h3 class="text-base font-bold text-surface-900 dark:text-white mb-3 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors line-clamp-2 leading-snug">
                                <a href="{{ route('frontend.berita.show', $berita->slug) }}">{{ $berita->judul }}</a>
                            </h3>
                            <div class="mt-auto flex items-center justify-between">
                                <a href="{{ route('frontend.berita.show', $berita->slug) }}" class="text-primary-600 dark:text-primary-400 font-bold text-sm flex items-center gap-1.5 group/l">
                                    {{ __('Baca Selengkapnya') }} <i data-lucide="arrow-right" class="w-4 h-4 group-hover/l:translate-x-1 transition-transform"></i>
                                </a>
                                <span class="flex items-center gap-1 text-xs text-surface-400 dark:text-surface-500 font-medium">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    {{ number_format($berita->view_count) }} {{ __('Lihat') }}
                                </span>
                            </div>
                        </div>
                        {{-- Image --}}
                        <div class="sm:w-1/2 aspect-[4/3] sm:aspect-auto overflow-hidden bg-surface-100 dark:bg-surface-800 relative order-1 sm:order-2">
                            @if($berita->gambar_cover)
                                <img src="{{ Storage::url($berita->gambar_cover) }}" alt="{{ $berita->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                            @else
                                <div class="w-full h-full min-h-[160px] flex flex-col items-center justify-center text-surface-300 dark:text-surface-600 bg-gradient-to-br from-surface-50 to-surface-100 dark:from-surface-800 dark:to-surface-900 gap-2">
                                    <i data-lucide="image" class="w-10 h-10"></i>
                                    <span class="text-xs font-medium text-surface-400 dark:text-surface-500">{{ __('[ FOTO / ILUSTRASI ]') }}</span>
                                </div>
                            @endif
                        </div>
                    </article>
                    @empty
                    <div class="text-center py-16 bg-white dark:bg-surface-900 rounded-2xl border border-dashed border-surface-300 dark:border-surface-700">
                        <div class="w-16 h-16 bg-surface-50 dark:bg-surface-800 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i data-lucide="newspaper" class="w-8 h-8 text-surface-300 dark:text-surface-500"></i>
                        </div>
                        <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-1">{{ __('Belum Ada Berita') }}</h3>
                        <p class="text-surface-500 dark:text-surface-400 text-sm">{{ __('Saat ini belum ada berita yang dipublikasikan.') }}</p>
                    </div>
                    @endforelse
                </div>

                {{-- Pagination Berita --}}
                @if($beritas->hasPages())
                <div class="mt-8 flex justify-center">
                    {{ $beritas->links() }}
                </div>
                @endif
            </div>

            {{-- ═══ KOLOM PENGUMUMAN ═══ --}}
            <div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-surface-900 dark:text-white mb-6 text-center">{{ __('Pengumuman') }}</h2>
                <div class="space-y-6">
                    @forelse($pengumumans as $pengumuman)
                    <article class="bg-white dark:bg-surface-900 rounded-2xl overflow-hidden border border-surface-100 dark:border-surface-800 hover:shadow-[0_15px_35px_-10px_rgba(0,0,0,0.08)] dark:hover:shadow-[0_10px_30px_-10px_rgba(0,0,0,0.5)] hover:-translate-y-0.5 transition-all duration-300 group flex flex-col sm:flex-row">
                        {{-- Text Content --}}
                        <div class="p-5 sm:p-6 flex flex-col flex-grow sm:w-1/2 order-2 sm:order-1">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="px-2.5 py-1 bg-warning-50 dark:bg-warning-900/30 text-warning-700 dark:text-warning-400 text-[11px] font-bold rounded-lg border border-warning-200 dark:border-warning-800">{{ __('Pengumuman') }}</span>
                                <span class="text-xs text-surface-400 dark:text-surface-500 font-medium">{{ $pengumuman->tanggal_format }}</span>
                            </div>
                            <h3 class="text-base font-bold text-surface-900 dark:text-white mb-3 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors line-clamp-2 leading-snug">
                                <a href="{{ route('frontend.berita.show', $pengumuman->slug) }}">{{ $pengumuman->judul }}</a>
                            </h3>
                            <div class="mt-auto flex items-center justify-between">
                                <a href="{{ route('frontend.berita.show', $pengumuman->slug) }}" class="text-primary-600 dark:text-primary-400 font-bold text-sm flex items-center gap-1.5 group/l">
                                    {{ __('Baca Selengkapnya') }} <i data-lucide="arrow-right" class="w-4 h-4 group-hover/l:translate-x-1 transition-transform"></i>
                                </a>
                                <span class="flex items-center gap-1 text-xs text-surface-400 dark:text-surface-500 font-medium">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    {{ number_format($pengumuman->view_count) }} {{ __('Lihat') }}
                                </span>
                            </div>
                        </div>
                        {{-- Image --}}
                        <div class="sm:w-1/2 aspect-[4/3] sm:aspect-auto overflow-hidden bg-surface-100 dark:bg-surface-800 relative order-1 sm:order-2">
                            @if($pengumuman->gambar_cover)
                                <img src="{{ Storage::url($pengumuman->gambar_cover) }}" alt="{{ $pengumuman->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                            @else
                                <div class="w-full h-full min-h-[160px] flex flex-col items-center justify-center text-surface-300 dark:text-surface-600 bg-gradient-to-br from-surface-50 to-surface-100 dark:from-surface-800 dark:to-surface-900 gap-2">
                                    <i data-lucide="megaphone" class="w-10 h-10"></i>
                                    <span class="text-xs font-medium text-surface-400 dark:text-surface-500">{{ __('[ FOTO / ILUSTRASI ]') }}</span>
                                </div>
                            @endif
                        </div>
                    </article>
                    @empty
                    <div class="text-center py-16 bg-white dark:bg-surface-900 rounded-2xl border border-dashed border-surface-300 dark:border-surface-700">
                        <div class="w-16 h-16 bg-surface-50 dark:bg-surface-800 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i data-lucide="megaphone" class="w-8 h-8 text-surface-300 dark:text-surface-500"></i>
                        </div>
                        <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-1">{{ __('Belum Ada Pengumuman') }}</h3>
                        <p class="text-surface-500 dark:text-surface-400 text-sm">{{ __('Saat ini belum ada pengumuman yang dipublikasikan.') }}</p>
                    </div>
                    @endforelse
                </div>

                {{-- Pagination Pengumuman --}}
                @if($pengumumans->hasPages())
                <div class="mt-8 flex justify-center">
                    {{ $pengumumans->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>
</section>
@endsection
