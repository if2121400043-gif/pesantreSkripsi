@extends('layouts.app')

@section('title', 'Konfigurasi & Profil Pesantren')

@section('page_header')
<div>
    <h1 class="text-2xl font-bold text-surface-900 font-heading">Konfigurasi Pesantren</h1>
    <p class="text-sm text-surface-500 mt-1">Kelola identitas dan informasi umum pondok pesantren.</p>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.konfigurasi.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            {{-- Identitas --}}
            <x-card title="Identitas Pesantren">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-surface-700 mb-1">Nama Pesantren <span class="text-danger-500">*</span></label>
                        <input type="text" name="nama" value="{{ old('nama', $pesantren->nama ?? '') }}" required class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">NSPP (Nomor Statistik Pondok Pesantren)</label>
                        <input type="text" name="nspp" value="{{ old('nspp', $pesantren->nspp ?? '') }}" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Tahun Berdiri</label>
                        <input type="number" name="tahun_berdiri" value="{{ old('tahun_berdiri', $pesantren->tahun_berdiri ?? '') }}" min="1900" max="{{ date('Y') }}" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-surface-700 mb-1">Nama Pimpinan / Kyai Pengasuh</label>
                        <input type="text" name="nama_pimpinan" value="{{ old('nama_pimpinan', $pesantren->nama_pimpinan ?? '') }}" placeholder="KH. ..." class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    </div>
                </div>
            </x-card>

            {{-- Kontak & Alamat --}}
            <x-card title="Kontak & Alamat">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-surface-700 mb-1">Alamat Lengkap</label>
                        <textarea name="alamat" rows="2" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">{{ old('alamat', $pesantren->alamat ?? '') }}</textarea>
                    </div>
                    {{-- Cascading Dropdowns for Wilayah --}}
                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 bg-surface-50 p-4 rounded-xl border border-surface-100">
                        <div class="md:col-span-2 mb-1">
                            <h4 class="font-bold text-surface-700">Wilayah Terdaftar</h4>
                            <p class="text-xs text-surface-500">Pilih wilayah untuk mempermudah pencarian (opsional). Biarkan kosong jika ingin menggunakan Alamat Lengkap saja.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Provinsi</label>
                            <select id="provinsi_id" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500" onchange="loadKabupaten(this.value)">
                                <option value="">Pilih Provinsi...</option>
                                @foreach($provinsis as $prov)
                                    <option value="{{ $prov->id }}" {{ ($pesantren->desa->kecamatan->kabupaten->provinsi_id ?? '') == $prov->id ? 'selected' : '' }}>{{ $prov->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Kabupaten/Kota</label>
                            <select id="kabupaten_id" {{ !isset($pesantren->desa) ? 'disabled' : '' }} class="w-full rounded-lg border border-surface-300 {{ !isset($pesantren->desa) ? 'bg-surface-100' : 'bg-white' }} px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500" onchange="loadKecamatan(this.value)">
                                <option value="">Pilih Kabupaten...</option>
                                @if(isset($pesantren->desa))
                                    @foreach($pesantren->desa->kecamatan->kabupaten->provinsi->kabupaten as $kab)
                                        <option value="{{ $kab->id }}" {{ $pesantren->desa->kecamatan->kabupaten_id == $kab->id ? 'selected' : '' }}>{{ $kab->nama }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Kecamatan</label>
                            <select id="kecamatan_id" {{ !isset($pesantren->desa) ? 'disabled' : '' }} class="w-full rounded-lg border border-surface-300 {{ !isset($pesantren->desa) ? 'bg-surface-100' : 'bg-white' }} px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500" onchange="loadDesa(this.value)">
                                <option value="">Pilih Kecamatan...</option>
                                @if(isset($pesantren->desa))
                                    @foreach($pesantren->desa->kecamatan->kabupaten->kecamatan as $kec)
                                        <option value="{{ $kec->id }}" {{ $pesantren->desa->kecamatan_id == $kec->id ? 'selected' : '' }}>{{ $kec->nama }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Desa/Kelurahan</label>
                            <input type="hidden" name="desa_id" value="">
                            <select name="desa_id" id="desa_id" {{ !isset($pesantren->desa) ? 'disabled' : '' }} class="w-full rounded-lg border border-surface-300 {{ !isset($pesantren->desa) ? 'bg-surface-100' : 'bg-white' }} px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                                <option value="">Pilih Desa...</option>
                                @if(isset($pesantren->desa))
                                    @foreach($pesantren->desa->kecamatan->desa as $ds)
                                        <option value="{{ $ds->id }}" {{ $pesantren->desa_id == $ds->id ? 'selected' : '' }}>{{ $ds->nama }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="md:col-span-2 mt-2 flex justify-end">
                            <button type="button" onclick="resetWilayah()" class="text-xs text-danger-600 hover:text-danger-800 flex items-center gap-1 font-medium bg-danger-50 px-2 py-1.5 rounded border border-danger-200">
                                <i data-lucide="x-circle" class="w-3.5 h-3.5"></i> Hapus / Reset Pilihan Wilayah
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Kode Pos</label>
                        <input type="text" name="kode_pos" value="{{ old('kode_pos', $pesantren->kode_pos ?? '') }}" maxlength="10" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Telepon</label>
                        <input type="text" name="telepon" value="{{ old('telepon', $pesantren->telepon ?? '') }}" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Email Resmi</label>
                        <input type="email" name="email" value="{{ old('email', $pesantren->email ?? '') }}" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Website</label>
                        <input type="text" name="website" value="{{ old('website', $pesantren->website ?? '') }}" placeholder="https://..." class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    </div>
                </div>
            </x-card>

            {{-- Visi Misi --}}
            <x-card title="Visi & Misi">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Visi</label>
                        <textarea name="visi" rows="3" placeholder="Tuliskan visi pesantren..." class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">{{ old('visi', $pesantren->visi ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Misi</label>
                        <textarea name="misi" rows="5" placeholder="Tuliskan misi pesantren (pisahkan dengan baris baru)..." class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">{{ old('misi', $pesantren->misi ?? '') }}</textarea>
                    </div>
                </div>
            </x-card>

            {{-- Sejarah --}}
            <x-card title="Sejarah Pesantren">
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Sejarah Berdiri</label>
                    <textarea name="sejarah" rows="6" placeholder="Tuliskan sejarah berdirinya pesantren..." class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">{{ old('sejarah', $pesantren->sejarah ?? '') }}</textarea>
                </div>
            </x-card>

            {{-- Lembaga Terdaftar (Read-only info) --}}
            @if($pesantren && $pesantren->lembaga->count() > 0)
            <x-card title="Lembaga Pendidikan Terdaftar">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($pesantren->lembaga as $l)
                    <div class="p-4 border border-surface-200 rounded-xl bg-surface-50/50">
                        <div class="font-bold text-surface-900">{{ $l->singkatan ?? $l->nama }}</div>
                        <div class="text-xs text-surface-500 mt-1">{{ $l->nama }}</div>
                        <div class="text-xs text-surface-500">Jenjang: {{ $l->jenjang }} · Tipe: {{ $l->tipe }}</div>
                    </div>
                    @endforeach
                </div>
                <p class="text-xs text-surface-500 mt-3">
                    <i data-lucide="info" class="w-3 h-3 inline"></i>
                    Kelola lembaga melalui menu <a href="{{ url('/admin/lembaga') }}" class="text-primary-600 hover:underline font-medium">Pesantren & Lembaga</a>.
                </p>
            </x-card>
            @endif

            <div class="flex justify-end pt-2 pb-6">
                <button type="submit" class="btn-primary flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
</div>
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

    function resetWilayah() {
        document.getElementById('provinsi_id').value = "";
        document.getElementById('desa_id').value = "";
        loadKabupaten("");
    }
</script>
@endpush
