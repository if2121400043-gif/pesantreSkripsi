@extends('layouts.app')

@section('title', 'Manajemen Relasi Keluarga — PP Nurul Furqon')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Hubungan Keluarga & Wali</h1>
        <p class="text-sm text-surface-500 mt-1">Kelola relasi antara santri dengan orang tua/wali serta hak akses perizinan.</p>
    </div>
    <a href="{{ route('admin.keluarga.create') }}" class="btn-primary flex items-center gap-2 py-2.5 px-4 rounded-xl font-bold text-xs shadow-md shadow-primary-700/20 shrink-0">
        <i data-lucide="plus" class="w-4 h-4"></i>
        <span>+ Tambah Relasi Baru</span>
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6 pb-12">

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-success-50 border border-success-200 text-success-800 flex items-center justify-between text-xs font-medium shadow-sm">
            <div class="flex items-center gap-2">
                <i data-lucide="check-circle" class="w-5 h-5 text-success-600"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-success-600 hover:text-success-900"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-danger-50 border border-danger-200 text-danger-800 flex items-center justify-between text-xs font-medium shadow-sm">
            <div class="flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-5 h-5 text-danger-600"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-danger-600 hover:text-danger-900"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>
    @endif

    <div class="bg-white rounded-3xl border border-surface-200 shadow-sm overflow-hidden">
        
        {{-- Search Bar with Guaranteed Padding --}}
        <div class="p-4 border-b border-surface-100 bg-surface-50/70">
            <form action="{{ route('admin.keluarga.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <div class="absolute top-1/2 -translate-y-1/2 text-surface-400 pointer-events-none flex items-center justify-center" style="left: 1.25rem !important;">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama Santri atau Nama Wali..." 
                           class="w-full pr-4 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-medium text-surface-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors"
                           style="padding-left: 3.25rem !important;">
                </div>

                <button type="submit" class="btn-primary py-2.5 px-5 rounded-xl text-xs font-bold shrink-0">Cari Data</button>

                @if(request()->anyFilled(['search']))
                    <a href="{{ route('admin.keluarga.index') }}" class="btn-secondary px-3.5 py-2.5 rounded-xl text-xs font-bold text-danger-600 border-danger-200 hover:bg-danger-50 flex items-center justify-center gap-1 shrink-0">
                        <i data-lucide="x" class="w-4 h-4"></i>
                        <span>Reset</span>
                    </a>
                @endif
            </form>
        </div>

        {{-- Data Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-surface-100/70 text-surface-600 border-b border-surface-200 uppercase tracking-wider text-[0.68rem] font-bold">
                    <tr>
                        <th class="px-6 py-3.5">Identitas Santri / Anak</th>
                        <th class="px-6 py-4 font-semibold">Identitas Orang Tua / Wali</th>
                        <th class="px-6 py-4 font-semibold">Status Relasi</th>
                        <th class="px-6 py-4 font-semibold">Hak Akses Wali</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-100 text-surface-700">
                    @forelse($hubungannya as $rel)
                    <tr class="hover:bg-primary-50/30 transition-colors">
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-primary-100 text-primary-700 font-bold text-xs flex items-center justify-center shrink-0 border border-primary-200">
                                    {{ substr($rel->anak->nama_lengkap ?? 'S', 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-extrabold text-surface-900 text-sm leading-tight">{{ $rel->anak->nama_lengkap ?? 'Data Terhapus/Tidak Diketahui' }}</div>
                                    <div class="text-[0.68rem] text-primary-700 font-mono font-bold mt-0.5">{{ $rel->anak->niup ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-3.5">
                            <div class="font-bold text-surface-900 text-xs">{{ $rel->orangTuaAtauWali->nama_lengkap ?? 'Data Terhapus/Tidak Diketahui' }}</div>
                            <div class="text-[0.65rem] text-surface-500 font-medium mt-0.5">{{ $rel->orangTuaAtauWali->telepon ?? 'Tidak ada No. HP' }}</div>
                        </td>
                        <td class="px-6 py-3.5">
                            <div class="flex flex-col gap-1 items-start">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[0.65rem] font-extrabold uppercase bg-blue-100 text-blue-700">
                                    {{ $rel->hubungan }}
                                </span>
                                @if($rel->is_wali_utama)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[0.6rem] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">WALI UTAMA</span>
                                @endif
                                @if($rel->is_mahrom)
                                    <span class="text-[0.62rem] text-emerald-700 font-bold flex items-center gap-1 mt-0.5"><i data-lucide="shield-check" class="w-3 h-3 text-emerald-600"></i> Mahram</span>
                                @else
                                    <span class="text-[0.62rem] text-amber-700 font-bold flex items-center gap-1 mt-0.5"><i data-lucide="shield-alert" class="w-3 h-3 text-amber-600"></i> Non-Mahram</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-3.5">
                            <div class="flex gap-2 text-surface-400">
                                <i data-lucide="car" class="w-4 h-4 {{ $rel->boleh_jemput ? 'text-emerald-600' : '' }}" title="Hak Penjemputan"></i>
                                <i data-lucide="eye" class="w-4 h-4 {{ $rel->boleh_kunjungi ? 'text-emerald-600' : '' }}" title="Hak Kunjungan / Sambangan"></i>
                                <i data-lucide="phone" class="w-4 h-4 {{ $rel->boleh_komunikasi ? 'text-emerald-600' : '' }}" title="Hak Komunikasi"></i>
                            </div>
                        </td>
                        <td class="px-6 py-3.5 text-right">
                            <div class="inline-flex items-center justify-end gap-1.5 flex-wrap">
                                {{-- Edit Profil Wali --}}
                                @if($rel->orangTuaAtauWali)
                                <a href="{{ route('admin.keluarga.wali.edit', $rel->orangTuaAtauWali->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-amber-50 text-amber-700 hover:bg-amber-600 hover:text-white border border-amber-200 text-xs font-bold transition-all shadow-2xs" title="Edit Profil Wali (Nama, No HP)">
                                    <i data-lucide="user-edit" class="w-3.5 h-3.5"></i>
                                    <span>Profil</span>
                                </a>

                                {{-- Reset Password --}}
                                <form action="{{ route('admin.keluarga.wali.reset-password', $rel->orangTuaAtauWali->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Reset password akun portal untuk Wali ini menjadi nomor HP-nya?');">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-indigo-50 text-indigo-700 hover:bg-indigo-600 hover:text-white border border-indigo-200 text-xs font-bold transition-all shadow-2xs" title="Reset Password Portal ke No HP">
                                        <i data-lucide="key-round" class="w-3.5 h-3.5"></i>
                                        <span>Reset</span>
                                    </button>
                                </form>
                                @endif

                                {{-- Hapus --}}
                                <form action="{{ route('admin.keluarga.destroy', $rel) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus relasi keluarga ini? Data orang tidak akan terhapus.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-600 hover:text-white border border-rose-200 text-xs font-bold transition-all shadow-2xs" title="Hapus Relasi">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-surface-500">
                            <div class="flex flex-col items-center justify-center">
                                <i data-lucide="users" class="w-12 h-12 text-surface-300 mb-3"></i>
                                <p class="font-bold text-surface-900 mb-1">Belum Ada Relasi Keluarga</p>
                                <p class="text-xs text-surface-450">Tautkan data santri dengan orang tua atau wali mereka.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($hubungannya->hasPages())
        <div class="p-4 border-t border-surface-100">
            {{ $hubungannya->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
