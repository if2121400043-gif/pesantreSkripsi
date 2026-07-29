@extends('layouts.app')

@section('title', 'Master Wilayah')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Master Wilayah Indonesia</h1>
        <p class="text-sm text-surface-500 mt-1">Kelola hierarki wilayah: Provinsi → Kabupaten/Kota → Kecamatan → Desa/Kelurahan.</p>
    </div>
    <button onclick="openModalProvinsi()" class="btn-primary flex items-center gap-2">
        <i data-lucide="plus" class="w-4 h-4"></i>
        <span>Tambah Provinsi</span>
    </button>
</div>
@endsection

@section('content')
{{-- Breadcrumb Navigation --}}
<div class="flex items-center gap-2 text-sm mb-4 flex-wrap">
    <a href="{{ route('admin.wilayah.index') }}" class="font-bold text-primary-600 hover:underline">Semua Provinsi</a>
    @if($selectedProvinsi)
        <i data-lucide="chevron-right" class="w-4 h-4 text-surface-400"></i>
        <a href="{{ route('admin.wilayah.index', ['provinsi_id' => $selectedProvinsi->id]) }}" class="font-bold text-primary-600 hover:underline">{{ $selectedProvinsi->nama }}</a>
    @endif
    @if($selectedKabupaten)
        <i data-lucide="chevron-right" class="w-4 h-4 text-surface-400"></i>
        <a href="{{ route('admin.wilayah.index', ['provinsi_id' => $selectedProvinsi->id, 'kabupaten_id' => $selectedKabupaten->id]) }}" class="font-bold text-primary-600 hover:underline">{{ $selectedKabupaten->nama }}</a>
    @endif
    @if($selectedKecamatan)
        <i data-lucide="chevron-right" class="w-4 h-4 text-surface-400"></i>
        <span class="font-bold text-surface-900">{{ $selectedKecamatan->nama }}</span>
    @endif
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- LEFT: Provinsi List (always visible) --}}
    <x-card title="Daftar Provinsi" :padding="false">
        <div class="divide-y divide-surface-100">
            @forelse($provinsis as $prov)
            <div class="flex items-center justify-between px-5 py-3 hover:bg-surface-50/50 transition-colors group {{ $selectedProvinsi && $selectedProvinsi->id == $prov->id ? 'bg-primary-50 border-l-4 border-l-primary-500' : '' }}">
                <a href="{{ route('admin.wilayah.index', ['provinsi_id' => $prov->id]) }}" class="flex-1">
                    <div class="font-bold text-surface-900">{{ $prov->nama }}</div>
                    <div class="text-xs text-surface-500">Kode: {{ $prov->kode }} · {{ $prov->kabupaten_count }} Kab/Kota</div>
                </a>
                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button onclick="editProvinsi({{ json_encode($prov) }})" class="p-1.5 rounded hover:bg-primary-100 text-primary-600"><i data-lucide="edit" class="w-3.5 h-3.5"></i></button>
                    <form action="{{ route('admin.wilayah.provinsi.destroy', $prov) }}" method="POST" onsubmit="return confirm('Hapus provinsi ini?');" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1.5 rounded hover:bg-danger-100 text-danger-500"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i></button>
                    </form>
                </div>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-surface-500">
                <i data-lucide="map" class="w-10 h-10 text-surface-300 mx-auto mb-2"></i>
                <p class="text-sm">Belum ada data provinsi.</p>
            </div>
            @endforelse
        </div>
        
        @if($provinsis instanceof \Illuminate\Pagination\LengthAwarePaginator && $provinsis->hasPages())
        <div class="p-4 border-t border-surface-100 bg-surface-50">
            {{ $provinsis->links('pagination::tailwind') }}
        </div>
        @endif
    </x-card>

    {{-- RIGHT: Child data based on selection --}}
    @if($selectedProvinsi && !$selectedKabupaten)
    {{-- Kabupaten List --}}
    <x-card :padding="false">
        <div class="px-5 py-3 border-b border-surface-100 bg-surface-50 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-surface-900">Kabupaten/Kota di {{ $selectedProvinsi->nama }}</h3>
                <p class="text-xs text-surface-500">{{ $kabupatens->count() }} data</p>
            </div>
            <button onclick="openModalKabupaten()" class="btn-primary py-1.5 px-3 text-xs flex items-center gap-1">
                <i data-lucide="plus" class="w-3 h-3"></i> Tambah
            </button>
        </div>
        <div class="divide-y divide-surface-100 max-h-[500px] overflow-y-auto">
            @forelse($kabupatens as $kab)
            <div class="flex items-center justify-between px-5 py-3 hover:bg-surface-50/50 transition-colors group">
                <a href="{{ route('admin.wilayah.index', ['provinsi_id' => $selectedProvinsi->id, 'kabupaten_id' => $kab->id]) }}" class="flex-1">
                    <div class="font-bold text-surface-900">{{ $kab->nama }}</div>
                    <div class="text-xs text-surface-500">Kode: {{ $kab->kode }} · {{ $kab->kecamatan_count }} Kecamatan</div>
                </a>
                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button onclick="editKabupaten({{ json_encode($kab) }})" class="p-1.5 rounded hover:bg-primary-100 text-primary-600"><i data-lucide="edit" class="w-3.5 h-3.5"></i></button>
                    <form action="{{ route('admin.wilayah.kabupaten.destroy', $kab) }}" method="POST" onsubmit="return confirm('Hapus?');" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1.5 rounded hover:bg-danger-100 text-danger-500"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i></button>
                    </form>
                </div>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-surface-500 text-sm">Belum ada Kabupaten/Kota.</div>
            @endforelse
        </div>
        
        @if($kabupatens instanceof \Illuminate\Pagination\LengthAwarePaginator && $kabupatens->hasPages())
        <div class="p-4 border-t border-surface-100 bg-surface-50">
            {{ $kabupatens->links('pagination::tailwind') }}
        </div>
        @endif
    </x-card>
    @endif

    @if($selectedKabupaten && !$selectedKecamatan)
    {{-- Kecamatan List --}}
    <x-card :padding="false">
        <div class="px-5 py-3 border-b border-surface-100 bg-surface-50 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-surface-900">Kecamatan di {{ $selectedKabupaten->nama }}</h3>
                <p class="text-xs text-surface-500">{{ $kecamatans->count() }} data</p>
            </div>
            <button onclick="openModalKecamatan()" class="btn-primary py-1.5 px-3 text-xs flex items-center gap-1">
                <i data-lucide="plus" class="w-3 h-3"></i> Tambah
            </button>
        </div>
        <div class="divide-y divide-surface-100 max-h-[500px] overflow-y-auto">
            @forelse($kecamatans as $kec)
            <div class="flex items-center justify-between px-5 py-3 hover:bg-surface-50/50 transition-colors group">
                <a href="{{ route('admin.wilayah.index', ['provinsi_id' => $selectedProvinsi->id, 'kabupaten_id' => $selectedKabupaten->id, 'kecamatan_id' => $kec->id]) }}" class="flex-1">
                    <div class="font-bold text-surface-900">{{ $kec->nama }}</div>
                    <div class="text-xs text-surface-500">Kode: {{ $kec->kode }} · {{ $kec->desa_count }} Desa</div>
                </a>
                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button onclick="editKecamatan({{ json_encode($kec) }})" class="p-1.5 rounded hover:bg-primary-100 text-primary-600"><i data-lucide="edit" class="w-3.5 h-3.5"></i></button>
                    <form action="{{ route('admin.wilayah.kecamatan.destroy', $kec) }}" method="POST" onsubmit="return confirm('Hapus?');" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1.5 rounded hover:bg-danger-100 text-danger-500"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i></button>
                    </form>
                </div>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-surface-500 text-sm">Belum ada Kecamatan.</div>
            @endforelse
        </div>
        
        @if($kecamatans instanceof \Illuminate\Pagination\LengthAwarePaginator && $kecamatans->hasPages())
        <div class="p-4 border-t border-surface-100 bg-surface-50">
            {{ $kecamatans->links('pagination::tailwind') }}
        </div>
        @endif
    </x-card>
    @endif

    @if($selectedKecamatan)
    {{-- Desa List --}}
    <x-card :padding="false">
        <div class="px-5 py-3 border-b border-surface-100 bg-surface-50 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-surface-900">Desa/Kelurahan di {{ $selectedKecamatan->nama }}</h3>
                <p class="text-xs text-surface-500">{{ $desas->count() }} data</p>
            </div>
            <button onclick="openModalDesa()" class="btn-primary py-1.5 px-3 text-xs flex items-center gap-1">
                <i data-lucide="plus" class="w-3 h-3"></i> Tambah
            </button>
        </div>
        <div class="divide-y divide-surface-100 max-h-[500px] overflow-y-auto">
            @forelse($desas as $ds)
            <div class="flex items-center justify-between px-5 py-3 hover:bg-surface-50/50 transition-colors group">
                <div class="flex-1">
                    <div class="font-bold text-surface-900">{{ $ds->nama }}</div>
                    <div class="text-xs text-surface-500">Kode: {{ $ds->kode }}</div>
                </div>
                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button onclick="editDesa({{ json_encode($ds) }})" class="p-1.5 rounded hover:bg-primary-100 text-primary-600"><i data-lucide="edit" class="w-3.5 h-3.5"></i></button>
                    <form action="{{ route('admin.wilayah.desa.destroy', $ds) }}" method="POST" onsubmit="return confirm('Hapus?');" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1.5 rounded hover:bg-danger-100 text-danger-500"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i></button>
                    </form>
                </div>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-surface-500 text-sm">Belum ada Desa/Kelurahan.</div>
            @endforelse
        </div>
        
        @if($desas instanceof \Illuminate\Pagination\LengthAwarePaginator && $desas->hasPages())
        <div class="p-4 border-t border-surface-100 bg-surface-50">
            {{ $desas->links('pagination::tailwind') }}
        </div>
        @endif
    </x-card>
    @endif

    @if(!$selectedProvinsi)
    <div class="flex items-center justify-center p-12 border-2 border-dashed border-surface-200 rounded-2xl">
        <div class="text-center text-surface-500">
            <i data-lucide="mouse-pointer-click" class="w-10 h-10 text-surface-300 mx-auto mb-3"></i>
            <p class="font-medium text-surface-700">Pilih Provinsi</p>
            <p class="text-sm">Klik salah satu provinsi di sebelah kiri untuk melihat Kabupaten/Kota.</p>
        </div>
    </div>
    @endif
</div>

{{-- ═══ MODALS ═══ --}}
@include('admin.wilayah._modals')
@endsection
