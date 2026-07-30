@extends('layouts.app')

@section('title', isset($jenisPresensi) ? 'Edit Jenis Presensi' : 'Tambah Jenis Presensi')

@section('page_header')
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-surface-900 dark:text-white">
                {{ isset($jenisPresensi) ? 'Edit Jenis Presensi' : 'Tambah Jenis Presensi' }}
            </h1>
            <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">
                {{ isset($jenisPresensi) ? 'Ubah informasi jenis presensi.' : 'Tambahkan jenis presensi baru.' }}
            </p>
        </div>
        <div>
            <a href="{{ route('admin.jenis-presensi.index') }}" class="btn bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-700 inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Kembali
            </a>
        </div>
    </div>
@endsection

@section('content')
    <x-card>
        <form action="{{ isset($jenisPresensi) ? route('admin.jenis-presensi.update', $jenisPresensi) : route('admin.jenis-presensi.store') }}" method="POST">
            @csrf
            @if(isset($jenisPresensi))
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama -->
                <div class="space-y-1">
                    <label for="nama" class="block text-sm font-medium text-surface-700 dark:text-surface-300">
                        Nama <span class="text-danger-500">*</span>
                    </label>
                    <input type="text" id="nama" name="nama" class="form-input w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-800 dark:text-white" value="{{ old('nama', $jenisPresensi->nama ?? '') }}" required placeholder="Contoh: Shalat Shubuh Jamaah">
                    @error('nama')
                        <p class="text-sm text-danger-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kode -->
                <div class="space-y-1">
                    <label for="kode" class="block text-sm font-medium text-surface-700 dark:text-surface-300">
                        Kode <span class="text-danger-500">*</span>
                    </label>
                    <input type="text" id="kode" name="kode" class="form-input w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-800 dark:text-white uppercase" value="{{ old('kode', $jenisPresensi->kode ?? '') }}" required placeholder="Contoh: SHB">
                    <p class="text-xs text-surface-500 dark:text-surface-400 mt-1">Gunakan huruf kapital tanpa spasi (misal: KBM, SHB).</p>
                    @error('kode')
                        <p class="text-sm text-danger-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div class="space-y-1 md:col-span-2">
                    <label for="deskripsi" class="block text-sm font-medium text-surface-700 dark:text-surface-300">
                        Deskripsi
                    </label>
                    <textarea id="deskripsi" name="deskripsi" rows="2" class="form-input w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-800 dark:text-white" placeholder="Opsional...">{{ old('deskripsi', $jenisPresensi->deskripsi ?? '') }}</textarea>
                    @error('deskripsi')
                        <p class="text-sm text-danger-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Target Gender -->
                <div class="space-y-1">
                    <label for="target_gender" class="block text-sm font-medium text-surface-700 dark:text-surface-300">
                        Target Gender <span class="text-danger-500">*</span>
                    </label>
                    <select id="target_gender" name="target_gender" class="form-input w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-800 dark:text-white" required>
                        <option value="SEMUA" {{ old('target_gender', $jenisPresensi->target_gender ?? '') === 'SEMUA' ? 'selected' : '' }}>Semua Gender</option>
                        <option value="PUTRA" {{ old('target_gender', $jenisPresensi->target_gender ?? '') === 'PUTRA' ? 'selected' : '' }}>Putra Saja</option>
                        <option value="PUTRI" {{ old('target_gender', $jenisPresensi->target_gender ?? '') === 'PUTRI' ? 'selected' : '' }}>Putri Saja</option>
                    </select>
                    @error('target_gender')
                        <p class="text-sm text-danger-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipe Target -->
                <div class="space-y-1">
                    <label for="tipe_target" class="block text-sm font-medium text-surface-700 dark:text-surface-300">
                        Tipe Target <span class="text-danger-500">*</span>
                    </label>
                    <select id="tipe_target" name="tipe_target" class="form-input w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-800 dark:text-white" required>
                        <option value="SEMUA_SANTRI" {{ old('tipe_target', $jenisPresensi->tipe_target ?? '') === 'SEMUA_SANTRI' ? 'selected' : '' }}>Semua Santri</option>
                        <option value="PER_ROMBEL" {{ old('tipe_target', $jenisPresensi->tipe_target ?? '') === 'PER_ROMBEL' ? 'selected' : '' }}>Per Rombel (Kelas)</option>
                        <option value="PER_ASRAMA" {{ old('tipe_target', $jenisPresensi->tipe_target ?? '') === 'PER_ASRAMA' ? 'selected' : '' }}>Per Asrama</option>
                    </select>
                    @error('tipe_target')
                        <p class="text-sm text-danger-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jam Mulai -->
                <div class="space-y-1">
                    <label for="jam_mulai" class="block text-sm font-medium text-surface-700 dark:text-surface-300">
                        Jam Mulai
                    </label>
                    <input type="time" id="jam_mulai" name="jam_mulai" class="form-input w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-800 dark:text-white" value="{{ old('jam_mulai', isset($jenisPresensi->jam_mulai) ? substr($jenisPresensi->jam_mulai, 0, 5) : '') }}">
                    @error('jam_mulai')
                        <p class="text-sm text-danger-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jam Selesai -->
                <div class="space-y-1">
                    <label for="jam_selesai" class="block text-sm font-medium text-surface-700 dark:text-surface-300">
                        Jam Selesai
                    </label>
                    <input type="time" id="jam_selesai" name="jam_selesai" class="form-input w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-800 dark:text-white" value="{{ old('jam_selesai', isset($jenisPresensi->jam_selesai) ? substr($jenisPresensi->jam_selesai, 0, 5) : '') }}">
                    @error('jam_selesai')
                        <p class="text-sm text-danger-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Urutan -->
                <div class="space-y-1">
                    <label for="urutan" class="block text-sm font-medium text-surface-700 dark:text-surface-300">
                        Urutan Tampil
                    </label>
                    <input type="number" id="urutan" name="urutan" min="1" class="form-input w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-800 dark:text-white" value="{{ old('urutan', $jenisPresensi->urutan ?? ($lastUrutan ?? 0) + 1) }}">
                    @error('urutan')
                        <p class="text-sm text-danger-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status Aktif -->
                <div class="space-y-1">
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                        Status
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $jenisPresensi->is_active ?? true) ? 'checked' : '' }}>
                        <div class="relative w-11 h-6 bg-surface-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-surface-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-surface-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-surface-600 peer-checked:bg-primary-600"></div>
                        <span class="ms-3 text-sm font-medium text-surface-700 dark:text-surface-300">Aktif</span>
                    </label>
                    @error('is_active')
                        <p class="text-sm text-danger-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="btn-primary inline-flex items-center px-4 py-2 rounded-lg">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                    Simpan
                </button>
            </div>
        </form>
    </x-card>
@endsection
