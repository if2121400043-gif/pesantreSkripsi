@extends('layouts.app')

@section('title', 'Role & Permission')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Role & Hak Akses</h1>
        <p class="text-sm text-surface-500 mt-1">Kelola peran pengguna dan izin akses setiap modul.</p>
    </div>
    <div class="flex items-center gap-2">
        <button id="btn-add-role" onclick="openModal()" class="btn-primary flex items-center gap-2">
            <i data-lucide="shield-plus" class="w-4 h-4"></i>
            <span>Tambah Role</span>
        </button>
        <button id="btn-add-permission" onclick="openPermissionModal()" class="btn-primary flex items-center gap-2 hidden">
            <i data-lucide="key-round" class="w-4 h-4"></i>
            <span>Tambah Permission</span>
        </button>
    </div>
</div>
@endsection

@section('content')
{{-- Tabs Menu --}}
<div class="border-b border-surface-200 mb-6 bg-white rounded-xl p-1.5 flex gap-2 w-fit shadow-sm">
    <button onclick="switchTab('roles')" id="tab-btn-roles" class="tab-btn px-4 py-2 text-sm font-semibold rounded-lg bg-primary-600 text-white shadow-sm transition-all duration-200">
        Daftar Peran (Roles)
    </button>
    <button onclick="switchTab('permissions')" id="tab-btn-permissions" class="tab-btn px-4 py-2 text-sm font-semibold rounded-lg text-surface-600 hover:bg-surface-50 transition-all duration-200">
        Hak Akses (Permissions)
    </button>
</div>

