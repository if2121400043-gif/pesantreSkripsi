@extends('layouts.app')

@section('title', 'Edit Identitas: ' . $orang->nama_lengkap)

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <div class="flex items-center gap-2 text-sm text-surface-500 mb-2">
            <a href="{{ route('admin.orang.index') }}" class="hover:text-primary-600 transition-colors">Data Orang</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <span class="text-surface-900 font-medium">{{ $orang->niup }}</span>
        </div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Edit Identitas: {{ $orang->nama_lengkap }}</h1>
    </div>
    <a href="{{ route('admin.orang.index') }}" class="btn-secondary flex items-center gap-2">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Kembali</span>
    </a>
</div>
@endsection

@section('content')
<form action="{{ route('admin.orang.update', $orang) }}" method="POST" enctype="multipart/form-data" class="max-w-6xl mx-auto space-y-6">
    @csrf
    @method('PUT')

    @if($errors->any())
        <div class="bg-danger-50 text-danger-700 p-4 rounded-xl border border-danger-200">
            <div class="flex gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0 mt-0.5"></i>
                <div>
                    <h3 class="font-semibold mb-1">Terdapat kesalahan pengisian:</h3>
                    <ul class="list-disc pl-5 space-y-1 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Kolom Kiri: Info Dasar --}}
        <div class="lg:col-span-2 space-y-6">
            <x-card title="Informasi Pribadi Dasar">
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-form-input name="nama_lengkap" label="Nama Lengkap (Sesuai Ijazah/Akte)" required value="{{ old('nama_lengkap', $orang->nama_lengkap) }}" />
                        <x-form-input name="nama_panggilan" label="Nama Panggilan" value="{{ old('nama_panggilan', $orang->nama_panggilan) }}" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <x-form-input name="nik" label="NIK (KTP/KIA)" value="{{ old('nik', $orang->nik) }}" maxlength="16" />
                        <x-form-input name="no_kk" label="Nomor Kartu Keluarga" value="{{ old('no_kk', $orang->no_kk) }}" maxlength="16" />
                        
                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Jenis Kelamin <span class="text-danger-500">*</span></label>
                            <select name="jenis_kelamin" required class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                                <option value="L" {{ old('jenis_kelamin', $orang->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $orang->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <x-ttl-input 
                            tempatName="tempat_lahir" 
                            tanggalName="tanggal_lahir" 
                            :tempatValue="old('tempat_lahir', $orang->tempat_lahir)" 
                            :tanggalValue="old('tanggal_lahir', $orang->tanggal_lahir?->format('Y-m-d'))" 
                        />
                        
                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Golongan Darah</label>
                            <select name="golongan_darah" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                                <option value="" {{ !old('golongan_darah', $orang->golongan_darah) ? 'selected' : '' }}>Tidak Tahu</option>
                                <option value="A" {{ old('golongan_darah', $orang->golongan_darah) === 'A' ? 'selected' : '' }}>A</option>
                                <option value="B" {{ old('golongan_darah', $orang->golongan_darah) === 'B' ? 'selected' : '' }}>B</option>
                                <option value="AB" {{ old('golongan_darah', $orang->golongan_darah) === 'AB' ? 'selected' : '' }}>AB</option>
                                <option value="O" {{ old('golongan_darah', $orang->golongan_darah) === 'O' ? 'selected' : '' }}>O</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <x-form-input name="kewarganegaraan" label="Kewarganegaraan" required value="{{ old('kewarganegaraan', $orang->kewarganegaraan) }}" />
                        <x-form-input type="number" name="anak_ke" label="Anak Ke" min="1" value="{{ old('anak_ke', $orang->anak_ke) }}" />
                        <x-form-input type="number" name="jumlah_saudara" label="Jumlah Saudara" min="0" value="{{ old('jumlah_saudara', $orang->jumlah_saudara) }}" />
                    </div>
                </div>
            </x-card>

            <x-card title="Alamat & Kontak">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Jalan / Dusun / Rincian Alamat</label>
                        <textarea name="alamat_lengkap" rows="2" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">{{ old('alamat_lengkap', $orang->alamat_lengkap) }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <x-form-input name="rt" label="RT" placeholder="001" value="{{ old('rt', $orang->rt) }}" />
                        <x-form-input name="rw" label="RW" placeholder="002" value="{{ old('rw', $orang->rw) }}" />
                        <div class="col-span-2">
                            <x-form-input name="kode_pos" label="Kode Pos" value="{{ old('kode_pos', $orang->kode_pos) }}" />
                        </div>
                    </div>

                    {{-- Data Wilayah Terpilih (Readonly for simplicity in edit view, full cascade requires pre-loading) --}}
                    <div class="bg-surface-50 p-4 rounded-xl border border-surface-100">
                        <p class="text-sm text-surface-500 mb-2">Wilayah Tercatat (Ubah hanya jika pindah domisili besar)</p>
                        <div class="font-medium text-surface-900">
                            @if($orang->desa)
                                Desa {{ $orang->desa->nama }}, Kec. {{ $orang->desa->kecamatan->nama }}, 
                                Kab. {{ $orang->desa->kecamatan->kabupaten->nama }}, Prov. {{ $orang->desa->kecamatan->kabupaten->provinsi->nama }}
                            @else
                                <span class="text-warning-600">Wilayah belum diatur.</span>
                            @endif
                        </div>
                        <input type="hidden" name="desa_id" value="{{ old('desa_id', $orang->desa_id) }}">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-form-input type="tel" name="telepon" label="Nomor WhatsApp / HP Aktif" value="{{ old('telepon', $orang->telepon) }}" />
                        <x-form-input type="email" name="email" label="Alamat Email (Opsional)" value="{{ old('email', $orang->email) }}" />
                    </div>
                </div>
            </x-card>
        </div>

        {{-- Kolom Kanan: Status & Simpan --}}
        <div class="lg:col-span-1 space-y-6">
            <x-card title="Data Sistem">
                
                <div class="p-4 bg-surface-50 border border-surface-200 rounded-xl mb-4 text-center">
                    <p class="text-xs text-surface-500 mb-1">Nomor Induk Unik Pesantren</p>
                    <h4 class="text-xl font-bold font-mono text-primary-900">{{ $orang->niup }}</h4>
                </div>

                <div class="flex items-center gap-3 p-3 bg-surface-50 border border-surface-200 rounded-lg">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $orang->is_active) ? 'checked' : '' }} class="w-5 h-5 rounded border-surface-300 text-primary-600 focus:ring-primary-500">
                    <label for="is_active" class="text-sm font-medium text-surface-900 cursor-pointer">
                        Status Orang Ini Aktif (Hidup)
                    </label>
                </div>

                <div class="mt-6 pt-6 border-t border-surface-100 space-y-3">
                    <button type="submit" class="btn-primary w-full justify-center flex items-center gap-2 py-3">
                        <i data-lucide="save" class="w-5 h-5"></i>
                        <span class="font-bold">Simpan Perubahan</span>
                    </button>
                    
                    <button type="button" onclick="if(confirm('Apakah Anda yakin ingin menghapus data identitas ini?')) { document.getElementById('delete-orang-form').submit(); }" class="w-full justify-center flex items-center gap-2 py-2.5 px-4 bg-danger-50 hover:bg-danger-100 text-danger-700 font-semibold rounded-lg transition-colors border border-danger-200">
                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                        <span>Hapus Data</span>
                    </button>
                </div>
            </x-card>
        </div>

    </div>
</form>

<form id="delete-orang-form" action="{{ route('admin.orang.destroy', $orang) }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endsection
