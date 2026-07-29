@extends('layouts.app')

@section('title', 'Detail Profil: ' . $orang->nama_lengkap)

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <div class="flex items-center gap-2 text-sm text-surface-500 mb-2">
            <a href="{{ route('admin.orang.index') }}" class="hover:text-primary-600 transition-colors">Data Orang</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <span class="text-surface-900 font-medium">Profil Detail</span>
        </div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">{{ $orang->nama_lengkap }}</h1>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.orang.index') }}" class="btn-secondary flex items-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span class="hidden sm:inline">Kembali</span>
        </a>
        <a href="{{ route('admin.orang.edit', $orang) }}" class="btn-primary flex items-center gap-2">
            <i data-lucide="edit" class="w-4 h-4"></i>
            <span class="hidden sm:inline">Edit Profil</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    
    {{-- Kolom Kiri: Profil Card & NIUP --}}
    <div class="xl:col-span-1 space-y-6">
        <x-card class="text-center" :padding="false">
            <div class="bg-gradient-to-br from-primary-800 to-primary-950 h-32 rounded-t-2xl relative">
                <div class="absolute -bottom-12 left-1/2 -translate-x-1/2">
                    <div class="w-24 h-24 rounded-full border-4 border-white bg-surface-200 flex items-center justify-center text-surface-500 text-3xl font-bold shadow-sm overflow-hidden">
                        @if($orang->foto)
                            <img src="{{ asset('storage/' . $orang->foto) }}" alt="Foto" class="w-full h-full object-cover">
                        @else
                            {{ substr($orang->nama_lengkap, 0, 1) }}
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="pt-16 pb-6 px-6">
                <h2 class="text-xl font-bold text-surface-900">{{ $orang->nama_lengkap }}</h2>
                <div class="mt-2 flex flex-wrap justify-center gap-2">
                    <span class="px-3 py-1 bg-primary-50 text-primary-700 rounded-full text-xs font-bold font-mono border border-primary-100">
                        {{ $orang->niup }}
                    </span>
                    @if($orang->is_active)
                        <span class="px-3 py-1 bg-success-50 text-success-700 rounded-full text-xs font-bold border border-success-100">Aktif</span>
                    @else
                        <span class="px-3 py-1 bg-danger-50 text-danger-700 rounded-full text-xs font-bold border border-danger-100">Nonaktif</span>
                    @endif
                </div>

                <div class="mt-6 flex gap-2 justify-center">
                    @if($orang->user)
                        <x-badge variant="info" class="flex items-center gap-1"><i data-lucide="shield-check" class="w-3 h-3"></i> Memiliki Akun</x-badge>
                    @endif
                    @if($orang->pesertaDidik)
                        <x-badge variant="warning" class="flex items-center gap-1"><i data-lucide="graduation-cap" class="w-3 h-3"></i> Santri</x-badge>
                    @endif
                    @if($orang->pegawai)
                        <x-badge variant="success" class="flex items-center gap-1"><i data-lucide="briefcase" class="w-3 h-3"></i> Pegawai</x-badge>
                    @endif
                    @if(!$orang->pesertaDidik && !$orang->pegawai && !$orang->user)
                        <x-badge variant="surface" class="flex items-center gap-1"><i data-lucide="help-circle" class="w-3 h-3"></i> Belum Berelasi</x-badge>
                    @endif
                </div>
            </div>
            
            <div class="border-t border-surface-100 divide-y divide-surface-100 text-left text-sm">
                <div class="px-6 py-4 flex justify-between items-center">
                    <span class="text-surface-500">Jenis Kelamin</span>
                    <span class="font-medium text-surface-900">{{ $orang->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                </div>
                <div class="px-6 py-4 flex justify-between items-center">
                    <span class="text-surface-500">Kewarganegaraan</span>
                    <span class="font-medium text-surface-900">{{ $orang->kewarganegaraan }}</span>
                </div>
                <div class="px-6 py-4 flex justify-between items-center">
                    <span class="text-surface-500">Telepon / WA</span>
                    <span class="font-medium text-surface-900">{{ $orang->telepon ?? '-' }}</span>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Kolom Kanan: Detail Informasi --}}
    <div class="xl:col-span-2 space-y-6">
        <x-card title="Data Kependudukan & Kelahiran">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-12">
                <div>
                    <p class="text-xs text-surface-500 font-medium mb-1">NIK (KTP/KIA)</p>
                    <p class="font-medium text-surface-900">{{ $orang->nik ?? 'Belum ada data' }}</p>
                </div>
                <div>
                    <p class="text-xs text-surface-500 font-medium mb-1">Nomor Kartu Keluarga</p>
                    <p class="font-medium text-surface-900">{{ $orang->no_kk ?? 'Belum ada data' }}</p>
                </div>
                <div>
                    <p class="text-xs text-surface-500 font-medium mb-1">Tempat, Tanggal Lahir</p>
                    <p class="font-medium text-surface-900">
                        {{ $orang->tempat_lahir ?? 'Tidak diketahui' }}, 
                        {{ $orang->tanggal_lahir ? $orang->tanggal_lahir->format('d M Y') : 'Tidak diketahui' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-surface-500 font-medium mb-1">Golongan Darah</p>
                    <p class="font-medium text-surface-900 flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded bg-danger-50 text-danger-600 font-bold text-xs">{{ $orang->golongan_darah ?? '?' }}</span>
                    </p>
                </div>
                <div>
                    <p class="text-xs text-surface-500 font-medium mb-1">Anak Ke / Dari</p>
                    <p class="font-medium text-surface-900">Anak ke-{{ $orang->anak_ke ?? '?' }} dari {{ ($orang->jumlah_saudara ?? 0) + 1 }} bersaudara</p>
                </div>
            </div>
        </x-card>

        <x-card title="Alamat Lengkap">
            <div class="space-y-4">
                <div class="bg-surface-50 p-4 rounded-xl border border-surface-100 flex gap-4 items-start">
                    <div class="mt-1">
                        <i data-lucide="map-pin" class="w-5 h-5 text-primary-500"></i>
                    </div>
                    <div>
                        <p class="text-surface-900 font-medium leading-relaxed">
                            {{ $orang->alamat_lengkap ?? 'Alamat rinci tidak diisi.' }}
                            @if($orang->rt || $orang->rw)
                                RT {{ $orang->rt ?? '-' }} / RW {{ $orang->rw ?? '-' }}
                            @endif
                        </p>
                        
                        @if($orang->desa)
                            <div class="text-sm text-surface-600 mt-2 space-y-1">
                                <p>Desa/Kel: <strong>{{ $orang->desa->nama }}</strong></p>
                                <p>Kecamatan: <strong>{{ $orang->desa->kecamatan->nama }}</strong></p>
                                <p>Kabupaten/Kota: <strong>{{ $orang->desa->kecamatan->kabupaten->nama }}</strong></p>
                                <p>Provinsi: <strong>{{ $orang->desa->kecamatan->kabupaten->provinsi->nama }}</strong></p>
                            </div>
                        @else
                            <p class="text-sm text-warning-600 mt-2 flex items-center gap-1">
                                <i data-lucide="alert-triangle" class="w-4 h-4"></i> Data wilayah kependudukan belum dilampirkan.
                            </p>
                        @endif
                        
                        @if($orang->kode_pos)
                            <p class="text-sm text-surface-500 mt-2">Kode Pos: {{ $orang->kode_pos }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </x-card>
        
        {{-- Integrasi Modul Lain: Pintu masuk untuk mendaftarkan orang ini ke modul lain --}}
        <h3 class="text-lg font-bold text-surface-900 font-heading pt-4">Integrasi Layanan</h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- Integrasi Akun --}}
            <div class="border border-surface-200 rounded-xl p-5 hover:border-primary-300 transition-colors bg-white">
                <div class="w-10 h-10 rounded-lg bg-surface-100 text-surface-600 flex items-center justify-center mb-3">
                    <i data-lucide="lock" class="w-5 h-5"></i>
                </div>
                <h4 class="font-bold text-surface-900 mb-1">Akun Pengguna Sistem</h4>
                @if($orang->user)
                    <p class="text-sm text-surface-500 mb-3">Orang ini telah memiliki akses masuk sistem.</p>
                    <x-badge variant="success" class="w-fit">Username: {{ $orang->user->username }}</x-badge>
                @else
                    <p class="text-sm text-surface-500 mb-4">Buat akun untuk memberikan akses login ke aplikasi pesantren.</p>
                    <button class="text-sm font-semibold text-primary-600 hover:text-primary-700">Buat Akun Baru &rarr;</button>
                @endif
            </div>

            {{-- Integrasi Santri --}}
            <div class="border border-surface-200 rounded-xl p-5 hover:border-primary-300 transition-colors bg-white">
                <div class="w-10 h-10 rounded-lg bg-surface-100 text-surface-600 flex items-center justify-center mb-3">
                    <i data-lucide="book-open" class="w-5 h-5"></i>
                </div>
                <h4 class="font-bold text-surface-900 mb-1">Profil Akademik (Santri)</h4>
                @if($orang->pesertaDidik)
                    <p class="text-sm text-surface-500 mb-3">Telah terdaftar sebagai Peserta Didik.</p>
                    <a href="#" class="text-sm font-semibold text-primary-600 hover:text-primary-700">Lihat Profil Akademik &rarr;</a>
                @else
                    <p class="text-sm text-surface-500 mb-4">Daftarkan sebagai Santri/Siswa untuk mencatat akademik dan asrama.</p>
                    <button class="text-sm font-semibold text-primary-600 hover:text-primary-700">Daftarkan Santri &rarr;</button>
                @endif
            </div>
            
            {{-- Integrasi Pegawai --}}
            <div class="border border-surface-200 rounded-xl p-5 hover:border-primary-300 transition-colors bg-white">
                <div class="w-10 h-10 rounded-lg bg-surface-100 text-surface-600 flex items-center justify-center mb-3">
                    <i data-lucide="briefcase" class="w-5 h-5"></i>
                </div>
                <h4 class="font-bold text-surface-900 mb-1">Profil Pegawai (SDM)</h4>
                @if($orang->pegawai)
                    <p class="text-sm text-surface-500 mb-3">Telah terdaftar sebagai Pegawai/Guru.</p>
                    <a href="#" class="text-sm font-semibold text-primary-600 hover:text-primary-700">Lihat Profil Kepegawaian &rarr;</a>
                @else
                    <p class="text-sm text-surface-500 mb-4">Jadikan sebagai Ustadz, Guru, atau Staff di pesantren ini.</p>
                    <button class="text-sm font-semibold text-primary-600 hover:text-primary-700">Daftarkan Pegawai &rarr;</button>
                @endif
            </div>
            
            {{-- Integrasi Keluarga --}}
            <div class="border border-surface-200 rounded-xl p-5 hover:border-primary-300 transition-colors bg-white">
                <div class="w-10 h-10 rounded-lg bg-surface-100 text-surface-600 flex items-center justify-center mb-3">
                    <i data-lucide="users" class="w-5 h-5"></i>
                </div>
                <h4 class="font-bold text-surface-900 mb-1">Hubungan Keluarga</h4>
                <p class="text-sm text-surface-500 mb-4">Tambahkan relasi orang tua, wali, atau anak untuk data ini.</p>
                <button class="text-sm font-semibold text-primary-600 hover:text-primary-700">Kelola Relasi &rarr;</button>
            </div>
        </div>

    </div>
</div>
@endsection
