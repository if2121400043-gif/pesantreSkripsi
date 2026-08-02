@extends('layouts.app')

@section('title', 'Registrasi Identitas Induk (NIUP) — PP Nurul Furqon')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gradient-to-r from-emerald-800 via-emerald-700 to-teal-800 p-6 md:p-8 rounded-3xl text-white shadow-xl relative overflow-hidden mb-6">
    <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
    <div class="relative z-10">
        <div class="flex items-center gap-2 text-emerald-200 text-xs font-semibold mb-2">
            <a href="{{ route('admin.orang.index') }}" class="hover:text-white transition-colors flex items-center gap-1">
                <i data-lucide="users" class="w-3.5 h-3.5"></i>
                <span>Data Induk Identitas</span>
            </a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-emerald-300/60"></i>
            <span class="text-white font-bold">Registrasi Identitas Baru</span>
        </div>
        <h1 class="text-2xl md:text-3xl font-extrabold font-heading text-white tracking-tight flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center border border-white/20 shadow-inner">
                <i data-lucide="user-plus" class="w-6 h-6 text-emerald-200"></i>
            </div>
            <span>Formulir Registrasi Identitas (NIUP)</span>
        </h1>
        <p class="text-xs md:text-sm text-emerald-100/90 mt-1 max-w-2xl leading-relaxed">
            Pendaftaran identitas induk untuk santri, ustadz, pegawai, atau orang tua wali dengan auto-generate Nomor Induk Unik Pesantren.
        </p>
    </div>
    <div class="relative z-10 shrink-0">
        <a href="{{ route('admin.orang.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs backdrop-blur-md border border-white/20 transition-all shadow-sm">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Daftar</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<form action="{{ route('admin.orang.store') }}" method="POST" enctype="multipart/form-data" class="max-w-6xl mx-auto space-y-6">
    @csrf

    {{-- Notifikasi Error Global --}}
    @if($errors->any())
        <div class="bg-rose-50 text-rose-800 p-4 md:p-5 rounded-2xl border border-rose-200 shadow-sm animate-fade-in">
            <div class="flex gap-3.5">
                <div class="w-9 h-9 rounded-xl bg-rose-500/10 text-rose-600 flex items-center justify-center shrink-0">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-xs text-rose-900 mb-1">Terdapat kesalahan pengisian pada formulir:</h3>
                    <ul class="list-disc pl-5 space-y-1 text-xs font-medium text-rose-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">
        
        {{-- Kolom Kiri: Informasi Pribadi & Alamat Domisili --}}
        <div class="lg:col-span-2 space-y-6 md:space-y-8">
            
            {{-- Card 1: Informasi Pribadi --}}
            <div class="bg-white rounded-3xl p-6 md:p-8 border border-surface-200 shadow-sm relative overflow-hidden hover:border-emerald-300 transition-all">
                <div class="flex items-center gap-3.5 mb-6 pb-4 border-b border-surface-100">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold shrink-0 border border-emerald-100">
                        <i data-lucide="user" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-extrabold text-surface-900 font-heading">Informasi Pribadi Dasar</h3>
                        <p class="text-xs text-surface-500">Data identitas utama sesuai Akte Kelahiran/KTP resmi.</p>
                    </div>
                </div>

                <div class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-surface-800 mb-1.5">Nama Lengkap (Sesuai Ijazah/Akte) <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required placeholder="Contoh: Muhammad Ahmad Fauzi" class="w-full rounded-2xl border border-surface-300 bg-white px-4 py-3 text-sm font-semibold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-surface-800 mb-1.5">Nama Panggilan</label>
                            <input type="text" name="nama_panggilan" value="{{ old('nama_panggilan') }}" placeholder="Contoh: Ahmad" class="w-full rounded-2xl border border-surface-300 bg-white px-4 py-3 text-sm font-semibold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-surface-800 mb-1.5">NIK (KTP/KIA)</label>
                            <input type="text" name="nik" value="{{ old('nik') }}" maxlength="16" placeholder="16 Digit NIK..." class="w-full rounded-2xl border border-surface-300 bg-white px-4 py-3 text-sm font-mono text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-surface-800 mb-1.5">Nomor Kartu Keluarga (KK)</label>
                            <input type="text" name="no_kk" value="{{ old('no_kk') }}" maxlength="16" placeholder="16 Digit No. KK..." class="w-full rounded-2xl border border-surface-300 bg-white px-4 py-3 text-sm font-mono text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-surface-800 mb-1.5">Jenis Kelamin <span class="text-rose-500">*</span></label>
                            <select name="jenis_kelamin" required class="w-full rounded-2xl border border-surface-300 bg-white px-4 py-3 text-xs font-extrabold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all">
                                <option value="" disabled {{ old('jenis_kelamin') ? '' : 'selected' }}>Pilih Jenis Kelamin...</option>
                                <option value="L" {{ old('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki (Putra)</option>
                                <option value="P" {{ old('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan (Putri)</option>
                            </select>
                        </div>
                    </div>

                    {{-- Component Reusable TTL --}}
                    <div class="pt-2 border-t border-surface-100">
                        <x-ttl-input 
                            tempatName="tempat_lahir" 
                            tanggalName="tanggal_lahir" 
                            :tempatValue="old('tempat_lahir')" 
                            :tanggalValue="old('tanggal_lahir')" 
                        />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 pt-2 border-t border-surface-100">
                        <div>
                            <label class="block text-xs font-bold text-surface-800 mb-1.5">Golongan Darah</label>
                            <select name="golongan_darah" class="w-full rounded-2xl border border-surface-300 bg-white px-4 py-3 text-xs font-extrabold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all">
                                <option value="" {{ !old('golongan_darah') ? 'selected' : '' }}>Tidak Tahu</option>
                                <option value="A" {{ old('golongan_darah') === 'A' ? 'selected' : '' }}>Golongan A</option>
                                <option value="B" {{ old('golongan_darah') === 'B' ? 'selected' : '' }}>Golongan B</option>
                                <option value="AB" {{ old('golongan_darah') === 'AB' ? 'selected' : '' }}>Golongan AB</option>
                                <option value="O" {{ old('golongan_darah') === 'O' ? 'selected' : '' }}>Golongan O</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-surface-800 mb-1.5">Kewarganegaraan <span class="text-rose-500">*</span></label>
                            <input type="text" name="kewarganegaraan" value="{{ old('kewarganegaraan', 'Indonesia') }}" required class="w-full rounded-2xl border border-surface-300 bg-white px-4 py-3 text-sm font-semibold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all">
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-bold text-surface-800 mb-1.5">Anak Ke</label>
                                <input type="number" name="anak_ke" min="1" value="{{ old('anak_ke') }}" placeholder="1" class="w-full rounded-2xl border border-surface-300 bg-white px-3 py-3 text-sm font-semibold text-surface-900 text-center focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-surface-800 mb-1.5">Saudara</label>
                                <input type="number" name="jumlah_saudara" min="0" value="{{ old('jumlah_saudara') }}" placeholder="0" class="w-full rounded-2xl border border-surface-300 bg-white px-3 py-3 text-sm font-semibold text-surface-900 text-center focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Alamat Domisili & Kontak --}}
            <div class="bg-white rounded-3xl p-6 md:p-8 border border-surface-200 shadow-sm relative overflow-hidden hover:border-teal-300 transition-all">
                <div class="flex items-center gap-3.5 mb-6 pb-4 border-b border-surface-100">
                    <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-700 flex items-center justify-center font-bold shrink-0 border border-teal-100">
                        <i data-lucide="map-pin" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-extrabold text-surface-900 font-heading">Alamat Domisili & Kontak</h3>
                        <p class="text-xs text-surface-500">Rincian tempat tinggal lengkap & nomor kontak aktif.</p>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-surface-800 mb-1.5">Jalan / Dusun / Rincian Alamat Rumah</label>
                        <textarea name="alamat_lengkap" rows="2" placeholder="Nama jalan, RT/RW, gang, nomor rumah..." class="w-full rounded-2xl border border-surface-300 bg-white px-4 py-3 text-sm font-semibold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all leading-relaxed">{{ old('alamat_lengkap') }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-surface-800 mb-1.5">RT</label>
                            <input type="text" name="rt" value="{{ old('rt') }}" placeholder="001" class="w-full rounded-2xl border border-surface-300 bg-white px-3.5 py-3 text-sm font-mono text-center text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-surface-800 mb-1.5">RW</label>
                            <input type="text" name="rw" value="{{ old('rw') }}" placeholder="002" class="w-full rounded-2xl border border-surface-300 bg-white px-3.5 py-3 text-sm font-mono text-center text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all">
                        </div>

                        <div class="col-span-2">
                            <label class="block text-xs font-bold text-surface-800 mb-1.5">Kode Pos</label>
                            <input type="text" name="kode_pos" value="{{ old('kode_pos') }}" placeholder="67123" class="w-full rounded-2xl border border-surface-300 bg-white px-4 py-3 text-sm font-mono text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all">
                        </div>
                    </div>

                    {{-- Cascading Dropdowns for Wilayah Indonesia --}}
                    <div class="space-y-3 bg-surface-50/80 p-5 rounded-3xl border border-surface-200">
                        <div class="flex items-center gap-2 mb-1">
                            <i data-lucide="globe" class="w-4 h-4 text-emerald-700"></i>
                            <span class="text-xs font-extrabold text-surface-900 uppercase tracking-wider">Wilayah Administrasi Indonesia</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-surface-700 mb-1">1. Provinsi</label>
                                <select id="provinsi_id" class="w-full rounded-2xl border border-surface-300 bg-white px-4 py-3 text-xs font-extrabold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all" onchange="loadKabupaten(this.value)">
                                    <option value="">Pilih Provinsi...</option>
                                    @foreach($provinsis as $prov)
                                        <option value="{{ $prov->id }}">{{ $prov->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-surface-700 mb-1">2. Kabupaten / Kota</label>
                                <select id="kabupaten_id" disabled class="w-full rounded-2xl border border-surface-300 bg-surface-100 px-4 py-3 text-xs font-extrabold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all" onchange="loadKecamatan(this.value)">
                                    <option value="">Pilih Kabupaten...</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-surface-700 mb-1">3. Kecamatan</label>
                                <select id="kecamatan_id" disabled class="w-full rounded-2xl border border-surface-300 bg-surface-100 px-4 py-3 text-xs font-extrabold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all" onchange="loadDesa(this.value)">
                                    <option value="">Pilih Kecamatan...</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-surface-700 mb-1">4. Desa / Kelurahan</label>
                                <select name="desa_id" id="desa_id" disabled class="w-full rounded-2xl border border-surface-300 bg-surface-100 px-4 py-3 text-xs font-extrabold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all">
                                    <option value="">Pilih Desa...</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2">
                        <div>
                            <label class="block text-xs font-bold text-surface-800 mb-1.5">Nomor WhatsApp / HP Aktif</label>
                            <div class="relative">
                                <input type="tel" name="telepon" value="{{ old('telepon') }}" placeholder="08xxxxxxxxxx" class="w-full rounded-2xl border border-surface-300 bg-white px-4 py-3 text-sm font-semibold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-surface-800 mb-1.5">Alamat Email (Opsional)</label>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="email@domain.com" class="w-full rounded-2xl border border-surface-300 bg-white px-4 py-3 text-sm font-semibold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Kolom Kanan: Panel Pengaturan NIUP & Tombol Simpan --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-3xl p-6 md:p-7 border border-surface-200 shadow-xl sticky top-24 relative overflow-hidden">
                
                <h3 class="text-base font-extrabold text-surface-900 mb-5 pb-3 border-b border-surface-100 flex items-center gap-2.5 font-heading">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold">
                        <i data-lucide="settings-2" class="w-4 h-4"></i>
                    </div>
                    <span>Pengaturan Identitas</span>
                </h3>
                
                {{-- NIUP Banner Illustration --}}
                <div class="p-5 bg-gradient-to-br from-emerald-50 via-teal-50 to-emerald-100/50 border border-emerald-200/80 rounded-2xl mb-6 text-center relative overflow-hidden">
                    <div class="w-14 h-14 rounded-2xl bg-white text-emerald-700 flex items-center justify-center mx-auto mb-3 font-bold shadow-md border border-emerald-100">
                        <i data-lucide="fingerprint" class="w-7 h-7"></i>
                    </div>
                    <h4 class="font-extrabold text-emerald-950 text-sm font-heading">Auto Generate NIUP</h4>
                    <p class="text-[0.7rem] text-emerald-700 mt-1.5 leading-relaxed font-medium">
                        Nomor Induk Unik Pesantren (NIUP) akan diterbitkan secara otomatis oleh sistem begitu data disimpan.
                    </p>
                </div>

                {{-- Status Keaktifan --}}
                <div class="flex items-center gap-3 p-4 bg-surface-50 border border-surface-200 rounded-2xl mb-6 hover:bg-surface-100 transition-colors">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="w-5 h-5 rounded-lg border-surface-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                    <label for="is_active" class="text-xs font-bold text-surface-900 cursor-pointer select-none">
                        Status Identitas Aktif (Hidup)
                    </label>
                </div>

                {{-- Action Submit Button --}}
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 py-3.5 px-6 rounded-2xl font-extrabold text-xs text-white transition-all shadow-lg hover:shadow-xl active:scale-98 cursor-pointer" style="background: linear-gradient(135deg, #047857, #065f46) !important; color: #ffffff !important;">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-white" style="color: #ffffff !important;"></i>
                    <span style="color: #ffffff !important;">Simpan & Terbitkan NIUP</span>
                </button>
                
                <p class="text-[0.68rem] text-center text-surface-450 mt-4 leading-relaxed">
                    Pastikan rincian nama dan tanggal lahir sudah sesuai dengan Akte / KTP resmi.
                </p>
            </div>
        </div>

    </div>
</form>
@endsection

@push('scripts')
<script>
    // Cascading Dropdown Logic for Wilayah Indonesia
    async function fetchRegionData(url, targetSelectId, defaultOptionText) {
        const targetSelect = document.getElementById(targetSelectId);
        targetSelect.innerHTML = `<option value="">Memuat...</option>`;
        targetSelect.disabled = true;
        
        try {
            const response = await fetch(url);
            const data = await response.json();
            
            targetSelect.innerHTML = `<option value="">${defaultOptionText}</option>`;
            data.forEach(item => {
                targetSelect.innerHTML += `<option value="${item.id}">${item.nama}</option>`;
            });
            
            targetSelect.disabled = false;
            targetSelect.classList.remove('bg-surface-100');
            targetSelect.classList.add('bg-white');
        } catch (error) {
            console.error('Error fetching region data:', error);
            targetSelect.innerHTML = `<option value="">Gagal memuat data</option>`;
        }
    }

    function loadKabupaten(provinsiId) {
        document.getElementById('kecamatan_id').innerHTML = '<option value="">Pilih Kecamatan...</option>';
        document.getElementById('kecamatan_id').disabled = true;
        document.getElementById('kecamatan_id').classList.add('bg-surface-100');
        document.getElementById('desa_id').innerHTML = '<option value="">Pilih Desa...</option>';
        document.getElementById('desa_id').disabled = true;
        document.getElementById('desa_id').classList.add('bg-surface-100');

        if (!provinsiId) {
            document.getElementById('kabupaten_id').innerHTML = '<option value="">Pilih Kabupaten...</option>';
            document.getElementById('kabupaten_id').disabled = true;
            document.getElementById('kabupaten_id').classList.add('bg-surface-100');
            return;
        }

        fetchRegionData(`/admin/api/provinsi/${provinsiId}/kabupaten`, 'kabupaten_id', 'Pilih Kabupaten...');
    }

    function loadKecamatan(kabupatenId) {
        document.getElementById('desa_id').innerHTML = '<option value="">Pilih Desa...</option>';
        document.getElementById('desa_id').disabled = true;
        document.getElementById('desa_id').classList.add('bg-surface-100');

        if (!kabupatenId) {
            document.getElementById('kecamatan_id').innerHTML = '<option value="">Pilih Kecamatan...</option>';
            document.getElementById('kecamatan_id').disabled = true;
            document.getElementById('kecamatan_id').classList.add('bg-surface-100');
            return;
        }

        fetchRegionData(`/admin/api/kabupaten/${kabupatenId}/kecamatan`, 'kecamatan_id', 'Pilih Kecamatan...');
    }

    function loadDesa(kecamatanId) {
        if (!kecamatanId) {
            document.getElementById('desa_id').innerHTML = '<option value="">Pilih Desa...</option>';
            document.getElementById('desa_id').disabled = true;
            document.getElementById('desa_id').classList.add('bg-surface-100');
            return;
        }

        fetchRegionData(`/admin/api/kecamatan/${kecamatanId}/desa`, 'desa_id', 'Pilih Desa/Kelurahan...');
    }
</script>
@endpush
