@extends('layouts.guest')

@section('title', '500 — Kesalahan Server | PP Nurul Furqon')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-surface-50 px-4 py-12">
    <div class="max-w-md w-full text-center space-y-6 bg-white p-8 md:p-10 rounded-3xl border border-surface-200 shadow-xl relative overflow-hidden">
        <div class="w-20 h-20 bg-amber-50 text-amber-600 rounded-3xl flex items-center justify-center mx-auto border border-amber-100 shadow-sm">
            <i data-lucide="server-off" class="w-10 h-10"></i>
        </div>
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-black uppercase tracking-wider mb-2">
                Error 500
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-surface-900 font-heading">Kesalahan Server</h1>
            <p class="text-xs md:text-sm text-surface-500 mt-2 leading-relaxed">
                Maaf, terjadi kendala teknis pada sistem. Tim pengembang telah diberitahu untuk menangani masalah ini.
            </p>
        </div>
        <div class="pt-4 border-t border-surface-100 flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ url('/') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-emerald-700 text-white font-extrabold text-xs hover:bg-emerald-800 transition-all shadow-md shadow-emerald-700/20">
                <i data-lucide="home" class="w-4 h-4"></i>
                <span>Kembali ke Beranda</span>
            </a>
            <button onclick="window.location.reload()" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-surface-100 text-surface-700 font-extrabold text-xs hover:bg-surface-200 transition-all border border-surface-200">
                <i data-lucide="rotate-cw" class="w-4 h-4"></i>
                <span>Muat Ulang Halaman</span>
            </button>
        </div>
    </div>
</div>
@endsection
