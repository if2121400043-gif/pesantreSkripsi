@extends('frontend.layouts.app')

@section('title', __('Upload Berkas - PSB'))

@section('content')
<section class="py-12 bg-surface-50 dark:bg-surface-950 transition-colors duration-300 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-extrabold text-surface-900 dark:text-white mb-2">{{ __('Pendaftaran Santri Baru') }}</h1>
            <p class="text-surface-500 dark:text-surface-400">{{ __('No. Pendaftaran:') }} <span class="font-bold text-primary-600 dark:text-primary-400">{{ $calonSantri->no_pendaftaran }}</span></p>
        </div>

        {{-- Stepper Component --}}
        @include('frontend.psb.partials.stepper', ['currentStep' => 2])

        @if(session('success'))
            <div class="bg-success-50 dark:bg-success-500/10 text-success-700 dark:text-success-500 p-6 rounded-2xl border border-success-200 dark:border-success-500/20 mb-8 flex items-start gap-4 shadow-sm animate-fade-in">
                <div class="bg-success-100 dark:bg-success-500/20 text-success-600 dark:text-success-400 p-2 rounded-full flex-shrink-0">
                    <i data-lucide="check-circle" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-1">{{ __('Berhasil!') }}</h3>
                    <p class="text-success-700 dark:text-success-400 leading-relaxed">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-danger-50 dark:bg-danger-500/10 text-danger-700 dark:text-danger-400 p-6 rounded-2xl border border-danger-200 dark:border-danger-500/20 mb-8 flex items-start gap-4 shadow-sm animate-fade-in">
                <div class="bg-danger-100 dark:bg-danger-500/20 text-danger-600 dark:text-danger-400 p-2 rounded-full flex-shrink-0">
                    <i data-lucide="alert-circle" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-1">{{ __('Gagal Mengunggah Berkas') }}</h3>
                    <ul class="list-disc pl-5 space-y-1 text-sm font-medium mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="bg-white dark:bg-surface-900 rounded-3xl shadow-xl shadow-surface-200/50 dark:shadow-none overflow-hidden border border-surface-100 dark:border-surface-800 transition-colors duration-300">
            <div class="p-8 border-b border-surface-100 dark:border-surface-800 bg-surface-50 dark:bg-surface-800/50 flex items-center gap-4">
                <div class="w-12 h-12 bg-secondary-100 dark:bg-secondary-900/30 text-secondary-600 dark:text-secondary-400 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="upload-cloud" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-surface-900 dark:text-white">{{ __('Unggah Berkas Persyaratan') }}</h3>
                    <p class="text-surface-500 dark:text-surface-400 text-sm mt-1">{{ __('Silakan unggah dokumen pendukung dalam format JPG, PNG, atau PDF (Maks. 2MB).') }}</p>
                </div>
            </div>

            <form action="{{ route('frontend.psb.store-berkas', $calonSantri->no_pendaftaran) }}" method="POST" enctype="multipart/form-data" class="p-8 md:p-12">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- KK --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-surface-900 dark:text-white">{{ __('Kartu Keluarga (KK)') }}</label>
                        <div class="relative group">
                            <input type="file" name="kartu_keluarga" accept=".jpg,.jpeg,.png,.pdf" data-preview="preview-kartu_keluarga" class="psb-file block w-full text-sm text-surface-500 dark:text-surface-400 file:mr-4 file:py-3 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-primary-50 dark:file:bg-primary-900/30 file:text-primary-700 dark:file:text-primary-400 hover:file:bg-primary-100 dark:hover:file:bg-primary-900/50 transition-colors border border-surface-200 dark:border-surface-700 rounded-xl bg-surface-50 dark:bg-surface-800/50 cursor-pointer">
                            <div id="preview-kartu_keluarga" class="mt-2 text-xs text-surface-500"></div>
                        </div>
                    </div>

                    {{-- Akta --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-surface-900 dark:text-white">{{ __('Akta Kelahiran') }}</label>
                        <div class="relative group">
                            <input type="file" name="akta_kelahiran" accept=".jpg,.jpeg,.png,.pdf" data-preview="preview-akta_kelahiran" class="psb-file block w-full text-sm text-surface-500 dark:text-surface-400 file:mr-4 file:py-3 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-primary-50 dark:file:bg-primary-900/30 file:text-primary-700 dark:file:text-primary-400 hover:file:bg-primary-100 dark:hover:file:bg-primary-900/50 transition-colors border border-surface-200 dark:border-surface-700 rounded-xl bg-surface-50 dark:bg-surface-800/50 cursor-pointer">
                            <div id="preview-akta_kelahiran" class="mt-2 text-xs text-surface-500"></div>
                        </div>
                    </div>

                    {{-- Ijazah --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-surface-900 dark:text-white">{{ __('Ijazah / SKHUN / SKL') }}</label>
                        <div class="relative group">
                            <input type="file" name="ijazah" accept=".jpg,.jpeg,.png,.pdf" data-preview="preview-ijazah" class="psb-file block w-full text-sm text-surface-500 dark:text-surface-400 file:mr-4 file:py-3 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-primary-50 dark:file:bg-primary-900/30 file:text-primary-700 dark:file:text-primary-400 hover:file:bg-primary-100 dark:hover:file:bg-primary-900/50 transition-colors border border-surface-200 dark:border-surface-700 rounded-xl bg-surface-50 dark:bg-surface-800/50 cursor-pointer">
                            <div id="preview-ijazah" class="mt-2 text-xs text-surface-500"></div>
                        </div>
                    </div>

                    {{-- Pas Foto --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-surface-900 dark:text-white">{{ __('Pas Foto (3x4)') }}</label>
                        <div class="relative group">
                            <input type="file" name="pas_foto" accept=".jpg,.jpeg,.png" data-preview="preview-pas_foto" class="psb-file block w-full text-sm text-surface-500 dark:text-surface-400 file:mr-4 file:py-3 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-primary-50 dark:file:bg-primary-900/30 file:text-primary-700 dark:file:text-primary-400 hover:file:bg-primary-100 dark:hover:file:bg-primary-900/50 transition-colors border border-surface-200 dark:border-surface-700 rounded-xl bg-surface-50 dark:bg-surface-800/50 cursor-pointer">
                            <div id="preview-pas_foto" class="mt-2 text-xs text-surface-500"></div>
                        </div>
                    </div>

                    {{-- KTP Orang Tua / Wali --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-surface-900 dark:text-white">{{ __('KTP Orang Tua / Wali') }}</label>
                        <div class="relative group">
                            <input type="file" name="ktp_orangtua" accept=".jpg,.jpeg,.png,.pdf" data-preview="preview-ktp_orangtua" class="psb-file block w-full text-sm text-surface-500 dark:text-surface-400 file:mr-4 file:py-3 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-primary-50 dark:file:bg-primary-900/30 file:text-primary-700 dark:file:text-primary-400 hover:file:bg-primary-100 dark:hover:file:bg-primary-900/50 transition-colors border border-surface-200 dark:border-surface-700 rounded-xl bg-surface-50 dark:bg-surface-800/50 cursor-pointer">
                            <div id="preview-ktp_orangtua" class="mt-2 text-xs text-surface-500"></div>
                        </div>
                    </div>
                </div>

                <div class="mt-10 p-4 bg-warning-50 dark:bg-warning-500/10 border border-warning-200 dark:border-warning-500/20 rounded-xl text-sm text-warning-700 dark:text-warning-400 flex gap-3">
                    <i data-lucide="info" class="w-5 h-5 flex-shrink-0 mt-0.5"></i>
                    <p>{{ __('Format JPG, PNG, atau PDF dan ukuran maksimal 2MB per berkas. Semua berkas wajib diunggah untuk dikirim ke verifikasi.') }}</p>
                </div>

                <div class="mt-10 pt-6 border-t border-surface-200 dark:border-surface-800 flex justify-end">
                    <button type="submit" class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-primary-600 to-secondary-600 hover:from-primary-500 hover:to-secondary-500 text-white font-bold rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-primary-500/40 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                        {{ __('Selesaikan Pendaftaran') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const maxSize = 2 * 1024 * 1024;
    const allowed = ['application/pdf', 'image/jpeg', 'image/png'];

    document.querySelectorAll('.psb-file').forEach((input) => input.addEventListener('change', () => {
        const file = input.files[0];
        const preview = document.getElementById(input.dataset.preview);
        preview.replaceChildren();
        if (!file) return;

        const valid = file.size <= maxSize && allowed.includes(file.type)
            && (input.name !== 'pas_foto' || file.type !== 'application/pdf');
        if (!valid) {
            preview.textContent = 'Berkas harus JPG/PNG/PDF (pas foto JPG/PNG) dan maksimal 2MB.';
            preview.className = 'mt-2 text-xs text-danger-600';
            input.value = '';
            return;
        }

        preview.className = 'mt-2 text-xs text-success-600';
        preview.textContent = `${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
        if (file.type.startsWith('image/')) {
            const image = document.createElement('img');
            image.className = 'mt-2 h-20 w-20 rounded-lg object-cover border';
            image.alt = 'Pratinjau berkas';
            image.src = URL.createObjectURL(file);
            preview.appendChild(image);
        }
    }));

    document.querySelector('form[enctype="multipart/form-data"]')?.addEventListener('submit', (event) => {
        if (!window.confirm('Periksa kembali berkas sebelum dikirim. Lanjutkan unggah?')) event.preventDefault();
    });
});
</script>
@endpush
