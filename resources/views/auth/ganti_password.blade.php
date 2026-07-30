@extends('layouts.app')

@section('title', 'Ganti Password')

@section('page_header')
<div>
    <h1 class="text-2xl font-bold text-surface-900 font-heading">Ganti Password</h1>
    <p class="text-sm text-surface-500 mt-1">Perbarui kata sandi akun Anda untuk meningkatkan keamanan.</p>
</div>
@endsection

@section('content')
<div class="max-w-xl mx-auto">
    <form action="{{ route('akun.ganti-password.update') }}" method="POST">
        @csrf
        
        <x-card title="Ubah Kata Sandi" class="space-y-4">
            {{-- Notifikasi Error --}}
            @if($errors->any())
                <div class="bg-danger-50 text-danger-700 p-4 rounded-xl border border-danger-200">
                    <div class="flex gap-3">
                        <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0 mt-0.5"></i>
                        <div>
                            <h3 class="font-semibold mb-1">Gagal memperbarui password:</h3>
                            <ul class="list-disc pl-5 space-y-1 text-sm">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <x-form-input type="password" name="current_password" label="Password Sekarang" required placeholder="Masukkan password saat ini" />
            
            <div class="border-t border-surface-100 my-4"></div>
            
            <x-form-input type="password" name="password" label="Password Baru" required placeholder="Minimal 8 karakter" autocomplete="new-password" />
            <x-form-input type="password" name="password_confirmation" label="Konfirmasi Password Baru" required placeholder="Ulangi password baru" />

            <div class="flex justify-end pt-4 border-t border-surface-100 mt-6">
                <button type="submit" class="btn-primary w-full sm:w-auto flex justify-center items-center gap-2 py-2 px-6">
                    <i data-lucide="key-round" class="w-4 h-4 text-white"></i>
                    <span class="text-white font-semibold">Perbarui Password</span>
                </button>
            </div>
        </x-card>
    </form>
</div>
@endsection
