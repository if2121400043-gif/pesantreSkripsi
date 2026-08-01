@extends('layouts.app')

@section('title', 'Input Presensi Santri — PP Nurul Furqon')

@section('content')
<div class="space-y-6">

    {{-- Hero Header Banner --}}
    <div class="rounded-3xl p-6 md:p-8 shadow-lg relative overflow-hidden text-white" style="background: linear-gradient(135deg, #047857, #065f46) !important; color: #ffffff !important;">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 text-xs font-semibold backdrop-blur-sm border border-white/20 mb-3" style="color: #ffffff !important;">
                    <i data-lucide="check-square" class="w-3.5 h-3.5 text-warning-300"></i>
                    Modul Absensi Harian Santri
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold font-heading" style="color: #ffffff !important;">
                    Input Presensi Kehadiran Santri
                </h1>
                <p class="text-xs md:text-sm mt-1 max-w-xl" style="color: #a7f3d0 !important;">
                    Catat kehadiran santri secara presisi berdasarkan kegiatan, rombel kelas, atau kamar asrama.
                </p>
            </div>
            
            @if(isset($tahunAktif) && $tahunAktif)
                <div class="px-4 py-2.5 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 text-xs shrink-0 flex items-center gap-2">
                    <i data-lucide="calendar" class="w-4 h-4 text-emerald-300"></i>
                    <div>
                        <div class="text-[0.65rem] text-emerald-200 uppercase tracking-wider font-extrabold">TAHUN PELAJARAN AKTIF</div>
                        <div class="font-bold text-white">{{ $tahunAktif->nama_tahun ?? $tahunAktif->nama }} ({{ $tahunAktif->semester === 'GANJIL' ? 'Ganjil' : 'Genap' }})</div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-start gap-3 shadow-2xs">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5"></i>
            <div class="text-xs font-bold">{{ session('success') }}</div>
        </div>
    @endif

    {{-- FILTER BAR HORIZONAL (Bebas Bug Overflow) --}}
    <div class="bg-white p-6 rounded-3xl border border-surface-200 shadow-sm space-y-4">
        <div class="flex items-center gap-2 pb-3 border-b border-surface-100">
            <i data-lucide="filter" class="w-4 h-4 text-emerald-700"></i>
            <h3 class="font-extrabold text-surface-900 text-sm">Filter Parameter Presensi</h3>
        </div>

        <form action="{{ route('admin.presensi.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            {{-- 1. Jenis Presensi --}}
            <div>
                <label for="jenis_presensi_id" class="block text-xs font-bold text-surface-700 mb-1">
                    1. Jenis Kegiatan / Presensi <span class="text-rose-500">*</span>
                </label>
                <select id="jenis_presensi_id" name="jenis_presensi_id" 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-semibold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs" 
                        onchange="this.form.submit()">
                    <option value="" disabled {{ !$selectedJenis ? 'selected' : '' }}>-- Pilih Jenis Presensi --</option>
                    @foreach($jenisPresensiList as $jp)
                        <option value="{{ $jp->id }}" {{ ($selectedJenis?->id == $jp->id) ? 'selected' : '' }}>
                            {{ $jp->nama }} ({{ $jp->tipe_target === 'PER_ROMBEL' ? 'Per Rombel' : ($jp->tipe_target === 'PER_ASRAMA' ? 'Per Asrama' : 'Semua Santri') }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- 2. Tanggal Presensi --}}
            <div>
                <label for="tanggal" class="block text-xs font-bold text-surface-700 mb-1">
                    2. Tanggal Presensi <span class="text-rose-500">*</span>
                </label>
                <input type="date" id="tanggal" name="tanggal" 
                       class="w-full px-3.5 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-semibold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs" 
                       value="{{ $tanggal }}" 
                       onchange="this.form.submit()">
            </div>

            {{-- 3. Dynamic Filter (Per Rombel / Per Asrama) --}}
            <div>
                @if($selectedJenis)
                    @if($selectedJenis->tipe_target === 'PER_ROMBEL')
                        <label for="rombel_id" class="block text-xs font-bold text-surface-700 mb-1">
                            3. Pilih Kelas / Rombel Target <span class="text-rose-500">*</span>
                        </label>
                        <select id="rombel_id" name="rombel_id" 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-semibold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs" 
                                onchange="this.form.submit()">
                            <option value="" disabled {{ !$selectedRombel ? 'selected' : '' }}>-- Pilih Rombel --</option>
                            @foreach($rombels as $rombel)
                                <option value="{{ $rombel->id }}" {{ ($selectedRombel?->id == $rombel->id) ? 'selected' : '' }}>
                                    {{ $rombel->nama_rombel ?? $rombel->nama }}
                                </option>
                            @endforeach
                        </select>
                    @elseif($selectedJenis->tipe_target === 'PER_ASRAMA')
                        <label for="asrama_id" class="block text-xs font-bold text-surface-700 mb-1">
                            3. Pilih Kamar / Asrama Target <span class="text-rose-500">*</span>
                        </label>
                        <select id="asrama_id" name="asrama_id" 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-semibold text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs" 
                                onchange="this.form.submit()">
                            <option value="" disabled {{ !$selectedAsrama ? 'selected' : '' }}>-- Pilih Asrama --</option>
                            @foreach($asramas as $asrama)
                                <option value="{{ $asrama->id }}" {{ ($selectedAsrama?->id == $asrama->id) ? 'selected' : '' }}>
                                    {{ $asrama->nama_asrama ?? $asrama->nama }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <div class="pt-6 text-xs font-bold text-emerald-700 flex items-center gap-1.5">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            Presensi berlaku untuk seluruh santri aktif
                        </div>
                    @endif
                @else
                    <label class="block text-xs font-bold text-surface-400 mb-1">3. Target Rombel / Asrama</label>
                    <div class="px-3.5 py-2.5 rounded-xl border border-surface-200 bg-surface-50 text-xs text-surface-400 font-medium">
                        Pilih jenis presensi terlebih dahulu...
                    </div>
                @endif
            </div>
        </form>
    </div>

    {{-- KONTEN UTAMA PRESENSI --}}
    <div class="bg-white rounded-3xl border border-surface-200 shadow-sm overflow-hidden p-6 md:p-8">

        @if(!$selectedJenis)
            <div class="p-12 text-center text-surface-500 max-w-md mx-auto space-y-3">
                <div class="w-16 h-16 bg-emerald-50 text-emerald-700 rounded-3xl flex items-center justify-center mx-auto border border-emerald-100 shadow-2xs">
                    <i data-lucide="clipboard-list" class="w-8 h-8"></i>
                </div>
                <h3 class="text-base font-extrabold text-surface-900">Silakan Pilih Jenis Presensi</h3>
                <p class="text-xs text-surface-450 leading-relaxed">Pilih jenis kegiatan presensi pada panel filter di atas untuk mulai mencatat kehadiran santri.</p>
            </div>

        @elseif(($selectedJenis->tipe_target === 'PER_ROMBEL' && !$selectedRombel) || ($selectedJenis->tipe_target === 'PER_ASRAMA' && !$selectedAsrama))
            <div class="p-12 text-center text-surface-500 max-w-md mx-auto space-y-3">
                <div class="w-16 h-16 bg-blue-50 text-blue-700 rounded-3xl flex items-center justify-center mx-auto border border-blue-100 shadow-2xs">
                    <i data-lucide="users" class="w-8 h-8"></i>
                </div>
                <h3 class="text-base font-extrabold text-surface-900">
                    Silakan Pilih {{ $selectedJenis->tipe_target === 'PER_ROMBEL' ? 'Kelas / Rombel' : 'Kamar / Asrama' }}
                </h3>
                <p class="text-xs text-surface-450 leading-relaxed">
                    Pilih target {{ $selectedJenis->tipe_target === 'PER_ROMBEL' ? 'kelas' : 'asrama' }} pada dropdown di atas untuk menampilkan daftar santri.
                </p>
            </div>

        @elseif($peserta->isEmpty())
            <div class="p-12 text-center text-surface-500 max-w-md mx-auto space-y-3">
                <div class="w-16 h-16 bg-rose-50 text-rose-600 rounded-3xl flex items-center justify-center mx-auto border border-rose-100 shadow-2xs">
                    <i data-lucide="user-x" class="w-8 h-8"></i>
                </div>
                <h3 class="text-base font-extrabold text-surface-900">Belum Ada Santri Ditemukan</h3>
                <p class="text-xs text-surface-450 leading-relaxed">Tidak ada data santri aktif yang terdaftar untuk kelas atau kriteria yang dipilih.</p>
            </div>

        @else
            {{-- HEADER DETIL PRESENSI & QUICK ACTIONS --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-5 border-b border-surface-200">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[0.65rem] font-black uppercase bg-emerald-100 text-emerald-800">
                            {{ $selectedJenis->nama }}
                        </span>
                        <span class="text-xs text-surface-400 font-medium">•</span>
                        <span class="text-xs font-bold text-surface-600">
                            {{ \Carbon\Carbon::parse($tanggal)->isoFormat('D MMMM Y') }}
                        </span>
                    </div>

                    <h2 class="text-xl font-extrabold text-surface-900 mt-1">
                        Daftar Presensi: 
                        @if($selectedJenis->tipe_target === 'PER_ROMBEL' && $selectedRombel)
                            {{ $selectedRombel->nama_rombel ?? $selectedRombel->nama }}
                        @elseif($selectedJenis->tipe_target === 'PER_ASRAMA' && $selectedAsrama)
                            {{ $selectedAsrama->nama_asrama ?? $selectedAsrama->nama }}
                        @else
                            Semua Santri
                        @endif
                    </h2>
                </div>

                {{-- Fast Action: Set All HADIR --}}
                <div class="flex items-center gap-2">
                    <button type="button" onclick="setAllStatus('HADIR')" class="px-3.5 py-2 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 font-extrabold text-xs hover:bg-emerald-100 transition-all flex items-center gap-1.5 shadow-2xs">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
                        <span>Set Semua HADIR</span>
                    </button>
                    <button type="button" onclick="setAllStatus('ALPHA')" class="px-3.5 py-2 rounded-xl bg-rose-50 text-rose-800 border border-rose-200 font-extrabold text-xs hover:bg-rose-100 transition-all flex items-center gap-1.5 shadow-2xs">
                        <i data-lucide="x-circle" class="w-4 h-4 text-rose-600"></i>
                        <span>Set Semua ALPHA</span>
                    </button>
                </div>
            </div>

            {{-- FORM PRESENSI --}}
            <form action="{{ route('admin.presensi.store') }}" method="POST" class="mt-6 space-y-6">
                @csrf
                <input type="hidden" name="jenis_presensi_id" value="{{ $selectedJenis->id }}">
                <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                @if($selectedJenis->tipe_target === 'PER_ROMBEL' && $selectedRombel)
                    <input type="hidden" name="rombel_id" value="{{ $selectedRombel->id }}">
                @endif
                @if($selectedJenis->tipe_target === 'PER_ASRAMA' && $selectedAsrama)
                    <input type="hidden" name="asrama_id" value="{{ $selectedAsrama->id }}">
                @endif

                {{-- TABEL PRESENSI --}}
                <div class="rounded-2xl border border-surface-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs whitespace-nowrap" style="table-layout: fixed;">
                            <thead class="bg-surface-100/80 text-surface-600 border-b border-surface-200 font-bold uppercase text-[0.65rem]">
                                <tr>
                                    <th class="px-4 py-3 text-center" style="width: 5%;">No</th>
                                    <th class="px-4 py-3" style="width: 35%;">Identitas Santri</th>
                                    <th class="px-4 py-3 text-center" style="width: 30%;">Status Kehadiran</th>
                                    <th class="px-4 py-3" style="width: 30%;">Catatan / Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-100 text-surface-800">
                                @foreach($peserta as $p)
                                    @php
                                        $currentStatus = old('presensi.'.$p->id.'.status', $presensiHariIni[$p->id]->status ?? 'HADIR');
                                        $currentKeterangan = old('presensi.'.$p->id.'.keterangan', $presensiHariIni[$p->id]->keterangan ?? '');
                                        $namaSantri = $p->orang?->nama_lengkap ?? '-';
                                    @endphp
                                    <tr class="hover:bg-surface-50/80 transition-colors">
                                        <td class="px-4 py-3 text-center font-bold text-surface-400">{{ $loop->iteration }}</td>
                                        
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center shrink-0 border" style="background-color: #ecfdf5; color: #047857; border-color: #a7f3d0;">
                                                    {{ substr($namaSantri, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="font-extrabold text-surface-900 text-xs">{{ $namaSantri }}</div>
                                                    <div class="text-[0.68rem] text-surface-500 font-mono">NIUP: {{ $p->orang?->niup ?? '-' }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- STATUS RADIO BUTTON PILLS --}}
                                        <td class="px-4 py-3">
                                            <div class="flex items-center justify-center gap-2">
                                                {{-- HADIR --}}
                                                <label class="cursor-pointer">
                                                    <input type="radio" name="presensi[{{ $p->id }}][status]" value="HADIR" class="peer sr-only status-radio-hadir" {{ $currentStatus === 'HADIR' ? 'checked' : '' }}>
                                                    <div class="px-3 py-1.5 rounded-xl border border-surface-300 text-surface-600 text-xs font-bold peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-emerald-600 hover:border-emerald-400 transition-all shadow-2xs flex items-center gap-1">
                                                        <span>H</span>
                                                        <span class="hidden sm:inline text-[0.65rem] font-semibold">Hadir</span>
                                                    </div>
                                                </label>
                                                
                                                {{-- SAKIT --}}
                                                <label class="cursor-pointer">
                                                    <input type="radio" name="presensi[{{ $p->id }}][status]" value="SAKIT" class="peer sr-only status-radio-sakit" {{ $currentStatus === 'SAKIT' ? 'checked' : '' }}>
                                                    <div class="px-3 py-1.5 rounded-xl border border-surface-300 text-surface-600 text-xs font-bold peer-checked:bg-amber-500 peer-checked:text-white peer-checked:border-amber-500 hover:border-amber-400 transition-all shadow-2xs flex items-center gap-1">
                                                        <span>S</span>
                                                        <span class="hidden sm:inline text-[0.65rem] font-semibold">Sakit</span>
                                                    </div>
                                                </label>

                                                {{-- IZIN --}}
                                                <label class="cursor-pointer">
                                                    <input type="radio" name="presensi[{{ $p->id }}][status]" value="IZIN" class="peer sr-only status-radio-izin" {{ $currentStatus === 'IZIN' ? 'checked' : '' }}>
                                                    <div class="px-3 py-1.5 rounded-xl border border-surface-300 text-surface-600 text-xs font-bold peer-checked:bg-sky-500 peer-checked:text-white peer-checked:border-sky-500 hover:border-sky-400 transition-all shadow-2xs flex items-center gap-1">
                                                        <span>I</span>
                                                        <span class="hidden sm:inline text-[0.65rem] font-semibold">Izin</span>
                                                    </div>
                                                </label>

                                                {{-- ALPHA --}}
                                                <label class="cursor-pointer">
                                                    <input type="radio" name="presensi[{{ $p->id }}][status]" value="ALPHA" class="peer sr-only status-radio-alpha" {{ $currentStatus === 'ALPHA' ? 'checked' : '' }}>
                                                    <div class="px-3 py-1.5 rounded-xl border border-surface-300 text-surface-600 text-xs font-bold peer-checked:bg-rose-600 peer-checked:text-white peer-checked:border-rose-600 hover:border-rose-400 transition-all shadow-2xs flex items-center gap-1">
                                                        <span>A</span>
                                                        <span class="hidden sm:inline text-[0.65rem] font-semibold">Alpha</span>
                                                    </div>
                                                </label>
                                            </div>
                                        </td>

                                        {{-- CATATAN / KETERANGAN --}}
                                        <td class="px-4 py-3">
                                            <input type="text" name="presensi[{{ $p->id }}][keterangan]" 
                                                   class="w-full px-3 py-1.5 text-xs rounded-xl border border-surface-300 bg-white text-surface-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors" 
                                                   placeholder="Catatan opsional (misal: Demam, Izin Pulang)..." 
                                                   value="{{ $currentKeterangan }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- SUBMIT BAR TERPADU --}}
                <div class="pt-4 border-t border-surface-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-xs text-surface-600 font-medium">
                        Total Santri: <strong class="text-surface-900">{{ count($peserta) }} Orang</strong>
                    </div>

                    <button type="submit" class="w-full sm:w-auto px-8 py-3 rounded-2xl font-black text-xs shadow-xl transition-all flex items-center justify-center gap-2 hover:scale-105" style="background-color: #fbbf24 !important; color: #1e1b4b !important;">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>SIMPAN PRESENSI SANTRI</span>
                    </button>
                </div>
            </form>
        @endif

    </div>

</div>
@endsection

@push('scripts')
<script>
    function setAllStatus(statusVal) {
        let valClass = statusVal.toLowerCase();
        let radios = document.querySelectorAll('.status-radio-' + valClass);
        radios.forEach(radio => {
            radio.checked = true;
            radio.dispatchEvent(new Event('change', { bubbles: true }));
        });
    }
</script>
@endpush
