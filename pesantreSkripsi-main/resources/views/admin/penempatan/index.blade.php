@extends('layouts.app')

@section('title', 'Penempatan Siswa (Plotting)')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Penempatan Kelas (Plotting)</h1>
        <p class="text-sm text-surface-500 mt-1">Atur dan tempatkan santri ke dalam rombongan belajar secara massal.</p>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Filter Bar --}}
    <x-card title="Pilih Parameter Rombel" class="border-t-4 border-t-primary-500">
        <form action="{{ route('admin.penempatan.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Lembaga <span class="text-danger-500">*</span></label>
                    <select name="lembaga_id" required class="w-full px-3 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        <option value="" disabled selected>Pilih Lembaga...</option>
                        @foreach($lembagas as $lembaga)
                            <option value="{{ $lembaga->id }}" {{ $lembagaId == $lembaga->id ? 'selected' : '' }}>
                                {{ $lembaga->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">Tahun Ajaran <span class="text-danger-500">*</span></label>
                    <select name="tahun_pelajaran_id" required class="w-full px-3 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        <option value="" disabled selected>Pilih Tahun Ajaran...</option>
                        @foreach($tahuns as $tahun)
                            <option value="{{ $tahun->id }}" {{ $tahunId == $tahun->id ? 'selected' : ($loop->first && !$tahunId ? 'selected' : '') }}>
                                {{ $tahun->nama }} {{ $tahun->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-3">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-surface-700 mb-1">Tingkat (Opsional)</label>
                        <select name="tingkat" class="w-full px-3 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                            <option value="">Semua Tingkat</option>
                            @for($i=1; $i<=15; $i++)
                                <option value="{{ $i }}" {{ $tingkat == $i ? 'selected' : '' }}>Tingkat {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <button type="submit" class="btn-primary h-[38px] px-6">Filter Data</button>
                </div>
            </div>
        </form>
    </x-card>

    @if($lembagaId && $tahunId)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Bagian Kiri: Daftar Santri Belum Dapat Kelas --}}
            <div class="lg:col-span-2 space-y-6">
                <x-card :padding="false">
                    <form action="{{ route('admin.penempatan.store') }}" method="POST" id="form-penempatan">
                        @csrf
                        <div class="p-4 border-b border-surface-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-surface-50 rounded-t-xl">
                            <div>
                                <h3 class="font-bold text-surface-900 flex items-center gap-2">
                                    <i data-lucide="users" class="w-5 h-5 text-warning-500"></i>
                                    Santri Belum Dapat Kelas
                                </h3>
                                <p class="text-xs text-surface-500 mt-1">Pilih santri yang akan ditempatkan (Bisa banyak)</p>
                            </div>
                            
                            <div class="flex gap-2 w-full sm:w-auto p-2 bg-white rounded-lg border border-surface-200">
                                <select name="rombel_id" required class="w-full sm:w-48 text-sm border-none bg-transparent focus:ring-0 py-1" id="target_rombel">
                                    <option value="" disabled selected>-- Pilih Kelas Tujuan --</option>
                                    @foreach($rombels as $rombel)
                                        <option value="{{ $rombel->id }}" data-capacity="{{ $rombel->kapasitas }}" data-filled="{{ $rombel->riwayat_peserta_count }}">
                                            Kelas {{ $rombel->tingkat ? $rombel->tingkat . '-' : '' }}{{ $rombel->nama }} 
                                            ({{ $rombel->riwayat_peserta_count }}/{{ $rombel->kapasitas }})
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" onclick="submitPenempatan()" class="btn-primary py-1 px-3 text-xs shrink-0 whitespace-nowrap">
                                    Tempatkan <i data-lucide="arrow-right" class="w-3 h-3 ml-1 inline"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
                            <table class="w-full text-left text-sm whitespace-nowrap">
                                <thead class="bg-surface-50/50 text-surface-600 sticky top-0 border-b border-surface-100 z-10 backdrop-blur-sm">
                                    <tr>
                                        <th class="px-4 py-3 font-semibold w-10 text-center">
                                            <input type="checkbox" id="check-all" class="rounded border-surface-300 text-primary-600 focus:ring-primary-500 w-4 h-4 cursor-pointer">
                                        </th>
                                        <th class="px-4 py-3 font-semibold">Nama Siswa</th>
                                        <th class="px-4 py-3 font-semibold">NIS/NISN</th>
                                        <th class="px-4 py-3 font-semibold text-center">L/P</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-surface-100 text-surface-700">
                                    @forelse($pesertaBelumDitempatkan as $peserta)
                                        <tr class="hover:bg-primary-50/50 transition-colors cursor-pointer row-clickable">
                                            <td class="px-4 py-3 text-center">
                                                <input type="checkbox" name="peserta_ids[]" value="{{ $peserta->id }}" class="peserta-checkbox rounded border-surface-300 text-primary-600 focus:ring-primary-500 w-4 h-4 cursor-pointer">
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="font-medium text-surface-900">{{ $peserta->orang->nama_lengkap }}</div>
                                                <div class="text-xs text-primary-600 font-mono">{{ $peserta->orang->niup }}</div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div>{{ $peserta->nis ?? '-' }}</div>
                                                <div class="text-xs text-surface-500">{{ $peserta->nisn ?? 'Tanpa NISN' }}</div>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="inline-flex w-6 h-6 items-center justify-center rounded-full {{ $peserta->orang->jenis_kelamin == 'L' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }} font-bold text-xs">
                                                    {{ $peserta->orang->jenis_kelamin }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-12 text-center text-surface-500">
                                                <i data-lucide="check-circle" class="w-12 h-12 text-success-400 mx-auto mb-3"></i>
                                                <p class="font-medium text-surface-900 mb-1">Semua Santri Sudah Memiliki Kelas!</p>
                                                <p class="text-sm">Tidak ada daftar santri aktif yang belum ditempatkan.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 bg-surface-50 border-t border-surface-100 text-xs text-surface-500 flex justify-between items-center">
                            <span>Total: <strong>{{ count($pesertaBelumDitempatkan) }}</strong> Santri Belum Terplot.</span>
                            <span id="selected-count" class="font-bold text-primary-600">0 Dipilih</span>
                        </div>
                    </form>
                </x-card>
            </div>

            {{-- Bagian Kanan: Status Kapasitas Kelas --}}
            <div class="lg:col-span-1 space-y-4">
                <h3 class="font-bold text-surface-900 flex items-center gap-2 mb-2">
                    <i data-lucide="layout-grid" class="w-5 h-5 text-primary-500"></i>
                    Status Kapasitas Kelas
                </h3>
                
                @forelse($rombels as $rombel)
                    <div class="bg-white border {{ $rombel->riwayat_peserta_count >= $rombel->kapasitas ? 'border-danger-200 bg-danger-50/30' : 'border-surface-200' }} rounded-xl p-4 hover:border-primary-300 transition-colors">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="font-bold text-surface-900">Kelas {{ $rombel->tingkat ? $rombel->tingkat . '-' : '' }}{{ $rombel->nama }}</h4>
                            </div>
                            <a href="{{ route('admin.rombel.show', $rombel) }}" class="text-xs text-primary-600 hover:underline">Lihat Detail</a>
                        </div>
                        
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs font-medium text-surface-600">Terisi: {{ $rombel->riwayat_peserta_count }} / {{ $rombel->kapasitas }}</span>
                            @php
                                $percentage = min(($rombel->riwayat_peserta_count / max($rombel->kapasitas, 1)) * 100, 100);
                            @endphp
                            <span class="text-xs font-bold {{ $percentage >= 100 ? 'text-danger-600' : 'text-success-600' }}">
                                {{ round($percentage) }}%
                            </span>
                        </div>
                        <div class="w-full bg-surface-200 rounded-full h-1.5">
                            @php
                                $color = $percentage >= 100 ? 'bg-danger-500' : ($percentage >= 80 ? 'bg-warning-500' : 'bg-success-500');
                            @endphp
                            <div class="{{ $color }} h-1.5 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                        
                        @if($percentage >= 100)
                            <p class="text-[0.65rem] text-danger-600 mt-2 font-medium"><i data-lucide="alert-circle" class="w-3 h-3 inline"></i> Kapasitas Penuh!</p>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-8 bg-surface-50 rounded-xl border border-surface-200 border-dashed">
                        <p class="text-sm text-surface-500">Belum ada kelas untuk parameter ini.</p>
                        <a href="{{ route('admin.rombel.create') }}" class="text-primary-600 font-medium text-sm mt-2 inline-block">Buat Kelas Baru</a>
                    </div>
                @endforelse
            </div>

        </div>
    @else
        <div class="bg-primary-50 rounded-xl p-8 text-center border border-primary-100">
            <div class="w-16 h-16 bg-white text-primary-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                <i data-lucide="filter" class="w-8 h-8"></i>
            </div>
            <h3 class="text-lg font-bold text-surface-900 mb-2">Silakan Pilih Lembaga dan Tahun Ajaran</h3>
            <p class="text-surface-500 max-w-md mx-auto">Untuk memulai plotting siswa, Anda harus memfilter berdasarkan lembaga pendidikan (SMP/SMA/MTs) dan Tahun Ajaran target terlebih dahulu melalui panel di atas.</p>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkAll = document.getElementById('check-all');
        const checkboxes = document.querySelectorAll('.peserta-checkbox');
        const countDisplay = document.getElementById('selected-count');
        const rows = document.querySelectorAll('.row-clickable');

        function updateCount() {
            if(!countDisplay) return;
            const checkedCount = document.querySelectorAll('.peserta-checkbox:checked').length;
            countDisplay.innerText = checkedCount + ' Dipilih';
            
            if(checkedCount > 0) {
                countDisplay.classList.add('bg-primary-100', 'px-2', 'py-1', 'rounded');
            } else {
                countDisplay.classList.remove('bg-primary-100', 'px-2', 'py-1', 'rounded');
            }
        }

        if(checkAll) {
            checkAll.addEventListener('change', function() {
                checkboxes.forEach(cb => {
                    cb.checked = checkAll.checked;
                });
                updateCount();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function(e) {
                e.stopPropagation(); // Prevent row click
                updateCount();
                
                // Update checkAll state
                if(!this.checked) checkAll.checked = false;
                if(document.querySelectorAll('.peserta-checkbox:checked').length === checkboxes.length) {
                    checkAll.checked = true;
                }
            });
        });

        // Click row to check
        rows.forEach(row => {
            row.addEventListener('click', function(e) {
                if(e.target.tagName !== 'INPUT') {
                    const cb = this.querySelector('.peserta-checkbox');
                    cb.checked = !cb.checked;
                    cb.dispatchEvent(new Event('change'));
                }
            });
        });
    });

    function submitPenempatan() {
        const checkedCount = document.querySelectorAll('.peserta-checkbox:checked').length;
        const targetRombel = document.getElementById('target_rombel').value;
        
        if (checkedCount === 0) {
            alert('Pilih minimal 1 santri untuk ditempatkan.');
            return;
        }
        
        if (!targetRombel) {
            alert('Pilih Kelas Tujuan terlebih dahulu.');
            return;
        }
        
        // Optional: Check capacity warning
        const option = document.querySelector('#target_rombel option:checked');
        const capacity = parseInt(option.dataset.capacity);
        const filled = parseInt(option.dataset.filled);
        
        if (filled + checkedCount > capacity) {
            if(!confirm(`PERINGATAN: Kapasitas kelas (${capacity}) akan terlampaui (menjadi ${filled + checkedCount}). Tetap lanjutkan?`)) {
                return;
            }
        } else {
            if(!confirm(`Anda akan menempatkan ${checkedCount} santri ke kelas ini. Lanjutkan?`)) {
                return;
            }
        }
        
        document.getElementById('form-penempatan').submit();
    }
</script>
@endpush
