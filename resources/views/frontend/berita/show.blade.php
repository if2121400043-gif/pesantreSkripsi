@extends('frontend.layouts.app')

@section('title', $berita->judul)

@section('content')
{{-- ═══ HERO HEADER ═══ --}}
<section class="relative overflow-hidden bg-gradient-to-br from-primary-900 via-primary-800 to-surface-900 dark:from-surface-950 dark:via-surface-900 dark:to-surface-950 text-white transition-colors duration-300">
    <div class="absolute inset-0 opacity-[0.05] dark:opacity-[0.02]" style="background-image:url('data:image/svg+xml,%3Csvg width=60 height=60 viewBox=%220 0 60 60%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22none%22 fill-rule=%22evenodd%22%3E%3Cg fill=%22%23ffffff%22 fill-opacity=%221%22%3E%3Cpath d=%22M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z%22/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 py-16 md:py-20">
        {{-- Breadcrumb --}}
        <nav class="flex text-sm text-primary-200/70 dark:text-surface-500 mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li><a href="{{ route('frontend.home') }}" class="hover:text-white dark:hover:text-primary-400 transition-colors">{{ __('Beranda') }}</a></li>
                <li><div class="flex items-center"><i data-lucide="chevron-right" class="w-3.5 h-3.5 mx-1"></i><a href="{{ route('frontend.berita') }}" class="hover:text-white dark:hover:text-primary-400 transition-colors">{{ __('Berita') }}</a></div></li>
                <li aria-current="page"><div class="flex items-center"><i data-lucide="chevron-right" class="w-3.5 h-3.5 mx-1"></i><span class="text-white/80 dark:text-surface-300 font-medium truncate max-w-[200px]">{{ $berita->judul }}</span></div></li>
            </ol>
        </nav>
        <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold leading-tight tracking-tight text-white">{{ $berita->judul }}</h1>
        <div class="flex flex-wrap items-center gap-4 mt-6 text-sm text-primary-200/80 dark:text-surface-400">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-white/10 dark:bg-surface-800 flex items-center justify-center"><i data-lucide="user" class="w-4 h-4"></i></div>
                <span class="font-medium text-white/90 dark:text-surface-300">{{ $berita->penulis->name ?? 'Admin' }}</span>
            </div>
            <div class="w-1 h-1 rounded-full bg-primary-300/50 dark:bg-surface-600"></div>
            <span class="flex items-center gap-1.5"><i data-lucide="calendar" class="w-4 h-4"></i> {{ $berita->tanggal_format }}</span>
            <div class="w-1 h-1 rounded-full bg-primary-300/50 dark:bg-surface-600"></div>
            <span class="flex items-center gap-1.5"><i data-lucide="eye" class="w-4 h-4"></i> {{ number_format($berita->view_count) }} {{ __('kali dibaca') }}</span>
        </div>
    </div>
    <div class="absolute bottom-0 left-0 right-0">
        <svg class="w-full h-12 sm:h-16 text-surface-50 dark:text-surface-950 fill-current" viewBox="0 0 1440 120" preserveAspectRatio="none">
            <path d="M0,64L80,69.3C160,75,320,85,480,80C640,75,800,53,960,48C1120,43,1280,53,1360,58.7L1440,64L1440,120L1360,120C1280,120,1120,120,960,120C800,120,640,120,480,120C320,120,160,120,80,120L0,120Z"></path>
        </svg>
    </div>
</section>

{{-- ═══ ARTICLE CONTENT ═══ --}}
<section class="py-12 bg-surface-50 dark:bg-surface-950 transition-colors duration-300">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <article class="bg-white dark:bg-surface-900 rounded-3xl shadow-xl shadow-surface-200/40 dark:shadow-none overflow-hidden border border-surface-100 dark:border-surface-800 transition-colors duration-300">
            @if($berita->gambar_cover)
                <div class="w-full aspect-[21/9] relative overflow-hidden bg-surface-200 dark:bg-surface-800">
                    <img src="{{ Storage::url($berita->gambar_cover) }}" alt="{{ $berita->judul }}" class="w-full h-full object-cover">
                </div>
            @endif

            <div class="p-8 md:p-12">
                <div class="prose prose-lg prose-surface dark:prose-invert max-w-none text-surface-700 dark:text-surface-300">
                    {!! $berita->konten !!}
                </div>

                {{-- Share options --}}
                <div class="mt-12 pt-6 border-t border-surface-100 dark:border-surface-800 flex items-center gap-4">
                    <span class="text-sm font-bold text-surface-900 dark:text-white">{{ __('Bagikan') }}:</span>
                    <button class="w-10 h-10 rounded-full bg-surface-100 dark:bg-surface-800 text-surface-600 dark:text-surface-400 flex items-center justify-center hover:bg-primary-50 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                        <i data-lucide="facebook" class="w-5 h-5"></i>
                    </button>
                    <button class="w-10 h-10 rounded-full bg-surface-100 dark:bg-surface-800 text-surface-600 dark:text-surface-400 flex items-center justify-center hover:bg-info-50 dark:hover:bg-info-500/10 hover:text-info-500 transition-colors">
                        <i data-lucide="twitter" class="w-5 h-5"></i>
                    </button>
                    <button class="w-10 h-10 rounded-full bg-surface-100 dark:bg-surface-800 text-surface-600 dark:text-surface-400 flex items-center justify-center hover:bg-success-50 dark:hover:bg-success-500/10 hover:text-success-500 transition-colors" onclick="navigator.clipboard.writeText(window.location.href); alert('{{ __('Tautan disalin!') }}')">
                        <i data-lucide="link" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
        </article>

        {{-- Berita Lainnya --}}
        @if($beritaLainnya->count() > 0)
            <div class="mt-16">
                <h3 class="text-2xl font-bold text-surface-900 dark:text-white mb-8">{{ __('Baca Juga') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($beritaLainnya as $lain)
                        <a href="{{ route('frontend.berita.show', $lain->slug) }}" class="group flex gap-4 bg-white dark:bg-surface-900 p-5 rounded-2xl shadow-sm dark:shadow-none border border-surface-100 dark:border-surface-800 hover:border-primary-200 dark:hover:border-primary-700 transition-all duration-300 hover:-translate-y-0.5">
                            <div class="w-24 h-24 flex-shrink-0 rounded-xl overflow-hidden bg-surface-100 dark:bg-surface-800">
                                @if($lain->gambar_cover)
                                    <img src="{{ Storage::url($lain->gambar_cover) }}" alt="{{ $lain->judul }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-surface-400 dark:text-surface-600">
                                        <i data-lucide="image" class="w-6 h-6"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex flex-col justify-center min-w-0">
                                <span class="text-xs text-surface-500 dark:text-surface-500 mb-1 font-bold">{{ $lain->tanggal_format }}</span>
                                <h4 class="font-bold text-surface-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors line-clamp-2 leading-snug">{{ $lain->judul }}</h4>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