{{-- Tab Content: Roles --}}
<div id="tab-roles" class="tab-content">
    <x-card :padding="false">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-surface-50/50 text-surface-600 border-b border-surface-100">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Role</th>
                        <th class="px-6 py-4 font-semibold">Kode Internal</th>
                        <th class="px-6 py-4 font-semibold text-center">Jumlah User</th>
                        <th class="px-6 py-4 font-semibold">Permissions</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-100 text-surface-700">
                    @forelse($roles as $role)
                    <tr class="hover:bg-surface-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-bold text-surface-900 flex items-center gap-2">
                                <i data-lucide="{{ $role->nama === 'SUPER_ADMIN' ? 'shield-alert' : 'shield' }}" class="w-4 h-4 {{ $role->nama === 'SUPER_ADMIN' ? 'text-purple-600' : 'text-primary-600' }}"></i>
                                {{ $role->label }}
                            </div>
                            @if($role->deskripsi)
                            <div class="text-xs text-surface-500 mt-0.5">{{ $role->deskripsi }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <code class="text-xs bg-surface-100 px-2 py-0.5 rounded font-mono">{{ $role->nama }}</code>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-bold bg-surface-100 text-surface-700">
                                <i data-lucide="users" class="w-3 h-3"></i> {{ $role->user_roles_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-normal max-w-xs">
                            @if($role->nama === 'SUPER_ADMIN')
                                <span class="inline-flex px-2 py-1 rounded text-[0.65rem] font-bold uppercase bg-purple-100 text-purple-700">FULL ACCESS</span>
                            @else
                                <div class="flex flex-wrap gap-1">
                                    @forelse($role->permissions->take(4) as $p)
                                        <span class="inline-flex px-1.5 py-0.5 border border-surface-200 rounded text-[0.6rem] bg-surface-50 text-surface-600">{{ $p->label }}</span>
                                    @empty
                                        <span class="text-surface-400 italic text-xs">Belum ada permission</span>
                                    @endforelse
                                    @if($role->permissions->count() > 4)
                                        <span class="inline-flex px-1.5 py-0.5 bg-surface-100 rounded text-[0.6rem] font-bold text-surface-700">+{{ $role->permissions->count() - 4 }}</span>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button onclick="editRole({{ json_encode($role) }}, {{ json_encode($role->permissions->pluck('id')) }})" class="text-primary-600 hover:text-primary-700 p-2 rounded-lg hover:bg-primary-50" title="Edit">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </button>
                            @if($role->nama !== 'SUPER_ADMIN')
                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus role ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-danger-500 hover:text-danger-600 p-2 rounded-lg hover:bg-danger-50" title="Hapus">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-12 text-center text-surface-500">Belum ada role.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>

{{-- Tab Content: Permissions --}}
<div id="tab-permissions" class="tab-content hidden">
    <x-card :padding="false">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-surface-50/50 text-surface-600 border-b border-surface-100">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Grup / Kategori</th>
                        <th class="px-6 py-4 font-semibold">Kode Internal</th>
                        <th class="px-6 py-4 font-semibold">Nama Hak Akses</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-100 text-surface-700">
                    @forelse($permissions as $p)
                    <tr class="hover:bg-surface-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold bg-surface-100 text-surface-700 uppercase tracking-wider">{{ $p->grup ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4 font-mono text-xs text-primary-600">{{ $p->nama }}</td>
                        <td class="px-6 py-4 font-bold text-surface-900">{{ $p->label }}</td>
                        <td class="px-6 py-4 text-right">
                            <form action="{{ route('admin.permissions.destroy', $p) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus hak akses ini? Tindakan ini akan memutuskan kaitan dari semua role.');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-danger-500 hover:text-danger-600 p-2 rounded-lg hover:bg-danger-50" title="Hapus">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-12 text-center text-surface-500">Belum ada permission.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>

{{-- Modal: Tambah/Edit Role --}}
<div id="modal-role" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-surface-900/50 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
        <form id="form-role" method="POST" action="{{ route('admin.roles.store') }}" class="bg-white rounded-2xl shadow-xl w-full max-w-xl flex flex-col overflow-hidden animate-scale-in" style="max-height: 80vh;">
            @csrf
            <input type="hidden" name="_method" id="form-method" value="POST">
            
            {{-- Modal Header --}}
            <div class="px-6 py-4 border-b border-surface-100 bg-surface-50 flex justify-between items-center flex-shrink-0">
                <h3 class="font-bold text-surface-900 font-heading" id="modal-title">Tambah Role</h3>
                <button type="button" onclick="closeModal()" class="text-surface-400 hover:text-surface-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            {{-- Modal Body --}}
            <div class="px-6 py-4 space-y-4 flex-1 overflow-y-auto">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Kode Internal <span class="text-danger-500">*</span></label>
                        <input type="text" name="nama" id="f-nama" required placeholder="Contoh: BENDAHARA" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 uppercase">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Label Tampilan <span class="text-danger-500">*</span></label>
                        <input type="text" name="label" id="f-label" required placeholder="Contoh: Bendahara" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Redirect URL Setelah Login</label>
                    <input type="text" name="redirect_url" id="f-redirect" placeholder="/admin/dashboard" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Deskripsi Singkat</label>
                    <input type="text" name="deskripsi" id="f-deskripsi" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                </div>
                <div class="border-t border-surface-100 pt-4">
                    <label class="block text-sm font-bold text-surface-900 mb-1">Hak Akses (Permissions)</label>
                    <p class="text-xs text-surface-500 mb-3">Centang modul yang boleh diakses oleh role ini.</p>
                    @php $grouped = $permissions->groupBy('grup'); @endphp
                    <div class="space-y-3 max-h-60 overflow-y-auto border border-surface-200 rounded-lg p-3 bg-surface-50/50">
                        @foreach($grouped as $grup => $perms)
                        <div>
                            <div class="text-[0.65rem] font-bold text-surface-500 uppercase tracking-wider mb-1">{{ $grup }}</div>
                            <div class="grid grid-cols-2 gap-1">
                                @foreach($perms as $p)
                                <label class="flex items-center gap-2 p-1.5 hover:bg-white rounded cursor-pointer transition-colors">
                                    <input type="checkbox" name="permission_ids[]" value="{{ $p->id }}" class="perm-cb text-primary-600 rounded border-surface-300 text-sm">
                                    <span class="text-xs font-medium text-surface-700">{{ $p->label }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            {{-- Modal Footer --}}
            <div class="px-6 py-4 bg-surface-50 border-t border-surface-100 flex justify-end gap-3 flex-shrink-0 rounded-b-2xl">
                <button type="button" onclick="closeModal()" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary">Simpan Role</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Tambah Permission --}}
<div id="modal-permission" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-surface-900/50 backdrop-blur-sm" onclick="closePermissionModal()"></div>
    <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
        <form action="{{ route('admin.permissions.store') }}" method="POST" class="bg-white rounded-2xl shadow-xl w-full max-w-md flex flex-col overflow-hidden animate-scale-in" style="max-height: 80vh;">
            @csrf
            
            {{-- Modal Header --}}
            <div class="px-6 py-4 border-b border-surface-100 bg-surface-50 flex justify-between items-center flex-shrink-0">
                <h3 class="font-bold text-surface-900 font-heading">Tambah Hak Akses (Permission)</h3>
                <button type="button" onclick="closePermissionModal()" class="text-surface-400 hover:text-surface-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            {{-- Modal Body --}}
            <div class="px-6 py-4 space-y-4 flex-1 overflow-y-auto">
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Kode Internal (Unique) <span class="text-danger-500">*</span></label>
                    <input type="text" name="nama" required placeholder="Contoh: manage-users" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    <p class="text-[0.65rem] text-surface-400 mt-1">Gunakan huruf kecil dan tanda hubung (-) tanpa spasi.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Label Tampilan <span class="text-danger-500">*</span></label>
                    <input type="text" name="label" required placeholder="Contoh: Kelola Pengguna" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Kategori / Grup <span class="text-danger-500">*</span></label>
                    <input type="text" name="grup" list="group-list" required placeholder="Pilih atau ketik grup baru..." class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    <datalist id="group-list">
                        @foreach($permissions->pluck('grup')->unique() as $g)
                            <option value="{{ $g }}">
                        @endforeach
                    </datalist>
                </div>
            </div>
            {{-- Modal Footer --}}
            <div class="px-6 py-4 bg-surface-50 border-t border-surface-100 flex justify-end gap-3 flex-shrink-0 rounded-b-2xl">
                <button type="button" onclick="closePermissionModal()" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary">Simpan Permission</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const modal = document.getElementById('modal-role');
const form = document.getElementById('form-role');
const permModal = document.getElementById('modal-permission');

function switchTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.getElementById('tab-' + tab).classList.remove('hidden');

    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('bg-primary-600', 'text-white', 'shadow-sm');
        btn.classList.add('text-surface-600', 'hover:bg-surface-50');
    });

    const activeBtn = document.getElementById('tab-btn-' + tab);
    activeBtn.classList.add('bg-primary-600', 'text-white', 'shadow-sm');
    activeBtn.classList.remove('text-surface-600', 'hover:bg-surface-50');

    // Toggle header action buttons
    if (tab === 'roles') {
        document.getElementById('btn-add-role').classList.remove('hidden');
        document.getElementById('btn-add-permission').classList.add('hidden');
    } else {
        document.getElementById('btn-add-role').classList.add('hidden');
        document.getElementById('btn-add-permission').classList.remove('hidden');
    }
}

function openModal() {
    form.reset();
    form.action = "{{ route('admin.roles.store') }}";
    document.getElementById('form-method').value = 'POST';
    document.getElementById('modal-title').innerText = 'Tambah Role Baru';
    document.querySelectorAll('.perm-cb').forEach(cb => cb.checked = false);
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function editRole(data, permIds) {
    form.reset();
    form.action = `/admin/roles/${data.id}`;
    document.getElementById('form-method').value = 'PUT';
    document.getElementById('modal-title').innerText = 'Edit Role: ' + data.label;
    document.getElementById('f-nama').value = data.nama;
    document.getElementById('f-label').value = data.label;
    document.getElementById('f-redirect').value = data.redirect_url || '';
    document.getElementById('f-deskripsi').value = data.deskripsi || '';
    document.querySelectorAll('.perm-cb').forEach(cb => {
        cb.checked = permIds.includes(parseInt(cb.value));
    });
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeModal() { 
    modal.classList.add('hidden'); 
    document.body.classList.remove('overflow-hidden');
}

function openPermissionModal() {
    permModal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closePermissionModal() {
    permModal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}
</script>
@endpush
