@extends('layouts.app')

@section('title', 'Dashboard')
@section('meta_description', 'Dashboard Admin Sistem Manajemen Pondok Pesantren Nurul Furqon')

@section('page_header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-surface-900 font-heading">Dashboard</h1>
            <p class="text-sm text-surface-500 mt-1">Selamat datang kembali, <span class="font-semibold text-surface-700">{{ auth()->user()->name ?? 'Admin' }}</span></p>
        </div>
        <div class="flex items-center gap-2">
            @if($tahunAktif)
            <span class="badge badge-success" dot>
                <span class="w-1.5 h-1.5 rounded-full bg-success-500 animate-pulse"></span>
                TA {{ $tahunAktif->nama }} Aktif
            </span>
            @else
            <span class="badge badge-warning" dot>
                <span class="w-1.5 h-1.5 rounded-full bg-warning-500"></span>
                Belum Ada TA Aktif
            </span>
            @endif
        </div>
    </div>
@endsection

@section('content')
    {{-- Stats Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
        <x-stat-card
            label="Total Santri Aktif"
            value="{{ number_format($totalSantri, 0, ',', '.') }}"
            icon="graduation-cap"
            color="primary"
            trend="up"
            trendValue="Terdaftar"
            href="/admin/peserta-didik" />

        <x-stat-card
            label="Total Pegawai"
            value="{{ number_format($totalPegawai, 0, ',', '.') }}"
            icon="briefcase"
            color="secondary"
            trend="up"
            trendValue="Aktif" />

        <x-stat-card
            label="Rombongan Belajar"
            value="{{ number_format($totalRombel, 0, ',', '.') }}"
            icon="book-open"
            color="accent" />

        <x-stat-card
            label="Pendaftar PSB"
            value="{{ number_format($totalPendaftar, 0, ',', '.') }}"
            icon="user-plus"
            color="warning"
            trend="up"
            href="/admin/psb/gelombang" />
    </div>

    {{-- Stats Row Perizinan (PEDATREN Visuals) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
        <x-stat-card
            label="Santri Dalam Masa Izin"
            value="{{ number_format($dalamMasaIzinCount, 0, ',', '.') }}"
            icon="door-open"
            color="success"
            href="{{ route('admin.perizinan.index') }}" />

        <x-stat-card
            label="Santri Terlambat / Telat Kembali"
            value="{{ number_format($telatBelumKembaliCount, 0, ',', '.') }}"
            icon="clock"
            color="danger"
            href="{{ route('admin.perizinan.index') }}" />
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Santri per Lembaga --}}
        <x-card title="Santri per Lembaga" class="lg:col-span-2">
            <x-slot:headerActions>
                <select class="text-xs border border-surface-200 rounded-lg px-2 py-1 text-surface-600 bg-white" id="chart-lembaga-filter">
                    <option>Tahun Ini</option>
                    <option>Tahun Lalu</option>
                </select>
            </x-slot:headerActions>
            <div class="h-64 flex items-center justify-center" id="chart-lembaga-container">
                <canvas id="chart-lembaga"></canvas>
            </div>
        </x-card>

        {{-- Distribusi Asrama --}}
        <x-card title="Distribusi Asrama">
            <div class="h-64 flex items-center justify-center" id="chart-asrama-container">
                <canvas id="chart-asrama"></canvas>
            </div>
        </x-card>
    </div>

    {{-- Bottom Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Tagihan Bulan Ini --}}
        <x-card title="Rekapitulasi Tagihan">
            <x-slot:headerActions>
                <span class="badge badge-accent">{{ $rekapTagihan['bulan'] ?? '' }}</span>
            </x-slot:headerActions>
            <div class="space-y-4">
                {{-- Progress bars --}}
                <div>
                    <div class="flex items-center justify-between text-sm mb-1.5">
                        <span class="text-surface-600">Lunas</span>
                        <span class="font-semibold text-success-700">{{ $rekapTagihan['persenLunas'] ?? 0 }}%</span>
                    </div>
                    <div class="w-full h-2.5 bg-surface-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-success-500 to-primary-500 rounded-full transition-all duration-1000" style="width: {{ $rekapTagihan['persenLunas'] ?? 0 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between text-sm mb-1.5">
                        <span class="text-surface-600">Belum Lunas</span>
                        <span class="font-semibold text-warning-700">{{ $rekapTagihan['persenBelumLunas'] ?? 0 }}%</span>
                    </div>
                    <div class="w-full h-2.5 bg-surface-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-warning-500 to-accent-500 rounded-full transition-all duration-1000" style="width: {{ $rekapTagihan['persenBelumLunas'] ?? 0 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between text-sm mb-1.5">
                        <span class="text-surface-600">Tunggakan</span>
                        <span class="font-semibold text-danger-500">{{ $rekapTagihan['persenTunggakan'] ?? 0 }}%</span>
                    </div>
                    <div class="w-full h-2.5 bg-surface-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-danger-500 to-danger-700 rounded-full transition-all duration-1000" style="width: {{ $rekapTagihan['persenTunggakan'] ?? 0 }}%"></div>
                    </div>
                </div>

                {{-- Summary --}}
                <div class="pt-4 border-t border-surface-100 grid grid-cols-3 gap-4 text-center">
                    <div>
                        <p class="text-lg font-bold text-surface-900 font-heading">Rp {{ number_format(($rekapTagihan['total'] ?? 0) / 1000000, 1, ',', '') }}jt</p>
                        <p class="text-xs text-surface-400">Total Tagihan</p>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-success-700 font-heading">Rp {{ number_format(($rekapTagihan['terbayar'] ?? 0) / 1000000, 1, ',', '') }}jt</p>
                        <p class="text-xs text-surface-400">Terbayar</p>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-danger-500 font-heading">Rp {{ number_format(($rekapTagihan['sisa'] ?? 0) / 1000000, 1, ',', '') }}jt</p>
                        <p class="text-xs text-surface-400">Sisa</p>
                    </div>
                </div>
            </div>
        </x-card>

        {{-- Aktivitas Terbaru --}}
        <x-card title="Aktivitas Terbaru" :padding="false">
            <x-slot:headerActions>
                <a href="#" class="text-xs text-primary-600 font-semibold hover:text-primary-700">Lihat Semua</a>
            </x-slot:headerActions>
            <div class="divide-y divide-surface-50">
                @forelse($recentActivities as $activity)
                <div class="flex gap-3 px-6 py-3.5 hover:bg-surface-50/50 transition-colors">
                    <div class="w-9 h-9 rounded-full {{ $activity['bg_color'] }} flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i data-lucide="{{ $activity['icon'] }}" class="w-4 h-4 {{ $activity['icon_color'] }}"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm text-surface-800">{!! $activity['title'] !!}</p>
                        <p class="text-xs text-surface-400 mt-0.5 flex items-center gap-1">
                            <i data-lucide="clock" class="w-3 h-3"></i> {{ \Carbon\Carbon::parse($activity['time'])->diffForHumans() }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-surface-400 text-sm">
                    Belum ada aktivitas terbaru.
                </div>
                @endforelse
            </div>
        </x-card>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
if (typeof Chart === 'undefined') {
    document.write('<script src="{{ asset("js/chart.umd.min.js") }}"><\/script>');
}
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart === 'undefined') return;

    // Chart defaults
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.plugins.legend.labels.usePointStyle = true;

    // Bar Chart — Santri per Lembaga
    const ctxBar = document.getElementById('chart-lembaga');
    if (ctxBar) {
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: {!! json_encode($labelsLembaga ?? []) !!},
                datasets: [{
                    label: 'Putra',
                    data: {!! json_encode($dataPutra ?? []) !!},
                    backgroundColor: '#0d9266',
                    borderRadius: 6,
                    borderSkipped: false,
                }, {
                    label: 'Putri',
                    data: {!! json_encode($dataPutri ?? []) !!},
                    backgroundColor: '#0284c7',
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        ticks: { color: '#888' },
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#888' },
                    }
                }
            }
        });
    }

    // Doughnut Chart — Distribusi Asrama
    const ctxDoughnut = document.getElementById('chart-asrama');
    const labelsAsrama = {!! json_encode($labelsAsrama ?? []) !!};
    const dataAsrama = {!! json_encode($dataAsrama ?? []) !!};
    const sumAsrama = dataAsrama.reduce((a, b) => a + b, 0);

    if (ctxDoughnut) {
        if (labelsAsrama.length === 0 || sumAsrama === 0) {
            const container = document.getElementById('chart-asrama-container');
            if (container) {
                container.innerHTML = `
                    <div class="text-center text-surface-400 p-6">
                        <i data-lucide="home" class="w-8 h-8 mx-auto mb-2 text-surface-300"></i>
                        <p class="text-xs font-semibold text-surface-600">Belum Ada Data Distribusi Asrama</p>
                        <p class="text-[0.7rem] text-surface-400 mt-0.5">Tambahkan data kamar & mukim di menu Asrama</p>
                    </div>
                `;
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }
        } else {
            const palette = ['#0d9266', '#0284c7', '#f59e0b', '#8b5cf6', '#14b8a6', '#ec4899', '#06b6d4', '#10b981'];
            const colors = labelsAsrama.map((_, i) => palette[i % palette.length]);

            new Chart(ctxDoughnut, {
                type: 'doughnut',
                data: {
                    labels: labelsAsrama,
                    datasets: [{
                        data: dataAsrama,
                        backgroundColor: colors,
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { padding: 12, boxWidth: 10 },
                        }
                    }
                }
            });
        }
    }
});
</script>
@endpush
