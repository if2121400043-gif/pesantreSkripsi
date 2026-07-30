{{-- Opsi Dropdown Pendidikan, Pekerjaan, Penghasilan --}}
@php
$opsiPendidikan = ['SD/MI', 'SMP/MTs', 'SMA/MA/SMK', 'D1', 'D2', 'D3', 'D4/S1', 'S2', 'S3', 'Tidak Sekolah'];
$opsiPekerjaan = ['Petani', 'Pedagang/Wiraswasta', 'PNS/ASN', 'TNI/Polri', 'Karyawan Swasta', 'Buruh', 'Nelayan', 'Guru/Dosen', 'Dokter', 'Tidak Bekerja', 'Sudah Meninggal', 'Lainnya'];
$opsiPenghasilan = ['< Rp 1.000.000', 'Rp 1.000.000 - Rp 2.000.000', 'Rp 2.000.000 - Rp 5.000.000', 'Rp 5.000.000 - Rp 10.000.000', '> Rp 10.000.000', 'Tidak Berpenghasilan'];
$opsiTinggalBersama = ['Orang Tua', 'Wali', 'Pesantren/Asrama', 'Lainnya'];
$opsiHubunganWali = ['Kakek/Nenek', 'Paman/Bibi', 'Kakak', 'Saudara Lainnya', 'Lainnya'];
$inputClass = 'w-full rounded-xl border-surface-300 dark:border-surface-700 bg-white dark:bg-surface-800 text-surface-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500/20 placeholder:text-surface-400 dark:placeholder:text-surface-500 transition-colors';
$labelClass = 'block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5';
$sectionHeaderClass = 'text-base font-bold text-surface-900 dark:text-white mb-4 pb-2 border-b border-surface-200 dark:border-surface-800 flex items-center gap-2';
@endphp

{{-- Tinggal Bersama & Alamat --}}
<div>
    <h4 class="{{ $sectionHeaderClass }}">
        <i data-lucide="home" class="w-4 h-4 text-primary-500 dark:text-primary-400"></i> {{ __('Alamat & Tempat Tinggal') }}
    </h4>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="{{ $labelClass }}">{{ __('Tinggal Bersama') }}</label>
            <select name="tinggal_bersama" id="tinggal_bersama" class="{{ $inputClass }}" onchange="toggleWaliSection()">
                <option value="">-- {{ __('Pilih') }} --</option>
                @foreach($opsiTinggalBersama as $opsi)
                    <option value="{{ $opsi }}" {{ old('tinggal_bersama') == $opsi ? 'selected' : '' }}>{{ $opsi }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $labelClass }}">{{ __('Nomor HP/WhatsApp Utama') }} <span class="text-danger-500">*</span></label>
            <input type="text" name="telepon_wali" value="{{ old('telepon_wali') }}" class="{{ $inputClass }}" placeholder="{{ __('Contoh: 08123456789') }}" required>
            <p class="text-[0.65rem] text-surface-500 mt-1 leading-tight">Wajib diisi. Jika tidak punya, gunakan No. HP keluarga terdekat yang bisa dihubungi.</p>
        </div>
        <div class="md:col-span-2">
            <label class="{{ $labelClass }}">{{ __('Alamat Lengkap') }}</label>
            <textarea name="alamat" rows="2" class="{{ $inputClass }}" placeholder="{{ __('Jalan, RT/RW, Desa, Kecamatan, Kabupaten') }}">{{ old('alamat') }}</textarea>
        </div>
    </div>
</div>

