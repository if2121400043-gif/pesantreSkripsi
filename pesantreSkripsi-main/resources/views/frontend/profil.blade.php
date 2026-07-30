@extends('frontend.layouts.app')

@section('title', __('Profil Pesantren'))

@section('content')
{{-- ═══ HERO HEADER ═══ --}}
<section class="relative overflow-hidden bg-gradient-to-br from-primary-900 via-primary-800 to-surface-900 dark:from-surface-950 dark:via-surface-900 dark:to-surface-950 text-white transition-colors duration-300">
    <div class="absolute inset-0 opacity-[0.05] dark:opacity-[0.02]" style="background-image:url('data:image/svg+xml,%3Csvg width=60 height=60 viewBox=%220 0 60 60%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22none%22 fill-rule=%22evenodd%22%3E%3Cg fill=%22%23ffffff%22 fill-opacity=%221%22%3E%3Cpath d=%22M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z%22/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>
    <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-secondary-500/15 rounded-full blur-[120px] pointer-events-none -translate-y-1/2 translate-x-1/3"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 py-20 md:py-28 text-center">
        <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 dark:bg-surface-800/50 border border-white/20 dark:border-surface-700 text-primary-200 dark:text-primary-400 text-xs font-bold tracking-widest uppercase mb-6 backdrop-blur-sm">
            <i data-lucide="building" class="w-3.5 h-3.5"></i> {{ __('Tentang Kami') }}
        </span>
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold mb-4 tracking-tight text-white">{{ __('Profil Pesantren') }}</h1>
        <p class="text-primary-100/80 dark:text-surface-400 text-lg max-w-2xl mx-auto leading-relaxed">{{ __('Mengenal lebih dekat') }} {{ $pesantren?->nama ?? 'Pesantren Nurul Furqon' }}, {{ __('sejarah, visi, dan misi kami.') }}</p>
    </div>
    <div class="absolute bottom-0 left-0 right-0">
        <svg class="w-full h-16 sm:h-20 text-surface-50 dark:text-surface-950 fill-current" viewBox="0 0 1440 120" preserveAspectRatio="none">
            <path d="M0,64L80,69.3C160,75,320,85,480,80C640,75,800,53,960,48C1120,43,1280,53,1360,58.7L1440,64L1440,120L1360,120C1280,120,1120,120,960,120C800,120,640,120,480,120C320,120,160,120,80,120L0,120Z"></path>
        </svg>
    </div>
</section>

