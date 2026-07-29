@extends('layouts.app')

@section('title', 'Edit Santri: ' . $pesertaDidik->orang->nama_lengkap)

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <div class="flex items-center gap-2 text-sm text-surface-500 mb-2">
            <a href="{{ route('admin.peserta-didik.index') }}" class="hover:text-primary-600 transition-colors">Data Santri</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <span class="text-surface-900 font-medium">{{ $pesertaDidik->orang->niup }}</span>
        </div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Edit Akademik: {{ $pesertaDidik->orang->nama_lengkap }}</h1>
    </div>
    <a href="{{ route('admin.peserta-didik.index') }}" class="btn-secondary flex items-center gap-2">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Kembali</span>
    </a>
</div>
@endsection

@section('content')
<form action="{{ route('admin.peserta-didik.update', $pesertaDidik) }}" method="POST" class="max-w-3xl mx-auto space-y-6">
    @csrf
    @method('PUT')

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

    <x-card title="Identitas Induk Terhubung">
        <div class="flex items-center gap-4 bg-surface-50 p-4 rounded-xl border border-surface-100">
            <div class="w-12 h-12 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-lg">
                {{ substr($pesertaDidik->orang->nama_lengkap, 0, 1) }}
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-surface-900">{{ $pesertaDidik->orang->nama_lengkap }}</h3>
                <p class="text-sm text-surface-500">NIUP: {{ $pesertaDidik->orang->niup }} &bull; {{ $pesertaDidik->orang->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
            </div>
            <a href="{{ route('admin.orang.edit', $pesertaDidik->orang_id) }}" class="btn-secondary text-xs px-3 py-1.5" target="_blank">Edit Biodata Induk</a>
        </div>
    </x-card>

    <x-card title="Data Akademik">
        <div class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input name="nis" label="Nomor Induk Siswa (NIS Lokal)" value="{{ old('nis', $pesertaDidik->nis) }}" />
                <x-form-input name="nisn" label="NISN (Nasional)" maxlength="10" value="{{ old('nisn', $pesertaDidik->nisn) }}" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input type="date" name="tanggal_masuk" label="Tanggal Masuk / Diterima" required value="{{ old('tanggal_masuk', $pesertaDidik->tanggal_masuk?->format('Y-m-d')) }}" />
                <x-form-input type="date" name="tanggal_keluar" label="Tanggal Keluar (Lulus/Pindah)" value="{{ old('tanggal_keluar', $pesertaDidik->tanggal_keluar?->format('Y-m-d')) }}" />
            </div>

            <div>
                <label class="block text-sm font-medium text-surface-700 mb-1">Status Akademik <span class="text-danger-500">*</span></label>
                <select name="status" required class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    <option value="AKTIF" {{ old('status', $pesertaDidik->status) === 'AKTIF' ? 'selected' : '' }}>AKTIF (Belajar)</option>
                    <option value="LULUS" {{ old('status', $pesertaDidik->status) === 'LULUS' ? 'selected' : '' }}>LULUS</option>
                    <option value="MUTASI_KELUAR" {{ old('status', $pesertaDidik->status) === 'MUTASI_KELUAR' ? 'selected' : '' }}>MUTASI KELUAR (Pindah)</option>
                    <option value="DIKELUARKAN" {{ old('status', $pesertaDidik->status) === 'DIKELUARKAN' ? 'selected' : '' }}>DIKELUARKAN</option>
                    <option value="MENGUNDURKAN_DIRI" {{ old('status', $pesertaDidik->status) === 'MENGUNDURKAN_DIRI' ? 'selected' : '' }}>MENGUNDURKAN DIRI</option>
                    <option value="MENINGGAL" {{ old('status', $pesertaDidik->status) === 'MENINGGAL' ? 'selected' : '' }}>MENINGGAL DUNIA</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-surface-700 mb-1">Alasan / Keterangan Perubahan Status (Dicatat di Riwayat)</label>
                <input type="text" name="keterangan_status" placeholder="Contoh: Lulus ujian akhir kepesantrenan, pindah sekolah formal, dll" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-surface-700 mb-1">Catatan Tambahan</label>
                <textarea name="catatan" rows="3" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">{{ old('catatan', $pesertaDidik->catatan) }}</textarea>
            </div>
        </div>
        
        <div class="mt-6 pt-6 border-t border-surface-100 flex justify-between">
            <button type="button" class="btn-secondary text-danger-600 border-danger-200 hover:bg-danger-50" onclick="document.getElementById('delete-form').submit()">Hapus Data Akademik</button>
            <div class="flex gap-3">
                <a href="{{ route('admin.peserta-didik.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
            </div>
        </div>
    </x-card>
</form>

<form id="delete-form" action="{{ route('admin.peserta-didik.destroy', $pesertaDidik) }}" method="POST" class="hidden" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data akademik santri ini? Ini tidak akan menghapus data Identitas Induk (NIUP).')">
    @csrf
    @method('DELETE')
</form>
@endsection
