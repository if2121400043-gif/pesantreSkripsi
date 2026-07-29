@extends('layouts.app')

@section('title', 'Dashboard Guru')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Ahlan wa Sahlan, {{ auth()->user()->name }}</h1>
        <p class="text-sm text-surface-500 mt-1">Portal manajemen akademik dan kepesantrenan.</p>
    </div>
</div>
@endsection

@section('content')
<div class="bg-warning-50 border border-warning-200 rounded-2xl p-8 text-center max-w-2xl mx-auto mt-12 shadow-sm">
    <div class="w-20 h-20 bg-warning-100 text-warning-600 rounded-full flex items-center justify-center mx-auto mb-6">
        <i data-lucide="alert-triangle" class="w-10 h-10"></i>
    </div>
    <h2 class="text-2xl font-bold text-surface-900 mb-3">Data Pegawai Tidak Ditemukan</h2>
    <p class="text-surface-600 mb-6 text-lg leading-relaxed">
        Akun Anda memiliki akses (role) sebagai Guru, namun belum ditautkan dengan data profil Pegawai (Ustadz/ah) manapun di sistem.
    </p>
    <div class="bg-white p-4 rounded-xl border border-warning-100 text-sm text-left inline-block shadow-sm">
        <p class="font-bold text-surface-900 mb-2">Apa yang harus dilakukan?</p>
        <ul class="list-disc pl-5 text-surface-600 space-y-1">
            <li>Hubungi Super Admin atau Operator.</li>
            <li>Minta admin untuk masuk ke menu <strong>Manajemen User</strong>.</li>
            <li>Pastikan akun Anda sudah dihubungkan dengan data <strong>Orang</strong> yang terdaftar sebagai <strong>Pegawai</strong>.</li>
        </ul>
    </div>
</div>
@endsection
