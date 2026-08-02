@extends('layouts.guest')

@section('title', '404 — Halaman Tidak Ditemukan | PP Nurul Furqon')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-surface-50 px-4 py-12">
    <div class="max-w-md w-full text-center space-y-6 bg-white p-8 md:p-10 rounded-3xl border border-surface-200 shadow-xl relative overflow-hidden">
        <div class="w-20 h-20 bg-emerald-50 text-emerald-700 rounded-3xl flex items-center justify-center mx-auto border border-emerald-100 shadow-sm">
            <i data-lucide="compass" class="w-10 h-10 animate-spin-slow"></i>
        </div>
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-black uppercase tracking-wider mb-2">
                Error 404
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-surface-900 font-heading">Halaman Tidak Ditemukan</h1>
            <p class="text-xs md:text-sm text-surface-500 mt-2 leading-relaxed">
                Maaf, halaman atau data yang Anda cari tidak ditemukan atau telah dipindahkan.
            </p>
        </div>
        <div class="pt-4 border-t border-surface-100 flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ url('/') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-emerald-700 text-white font-extrabold text-xs hover:bg-emerald-800 transition-all shadow-md shadow-emerald-700/20">
                <i data-lucide="home" class="w-4 h-4"></i>
                <span>Kembali ke Beranda</span>
            </a>
            <button onclick="window.history.back()" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-surface-100 text-surface-700 font-extrabold text-xs hover:bg-surface-200 transition-all border border-surface-200">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Halaman Sebelumnya</span>
            </button>
        </div>
    </div>
</div>
@endsection