{{-- Data Ayah Kandung --}}
<div>
    <h4 class="{{ $sectionHeaderClass }}">
        <i data-lucide="user-round" class="w-4 h-4 text-primary-500 dark:text-primary-400"></i> {{ __('Data Ayah Kandung') }}
    </h4>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="{{ $labelClass }}">{{ __('Nama Lengkap Ayah') }}</label>
            <input type="text" name="nama_ayah" value="{{ old('nama_ayah') }}" class="{{ $inputClass }} uppercase">
        </div>
        <div>
            <label class="{{ $labelClass }}">{{ __('NIK Ayah') }}</label>
            <input type="text" name="nik_ayah" value="{{ old('nik_ayah') }}" class="{{ $inputClass }}" maxlength="16" placeholder="16 Digit">
        </div>
        <div>
            <label class="{{ $labelClass }}">{{ __('Tahun Lahir') }}</label>
            <input type="text" name="tahun_lahir_ayah" value="{{ old('tahun_lahir_ayah') }}" class="{{ $inputClass }}" maxlength="4" placeholder="{{ __('Contoh: 1975') }}">
        </div>
        <div>
            <label class="{{ $labelClass }}">{{ __('Pendidikan Terakhir') }}</label>
            <select name="pendidikan_ayah" class="{{ $inputClass }}">
                <option value="">-- {{ __('Pilih') }} --</option>
                @foreach($opsiPendidikan as $opsi)
                    <option value="{{ $opsi }}" {{ old('pendidikan_ayah') == $opsi ? 'selected' : '' }}>{{ $opsi }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $labelClass }}">{{ __('Pekerjaan Utama') }}</label>
            <select name="pekerjaan_ayah" class="{{ $inputClass }}">
                <option value="">-- {{ __('Pilih') }} --</option>
                @foreach($opsiPekerjaan as $opsi)
                    <option value="{{ $opsi }}" {{ old('pekerjaan_ayah') == $opsi ? 'selected' : '' }}>{{ $opsi }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $labelClass }}">{{ __('Penghasilan per Bulan') }}</label>
            <select name="penghasilan_ayah" class="{{ $inputClass }}">
                <option value="">-- {{ __('Pilih') }} --</option>
                @foreach($opsiPenghasilan as $opsi)
                    <option value="{{ $opsi }}" {{ old('penghasilan_ayah') == $opsi ? 'selected' : '' }}>{{ $opsi }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $labelClass }}">{{ __('No. HP/WhatsApp Ayah') }}</label>
            <input type="text" name="no_hp_ayah" value="{{ old('no_hp_ayah') }}" class="{{ $inputClass }}" placeholder="08xxxxxxxxxx">
        </div>
    </div>
</div>

{{-- Data Ibu Kandung --}}
<div>
    <h4 class="{{ $sectionHeaderClass }}">
        <i data-lucide="user-round" class="w-4 h-4 text-pink-500 dark:text-pink-400"></i> {{ __('Data Ibu Kandung') }}
    </h4>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="{{ $labelClass }}">{{ __('Nama Lengkap Ibu') }}</label>
            <input type="text" name="nama_ibu" value="{{ old('nama_ibu') }}" class="{{ $inputClass }} uppercase">
        </div>
        <div>
            <label class="{{ $labelClass }}">{{ __('NIK Ibu') }}</label>
            <input type="text" name="nik_ibu" value="{{ old('nik_ibu') }}" class="{{ $inputClass }}" maxlength="16" placeholder="16 Digit">
        </div>
        <div>
            <label class="{{ $labelClass }}">{{ __('Tahun Lahir') }}</label>
            <input type="text" name="tahun_lahir_ibu" value="{{ old('tahun_lahir_ibu') }}" class="{{ $inputClass }}" maxlength="4" placeholder="{{ __('Contoh: 1980') }}">
        </div>
        <div>
            <label class="{{ $labelClass }}">{{ __('Pendidikan Terakhir') }}</label>
            <select name="pendidikan_ibu" class="{{ $inputClass }}">
                <option value="">-- {{ __('Pilih') }} --</option>
                @foreach($opsiPendidikan as $opsi)
                    <option value="{{ $opsi }}" {{ old('pendidikan_ibu') == $opsi ? 'selected' : '' }}>{{ $opsi }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $labelClass }}">{{ __('Pekerjaan Utama') }}</label>
            <select name="pekerjaan_ibu" class="{{ $inputClass }}">
                <option value="">-- {{ __('Pilih') }} --</option>
                @foreach($opsiPekerjaan as $opsi)
                    <option value="{{ $opsi }}" {{ old('pekerjaan_ibu') == $opsi ? 'selected' : '' }}>{{ $opsi }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $labelClass }}">{{ __('Penghasilan per Bulan') }}</label>
            <select name="penghasilan_ibu" class="{{ $inputClass }}">
                <option value="">-- {{ __('Pilih') }} --</option>
                @foreach($opsiPenghasilan as $opsi)
                    <option value="{{ $opsi }}" {{ old('penghasilan_ibu') == $opsi ? 'selected' : '' }}>{{ $opsi }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $labelClass }}">{{ __('No. HP/WhatsApp Ibu') }}</label>
            <input type="text" name="no_hp_ibu" value="{{ old('no_hp_ibu') }}" class="{{ $inputClass }}" placeholder="08xxxxxxxxxx">
        </div>
    </div>
