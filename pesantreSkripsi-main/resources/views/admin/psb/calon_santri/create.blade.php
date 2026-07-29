@extends('layouts.app')

@section('title', 'Pendaftaran Calon Santri Baru')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <div class="flex items-center gap-2 text-sm text-surface-500 mb-2">
            <a href="{{ route('admin.psb.calon-santri.index') }}" class="hover:text-primary-600 transition-colors">Data Pendaftar</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <span class="text-surface-900 font-medium">Daftar Manual</span>
        </div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Form Pendaftaran Offline</h1>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.psb.calon-santri.store') }}" method="POST">
        @csrf
        <div class="space-y-6">
            
            <x-card title="Data Pendaftaran">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Pilih Gelombang <span class="text-danger-500">*</span></label>
                        <select name="gelombang_id" required class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                            <option value="" disabled selected>Pilih Gelombang Aktif...</option>
                            @foreach($gelombangs as $g)
                                <option value="{{ $g->id }}">{{ $g->nama }} (T.A {{ $g->tahunPelajaran->nama }})</option>
                            @endforeach
                        </select>
                        @if($gelombangs->isEmpty())
                            <p class="text-xs text-danger-500 mt-1">Belum ada gelombang PSB yang aktif. Silakan buka gelombang terlebih dahulu.</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Tujuan Pendidikan <span class="text-danger-500">*</span></label>
                        <select name="lembaga_tujuan_id" required class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                            <option value="" disabled selected>Pilih Lembaga Tujuan...</option>
                            @foreach($lembagas as $l)
                                <option value="{{ $l->id }}">{{ $l->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </x-card>

            <x-card title="Biodata Calon Santri">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-surface-700 mb-1">Nama Lengkap Sesuai Akta <span class="text-danger-500">*</span></label>
                        <input type="text" name="nama_lengkap" required placeholder="Contoh: Ahmad Fathoni" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Jenis Kelamin <span class="text-danger-500">*</span></label>
                        <div class="flex gap-4 mt-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="jenis_kelamin" value="L" required class="text-primary-600 focus:ring-primary-500">
                                <span>Laki-laki</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="jenis_kelamin" value="P" required class="text-pink-600 focus:ring-pink-500">
                                <span>Perempuan</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Asal Sekolah</label>
                        <input type="text" name="asal_sekolah" placeholder="Contoh: SDN 1 Bandung Raya" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" placeholder="Contoh: Bandung" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">NIK (Nomor Induk Kependudukan)</label>
                        <input type="text" name="nik" placeholder="16 Digit" maxlength="16" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">NISN (Nasional)</label>
                        <input type="text" name="nisn" placeholder="10 Digit" maxlength="10" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-surface-700 mb-1">Alamat Domisili</label>
                        <textarea name="alamat" rows="2" placeholder="Jalan, RT/RW, Desa, Kecamatan" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500"></textarea>
                    </div>
                </div>
            </x-card>

            <x-card title="Data Ayah Kandung">
                @php
                $opsiPendidikan = ['SD/MI', 'SMP/MTs', 'SMA/MA/SMK', 'D1', 'D2', 'D3', 'D4/S1', 'S2', 'S3', 'Tidak Sekolah'];
                $opsiPekerjaan = ['Petani', 'Pedagang/Wiraswasta', 'PNS/ASN', 'TNI/Polri', 'Karyawan Swasta', 'Buruh', 'Nelayan', 'Guru/Dosen', 'Dokter', 'Tidak Bekerja', 'Sudah Meninggal', 'Lainnya'];
                $opsiPenghasilan = ['< Rp 1.000.000', 'Rp 1.000.000 - Rp 2.000.000', 'Rp 2.000.000 - Rp 5.000.000', 'Rp 5.000.000 - Rp 10.000.000', '> Rp 10.000.000', 'Tidak Berpenghasilan'];
                $inputCls = 'w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500';
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Nama Ayah</label>
                        <input type="text" name="nama_ayah" class="{{ $inputCls }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">NIK Ayah</label>
                        <input type="text" name="nik_ayah" maxlength="16" class="{{ $inputCls }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Tahun Lahir</label>
                        <input type="text" name="tahun_lahir_ayah" maxlength="4" placeholder="1975" class="{{ $inputCls }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Pendidikan</label>
                        <select name="pendidikan_ayah" class="{{ $inputCls }}"><option value="">-- Pilih --</option>@foreach($opsiPendidikan as $o)<option value="{{ $o }}">{{ $o }}</option>@endforeach</select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Pekerjaan</label>
                        <select name="pekerjaan_ayah" class="{{ $inputCls }}"><option value="">-- Pilih --</option>@foreach($opsiPekerjaan as $o)<option value="{{ $o }}">{{ $o }}</option>@endforeach</select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Penghasilan</label>
                        <select name="penghasilan_ayah" class="{{ $inputCls }}"><option value="">-- Pilih --</option>@foreach($opsiPenghasilan as $o)<option value="{{ $o }}">{{ $o }}</option>@endforeach</select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">No. HP Ayah</label>
                        <input type="text" name="no_hp_ayah" class="{{ $inputCls }}">
                    </div>
                </div>
            </x-card>

            <x-card title="Data Ibu Kandung">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Nama Ibu</label>
                        <input type="text" name="nama_ibu" class="{{ $inputCls }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">NIK Ibu</label>
                        <input type="text" name="nik_ibu" maxlength="16" class="{{ $inputCls }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Tahun Lahir</label>
                        <input type="text" name="tahun_lahir_ibu" maxlength="4" placeholder="1980" class="{{ $inputCls }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Pendidikan</label>
                        <select name="pendidikan_ibu" class="{{ $inputCls }}"><option value="">-- Pilih --</option>@foreach($opsiPendidikan as $o)<option value="{{ $o }}">{{ $o }}</option>@endforeach</select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Pekerjaan</label>
                        <select name="pekerjaan_ibu" class="{{ $inputCls }}"><option value="">-- Pilih --</option>@foreach($opsiPekerjaan as $o)<option value="{{ $o }}">{{ $o }}</option>@endforeach</select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Penghasilan</label>
                        <select name="penghasilan_ibu" class="{{ $inputCls }}"><option value="">-- Pilih --</option>@foreach($opsiPenghasilan as $o)<option value="{{ $o }}">{{ $o }}</option>@endforeach</select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">No. HP Ibu</label>
                        <input type="text" name="no_hp_ibu" class="{{ $inputCls }}">
                    </div>
                </div>
            </x-card>

            <x-card title="Kontak & Wali">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">No. HP/WhatsApp Aktif <span class="text-danger-500">*</span></label>
                        <input type="text" name="telepon_wali" required placeholder="08123456789" class="{{ $inputCls }} md:w-full">
                        <p class="text-xs text-surface-500 mt-1">Nomor ini akan dihubungi oleh panitia untuk konfirmasi.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Tinggal Bersama</label>
                        <select name="tinggal_bersama" id="admin_tinggal_bersama" class="{{ $inputCls }}" onchange="toggleAdminWali()">
                            <option value="">-- Pilih --</option>
                            <option value="Orang Tua">Orang Tua</option>
                            <option value="Wali">Wali</option>
                            <option value="Pesantren/Asrama">Pesantren/Asrama</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                </div>
            </x-card>

            <div id="admin-wali-section" style="display: none;">
                <x-card title="Data Wali">
                    <p class="text-xs text-surface-500 mb-4">Diisi jika calon santri tinggal bersama wali (bukan orang tua kandung).</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Nama Wali</label>
                            <input type="text" name="nama_wali" class="{{ $inputCls }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Hubungan dengan Santri</label>
                            <select name="hubungan_wali" class="{{ $inputCls }}">
                                <option value="">-- Pilih --</option>
                                <option value="Kakek/Nenek">Kakek/Nenek</option>
                                <option value="Paman/Bibi">Paman/Bibi</option>
                                <option value="Kakak">Kakak</option>
                                <option value="Saudara Lainnya">Saudara Lainnya</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">NIK Wali</label>
                            <input type="text" name="nik_wali" maxlength="16" class="{{ $inputCls }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Tahun Lahir</label>
                            <input type="text" name="tahun_lahir_wali" maxlength="4" placeholder="1970" class="{{ $inputCls }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Pendidikan</label>
                            <select name="pendidikan_wali" class="{{ $inputCls }}"><option value="">-- Pilih --</option>@foreach($opsiPendidikan as $o)<option value="{{ $o }}">{{ $o }}</option>@endforeach</select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Pekerjaan</label>
                            <select name="pekerjaan_wali" class="{{ $inputCls }}"><option value="">-- Pilih --</option>@foreach($opsiPekerjaan as $o)<option value="{{ $o }}">{{ $o }}</option>@endforeach</select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Penghasilan</label>
                            <select name="penghasilan_wali" class="{{ $inputCls }}"><option value="">-- Pilih --</option>@foreach($opsiPenghasilan as $o)<option value="{{ $o }}">{{ $o }}</option>@endforeach</select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">No. HP Wali</label>
                            <input type="text" name="no_hp_wali" class="{{ $inputCls }}">
                        </div>
                    </div>
                </x-card>
            </div>

            <script>
            function toggleAdminWali() {
                var el = document.getElementById('admin_tinggal_bersama');
                var section = document.getElementById('admin-wali-section');
                section.style.display = (el.value === 'Wali') ? 'block' : 'none';
            }
            </script>

            <div class="flex justify-end gap-3 pt-4 border-t border-surface-200">
                <a href="{{ route('admin.psb.calon-santri.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary" {{ $gelombangs->isEmpty() ? 'disabled' : '' }}>
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan Pendaftaran
                </button>
            </div>
            
        </div>
    </form>
</div>
@endsection
