@extends('layouts.app')

@section('title', 'Jadwal Pelajaran')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Jadwal Pelajaran</h1>
        <p class="text-sm text-surface-500 mt-1">Kelola jam mengajar guru dan jadwal per kelas.</p>
    </div>
    @if($rombelId)
    <button onclick="openModal()" class="btn-primary flex items-center gap-2">
        <i data-lucide="plus" class="w-4 h-4"></i>
        <span>Tambah Jadwal</span>
    </button>
    @endif
</div>
@endsection

@section('content')
<div class="space-y-6">

    <x-card class="border-t-4 border-t-primary-500" :padding="false">
        <div class="p-4 bg-surface-50 border-b border-surface-100 flex flex-col sm:flex-row gap-4 items-center">
            <h3 class="font-bold text-surface-900 flex-shrink-0">Pilih Kelas & Tahun Pelajaran:</h3>
            <form action="{{ route('admin.jadwal-pelajaran.index') }}" method="GET" class="w-full flex-1 flex flex-col sm:flex-row gap-4 max-w-3xl">
                <div class="w-full sm:w-64">
                    <select name="tahun_pelajaran_id" class="w-full px-3 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500" onchange="this.form.submit()">
                        @foreach($tahuns as $t)
                            <option value="{{ $t->id }}" {{ $tahunId == $t->id ? 'selected' : '' }}>Tahun Ajaran: {{ $t->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-80">
                    <select name="rombel_id" class="w-full px-3 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500" onchange="this.form.submit()">
                        <option value="" disabled {{ !$rombelId ? 'selected' : '' }}>-- Pilih Kelas --</option>
                        @foreach($rombels as $r)
                            <option value="{{ $r->id }}" {{ $rombelId == $r->id ? 'selected' : '' }}>
                                {{ $r->lembaga->singkatan ?? $r->lembaga->nama }} | {{ $r->tingkat ? $r->tingkat . '-' : '' }}{{ $r->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </x-card>

    @if($rombelId)
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @php
                $hariList = ['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU', 'AHAD'];
            @endphp
            
            @foreach($hariList as $hari)
                <div class="bg-white rounded-xl border border-surface-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                    <div class="bg-surface-50 px-4 py-3 border-b border-surface-200 flex justify-between items-center">
                        <h4 class="font-bold text-surface-900">{{ $hari }}</h4>
                        <span class="text-xs font-semibold bg-surface-200 text-surface-600 px-2 py-0.5 rounded-full">
                            {{ isset($jadwals[$hari]) ? count($jadwals[$hari]) . ' Sesi' : 'Libur' }}
                        </span>
                    </div>
                    
                    <div class="p-0">
                        @if(isset($jadwals[$hari]) && count($jadwals[$hari]) > 0)
                            <div class="divide-y divide-surface-100">
                                @foreach($jadwals[$hari] as $jadwal)
                                    <div class="p-4 hover:bg-surface-50 transition-colors group">
                                        <div class="flex justify-between items-start mb-1">
                                            <span class="text-xs font-bold text-primary-600 font-mono bg-primary-50 px-1.5 py-0.5 rounded">
                                                {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}
                                            </span>
                                            <form action="{{ route('admin.jadwal-pelajaran.destroy', $jadwal) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-surface-300 hover:text-danger-500 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        </div>
                                        <h5 class="font-bold text-surface-900 mt-1">{{ $jadwal->mataPelajaran->nama_mapel }}</h5>
                                        <p class="text-sm text-surface-500 mt-0.5 flex items-center gap-1">
                                            <i data-lucide="user" class="w-3 h-3"></i> 
                                            {{ $jadwal->guru ? $jadwal->guru->orang->nama_lengkap : 'Guru Belum Ditentukan' }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-8 text-center text-surface-400">
                                <i data-lucide="coffee" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                                <p class="text-sm">Kosong / Libur</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @elseif(!request()->has('rombel_id'))
        <div class="bg-primary-50 rounded-xl p-8 text-center border border-primary-100">
            <div class="w-16 h-16 bg-white text-primary-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                <i data-lucide="calendar" class="w-8 h-8"></i>
            </div>
            <h3 class="text-lg font-bold text-surface-900 mb-2">Jadwal Pelajaran</h3>
            <p class="text-surface-500 max-w-md mx-auto">Silakan pilih kelas pada menu dropdown di atas untuk melihat atau mengatur jadwal mata pelajaran dan guru mengajar.</p>
        </div>
    @endif
</div>

@if($rombelId)
{{-- Modal Tambah Jadwal --}}
<div id="modal-jadwal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-surface-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
    
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-visible rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                <div class="px-6 py-4 border-b border-surface-100 flex justify-between items-center bg-surface-50">
                    <h3 class="text-lg font-bold text-surface-900 font-heading">Tambah Jadwal Baru</h3>
                    <button type="button" onclick="closeModal()" class="text-surface-400 hover:text-surface-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <form action="{{ route('admin.jadwal-pelajaran.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="rombel_id" value="{{ $rombelId }}">
                    
                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Hari <span class="text-danger-500">*</span></label>
                            <select name="hari" required class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                                @foreach(['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU', 'AHAD'] as $h)
                                    <option value="{{ $h }}">{{ $h }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Jam Mulai <span class="text-danger-500">*</span></label>
                                <input type="time" name="jam_mulai" required class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 mb-1">Jam Selesai <span class="text-danger-500">*</span></label>
                                <input type="time" name="jam_selesai" required class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Mata Pelajaran <span class="text-danger-500">*</span></label>
                            <select name="mata_pelajaran_id" required class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                                <option value="" disabled selected>Pilih Mapel...</option>
                                @foreach($mapels as $m)
                                    <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Guru Pengampu (Opsional)</label>
                            <select name="pegawai_id" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                                <option value="" selected>Belum Ditentukan</option>
                                @foreach($gurus as $g)
                                    <option value="{{ $g->id }}">{{ $g->orang->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 bg-surface-50 border-t border-surface-100 flex justify-end gap-3">
                        <button type="button" onclick="closeModal()" class="btn-secondary">Batal</button>
                        <button type="submit" class="btn-primary">Simpan Jadwal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openModal() {
        document.getElementById('modal-jadwal').classList.remove('hidden');
    }
    function closeModal() {
        document.getElementById('modal-jadwal').classList.add('hidden');
    }
</script>
@endpush
@endif
@endsection