</div>

{{-- Data Wali (Dinamis) --}}
<div id="wali-section" style="display: {{ old('tinggal_bersama') == 'Wali' ? 'block' : 'none' }};">
    <h4 class="{{ $sectionHeaderClass }}">
        <i data-lucide="users" class="w-4 h-4 text-secondary-500 dark:text-secondary-400"></i> {{ __('Data Wali') }}
    </h4>
    <p class="text-sm text-surface-500 dark:text-surface-400 mb-4 -mt-2">{{ __('Diisi jika calon santri tinggal bersama wali (bukan orang tua kandung).') }}</p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="{{ $labelClass }}">{{ __('Nama Lengkap Wali') }}</label>
            <input type="text" name="nama_wali" value="{{ old('nama_wali') }}" class="{{ $inputClass }} uppercase">
        </div>
        <div>
            <label class="{{ $labelClass }}">{{ __('Hubungan dengan Calon Santri') }}</label>
            <select name="hubungan_wali" class="{{ $inputClass }}">
                <option value="">-- {{ __('Pilih') }} --</option>
                @foreach($opsiHubunganWali as $opsi)
                    <option value="{{ $opsi }}" {{ old('hubungan_wali') == $opsi ? 'selected' : '' }}>{{ $opsi }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $labelClass }}">{{ __('NIK Wali') }}</label>
            <input type="text" name="nik_wali" value="{{ old('nik_wali') }}" class="{{ $inputClass }}" maxlength="16" placeholder="16 Digit">
        </div>
        <div>
            <label class="{{ $labelClass }}">{{ __('Tahun Lahir') }}</label>
            <input type="text" name="tahun_lahir_wali" value="{{ old('tahun_lahir_wali') }}" class="{{ $inputClass }}" maxlength="4" placeholder="{{ __('Contoh: 1970') }}">
        </div>
        <div>
            <label class="{{ $labelClass }}">{{ __('Pendidikan Terakhir') }}</label>
            <select name="pendidikan_wali" class="{{ $inputClass }}">
                <option value="">-- {{ __('Pilih') }} --</option>
                @foreach($opsiPendidikan as $opsi)
                    <option value="{{ $opsi }}" {{ old('pendidikan_wali') == $opsi ? 'selected' : '' }}>{{ $opsi }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $labelClass }}">{{ __('Pekerjaan Utama') }}</label>
            <select name="pekerjaan_wali" class="{{ $inputClass }}">
                <option value="">-- {{ __('Pilih') }} --</option>
                @foreach($opsiPekerjaan as $opsi)
                    <option value="{{ $opsi }}" {{ old('pekerjaan_wali') == $opsi ? 'selected' : '' }}>{{ $opsi }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $labelClass }}">{{ __('Penghasilan per Bulan') }}</label>
            <select name="penghasilan_wali" class="{{ $inputClass }}">
                <option value="">-- {{ __('Pilih') }} --</option>
                @foreach($opsiPenghasilan as $opsi)
                    <option value="{{ $opsi }}" {{ old('penghasilan_wali') == $opsi ? 'selected' : '' }}>{{ $opsi }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $labelClass }}">{{ __('No. HP/WhatsApp Wali') }}</label>
            <input type="text" name="no_hp_wali" value="{{ old('no_hp_wali') }}" class="{{ $inputClass }}" placeholder="08xxxxxxxxxx">
        </div>
    </div>
</div>

<script>
function toggleWaliSection() {
    var el = document.getElementById('tinggal_bersama');
    var waliSection = document.getElementById('wali-section');
    waliSection.style.display = (el.value === 'Wali') ? 'block' : 'none';
}
</script>
