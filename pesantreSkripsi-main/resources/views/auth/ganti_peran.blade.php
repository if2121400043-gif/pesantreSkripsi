@extends('layouts.app')

@section('title', 'Ganti Peran')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Ganti Peran / Hak Akses</h1>
        <p class="text-sm text-surface-500 mt-1">Pilih peran aktif yang ingin Anda gunakan saat ini.</p>
    </div>
    <a href="#" onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href = '/'; } return false;" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-surface-200 bg-white text-sm font-medium text-surface-600 hover:bg-surface-50 transition-colors">
        <i data-lucide="x" class="w-4 h-4"></i>
        <span>Tutup</span>
    </a>
</div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <x-card title="Daftar Peran Anda">
        <p class="text-sm text-surface-500 mb-6">Akun Anda terdaftar dengan beberapa hak akses. Pilih peran di bawah untuk berganti portal dashboard secara otomatis.</p>
        
        <div class="space-y-4">
            @forelse($userRoles as $ur)
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 border rounded-xl transition-all {{ $ur->is_default ? 'bg-primary-50/50 border-primary-300 ring-1 ring-primary-300' : 'bg-white border-surface-200 hover:bg-surface-50' }}">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0 {{ $ur->is_default ? 'bg-primary-100 text-primary-700' : 'bg-surface-100 text-surface-600' }}">
                            <i data-lucide="{{ $ur->role->nama === 'SUPER_ADMIN' ? 'shield-check' : ($ur->role->nama === 'GURU' ? 'graduation-cap' : ($ur->role->nama === 'BENDAHARA' ? 'wallet' : 'user')) }}" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <div class="font-bold text-surface-900 flex items-center gap-2">
                                <span>{{ $ur->role->label }}</span>
                                @if($ur->is_default)
                                    <span class="inline-flex px-2 py-0.5 rounded text-[0.6rem] font-bold uppercase tracking-wider bg-primary-600 text-white">
                                        Aktif saat ini
                                    </span>
                                @endif
                            </div>
                            <span class="text-xs text-surface-500 block mt-0.5">{{ $ur->role->deskripsi ?? 'Hak akses portal ' . strtolower($ur->role->label) }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-4 sm:mt-0 w-full sm:w-auto">
                        @if($ur->is_default)
                            <button disabled class="w-full sm:w-auto px-4 py-2 bg-surface-200 text-surface-500 rounded-lg text-sm font-semibold cursor-not-allowed">
                                Peran Sedang Dipakai
                            </button>
                        @else
                            <form action="{{ route('akun.ganti-peran.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="user_role_id" value="{{ $ur->id }}">
                                <button type="submit" class="w-full sm:w-auto btn-primary flex justify-center items-center gap-1 text-sm">
                                    <i data-lucide="repeat" class="w-4 h-4"></i>
                                    <span>Ganti Ke Peran Ini</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-surface-400">
                    <i data-lucide="alert-circle" class="w-12 h-12 mx-auto mb-2"></i>
                    <p>Anda hanya memiliki satu peran standar.</p>
                </div>
            @endforelse
        </div>
    </x-card>
</div>
@endsection
