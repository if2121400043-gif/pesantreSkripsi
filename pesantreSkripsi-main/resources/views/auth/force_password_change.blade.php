@extends('layouts.guest')

@section('title', 'Buat Password Baru')
@section('meta_description', 'Pembaruan kata sandi keamanan untuk Wali Santri')

@push('head')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@endpush

@section('body')
<div class="min-h-screen flex flex-col items-center justify-center p-4 sm:p-8
            bg-gradient-to-br from-primary-950 via-primary-900 to-secondary-900
            pattern-islamic relative overflow-hidden">

    {{-- Decorative blurred orbs --}}
    <div class="absolute top-[-15%] right-[-8%] w-[500px] h-[500px] bg-accent-500/8 rounded-full blur-[100px] animate-float"></div>
    <div class="absolute bottom-[-15%] left-[-8%] w-[400px] h-[400px] bg-secondary-500/8 rounded-full blur-[100px]"></div>
    <div class="absolute top-[30%] left-[20%] w-48 h-48 bg-primary-400/5 rounded-full blur-[80px]"></div>

    {{-- Main Container 2-Kolom --}}
    <div class="w-full max-w-5xl rounded-3xl shadow-modal overflow-hidden flex flex-col md:flex-row relative z-10 animate-fade-in-up">
        
        {{-- KOLOM KIRI: Logo & Branding --}}
        <div class="w-full md:w-5/12 relative overflow-hidden">
            {{-- Background gradient layer --}}
            <div class="absolute inset-0 bg-gradient-to-b from-primary-900/95 via-primary-800/90 to-secondary-900/95"></div>
            {{-- Subtle pattern overlay --}}
            <div class="absolute inset-0 pattern-islamic opacity-30"></div>
            {{-- Bottom accent line --}}
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-accent-400 via-secondary-400 to-accent-400"></div>
            
            <div class="relative z-10 p-8 lg:p-12 flex flex-col items-center justify-center text-center min-h-[280px] md:min-h-full">
                <div class="animate-fade-in animate-delay-100">
                    {{-- Logo --}}
                    <div class="inline-block p-2 rounded-full bg-white/10 backdrop-blur-sm mb-6 ring-1 ring-white/20 shadow-2xl">
                        <picture>
                            <source srcset="{{ asset('images/logo-pesantren-256.webp') }}?v={{ time() }}" type="image/webp">
                            <img src="{{ asset('images/logo-pesantren-256.webp') }}?v={{ time() }}"
                                 alt="Logo Pondok Pesantren Nurul Furqon"
                                 class="w-28 h-28 lg:w-36 lg:h-36 rounded-full object-cover"
                                 id="login-logo">
                        </picture>
                    </div>
                    
                    {{-- Branding text --}}
                    <h1 class="text-2xl lg:text-3xl font-extrabold text-white font-['Poppins'] leading-tight mb-1">Keamanan</h1>
                    <p class="text-accent-300 text-xl lg:text-2xl font-['Poppins'] font-bold mb-5">Sistem Portal</p>
                    
                    <div class="w-12 h-0.5 bg-gradient-to-r from-transparent via-accent-400/60 to-transparent mx-auto mb-5"></div>
                    
                    <p class="text-primary-200/80 text-sm leading-relaxed px-4 max-w-[260px] mx-auto">Kami mengutamakan privasi dan keamanan data Anda dan santri.</p>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Form Card --}}
        <div class="w-full md:w-7/12 bg-white p-8 lg:p-12 xl:p-14 flex items-center">
            <div class="w-full max-w-md mx-auto animate-fade-in animate-delay-200">
                {{-- Header --}}
                <div class="mb-8">
                    <h2 class="text-2xl lg:text-3xl font-bold text-surface-900 mb-2 font-['Poppins']">Buat Password Baru</h2>
                    <p class="text-sm lg:text-base text-surface-500 leading-relaxed">Halo Bapak/Ibu <strong>{{ auth()->user()->orang->nama_lengkap ?? auth()->user()->username }}</strong>,<br>Demi keamanan, silakan buat password baru yang hanya Anda yang tahu sebelum melanjutkan ke dashboard.</p>
                </div>

                {{-- Error Alert --}}
                @if($errors->any())
                    <div class="mb-6 p-4 bg-danger-50 text-danger-700 rounded-xl border border-danger-500/20 flex gap-3 text-sm animate-shake">
                        <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0 mt-0.5"></i>
                        <div>
                            @foreach($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.force-change.store') }}" id="password-form">
                    @csrf

                    {{-- Password Baru --}}
                    <div class="mb-5">
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-sm font-semibold text-surface-700 font-['Poppins']">
                                Password Baru <span class="text-danger-500">*</span>
                            </label>
                        </div>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-surface-400 group-focus-within:text-primary-500 transition-colors">
                                <i data-lucide="lock" class="w-[18px] h-[18px]"></i>
                            </div>
                            <input type="password"
                                   name="password"
                                   id="password"
                                   placeholder="Minimal 8 karakter"
                                   class="block w-full pl-11 pr-12 py-3 bg-surface-50 border border-surface-200 rounded-xl text-surface-900 text-sm placeholder:text-surface-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all {{ $errors->has('password') ? 'border-danger-500 bg-danger-50/30 focus:ring-danger-500/20' : '' }}"
                                   required
                                   autofocus>
                            <button type="button"
                                    onclick="togglePassword('password', 'icon-eye', 'icon-eye-off')"
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-surface-400 hover:text-primary-600 transition-colors"
                                    aria-label="Tampilkan password">
                                <i data-lucide="eye" class="w-[18px] h-[18px]" id="icon-eye"></i>
                                <i data-lucide="eye-off" class="w-[18px] h-[18px] hidden" id="icon-eye-off"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password_confirmation" class="block text-sm font-semibold text-surface-700 font-['Poppins']">
                                Ulangi Password Baru <span class="text-danger-500">*</span>
                            </label>
                        </div>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-surface-400 group-focus-within:text-primary-500 transition-colors">
                                <i data-lucide="shield-check" class="w-[18px] h-[18px]"></i>
                            </div>
                            <input type="password"
                                   name="password_confirmation"
                                   id="password_confirmation"
                                   placeholder="Ketik ulang password baru Anda"
                                   class="block w-full pl-11 pr-12 py-3 bg-surface-50 border border-surface-200 rounded-xl text-surface-900 text-sm placeholder:text-surface-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all"
                                   required>
                            <button type="button"
                                    onclick="togglePassword('password_confirmation', 'icon-eye-2', 'icon-eye-off-2')"
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-surface-400 hover:text-primary-600 transition-colors"
                                    aria-label="Tampilkan password">
                                <i data-lucide="eye" class="w-[18px] h-[18px]" id="icon-eye-2"></i>
                                <i data-lucide="eye-off" class="w-[18px] h-[18px] hidden" id="icon-eye-off-2"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" 
                            class="w-full flex items-center justify-center gap-2.5 py-3.5 px-4 rounded-xl text-sm font-bold text-white 
                                   bg-gradient-to-r from-primary-600 via-primary-500 to-secondary-600 
                                   hover:from-primary-500 hover:via-primary-400 hover:to-secondary-500
                                   focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:ring-offset-2
                                   shadow-lg shadow-primary-600/25 hover:shadow-xl hover:shadow-primary-500/30 
                                   transform hover:-translate-y-0.5 active:translate-y-0
                                   transition-all duration-200" 
                            id="btn-submit">
                        <i data-lucide="save" class="w-[18px] h-[18px]"></i>
                        Simpan & Lanjutkan
                    </button>
                </form>

            </div>
        </div>
    </div>

    {{-- Footer --}}
    <p class="text-center text-primary-300/50 text-xs mt-8 relative z-10">
        &copy; {{ date('Y') }} Pondok Pesantren Nurul Furqon. Hak Cipta Dilindungi.
    </p>
</div>
@endsection

@push('scripts')
<script>
    function togglePassword(inputId, eyeId, eyeOffId) {
        const input = document.getElementById(inputId);
        const iconEye = document.getElementById(eyeId);
        const iconEyeOff = document.getElementById(eyeOffId);

        if (input.type === 'password') {
            input.type = 'text';
            iconEye.classList.add('hidden');
            iconEyeOff.classList.remove('hidden');
        } else {
            input.type = 'password';
            iconEye.classList.remove('hidden');
            iconEyeOff.classList.add('hidden');
        }
    }
</script>
@endpush
