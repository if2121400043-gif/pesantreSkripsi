@extends('frontend.layouts.app')

@section('title', __('Pendaftaran Selesai - PSB'))

@section('content')
<section class="py-12 bg-surface-50 dark:bg-surface-950 transition-colors duration-300 min-h-screen flex items-center justify-center">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        
        {{-- Stepper Component --}}
        @include('frontend.psb.partials.stepper', ['currentStep' => 3])

        <div class="bg-white dark:bg-surface-900 rounded-3xl shadow-2xl shadow-surface-200/50 dark:shadow-none overflow-hidden border border-surface-100 dark:border-surface-800 transition-colors duration-300 relative text-center">
            {{-- Top Accent Bar --}}
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-success-400 to-success-600"></div>

            <div class="p-10 md:p-16">
                {{-- Success Icon --}}
                <div class="w-24 h-24 mx-auto bg-success-50 dark:bg-success-500/10 text-success-500 rounded-full flex items-center justify-center mb-8 relative">
                    <div class="absolute inset-0 bg-success-500/20 rounded-full animate-ping"></div>
                    <i data-lucide="check-circle" class="w-12 h-12 relative z-10"></i>
                </div>

                <h1 class="text-3xl font-extrabold text-surface-900 dark:text-white mb-4">{{ __('Pendaftaran Berhasil!') }}</h1>
                <p class="text-surface-600 dark:text-surface-400 text-lg mb-8 leading-relaxed max-w-lg mx-auto">
                    {{ __('Alhamdulillah, data pendaftaran ananda') }} <strong class="text-surface-900 dark:text-white">{{ $calonSantri->nama_lengkap }}</strong> {{ __('telah kami terima dengan Nomor Pendaftaran:') }}
                </p>

                <div class="inline-block px-8 py-4 bg-surface-100 dark:bg-surface-800 rounded-2xl mb-10 border border-surface-200 dark:border-surface-700">
                    <span class="block text-sm text-surface-500 dark:text-surface-400 font-bold mb-1 uppercase tracking-widest">{{ __('No. Pendaftaran') }}</span>
                    <span class="text-3xl font-black text-primary-600 dark:text-primary-400 tracking-tight">{{ $calonSantri->no_pendaftaran }}</span>
                </div>

                <div class="p-6 bg-info-50 dark:bg-info-500/10 border border-info-200 dark:border-info-500/20 rounded-2xl text-left mb-10">
                    <h4 class="font-bold text-info-800 dark:text-info-400 mb-2 flex items-center gap-2">
                        <i data-lucide="info" class="w-5 h-5"></i>
                        {{ __('Langkah Selanjutnya') }}
                    </h4>
                    <p class="text-info-700 dark:text-info-300 text-sm leading-relaxed mb-4">
                        {{ __('Silakan simpan Nomor Pendaftaran Anda. Untuk mendapatkan informasi lebih lanjut terkait jadwal tes atau proses selanjutnya, silakan bergabung dengan grup WhatsApp kami atau hubungi Admin Pendaftaran.') }}
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4">
                        @php
                            $phone = $pesantren?->telepon ?? '0';
                            // Remove non-numeric characters except leading '+'
                            $waNumber = preg_replace('/[^0-9]/', '', $phone);
                            // If starts with 0, change to 62 (for Indonesia)
                            if (str_starts_with($waNumber, '0')) {
                                $waNumber = '62' . substr($waNumber, 1);
                            }
                            
                            $waMessage = urlencode("Assalamu'alaikum, saya ingin konfirmasi pendaftaran santri baru dengan Nomor Pendaftaran: " . $calonSantri->no_pendaftaran . " atas nama " . $calonSantri->nama_lengkap . ".");
                            $waLink = "https://wa.me/{$waNumber}?text={$waMessage}";
                        @endphp
                        
                        <a href="{{ $waLink }}" target="_blank" class="flex-1 px-6 py-3.5 bg-success-600 hover:bg-success-700 text-white font-bold rounded-xl shadow-lg shadow-success-500/30 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                            <i data-lucide="message-circle" class="w-5 h-5"></i>
                            {{ __('Chat Admin (WhatsApp)') }}
                        </a>
                        
                        <a href="{{ route('frontend.home') }}" class="flex-1 px-6 py-3.5 bg-surface-800 dark:bg-surface-700 hover:bg-surface-900 dark:hover:bg-surface-600 text-white font-bold rounded-xl shadow-lg shadow-surface-900/20 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                            <i data-lucide="home" class="w-5 h-5"></i>
                            {{ __('Kembali ke Beranda') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
