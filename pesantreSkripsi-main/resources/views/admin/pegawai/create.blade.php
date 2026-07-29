@extends('layouts.app')

@section('title', 'Pendaftaran Pegawai Baru')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <div class="flex items-center gap-2 text-sm text-surface-500 mb-2">
            <a href="{{ route('admin.pegawai.index') }}" class="hover:text-primary-600 transition-colors">Data Pegawai</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <span class="text-surface-900 font-medium">Registrasi SDM</span>
        </div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Daftarkan Pegawai / Guru Baru</h1>
    </div>
    <a href="{{ route('admin.pegawai.index') }}" class="btn-secondary flex items-center gap-2">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Kembali</span>
    </a>
</div>
@endsection

@section('content')
<form action="{{ route('admin.pegawai.store') }}" method="POST" class="max-w-3xl mx-auto space-y-6">
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
            <p class="text-sm text-surface-500">Pegawai harus terdaftar di sistem identitas induk (NIUP) terlebih dahulu sebelum bisa didaftarkan secara kepegawaian.</p>
            
            <div>
                <label for="orang_id" class="block text-sm font-medium text-surface-700 mb-1">Pilih Orang <span class="text-danger-500">*</span></label>
                <select name="orang_id" id="orang_id" required class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    <option value="" disabled selected>Pilih dari data yang belum menjadi pegawai...</option>
                    @foreach($calonPegawai as $orang)
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

    <x-card title="Data Kepegawaian (SDM)">
        <div class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input name="nip" label="NIP / Nomor Induk Pegawai" placeholder="Kosongkan jika belum ada" value="{{ old('nip') }}" />
                <x-form-input name="nuptk" label="NUPTK (Nasional)" placeholder="16 Digit Angka" maxlength="16" value="{{ old('nuptk') }}" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Jenis Pegawai Pokok <span class="text-danger-500">*</span></label>
                    <select name="jenis_pegawai" required class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        <option value="" disabled selected>Pilih Jenis...</option>
                        <option value="GURU" {{ old('jenis_pegawai') === 'GURU' ? 'selected' : '' }}>GURU / PENGAJAR</option>
                        <option value="USTADZ" {{ old('jenis_pegawai') === 'USTADZ' ? 'selected' : '' }}>USTADZ ASRAMA (Musyrif)</option>
                        <option value="PENGASUH" {{ old('jenis_pegawai') === 'PENGASUH' ? 'selected' : '' }}>PENGASUH / KYAI</option>
                        <option value="STAFF_ADMIN" {{ old('jenis_pegawai') === 'STAFF_ADMIN' ? 'selected' : '' }}>STAFF ADMINISTRASI</option>
                        <option value="TENAGA_KEBERSIHAN" {{ old('jenis_pegawai') === 'TENAGA_KEBERSIHAN' ? 'selected' : '' }}>TENAGA KEBERSIHAN / UMUM</option>
                        <option value="KEAMANAN" {{ old('jenis_pegawai') === 'KEAMANAN' ? 'selected' : '' }}>KEAMANAN / SATPAM</option>
                        <option value="LAINNYA" {{ old('jenis_pegawai') === 'LAINNYA' ? 'selected' : '' }}>LAINNYA</option>
                    </select>
                </div>
                <x-form-input name="jabatan" label="Jabatan Spesifik (Struktural)" placeholder="Contoh: Kepala Sekolah, Waka Kurikulum" value="{{ old('jabatan') }}" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input type="date" name="tanggal_masuk" label="Tanggal Mulai Bekerja" value="{{ old('tanggal_masuk', date('Y-m-d')) }}" />
                
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Status Kepegawaian <span class="text-danger-500">*</span></label>
                    <select name="status_kepegawaian" required class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        <option value="TETAP" {{ old('status_kepegawaian') === 'TETAP' ? 'selected' : '' }}>TETAP (GTY/PTY)</option>
                        <option value="KONTRAK" {{ old('status_kepegawaian') === 'KONTRAK' ? 'selected' : '' }}>KONTRAK / PKWT</option>
                        <option value="HONORER" {{ old('status_kepegawaian') === 'HONORER' ? 'selected' : '' }}>HONORER</option>
                        <option value="SUKARELAWAN" {{ old('status_kepegawaian') === 'SUKARELAWAN' ? 'selected' : '' }}>SUKARELAWAN (Khidmah)</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Pendidikan Terakhir</label>
                    <select name="pendidikan_terakhir" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        <option value="">Pilih...</option>
                        <option value="SD">SD/Sederajat</option>
                        <option value="SMP">SMP/Sederajat</option>
                        <option value="SMA">SMA/Sederajat</option>
                        <option value="D1">D1</option>
                        <option value="D2">D2</option>
                        <option value="D3">D3</option>
                        <option value="S1">S1 / D4</option>
                        <option value="S2">S2</option>
                        <option value="S3">S3</option>
                    </select>
                </div>
                <x-form-input name="jurusan_pendidikan" label="Program Studi / Jurusan" placeholder="Contoh: Pendidikan Agama Islam" value="{{ old('jurusan_pendidikan') }}" />
            </div>

            <div>
                <label class="block text-sm font-medium text-surface-700 mb-1">Catatan Kepegawaian</label>
                <textarea name="catatan" rows="3" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">{{ old('catatan') }}</textarea>
            </div>

            <div class="flex items-center gap-2 mt-4">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked class="rounded border-surface-300 text-primary-600 focus:ring-primary-500 w-4 h-4">
                <label for="is_active" class="text-sm font-medium text-surface-700">Pegawai Aktif</label>
            </div>
        </div>
        
        <div class="mt-6 pt-6 border-t border-surface-100 flex justify-end gap-3">
            <a href="{{ route('admin.pegawai.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">Simpan Pegawai</button>
        </div>
    </x-card>
</form>
@endsection
