@extends('layouts.app')

@section('title', 'Mutasikan Santri Baru')

@section('page_header')
<div class="flex items-center gap-3">
    <a href="{{ route('admin.mutasi.index') }}" class="btn-secondary p-2 rounded-lg hover:bg-surface-100 transition-colors">
        <i data-lucide="arrow-left" class="w-5 h-5 text-surface-600"></i>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Mutasikan Santri</h1>
        <p class="text-sm text-surface-500 mt-1">Lakukan perpindahan Kamar Asrama atau Rombongan Belajar (Kelas) santri.</p>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Form Column --}}
    <div class="lg:col-span-2">
        <x-card>
            <form action="{{ route('admin.mutasi.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- 1. Pilih Santri --}}
                <div>
                    <label for="peserta_didik_id" class="block text-sm font-semibold text-surface-700 mb-1.5">Pilih Santri *</label>
                    <select name="peserta_didik_id" id="peserta_didik_id" required class="w-full px-4 py-2.5 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors">
                        <option value="" disabled selected>-- Cari & Pilih Santri --</option>
                        @foreach($santris as $s)
                            @php
                                $roomName = 'Belum Bermukim';
                                $rombelName = 'Belum Ada Kelas';
                                
                                $activeRoom = $s->riwayatMukim->first();
                                if ($activeRoom && $activeRoom->kamar) {
                                    $roomName = $activeRoom->kamar->asrama->nama . ' - Kamar ' . $activeRoom->kamar->nama;
                                }

                                $activeRombel = $s->riwayatRombel->first();
                                if ($activeRombel && $activeRombel->rombel) {
                                    $rombelName = $activeRombel->rombel->lembaga->singkatan . ' - Kelas ' . $activeRombel->rombel->nama;
                                }
                            @endphp
                            <option value="{{ $s->id }}" 
                                    data-room="{{ $roomName }}" 
                                    data-class="{{ $rombelName }}"
                                    {{ old('peserta_didik_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->orang->nama_lengkap }} (NIUP: {{ $s->orang->niup }})
                            </option>
                        @endforeach
                    </select>
                    @error('peserta_didik_id')
                        <p class="text-xs text-danger-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 2. Jenis Mutasi --}}
                <div>
                    <label class="block text-sm font-semibold text-surface-700 mb-2">Jenis Mutasi *</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="relative flex items-center justify-between p-4 rounded-xl border border-surface-200 bg-white hover:border-primary-500 cursor-pointer transition-all duration-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                    <i data-lucide="home" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-surface-900">Mutasi Kamar Asrama</p>
                                    <p class="text-xs text-surface-500 mt-0.5">Perpindahan kamar santri</p>
                                </div>
                            </div>
                            <input type="radio" name="jenis_mutasi" id="type_asrama" value="ASRAMA" class="h-4 w-4 text-primary-600 border-surface-300 focus:ring-primary-500" {{ old('jenis_mutasi', 'ASRAMA') === 'ASRAMA' ? 'checked' : '' }}>
                        </label>

                        <label class="relative flex items-center justify-between p-4 rounded-xl border border-surface-200 bg-white hover:border-primary-500 cursor-pointer transition-all duration-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                                    <i data-lucide="book-open" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-surface-900">Mutasi Rombel (Kelas)</p>
                                    <p class="text-xs text-surface-500 mt-0.5">Perpindahan kelas santri</p>
                                </div>
                            </div>
                            <input type="radio" name="jenis_mutasi" id="type_rombel" value="ROMBEL" class="h-4 w-4 text-primary-600 border-surface-300 focus:ring-primary-500" {{ old('jenis_mutasi') === 'ROMBEL' ? 'checked' : '' }}>
                        </label>
                    </div>
                </div>

                {{-- 3. Target Kamar Asrama --}}
                <div id="container_asrama" class="space-y-1.5">
                    <label for="kamar_id" class="block text-sm font-semibold text-surface-700">Pilih Kamar Tujuan *</label>
                    <select name="kamar_id" id="kamar_id" class="w-full px-4 py-2.5 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors">
                        <option value="" disabled selected>-- Pilih Kamar Asrama --</option>
                        @foreach($kamars as $k)
                            @php
                                $isFull = $k->occupants_count >= $k->kapasitas;
                            @endphp
                            <option value="{{ $k->id }}" {{ $isFull ? 'disabled class=text-danger-500' : '' }} {{ old('kamar_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->asrama->nama }} - Kamar {{ $k->nama }} (Kapasitas: {{ $k->occupants_count }}/{{ $k->kapasitas }} {{ $isFull ? '- PENUH' : 'tersedia' }})
                            </option>
                        @endforeach
                    </select>
                    @error('kamar_id')
                        <p class="text-xs text-danger-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 4. Target Rombongan Belajar --}}
                <div id="container_rombel" class="space-y-1.5 hidden">
                    <label for="rombel_id" class="block text-sm font-semibold text-surface-700">Pilih Kelas Tujuan *</label>
                    <select name="rombel_id" id="rombel_id" class="w-full px-4 py-2.5 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors">
                        <option value="" disabled selected>-- Pilih Rombongan Belajar --</option>
                        @foreach($rombels as $r)
                            @php
                                $isFull = $r->students_count >= $r->kapasitas;
                            @endphp
                            <option value="{{ $r->id }}" {{ $isFull ? 'disabled class=text-danger-500' : '' }} {{ old('rombel_id') == $r->id ? 'selected' : '' }}>
                                {{ $r->lembaga->singkatan }} - Kelas {{ $r->nama }} (Kapasitas: {{ $r->students_count }}/{{ $r->kapasitas }} {{ $isFull ? '- PENUH' : 'tersedia' }})
                            </option>
                        @endforeach
                    </select>
                    @error('rombel_id')
                        <p class="text-xs text-danger-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 5. Tanggal Mutasi --}}
                <div>
                    <label for="tanggal_mutasi" class="block text-sm font-semibold text-surface-700 mb-1.5">Tanggal Mutasi *</label>
                    <input type="date" name="tanggal_mutasi" id="tanggal_mutasi" required value="{{ old('tanggal_mutasi', date('Y-m-d')) }}" class="w-full px-4 py-2.5 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors">
                    @error('tanggal_mutasi')
                        <p class="text-xs text-danger-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 6. Keterangan / Catatan --}}
                <div>
                    <label for="keterangan" class="block text-sm font-semibold text-surface-700 mb-1.5">Keterangan / Alasan *</label>
                    <textarea name="keterangan" id="keterangan" rows="3" placeholder="Masukkan alasan mutasi atau keterangan pendukung..." class="w-full px-4 py-2.5 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <p class="text-xs text-danger-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-4 border-t border-surface-100 flex justify-end gap-3">
                    <a href="{{ route('admin.mutasi.index') }}" class="btn-secondary px-5 py-2.5">Batal</a>
                    <button type="submit" class="btn-primary px-6 py-2.5">Proses Mutasi</button>
                </div>
            </form>
        </x-card>
    </div>

    {{-- Side Information Panel --}}
    <div class="space-y-6">
        <x-card title="Posisi Santri Saat Ini">
            <div class="space-y-4">
                <div class="p-4 rounded-xl bg-surface-50 border border-surface-100">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                            <i data-lucide="home" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider">Kamar Saat Ini</p>
                            <p id="current_room_text" class="text-sm font-bold text-surface-800 mt-1">-</p>
                        </div>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-surface-50 border border-surface-100">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                            <i data-lucide="book-open" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider">Kelas Saat Ini</p>
                            <p id="current_class_text" class="text-sm font-bold text-surface-800 mt-1">-</p>
                        </div>
                    </div>
                </div>
            </div>
        </x-card>

        <div class="p-5 rounded-xl bg-gradient-to-br from-primary-500 to-secondary-600 text-white shadow-md relative overflow-hidden">
            <div class="relative z-10">
                <h4 class="font-bold font-heading text-lg">Sidang Skripsi Tips</h4>
                <p class="text-xs text-white/90 mt-2 leading-relaxed">
                    Tunjukkan pada penguji bagaimana sistem ini secara aman memproses perpindahan kelas dan kamar menggunakan <strong>Database Transaction</strong>.
                </p>
                <p class="text-xs text-white/90 mt-2 leading-relaxed">
                    Sistem ini mengarsipkan penempatan lama dan membuat penempatan baru serta mencatat audit log mutasi secara lengkap demi menjaga integritas data historis.
                </p>
            </div>
            <div class="absolute right-[-20px] bottom-[-20px] opacity-10 text-white">
                <i data-lucide="shield-check" class="w-32 h-32"></i>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const studentSelect = document.getElementById('peserta_didik_id');
    const typeAsrama = document.getElementById('type_asrama');
    const typeRombel = document.getElementById('type_rombel');
    const containerAsrama = document.getElementById('container_asrama');
    const containerRombel = document.getElementById('container_rombel');
    const currentRoomText = document.getElementById('current_room_text');
    const currentClassText = document.getElementById('current_class_text');

    // Handle student select change
    studentSelect.addEventListener('change', function() {
        const selectedOption = studentSelect.options[studentSelect.selectedIndex];
        if (selectedOption && selectedOption.value !== "") {
            const currentRoom = selectedOption.getAttribute('data-room') || 'Belum Bermukim';
            const currentClass = selectedOption.getAttribute('data-class') || 'Belum Ada Kelas';
            currentRoomText.textContent = currentRoom;
            currentClassText.textContent = currentClass;
        } else {
            currentRoomText.textContent = '-';
            currentClassText.textContent = '-';
        }
    });

    // Handle mutation type toggle
    function toggleContainers() {
        if (typeAsrama.checked) {
            containerAsrama.classList.remove('hidden');
            containerRombel.classList.add('hidden');
            document.getElementById('kamar_id').setAttribute('required', 'required');
            document.getElementById('rombel_id').removeAttribute('required');
        } else if (typeRombel.checked) {
            containerRombel.classList.remove('hidden');
            containerAsrama.classList.add('hidden');
            document.getElementById('rombel_id').setAttribute('required', 'required');
            document.getElementById('kamar_id').removeAttribute('required');
        }
    }

    typeAsrama.addEventListener('change', toggleContainers);
    typeRombel.addEventListener('change', toggleContainers);

    // Trigger initial state
    toggleContainers();
    studentSelect.dispatchEvent(new Event('change'));
});
</script>
@endpush
