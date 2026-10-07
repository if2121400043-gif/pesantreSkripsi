@extends('frontend.layouts.app', ['hideNavbar' => true, 'hideFooter' => true])

@section('title', __('Nomer Registrasi Pendaftaran'))

@section('content')
<div class="min-h-screen bg-[#FAFAFA] font-sans flex flex-col items-center">
    
    {{-- Header (Full Width Border) --}}
    <div class="w-full bg-white border-b border-gray-200 px-4 sm:px-6 lg:px-8 py-5">
        <div class="max-w-6xl mx-auto flex items-center justify-between relative">
            <a href="{{ route('frontend.psb') }}" class="text-gray-500 hover:text-gray-900 text-sm font-medium flex items-center">
                Kembali ke halaman PSB
            </a>
            <h1 class="text-base font-bold text-gray-900 absolute left-1/2 transform -translate-x-1/2 hidden md:block">Formulir Pendaftaran Santri Baru</h1>
            <div class="hidden md:block w-32"></div> {{-- Spacer --}}
        </div>
    </div>

    {{-- Main Content --}}
    <div class="w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-16">
        
        @if(session('success'))
            <div class="max-w-2xl mx-auto mb-8 bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-lg text-sm text-center">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex flex-col items-center max-w-2xl mx-auto text-center mt-8">
            
            <h2 class="text-sm font-bold text-gray-700 mb-4">Nomer Registrasi</h2>
            <div class="flex items-center justify-center gap-3 mb-12">
                <div class="text-4xl md:text-5xl font-black text-gray-800 tracking-wide" id="nomerRegistrasi">
                    {{ $calonSantri->no_pendaftaran }}
                </div>
                <button type="button" onclick="copyToClipboard(this)" class="text-gray-400 hover:text-gray-700 transition-colors" title="Salin Nomer Registrasi">
                    <svg id="copyIcon" xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <svg id="checkIcon" xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 hidden text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </button>
            </div>

            <p class="text-gray-600 mb-16">
                Simpanlah No. Registrasi di atas, karena akan digunakan sebagai username pada saat login.
            </p>

            <div class="text-left mb-16 inline-block">
                <h3 class="font-bold text-gray-800 mb-4 text-[15px]">Langkah Selanjutnya:</h3>
                <ol class="list-decimal pl-5 text-gray-600 space-y-1.5 text-[15px]">
                    <li>Masuk dengan Nomor Registrasi dan Password yang telah di daftarkan.</li>
                    <li>Validasi Data serta Pengisian identitas Orangtua / Wali</li>
                    <li>Upload Berkas-berkas Pendukung</li>
                </ol>
            </div>

            <div class="flex flex-col w-full max-w-[280px] gap-4">
                <a href="{{ route('login') }}" class="w-full py-3 bg-[#E5E7EB] hover:bg-[#D1D5DB] text-gray-800 font-semibold rounded-md text-center transition-colors text-[15px]">
                    Masuk di sini
                </a>
                
                <a href="{{ route('frontend.home') }}" class="w-full py-3 bg-[#E5E7EB] hover:bg-[#D1D5DB] text-gray-800 font-semibold rounded-md text-center transition-colors text-[15px]">
                    Kembali ke Halaman Utama
                </a>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function copyToClipboard(btn) {
        const text = document.getElementById('nomerRegistrasi').innerText.trim();
        navigator.clipboard.writeText(text).then(() => {
            const copyIcon = btn.querySelector('#copyIcon');
            const checkIcon = btn.querySelector('#checkIcon');
            
            copyIcon.classList.add('hidden');
            checkIcon.classList.remove('hidden');
            
            setTimeout(() => {
                copyIcon.classList.remove('hidden');
                checkIcon.classList.add('hidden');
            }, 2000);
        }).catch(err => {
            console.error('Gagal menyalin: ', err);
            alert('Gagal menyalin nomer registrasi.');
        });
    }
</script>
@endpush
