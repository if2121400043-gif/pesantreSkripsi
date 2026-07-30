@extends('layouts.app')

@section('title', 'Tambah User Baru')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <div class="flex items-center gap-2 text-sm text-surface-500 mb-2">
            <a href="{{ route('admin.users.index') }}" class="hover:text-primary-600 transition-colors">Manajemen User</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <span class="text-surface-900 font-medium">Tambah User</span>
        </div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Tambah User Baru</h1>
    </div>
    <a href="{{ route('admin.users.index') }}" class="btn-secondary flex items-center gap-2">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Kembali</span>
    </a>
</div>
@endsection

@section('content')
<form action="{{ route('admin.users.store') }}" method="POST" class="max-w-4xl mx-auto space-y-6">
    @csrf

    {{-- Notifikasi Error --}}
    @if($errors->any())
        <div class="bg-danger-50 text-danger-700 p-4 rounded-xl border border-danger-200">
            <div class="flex gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0 mt-0.5"></i>
                <div>
                    <h3 class="font-semibold mb-1">Terdapat kesalahan pengisian:</h3>
                    <ul class="list-disc pl-5 space-y-1 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Kolom Kiri: Input Data --}}
        <div class="lg:col-span-2 space-y-6">
            <x-card title="Informasi Akun">
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-form-input name="username" label="Username" required value="{{ old('username') }}" placeholder="Contoh: bendahara1" />
                        <x-form-input type="email" name="email" label="Alamat Email" required value="{{ old('email') }}" placeholder="Contoh: bendahara@pesantren.id" />
                    </div>

                    <x-form-input type="password" name="password" label="Password" required placeholder="Minimal 8 karakter" autocomplete="new-password" />
                </div>
            </x-card>

            <x-card title="Hubungkan Biodata">
                <div class="space-y-4">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-surface-700">Hubungkan dengan Data Orang (Biodata Pegawai / Wali)</label>
                        <div class="relative" id="search_box_container">
                            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400"></i>
                            <input type="text" id="search_orang" placeholder="Ketik nama atau NIUP (Min 3 huruf)..." 
                                   class="w-full pl-10 pr-4 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors">
                        </div>
                        <div id="search_results" class="absolute bg-white border border-surface-200 rounded-lg shadow-lg max-h-48 overflow-y-auto hidden z-20 w-[95%] max-w-lg"></div>
                        
                        <input type="hidden" name="orang_id" id="orang_id" value="{{ old('orang_id') }}">
                        
                        <div id="selected_orang" class="hidden flex justify-between items-center bg-primary-50 border border-primary-200 p-3 rounded-lg text-sm">
                            <div>
                                <span class="font-bold text-primary-800" id="selected_orang_name"></span>
                                <p class="text-xs text-primary-600">Akun ini akan terhubung dengan biodata di atas.</p>
                            </div>
                            <button type="button" onclick="clearOrang()" class="text-primary-600 hover:text-danger-600">
                                <i data-lucide="x-circle" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </x-card>

            <x-card title="Hak Akses (Role)">
                <div>
                    <label class="block text-sm font-bold text-surface-900 mb-3">Pilih Satu atau Lebih Peran <span class="text-danger-500">*</span></label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($roles as $role)
                        <label class="flex items-center gap-3 p-3 border border-surface-200 rounded-xl hover:bg-surface-50 cursor-pointer transition-all">
                            <input type="checkbox" name="role_ids[]" value="{{ $role->id }}" 
                                   {{ is_array(old('role_ids')) && in_array($role->id, old('role_ids')) ? 'checked' : '' }}
                                   class="role-cb text-primary-600 rounded border-surface-300 w-5 h-5 focus:ring-primary-500/20">
                            <div>
                                <span class="text-sm font-bold text-surface-900">{{ $role->label }}</span>
                                @if($role->deskripsi)
                                <span class="text-xs text-surface-500 block mt-0.5">{{ $role->deskripsi }}</span>
                                @endif
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
            </x-card>
        </div>

        {{-- Kolom Kanan: Pengaturan --}}
        <div class="lg:col-span-1 space-y-6">
            <x-card title="Status Pengguna">
                <div class="space-y-4">
                    <div class="flex items-center gap-3 p-3 bg-surface-50 border border-surface-200 rounded-xl">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} 
                               class="w-5 h-5 rounded border-surface-300 text-primary-600 focus:ring-primary-500">
                        <label for="is_active" class="text-sm font-medium text-surface-900 cursor-pointer">
                            Akun Aktif (Dapat Login)
                        </label>
                    </div>

                    <div class="pt-4 border-t border-surface-100">
                        <button type="submit" class="btn-primary w-full justify-center flex items-center gap-2 py-3">
                            <i data-lucide="save" class="w-5 h-5"></i>
                            <span class="font-bold">Simpan User</span>
                        </button>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    // AJAX Live Search for Orang
    const searchInput = document.getElementById('search_orang');
    const searchResults = document.getElementById('search_results');
    const hiddenId = document.getElementById('orang_id');
    const selectedOrang = document.getElementById('selected_orang');
    const selectedOrangName = document.getElementById('selected_orang_name');
    let timeout = null;

    searchInput.addEventListener('input', function() {
        clearTimeout(timeout);
        const query = this.value;
        
        if (query.length < 3) {
            searchResults.classList.add('hidden');
            return;
        }
        
        timeout = setTimeout(() => {
            fetch(`/admin/api/search-orang?q=${query}`)
                .then(res => res.json())
                .then(data => {
                    searchResults.innerHTML = '';
                    if (data.length === 0) {
                        searchResults.innerHTML = '<div class="p-3 text-sm text-surface-500 bg-white">Biodata tidak ditemukan</div>';
                    } else {
                        data.forEach(item => {
                            const div = document.createElement('div');
                            div.className = 'p-3 text-sm hover:bg-surface-50 cursor-pointer border-b border-surface-100 last:border-0 bg-white';
                            div.textContent = item.text;
                            div.onclick = function() {
                                hiddenId.value = item.id;
                                selectedOrangName.textContent = item.text;
                                selectedOrang.classList.remove('hidden');
                                document.getElementById('search_box_container').classList.add('hidden');
                                searchResults.classList.add('hidden');
                            };
                            searchResults.appendChild(div);
                        });
                    }
                    searchResults.classList.remove('hidden');
                });
        }, 300);
    });

    // Close search results when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchResults.contains(e.target) && e.target !== searchInput) {
            searchResults.classList.add('hidden');
        }
    });

    function clearOrang() {
        hiddenId.value = '';
        searchInput.value = '';
        selectedOrang.classList.add('hidden');
        document.getElementById('search_box_container').classList.remove('hidden');
        searchInput.focus();
    }
</script>
@endpush
