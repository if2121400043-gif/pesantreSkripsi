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
        let el = document.getElementById('hidden_rombel_id');
        if(el) el.value = id;
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
                    Alur sederhana untuk mengelompokkan santri ke dalam kelas/rombel secara terpadu.
                </p>
            </div>
            
            <a href="{{ route('admin.rombel.index') }}" class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl font-bold text-xs shadow-lg transition-all border border-white/30 hover:bg-white/20" style="background: rgba(255,255,255,0.1) !important; color: #ffffff !important;">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali ke Data Rombel</span>
            </a>
        </div>
    </div>

    {{-- FILTER LEMBAGA & TAHUN --}}
    <div class="bg-white p-5 rounded-3xl border border-surface-200 shadow-sm space-y-3">
        <form action="{{ route('admin.penempatan.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-surface-700 mb-1">1. Lembaga Pendidikan <span class="text-rose-500">*</span></label>
                <select name="lembaga_id" required class="w-full px-3.5 py-2 rounded-xl border border-surface-300 bg-white text-xs font-semibold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
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
                <select name="tahun_pelajaran_id" required class="w-full px-3.5 py-2 rounded-xl border border-surface-300 bg-white text-xs font-semibold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
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
                    <select name="tingkat" class="w-full px-3.5 py-2 rounded-xl border border-surface-300 bg-white text-xs font-semibold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        <option value="">Semua Tingkat</option>
                        @for($i=1; $i<=15; $i++)
                            <option value="{{ $i }}" {{ $tingkat == $i ? 'selected' : '' }}>Tingkat {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <button type="submit" class="px-5 py-2 rounded-xl text-white font-extrabold text-xs shadow-md transition-all shrink-0 hover:scale-102" style="background-color: #047857 !important; color: #ffffff !important;">
                    Tampilkan Data
                </button>
            </div>
        </form>
    </div>

    @if($lembagaId && $tahunId)
        {{-- KARTU UTAMA TERPADU --}}
        <form action="{{ route('admin.penempatan.store') }}" method="POST" id="form-penempatan">
            @csrf
            {{-- Hidden input with direct native value binding --}}
            <input type="hidden" name="rombel_id" id="hidden_rombel_id" value="{{ old('rombel_id', request('rombel_id')) }}" required>

            <div class="bg-white rounded-3xl border border-surface-200 shadow-sm overflow-hidden space-y-6 p-6 md:p-8">
                
                {{-- SECTION 1: KELAS TUJUAN --}}
                <div class="space-y-3 pb-6 border-b border-surface-100">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                        <div>
                            <h3 class="font-extrabold text-surface-900 text-base flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-emerald-700 text-white font-black text-xs flex items-center justify-center shrink-0" style="background-color: #047857 !important; color: #ffffff !important;">1</span>
                                Pilih Kelas Tujuan (Klik Kartu Kelas)
                            </h3>
                            <p class="text-xs text-surface-500 mt-0.5">Klik salah satu kelas di bawah ini untuk menentukan kelas target penempatan santri.</p>
                        </div>

                        <div class="text-xs text-surface-600 font-bold bg-surface-50 px-3 py-1.5 rounded-xl border border-surface-200 shrink-0">
                            Kelas Terpilih: <strong x-text="selectedRombelName" class="text-emerald-800 font-extrabold ml-1"></strong>
                        </div>
                    </div>

                    @if($rombels->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 pt-2">
                            @foreach($rombels as $rombel)
                                @php
                                    $rFilled = $rombel->riwayat_peserta_count;
                                    $rCap = max($rombel->kapasitas, 1);
                                    $rPct = min(round(($rFilled / $rCap) * 100), 100);
                                    $rFull = ($rFilled >= $rombel->kapasitas);
                                    $rName = ($rombel->tingkat ? 'Kelas ' . $rombel->tingkat . '-' : '') . $rombel->nama;
                                @endphp

                                <div @click="selectRombel('{{ $rombel->id }}', '{{ addslashes($rName) }}', {{ $rFilled }}, {{ $rombel->kapasitas }}, '{{ $rombel->gender_target }}'); document.getElementById('hidden_rombel_id').value = '{{ $rombel->id }}';"
                                     :class="selectedRombelId == '{{ $rombel->id }}' ? 'border-2 border-emerald-600 bg-emerald-50 shadow-md ring-2 ring-emerald-500/20' : 'border border-surface-200 bg-white hover:border-emerald-300 hover:shadow-2xs'"
                                     class="p-3.5 rounded-2xl cursor-pointer transition-all relative overflow-hidden group">
                                    
                                    <div class="flex items-start justify-between gap-1 mb-1.5">
                                        <h4 class="font-extrabold text-surface-900 text-xs leading-snug group-hover:text-emerald-700 transition-colors">
                                            {{ $rName }}
                                        </h4>
                                        <div x-show="selectedRombelId == '{{ $rombel->id }}'" transition class="px-2 py-0.5 rounded-full text-[0.6rem] font-black bg-emerald-700 text-white shrink-0" style="color: #ffffff !important; background-color: #047857 !important;">
                                            ✓ TERPILIH
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between text-[0.65rem] text-surface-500 mb-1.5">
                                        <span class="px-1.5 py-0.5 rounded text-[0.6rem] font-extrabold uppercase {{ $rombel->gender_target === 'PUTRA' ? 'bg-blue-100 text-blue-800' : ($rombel->gender_target === 'PUTRI' ? 'bg-pink-100 text-pink-800' : 'bg-surface-100 text-surface-700') }}">
                                            {{ $rombel->gender_target }}
                                        </span>
                                        <span class="font-bold {{ $rFull ? 'text-rose-600' : 'text-emerald-700' }}">{{ $rFilled }}/{{ $rombel->kapasitas }} ({{ $rPct }}%)</span>
                                    </div>

                                    <div class="w-full bg-surface-200 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-1.5 rounded-full {{ $rFull ? 'bg-rose-500' : ($rPct >= 80 ? 'bg-amber-500' : 'bg-emerald-600') }}" style="width: {{ $rPct }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-6 text-center bg-surface-50 rounded-2xl border border-surface-200 border-dashed">
                            <p class="text-xs text-surface-500">Belum ada rombel/kelas di lembaga ini.</p>
                            <a href="{{ route('admin.rombel.create') }}" class="text-emerald-700 font-bold text-xs mt-1 inline-block hover:underline">Buat Kelas Baru</a>
                        </div>
                    @endif
                </div>

                {{-- SECTION 2: SEARCH & FILTER SANTRI --}}
                <div class="space-y-4">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                        <h3 class="font-extrabold text-surface-900 text-base flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-blue-700 text-white font-black text-xs flex items-center justify-center shrink-0">2</span>
                            Pilih Santri Belum Dapat Kelas (Maks. 10 Per Halaman)
                        </h3>

                        {{-- Gender Filter Buttons --}}
                        <div class="flex items-center gap-2 text-xs overflow-x-auto">
                            <span class="text-surface-500 font-bold shrink-0">Filter Gender:</span>
                            <div class="inline-flex rounded-xl p-1 bg-surface-100 border border-surface-200 shrink-0">
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

                    {{-- Search Box --}}
                    <div class="relative w-full">
                        <div class="absolute top-1/2 -translate-y-1/2 left-3.5 text-surface-400 pointer-events-none flex items-center justify-center">
                            <i data-lucide="search" class="w-4 h-4"></i>
                        </div>
                        <input type="text" id="live-search-santri" placeholder="Cari nama santri, NIUP, atau NISN..." 
                               class="w-full pr-4 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-medium text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors"
                               style="padding-left: 2.75rem !important;">
                    </div>

                    {{-- TABEL SANTRI DENGAN PAGINASI --}}
                    <div class="rounded-2xl border border-surface-200 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs whitespace-nowrap" id="table-santri-plotting">
                                <thead class="bg-surface-100/80 text-surface-600 border-b border-surface-200 font-bold uppercase text-[0.65rem]">
                                    <tr>
                                        <th class="px-4 py-3 text-center w-12">
                                            <input type="checkbox" id="check-all" class="rounded border-surface-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4 cursor-pointer">
                                        </th>
                                        <th class="px-4 py-3">Nama Santri</th>
                                        <th class="px-4 py-3">NIUP / NISN</th>
                                        <th class="px-4 py-3 text-center w-20">Gender</th>
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
                                            <td colspan="4" class="px-4 py-10 text-center text-surface-500">
                                                <i data-lucide="check-circle" class="w-10 h-10 text-emerald-500 mx-auto mb-2"></i>
                                                <p class="font-bold text-surface-900 text-sm mb-0.5">Semua Santri Sudah Memiliki Kelas!</p>
                                                <p class="text-xs text-surface-450">Tidak ada daftar santri aktif yang belum ditempatkan.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination Bar --}}
                        <div class="p-3 bg-surface-50 border-t border-surface-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                            <div class="text-surface-600 font-medium" id="page-info-display">
                                Menampilkan Halaman <strong id="current-page-num" class="text-surface-900">1</strong> dari <strong id="total-page-num" class="text-surface-900">1</strong>
                                (<span id="total-filtered-count">0</span> Santri Belum Terplot)
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button" id="btn-prev-page" class="px-3.5 py-1.5 rounded-xl bg-white border border-surface-300 font-bold text-surface-700 hover:bg-emerald-50 hover:text-emerald-800 disabled:opacity-40 disabled:cursor-not-allowed transition-all shadow-2xs">
                                    ← Previous
                                </button>
                                <button type="button" id="btn-next-page" class="px-3.5 py-1.5 rounded-xl bg-white border border-surface-300 font-bold text-surface-700 hover:bg-emerald-50 hover:text-emerald-800 disabled:opacity-40 disabled:cursor-not-allowed transition-all shadow-2xs">
                                    Next →
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 4: SUBMIT ACTION BAR --}}
                <div class="pt-4 border-t border-surface-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-xs text-surface-600 font-medium">
                        Total Santri Belum Terplot: <strong class="text-surface-900">{{ count($pesertaBelumDitempatkan) }} Orang</strong>
                    </div>

                    <button type="button" onclick="submitPenempatan()" class="w-full sm:w-auto px-7 py-3 rounded-2xl font-black text-xs shadow-xl transition-all flex items-center justify-center gap-2 hover:scale-105" style="background-color: #fbbf24 !important; color: #1e1b4b !important;">
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                        <span>MASUKKAN SANTRI TERPILIH KE KELAS</span>
                    </button>
                </div>

            </div>
        </form>
    @else
        <div class="bg-white rounded-3xl p-12 text-center border border-surface-200 shadow-sm space-y-3 max-w-lg mx-auto">
            <div class="w-16 h-16 bg-emerald-50 text-emerald-700 rounded-2xl flex items-center justify-center mx-auto mb-2 border border-emerald-100">
                <i data-lucide="filter" class="w-8 h-8"></i>
            </div>
            <h3 class="text-lg font-extrabold text-surface-900">Silakan Pilih Lembaga & Tahun Pelajaran</h3>
            <p class="text-xs text-surface-500 leading-relaxed">Pilih <strong>Lembaga Pendidikan</strong> dan <strong>Tahun Pelajaran</strong> pada panel filter di atas untuk mulai menempatkan santri ke dalam kelas.</p>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkAll = document.getElementById('check-all');
        const rows = Array.from(document.querySelectorAll('.santri-row'));
        const searchInput = document.getElementById('live-search-santri');
        const genderBtns = document.querySelectorAll('.gender-filter-btn');
        const btnPrev = document.getElementById('btn-prev-page');
        const btnNext = document.getElementById('btn-next-page');
        const currentPageEl = document.getElementById('current-page-num');
        const totalPageEl = document.getElementById('total-page-num');
        const totalFilteredEl = document.getElementById('total-filtered-count');
        
        const pageSize = 10;
        let currentPage = 1;
        let currentGenderFilter = 'ALL';
        let matchingRows = [];

        // Check All handler for currently visible rows across page
        if(checkAll) {
            checkAll.addEventListener('change', function() {
                matchingRows.forEach(row => {
                    const cb = row.querySelector('.peserta-checkbox');
                    if (cb) cb.checked = checkAll.checked;
                });
            });
        }

        // Click row to toggle check
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

        // Filter and Paginate function
        function applyFilterAndPagination() {
            const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
            
            // 1. Filter matching rows
            matchingRows = rows.filter(row => {
                const name = row.getAttribute('data-name') || '';
                const niup = row.getAttribute('data-niup') || '';
                const nis = row.getAttribute('data-nis') || '';
                const nisn = row.getAttribute('data-nisn') || '';
                const gender = row.getAttribute('data-gender') || '';

                const matchesSearch = !query || name.includes(query) || niup.includes(query) || nis.includes(query) || nisn.includes(query);
                const matchesGender = (currentGenderFilter === 'ALL') || (gender === currentGenderFilter);

                return matchesSearch && matchesGender;
            });

            // 2. Calculate total pages
            const totalMatching = matchingRows.length;
            const totalPages = Math.max(1, Math.ceil(totalMatching / pageSize));
            
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            // 3. Render row visibility based on current page
            rows.forEach(row => {
                row.style.display = 'none';
            });

            const startIdx = (currentPage - 1) * pageSize;
            const endIdx = startIdx + pageSize;

            matchingRows.slice(startIdx, endIdx).forEach(row => {
                row.style.display = '';
            });

            // 4. Update UI Displays
            if (currentPageEl) currentPageEl.innerText = currentPage;
            if (totalPageEl) totalPageEl.innerText = totalPages;
            if (totalFilteredEl) totalFilteredEl.innerText = totalMatching;

            if (btnPrev) btnPrev.disabled = (currentPage <= 1);
            if (btnNext) btnNext.disabled = (currentPage >= totalPages || totalMatching === 0);
        }

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                currentPage = 1;
                applyFilterAndPagination();
            });
        }

        genderBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                genderBtns.forEach(b => {
                    b.classList.remove('bg-white', 'text-emerald-800', 'shadow-2xs', 'font-bold');
                    b.classList.add('text-surface-600', 'font-semibold');
                });
                this.classList.add('bg-white', 'text-emerald-800', 'shadow-2xs', 'font-bold');
                this.classList.remove('text-surface-600', 'font-semibold');
                currentGenderFilter = this.getAttribute('data-gender');
                currentPage = 1;
                applyFilterAndPagination();
            });
        });

        if (btnPrev) {
            btnPrev.addEventListener('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    applyFilterAndPagination();
                }
            });
        }

        if (btnNext) {
            btnNext.addEventListener('click', function() {
                currentPage++;
                applyFilterAndPagination();
            });
        }

        // Initial call
        applyFilterAndPagination();
    });

    function submitPenempatan() {
        const hiddenEl = document.getElementById('hidden_rombel_id');
        const selectedRombelId = hiddenEl ? hiddenEl.value : '';
        const checkedCount = document.querySelectorAll('.peserta-checkbox:checked').length;

        if (!selectedRombelId || selectedRombelId === '') {
            alert('⚠️ Harap klik dan pilih Kelas Tujuan terlebih dahulu pada Section 1!');
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