{{-- ═══ INFO CARDS ═══ --}}
<section class="py-16 bg-surface-50 dark:bg-surface-950 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Sidebar Info Card --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-surface-900 rounded-3xl shadow-lg shadow-surface-200/40 dark:shadow-none border border-surface-100 dark:border-surface-800 p-8 sticky top-24 transition-colors duration-300">
                    <div class="flex items-center gap-4 mb-6">
                        <img src="{{ asset('images/logo-pesantren.webp') }}?v={{ time() }}" alt="Logo" class="w-16 h-16 object-contain bg-white dark:bg-surface-800 rounded-2xl shadow-md p-1 border border-surface-100 dark:border-surface-700">
                        <div>
                            <h2 class="text-lg font-bold text-surface-900 dark:text-white">{{ $pesantren?->nama ?? 'Pesantren Nurul Furqon' }}</h2>
                            <p class="text-surface-400 dark:text-surface-500 text-xs font-bold uppercase tracking-wider">NSPP: {{ $pesantren?->nspp ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="space-y-5 divide-y divide-surface-100 dark:divide-surface-800">
                        <div class="pt-4 first:pt-0">
                            <h3 class="text-[11px] font-bold uppercase tracking-widest text-surface-400 dark:text-surface-500 mb-2">{{ __('Pimpinan') }}</h3>
                            <p class="font-semibold text-surface-900 dark:text-white text-sm">{{ $pesantren?->nama_pimpinan ?? '-' }}</p>
                        </div>
                        <div class="pt-4">
                            <h3 class="text-[11px] font-bold uppercase tracking-widest text-surface-400 dark:text-surface-500 mb-3">{{ __('Kontak') }}</h3>
                            <ul class="space-y-3">
                                @if($pesantren?->telepon)
                                <li class="flex items-center gap-3 text-sm">
                                    <div class="w-9 h-9 rounded-xl bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 flex items-center justify-center flex-shrink-0"><i data-lucide="phone" class="w-4 h-4"></i></div>
                                    <span class="font-medium text-surface-700 dark:text-surface-300">{{ $pesantren?->telepon }}</span>
                                </li>
                                @endif
                                @if($pesantren?->email)
                                <li class="flex items-center gap-3 text-sm">
                                    <div class="w-9 h-9 rounded-xl bg-info-50 dark:bg-info-500/10 text-info-500 flex items-center justify-center flex-shrink-0"><i data-lucide="mail" class="w-4 h-4"></i></div>
                                    <span class="font-medium text-surface-700 dark:text-surface-300">{{ $pesantren?->email }}</span>
                                </li>
                                @endif
                                @if($pesantren?->website)
                                <li class="flex items-center gap-3 text-sm">
                                    <div class="w-9 h-9 rounded-xl bg-success-50 dark:bg-success-500/10 text-success-500 flex items-center justify-center flex-shrink-0"><i data-lucide="globe" class="w-4 h-4"></i></div>
                                    <span class="font-medium text-surface-700 dark:text-surface-300">{{ $pesantren?->website }}</span>
                                </li>
                                @endif
                            </ul>
                        </div>
                        <div class="pt-4">
                            <h3 class="text-[11px] font-bold uppercase tracking-widest text-surface-400 dark:text-surface-500 mb-3">{{ __('Alamat') }}</h3>
                            <div class="flex items-start gap-3 text-sm">
                                <div class="w-9 h-9 rounded-xl bg-accent-50 dark:bg-accent-500/10 text-accent-600 dark:text-accent-400 flex items-center justify-center flex-shrink-0 mt-0.5"><i data-lucide="map-pin" class="w-4 h-4"></i></div>
                                <p class="text-surface-700 dark:text-surface-300 leading-relaxed">
                                    {{ $pesantren?->alamat ?? '-' }}
                                    @if($pesantren?->kode_pos) Kode Pos: {{ $pesantren?->kode_pos }} @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-8">

                {{-- Sejarah --}}
                <div class="bg-white dark:bg-surface-900 rounded-3xl shadow-sm dark:shadow-none border border-surface-100 dark:border-surface-800 p-8 md:p-10 transition-colors duration-300">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-11 h-11 rounded-2xl bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 flex items-center justify-center"><i data-lucide="book-open" class="w-5 h-5"></i></div>
                        <h2 class="text-xl font-bold text-surface-900 dark:text-white">{{ __('Sejarah Berdiri') }}</h2>
                    </div>
                    <div class="prose prose-surface dark:prose-invert max-w-none text-surface-600 dark:text-surface-300 leading-relaxed text-[15px]">
                        {!! $pesantren?->sejarah ?? '<p class="text-surface-400 dark:text-surface-500 italic">' . __('Belum ada informasi sejarah pesantren.') . '</p>' !!}
                    </div>
                </div>

                {{-- Visi & Misi --}}
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-surface-900 rounded-3xl shadow-sm dark:shadow-none border border-surface-100 dark:border-surface-800 p-8 relative overflow-hidden group hover:border-primary-300 dark:hover:border-primary-700 transition-all duration-300">
                        <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-primary-50 dark:bg-primary-900/20 rounded-full opacity-50 group-hover:opacity-100 group-hover:scale-110 transition-all duration-500"></div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-11 h-11 rounded-2xl bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 flex items-center justify-center"><i data-lucide="eye" class="w-5 h-5"></i></div>
                                <h3 class="text-lg font-bold text-surface-900 dark:text-white">{{ __('Visi') }}</h3>
                            </div>
                            <div class="prose prose-sm dark:prose-invert text-surface-600 dark:text-surface-300 leading-relaxed">
                                {!! $pesantren?->visi ?? '<p class="text-surface-400 dark:text-surface-500 italic">' . __('Belum diisi.') . '</p>' !!}
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-surface-900 rounded-3xl shadow-sm dark:shadow-none border border-surface-100 dark:border-surface-800 p-8 relative overflow-hidden group hover:border-secondary-300 dark:hover:border-secondary-700 transition-all duration-300">
                        <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-secondary-50 dark:bg-secondary-900/20 rounded-full opacity-50 group-hover:opacity-100 group-hover:scale-110 transition-all duration-500"></div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-11 h-11 rounded-2xl bg-secondary-50 dark:bg-secondary-900/30 text-secondary-600 dark:text-secondary-400 flex items-center justify-center"><i data-lucide="target" class="w-5 h-5"></i></div>
                                <h3 class="text-lg font-bold text-surface-900 dark:text-white">{{ __('Misi') }}</h3>
                            </div>
                            <div class="prose prose-sm dark:prose-invert text-surface-600 dark:text-surface-300 leading-relaxed">
                                {!! $pesantren?->misi ?? '<p class="text-surface-400 dark:text-surface-500 italic">' . __('Belum diisi.') . '</p>' !!}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tahun Berdiri Badge --}}
                @if($pesantren?->tahun_berdiri)
                <div class="bg-gradient-to-r from-primary-600 to-secondary-600 rounded-3xl p-8 text-white flex items-center gap-6 shadow-xl shadow-primary-600/20 border border-white/10 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full blur-xl pointer-events-none -translate-y-1/2 translate-x-1/4"></div>
                    <div class="w-16 h-16 rounded-2xl bg-white/10 backdrop-blur-sm flex items-center justify-center flex-shrink-0 border border-white/10">
                        <i data-lucide="landmark" class="w-8 h-8"></i>
                    </div>
                    <div class="relative z-10">
                        <p class="text-primary-100 text-sm font-bold uppercase tracking-widest">{{ __('Berdiri Sejak') }}</p>
                        <p class="text-4xl font-extrabold mt-1">{{ $pesantren?->tahun_berdiri }}</p>
                        <p class="text-primary-100/80 text-sm mt-1 font-medium">{{ $pesantren?->tahun_berdiri ? date('Y') - $pesantren->tahun_berdiri : 0 }} {{ __('tahun mengabdi untuk pendidikan Islam') }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
