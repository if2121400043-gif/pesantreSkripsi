@extends('layouts.guest')

@section('title', '419 — Sesi Berakhir | PP Nurul Furqon')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-surface-50 px-4 py-12">
    <div class="max-w-md w-full text-center space-y-6 bg-white p-8 md:p-10 rounded-3xl border border-surface-200 shadow-xl relative overflow-hidden">
        <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-3xl flex items-center justify-center mx-auto border border-blue-100 shadow-sm">
            <i data-lucide="clock" class="w-10 h-10"></i>
        </div>
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-black uppercase tracking-wider mb-2">
                Error 419
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-surface-900 font-heading">Sesi Telah Berakhir</h1>
            <p class="text-xs md:text-sm text-surface-500 mt-2 leading-relaxed">
                Halaman telah didiamkan terlalu lama sehingga token keamanan expired. Silakan muat ulang halaman.
            </p>
        </div>
        <div class="pt-4 border-t border-surface-100 flex flex-col sm:flex-row gap-3 justify-center">
            <button onclick="window.location.reload()" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-emerald-700 text-white font-extrabold text-xs hover:bg-emerald-800 transition-all shadow-md shadow-emerald-700/20">
                <i data-lucide="rotate-cw" class="w-4 h-4"></i>
                <span>Muat Ulang Halaman</span>
            </button>
            <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-surface-100 text-surface-700 font-extrabold text-xs hover:bg-surface-200 transition-all border border-surface-200">
                <i data-lucide="log-in" class="w-4 h-4"></i>
                <span>Halaman Login</span>
            </a>
        </div>
    </div>
</div>
@endsection
