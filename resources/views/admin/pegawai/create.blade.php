@extends('layouts.app')

@section('title', 'Daftarkan Pegawai / Guru Baru — PP Nurul Furqon')

@section('content')
<div class="space-y-6">

    {{-- Hero Header Banner --}}
    <div class="rounded-3xl p-6 md:p-8 shadow-lg relative overflow-hidden text-white" style="background: linear-gradient(135deg, #047857, #065f46) !important; color: #ffffff !important;">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 text-xs font-semibold backdrop-blur-sm border border-white/20 mb-3" style="color: #ffffff !important;">
                    <i data-lucide="user-plus" class="w-3.5 h-3.5 text-warning-300"></i>
                    Registrasi Kepegawaian Pesantren
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold font-heading" style="color: #ffffff !important;">
                    Daftarkan Pegawai / Guru Baru
                </h1>
                <p class="text-xs md:text-sm mt-1 max-w-xl" style="color: #a7f3d0 !important;">
                    Daftarkan ustadz, guru pengajar, atau staf operasional dari data identitas induk (NIUP) ke struktur SDM.
                </p>
            </div>
            
            <a href="{{ route('admin.pegawai.index') }}" class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl font-bold text-xs shadow-lg transition-all border border-white/30 hover:bg-white/20" style="background: rgba(255,255,255,0.1) !important; color: #ffffff !important;">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    {{-- Form Registrasi Pegawai --}}
    <form action="{{ route('admin.pegawai.store') }}" method="POST" class="max-w-4xl mx-auto space-y-6">
        @csrf

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

        {{-- Card 1: Pilih Identitas Induk --}}
        <div class="bg-white rounded-3xl p-6 md:p-8 border border-surface-200 shadow-sm space-y-4">
            <h3 class="font-extrabold text-surface-900 text-base flex items-center gap-2 pb-3 border-b border-surface-100">
                <i data-lucide="user-check" class="w-5 h-5 text-emerald-700"></i>
                1. Pilih Identitas Induk (Orang)
            </h3>
            <p class="text-xs text-surface-500">Pegawai harus terdaftar di sistem identitas induk (NIUP) terlebih dahulu sebelum bisa didaftarkan secara kepegawaian.</p>

            {{-- Alpine.js Searchable Select Component --}}
            <div x-data="{
                search: '',
                open: false,
                selectedId: '{{ old('orang_id', $selectedOrangId ?? '') }}',
                selectedText: '',
                items: [
                    @foreach($calonPegawai as $orang)
                        { id: '{{ $orang->id }}', text: '{{ $orang->niup }} — {{ addslashes($orang->nama_lengkap) }} ({{ $orang->jenis_kelamin }})', nama: '{{ addslashes($orang->nama_lengkap) }}', niup: '{{ $orang->niup }}' },
                    @endforeach
                ],
                init() {
                    if(this.selectedId) {
                        let found = this.items.find(i => i.id == this.selectedId);
                        if(found) this.selectedText = found.text;
                    }
                },
                get filteredItems() {
                    if (!this.search) return this.items.slice(0, 40);
                    let q = this.search.toLowerCase();
                    return this.items.filter(i => i.text.toLowerCase().includes(q)).slice(0, 40);
                },
                select(item) {
                    this.selectedId = item.id;
                    this.selectedText = item.text;
                    this.open = false;
                    this.search = '';
                }
            }" class="relative">
                <input type="hidden" name="orang_id" :value="selectedId" required>
                
                <label class="block text-xs font-extrabold text-surface-700 mb-1.5">Pilih Orang / Pengajar <span class="text-rose-500">*</span></label>
                
                <div @click="open = !open" class="w-full px-4 py-3 rounded-2xl border border-surface-300 bg-white text-xs font-semibold text-surface-900 cursor-pointer flex items-center justify-between shadow-2xs hover:border-emerald-500 transition-colors">
                    <span x-text="selectedText || 'Klik untuk mencari nama atau NIUP...'" :class="selectedText ? 'text-surface-900 font-bold' : 'text-surface-400'"></span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-surface-400"></i>
                </div>

                {{-- Dropdown Popup --}}
                <div x-show="open" @click.away="open = false" transition class="absolute z-50 left-0 right-0 mt-2 bg-white rounded-2xl border border-surface-200 shadow-2xl p-3 max-h-72 flex flex-col gap-2">
                    <div class="relative">
                        <input type="text" x-model="search" placeholder="Ketik nama atau NIUP untuk memfilter..." class="w-full pl-9 pr-3 py-2 rounded-xl border border-surface-300 text-xs font-medium focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        <i data-lucide="search" class="w-4 h-4 text-surface-400 absolute left-3 top-2.5"></i>
                    </div>
                    
                    <div class="overflow-y-auto divide-y divide-surface-100 flex-1">
                        <template x-for="item in filteredItems" :key="item.id">
                            <div @click="select(item)" class="p-2.5 hover:bg-emerald-50 rounded-xl cursor-pointer transition-colors flex items-center justify-between">
                                <div>
                                    <div class="font-bold text-surface-900 text-xs" x-text="item.nama"></div>
                                    <div class="text-[0.68rem] text-emerald-700 font-mono" x-text="item.niup"></div>
                                </div>
                                <i x-show="selectedId == item.id" data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                            </div>
                        </template>
                        <div x-show="filteredItems.length === 0" class="p-4 text-center text-xs text-surface-400">
                            Tidak ada data calon pegawai yang cocok dengan pencarian.
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-2 text-xs text-surface-500 flex items-center gap-1.5">
                <i data-lucide="info" class="w-3.5 h-3.5 text-emerald-600"></i>
                <span>Tidak menemukan nama?</span>
                <a href="{{ route('admin.orang.create') }}" class="text-emerald-700 font-extrabold hover:underline">Buat Identitas Induk Baru</a>
            </div>
        </div>

        {{-- Card 2: Data Kepegawaian (SDM) --}}
        <div class="bg-white rounded-3xl p-6 md:p-8 border border-surface-200 shadow-sm space-y-5">
            <h3 class="font-extrabold text-surface-900 text-base flex items-center gap-2 pb-3 border-b border-surface-100">
                <i data-lucide="briefcase" class="w-5 h-5 text-emerald-700"></i>
                2. Data Spesifik Kepegawaian
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-surface-700 mb-1">NIP (Nomor Induk Pegawai)</label>
                    <input type="text" name="nip" value="{{ old('nip') }}" placeholder="Kosongkan jika belum ada NIP" class="w-full px-3.5 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-medium text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-surface-700 mb-1">NUPTK (Nasional)</label>
                    <input type="text" name="nuptk" value="{{ old('nuptk') }}" placeholder="16 Digit Angka" maxlength="16" class="w-full px-3.5 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-medium text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-extrabold text-surface-700 mb-1">Jenis Pegawai Pokok <span class="text-rose-500">*</span></label>
                    <select name="jenis_pegawai" required class="w-full px-3.5 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-semibold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        <option value="" disabled selected>Pilih Jenis SDM...</option>
                        <option value="GURU" {{ old('jenis_pegawai') === 'GURU' ? 'selected' : '' }}>GURU / PENGAJAR</option>
                        <option value="USTADZ" {{ old('jenis_pegawai') === 'USTADZ' ? 'selected' : '' }}>USTADZ ASRAMA (Musyrif)</option>
                        <option value="PENGASUH" {{ old('jenis_pegawai') === 'PENGASUH' ? 'selected' : '' }}>PENGASUH / KYAI</option>
                        <option value="STAFF_ADMIN" {{ old('jenis_pegawai') === 'STAFF_ADMIN' ? 'selected' : '' }}>STAFF ADMINISTRASI</option>
                        <option value="TENAGA_KEBERSIHAN" {{ old('jenis_pegawai') === 'TENAGA_KEBERSIHAN' ? 'selected' : '' }}>TENAGA KEBERSIHAN / UMUM</option>
                        <option value="KEAMANAN" {{ old('jenis_pegawai') === 'KEAMANAN' ? 'selected' : '' }}>KEAMANAN / SATPAM</option>
                        <option value="LAINNYA" {{ old('jenis_pegawai') === 'LAINNYA' ? 'selected' : '' }}>LAINNYA</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-surface-700 mb-1">Jabatan Spesifik (Struktural)</label>
                    <input type="text" name="jabatan" value="{{ old('jabatan') }}" placeholder="Contoh: Kepala Sekolah, Waka Kurikulum" class="w-full px-3.5 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-medium text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-surface-700 mb-1">Tanggal Mulai Bekerja</label>
                    <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk', date('Y-m-d')) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-medium text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-extrabold text-surface-700 mb-1">Status Kepegawaian <span class="text-rose-500">*</span></label>
                    <select name="status_kepegawaian" required class="w-full px-3.5 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-semibold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        <option value="TETAP" {{ old('status_kepegawaian') === 'TETAP' ? 'selected' : '' }}>TETAP (GTY/PTY)</option>
                        <option value="KONTRAK" {{ old('status_kepegawaian') === 'KONTRAK' ? 'selected' : '' }}>KONTRAK / PKWT</option>
                        <option value="HONORER" {{ old('status_kepegawaian') === 'HONORER' ? 'selected' : '' }}>HONORER</option>
                        <option value="SUKARELAWAN" {{ old('status_kepegawaian') === 'SUKARELAWAN' ? 'selected' : '' }}>SUKARELAWAN (Khidmah)</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-surface-700 mb-1">Pendidikan Terakhir</label>
                    <select name="pendidikan_terakhir" class="w-full px-3.5 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-semibold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        <option value="">Pilih Pendidikan...</option>
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
                <div>
                    <label class="block text-xs font-bold text-surface-700 mb-1">Program Studi / Jurusan</label>
                    <input type="text" name="jurusan_pendidikan" value="{{ old('jurusan_pendidikan') }}" placeholder="Contoh: Pendidikan Agama Islam" class="w-full px-3.5 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-medium text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-surface-700 mb-1">Catatan Kepegawaian</label>
                <textarea name="catatan" rows="3" placeholder="Catatan tambahan mengenai tugas atau status pegawai..." class="w-full px-3.5 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-medium text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">{{ old('catatan') }}</textarea>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked class="rounded border-surface-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                <label for="is_active" class="text-xs font-extrabold text-surface-800">Pegawai Aktif Bekerja</label>
            </div>

            {{-- Form Footer Actions --}}
            <div class="pt-6 border-t border-surface-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.pegawai.index') }}" class="px-5 py-2.5 rounded-2xl bg-surface-100 text-surface-700 font-bold text-xs hover:bg-surface-200 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-2xl text-white font-extrabold text-xs shadow-lg shadow-emerald-700/20 hover:scale-102 transition-all" style="color: #ffffff !important; background-color: #047857 !important;">
                    Simpan Data Pegawai
                </button>
            </div>
        </div>
    </form>

</div>
@endsection
