@extends('layouts.app')

@section('title', 'Verifikasi Calon Santri: ' . $calonSantri->nama_lengkap)

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <div class="flex items-center gap-2 text-sm text-surface-500 mb-2">
            <a href="{{ route('panitia-psb.calon-santri.index') }}" class="hover:text-primary-600 transition-colors">Data Pendaftar</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <span class="text-surface-900 font-medium">Verifikasi Berkas</span>
        </div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">
            {{ $calonSantri->nama_lengkap }}
        </h1>
        <p class="text-sm text-surface-500 font-mono mt-1">No. Reg: {{ $calonSantri->no_pendaftaran }}</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('panitia-psb.calon-santri.index') }}" class="btn-secondary">Kembali</a>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    
    {{-- Main Biodata --}}
    <div class="xl:col-span-2 space-y-6">
        <x-card title="Biodata Pendaftar">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8">
                <div>
                    <span class="text-xs font-bold text-surface-400 uppercase tracking-wider block mb-1">Nama Lengkap</span>
                    <span class="font-semibold text-surface-900">{{ $calonSantri->nama_lengkap }}</span>
                </div>
                <div>
                    <span class="text-xs font-bold text-surface-400 uppercase tracking-wider block mb-1">Jenis Kelamin</span>
                    <span class="font-semibold text-surface-900">{{ $calonSantri->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                </div>
                <div>
                    <span class="text-xs font-bold text-surface-400 uppercase tracking-wider block mb-1">Tempat, Tgl Lahir</span>
                    <span class="font-semibold text-surface-900">{{ $calonSantri->tempat_lahir ?? '-' }}, {{ $calonSantri->tanggal_lahir ? \Carbon\Carbon::parse($calonSantri->tanggal_lahir)->format('d M Y') : '-' }}</span>
                </div>
                <div>
                    <span class="text-xs font-bold text-surface-400 uppercase tracking-wider block mb-1">NIK / NISN</span>
                    <span class="font-semibold text-surface-900">{{ $calonSantri->nik ?? '-' }} / {{ $calonSantri->nisn ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-xs font-bold text-surface-400 uppercase tracking-wider block mb-1">Asal Sekolah</span>
                    <span class="font-semibold text-surface-900">{{ $calonSantri->asal_sekolah ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-xs font-bold text-surface-400 uppercase tracking-wider block mb-1">Tujuan Pendidikan</span>
                    <span class="font-semibold text-surface-900">{{ $calonSantri->lembagaTujuan->nama ?? 'Pondok Pesantren' }}</span>
                </div>
                <div class="md:col-span-2 border-t border-surface-100 pt-4 mt-2">
                    <span class="text-xs font-bold text-surface-400 uppercase tracking-wider block mb-1">Alamat Domisili</span>
                    <span class="font-semibold text-surface-900">{{ $calonSantri->alamat ?? '-' }}</span>
                </div>
                <div class="md:col-span-2 border-t border-surface-100 pt-4 mt-2">
                    <span class="text-xs font-bold text-surface-400 uppercase tracking-wider block mb-3">Data Ayah Kandung</span>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div><span class="text-xs text-surface-400 block mb-0.5">Nama</span><span class="font-semibold text-surface-900">{{ $calonSantri->nama_ayah ?? '-' }}</span></div>
                        <div><span class="text-xs text-surface-400 block mb-0.5">NIK</span><span class="font-semibold text-surface-900">{{ $calonSantri->nik_ayah ?? '-' }}</span></div>
                        <div><span class="text-xs text-surface-400 block mb-0.5">Tahun Lahir</span><span class="font-semibold text-surface-900">{{ $calonSantri->tahun_lahir_ayah ?? '-' }}</span></div>
                        <div><span class="text-xs text-surface-400 block mb-0.5">Pendidikan</span><span class="font-semibold text-surface-900">{{ $calonSantri->pendidikan_ayah ?? '-' }}</span></div>
                        <div><span class="text-xs text-surface-400 block mb-0.5">Pekerjaan</span><span class="font-semibold text-surface-900">{{ $calonSantri->pekerjaan_ayah ?? '-' }}</span></div>
                        <div><span class="text-xs text-surface-400 block mb-0.5">Penghasilan</span><span class="font-semibold text-surface-900">{{ $calonSantri->penghasilan_ayah ?? '-' }}</span></div>
                        <div><span class="text-xs text-surface-400 block mb-0.5">No. HP</span><span class="font-semibold text-surface-900">{{ $calonSantri->no_hp_ayah ?? '-' }}</span></div>
                    </div>
                </div>
                <div class="md:col-span-2 border-t border-surface-100 pt-4 mt-2">
                    <span class="text-xs font-bold text-surface-400 uppercase tracking-wider block mb-3">Data Ibu Kandung</span>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div><span class="text-xs text-surface-400 block mb-0.5">Nama</span><span class="font-semibold text-surface-900">{{ $calonSantri->nama_ibu ?? '-' }}</span></div>
                        <div><span class="text-xs text-surface-400 block mb-0.5">NIK</span><span class="font-semibold text-surface-900">{{ $calonSantri->nik_ibu ?? '-' }}</span></div>
                        <div><span class="text-xs text-surface-400 block mb-0.5">Tahun Lahir</span><span class="font-semibold text-surface-900">{{ $calonSantri->tahun_lahir_ibu ?? '-' }}</span></div>
                        <div><span class="text-xs text-surface-400 block mb-0.5">Pendidikan</span><span class="font-semibold text-surface-900">{{ $calonSantri->pendidikan_ibu ?? '-' }}</span></div>
                        <div><span class="text-xs text-surface-400 block mb-0.5">Pekerjaan</span><span class="font-semibold text-surface-900">{{ $calonSantri->pekerjaan_ibu ?? '-' }}</span></div>
                        <div><span class="text-xs text-surface-400 block mb-0.5">Penghasilan</span><span class="font-semibold text-surface-900">{{ $calonSantri->penghasilan_ibu ?? '-' }}</span></div>
                        <div><span class="text-xs text-surface-400 block mb-0.5">No. HP</span><span class="font-semibold text-surface-900">{{ $calonSantri->no_hp_ibu ?? '-' }}</span></div>
                    </div>
                </div>
                @if($calonSantri->nama_wali)
                <div class="md:col-span-2 border-t border-surface-100 pt-4 mt-2">
                    <span class="text-xs font-bold text-surface-400 uppercase tracking-wider block mb-3">Data Wali</span>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div><span class="text-xs text-surface-400 block mb-0.5">Nama</span><span class="font-semibold text-surface-900">{{ $calonSantri->nama_wali }}</span></div>
                        <div><span class="text-xs text-surface-400 block mb-0.5">Hubungan</span><span class="font-semibold text-surface-900">{{ $calonSantri->hubungan_wali ?? '-' }}</span></div>
                        <div><span class="text-xs text-surface-400 block mb-0.5">NIK</span><span class="font-semibold text-surface-900">{{ $calonSantri->nik_wali ?? '-' }}</span></div>
                        <div><span class="text-xs text-surface-400 block mb-0.5">Tahun Lahir</span><span class="font-semibold text-surface-900">{{ $calonSantri->tahun_lahir_wali ?? '-' }}</span></div>
                        <div><span class="text-xs text-surface-400 block mb-0.5">Pendidikan</span><span class="font-semibold text-surface-900">{{ $calonSantri->pendidikan_wali ?? '-' }}</span></div>
                        <div><span class="text-xs text-surface-400 block mb-0.5">Pekerjaan</span><span class="font-semibold text-surface-900">{{ $calonSantri->pekerjaan_wali ?? '-' }}</span></div>
                        <div><span class="text-xs text-surface-400 block mb-0.5">Penghasilan</span><span class="font-semibold text-surface-900">{{ $calonSantri->penghasilan_wali ?? '-' }}</span></div>
                        <div><span class="text-xs text-surface-400 block mb-0.5">No. HP</span><span class="font-semibold text-surface-900">{{ $calonSantri->no_hp_wali ?? '-' }}</span></div>
                    </div>
                </div>
                @endif
                <div class="md:col-span-2 border-t border-surface-100 pt-4 mt-2">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-xs font-bold text-surface-400 uppercase tracking-wider block mb-1">Tinggal Bersama</span>
                            <span class="font-semibold text-surface-900">{{ $calonSantri->tinggal_bersama ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-surface-400 uppercase tracking-wider block mb-1">No. HP/WA Utama</span>
                            <span class="font-semibold text-surface-900">{{ $calonSantri->telepon_wali ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </x-card>
        
        <x-card title="Dokumen Persyaratan">
            @if($calonSantri->dokumen->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($calonSantri->dokumen as $doc)
                        <div class="border border-surface-200 rounded-xl p-4 flex flex-col items-center justify-between group hover:border-primary-300 hover:shadow-md transition-all bg-white text-center">
                            <div class="mb-3">
                                @php
                                    $ext = pathinfo($doc->file_path, PATHINFO_EXTENSION);
                                    $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png']);
                                @endphp
                                
                                @if($isImage)
                                    <div class="w-20 h-20 bg-surface-100 rounded-lg mx-auto flex items-center justify-center overflow-hidden border border-surface-200">
                                        <img src="{{ route('frontend.psb.view-dokumen', $doc->id) }}" alt="{{ $doc->jenis_dokumen }}" class="w-full h-full object-cover">
                                    </div>
                                @else
                                    <div class="w-16 h-16 bg-danger-50 text-danger-500 rounded-full mx-auto flex items-center justify-center">
                                        <i data-lucide="file-text" class="w-8 h-8"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <h4 class="text-sm font-bold text-surface-900 capitalize mb-1">{{ str_replace('_', ' ', $doc->jenis_dokumen) }}</h4>
                            <div class="flex items-center gap-2 mb-3">
                                @if($doc->is_verified)
                                    <span class="text-[0.65rem] font-bold bg-success-100 text-success-700 px-2 py-0.5 rounded-full"><i data-lucide="check" class="w-3 h-3 inline"></i> Valid</span>
                                @else
                                    <span class="text-[0.65rem] font-bold bg-warning-100 text-warning-700 px-2 py-0.5 rounded-full">Menunggu</span>
                                @endif
                            </div>
                            
                            <div class="flex gap-2 w-full mt-auto">
                                <a href="{{ route('frontend.psb.view-dokumen', $doc->id) }}" target="_blank" class="btn-secondary w-full text-xs py-1.5 flex justify-center items-center gap-1">
                                    <i data-lucide="external-link" class="w-3 h-3"></i> Lihat
                                </a>
                                <a href="{{ route('frontend.psb.view-dokumen', $doc->id) }}" download class="btn-primary w-full text-xs py-1.5 flex justify-center items-center gap-1">
                                    <i data-lucide="download" class="w-3 h-3"></i> Unduh
                                </a>
                            </div>
                            
                            <div class="flex gap-2 w-full mt-2 pt-2 border-t border-surface-100">
                                @if($doc->is_verified)
                                    <form action="{{ route('panitia-psb.calon-santri.verifikasi-dokumen', [$calonSantri->id, $doc->id]) }}" method="POST" class="w-full">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="is_verified" value="0">
                                        <button type="submit" class="w-full text-xs py-1 px-2 border border-danger-200 text-danger-600 bg-danger-50 hover:bg-danger-100 rounded-lg transition-colors flex items-center justify-center gap-1" title="Batalkan Validasi (Tolak)" onclick="return confirm('Batalkan status valid dokumen ini?')">
                                            <i data-lucide="x" class="w-3 h-3"></i> Tolak
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('panitia-psb.calon-santri.verifikasi-dokumen', [$calonSantri->id, $doc->id]) }}" method="POST" class="w-full">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="is_verified" value="1">
                                        <button type="submit" class="w-full text-xs py-1 px-2 border border-success-200 text-success-600 bg-success-50 hover:bg-success-100 rounded-lg transition-colors flex items-center justify-center gap-1" title="Tandai Valid" onclick="return confirm('Tandai dokumen ini sebagai valid?')">
                                            <i data-lucide="check" class="w-3 h-3"></i> Setujui
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-8 text-center text-surface-500 border-2 border-dashed border-surface-200 rounded-xl bg-surface-50">
                    <i data-lucide="folder-open" class="w-10 h-10 text-surface-300 mx-auto mb-3"></i>
                    <p class="font-bold text-surface-900 mb-1">Belum Ada Dokumen</p>
                    <p class="text-sm">Calon santri belum mengunggah dokumen persyaratan secara mandiri via portal.</p>
                </div>
            @endif
        </x-card>
    </div>

    {{-- Action & Verification Box --}}
    <div class="xl:col-span-1 space-y-6">
        <x-card title="Panel Verifikasi & Keputusan" class="border-t-4 {{ $calonSantri->status === 'DITERIMA' ? 'border-t-success-500' : ($calonSantri->status === 'TIDAK_LULUS' ? 'border-t-danger-500' : ($calonSantri->status === 'HADIR_TES' ? 'border-t-warning-500' : ($calonSantri->status === 'DIBATALKAN' ? 'border-t-surface-400' : 'border-t-primary-500'))) }}">
            <div class="mb-4">
                <p class="text-xs font-bold text-surface-400 uppercase tracking-wider mb-1">Status Saat Ini</p>
                @if($calonSantri->status === 'DITERIMA')
                    <div class="bg-success-100 text-success-800 p-3 rounded-lg flex items-center gap-3 font-bold">
                        <i data-lucide="check-circle" class="w-5 h-5 text-success-600"></i> DITERIMA
                    </div>
                @elseif($calonSantri->status === 'TIDAK_LULUS')
                    <div class="bg-danger-100 text-danger-800 p-3 rounded-lg flex items-center gap-3 font-bold">
                        <i data-lucide="x-circle" class="w-5 h-5 text-danger-600"></i> TIDAK LULUS
                    </div>
                @elseif($calonSantri->status === 'HADIR_TES')
                    <div class="bg-warning-100 text-warning-800 p-3 rounded-lg flex items-center gap-3 font-bold">
                        <i data-lucide="user-check" class="w-5 h-5 text-warning-600"></i> HADIR TES / SUDAH BAYAR
                    </div>
                @elseif($calonSantri->status === 'DIBATALKAN')
                    <div class="bg-surface-200 text-surface-600 p-3 rounded-lg flex items-center gap-3 font-bold">
                        <i data-lucide="ban" class="w-5 h-5 text-surface-500"></i> DIBATALKAN / TIDAK HADIR
                    </div>
                @else
                    <div class="bg-info-100 text-info-800 p-3 rounded-lg flex items-center gap-3 font-bold">
                        <i data-lucide="inbox" class="w-5 h-5 text-info-600"></i> BARU MASUK (Pendaftaran Online)
                    </div>
                @endif
            </div>

            @if($calonSantri->status === 'DITERIMA')
                <div class="bg-primary-50 p-4 border border-primary-100 rounded-lg mb-4 text-sm">
                    <i data-lucide="info" class="w-4 h-4 inline text-primary-600 mr-1"></i>
                    Santri ini telah diterima. Sistem otomatis membuat identitas (Orang) dan memasukkannya ke database akademik pesantren.
                </div>
            @endif

            @if(!in_array($calonSantri->status, ['DITERIMA', 'DIBATALKAN']))
            <form action="{{ route('panitia-psb.calon-santri.verifikasi', $calonSantri) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Ubah Status / Keputusan</label>
                    <select name="status" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        @if($calonSantri->status === 'BARU_MASUK')
                            <option value="HADIR_TES">Hadir Tes / Sudah Bayar</option>
                            <option value="DIBATALKAN">Dibatalkan / Tidak Hadir</option>
                        @elseif($calonSantri->status === 'HADIR_TES')
                            <option value="DITERIMA">Diterima (Lulus Tes)</option>
                            <option value="TIDAK_LULUS">Tidak Lulus Tes</option>
                            <option value="DIBATALKAN">Dibatalkan</option>
                        @elseif($calonSantri->status === 'TIDAK_LULUS')
                            <option value="HADIR_TES">Kembalikan ke Hadir Tes</option>
                        @endif
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Catatan Panitia (Opsional)</label>
                    <textarea name="catatan_verifikasi" rows="3" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">{{ $calonSantri->catatan_verifikasi }}</textarea>
                </div>
                
                <button type="submit" class="w-full btn-primary py-2 flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan Keputusan
                </button>

                @if($calonSantri->status === 'HADIR_TES')
                    <p class="text-xs text-danger-500 text-center mt-2 font-semibold">
                        ⚠️ Memilih "Diterima" akan otomatis mendaftarkan anak sebagai santri aktif & membuatkan akun wali.
                    </p>
                @endif
            </form>
            @endif

            @if($calonSantri->verifikator)
                <div class="mt-4 pt-4 border-t border-surface-100 text-xs text-surface-500">
                    <p>Diverifikasi oleh: <strong class="text-surface-700">{{ $calonSantri->verifikator->orang->nama_lengkap ?? $calonSantri->verifikator->username }}</strong></p>
                    <p>Pada: {{ $calonSantri->tanggal_verifikasi ? \Carbon\Carbon::parse($calonSantri->tanggal_verifikasi)->format('d M Y, H:i') : '-' }}</p>
                </div>
            @endif
        </x-card>

        @if($calonSantri->status === 'BARU_MASUK')
            <form action="{{ route('panitia-psb.calon-santri.destroy', $calonSantri) }}" method="POST" onsubmit="return confirm('Hapus data pendaftar ini selamanya?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full btn-secondary text-danger-600 border-danger-200 hover:bg-danger-50 flex items-center justify-center gap-2">
                    <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus Data Pendaftar
                </button>
            </form>
        @endif
    </div>
</div>
@endsection
