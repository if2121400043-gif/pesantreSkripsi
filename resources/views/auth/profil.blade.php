@extends('layouts.app')

@section('title', 'Profil Saya')

@section('page_header')
<div>
    <h1 class="text-2xl font-bold text-surface-900 font-heading">Profil Saya</h1>
    <p class="text-sm text-surface-500 mt-1">Kelola data profil akun dan pantau biodata terhubung Anda.</p>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- Left Column: Avatar & Summary --}}
        <div class="md:col-span-1 space-y-6">
            <x-card class="text-center">
                <div class="w-24 h-24 rounded-full bg-gradient-to-br from-primary-500 to-secondary-600 flex items-center justify-center text-white text-3xl font-bold mx-auto shadow-md">
                    {{ substr($user->orang->nama_lengkap ?? $user->username ?? 'A', 0, 1) }}
                </div>
                <h3 class="font-bold text-surface-900 font-heading mt-4">{{ $user->orang->nama_lengkap ?? $user->username }}</h3>
                <p class="text-xs text-primary-600 font-mono mt-1">{{ $user->orang->niup ?? 'Akun Sistem' }}</p>
                <div class="mt-4 pt-4 border-t border-surface-100 flex flex-wrap justify-center gap-1.5">
                    @foreach($user->roles as $ur)
                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-[0.65rem] font-bold uppercase tracking-wider bg-primary-50 text-primary-700 border border-primary-100">
                            {{ $ur->role->label }}
                        </span>
                    @endforeach
                </div>
            </x-card>
        </div>

        {{-- Right Column: Forms --}}
        <div class="md:col-span-2 space-y-6">
            {{-- Edit Account Card --}}
            <form action="{{ route('akun.profil.update') }}" method="POST">
                @csrf
                <x-card title="Pengaturan Akun" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-form-input name="username" label="Username" required value="{{ old('username', $user->username) }}" />
                        <x-form-input type="email" name="email" label="Alamat Email" required value="{{ old('email', $user->email) }}" />
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="btn-primary flex items-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </x-card>
            </form>

            {{-- Biodata Card (Read Only) --}}
            <x-card title="Informasi Biodata Terhubung">
                @if($user->orang)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6 text-sm">
                        <div>
                            <span class="text-xs text-surface-400 block uppercase tracking-wider font-semibold">Nama Lengkap</span>
                            <span class="font-bold text-surface-800">{{ $user->orang->nama_lengkap }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-surface-400 block uppercase tracking-wider font-semibold">NIUP</span>
                            <span class="font-mono font-bold text-primary-700">{{ $user->orang->niup }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-surface-400 block uppercase tracking-wider font-semibold">NIK (No. KTP)</span>
                            <span class="font-bold text-surface-800">{{ $user->orang->nik ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-surface-400 block uppercase tracking-wider font-semibold">Jenis Kelamin</span>
                            <span class="font-bold text-surface-800">{{ $user->orang->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-surface-400 block uppercase tracking-wider font-semibold">Konten Telepon / WA</span>
                            <span class="font-bold text-surface-800">{{ $user->orang->telepon ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-surface-400 block uppercase tracking-wider font-semibold">Status Keaktifan</span>
                            <span class="font-bold">
                                @if($user->orang->is_active)
                                    <span class="text-success-600 bg-success-50 px-2 py-0.5 rounded text-xs border border-success-100">AKTIF</span>
                                @else
                                    <span class="text-danger-600 bg-danger-50 px-2 py-0.5 rounded text-xs border border-danger-100">NONAKTIF</span>
                                @endif
                            </span>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="text-xs text-surface-400 block uppercase tracking-wider font-semibold">Alamat Lengkap</span>
                            <span class="font-medium text-surface-700 block mt-0.5">{{ $user->orang->alamat_lengkap ?? '-' }}</span>
                        </div>
                    </div>
                @else
                    <div class="p-8 text-center bg-surface-50 border border-surface-200 rounded-2xl">
                        <i data-lucide="user-x" class="w-12 h-12 text-surface-300 mx-auto mb-3"></i>
                        <h4 class="font-bold text-surface-850">Belum Terhubung dengan Biodata</h4>
                        <p class="text-xs text-surface-500 max-w-sm mx-auto mt-1 leading-relaxed">
                            Akun Anda saat ini merupakan akun administratif sistem yang belum dihubungkan ke data pribadi orang (Pegawai / Wali).
                        </p>
                    </div>
                @endif
            </x-card>
        </div>

    </div>
</div>
@endsection
