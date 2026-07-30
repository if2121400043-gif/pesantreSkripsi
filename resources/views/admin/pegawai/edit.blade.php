@extends('layouts.app')

@section('title', 'Edit Pegawai: ' . $pegawai->orang->nama_lengkap)

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <div class="flex items-center gap-2 text-sm text-surface-500 mb-2">
            <a href="{{ route('admin.pegawai.index') }}" class="hover:text-primary-600 transition-colors">Data Pegawai</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <span class="text-surface-900 font-medium">{{ $pegawai->orang->niup }}</span>
        </div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Edit SDM: {{ $pegawai->orang->nama_lengkap }}</h1>
    </div>
    <a href="{{ route('admin.pegawai.index') }}" class="btn-secondary flex items-center gap-2">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Kembali</span>
    </a>
</div>
@endsection

@section('content')
<form action="{{ route('admin.pegawai.update', $pegawai) }}" method="POST" class="max-w-3xl mx-auto space-y-6">
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
                {{ substr($pegawai->orang->nama_lengkap, 0, 1) }}
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-surface-900">{{ $pegawai->orang->nama_lengkap }}</h3>
                <p class="text-sm text-surface-500">NIUP: {{ $pegawai->orang->niup }} &bull; {{ $pegawai->orang->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
            </div>
            <a href="{{ route('admin.orang.edit', $pegawai->orang_id) }}" class="btn-secondary text-xs px-3 py-1.5" target="_blank">Edit Biodata Induk</a>
        </div>
    </x-card>

    <x-card title="Data Kepegawaian (SDM)">
        <div class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input name="nip" label="NIP / Nomor Induk Pegawai" value="{{ old('nip', $pegawai->nip) }}" />
                <x-form-input name="nuptk" label="NUPTK (Nasional)" maxlength="16" value="{{ old('nuptk', $pegawai->nuptk) }}" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Jenis Pegawai Pokok <span class="text-danger-500">*</span></label>
                    <select name="jenis_pegawai" required class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        <option value="GURU" {{ old('jenis_pegawai', $pegawai->jenis_pegawai) === 'GURU' ? 'selected' : '' }}>GURU / PENGAJAR</option>
                        <option value="USTADZ" {{ old('jenis_pegawai', $pegawai->jenis_pegawai) === 'USTADZ' ? 'selected' : '' }}>USTADZ ASRAMA (Musyrif)</option>
                        <option value="PENGASUH" {{ old('jenis_pegawai', $pegawai->jenis_pegawai) === 'PENGASUH' ? 'selected' : '' }}>PENGASUH / KYAI</option>
                        <option value="STAFF_ADMIN" {{ old('jenis_pegawai', $pegawai->jenis_pegawai) === 'STAFF_ADMIN' ? 'selected' : '' }}>STAFF ADMINISTRASI</option>
                        <option value="TENAGA_KEBERSIHAN" {{ old('jenis_pegawai', $pegawai->jenis_pegawai) === 'TENAGA_KEBERSIHAN' ? 'selected' : '' }}>TENAGA KEBERSIHAN / UMUM</option>
                        <option value="KEAMANAN" {{ old('jenis_pegawai', $pegawai->jenis_pegawai) === 'KEAMANAN' ? 'selected' : '' }}>KEAMANAN / SATPAM</option>
                        <option value="LAINNYA" {{ old('jenis_pegawai', $pegawai->jenis_pegawai) === 'LAINNYA' ? 'selected' : '' }}>LAINNYA</option>
                    </select>
                </div>
                <x-form-input name="jabatan" label="Jabatan Spesifik (Struktural)" value="{{ old('jabatan', $pegawai->jabatan) }}" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-form-input type="date" name="tanggal_masuk" label="Tanggal Masuk" value="{{ old('tanggal_masuk', $pegawai->tanggal_masuk?->format('Y-m-d')) }}" />
                <x-form-input type="date" name="tanggal_keluar" label="Tanggal Keluar" value="{{ old('tanggal_keluar', $pegawai->tanggal_keluar?->format('Y-m-d')) }}" />
                
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Status Pekerja <span class="text-danger-500">*</span></label>
                    <select name="status_kepegawaian" required class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        <option value="TETAP" {{ old('status_kepegawaian', $pegawai->status_kepegawaian) === 'TETAP' ? 'selected' : '' }}>TETAP (GTY/PTY)</option>
                        <option value="KONTRAK" {{ old('status_kepegawaian', $pegawai->status_kepegawaian) === 'KONTRAK' ? 'selected' : '' }}>KONTRAK / PKWT</option>
                        <option value="HONORER" {{ old('status_kepegawaian', $pegawai->status_kepegawaian) === 'HONORER' ? 'selected' : '' }}>HONORER</option>
                        <option value="SUKARELAWAN" {{ old('status_kepegawaian', $pegawai->status_kepegawaian) === 'SUKARELAWAN' ? 'selected' : '' }}>SUKARELAWAN</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Pendidikan Terakhir</label>
                    <select name="pendidikan_terakhir" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        <option value="">Pilih...</option>
                        @foreach(['SD', 'SMP', 'SMA', 'D1', 'D2', 'D3', 'S1', 'S2', 'S3'] as $pend)
                            <option value="{{ $pend }}" {{ old('pendidikan_terakhir', $pegawai->pendidikan_terakhir) === $pend ? 'selected' : '' }}>{{ $pend }}</option>
                        @endforeach
                    </select>
                </div>
                <x-form-input name="jurusan_pendidikan" label="Program Studi / Jurusan" value="{{ old('jurusan_pendidikan', $pegawai->jurusan_pendidikan) }}" />
            </div>

            <div>
                <label class="block text-sm font-medium text-surface-700 mb-1">Catatan Kepegawaian</label>
                <textarea name="catatan" rows="3" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">{{ old('catatan', $pegawai->catatan) }}</textarea>
            </div>

            <div class="flex items-center gap-2 mt-4">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $pegawai->is_active) ? 'checked' : '' }} class="rounded border-surface-300 text-primary-600 focus:ring-primary-500 w-4 h-4">
                <label for="is_active" class="text-sm font-medium text-surface-700">Pegawai Aktif Bekerja</label>
            </div>
        </div>
        
        <div class="mt-6 pt-6 border-t border-surface-100 flex justify-between">
            <button type="button" class="btn-secondary text-danger-600 border-danger-200 hover:bg-danger-50" onclick="document.getElementById('delete-form').submit()">Hapus Data Pegawai</button>
            <div class="flex gap-3">
                <a href="{{ route('admin.pegawai.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
            </div>
        </div>
    </x-card>
</form>

<form id="delete-form" action="{{ route('admin.pegawai.destroy', $pegawai) }}" method="POST" class="hidden" onsubmit="return confirm('Apakah Anda yakin ingin menghapus profil kepegawaian ini? Ini tidak akan menghapus data Identitas Induk (NIUP).')">
    @csrf
    @method('DELETE')
</form>
@endsection
