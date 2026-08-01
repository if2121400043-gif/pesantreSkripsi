@extends('layouts.app')

@section('title', 'Edit SDM: ' . $pegawai->orang->nama_lengkap)

@section('content')
<div class="space-y-6">

    {{-- Hero Header Banner --}}
    <div class="rounded-3xl p-6 md:p-8 shadow-lg relative overflow-hidden text-white" style="background: linear-gradient(135deg, #047857, #065f46) !important; color: #ffffff !important;">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 text-xs font-semibold backdrop-blur-sm border border-white/20 mb-3" style="color: #ffffff !important;">
                    <i data-lucide="edit-3" class="w-3.5 h-3.5 text-warning-300"></i>
                    Pembaruan Data SDM
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold font-heading" style="color: #ffffff !important;">
                    Edit SDM: {{ $pegawai->orang->nama_lengkap }}
                </h1>
                <p class="text-xs md:text-sm mt-1 max-w-xl" style="color: #a7f3d0 !important;">
                    Perbarui rincian tugas, jenis kepegawaian, NIP, NUPTK, serta status keaktifan pegawai.
                </p>
            </div>
            
            <a href="{{ route('admin.pegawai.index') }}" class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl font-bold text-xs shadow-lg transition-all border border-white/30 hover:bg-white/20" style="background: rgba(255,255,255,0.1) !important; color: #ffffff !important;">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    {{-- Form Edit Pegawai --}}
    <form action="{{ route('admin.pegawai.update', $pegawai) }}" method="POST" class="max-w-4xl mx-auto space-y-6">
        @csrf
        @method('PUT')

        @if($errors->any())
            <div class="bg-rose-50 text-rose-800 p-4 rounded-2xl border border-rose-200 text-xs shadow-sm">
                <div class="flex items-start gap-2.5">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0 mt-0.5"></i>
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Card 1: Identitas Induk Terhubung --}}
        <div class="bg-white rounded-3xl p-6 md:p-8 border border-surface-200 shadow-sm space-y-4">
            <h3 class="font-extrabold text-surface-900 text-base flex items-center gap-2 pb-3 border-b border-surface-100">
                <i data-lucide="user-check" class="w-5 h-5 text-emerald-700"></i>
                Identitas Induk Terhubung
            </h3>
            
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 rounded-2xl bg-surface-50 border border-surface-200">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-800 font-extrabold text-lg flex items-center justify-center shrink-0 border border-emerald-200">
                        {{ substr($pegawai->orang->nama_lengkap, 0, 1) }}
                    </div>
                    <div>
                        <h4 class="font-extrabold text-surface-900 text-sm">{{ $pegawai->orang->nama_lengkap }}</h4>
                        <div class="text-xs text-surface-500 font-mono mt-0.5">
                            NIUP: <strong class="text-surface-800">{{ $pegawai->orang->niup }}</strong> • {{ $pegawai->orang->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                        </div>
                    </div>
                </div>
                <a href="{{ route('admin.orang.edit', $pegawai->orang_id) }}" class="px-3.5 py-2 rounded-xl bg-white border border-surface-300 text-xs font-bold text-surface-700 hover:bg-surface-100 transition-colors shrink-0" target="_blank">
                    Edit Biodata Induk
                </a>
            </div>
        </div>

        {{-- Card 2: Data Kepegawaian (SDM) --}}
        <div class="bg-white rounded-3xl p-6 md:p-8 border border-surface-200 shadow-sm space-y-5">
            <h3 class="font-extrabold text-surface-900 text-base flex items-center gap-2 pb-3 border-b border-surface-100">
                <i data-lucide="briefcase" class="w-5 h-5 text-emerald-700"></i>
                Data Spesifik Kepegawaian
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-surface-700 mb-1">NIP (Nomor Induk Pegawai)</label>
                    <input type="text" name="nip" value="{{ old('nip', $pegawai->nip) }}" placeholder="Kosongkan jika belum ada NIP" class="w-full px-3.5 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-medium text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-surface-700 mb-1">NUPTK (Nasional)</label>
                    <input type="text" name="nuptk" value="{{ old('nuptk', $pegawai->nuptk) }}" placeholder="16 Digit Angka" maxlength="16" class="w-full px-3.5 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-medium text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-extrabold text-surface-700 mb-1">Jenis Pegawai Pokok <span class="text-rose-500">*</span></label>
                    <select name="jenis_pegawai" required class="w-full px-3.5 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-semibold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        <option value="GURU" {{ old('jenis_pegawai', $pegawai->jenis_pegawai) === 'GURU' ? 'selected' : '' }}>GURU / PENGAJAR</option>
                        <option value="USTADZ" {{ old('jenis_pegawai', $pegawai->jenis_pegawai) === 'USTADZ' ? 'selected' : '' }}>USTADZ ASRAMA (Musyrif)</option>
                        <option value="PENGASUH" {{ old('jenis_pegawai', $pegawai->jenis_pegawai) === 'PENGASUH' ? 'selected' : '' }}>PENGASUH / KYAI</option>
                        <option value="STAFF_ADMIN" {{ old('jenis_pegawai', $pegawai->jenis_pegawai) === 'STAFF_ADMIN' ? 'selected' : '' }}>STAFF ADMINISTRASI</option>
                        <option value="TENAGA_KEBERSIHAN" {{ old('jenis_pegawai', $pegawai->jenis_pegawai) === 'TENAGA_KEBERSIHAN' ? 'selected' : '' }}>TENAGA KEBERSIHAN / UMUM</option>
                        <option value="KEAMANAN" {{ old('jenis_pegawai', $pegawai->jenis_pegawai) === 'KEAMANAN' ? 'selected' : '' }}>KEAMANAN / SATPAM</option>
                        <option value="LAINNYA" {{ old('jenis_pegawai', $pegawai->jenis_pegawai) === 'LAINNYA' ? 'selected' : '' }}>LAINNYA</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-surface-700 mb-1">Jabatan Spesifik (Struktural)</label>
                    <input type="text" name="jabatan" value="{{ old('jabatan', $pegawai->jabatan) }}" placeholder="Contoh: Kepala Sekolah, Waka Kurikulum" class="w-full px-3.5 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-medium text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-surface-700 mb-1">Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk', $pegawai->tanggal_masuk?->format('Y-m-d')) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-medium text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-surface-700 mb-1">Tanggal Keluar</label>
                    <input type="date" name="tanggal_keluar" value="{{ old('tanggal_keluar', $pegawai->tanggal_keluar?->format('Y-m-d')) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-medium text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-extrabold text-surface-700 mb-1">Status Kepegawaian <span class="text-rose-500">*</span></label>
                    <select name="status_kepegawaian" required class="w-full px-3.5 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-semibold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        <option value="TETAP" {{ old('status_kepegawaian', $pegawai->status_kepegawaian) === 'TETAP' ? 'selected' : '' }}>TETAP (GTY/PTY)</option>
                        <option value="KONTRAK" {{ old('status_kepegawaian', $pegawai->status_kepegawaian) === 'KONTRAK' ? 'selected' : '' }}>KONTRAK / PKWT</option>
                        <option value="HONORER" {{ old('status_kepegawaian', $pegawai->status_kepegawaian) === 'HONORER' ? 'selected' : '' }}>HONORER</option>
                        <option value="SUKARELAWAN" {{ old('status_kepegawaian', $pegawai->status_kepegawaian) === 'SUKARELAWAN' ? 'selected' : '' }}>SUKARELAWAN (Khidmah)</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-surface-700 mb-1">Pendidikan Terakhir</label>
                    <select name="pendidikan_terakhir" class="w-full px-3.5 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-semibold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        <option value="">Pilih Pendidikan...</option>
                        @foreach(['SD', 'SMP', 'SMA', 'D1', 'D2', 'D3', 'S1', 'S2', 'S3'] as $pend)
                            <option value="{{ $pend }}" {{ old('pendidikan_terakhir', $pegawai->pendidikan_terakhir) === $pend ? 'selected' : '' }}>{{ $pend }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-surface-700 mb-1">Program Studi / Jurusan</label>
                    <input type="text" name="jurusan_pendidikan" value="{{ old('jurusan_pendidikan', $pegawai->jurusan_pendidikan) }}" placeholder="Contoh: Pendidikan Agama Islam" class="w-full px-3.5 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-medium text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-surface-700 mb-1">Catatan Kepegawaian</label>
                <textarea name="catatan" rows="3" placeholder="Catatan mengenai tugas atau status pegawai..." class="w-full px-3.5 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-medium text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">{{ old('catatan', $pegawai->catatan) }}</textarea>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $pegawai->is_active) ? 'checked' : '' }} class="rounded border-surface-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                <label for="is_active" class="text-xs font-extrabold text-surface-800">Pegawai Aktif Bekerja</label>
            </div>

            {{-- Form Footer Actions --}}
            <div class="pt-6 border-t border-surface-100 flex items-center justify-between gap-3">
                <button type="button" class="px-4 py-2.5 rounded-2xl bg-rose-50 text-rose-700 font-bold text-xs hover:bg-rose-100 transition-colors border border-rose-200" onclick="if(confirm('Apakah Anda yakin ingin menghapus data pegawai ini?')) document.getElementById('delete-form').submit()">
                    Hapus Data Pegawai
                </button>
                
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.pegawai.index') }}" class="px-5 py-2.5 rounded-2xl bg-surface-100 text-surface-700 font-bold text-xs hover:bg-surface-200 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-2xl text-white font-extrabold text-xs shadow-lg shadow-emerald-700/20 hover:scale-102 transition-all" style="color: #ffffff !important; background-color: #047857 !important;">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </form>

    <form id="delete-form" action="{{ route('admin.pegawai.destroy', $pegawai) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

</div>
@endsection
