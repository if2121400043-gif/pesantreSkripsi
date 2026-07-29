@extends('layouts.app')

@section('title', 'Tambah Rombongan Belajar')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <div class="flex items-center gap-2 text-sm text-surface-500 mb-2">
            <a href="{{ route('admin.rombel.index') }}" class="hover:text-primary-600 transition-colors">Data Rombel</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <span class="text-surface-900 font-medium">Tambah Kelas Baru</span>
        </div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Tambah Rombongan Belajar</h1>
    </div>
    <a href="{{ route('admin.rombel.index') }}" class="btn-secondary flex items-center gap-2">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Kembali</span>
    </a>
</div>
@endsection

@section('content')
<form action="{{ route('admin.rombel.store') }}" method="POST" class="max-w-3xl mx-auto space-y-6">
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

    <x-card title="Pengaturan Kelas & Lembaga">
        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Lembaga Pendidikan <span class="text-danger-500">*</span></label>
                    <select name="lembaga_id" required class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        <option value="" disabled selected>Pilih Lembaga...</option>
                        @foreach($lembagas as $lembaga)
                            <option value="{{ $lembaga->id }}" {{ old('lembaga_id') == $lembaga->id ? 'selected' : '' }}>
                                {{ $lembaga->nama }} ({{ $lembaga->jenjang }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Tahun Pelajaran <span class="text-danger-500">*</span></label>
                    <select name="tahun_pelajaran_id" required class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        @foreach($tahuns as $tahun)
                            <option value="{{ $tahun->id }}" {{ (old('tahun_pelajaran_id') == $tahun->id || ($loop->first && !old('tahun_pelajaran_id'))) ? 'selected' : '' }}>
                                {{ $tahun->nama }} {{ $tahun->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-input type="text" name="tingkat" label="Tingkat Kelas (Opsional)" placeholder="Contoh: 7, 8, 9, X, XI" value="{{ old('tingkat') }}" />
                <x-form-input name="nama" label="Nama Kelas (Grup) *" required placeholder="Contoh: 7A, X-IPA-1" value="{{ old('nama') }}" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Wali Kelas (Opsional)</label>
                    <select name="wali_kelas_id" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        <option value="" selected>Belum Ditentukan</option>
                        @foreach($pegawais as $pegawai)
                            <option value="{{ $pegawai->id }}" {{ old('wali_kelas_id') == $pegawai->id ? 'selected' : '' }}>
                                {{ $pegawai->orang->nama_lengkap }} ({{ $pegawai->jenis_pegawai }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <x-form-input type="number" name="kapasitas" label="Kapasitas Siswa Maksimal" required min="1" placeholder="Contoh: 30" value="{{ old('kapasitas', 30) }}" />
            </div>
        </div>
        
        <div class="mt-6 pt-6 border-t border-surface-100 flex justify-end gap-3">
            <a href="{{ route('admin.rombel.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">Simpan Rombel</button>
        </div>
    </x-card>
</form>
@endsection
