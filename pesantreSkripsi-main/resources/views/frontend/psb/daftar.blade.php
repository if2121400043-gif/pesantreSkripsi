@extends('frontend.layouts.app')

@section('title', __('Formulir Pendaftaran - PSB'))

@section('content')
<section class="py-12 bg-surface-50 dark:bg-surface-950 transition-colors duration-300 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-extrabold text-surface-900 dark:text-white mb-2">{{ __('Pendaftaran Santri Baru') }}</h1>
            <p class="text-surface-500 dark:text-surface-400">{{ $gelombangAktif->nama ?? 'Gelombang Aktif' }}</p>
        </div>

        {{-- Stepper Component --}}
        @include('frontend.psb.partials.stepper', ['currentStep' => 1])

        @if(session('error'))
            <div class="bg-danger-50 dark:bg-danger-500/10 text-danger-700 dark:text-danger-500 p-6 rounded-2xl border border-danger-200 dark:border-danger-500/20 mb-8 flex items-start gap-4 shadow-sm animate-fade-in">
                <div class="bg-danger-100 dark:bg-danger-500/20 text-danger-600 dark:text-danger-400 p-2 rounded-full flex-shrink-0">
                    <i data-lucide="alert-circle" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-1">{{ __('Pendaftaran Gagal') }}</h3>
                    <p class="text-danger-700 dark:text-danger-400 leading-relaxed">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        {{-- Registration Form --}}
        <div class="bg-white dark:bg-surface-900 rounded-3xl shadow-xl shadow-surface-200/50 dark:shadow-none overflow-hidden border border-surface-100 dark:border-surface-800 transition-colors duration-300">
            <div class="p-8 border-b border-surface-100 dark:border-surface-800 bg-surface-50 dark:bg-surface-800/50 flex items-center gap-4">
                <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="file-edit" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-surface-900 dark:text-white">{{ __('Form Formulir Santri Baru') }}</h3>
                    <p class="text-surface-500 dark:text-surface-400 text-sm mt-1">{{ __('Lengkapi data di bawah ini dengan sebenar-benarnya sesuai Kartu Keluarga.') }}</p>
                </div>
            </div>

            <form x-data="{ 
                    step: {{ $errors->any() ? 2 : 1 }},
                    goToStep2() {
                        const step1 = document.getElementById('step-1-container');
                        const inputs = step1.querySelectorAll('input[required], select[required]');
                        for(let i=0; i<inputs.length; i++) {
                            if(!inputs[i].checkValidity()) {
                                inputs[i].reportValidity();
                                return;
                            }
                        }
                        this.step = 2;
                        window.scrollTo({top: document.getElementById('form-header').offsetTop - 20, behavior: 'smooth'});
                    }
                }" 
                action="{{ route('frontend.psb.store') }}" method="POST" class="p-8 md:p-12">
                @csrf
                <input type="hidden" name="gelombang_id" value="{{ $gelombangAktif->id }}">

                @if($errors->any())
                    <div class="bg-danger-50 dark:bg-danger-500/10 text-danger-700 dark:text-danger-400 p-4 rounded-xl border border-danger-200 dark:border-danger-500/20 mb-8">
                        <ul class="list-disc pl-5 space-y-1 text-sm font-medium">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Indikator Step Lokal --}}
                <div class="flex items-center justify-between mb-8 pb-4 border-b border-surface-200 dark:border-surface-800" id="form-header">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold transition-colors"
                             :class="step >= 1 ? 'bg-primary-600 text-white shadow-md shadow-primary-500/30' : 'bg-surface-200 dark:bg-surface-800 text-surface-500'">
                            1
                        </div>
                        <div class="hidden sm:block">
                            <p class="text-sm font-bold" :class="step >= 1 ? 'text-surface-900 dark:text-white' : 'text-surface-500'">Identitas Santri</p>
                            <p class="text-xs text-surface-500">Data diri & sekolah</p>
                        </div>
                    </div>
                    <div class="flex-1 h-px bg-surface-200 dark:bg-surface-800 mx-4">
                        <div class="h-full bg-primary-500 transition-all duration-500" :style="'width: ' + (step === 2 ? '100%' : '0%')"></div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="hidden sm:block text-right">
                            <p class="text-sm font-bold" :class="step === 2 ? 'text-surface-900 dark:text-white' : 'text-surface-500'">Data Keluarga</p>
                            <p class="text-xs text-surface-500">Orang tua & wali</p>
                        </div>
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold transition-colors"
                             :class="step === 2 ? 'bg-primary-600 text-white shadow-md shadow-primary-500/30' : 'bg-surface-200 dark:bg-surface-800 text-surface-500'">
                            2
                        </div>
                    </div>
                </div>

                {{-- ==================== LANGKAH 1 ==================== --}}
                <div id="step-1-container" x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" class="space-y-8">
                    {{-- Identitas Utama --}}
                    <div>
                        <h4 class="text-base font-bold text-surface-900 dark:text-white mb-4 pb-2 border-b border-surface-200 dark:border-surface-800 flex items-center gap-2">
                            <i data-lucide="user" class="w-4 h-4 text-primary-500 dark:text-primary-400"></i> {{ __('Identitas Calon Santri') }}
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5">{{ __('NIK (Nomor Induk Kependudukan)') }} <span class="text-danger-500">*</span></label>
                                <input type="text" name="nik" value="{{ old('nik') }}" class="w-full rounded-xl border-surface-300 dark:border-surface-700 bg-white dark:bg-surface-800 text-surface-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500/20 placeholder:text-surface-400 dark:placeholder:text-surface-500 transition-colors" maxlength="16" required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5">{{ __('Nomor Kartu Keluarga (KK)') }} <span class="text-danger-500">*</span></label>
                                {{-- PERHATIKAN: Kolom KK tidak masuk database di controller, tapi tetap diwajibkan isi untuk syarat administrasi --}}
                                <input type="text" name="kk" value="{{ old('kk') }}" class="w-full rounded-xl border-surface-300 dark:border-surface-700 bg-white dark:bg-surface-800 text-surface-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500/20 placeholder:text-surface-400 dark:placeholder:text-surface-500 transition-colors" maxlength="16" required>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5">{{ __('Nama Lengkap') }} <span class="text-danger-500">*</span></label>
                                {{-- DIUBAH: name="nama_lengkap" --}}
                                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" class="w-full rounded-xl border-surface-300 dark:border-surface-700 bg-white dark:bg-surface-800 text-surface-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500/20 uppercase placeholder:text-surface-400 dark:placeholder:text-surface-500 transition-colors" required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5">{{ __('Tempat Lahir') }} <span class="text-danger-500">*</span></label>
                                <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}" class="w-full rounded-xl border-surface-300 dark:border-surface-700 bg-white dark:bg-surface-800 text-surface-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500/20 uppercase placeholder:text-surface-400 dark:placeholder:text-surface-500 transition-colors" required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5">{{ __('Tanggal Lahir') }} <span class="text-danger-500">*</span></label>
                                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="w-full rounded-xl border-surface-300 dark:border-surface-700 bg-white dark:bg-surface-800 text-surface-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500/20 transition-colors" required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5">{{ __('Jenis Kelamin') }} <span class="text-danger-500">*</span></label>
                                <select name="jenis_kelamin" class="w-full rounded-xl border-surface-300 dark:border-surface-700 bg-white dark:bg-surface-800 text-surface-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500/20 transition-colors" required>
                                    <option value="">-- {{ __('Pilih') }} --</option>
                                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>{{ __('Laki-laki') }}</option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>{{ __('Perempuan') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Asal Sekolah --}}
                    <div>
                        <h4 class="text-base font-bold text-surface-900 dark:text-white mb-4 pb-2 border-b border-surface-200 dark:border-surface-800 flex items-center gap-2">
                            <i data-lucide="info" class="w-4 h-4 text-primary-500 dark:text-primary-400"></i> {{ __('Informasi Tambahan') }}
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5">{{ __('Asal Sekolah') }}</label>
                                <input type="text" name="asal_sekolah" value="{{ old('asal_sekolah') }}" class="w-full rounded-xl border-surface-300 dark:border-surface-700 bg-white dark:bg-surface-800 text-surface-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500/20 placeholder:text-surface-400 dark:placeholder:text-surface-500 transition-colors" placeholder="{{ __('Misal: SDN 1 Jakarta') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5">{{ __('Rencana Pendidikan') }}</label>
                                <select name="lembaga_tujuan_id" class="w-full rounded-xl border-surface-300 dark:border-surface-700 bg-white dark:bg-surface-800 text-surface-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500/20 transition-colors">
                                    <option value="">-- {{ __('Pilih Lembaga Tujuan') }} --</option>
                                    @foreach($lembagas as $lembaga)
                                        <option value="{{ $lembaga->id }}" {{ old('lembaga_tujuan_id') == $lembaga->id ? 'selected' : '' }}>{{ $lembaga->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 pt-6 border-t border-surface-200 dark:border-surface-800 flex justify-end">
                        <button type="button" @click="goToStep2()" class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-primary-600 to-secondary-600 hover:from-primary-500 hover:to-secondary-500 text-white font-bold rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-primary-500/40 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                            {{ __('Selanjutnya: Data Orang Tua') }}
                            <i data-lucide="arrow-right" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>

                {{-- ==================== LANGKAH 2 ==================== --}}
                <div id="step-2-container" x-show="step === 2" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" class="space-y-8">
                    
                    {{-- Data Orang Tua / Wali --}}
                    @include('frontend.psb.partials.data_orangtua')

                    {{-- Keamanan (Captcha & Honeypot) --}}
                    <div class="bg-surface-50 dark:bg-surface-800/50 p-6 rounded-2xl border border-surface-200 dark:border-surface-700">
                        <h4 class="text-sm font-bold text-surface-900 dark:text-white mb-3 flex items-center gap-2">
                            <i data-lucide="shield-check" class="w-4 h-4 text-success-500"></i> {{ __('Verifikasi Keamanan') }}
                        </h4>
                        <p class="text-sm text-surface-600 dark:text-surface-400 mb-4">{{ __('Untuk mencegah spam, silakan jawab pertanyaan matematika sederhana di bawah ini:') }}</p>

                        <div class="flex items-center gap-4 max-w-sm">
                            <div class="bg-white dark:bg-surface-800 px-5 py-2.5 rounded-xl border border-surface-300 dark:border-surface-700 font-bold text-lg text-surface-900 dark:text-white shadow-sm flex-shrink-0">
                                {{ $captcha_num1 ?? 0 }} + {{ $captcha_num2 ?? 0 }} =
                            </div>
                            <input type="number" name="captcha_answer" class="w-full rounded-xl border-surface-300 dark:border-surface-700 bg-white dark:bg-surface-800 text-surface-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500/20 text-lg font-bold px-4 transition-colors" placeholder="?" required>
                        </div>

                        {{-- Honeypot --}}
                        <div class="hidden" aria-hidden="true">
                            <label for="website_url_website">Leave this field empty if you're human:</label>
                            <input type="text" name="website_url_website" id="website_url_website" tabindex="-1" autocomplete="off">
                        </div>
                    </div>

                    <div class="mt-10 pt-6 border-t border-surface-200 dark:border-surface-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <button type="button" @click="step = 1; window.scrollTo({top: document.getElementById('form-header').offsetTop - 20, behavior: 'smooth'})" class="w-full sm:w-auto px-6 py-4 bg-surface-200 hover:bg-surface-300 dark:bg-surface-800 dark:hover:bg-surface-700 text-surface-700 dark:text-surface-300 font-bold rounded-xl transition-all flex items-center justify-center gap-2">
                            <i data-lucide="arrow-left" class="w-5 h-5"></i>
                            {{ __('Kembali') }}
                        </button>

                        <button type="submit" class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-primary-600 to-secondary-600 hover:from-primary-500 hover:to-secondary-500 text-white font-bold rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-primary-500/40 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                            {{ __('Selesaikan Pendaftaran') }}
                            <i data-lucide="check-circle" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection