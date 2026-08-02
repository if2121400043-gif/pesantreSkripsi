@extends('layouts.app')

@section('title', 'Registrasi Identitas Induk (NIUP) — PP Nurul Furqon')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <div class="flex items-center gap-2 text-xs text-surface-500 mb-1.5">
            <a href="{{ route('admin.orang.index') }}" class="hover:text-emerald-700 transition-colors font-medium">Data Induk Identitas</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            <span class="text-surface-900 font-bold">Registrasi NIUP Baru</span>
        </div>
        <h1 class="text-2xl font-extrabold text-surface-900 font-heading">Formulir Registrasi Identitas Induk</h1>
    </div>
    <a href="{{ route('admin.orang.index') }}" class="btn-secondary flex items-center gap-2 text-xs font-bold py-2 px-4 rounded-xl">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Kembali</span>
    </a>
</div>
@endsection

@section('content')
<form action="{{ route('admin.orang.store') }}" method="POST" enctype="multipart/form-data" class="max-w-6xl mx-auto space-y-6">
    @csrf

    {{-- Notifikasi Error Global --}}
    @if($errors->any())
        <div class="bg-rose-50 text-rose-800 p-4 rounded-2xl border border-rose-200 shadow-sm">
            <div class="flex gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0 mt-0.5"></i>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Kolom Kiri: Informasi Pribadi & Alamat Domisili --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Card 1: Informasi Pribadi --}}
            <div class="bg-white rounded-3xl p-6 border border-surface-200 shadow-sm relative overflow-hidden">
                <div class="flex items-center gap-3 mb-5 pb-3 border-b border-surface-100">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold shrink-0 border border-emerald-100">
                        <i data-lucide="user" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-surface-900">Informasi Pribadi Dasar</h3>
                        <p class="text-xs text-surface-500">Data identitas utama sesuai Akte Kelahiran / KTP kependudukan.</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-surface-700 mb-1">Nama Lengkap (Sesuai Ijazah/Akte) <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required placeholder="Ketik nama lengkap..." class="w-full rounded-xl border border-surface-300 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-surface-700 mb-1">Nama Panggilan</label>
                            <input type="text" name="nama_panggilan" value="{{ old('nama_panggilan') }}" placeholder="Nama panggilan..." class="w-full rounded-xl border border-surface-300 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-surface-700 mb-1">NIK (KTP/KIA)</label>
                            <input type="text" name="nik" value="{{ old('nik') }}" maxlength="16" placeholder="16 Digit NIK..." class="w-full rounded-xl border border-surface-300 px-3.5 py-2.5 text-sm font-mono focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-surface-700 mb-1">Nomor Kartu Keluarga</label>
                            <input type="text" name="no_kk" value="{{ old('no_kk') }}" maxlength="16" placeholder="16 Digit No. KK..." class="w-full rounded-xl border border-surface-300 px-3.5 py-2.5 text-sm font-mono focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-surface-700 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                            <select name="jenis_kelamin" required class="w-full rounded-xl border border-surface-300 bg-white px-3.5 py-2.5 text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">
                                <option value="" disabled {{ old('jenis_kelamin') ? '' : 'selected' }}>Pilih...</option>
                                <option value="L" {{ old('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki (Putra)</option>
                                <option value="P" {{ old('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan (Putri)</option>
                            </select>
                        </div>
                    </div>

                    {{-- Reusable Component TTL --}}
                    <div class="pt-2">
                        <x-ttl-input 
                            tempatName="tempat_lahir" 
                            tanggalName="tanggal_lahir" 
                            :tempatValue="old('tempat_lahir')" 
                            :tanggalValue="old('tanggal_lahir')" 
                        />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                        <div>
                            <label class="block text-xs font-bold text-surface-700 mb-1">Golongan Darah</label>
                            <select name="golongan_darah" class="w-full rounded-xl border border-surface-300 bg-white px-3.5 py-2.5 text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">
                                <option value="" {{ !old('golongan_darah') ? 'selected' : '' }}>Tidak Tahu</option>
                                <option value="A" {{ old('golongan_darah') === 'A' ? 'selected' : '' }}>A</option>
                                <option value="B" {{ old('golongan_darah') === 'B' ? 'selected' : '' }}>B</option>
                                <option value="AB" {{ old('golongan_darah') === 'AB' ? 'selected' : '' }}>AB</option>
                                <option value="O" {{ old('golongan_darah') === 'O' ? 'selected' : '' }}>O</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-surface-700 mb-1">Kewarganegaraan <span class="text-rose-500">*</span></label>
                            <input type="text" name="kewarganegaraan" value="{{ old('kewarganegaraan', 'Indonesia') }}" required class="w-full rounded-xl border border-surface-300 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-bold text-surface-700 mb-1">Anak Ke</label>
                                <input type="number" name="anak_ke" min="1" value="{{ old('anak_ke') }}" placeholder="1" class="w-full rounded-xl border border-surface-300 px-3.5 py-2.5 text-sm text-center focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-surface-700 mb-1">Saudara</label>
                                <input type="number" name="jumlah_saudara" min="0" value="{{ old('jumlah_saudara') }}" placeholder="0" class="w-full rounded-xl border border-surface-300 px-3.5 py-2.5 text-sm text-center focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Alamat Domisili & Kontak --}}
            <div class="bg-white rounded-3xl p-6 border border-surface-200 shadow-sm relative overflow-hidden">
                <div class="flex items-center gap-3 mb-5 pb-3 border-b border-surface-100">
                    <div class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-700 flex items-center justify-center font-bold shrink-0 border border-teal-100">
                        <i data-lucide="map-pin" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-surface-900">Alamat Domisili & Kontak</h3>
                        <p class="text-xs text-surface-500">Rincian lokasi tempat tinggal & nomor telepon aktif.</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-surface-700 mb-1">Jalan / Dusun / Rincian Alamat Rumah</label>
                        <textarea name="alamat_lengkap" rows="2" placeholder="Nama jalan, RT/RW, nomor rumah..." class="w-full rounded-xl border border-surface-300 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">{{ old('alamat_lengkap') }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-surface-700 mb-1">RT</label>
                            <input type="text" name="rt" value="{{ old('rt') }}" placeholder="001" class="w-full rounded-xl border border-surface-300 px-3.5 py-2.5 text-sm font-mono text-center focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-surface-700 mb-1">RW</label>
                            <input type="text" name="rw" value="{{ old('rw') }}" placeholder="002" class="w-full rounded-xl border border-surface-300 px-3.5 py-2.5 text-sm font-mono text-center focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">
                        </div>

                        <div class="col-span-2">
                            <label class="block text-xs font-bold text-surface-700 mb-1">Kode Pos</label>
                            <input type="text" name="kode_pos" value="{{ old('kode_pos') }}" placeholder="67123" class="w-full rounded-xl border border-surface-300 px-3.5 py-2.5 text-sm font-mono focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">
                        </div>
                    </div>

                    {{-- Cascading Dropdowns for Wilayah --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-surface-50 p-4 rounded-2xl border border-surface-200">
                        <div>
                            <label class="block text-xs font-bold text-surface-700 mb-1">1. Provinsi</label>
                            <select id="provinsi_id" class="w-full rounded-xl border border-surface-300 bg-white px-3.5 py-2.5 text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600" onchange="loadKabupaten(this.value)">
                                <option value="">Pilih Provinsi...</option>
                                @foreach($provinsis as $prov)
                                    <option value="{{ $prov->id }}">{{ $prov->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-surface-700 mb-1">2. Kabupaten/Kota</label>
                            <select id="kabupaten_id" disabled class="w-full rounded-xl border border-surface-300 bg-surface-100 px-3.5 py-2.5 text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600" onchange="loadKecamatan(this.value)">
                                <option value="">Pilih Kabupaten...</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-surface-700 mb-1">3. Kecamatan</label>
                            <select id="kecamatan_id" disabled class="w-full rounded-xl border border-surface-300 bg-surface-100 px-3.5 py-2.5 text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600" onchange="loadDesa(this.value)">
                                <option value="">Pilih Kecamatan...</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-surface-700 mb-1">4. Desa/Kelurahan</label>
                            <select name="desa_id" id="desa_id" disabled class="w-full rounded-xl border border-surface-300 bg-surface-100 px-3.5 py-2.5 text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">
                                <option value="">Pilih Desa...</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-surface-700 mb-1">Nomor WhatsApp / HP Aktif</label>
                            <input type="tel" name="telepon" value="{{ old('telepon') }}" placeholder="08xxxxxxxxxx" class="w-full rounded-xl border border-surface-300 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-surface-700 mb-1">Alamat Email (Opsional)</label>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="email@domain.com" class="w-full rounded-xl border border-surface-300 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Kolom Kanan: Pengaturan Data NIUP & Submit --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-3xl p-6 border border-surface-200 shadow-sm sticky top-20">
                <h3 class="text-base font-extrabold text-surface-900 mb-4 pb-3 border-b border-surface-100 flex items-center gap-2">
                    <i data-lucide="settings" class="w-5 h-5 text-emerald-700"></i>
                    Pengaturan Identitas
                </h3>
                
                {{-- NIUP Banner --}}
                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl mb-5 text-center">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto mb-2 font-bold">
                        <i data-lucide="fingerprint" class="w-6 h-6"></i>
                    </div>
                    <h4 class="font-extrabold text-emerald-900 text-sm">Auto Generate NIUP</h4>
                    <p class="text-[0.68rem] text-emerald-700 mt-1 leading-relaxed">
                        Nomor Induk Unik Pesantren (NIUP) akan dibuatkan otomatis oleh sistem setelah disimpan.
                    </p>
                </div>

                {{-- Status Keaktifan --}}
                <div class="flex items-center gap-3 p-3.5 bg-surface-50 border border-surface-200 rounded-2xl mb-6">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="w-5 h-5 rounded border-surface-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                    <label for="is_active" class="text-xs font-bold text-surface-900 cursor-pointer select-none">
                        Status Orang Ini Aktif (Hidup)
                    </label>
                </div>

                {{-- Submit Button --}}
                <button type="submit" class="btn-primary w-full justify-center flex items-center gap-2 py-3 px-6 rounded-xl font-bold text-xs shadow-md shadow-emerald-700/20" style="color: #ffffff !important; background-color: #047857 !important;">
                    <i data-lucide="save" class="w-4 h-4 text-white" style="color: #ffffff !important;"></i>
                    <span style="color: #ffffff !important;">Simpan & Generate NIUP</span>
                </button>
                
                <p class="text-[0.65rem] text-center text-surface-450 mt-3 leading-relaxed">
                    Pastikan rincian nama dan tanggal lahir sudah sesuai dengan akte/KTP kependudukan resmi.
                </p>
            </div>
        </div>

    </div>
</form>
@endsection

@push('scripts')
<script>
    // Cascading Dropdown Logic
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
