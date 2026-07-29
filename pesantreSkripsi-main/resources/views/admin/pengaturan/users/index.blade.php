@extends('layouts.app')

@section('title', 'Manajemen User')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Manajemen User</h1>
        <p class="text-sm text-surface-500 mt-1">Kelola akun pengguna dan hak akses mereka ke dalam sistem.</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn-primary flex items-center gap-2">
        <i data-lucide="user-plus" class="w-4 h-4"></i>
        <span>Tambah User</span>
    </a>
</div>
@endsection

@section('content')
<x-card :padding="false">
    {{-- Filter --}}
    <div class="p-4 border-b border-surface-100 bg-surface-50">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1 relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari username, email, atau nama..." 
                       class="w-full pl-10 pr-4 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
            </div>
            <div class="sm:w-56">
                <select name="role_id" class="w-full px-4 py-2 rounded-lg border border-surface-300 bg-white text-sm" onchange="this.form.submit()">
                    <option value="">Semua Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>{{ $role->label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-secondary px-4 py-2 hidden sm:block">Filter</button>
        </form>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-surface-50/50 text-surface-600 border-b border-surface-100">
                <tr>
                    <th class="px-6 py-4 font-semibold">User</th>
                    <th class="px-6 py-4 font-semibold">Identitas (NIUP)</th>
                    <th class="px-6 py-4 font-semibold">Role / Peran</th>
                    <th class="px-6 py-4 font-semibold text-center">Status</th>
                    <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-100 text-surface-700">
                @forelse($users as $u)
                <tr class="hover:bg-surface-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                {{ strtoupper(substr($u->username, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-bold text-surface-900">{{ $u->username }}</div>
                                <div class="text-xs text-surface-500">{{ $u->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($u->orang)
                            <div class="font-medium text-surface-900">{{ $u->orang->nama_lengkap }}</div>
                            <div class="text-xs text-primary-600 font-mono">{{ $u->orang->niup }}</div>
                        @else
                            <span class="text-surface-400 italic text-xs">Belum terhubung</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1">
                            @forelse($u->roles as $ur)
                                <span class="inline-flex px-2 py-0.5 rounded text-[0.65rem] font-bold uppercase tracking-wider {{ $ur->role->nama === 'SUPER_ADMIN' ? 'bg-purple-100 text-purple-700' : 'bg-surface-200 text-surface-700' }}">
                                    {{ $ur->role->label }}
                                </span>
                            @empty
                                <span class="text-surface-400 italic text-xs">Belum ada role</span>
                            @endforelse
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($u->is_active)
                            <x-badge variant="success" dot>Aktif</x-badge>
                        @else
                            <x-badge variant="danger" dot>Nonaktif</x-badge>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if($u->id !== auth()->id())
                            <a href="{{ route('admin.users.edit', $u) }}" class="text-primary-600 hover:text-primary-700 p-2 rounded-lg hover:bg-primary-50 inline-block" title="Edit">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </a>
                            <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus user ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-danger-500 hover:text-danger-600 p-2 rounded-lg hover:bg-danger-50" title="Hapus">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        @else
                            <span class="text-xs text-surface-400 italic">Anda</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-12 text-center text-surface-500">Belum ada user.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="p-4 border-t border-surface-100">{{ $users->links() }}</div>
    @endif
</x-card>

@endsection
