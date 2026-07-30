@extends('layouts.app')

@section('title', 'Profil Akademik: ' . $pesertaDidik->orang->nama_lengkap)

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <div class="flex items-center gap-2 text-sm text-surface-500 mb-2">
            <a href="{{ route('admin.peserta-didik.index') }}" class="hover:text-primary-600 transition-colors">Data Santri</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <span class="text-surface-900 font-medium">Profil Akademik</span>
        </div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">{{ $pesertaDidik->orang->nama_lengkap }}</h1>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.peserta-didik.index') }}" class="btn-secondary flex items-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span class="hidden sm:inline">Kembali</span>
        </a>
        <a href="{{ route('admin.peserta-didik.edit', $pesertaDidik) }}" class="btn-primary flex items-center gap-2">
            <i data-lucide="edit" class="w-4 h-4"></i>
            <span class="hidden sm:inline">Edit Status / Catatan</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    {{-- Kolom Kiri: Ringkasan & Profil Utama --}}
    <div class="lg:col-span-1 space-y-6">
        <x-card :padding="false">
            <div class="p-6 text-center border-b border-surface-100 bg-surface-50/50">
                @if($pesertaDidik->orang->foto)
                    <img src="{{ asset('storage/' . $pesertaDidik->orang->foto) }}" alt="Foto {{ $pesertaDidik->orang->nama_lengkap }}" class="w-28 h-28 rounded-full object-cover mx-auto mb-4 border-4 border-white shadow-md">
                @else
                    <div class="w-24 h-24 rounded-full bg-primary-100 text-primary-700 mx-auto flex items-center justify-center text-3xl font-bold mb-4 shadow-inner border border-primary-200">
                        {{ substr($pesertaDidik->orang->nama_lengkap, 0, 1) }}
                    </div>
                @endif
                <h2 class="text-lg font-bold text-surface-900 leading-tight">{{ $pesertaDidik->orang->nama_lengkap }}</h2>
                <p class="text-surface-500 text-sm mt-1">NIUP: <span class="font-mono text-primary-600 font-medium">{{ $pesertaDidik->orang->niup }}</span></p>
                
                <div class="mt-4">
                    @if($pesertaDidik->status === 'AKTIF')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-success-100 text-success-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-success-600"></span> Santri Aktif
                        </span>
                    @elseif($pesertaDidik->status === 'LULUS')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-info-100 text-info-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-info-600"></span> Alumni / Lulus
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-danger-100 text-danger-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-danger-600"></span> {{ str_replace('_', ' ', $pesertaDidik->status) }}
                        </span>
                    @endif
                </div>
            </div>
            
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-surface-400 font-medium mb-0.5">NIS (Lokal)</p>
                        <p class="font-semibold text-surface-800 font-mono text-sm">{{ $pesertaDidik->nis ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-surface-400 font-medium mb-0.5">NISN (Pusat)</p>
                        <p class="font-semibold text-surface-800 font-mono text-sm">{{ $pesertaDidik->nisn ?? '-' }}</p>
                    </div>
                </div>
                
                <div class="border-t border-surface-100 pt-3">
                    <p class="text-xs text-surface-400 font-medium mb-0.5">Tanggal Masuk</p>
                    <p class="font-medium text-surface-800 text-sm">{{ $pesertaDidik->tanggal_masuk ? $pesertaDidik->tanggal_masuk->format('d M Y') : '-' }}</p>
                </div>

                @if($pesertaDidik->tanggal_keluar)
                <div class="border-t border-surface-100 pt-3">
                    <p class="text-xs text-danger-500 font-medium mb-0.5">Tanggal Keluar</p>
                    <p class="font-medium text-danger-700 text-sm">{{ $pesertaDidik->tanggal_keluar->format('d M Y') }}</p>
                </div>
                @endif
                
                <div class="pt-4 border-t border-surface-100 flex flex-col gap-2">
                    <a href="{{ route('admin.orang.show', $pesertaDidik->orang_id) }}" class="btn-secondary text-xs w-full py-2 flex items-center justify-center gap-1">
                        <i data-lucide="user" class="w-3.5 h-3.5"></i>
                        <span>Lihat Profil Induk Orang</span>
                    </a>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Kolom Kanan: Tab Menu & Detil Historis --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-surface-200 shadow-sm overflow-hidden">
            {{-- Navigasi Tab (Terinspirasi PEDATREN) --}}
            <div class="border-b border-surface-200 bg-surface-50/50">
                <nav class="-mb-px flex space-x-6 px-6 overflow-x-auto" aria-label="Tabs">
                    <button type="button" onclick="switchTab('biodata')" id="tab-btn-biodata"
                       class="tab-btn shrink-0 border-b-2 px-1 py-4 text-sm font-semibold border-primary-500 text-primary-600 focus:outline-none transition-all">
                        Biodata
                    </button>
                    <button type="button" onclick="switchTab('keluarga')" id="tab-btn-keluarga"
                       class="tab-btn shrink-0 border-b-2 px-1 py-4 text-sm font-semibold border-transparent text-surface-500 hover:border-surface-300 hover:text-surface-700 focus:outline-none transition-all">
                        Keluarga
                    </button>
                    <button type="button" onclick="switchTab('status')" id="tab-btn-status"
                       class="tab-btn shrink-0 border-b-2 px-1 py-4 text-sm font-semibold border-transparent text-surface-500 hover:border-surface-300 hover:text-surface-700 focus:outline-none transition-all">
                        Status Santri
                    </button>
                    <button type="button" onclick="switchTab('domisili')" id="tab-btn-domisili"
                       class="tab-btn shrink-0 border-b-2 px-1 py-4 text-sm font-semibold border-transparent text-surface-500 hover:border-surface-300 hover:text-surface-700 focus:outline-none transition-all">
                        Domisili
                    </button>
                    <button type="button" onclick="switchTab('pendidikan')" id="tab-btn-pendidikan"
                       class="tab-btn shrink-0 border-b-2 px-1 py-4 text-sm font-semibold border-transparent text-surface-500 hover:border-surface-300 hover:text-surface-700 focus:outline-none transition-all">
                        Pendidikan
                    </button>
                    <button type="button" onclick="switchTab('catatan')" id="tab-btn-catatan"
                       class="tab-btn shrink-0 border-b-2 px-1 py-4 text-sm font-semibold border-transparent text-surface-500 hover:border-surface-300 hover:text-surface-700 focus:outline-none transition-all">
                        Catatan & Progress
                    </button>
                </nav>
            </div>

            {{-- Isi Konten Tab --}}
            <div class="p-6">
                
                {{-- TAB 1: BIODATA --}}
                <div id="tab-content-biodata" class="tab-content space-y-6">
                    <h3 class="text-base font-bold text-surface-900 pb-2 border-b border-surface-100 flex items-center gap-2">
                        <i data-lucide="user" class="w-4 h-4 text-primary-500"></i>
                        <span>Biodata Santri</span>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <span class="text-xs text-surface-400 font-medium">Nomor Kartu Keluarga (KK)</span>
                            <p class="font-medium text-surface-800 text-sm mt-0.5">{{ $pesertaDidik->orang->no_kk ?? '-' }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-surface-400 font-medium">Nomor Induk Kependudukan (NIK)</span>
                            <p class="font-medium text-surface-800 text-sm mt-0.5">{{ $pesertaDidik->orang->nik ?? '-' }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-surface-400 font-medium">Nama Lengkap</span>
                            <p class="font-medium text-surface-800 text-sm mt-0.5">{{ $pesertaDidik->orang->nama_lengkap }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-surface-400 font-medium">Nama Panggilan</span>
                            <p class="font-medium text-surface-800 text-sm mt-0.5">{{ $pesertaDidik->orang->nama_panggilan ?? '-' }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-surface-400 font-medium">Tempat, Tanggal Lahir</span>
                            <p class="font-medium text-surface-800 text-sm mt-0.5">
                                {{ $pesertaDidik->orang->tempat_lahir ?? '-' }}, {{ $pesertaDidik->orang->tanggal_lahir ? $pesertaDidik->orang->tanggal_lahir->format('d F Y') : '-' }}
                            </p>
                        </div>
                        <div>
                            <span class="text-xs text-surface-400 font-medium">Jenis Kelamin</span>
                            <p class="font-medium text-surface-800 text-sm mt-0.5">{{ $pesertaDidik->orang->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-surface-400 font-medium">Anak Ke</span>
                            <p class="font-medium text-surface-800 text-sm mt-0.5">{{ $pesertaDidik->orang->anak_ke ?? '-' }} dari {{ $pesertaDidik->orang->jumlah_saudara ?? '-' }} bersaudara</p>
                        </div>
                        <div>
                            <span class="text-xs text-surface-400 font-medium">Golongan Darah</span>
                            <p class="font-medium text-surface-800 text-sm mt-0.5"><x-badge variant="info">{{ $pesertaDidik->orang->golongan_darah ?? '-' }}</x-badge></p>
                        </div>
                    </div>

                    <h3 class="text-base font-bold text-surface-900 pb-2 border-b border-surface-100 flex items-center gap-2 pt-4">
                        <i data-lucide="map-pin" class="w-4 h-4 text-primary-500"></i>
                        <span>Alamat & Kontak</span>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        <div class="md:col-span-2">
                            <span class="text-xs text-surface-400 font-medium">Alamat Lengkap</span>
                            <p class="font-medium text-surface-800 text-sm mt-0.5">{{ $pesertaDidik->orang->alamat_lengkap ?? '-' }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-surface-400 font-medium">RT / RW</span>
                            <p class="font-medium text-surface-800 text-sm mt-0.5">RT {{ $pesertaDidik->orang->rt ?? '-' }} / RW {{ $pesertaDidik->orang->rw ?? '-' }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-surface-400 font-medium">Kecamatan / Kabupaten</span>
                            <p class="font-medium text-surface-800 text-sm mt-0.5">
                                {{ $pesertaDidik->orang->desa?->kecamatan?->nama_kecamatan ?? '-' }}, {{ $pesertaDidik->orang->desa?->kecamatan?->kabupaten?->nama_kabupaten ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <span class="text-xs text-surface-400 font-medium">Provinsi</span>
                            <p class="font-medium text-surface-800 text-sm mt-0.5">{{ $pesertaDidik->orang->desa?->kecamatan?->kabupaten?->provinsi?->nama_provinsi ?? '-' }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-surface-400 font-medium">Nomor Telepon</span>
                            <p class="font-medium text-surface-800 text-sm mt-0.5">{{ $pesertaDidik->orang->telepon ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- TAB 2: KELUARGA --}}
                <div id="tab-content-keluarga" class="tab-content hidden space-y-6">
                    <h3 class="text-base font-bold text-surface-900 pb-2 border-b border-surface-100 flex items-center gap-2">
                        <i data-lucide="users" class="w-4 h-4 text-primary-500"></i>
                        <span>Hubungan Keluarga / Wali</span>
                    </h3>
                    
                    @forelse($keluarga as $hub)
                        <div class="p-4 rounded-xl border border-surface-100 bg-surface-50/50 flex flex-col md:flex-row justify-between gap-4">
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-bold text-surface-900">{{ $hub->orangTuaAtauWali->nama_lengkap }}</h4>
                                    <x-badge variant="primary" class="text-[0.65rem] px-2 py-0.5">{{ $hub->hub_keluarga }}</x-badge>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-1 text-sm text-surface-600">
                                    <p><span class="text-surface-400">NIK:</span> {{ $hub->orangTuaAtauWali->nik ?? '-' }}</p>
                                    <p><span class="text-surface-400">Telepon:</span> {{ $hub->orangTuaAtauWali->telepon ?? '-' }}</p>
                                    <p class="md:col-span-2"><span class="text-surface-400">Pekerjaan:</span> {{ $hub->pekerjaan ?? '-' }}</p>
                                    <p class="md:col-span-2"><span class="text-surface-400">Alamat:</span> {{ $hub->orangTuaAtauWali->alamat_lengkap ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 bg-surface-50 rounded-xl border border-surface-100 border-dashed">
                            <i data-lucide="users" class="w-8 h-8 text-surface-300 mx-auto mb-2"></i>
                            <p class="text-sm text-surface-500">Data wali atau hubungan keluarga belum diinput.</p>
                        </div>
                    @endif
                </div>

                {{-- TAB 3: STATUS SANTRI (AUDIT LOG HISTORIS) --}}
                <div id="tab-content-status" class="tab-content hidden space-y-6">
                    <h3 class="text-base font-bold text-surface-900 pb-2 border-b border-surface-100 flex items-center gap-2">
                        <i data-lucide="history" class="w-4 h-4 text-primary-500"></i>
                        <span>Riwayat / Perubahan Status Santri</span>
                    </h3>

                    @if($pesertaDidik->riwayatStatus->count() > 0)
                        <div class="relative border-l-2 border-surface-200 ml-4 py-2 space-y-6">
                            @foreach($pesertaDidik->riwayatStatus->sortByDesc('tanggal_perubahan') as $riwayat)
                                <div class="relative pl-6">
                                    <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-white border-2 border-primary-500"></div>
                                    <div>
                                        <div class="flex items-center flex-wrap gap-2">
                                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-surface-100 text-surface-600 border border-surface-200">
                                                {{ $riwayat->status_lama }}
                                            </span>
                                            <i data-lucide="arrow-right" class="w-3.5 h-3.5 text-surface-400"></i>
                                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-primary-100 text-primary-700">
                                                {{ $riwayat->status_baru }}
                                            </span>
                                            <span class="text-xs text-surface-400 ml-auto">{{ $riwayat->tanggal_perubahan->format('d M Y') }}</span>
                                        </div>
                                        @if($riwayat->keterangan)
                                            <p class="text-sm text-surface-600 mt-2 bg-surface-50 p-2.5 rounded-lg border border-surface-100">
                                                {{ $riwayat->keterangan }}
                                            </p>
                                        @endif
                                        <p class="text-[0.65rem] text-surface-400 mt-1">Diubah oleh: {{ $riwayat->pengubah?->name ?? 'Sistem' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 bg-surface-50 rounded-xl border border-surface-100 border-dashed">
                            <i data-lucide="history" class="w-8 h-8 text-surface-300 mx-auto mb-2"></i>
                            <p class="text-sm text-surface-500">Belum ada catatan perubahan status santri. Status awal: <strong>{{ $pesertaDidik->status }}</strong></p>
                        </div>
                    @endif
                </div>

                {{-- TAB 4: DOMISILI --}}
                <div id="tab-content-domisili" class="tab-content hidden space-y-6">
                    <h3 class="text-base font-bold text-surface-900 pb-2 border-b border-surface-100 flex items-center gap-2">
                        <i data-lucide="home" class="w-4 h-4 text-primary-500"></i>
                        <span>Riwayat Domisili & Kamar Asrama</span>
                    </h3>

                    @if($pesertaDidik->riwayatMukim->count() > 0)
                        <div class="relative border-l-2 border-surface-200 ml-4 py-2 space-y-6">
                            @foreach($pesertaDidik->riwayatMukim->sortByDesc('tahunPelajaran.tanggal_mulai') as $mukim)
                                <div class="relative pl-6">
                                    <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-white border-2 {{ $mukim->is_aktif ? 'border-success-500' : 'border-surface-300' }}"></div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-bold text-surface-900">Kamar: {{ $mukim->kamar->nama }} (Asrama {{ $mukim->kamar->asrama->nama }})</h4>
                                            @if($mukim->is_aktif)
                                                <x-badge variant="success" class="text-[0.65rem] px-1.5 py-0.5">Mukim Sekarang</x-badge>
                                            @endif
                                        </div>
                                        <p class="text-xs text-surface-500 mt-1">Tahun Ajaran: {{ $mukim->tahunPelajaran->nama }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 bg-surface-50 rounded-xl border border-surface-100 border-dashed">
                            <i data-lucide="home" class="w-8 h-8 text-surface-300 mx-auto mb-2"></i>
                            <p class="text-sm text-surface-500">Santri ini belum dialokasikan ke asrama atau kamar mana pun.</p>
                        </div>
                    @endif
                </div>

                {{-- TAB 5: PENDIDIKAN --}}
                <div id="tab-content-pendidikan" class="tab-content hidden space-y-6">
                    <h3 class="text-base font-bold text-surface-900 pb-2 border-b border-surface-100 flex items-center gap-2">
                        <i data-lucide="landmark" class="w-4 h-4 text-primary-500"></i>
                        <span>Riwayat Lembaga / Sekolah</span>
                    </h3>

                    @if($pesertaDidik->riwayatLembaga->count() > 0)
                        <div class="relative border-l-2 border-surface-200 ml-4 py-2 space-y-6">
                            @foreach($pesertaDidik->riwayatLembaga->sortByDesc('tahunPelajaran.tanggal_mulai') as $lbg)
                                <div class="relative pl-6">
                                    <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-white border-2 {{ $lbg->is_aktif ? 'border-success-500' : 'border-surface-300' }}"></div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-bold text-surface-900">{{ $lbg->lembaga->nama }} ({{ $lbg->lembaga->kode_lembaga }})</h4>
                                            @if($lbg->is_aktif)
                                                <x-badge variant="success" class="text-[0.65rem] px-1.5 py-0.5">Aktif</x-badge>
                                            @endif
                                        </div>
                                        <p class="text-xs text-surface-500 mt-1">Tahun Ajaran: {{ $lbg->tahunPelajaran->nama }} | Status: {{ $lbg->status_siswa }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6 bg-surface-50 rounded-xl border border-surface-100 border-dashed">
                            <p class="text-sm text-surface-500">Belum ada riwayat lembaga.</p>
                        </div>
                    @endif

                    <h3 class="text-base font-bold text-surface-900 pb-2 border-b border-surface-100 flex items-center gap-2 pt-4">
                        <i data-lucide="graduation-cap" class="w-4 h-4 text-primary-500"></i>
                        <span>Riwayat Rombongan Belajar (Rombel / Kelas)</span>
                    </h3>

                    @if($pesertaDidik->riwayatRombel->count() > 0)
                        <div class="relative border-l-2 border-surface-200 ml-4 py-2 space-y-6">
                            @foreach($pesertaDidik->riwayatRombel->sortByDesc('tahunPelajaran.tanggal_mulai') as $rbl)
                                <div class="relative pl-6">
                                    <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-white border-2 {{ $rbl->is_aktif ? 'border-success-500' : 'border-surface-300' }}"></div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-bold text-surface-900">Kelas: {{ $rbl->rombel->nama }} (Tingkat {{ $rbl->rombel->tingkat }})</h4>
                                            @if($rbl->is_aktif)
                                                <x-badge variant="success" class="text-[0.65rem] px-1.5 py-0.5">Kelas Sekarang</x-badge>
                                            @endif
                                        </div>
                                        <p class="text-xs text-surface-500 mt-1">Tahun Ajaran: {{ $rbl->tahunPelajaran->nama }} | Wali Kelas: {{ $rbl->rombel->waliKelas?->orang?->nama_lengkap ?? '-' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 bg-surface-50 rounded-xl border border-surface-100 border-dashed">
                            <i data-lucide="graduation-cap" class="w-8 h-8 text-surface-300 mx-auto mb-2"></i>
                            <p class="text-sm text-surface-500">Santri ini belum dimasukkan ke Rombongan Belajar (Rombel/Kelas) mana pun.</p>
                        </div>
                    @endif
                </div>

                {{-- TAB 6: CATATAN & PROGRESS --}}
                <div id="tab-content-catatan" class="tab-content hidden space-y-6">
                    <h3 class="text-base font-bold text-surface-900 pb-2 border-b border-surface-100 flex items-center gap-2">
                        <i data-lucide="clipboard-list" class="w-4 h-4 text-primary-500"></i>
                        <span>Catatan Akademik & Perkembangan</span>
                    </h3>

                    @if($pesertaDidik->catatan)
                        <div class="p-4 rounded-xl border border-surface-200 bg-surface-50/50">
                            <p class="text-surface-700 whitespace-pre-wrap text-sm leading-relaxed">{{ $pesertaDidik->catatan }}</p>
                        </div>
                    @else
                        <div class="text-center py-8 bg-surface-50 rounded-xl border border-surface-100 border-dashed">
                            <i data-lucide="clipboard" class="w-8 h-8 text-surface-300 mx-auto mb-2"></i>
                            <p class="text-sm text-surface-500">Tidak ada catatan perkembangan khusus.</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function switchTab(tabId) {
        // Hide all contents
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        // Show selected content
        document.getElementById('tab-content-' + tabId).classList.remove('hidden');

        // Reset all tab button styles
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('border-primary-500', 'text-primary-600');
            btn.classList.add('border-transparent', 'text-surface-500', 'hover:border-surface-300', 'hover:text-surface-700');
        });

        // Set active tab button style
        const activeBtn = document.getElementById('tab-btn-' + tabId);
        activeBtn.classList.remove('border-transparent', 'text-surface-500', 'hover:border-surface-300', 'hover:text-surface-700');
        activeBtn.classList.add('border-primary-500', 'text-primary-600');
    }
</script>
@endpush
