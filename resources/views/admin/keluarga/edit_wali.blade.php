@extends('layouts.app')

@section('title', 'Edit Profil Wali — PP Nurul Furqon')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <div class="flex items-center gap-2 text-xs text-surface-500 mb-1.5">
            <a href="{{ route('admin.keluarga.index') }}" class="hover:text-primary-600 transition-colors font-medium">Relasi Keluarga</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            <span class="text-surface-900 font-bold">Edit Profil Wali</span>
        </div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Edit Profil Wali Santri</h1>
    </div>
    <a href="{{ route('admin.keluarga.index') }}" class="btn-secondary flex items-center gap-2 text-xs font-bold py-2.5 px-4 rounded-xl">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Kembali ke Daftar</span>
    </a>
</div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6 pb-12">

    {{-- Notifikasi Error Global --}}
    @if($errors->any())
        <div class="bg-danger-50 text-danger-800 p-4 rounded-2xl border border-danger-200 shadow-sm">
            <div class="flex gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 text-danger-600 flex-shrink-0 mt-0.5"></i>
                <div>
                    <h3 class="font-bold text-xs mb-1">Terdapat kesalahan pengisian:</h3>
                    <ul class="list-disc pl-5 space-y-1 text-xs font-medium">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.keluarga.wali.update', $orang) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-surface-200 shadow-sm space-y-6">
            
            <div class="flex items-center gap-3.5 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl">
                <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold shrink-0 shadow-sm" style="background-color: #047857 !important; color: #ffffff !important;">
                    <i data-lucide="user-cog" class="w-5 h-5"></i>
                </div>
                <div>
                    <div class="text-[0.68rem] text-emerald-800 font-extrabold uppercase tracking-wider">NIUP / Kode Wali</div>
                    <div class="text-sm font-extrabold text-emerald-950 font-mono">{{ $orang->niup ?? '-' }}</div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-surface-700 mb-1.5">Nama Lengkap Wali <span class="text-danger-500">*</span></label>
                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $orang->nama_lengkap) }}" required class="w-full rounded-xl border border-surface-300 bg-white px-3.5 py-2.5 text-xs font-semibold focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-surface-700 mb-1.5">Nomor WhatsApp / Telepon <span class="text-danger-500">*</span></label>
                <input type="text" name="telepon" value="{{ old('telepon', $orang->telepon) }}" required class="w-full rounded-xl border border-surface-300 bg-white px-3.5 py-2.5 text-xs font-semibold focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                <p class="text-[0.68rem] text-surface-500 mt-1">Nomor ini digunakan untuk pengiriman notifikasi pengingat & akses login portal wali.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-surface-700 mb-1.5">Alamat Lengkap</label>
                <textarea name="alamat_lengkap" rows="3" class="w-full rounded-xl border border-surface-300 bg-white px-3.5 py-2.5 text-xs font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">{{ old('alamat_lengkap', $orang->alamat_lengkap) }}</textarea>
            </div>

            {{-- Submit & Cancel Buttons --}}
            <div class="pt-4 border-t border-surface-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.keluarga.index') }}" class="btn-secondary text-xs font-bold py-2.5 px-5 rounded-xl">
                    Batal
                </a>
                <button type="submit" class="btn-primary text-xs font-bold py-2.5 px-6 rounded-xl shadow-md flex items-center gap-2" style="color: #ffffff !important; background-color: #047857 !important;">
                    <i data-lucide="save" class="w-4 h-4 text-white" style="color: #ffffff !important;"></i>
                    <span style="color: #ffffff !important;">Simpan Perubahan Profil</span>
                </button>
            </div>

        </div>
    </form>
</div>
@endsection
