@extends('layouts.app')

@section('title', 'Manajemen SDM & Pegawai — PP Nurul Furqon')

@section('content')
<div class="space-y-6">

    {{-- Hero Header Banner Premium --}}
    <div class="rounded-3xl p-6 md:p-8 shadow-lg relative overflow-hidden text-white" style="background: linear-gradient(135deg, #047857, #065f46) !important; color: #ffffff !important;">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 text-xs font-semibold backdrop-blur-sm border border-white/20 mb-3" style="color: #ffffff !important;">
                    <i data-lucide="users" class="w-3.5 h-3.5 text-warning-300"></i>
                    Direktori Sumber Daya Manusia Pesantren
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold font-heading" style="color: #ffffff !important;">
                    Manajemen SDM & Pegawai
                </h1>
                <p class="text-xs md:text-sm mt-1 max-w-xl" style="color: #a7f3d0 !important;">
                    Kelola data ustadz, guru pengajar, pengasuh santri, serta staf administrasi dan operasional pesantren secara terpadu.
                </p>
            </div>
            
            <a href="{{ route('admin.pegawai.create') }}" class="shrink-0 inline-flex items-center gap-2 px-5 py-3 rounded-2xl font-extrabold text-sm shadow-xl transition-all duration-300 hover:scale-105" style="background-color: #fbbf24 !important; color: #1e1b4b !important;">
                <i data-lucide="user-plus" class="w-5 h-5"></i>
                + Daftarkan Pegawai Baru
            </a>
        </div>
    </div>

    {{-- Interactive Filter Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card 1: Pegawai Aktif --}}
        @php $isTabAktif = ($tab === 'aktif' && !request('jenis_pegawai')); @endphp
        <a href="{{ route('admin.pegawai.index', ['tab' => 'aktif', 'search' => request('search')]) }}" 
           class="p-4 rounded-2xl border transition-all duration-200 flex items-center gap-3 group {{ $isTabAktif ? 'bg-emerald-50/90 border-emerald-500 shadow-md ring-2 ring-emerald-500/20 scale-[1.02]' : 'bg-white border-surface-200 shadow-sm hover:border-emerald-300 hover:shadow-md' }}">
            <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-200 group-hover:scale-110 transition-transform">
                <i data-lucide="user-check" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-xs text-surface-500 font-bold group-hover:text-emerald-800">Pegawai Aktif</div>
                <div class="text-xl font-black text-surface-900">{{ $countAktif }} Orang</div>
            </div>
        </a>

        {{-- Card 2: Guru & Ustadz --}}
        @php $isTabGuru = (request('jenis_pegawai') === 'GURU'); @endphp
        <a href="{{ route('admin.pegawai.index', ['tab' => 'aktif', 'jenis_pegawai' => 'GURU', 'search' => request('search')]) }}" 
           class="p-4 rounded-2xl border transition-all duration-200 flex items-center gap-3 group {{ $isTabGuru ? 'bg-blue-50/90 border-blue-500 shadow-md ring-2 ring-blue-500/20 scale-[1.02]' : 'bg-white border-surface-200 shadow-sm hover:border-blue-300 hover:shadow-md' }}">
            <div class="w-11 h-11 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 border border-blue-200 group-hover:scale-110 transition-transform">
                <i data-lucide="graduation-cap" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-xs text-surface-500 font-bold group-hover:text-blue-800">Guru & Ustadz</div>
                <div class="text-xl font-black text-surface-900">{{ $countGuru }} Pengajar</div>
            </div>
        </a>

        {{-- Card 3: Staf & Operasional --}}
        @php $isTabStaff = (request('jenis_pegawai') === 'STAFF_ADMIN'); @endphp
        <a href="{{ route('admin.pegawai.index', ['tab' => 'aktif', 'jenis_pegawai' => 'STAFF_ADMIN', 'search' => request('search')]) }}" 
           class="p-4 rounded-2xl border transition-all duration-200 flex items-center gap-3 group {{ $isTabStaff ? 'bg-indigo-50/90 border-indigo-500 shadow-md ring-2 ring-indigo-500/20 scale-[1.02]' : 'bg-white border-surface-200 shadow-sm hover:border-indigo-300 hover:shadow-md' }}">
            <div class="w-11 h-11 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0 border border-indigo-200 group-hover:scale-110 transition-transform">
                <i data-lucide="briefcase" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-xs text-surface-500 font-bold group-hover:text-indigo-800">Staf & Operasional</div>
                <div class="text-xl font-black text-surface-900">{{ $countStaff }} Pegawai</div>
            </div>
        </a>

        {{-- Card 4: Nonaktif / Arsip --}}
        @php $isTabNonaktif = ($tab === 'nonaktif'); @endphp
        <a href="{{ route('admin.pegawai.index', ['tab' => 'nonaktif', 'search' => request('search')]) }}" 
           class="p-4 rounded-2xl border transition-all duration-200 flex items-center gap-3 group {{ $isTabNonaktif ? 'bg-rose-50/90 border-rose-500 shadow-md ring-2 ring-rose-500/20 scale-[1.02]' : 'bg-white border-surface-200 shadow-sm hover:border-rose-300 hover:shadow-md' }}">
            <div class="w-11 h-11 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0 border border-rose-200 group-hover:scale-110 transition-transform">
                <i data-lucide="user-x" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-xs text-surface-500 font-bold group-hover:text-rose-800">Nonaktif / Arsip</div>
                <div class="text-xl font-black text-surface-900">{{ $countNonAktif }} Orang</div>
            </div>
        </a>
    </div>

    {{-- Search Form Container --}}
    <div class="bg-white p-4 rounded-3xl border border-surface-200 shadow-sm">
        <form action="{{ route('admin.pegawai.index') }}" method="GET" class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <input type="hidden" name="tab" value="{{ $tab }}">
            
            {{-- Search Box --}}
            <div class="flex-1 relative w-full">
                <div class="absolute top-1/2 -translate-y-1/2 text-surface-400 pointer-events-none flex items-center justify-center" style="left: 1.25rem !important;">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama Pegawai, NIP, atau NUPTK..." 
                       class="w-full pr-4 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-medium text-surface-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors"
                       style="padding-left: 3.25rem !important;">
            </div>

            {{-- Filter Jenis SDM --}}
            <div class="sm:w-56 w-full">
                <select name="jenis_pegawai" class="w-full px-3.5 py-2.5 rounded-xl border border-surface-300 bg-white text-xs font-semibold text-surface-800 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500" onchange="this.form.submit()">
                    <option value="">Semua Jenis SDM</option>
                    <option value="GURU" {{ request('jenis_pegawai') === 'GURU' ? 'selected' : '' }}>Guru / Ustadz</option>
                    <option value="PENGASUH" {{ request('jenis_pegawai') === 'PENGASUH' ? 'selected' : '' }}>Pengasuh</option>
                    <option value="STAFF_ADMIN" {{ request('jenis_pegawai') === 'STAFF_ADMIN' ? 'selected' : '' }}>Staff / Admin</option>
                    <option value="LAINNYA" {{ request('jenis_pegawai') === 'LAINNYA' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            @if(request()->anyFilled(['search', 'jenis_pegawai']))
                <a href="{{ route('admin.pegawai.index', ['tab' => $tab]) }}" class="btn-secondary px-3.5 py-2.5 rounded-xl text-xs font-bold text-danger-600 border-danger-200 hover:bg-danger-50 flex items-center justify-center gap-1 shrink-0">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    <span>Reset</span>
                </a>
            @endif
        </form>
    </div>

    {{-- GRID CARDS VIEW --}}
    <div>
        @if($pegawais->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                @foreach($pegawais as $pegawai)
                    <div class="bg-white rounded-3xl border border-surface-200 shadow-sm hover:shadow-xl hover:border-emerald-400 transition-all duration-300 p-6 md:p-7 flex flex-col justify-between relative overflow-hidden group">
                        
                        {{-- Top Section: Avatar & Status --}}
                        <div>
                            <div class="flex items-start justify-between gap-3 mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-14 h-14 rounded-2xl font-extrabold text-lg flex items-center justify-center shrink-0 shadow-md border" style="background: linear-gradient(135deg, #ecfdf5, #d1fae5) !important; color: #047857 !important; border-color: #a7f3d0 !important;">
                                        {{ substr($pegawai->orang->nama_lengkap ?? 'P', 0, 1) }}
                                    </div>
                                    <div>
                                        <h3 class="font-extrabold text-surface-900 text-base leading-snug group-hover:text-emerald-700 transition-colors">
                                            {{ $pegawai->orang->nama_lengkap }}
                                        </h3>
                                        <div class="text-xs text-surface-500 font-mono mt-0.5">
                                            NIUP: <span class="font-bold text-surface-800">{{ $pegawai->orang->niup }}</span>
                                        </div>
                                    </div>
                                </div>

                                @if($pegawai->is_active)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[0.65rem] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200 shrink-0">
                                        ● Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[0.65rem] font-extrabold bg-rose-100 text-rose-800 border border-rose-200 shrink-0">
                                        ● Nonaktif
                                    </span>
                                @endif
                            </div>

                            {{-- Role Badge & Workload Preview --}}
                            <div class="space-y-3 pt-3 border-t border-surface-100">
                                <div class="flex items-center justify-between">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[0.68rem] font-extrabold uppercase bg-blue-50 text-blue-800 border border-blue-200">
                                        {{ str_replace('_', ' ', $pegawai->jenis_pegawai) }}
                                    </span>
                                    <span class="text-xs font-semibold text-surface-500">
                                        {{ $pegawai->status_kepegawaian }}
                                    </span>
                                </div>

                                {{-- Workload Details --}}
                                @if(in_array($pegawai->jenis_pegawai, ['GURU', 'USTADZ', 'PENGASUH']))
                                    @php
                                        $mapelList = $pegawai->jadwalMengajar->pluck('mataPelajaran.nama')->filter()->unique();
                                        $totalSesi = $pegawai->jadwalMengajar->count();
                                    @endphp
                                    <div class="p-3 rounded-2xl bg-surface-50 border border-surface-200">
                                        <div class="text-[0.65rem] uppercase font-bold text-surface-500 tracking-wider mb-1">Mata Pelajaran Diampu</div>
                                        @if($mapelList->count() > 0)
                                            <div class="flex flex-wrap gap-1 mb-1.5">
                                                @foreach($mapelList->take(3) as $mName)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[0.65rem] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                        📚 {{ $mName }}
                                                    </span>
                                                @endforeach
                                                @if($mapelList->count() > 3)
                                                    <span class="text-[0.65rem] font-bold text-surface-500">+{{ $mapelList->count() - 3 }} mapel</span>
                                                @endif
                                            </div>
                                            <div class="text-xs font-bold text-emerald-800 flex items-center justify-between pt-1 border-t border-surface-200/60">
                                                <span>Beban Mengajar:</span>
                                                <span class="px-2 py-0.5 rounded-md bg-white border border-emerald-300 shadow-2xs">{{ $totalSesi }} Sesi / Minggu</span>
                                            </div>
                                        @else
                                            <div class="text-xs text-surface-400 italic">Belum ada jadwal mengajar terpasang</div>
                                        @endif
                                    </div>
                                @else
                                    <div class="p-3 rounded-2xl bg-surface-50 border border-surface-200">
                                        <div class="text-[0.65rem] uppercase font-bold text-surface-500 tracking-wider mb-1">Jabatan & Peran</div>
                                        <div class="text-xs font-bold text-surface-800">💼 {{ $pegawai->jabatan ?? 'Staf Operasional Pesantren' }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Footer Actions --}}
                        <div class="mt-5 pt-3 border-t border-surface-100 flex items-center justify-between gap-2">
                            <a href="{{ route('admin.pegawai.show', $pegawai) }}" class="flex-1 py-2 px-3 rounded-xl text-white font-bold text-xs transition-all text-center shadow-md flex items-center justify-center gap-1.5 hover:scale-102" style="color: #ffffff !important; background-color: #047857 !important;">
                                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                <span>Detail & Beban Tugas</span>
                            </a>
                            <a href="{{ route('admin.pegawai.edit', $pegawai) }}" class="p-2 rounded-xl bg-surface-100 text-surface-700 hover:bg-surface-200 transition-colors border border-surface-200" title="Edit Data">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </a>
                        </div>

                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-3xl border-2 border-dashed border-surface-200 p-12 text-center">
                <i data-lucide="users" class="w-12 h-12 text-surface-300 mx-auto mb-3"></i>
                <h3 class="font-bold text-surface-900 text-base mb-1">Belum Ada Pegawai Ditemukan</h3>
                <p class="text-xs text-surface-500">Silakan daftarkan pegawai baru atau sesuaikan kata kunci pencarian Anda.</p>
            </div>
        @endif
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $pegawais->links() }}
    </div>

</div>
@endsection
