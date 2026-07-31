@extends('layouts.app')

@section('title', 'Jadwal Pelajaran — PP Nurul Furqon')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Jadwal Pelajaran</h1>
        <p class="text-sm text-surface-500 mt-1">Kelola jam mengajar guru dan jadwal per kelas.</p>
    </div>
    @if($rombelId)
    <button onclick="openModal()" class="btn-primary flex items-center gap-2 py-2.5 px-4 rounded-xl font-bold text-xs shadow-md shadow-primary-700/20 shrink-0">
        <i data-lucide="plus" class="w-4 h-4"></i>
        <span>+ Tambah Jadwal Baru</span>
    </button>
    @endif
</div>
@endsection

@section('content')
<div class="space-y-6 pb-12">

    {{-- Responsive Filter Card --}}
    <div class="bg-white rounded-3xl border border-surface-200 shadow-sm p-4 sm:p-5 overflow-hidden">
        <form action="{{ route('admin.jadwal-pelajaran.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
            
            <div class="md:col-span-4 flex items-center gap-2 text-surface-900 font-extrabold text-xs sm:text-sm">
                <div class="w-8 h-8 rounded-xl bg-primary-100 text-primary-700 flex items-center justify-center shrink-0">
                    <i data-lucide="calendar-range" class="w-4 h-4"></i>
                </div>
                <span>Pilih Kelas & Tahun Pelajaran:</span>
            </div>

            <div class="md:col-span-4">
                <select name="tahun_pelajaran_id" class="w-full px-3.5 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-semibold text-surface-800 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500" onchange="this.form.submit()">
                    @foreach($tahuns as $t)
                        <option value="{{ $t->id }}" {{ $tahunId == $t->id ? 'selected' : '' }}>Tahun Ajaran: {{ $t->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-4">
                <select name="rombel_id" class="w-full px-3.5 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-semibold text-surface-800 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500" onchange="this.form.submit()">
                    <option value="" disabled {{ !$rombelId ? 'selected' : '' }}>-- Pilih Kelas / Rombel --</option>
                    @foreach($rombels as $r)
                        <option value="{{ $r->id }}" {{ $rombelId == $r->id ? 'selected' : '' }}>
                            {{ $r->lembaga->singkatan ?? $r->lembaga->nama }} | {{ $r->tingkat ? $r->tingkat . '-' : '' }}{{ $r->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

        </form>
    </div>

    {{-- Grid Jadwal Per Hari --}}
    @if($rombelId)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
                $hariList = ['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU', 'AHAD'];
            @endphp
            
            @foreach($hariList as $hari)
                @php $hasJadwal = isset($jadwals[$hari]) && count($jadwals[$hari]) > 0; @endphp
                <div class="bg-white rounded-3xl border border-surface-200 overflow-hidden shadow-sm hover:shadow-md transition-all flex flex-col">
                    
                    {{-- Header Hari --}}
                    <div class="bg-surface-50/80 px-5 py-3.5 border-b border-surface-100 flex justify-between items-center">
                        <h4 class="font-extrabold text-surface-900 text-sm tracking-wide font-heading">{{ $hari }}</h4>
                        @if($hasJadwal)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[0.65rem] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
                                ● {{ count($jadwals[$hari]) }} Sesi
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[0.65rem] font-extrabold uppercase bg-surface-200 text-surface-600 border border-surface-300">
                                Libur
                            </span>
                        @endif
                    </div>
                    
                    {{-- Body Sesi Pelajaran --}}
                    <div class="p-0 flex-1">
                        @if($hasJadwal)
                            <div class="divide-y divide-surface-100">
                                @foreach($jadwals[$hari] as $jadwal)
                                    <div class="p-4 hover:bg-primary-50/30 transition-colors group relative">
                                        <div class="flex justify-between items-start mb-1.5">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-[0.65rem] font-extrabold font-mono bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                <i data-lucide="clock" class="w-3 h-3 text-emerald-600"></i>
                                                {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }} WIB
                                            </span>

                                            <form action="{{ route('admin.jadwal-pelajaran.destroy', $jadwal) }}" method="POST" onsubmit="return confirm('Hapus sesi jadwal ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 transition-colors" title="Hapus Jadwal">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        </div>

                                        <h5 class="font-extrabold text-surface-900 text-sm leading-tight mt-1">{{ $jadwal->mataPelajaran->nama_mapel }}</h5>
                                        
                                        <p class="text-xs text-surface-500 font-medium mt-1.5 flex items-center gap-1.5">
                                            <i data-lucide="user-check" class="w-3.5 h-3.5 text-primary-600 shrink-0"></i> 
                                            <span class="{{ $jadwal->guru ? 'text-surface-800 font-semibold' : 'text-amber-600 italic' }}">
                                                {{ $jadwal->guru ? $jadwal->guru->orang->nama_lengkap : 'Guru Belum Ditentukan' }}
                                            </span>
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-8 text-center text-surface-400">
                                <i data-lucide="coffee" class="w-8 h-8 mx-auto mb-2 text-surface-300"></i>
                                <p class="text-xs font-bold text-surface-500">Kosong / Libur</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @elseif(!request()->has('rombel_id'))
        <div class="bg-white rounded-3xl p-8 text-center border border-surface-200 shadow-sm max-w-2xl mx-auto">
            <div class="w-16 h-16 bg-primary-100 text-primary-700 rounded-3xl flex items-center justify-center mx-auto mb-4 border border-primary-200 shadow-sm">
                <i data-lucide="calendar-days" class="w-8 h-8"></i>
            </div>
            <h3 class="text-lg font-bold text-surface-900 mb-2 font-heading">Jadwal Pelajaran Pesantren</h3>
            <p class="text-xs text-surface-500 max-w-md mx-auto leading-relaxed">
                Silakan pilih **Kelas / Rombel** pada menu dropdown filter di atas untuk melihat atau menambahkan jadwal mata pelajaran per hari.
            </p>
        </div>
    @endif
</div>

@if($rombelId)
{{-- Modal Tambah Jadwal --}}
<div id="modal-jadwal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-surface-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
    
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-visible rounded-3xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-surface-200">
                <div class="px-6 py-4 border-b border-surface-100 flex justify-between items-center bg-surface-50 rounded-t-3xl">
                    <h3 class="text-base font-bold text-surface-900 font-heading">Tambah Jadwal Baru</h3>
                    <button type="button" onclick="closeModal()" class="text-surface-400 hover:text-surface-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <form action="{{ route('admin.jadwal-pelajaran.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="rombel_id" value="{{ $rombelId }}">
                    
                    <div class="px-6 py-4 space-y-4 text-xs">
                        <div>
                            <label class="block font-bold text-surface-700 mb-1">Hari <span class="text-danger-500">*</span></label>
                            <select name="hari" required class="w-full rounded-xl border border-surface-300 bg-white px-3.5 py-2.5 text-xs font-semibold focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                                @foreach(['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU', 'AHAD'] as $h)
                                    <option value="{{ $h }}">{{ $h }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block font-bold text-surface-700 mb-1">Jam Mulai <span class="text-danger-500">*</span></label>
                                <input type="time" name="jam_mulai" required class="w-full rounded-xl border border-surface-300 bg-white px-3.5 py-2.5 text-xs font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                            </div>
                            <div>
                                <label class="block font-bold text-surface-700 mb-1">Jam Selesai <span class="text-danger-500">*</span></label>
                                <input type="time" name="jam_selesai" required class="w-full rounded-xl border border-surface-300 bg-white px-3.5 py-2.5 text-xs font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-surface-700 mb-1">Mata Pelajaran <span class="text-danger-500">*</span></label>
                            <select name="mata_pelajaran_id" required class="w-full rounded-xl border border-surface-300 bg-white px-3.5 py-2.5 text-xs font-semibold focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                                <option value="" disabled selected>Pilih Mapel...</option>
                                @foreach($mapels as $m)
                                    <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-surface-700 mb-1">Guru Pengampu (Opsional)</label>
                            <select name="pegawai_id" class="w-full rounded-xl border border-surface-300 bg-white px-3.5 py-2.5 text-xs font-semibold focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                                <option value="" selected>Belum Ditentukan</option>
                                @foreach($gurus as $g)
                                    <option value="{{ $g->id }}">{{ $g->orang->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 bg-surface-50 border-t border-surface-100 flex justify-end gap-3 rounded-b-3xl">
                        <button type="button" onclick="closeModal()" class="btn-secondary text-xs font-bold py-2 px-4 rounded-xl">Batal</button>
                        <button type="submit" class="btn-primary text-xs font-bold py-2 px-5 rounded-xl">Simpan Jadwal</button>
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
