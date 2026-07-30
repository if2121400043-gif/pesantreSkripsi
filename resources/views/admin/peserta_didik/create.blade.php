@extends('layouts.app')

@section('title', 'Pendaftaran Santri Baru')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <div class="flex items-center gap-2 text-sm text-surface-500 mb-2">
            <a href="{{ route('admin.peserta-didik.index') }}" class="hover:text-primary-600 transition-colors">Data Santri</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <span class="text-surface-900 font-medium">Registrasi Akademik</span>
        </div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Daftarkan Santri / Siswa Baru</h1>
    </div>
    <a href="{{ route('admin.peserta-didik.index') }}" class="btn-secondary flex items-center gap-2">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Kembali</span>
    </a>
</div>
@endsection

@section('content')
<form action="{{ route('admin.peserta-didik.store') }}" method="POST" class="max-w-3xl mx-auto space-y-6">
    @csrf

    @if($errors->any())
        <div class="bg-danger-50 text-danger-700 p-4 rounded-xl border border-danger-200">
            <div class="flex gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0 mt-0.5"></i>
                <ul class="list-disc pl-5 space-y-1 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <x-card title="Pilih Identitas Induk (Orang)">
        <div class="space-y-4">
            <p class="text-sm text-surface-500">Santri harus terdaftar di sistem identitas induk (NIUP) terlebih dahulu sebelum bisa didaftarkan secara akademik.</p>
            
            <div>
                <label for="orang_id" class="block text-sm font-medium text-surface-700 mb-1">Pilih Orang <span class="text-danger-500">*</span></label>
                <select name="orang_id" id="orang_id" required class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    <option value="" disabled selected>Pilih dari data yang belum menjadi santri...</option>
                    @foreach($calonPeserta as $orang)
                        <option value="{{ $orang->id }}" {{ (old('orang_id') == $orang->id || $selectedOrangId == $orang->id) ? 'selected' : '' }}>
                            {{ $orang->niup }} — {{ $orang->nama_lengkap }} ({{ $orang->jenis_kelamin }})
                        </option>
                    @endforeach
                </select>
                <div class="mt-2 text-xs text-surface-500 flex items-center gap-1">
                    <i data-lucide="info" class="w-3 h-3"></i> Tidak menemukan nama? <a href="{{ route('admin.orang.create') }}" class="text-primary-600 font-medium hover:underline">Buat Identitas Induk Baru</a>
                </div>
            </div>
        </div>
    </x-card>

    <x-card title="Data Akademik">
        <div class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input name="nis" label="Nomor Induk Siswa (NIS Lokal)" placeholder="Kosongkan jika belum ada" value="{{ old('nis') }}" />
                <x-form-input name="nisn" label="NISN (Nasional)" placeholder="10 Digit Angka" maxlength="10" value="{{ old('nisn') }}" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input type="date" name="tanggal_masuk" label="Tanggal Masuk / Diterima" required value="{{ old('tanggal_masuk', date('Y-m-d')) }}" />
                
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Status Akademik <span class="text-danger-500">*</span></label>
                    <select name="status" required class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        <option value="AKTIF" selected>AKTIF (Belajar)</option>
                        <option value="MUTASI_KELUAR">MUTASI (Pindahan dari sini)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-surface-700 mb-1">Catatan Tambahan</label>
                <textarea name="catatan" rows="3" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">{{ old('catatan') }}</textarea>
            </div>
        </div>
        
        <div class="mt-6 pt-6 border-t border-surface-100 flex justify-end gap-3">
            <a href="{{ route('admin.peserta-didik.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">Daftarkan Santri</button>
        </div>
    </x-card>
</form>
@endsection
