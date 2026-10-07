@extends('frontend.layouts.app', ['hideNavbar' => true, 'hideFooter' => true])

@section('title', __('Formulir Pendaftaran Santri Baru'))

@section('content')
<div class="min-h-screen bg-[#FAFAFA] pt-8 pb-12 font-sans">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8 relative">
            <a href="{{ route('frontend.psb') }}" class="text-gray-500 hover:text-gray-900 text-sm font-medium flex items-center">
                Kembali ke halaman PSB
            </a>
            <h1 class="text-xl font-bold text-gray-900 absolute left-1/2 transform -translate-x-1/2 hidden md:block">Formulir Pendaftaran Santri Baru</h1>
            <div class="hidden md:block w-32"></div> {{-- Spacer --}}
        </div>
        <h1 class="text-xl font-bold text-gray-900 mb-6 text-center md:hidden">Formulir Pendaftaran Santri Baru</h1>

        {{-- Validation Notices (if any) --}}
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-600 px-6 py-4 rounded-lg text-sm">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-600 px-6 py-4 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Main Form Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-10 md:p-14">
            
            <form action="{{ route('frontend.psb.store') }}" method="POST" id="psbForm" novalidate>
                @csrf

                {{-- SECTION 1: BIODATA --}}
                <div class="mb-10">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Biodata Pesertadidik</h2>
                    <p class="text-gray-500 text-sm mb-6">Lengkapi data pendaftaran untuk membuat akun / melanjutkan proses pendaftaran.</p>
                    <hr class="border-gray-100 mb-8">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-8">
                        {{-- KK --}}
                        <div>
                            <label class="block text-sm text-gray-700 mb-2">Nomor Kartu Keluarga (KK) <span class="text-red-500">*</span></label>
                            <input type="text" name="kk" class="w-full px-4 py-3 rounded-md border border-gray-200 focus:ring-1 focus:ring-gray-300 focus:border-gray-300 text-sm placeholder-gray-400" placeholder="Nomor kartu keluarga...." value="{{ old('kk') }}" required>
                        </div>
                        
                        {{-- NIK --}}
                        <div>
                            <label class="block text-sm text-gray-700 mb-2">Nomor Induk Keluarga (NIK) <span class="text-red-500">*</span></label>
                            <input type="text" name="nik" class="w-full px-4 py-3 rounded-md border border-gray-200 focus:ring-1 focus:ring-gray-300 focus:border-gray-300 text-sm placeholder-gray-400" placeholder="Nomor induk keluarga...." value="{{ old('nik') }}" required>
                        </div>

                        {{-- Nama Lengkap --}}
                        <div>
                            <label class="block text-sm text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_lengkap" class="w-full px-4 py-3 rounded-md border border-gray-200 focus:ring-1 focus:ring-gray-300 focus:border-gray-300 text-sm placeholder-gray-400" placeholder="Nama lengkap...." value="{{ old('nama_lengkap') }}" required>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            {{-- Jenis Kelamin --}}
                            <div>
                                <label class="block text-sm text-gray-700 mb-2">Jenis Kelamin <span class="text-red-500">*</span></label>
                                <div class="space-y-3 mt-3">
                                    <label class="flex items-center text-sm text-gray-500 cursor-pointer">
                                        <input type="radio" name="jenis_kelamin" value="L" class="w-3.5 h-3.5 text-gray-500 border-gray-300 focus:ring-gray-500 mr-3" {{ old('jenis_kelamin') == 'L' ? 'checked' : '' }} required>
                                        Laki-laki
                                    </label>
                                    <label class="flex items-center text-sm text-gray-500 cursor-pointer">
                                        <input type="radio" name="jenis_kelamin" value="P" class="w-3.5 h-3.5 text-gray-500 border-gray-300 focus:ring-gray-500 mr-3" {{ old('jenis_kelamin') == 'P' ? 'checked' : '' }}>
                                        Perempuan
                                    </label>
                                </div>
                            </div>

                            {{-- Tempat Lahir --}}
                            <div>
                                <label class="block text-sm text-gray-700 mb-2">Tempat Lahir <span class="text-red-500">*</span></label>
                                <input type="text" name="tempat_lahir" class="w-full px-4 py-3 rounded-md border border-gray-200 focus:ring-1 focus:ring-gray-300 focus:border-gray-300 text-sm placeholder-gray-400" placeholder="Tempat lahir...." value="{{ old('tempat_lahir') }}" required>
                            </div>
                        </div>

                        {{-- Tanggal Lahir --}}
                        <div>
                            <label class="block text-sm text-gray-700 mb-2">Tanggal Lahir <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-3 gap-3">
                                <div class="relative">
                                    <select id="tgl_hari" class="w-full px-4 py-3 rounded-md border border-gray-200 appearance-none text-sm text-gray-500 focus:ring-1 focus:ring-gray-300 focus:border-gray-300 bg-white" required>
                                        <option value="">Tanggal</option>
                                        @for($i=1; $i<=31; $i++)
                                            <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                                <div class="relative">
                                    <select id="tgl_bulan" class="w-full px-4 py-3 rounded-md border border-gray-200 appearance-none text-sm text-gray-500 focus:ring-1 focus:ring-gray-300 focus:border-gray-300 bg-white" required>
                                        <option value="">Bulan</option>
                                        @php
                                            $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                        @endphp
                                        @foreach($bulan as $key => $b)
                                            <option value="{{ str_pad($key+1, 2, '0', STR_PAD_LEFT) }}">{{ $b }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                                <div class="relative">
                                    <select id="tgl_tahun" class="w-full px-4 py-3 rounded-md border border-gray-200 appearance-none text-sm text-gray-500 focus:ring-1 focus:ring-gray-300 focus:border-gray-300 bg-white" required>
                                        <option value="">Tahun</option>
                                        @for($i=date('Y'); $i>=1990; $i--)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                                <input type="hidden" name="tanggal_lahir" id="tanggal_lahir_hidden" value="{{ old('tanggal_lahir') }}">
                            </div>
                        </div>

                        {{-- Pendidikan Terakhir --}}
                        <div>
                            <label class="block text-sm text-gray-700 mb-2">Pendidikan Terakhir</label>
                            <input type="text" name="asal_sekolah" class="w-full px-4 py-3 rounded-md border border-gray-200 focus:ring-1 focus:ring-gray-300 focus:border-gray-300 text-sm placeholder-gray-400" placeholder="Sekolah terakhir" value="{{ old('asal_sekolah') }}">
                        </div>

                        {{-- Alamat Lengkap --}}
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm text-gray-700 mb-2">Alamat Lengkap</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                <div class="relative">
                                    <select id="provinsi" name="provinsi" class="w-full px-4 py-3 rounded-md border border-gray-200 appearance-none text-sm text-gray-500 focus:ring-1 focus:ring-gray-300 focus:border-gray-300 bg-white" required>
                                        <option value="">Pilih Provinsi</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                                <div class="relative">
                                    <select id="kabupaten" name="kabupaten" class="w-full px-4 py-3 rounded-md border border-gray-200 appearance-none text-sm text-gray-500 focus:ring-1 focus:ring-gray-300 focus:border-gray-300 bg-white disabled:bg-gray-100 disabled:cursor-not-allowed" disabled required>
                                        <option value="">Pilih Kabupaten/Kota</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                                <div class="relative">
                                    <select id="kecamatan" name="kecamatan" class="w-full px-4 py-3 rounded-md border border-gray-200 appearance-none text-sm text-gray-500 focus:ring-1 focus:ring-gray-300 focus:border-gray-300 bg-white disabled:bg-gray-100 disabled:cursor-not-allowed" disabled required>
                                        <option value="">Pilih Kecamatan</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                                <div class="relative">
                                    <select id="desa" name="desa" class="w-full px-4 py-3 rounded-md border border-gray-200 appearance-none text-sm text-gray-500 focus:ring-1 focus:ring-gray-300 focus:border-gray-300 bg-white disabled:bg-gray-100 disabled:cursor-not-allowed" disabled required>
                                        <option value="">Pilih Desa</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="col-span-1 md:col-span-3">
                                    <input type="text" name="alamat" class="w-full px-4 py-3 rounded-md border border-gray-200 focus:ring-1 focus:ring-gray-300 focus:border-gray-300 text-sm placeholder-gray-400" placeholder="Jalan atau detail alamat...." value="{{ old('alamat') }}">
                                </div>
                                <div>
                                    <input type="text" name="kode_pos" class="w-full px-4 py-3 rounded-md border border-gray-200 focus:ring-1 focus:ring-gray-300 focus:border-gray-300 text-sm placeholder-gray-400" placeholder="Kode pos...." value="{{ old('kode_pos') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100 mb-10">

                {{-- SECTION 2: RENCANA PENDIDIKAN --}}
                <div class="mb-10">
                    <h2 class="text-2xl font-bold text-gray-900 mb-8">Rencana pendidikan</h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                        {{-- Jenis Pendaftaran --}}
                        <div>
                            <label class="block text-sm text-gray-700 mb-2">Jenis Pendaftaran</label>
                            <div class="relative">
                                <select name="jenis_pendaftaran" class="w-full px-4 py-3 rounded-md border border-gray-200 appearance-none text-sm text-gray-500 focus:ring-1 focus:ring-gray-300 focus:border-gray-300 bg-white">
                                    <option value="">Baru atau Mutasi....</option>
                                    <option value="Baru" {{ old('jenis_pendaftaran') == 'Baru' ? 'selected' : '' }}>Baru</option>
                                    <option value="Mutasi" {{ old('jenis_pendaftaran') == 'Mutasi' ? 'selected' : '' }}>Mutasi</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>

                        {{-- Pilih Lembaga --}}
                        <div>
                            <label class="block text-sm text-gray-700 mb-2">Pilih Lembaga</label>
                            <div class="relative">
                                <select name="lembaga_tujuan_id" class="w-full px-4 py-3 rounded-md border border-gray-200 appearance-none text-sm text-gray-500 focus:ring-1 focus:ring-gray-300 focus:border-gray-300 bg-white">
                                    <option value="">Pilih lembaga....</option>
                                    @foreach($lembagas ?? [] as $lembaga)
                                        <option value="{{ $lembaga->id }}" {{ old('lembaga_tujuan_id') == $lembaga->id ? 'selected' : '' }}>{{ $lembaga->nama }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>

                        {{-- Pilih Jurusan --}}
                        <div>
                            <label class="block text-sm text-gray-700 mb-2">Pilih Jurusan</label>
                            <div class="relative">
                                <input type="text" name="jurusan" class="w-full px-4 py-3 rounded-md border border-gray-200 focus:ring-1 focus:ring-gray-300 focus:border-gray-300 text-sm placeholder-gray-400" placeholder="Pilih jurusan...." value="{{ old('jurusan') }}">
                            </div>
                        </div>

                        {{-- NISN --}}
                        <div>
                            <label class="block text-sm text-gray-700 mb-2">Nomor Induk Nasional (NISN)</label>
                            <input type="text" name="nisn" class="w-full px-4 py-3 rounded-md border border-gray-200 focus:ring-1 focus:ring-gray-300 focus:border-gray-300 text-sm placeholder-gray-400 mb-1" placeholder="NISN...." value="{{ old('nisn') }}">
                            <span class="text-xs text-gray-400 italic">Opsional jika ada</span>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100 mb-10">

                {{-- SECTION 3: AKUN PENDAFTARAN --}}
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-8">Akun Pendaftaran</h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                        {{-- Email --}}
                        <div>
                            <label class="block text-sm text-gray-700 mb-2">Alamat Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" class="w-full px-4 py-3 rounded-md border border-gray-200 focus:ring-1 focus:ring-gray-300 focus:border-gray-300 text-sm placeholder-gray-400 mb-2" placeholder="Alamat email...." value="{{ old('email') }}" required>
                            <span class="text-[11px] leading-tight text-gray-500 block">Isi dengan benar agar anda mendapat informasi pendaftaran</span>
                        </div>

                        {{-- No Telp --}}
                        <div>
                            <label class="block text-sm text-gray-700 mb-2">Nomer Telepon/WhatsApp <span class="text-red-500">*</span></label>
                            <input type="text" name="telepon_wali" class="w-full px-4 py-3 rounded-md border border-gray-200 focus:ring-1 focus:ring-gray-300 focus:border-gray-300 text-sm placeholder-gray-400 mb-2" placeholder="+628xxxxxxxxx" value="{{ old('telepon_wali') }}" required>
                            <span class="text-[11px] leading-tight text-gray-500 block">Nomer telepon digunakan untuk menghubungi anda terkait pendaftaran</span>
                        </div>

                        {{-- Password --}}
                        <div>
                            <label class="block text-sm text-gray-700 mb-2">Kata Sandi <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="password" name="password" id="password" class="w-full px-4 py-3 rounded-md border border-gray-200 focus:ring-1 focus:ring-gray-300 focus:border-gray-300 text-sm placeholder-gray-400 pr-10" placeholder="Masukkan kata sandi...." required>
                                <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 toggle-password" data-target="password">
                                    <svg class="h-5 w-5 eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg class="h-5 w-5 eye-slash-icon hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Konfirmasi Password --}}
                        <div>
                            <label class="block text-sm text-gray-700 mb-2">Ulangi Kata Sandi <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="password" name="password_confirmation" id="password_confirmation" class="w-full px-4 py-3 rounded-md border border-gray-200 focus:ring-1 focus:ring-gray-300 focus:border-gray-300 text-sm placeholder-gray-400 pr-10" placeholder="Masukkan kata sandi yang sama...." required>
                                <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 toggle-password" data-target="password_confirmation">
                                    <svg class="h-5 w-5 eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg class="h-5 w-5 eye-slash-icon hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col items-center justify-center pt-8">
                    {{-- Hidden honeypot and captcha for backend validation --}}
                    @if(isset($captcha_num1) && isset($captcha_num2))
                        <input type="hidden" name="captcha_answer" value="{{ $captcha_num1 + $captcha_num2 }}">
                    @else
                        <input type="hidden" name="captcha_answer" value="{{ session('captcha_answer') }}">
                    @endif
                    <button type="submit" class="w-full md:w-80 py-4 bg-[#2D3136] hover:bg-black text-white font-medium rounded-lg transition-colors shadow-md mb-4 text-[15px]">
                        Daftar / Lanjutkan
                    </button>
                    <div class="text-[13px] text-gray-500">
                        Sudah memiliki akun? <a href="{{ route('login') }}" class="text-gray-900 font-medium hover:underline">Login</a>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tglHari = document.getElementById('tgl_hari');
        const tglBulan = document.getElementById('tgl_bulan');
        const tglTahun = document.getElementById('tgl_tahun');
        const tglHidden = document.getElementById('tanggal_lahir_hidden');

        function updateTanggal() {
            if (tglHari.value && tglBulan.value && tglTahun.value) {
                tglHidden.value = `${tglTahun.value}-${tglBulan.value}-${tglHari.value}`;
            } else {
                tglHidden.value = '';
            }
        }

        tglHari.addEventListener('change', updateTanggal);
        tglBulan.addEventListener('change', updateTanggal);
        tglTahun.addEventListener('change', updateTanggal);
        
        // Populate if old value exists
        if(tglHidden.value) {
            const parts = tglHidden.value.split('-');
            if(parts.length === 3) {
                tglTahun.value = parts[0];
                tglBulan.value = parts[1];
                tglHari.value = parts[2];
            }
        }

        // Form Validation Scroll and Inline Errors
        const form = document.getElementById('psbForm');
        
        function getLabelForInput(input) {
            const container = input.closest('div');
            if (input.type === 'radio') {
                return container.closest('.grid').previousElementSibling; // The main label above radios
            }
            if (container.parentElement.classList.contains('grid-cols-3')) {
                return container.parentElement.previousElementSibling; // The main label above date selects
            }
            return container.querySelector('label');
        }

        form.addEventListener('submit', function(e) {
            // Remove existing errors
            document.querySelectorAll('.error-text').forEach(el => el.remove());
            let firstInvalid = null;

            const inputs = form.querySelectorAll('input[required], select[required]');
            let isValid = true;

            inputs.forEach(input => {
                if (!input.checkValidity()) {
                    isValid = false;
                    if (!firstInvalid) firstInvalid = input;
                    
                    const label = getLabelForInput(input);
                    if (label && !label.querySelector('.error-text')) {
                        const errorSpan = document.createElement('span');
                        errorSpan.className = 'text-red-500 text-xs italic ml-2 error-text font-medium';
                        errorSpan.textContent = 'wajib di isi';
                        label.appendChild(errorSpan);
                    }
                }
            });

            if (!isValid) {
                e.preventDefault();
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    setTimeout(() => {
                        firstInvalid.focus();
                    }, 500);
                }
            }
        });

        // Clear error when user types/changes value
        form.addEventListener('input', function(e) {
            if (e.target.required && e.target.checkValidity()) {
                const label = getLabelForInput(e.target);
                if (label) {
                    const err = label.querySelector('.error-text');
                    if (err) err.remove();
                }
            }
        });

        // ----------------------------------------------------------------
        // API Wilayah Indonesia (cahyadsn via emsifa public API)
        // ----------------------------------------------------------------
        const baseUrl = 'https://www.emsifa.com/api-wilayah-indonesia/api';
        const selProv = document.getElementById('provinsi');
        const selKab = document.getElementById('kabupaten');
        const selKec = document.getElementById('kecamatan');
        const selDesa = document.getElementById('desa');

        // Initial fetch Provinsi
        fetch(`${baseUrl}/provinces.json`)
            .then(res => res.json())
            .then(data => {
                let options = '<option value="">Pilih Provinsi</option>';
                data.forEach(prov => {
                    options += `<option value="${prov.id}" data-name="${prov.name}">${prov.name}</option>`;
                });
                selProv.innerHTML = options;

                const oldProv = "{{ old('provinsi') }}";
                if(oldProv) {
                    selProv.value = oldProv;
                    selProv.dispatchEvent(new Event('change'));
                }
            })
            .catch(err => console.error('Error fetching provinces:', err));

        // When Provinsi changes -> Fetch Kabupaten
        selProv.addEventListener('change', (e) => {
            selKab.innerHTML = '<option value="">Pilih Kabupten/Kota</option>';
            selKec.innerHTML = '<option value="">Pilih Kecamatan</option>';
            selDesa.innerHTML = '<option value="">Pilih Desa</option>';
            selKab.disabled = true;
            selKec.disabled = true;
            selDesa.disabled = true;

            // Trigger validation clear since it changed
            selKab.dispatchEvent(new Event('input', { bubbles: true }));
            selKec.dispatchEvent(new Event('input', { bubbles: true }));
            selDesa.dispatchEvent(new Event('input', { bubbles: true }));

            const provId = e.target.value;
            if (!provId) return;

            selKab.disabled = false;
            fetch(`${baseUrl}/regencies/${provId}.json`)
                .then(res => res.json())
                .then(data => {
                    let options = '<option value="">Pilih Kabupten/Kota</option>';
                    data.forEach(kab => {
                        options += `<option value="${kab.id}" data-name="${kab.name}">${kab.name}</option>`;
                    });
                    selKab.innerHTML = options;

                    const oldKab = "{{ old('kabupaten') }}";
                    if(oldKab && provId == "{{ old('provinsi') }}") {
                        selKab.value = oldKab;
                        selKab.dispatchEvent(new Event('change'));
                    }
                })
                .catch(err => console.error('Error fetching regencies:', err));
        });

        // When Kabupaten changes -> Fetch Kecamatan
        selKab.addEventListener('change', (e) => {
            selKec.innerHTML = '<option value="">Pilih Kecamatan</option>';
            selDesa.innerHTML = '<option value="">Pilih Desa</option>';
            selKec.disabled = true;
            selDesa.disabled = true;

            selKec.dispatchEvent(new Event('input', { bubbles: true }));
            selDesa.dispatchEvent(new Event('input', { bubbles: true }));

            const kabId = e.target.value;
            if (!kabId) return;

            selKec.disabled = false;
            fetch(`${baseUrl}/districts/${kabId}.json`)
                .then(res => res.json())
                .then(data => {
                    let options = '<option value="">Pilih Kecamatan</option>';
                    data.forEach(kec => {
                        options += `<option value="${kec.id}" data-name="${kec.name}">${kec.name}</option>`;
                    });
                    selKec.innerHTML = options;

                    const oldKec = "{{ old('kecamatan') }}";
                    if(oldKec && kabId == "{{ old('kabupaten') }}") {
                        selKec.value = oldKec;
                        selKec.dispatchEvent(new Event('change'));
                    }
                })
                .catch(err => console.error('Error fetching districts:', err));
        });

        // When Kecamatan changes -> Fetch Desa
        selKec.addEventListener('change', (e) => {
            selDesa.innerHTML = '<option value="">Pilih Desa</option>';
            selDesa.disabled = true;
            
            selDesa.dispatchEvent(new Event('input', { bubbles: true }));

            const kecId = e.target.value;
            if (!kecId) return;

            selDesa.disabled = false;
            fetch(`${baseUrl}/villages/${kecId}.json`)
                .then(res => res.json())
                .then(data => {
                    let options = '<option value="">Pilih Desa</option>';
                    data.forEach(desa => {
                        options += `<option value="${desa.id}" data-name="${desa.name}">${desa.name}</option>`;
                    });
                    selDesa.innerHTML = options;

                    const oldDesa = "{{ old('desa') }}";
                    if(oldDesa && kecId == "{{ old('kecamatan') }}") {
                        selDesa.value = oldDesa;
                    }
                })
                .catch(err => console.error('Error fetching villages:', err));
        });

        // ----------------------------------------------------------------
        // Toggle Password Visibility
        // ----------------------------------------------------------------
        document.querySelectorAll('.toggle-password').forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const eyeIcon = this.querySelector('.eye-icon');
                const eyeSlashIcon = this.querySelector('.eye-slash-icon');

                if (input.type === 'password') {
                    input.type = 'text';
                    eyeIcon.classList.add('hidden');
                    eyeSlashIcon.classList.remove('hidden');
                } else {
                    input.type = 'password';
                    eyeIcon.classList.remove('hidden');
                    eyeSlashIcon.classList.add('hidden');
                }
            });
        });

    });
</script>
@endsection