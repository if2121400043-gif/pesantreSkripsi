@extends('layouts.app')

@section('title', 'Manajemen Relasi Keluarga — PP Nurul Furqon')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 2.75rem !important;
        border-radius: 0.75rem !important;
        border-color: #cbd5e1 !important;
        display: flex !important;
        align-items: center !important;
        padding-left: 0.5rem !important;
        background-color: #ffffff !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #0f172a !important;
        font-size: 0.8125rem !important;
        font-weight: 600 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 2.75rem !important;
        right: 0.75rem !important;
    }
    .select2-dropdown {
        border-radius: 0.75rem !important;
        border-color: #cbd5e1 !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15) !important;
        overflow: hidden !important;
        z-index: 9999 !important;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border-radius: 0.5rem !important;
        border-color: #cbd5e1 !important;
        padding: 0.5rem 0.75rem !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #047857 !important;
        color: #ffffff !important;
    }
</style>
@endpush

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Hubungan Keluarga & Wali</h1>
        <p class="text-sm text-surface-500 mt-1">Kelola relasi antara santri dengan orang tua/wali serta hak akses perizinan.</p>
    </div>
    <button onclick="openModal()" class="btn-primary flex items-center gap-2 py-2.5 px-4 rounded-xl font-bold text-xs shadow-md shadow-primary-700/20">
        <i data-lucide="plus" class="w-4 h-4"></i>
        <span>+ Tambah Relasi Baru</span>
    </button>
</div>
@endsection

@section('content')
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
                        <!-- Group Relasi -->
                        <div class="inline-flex items-center gap-1 border-r border-surface-200 pr-2 mr-1">
                            <button onclick="editRelasi({{ json_encode($rel) }})" class="text-primary-600 hover:text-primary-800 p-1.5 rounded-lg hover:bg-primary-50 transition-colors" title="Edit Relasi (Status, Hak Akses)">
                                <i data-lucide="link" class="w-4 h-4"></i>
                            </button>
                            <form action="{{ route('admin.keluarga.destroy', $rel) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus relasi keluarga ini? Data orang tidak akan terhapus.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-500 hover:text-rose-700 p-1.5 rounded-lg hover:bg-rose-50 transition-colors" title="Hapus Relasi">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                        
                        <!-- Group Profil Wali -->
                        <div class="inline-flex items-center gap-1">
                            <button onclick="editProfilWali({{ json_encode($rel->orangTuaAtauWali) }})" class="text-amber-600 hover:text-amber-700 p-1.5 rounded-lg hover:bg-amber-50 transition-colors" title="Edit Profil Wali (Nama, No HP)">
                                <i data-lucide="user-edit" class="w-4 h-4"></i>
                            </button>
                            <form action="{{ route('admin.keluarga.wali.reset-password', $rel->orangTuaAtauWali->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Reset password akun portal untuk Wali ini menjadi nomor HP-nya?');">
                                @csrf
                                <button type="submit" class="text-indigo-600 hover:text-indigo-700 p-1.5 rounded-lg hover:bg-indigo-50 transition-colors" title="Reset Password Portal ke No HP">
                                    <i data-lucide="key-round" class="w-4 h-4"></i>
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

