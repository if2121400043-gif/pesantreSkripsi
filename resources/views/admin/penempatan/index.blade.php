@extends('layouts.app')

@section('title', 'Penempatan Santri Ke Kelas (Plotting) — PP Nurul Furqon')

@section('content')
<div class="space-y-6" x-data="{
    selectedRombelId: '{{ old('rombel_id', request('rombel_id')) }}',
    selectedRombelName: 'Belum Dipilih',
    selectedCapacity: 0,
    selectedFilled: 0,
    selectedGender: 'CAMPUR',
    selectRombel(id, name, filled, capacity, gender) {
        this.selectedRombelId = id;
        this.selectedRombelName = name;
        this.selectedCapacity = capacity;
        this.selectedFilled = filled;
        this.selectedGender = gender;
    }
}">

    {{-- Hero Header Banner --}}
    <div class="rounded-3xl p-6 md:p-8 shadow-lg relative overflow-hidden text-white" style="background: linear-gradient(135deg, #047857, #065f46) !important; color: #ffffff !important;">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 text-xs font-semibold backdrop-blur-sm border border-white/20 mb-3" style="color: #ffffff !important;">
                    <i data-lucide="layout-grid" class="w-3.5 h-3.5 text-warning-300"></i>
                    Manajemen Rombongan Belajar
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold font-heading" style="color: #ffffff !important;">
                    Penempatan Kelas (Plotting Santri)
                </h1>
                <p class="text-xs md:text-sm mt-1 max-w-xl" style="color: #a7f3d0 !important;">
                    Panduan 3 langkah mudah untuk mengelompokkan santri ke dalam kelas/rombel secara massal dan cepat.
                </p>
            </div>
            
            <a href="{{ route('admin.rombel.index') }}" class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl font-bold text-xs shadow-lg transition-all border border-white/30 hover:bg-white/20" style="background: rgba(255,255,255,0.1) !important; color: #ffffff !important;">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali ke Data Rombel</span>
            </a>
        </div>
    </div>

    {{-- LANGKAH 1: Pilih Lembaga & Tahun Pelajaran --}}
    <div class="bg-white p-6 rounded-3xl border border-surface-200 shadow-sm space-y-4">
        <div class="flex items-center gap-3 pb-3 border-b border-surface-100">
            <span class="w-7 h-7 rounded-xl bg-emerald-700 text-white font-black text-xs flex items-center justify-center shrink-0" style="background-color: #047857 !important; color: #ffffff !important;">1</span>
            <div>
                <h3 class="font-extrabold text-surface-900 text-sm">LANGKAH 1: Pilih Lembaga & Tahun Pelajaran Target</h3>
                <p class="text-xs text-surface-500">Tentukan lembaga pendidikan dan tahun ajaran yang akan di-plot santrinya.</p>
            </div>
        </div>

        <form action="{{ route('admin.penempatan.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-surface-700 mb-1">1. Lembaga Pendidikan <span class="text-rose-500">*</span></label>
                <select name="lembaga_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-semibold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    <option value="" disabled selected>Pilih Lembaga (SMP / SMA / Madin)...</option>
                    @foreach($lembagas as $lembaga)
                        <option value="{{ $lembaga->id }}" {{ $lembagaId == $lembaga->id ? 'selected' : '' }}>
                            {{ $lembaga->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-surface-700 mb-1">2. Tahun Pelajaran <span class="text-rose-500">*</span></label>
                <select name="tahun_pelajaran_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-semibold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    <option value="" disabled selected>Pilih Tahun Ajaran...</option>
                    @foreach($tahuns as $tahun)
                        <option value="{{ $tahun->id }}" {{ $tahunId == $tahun->id ? 'selected' : ($loop->first && !$tahunId ? 'selected' : '') }}>
                            {{ $tahun->nama }} {{ $tahun->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label class="block text-xs font-bold text-surface-700 mb-1">3. Tingkat (Opsional)</label>
                    <select name="tingkat" class="w-full px-3.5 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-semibold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        <option value="">Semua Tingkat</option>
                        @for($i=1; $i<=15; $i++)
                            <option value="{{ $i }}" {{ $tingkat == $i ? 'selected' : '' }}>Tingkat {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-white font-extrabold text-xs shadow-md transition-all shrink-0 hover:scale-102" style="background-color: #047857 !important; color: #ffffff !important;">
                    Tampilkan Data
                </button>
            </div>
        </form>
    </div>

    @if($lembagaId && $tahunId)
        <form action="{{ route('admin.penempatan.store') }}" method="POST" id="form-penempatan">
            @csrf
            <input type="hidden" name="rombel_id" :value="selectedRombelId" required>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- LANGKAH 2: Pilih Kelas Tujuan (Visual Cards) --}}
                <div class="lg:col-span-1 space-y-4">
                    <div class="bg-white p-5 rounded-3xl border border-surface-200 shadow-sm space-y-4">
                        <div class="flex items-center gap-2.5 pb-3 border-b border-surface-100">
                            <span class="w-7 h-7 rounded-xl bg-blue-700 text-white font-black text-xs flex items-center justify-center shrink-0">2</span>
                            <div>
                                <h3 class="font-extrabold text-surface-900 text-sm">LANGKAH 2: Klik Kelas Tujuan</h3>
                                <p class="text-[0.68rem] text-surface-500">Klik salah satu kartu kelas di bawah untuk memilih kelas target.</p>
                            </div>
                        </div>

                        {{-- Direct Rombel Selector Cards --}}
                        <div class="space-y-3 max-h-[550px] overflow-y-auto pr-1">
                            @forelse($rombels as $rombel)
                                @php
                                    $rFilled = $rombel->riwayat_peserta_count;
                                    $rCap = max($rombel->kapasitas, 1);
                                    $rPct = min(round(($rFilled / $rCap) * 100), 100);
                                    $rFull = ($rFilled >= $rombel->kapasitas);
                                    $rName = ($rombel->tingkat ? 'Kelas ' . $rombel->tingkat . '-' : '') . $rombel->nama;
                                @endphp

                                <div @click="selectRombel('{{ $rombel->id }}', '{{ addslashes($rName) }}', {{ $rFilled }}, {{ $rombel->kapasitas }}, '{{ $rombel->gender_target }}')"
                                     :class="selectedRombelId == '{{ $rombel->id }}' ? 'border-2 border-emerald-600 bg-emerald-50/70 shadow-md ring-2 ring-emerald-500/20' : 'border border-surface-200 bg-white hover:border-emerald-300 hover:shadow-sm'"
                                     class="p-4 rounded-2xl cursor-pointer transition-all relative overflow-hidden group">
                                    
                                    <div class="flex items-start justify-between gap-2 mb-2">
                                        <div>
                                            <h4 class="font-extrabold text-surface-900 text-sm group-hover:text-emerald-700 transition-colors">
                                                {{ $rName }}
                                            </h4>
                                            <div class="flex items-center gap-1.5 mt-1">
                                                <span class="px-2 py-0.5 rounded text-[0.65rem] font-extrabold uppercase {{ $rombel->gender_target === 'PUTRA' ? 'bg-blue-100 text-blue-800' : ($rombel->gender_target === 'PUTRI' ? 'bg-pink-100 text-pink-800' : 'bg-surface-100 text-surface-700') }}">
                                                    {{ $rombel->gender_target }}
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Selection Indicator Badge --}}
                                        <div x-show="selectedRombelId == '{{ $rombel->id }}'" transition class="px-2.5 py-1 rounded-full text-[0.65rem] font-black bg-emerald-700 text-white shadow-xs" style="color: #ffffff !important; background-color: #047857 !important;">
                                            ✓ TERPILIH
                                        </div>
                                    </div>

                                    {{-- Capacity Progress Bar --}}
                                    <div class="space-y-1 mt-3">
                                        <div class="flex justify-between items-center text-[0.68rem]">
                                            <span class="font-bold text-surface-600">Terisi: <strong>{{ $rFilled }} / {{ $rombel->kapasitas }}</strong> Santri</span>
                                            <span class="font-extrabold {{ $rFull ? 'text-rose-600' : 'text-emerald-700' }}">{{ $rPct }}%</span>
                                        </div>
                                        <div class="w-full bg-surface-200 rounded-full h-2 overflow-hidden">
                                            <div class="h-2 rounded-full transition-all duration-300 {{ $rFull ? 'bg-rose-500' : ($rPct >= 80 ? 'bg-amber-500' : 'bg-emerald-600') }}" style="width: {{ $rPct }}%"></div>
                                        </div>
                                    </div>

                                    @if($rFull)
                                        <p class="text-[0.65rem] text-rose-600 font-extrabold mt-2 flex items-center gap-1">
                                            <i data-lucide="alert-circle" class="w-3 h-3"></i> Kapasitas Penuh
                                        </p>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-8 bg-surface-50 rounded-2xl border border-surface-200 border-dashed">
                                    <p class="text-xs text-surface-500">Belum ada kelas di lembaga ini.</p>
                                    <a href="{{ route('admin.rombel.create') }}" class="text-emerald-700 font-bold text-xs mt-2 inline-block hover:underline">Buat Kelas Baru</a>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- LANGKAH 3: Centang & Tempatkan Santri --}}
                <div class="lg:col-span-2 space-y-4">
                    <div class="bg-white rounded-3xl border border-surface-200 shadow-sm overflow-hidden flex flex-col justify-between h-full">
                        
                        <div>
                            {{-- Header Step 3 --}}
                            <div class="p-5 border-b border-surface-100 flex items-center justify-between gap-4 bg-surface-50">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-7 h-7 rounded-xl bg-amber-600 text-white font-black text-xs flex items-center justify-center shrink-0">3</span>
                                    <div>
                                        <h3 class="font-extrabold text-surface-900 text-sm">LANGKAH 3: Centang Santri yang Akan Dimasukkan</h3>
                                        <p class="text-[0.68rem] text-surface-500">Centang santri yang akan dipindahkan ke kelas target terpilih.</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Instant Search & Gender Filter --}}
                            <div class="p-4 bg-surface-50/70 border-b border-surface-200 flex flex-col sm:flex-row justify-between items-center gap-3">
                                <div class="relative flex-1 w-full">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-surface-400">
                                        <i data-lucide="search" class="w-4 h-4"></i>
                                    </div>
                                    <input type="text" id="live-search-santri" placeholder="Cari nama santri, NIUP, NISN..." class="w-full pr-8 py-2 text-xs rounded-xl border border-surface-300 bg-white text-surface-900 shadow-2xs focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" style="padding-left: 2.75rem !important;">
                                </div>

                                <div class="flex items-center gap-2 text-xs overflow-x-auto w-full sm:w-auto">
                                    <span class="text-surface-500 font-bold shrink-0">Filter:</span>
                                    <div class="inline-flex rounded-xl p-1 bg-surface-200/60 border border-surface-200 shrink-0">
                                        <button type="button" id="btn-filter-all" data-gender="ALL" class="gender-filter-btn px-3 py-1 rounded-lg text-xs font-bold bg-white text-emerald-800 shadow-2xs transition-all">
                                            Semua
                                        </button>
                                        <button type="button" id="btn-filter-l" data-gender="L" class="gender-filter-btn px-3 py-1 rounded-lg text-xs font-semibold text-surface-600 hover:text-surface-900 transition-all">
                                            👦 Putra (L)
                                        </button>
                                        <button type="button" id="btn-filter-p" data-gender="P" class="gender-filter-btn px-3 py-1 rounded-lg text-xs font-semibold text-surface-600 hover:text-surface-900 transition-all">
                                            👧 Putri (P)
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Table Santri Belum Ditempatkan --}}
                            <div class="overflow-x-auto max-h-[480px] overflow-y-auto">
                                <table class="w-full text-left text-xs whitespace-nowrap" id="table-santri-plotting">
                                    <thead class="bg-surface-100/70 text-surface-600 sticky top-0 border-b border-surface-200 z-10 font-bold uppercase text-[0.65rem]">
                                        <tr>
                                            <th class="px-4 py-3 text-center w-10">
                                                <input type="checkbox" id="check-all" class="rounded border-surface-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4 cursor-pointer">
                                            </th>
                                            <th class="px-4 py-3">Nama Santri</th>
                                            <th class="px-4 py-3">NIUP / NISN</th>
                                            <th class="px-4 py-3 text-center">Gender</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-surface-100 text-surface-800" id="tbody-santri-plotting">
                                        @forelse($pesertaBelumDitempatkan as $peserta)
                                            <tr class="hover:bg-emerald-50/50 transition-colors cursor-pointer row-clickable santri-row" data-name="{{ strtolower($peserta->orang->nama_lengkap) }}" data-niup="{{ strtolower($peserta->orang->niup) }}" data-nis="{{ strtolower($peserta->nis ?? '') }}" data-nisn="{{ strtolower($peserta->nisn ?? '') }}" data-gender="{{ $peserta->orang->jenis_kelamin }}">
                                                <td class="px-4 py-3 text-center">
                                                    <input type="checkbox" name="peserta_ids[]" value="{{ $peserta->id }}" class="peserta-checkbox rounded border-surface-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4 cursor-pointer">
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="font-extrabold text-surface-900 text-xs">{{ $peserta->orang->nama_lengkap }}</div>
                                                    <div class="text-[0.68rem] text-surface-500 font-mono">NIUP: {{ $peserta->orang->niup }}</div>
                                                </td>
                                                <td class="px-4 py-3 font-mono">
                                                    <div>{{ $peserta->nis ?? '-' }}</div>
                                                    <div class="text-[0.65rem] text-surface-450">{{ $peserta->nisn ?? 'Tanpa NISN' }}</div>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="inline-flex w-6 h-6 items-center justify-center rounded-full font-bold text-xs {{ $peserta->orang->jenis_kelamin == 'L' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                                                        {{ $peserta->orang->jenis_kelamin }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr id="empty-db-row">
                                                <td colspan="4" class="px-4 py-12 text-center text-surface-500">
                                                    <i data-lucide="check-circle" class="w-12 h-12 text-emerald-500 mx-auto mb-3"></i>
                                                    <p class="font-bold text-surface-900 text-sm mb-1">Semua Santri Sudah Memiliki Kelas!</p>
                                                    <p class="text-xs text-surface-450">Tidak ada daftar santri aktif yang belum ditempatkan.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Floating Big Action Bar at Bottom --}}
                        <div class="p-4 bg-emerald-950 text-white border-t border-emerald-900 flex flex-col sm:flex-row justify-between items-center gap-3">
                            <div class="text-xs">
                                <span class="text-emerald-300">Kelas Target:</span> 
                                <strong x-text="selectedRombelName" class="text-white font-extrabold text-sm ml-1"></strong>
                            </div>

                            <button type="button" onclick="submitPenempatan()" :disabled="!selectedRombelId" class="w-full sm:w-auto px-6 py-3 rounded-2xl font-black text-xs shadow-xl transition-all flex items-center justify-center gap-2 hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed" style="background-color: #fbbf24 !important; color: #1e1b4b !important;">
                                <i data-lucide="user-plus" class="w-4 h-4"></i>
                                <span>MASUKKAN SANTRI TERPILIH KE KELAS</span>
                            </button>
                        </div>

                    </div>
                </div>

            </div>
        </form>
    @else
        <div class="bg-white rounded-3xl p-12 text-center border border-surface-200 shadow-sm space-y-3 max-w-lg mx-auto">
            <div class="w-16 h-16 bg-emerald-50 text-emerald-700 rounded-2xl flex items-center justify-center mx-auto mb-2 border border-emerald-100">
                <i data-lucide="filter" class="w-8 h-8"></i>
            </div>
            <h3 class="text-lg font-extrabold text-surface-900">Silakan Jalankan LANGKAH 1</h3>
            <p class="text-xs text-surface-500 leading-relaxed">Pilih <strong>Lembaga Pendidikan</strong> (SMP/SMA/Madin) dan <strong>Tahun Pelajaran</strong> pada panel Langkah 1 di atas untuk mulai menempatkan santri ke dalam kelas.</p>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkAll = document.getElementById('check-all');
        const checkboxes = document.querySelectorAll('.peserta-checkbox');
        const rows = document.querySelectorAll('.row-clickable');
        const searchInput = document.getElementById('live-search-santri');
        const genderBtns = document.querySelectorAll('.gender-filter-btn');
        
        let currentGenderFilter = 'ALL';

        if(checkAll) {
            checkAll.addEventListener('change', function() {
                document.querySelectorAll('.santri-row').forEach(row => {
                    if (row.style.display !== 'none') {
                        const cb = row.querySelector('.peserta-checkbox');
                        if (cb) cb.checked = checkAll.checked;
                    }
                });
            });
        }

        // Click row to check
        rows.forEach(row => {
            row.addEventListener('click', function(e) {
                if(e.target.tagName !== 'INPUT') {
                    const cb = this.querySelector('.peserta-checkbox');
                    if (cb) {
                        cb.checked = !cb.checked;
                    }
                }
            });
        });

        // Instant Filter Function
        function filterSantri() {
            const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
            let visibleCount = 0;

            document.querySelectorAll('.santri-row').forEach(row => {
                const name = row.getAttribute('data-name') || '';
                const niup = row.getAttribute('data-niup') || '';
                const nis = row.getAttribute('data-nis') || '';
                const nisn = row.getAttribute('data-nisn') || '';
                const gender = row.getAttribute('data-gender') || '';

                const matchesSearch = !query || name.includes(query) || niup.includes(query) || nis.includes(query) || nisn.includes(query);
                const matchesGender = (currentGenderFilter === 'ALL') || (gender === currentGenderFilter);

                if (matchesSearch && matchesGender) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
        }

        if(searchInput) searchInput.addEventListener('input', filterSantri);

        genderBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                genderBtns.forEach(b => {
                    b.classList.remove('bg-white', 'text-emerald-800', 'shadow-2xs', 'font-bold');
                    b.classList.add('text-surface-600', 'font-semibold');
                });
                this.classList.add('bg-white', 'text-emerald-800', 'shadow-2xs', 'font-bold');
                this.classList.remove('text-surface-600', 'font-semibold');
                currentGenderFilter = this.getAttribute('data-gender');
                filterSantri();
            });
        });
    });

    function submitPenempatan() {
        const selectedRombelId = document.querySelector('input[name="rombel_id"]').value;
        const checkedCount = document.querySelectorAll('.peserta-checkbox:checked').length;

        if (!selectedRombelId) {
            alert('⚠️ Harap pilih Kelas Tujuan terlebih dahulu pada Langkah 2!');
            return;
        }

        if (checkedCount === 0) {
            alert('⚠️ Harap centang minimal 1 santri yang akan dimasukkan ke kelas!');
            return;
        }

        if (confirm(`Konfirmasi: Masukkan ${checkedCount} santri ke kelas target terpilih?`)) {
            document.getElementById('form-penempatan').submit();
        }
    }
</script>
@endpush
