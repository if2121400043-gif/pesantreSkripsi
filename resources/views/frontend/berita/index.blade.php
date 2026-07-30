@extends('frontend.layouts.app')

@section('title', __('Berita & Kegiatan'))

@section('content')
{{-- ═══ HERO HEADER ═══ --}}
<section class="relative overflow-hidden bg-gradient-to-br from-primary-900 via-primary-800 to-surface-900 dark:from-surface-950 dark:via-surface-900 dark:to-surface-950 text-white transition-colors duration-300">
    <div class="absolute inset-0 opacity-[0.05] dark:opacity-[0.02]" style="background-image:url('data:image/svg+xml,%3Csvg width=60 height=60 viewBox=%220 0 60 60%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22none%22 fill-rule=%22evenodd%22%3E%3Cg fill=%22%23ffffff%22 fill-opacity=%221%22%3E%3Cpath d=%22M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z%22/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>
    <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-primary-500/15 rounded-full blur-[120px] pointer-events-none translate-y-1/3 -translate-x-1/4"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 py-20 md:py-28 text-center">
        <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 dark:bg-surface-800/50 border border-white/20 dark:border-surface-700 text-primary-200 dark:text-primary-400 text-xs font-bold tracking-widest uppercase mb-6 backdrop-blur-sm">
            <i data-lucide="newspaper" class="w-3.5 h-3.5"></i> {{ __('Informasi Terkini') }}
        </span>
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold mb-4 tracking-tight text-white">{{ __('Berita & Kegiatan') }}</h1>
        <p class="text-primary-100/80 dark:text-surface-400 text-lg max-w-2xl mx-auto leading-relaxed">{{ __('Kabar terbaru, pengumuman, dan liputan kegiatan seputar') }} {{ $pesantren->nama ?? 'Pesantren' }}.</p>
    </div>
    <div class="absolute bottom-0 left-0 right-0">
        <svg class="w-full h-16 sm:h-20 text-surface-50 dark:text-surface-950 fill-current" viewBox="0 0 1440 120" preserveAspectRatio="none">
            <path d="M0,64L80,69.3C160,75,320,85,480,80C640,75,800,53,960,48C1120,43,1280,53,1360,58.7L1440,64L1440,120L1360,120C1280,120,1120,120,960,120C800,120,640,120,480,120C320,120,160,120,80,120L0,120Z"></path>
        </svg>
    </div>
</section>

{{-- ═══ BERITA GRID ═══ --}}
<section class="py-16 bg-surface-50 dark:bg-surface-950 min-h-[50vh] transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($beritas as $berita)
            <article class="bg-white dark:bg-surface-900 rounded-3xl overflow-hidden border border-surface-100 dark:border-surface-800 hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] dark:hover:shadow-[0_10px_30px_-10px_rgba(0,0,0,0.5)] hover:-translate-y-1 transition-all duration-300 group flex flex-col">
                <div class="aspect-video overflow-hidden bg-surface-100 dark:bg-surface-800 relative">
                    @if($berita->gambar_cover)
                        <img src="{{ Storage::url($berita->gambar_cover) }}" alt="{{ $berita->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-surface-300 dark:text-surface-600 bg-gradient-to-br from-surface-50 to-surface-100 dark:from-surface-800 dark:to-surface-900">
                            <i data-lucide="image" class="w-12 h-12"></i>
                        </div>
                    @endif
                    @if($berita->kategori)
                    <div class="absolute top-4 left-4">
                        <span class="px-3 py-1 bg-white/90 dark:bg-surface-900/90 backdrop-blur-md text-primary-700 dark:text-primary-400 text-xs font-bold rounded-lg shadow-sm">
                            {{ $berita->kategori->nama }}
                        </span>
                    </div>
                    @endif
                </div>
                <div class="p-7 flex flex-col flex-grow">
                    <div class="flex items-center gap-4 text-xs text-surface-400 dark:text-surface-500 font-bold mb-3">
                        <span class="flex items-center gap-1.5">
                            <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                            {{ $berita->tanggal_format }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                            {{ number_format($berita->view_count) }}x
                        </span>
                    </div>
                    <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-3 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors line-clamp-2 leading-snug">
                        <a href="{{ route('frontend.berita.show', $berita->slug) }}">{{ $berita->judul }}</a>
                    </h3>
                    <p class="text-surface-500 dark:text-surface-400 text-sm line-clamp-3 mb-5 flex-grow leading-relaxed">{{ $berita->ringkasan ?? Str::limit(strip_tags($berita->konten), 120) }}</p>
                    <a href="{{ route('frontend.berita.show', $berita->slug) }}" class="text-primary-600 dark:text-primary-400 font-bold text-sm flex items-center gap-2 group/l">
                        {{ __('Baca Selengkapnya') }} <i data-lucide="arrow-right" class="w-4 h-4 group-hover/l:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </article>
            @empty
            <div class="col-span-full text-center py-20 bg-white dark:bg-surface-900 rounded-3xl border border-dashed border-surface-300 dark:border-surface-700">
                <div class="w-20 h-20 bg-surface-50 dark:bg-surface-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="newspaper" class="w-10 h-10 text-surface-300 dark:text-surface-500"></i>
                </div>
                <h3 class="text-xl font-bold text-surface-900 dark:text-white mb-2">{{ __('Belum Ada Berita') }}</h3>
                <p class="text-surface-500 dark:text-surface-400">{{ __('Saat ini belum ada berita atau kegiatan yang dipublikasikan.') }}</p>
            </div>
            @endforelse
        </div>

        @if($beritas->hasPages())
        <div class="mt-12 flex justify-center">
            {{ $beritas->links() }}
        </div>
        @endif
    </div>
</section>
@endsection