{{-- Modal Tambah / Edit Relasi --}}
<div id="modal-relasi" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-surface-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
    
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-visible rounded-3xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-surface-200">
                <div class="px-6 py-4 border-b border-surface-100 flex justify-between items-center bg-surface-50 rounded-t-3xl">
                    <h3 class="text-base font-bold text-surface-900 font-heading" id="modal-title">Tambah Relasi Keluarga</h3>
                    <button type="button" onclick="closeModal()" class="text-surface-400 hover:text-surface-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <form id="form-relasi" action="{{ route('admin.keluarga.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="form-method" value="POST">
                    
                    <div class="px-6 py-4 space-y-4 text-xs">
                        <div id="wrapper_orang_id">
                            <label class="block font-bold text-surface-700 mb-1">Pilih Identitas Santri/Anak <span class="text-danger-500">*</span></label>
                            <select name="orang_id" id="orang_id" required class="select2-modal w-full">
                                <option value="" disabled selected>Ketik nama atau NIUP santri/anak...</option>
                                @foreach($semuaOrang as $o)
                                    <option value="{{ $o->id }}">{{ $o->nama_lengkap }} — (NIUP: {{ $o->niup }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="wrapper_keluarga_id">
                            <div class="flex items-center gap-4 mb-3 border-b border-surface-200 pb-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="mode_wali" value="existing" checked onchange="toggleModeWali()" class="text-primary-600 focus:ring-primary-500">
                                    <span class="font-bold text-surface-700">Pilih Data Master</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="mode_wali" value="new" onchange="toggleModeWali()" class="text-primary-600 focus:ring-primary-500">
                                    <span class="font-bold text-surface-700">Input Wali Baru</span>
                                </label>
                            </div>

                            <!-- Mode Existing -->
                            <div id="mode_existing">
                                <label class="block font-bold text-surface-700 mb-1">Pilih Identitas Wali <span class="text-danger-500">*</span></label>
                                <select name="keluarga_id" id="keluarga_id" class="select2-modal w-full">
                                    <option value="" disabled selected>Ketik nama atau NIUP wali...</option>
                                    @foreach($semuaOrang as $o)
                                        <option value="{{ $o->id }}">{{ $o->nama_lengkap }} — (NIUP: {{ $o->niup }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Mode New -->
                            <div id="mode_new" class="hidden space-y-3 bg-primary-50 p-3.5 rounded-2xl border border-primary-100">
                                <div>
                                    <label class="block font-bold text-surface-700 mb-1">Nama Lengkap Wali <span class="text-danger-500">*</span></label>
                                    <input type="text" name="nama_wali" id="nama_wali" class="w-full rounded-xl border border-surface-300 bg-white px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                                </div>
                                <div>
                                    <label class="block font-bold text-surface-700 mb-1">Nomor WA / Telepon <span class="text-danger-500">*</span></label>
                                    <input type="text" name="telepon_wali" id="telepon_wali" class="w-full rounded-xl border border-surface-300 bg-white px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500" placeholder="08xxxxxxxxxx">
                                    <p class="text-[0.68rem] text-primary-700 font-semibold mt-1"><i data-lucide="info" class="w-3 h-3 inline"></i> Nomor ini akan menjadi Username & Password portal.</p>
                                </div>
                                <div>
                                    <label class="block font-bold text-surface-700 mb-1">Email (Opsional)</label>
                                    <input type="email" name="email_wali" id="email_wali" class="w-full rounded-xl border border-surface-300 bg-white px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500" placeholder="wali@example.com">
                                </div>
                                <div>
                                    <label class="block font-bold text-surface-700 mb-1">Alamat (Opsional)</label>
                                    <input type="text" name="alamat_wali" class="w-full rounded-xl border border-surface-300 bg-white px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-surface-700 mb-1">Hubungan <span class="text-danger-500">*</span></label>
                            <select name="hubungan" id="hubungan" required class="w-full rounded-xl border border-surface-300 bg-white px-3.5 py-2.5 text-xs font-semibold focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                                <option value="AYAH">Ayah Kandung</option>
                                <option value="IBU">Ibu Kandung</option>
                                <option value="WALI">Wali (Selain Orang Tua)</option>
                                <option value="KAKAK">Kakak</option>
                                <option value="ADIK">Adik</option>
                                <option value="PAMAN">Paman (Pakde/Om)</option>
                                <option value="BIBI">Bibi (Bude/Tante)</option>
                                <option value="KAKEK">Kakek</option>
                                <option value="NENEK">Nenek</option>
                                <option value="LAINNYA">Lainnya</option>
                            </select>
                        </div>

                        <div class="bg-surface-50 p-4 border border-surface-200 rounded-2xl space-y-3">
                            <p class="font-bold text-surface-900 mb-2 border-b border-surface-200 pb-2">Hak Akses & Aturan Syariat</p>
                            
                            <label class="flex items-start gap-2 cursor-pointer">
                                <input type="checkbox" name="is_mahrom" id="is_mahrom" value="1" class="mt-0.5 rounded border-surface-300 text-primary-600 focus:ring-primary-500 w-4 h-4">
                                <div>
                                    <span class="font-bold text-surface-900">Mahram secara Syariat</span>
                                    <p class="text-[0.65rem] text-surface-500">Mempengaruhi perizinan pertemuan santri putri dengan wali laki-laki.</p>
                                </div>
                            </label>
                            
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_wali_utama" id="is_wali_utama" value="1" class="rounded border-surface-300 text-primary-600 focus:ring-primary-500 w-4 h-4">
                                <span class="font-bold text-surface-900">Jadikan Wali Utama (Penanggung Jawab Keuangan & Akademik)</span>
                            </label>

                            <div class="grid grid-cols-3 gap-2 pt-2 border-t border-surface-100">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="boleh_jemput" id="boleh_jemput" value="1" checked class="rounded border-surface-300 text-primary-600 focus:ring-primary-500 w-4 h-4">
                                    <span class="text-[0.68rem] font-bold text-surface-900">Boleh Menjemput</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="boleh_kunjungi" id="boleh_kunjungi" value="1" checked class="rounded border-surface-300 text-primary-600 focus:ring-primary-500 w-4 h-4">
                                    <span class="text-[0.68rem] font-bold text-surface-900">Boleh Kunjungan</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="boleh_komunikasi" id="boleh_komunikasi" value="1" checked class="rounded border-surface-300 text-primary-600 focus:ring-primary-500 w-4 h-4">
                                    <span class="text-[0.68rem] font-bold text-surface-900">Boleh Komunikasi</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-surface-700 mb-1">Catatan Tambahan</label>
                            <input type="text" name="catatan" id="catatan" class="w-full rounded-xl border border-surface-300 bg-white px-3.5 py-2.5 text-xs font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 bg-surface-50 border-t border-surface-100 flex justify-end gap-3 rounded-b-3xl">
                        <button type="button" onclick="closeModal()" class="btn-secondary text-xs font-bold py-2 px-4 rounded-xl">Batal</button>
                        <button type="submit" class="btn-primary text-xs font-bold py-2 px-5 rounded-xl">Simpan Relasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit Profil Wali --}}
<div id="modal-profil-wali" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title-profil" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-surface-900/50 backdrop-blur-sm transition-opacity" onclick="closeModalProfil()"></div>
    
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-surface-200">
                <div class="px-6 py-4 border-b border-surface-100 flex justify-between items-center bg-surface-50 rounded-t-3xl">
                    <h3 class="text-base font-bold text-surface-900 font-heading" id="modal-title-profil">Edit Profil Wali</h3>
                    <button type="button" onclick="closeModalProfil()" class="text-surface-400 hover:text-surface-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <form id="form-profil-wali" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="px-6 py-4 space-y-4 text-xs">
                        <div>
                            <label class="block font-bold text-surface-700 mb-1">Nama Lengkap Wali <span class="text-danger-500">*</span></label>
                            <input type="text" name="nama_lengkap" id="edit_nama_wali" required class="w-full rounded-xl border border-surface-300 bg-white px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block font-bold text-surface-700 mb-1">Nomor WhatsApp / Telepon <span class="text-danger-500">*</span></label>
                            <input type="text" name="telepon" id="edit_telepon_wali" required class="w-full rounded-xl border border-surface-300 bg-white px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                            <p class="text-[0.68rem] text-amber-700 font-semibold mt-1"><i data-lucide="alert-circle" class="w-3 h-3 inline"></i> Mengubah nomor HP TIDAK otomatis mengubah password portal.</p>
                        </div>
                        <div>
                            <label class="block font-bold text-surface-700 mb-1">Alamat Lengkap</label>
                            <textarea name="alamat_lengkap" id="edit_alamat_wali" rows="2" class="w-full rounded-xl border border-surface-300 bg-white px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500"></textarea>
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 bg-surface-50 border-t border-surface-100 flex justify-end gap-3 rounded-b-3xl">
                        <button type="button" onclick="closeModalProfil()" class="btn-secondary text-xs font-bold py-2 px-4 rounded-xl">Batal</button>
                        <button type="submit" class="btn-primary text-xs font-bold py-2 px-5 rounded-xl">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-modal').select2({
            placeholder: 'Ketik nama atau NIUP...',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#modal-relasi')
        });
    });

    const modal = document.getElementById('modal-relasi');
    const form = document.getElementById('form-relasi');
    const title = document.getElementById('modal-title');
    const methodInput = document.getElementById('form-method');

    function openModal() {
        form.reset();
        form.action = "{{ route('admin.keluarga.store') }}";
        methodInput.value = "POST";
        title.innerText = "Tambah Relasi Keluarga";
        
        document.getElementById('wrapper_orang_id').style.display = 'block';
        document.getElementById('wrapper_keluarga_id').style.display = 'block';
        document.getElementById('orang_id').required = true;
        
        toggleModeWali();
        
        document.getElementById('boleh_jemput').checked = true;
        document.getElementById('boleh_kunjungi').checked = true;
        document.getElementById('boleh_komunikasi').checked = true;
        
        modal.classList.remove('hidden');
    }

    function toggleModeWali() {
        const mode = document.querySelector('input[name="mode_wali"]:checked');
        const isNew = mode && mode.value === 'new';
        
        document.getElementById('mode_existing').style.display = isNew ? 'none' : 'block';
        document.getElementById('mode_new').style.display = isNew ? 'block' : 'none';
        
        document.getElementById('keluarga_id').required = !isNew;
        document.getElementById('nama_wali').required = isNew;
        document.getElementById('telepon_wali').required = isNew;
    }

    function editRelasi(data) {
        form.action = `/admin/keluarga/${data.id}`;
        methodInput.value = "PUT";
        title.innerText = "Edit Relasi: " + data.anak.nama_lengkap + " & " + data.orang_tua_atau_wali.nama_lengkap;
        
        // Hide selects on edit since pivot keys shouldn't easily change
        document.getElementById('wrapper_orang_id').style.display = 'none';
        document.getElementById('wrapper_keluarga_id').style.display = 'none';
        document.getElementById('orang_id').required = false;
        document.getElementById('keluarga_id').required = false;
        
        document.getElementById('hubungan').value = data.hubungan;
        document.getElementById('is_mahrom').checked = data.is_mahrom;
        document.getElementById('is_wali_utama').checked = data.is_wali_utama;
        document.getElementById('boleh_jemput').checked = data.boleh_jemput;
        document.getElementById('boleh_kunjungi').checked = data.boleh_kunjungi;
        document.getElementById('boleh_komunikasi').checked = data.boleh_komunikasi;
        document.getElementById('catatan').value = data.catatan || '';
        
        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
    }

    function editProfilWali(orang) {
        const modalProfil = document.getElementById('modal-profil-wali');
        const formProfil = document.getElementById('form-profil-wali');
        
        formProfil.action = `/admin/keluarga/wali/${orang.id}/update`;
        document.getElementById('edit_nama_wali').value = orang.nama_lengkap;
        document.getElementById('edit_telepon_wali').value = orang.telepon || '';
        document.getElementById('edit_alamat_wali').value = orang.alamat_lengkap || '';
        
        modalProfil.classList.remove('hidden');
    }

    function closeModalProfil() {
        document.getElementById('modal-profil-wali').classList.add('hidden');
    }
</script>
@endpush
