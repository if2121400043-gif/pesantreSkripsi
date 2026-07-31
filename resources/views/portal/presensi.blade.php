@extends('layouts.portal')

@section('title', 'Kehadiran Kelas')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Kehadiran Kelas</h1>
        <p class="text-sm text-surface-500 mt-1">Pantau presensi harian ananda di kelas.</p>
    </div>
</div>
@endsection

@section('content')
<x-card>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-surface-200">
                    <th class="text-left py-3 px-4 font-semibold text-surface-600">Tanggal</th>
                    <th class="text-left py-3 px-4 font-semibold text-surface-600">Santri</th>
                    <th class="text-left py-3 px-4 font-semibold text-surface-600">Kelas / Rombel</th>
                    <th class="text-center py-3 px-4 font-semibold text-surface-600">Status</th>
                    <th class="text-left py-3 px-4 font-semibold text-surface-600">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-100">
                @forelse($presensis as $presensi)
                <tr class="hover:bg-surface-50/50 transition-colors">
                    <td class="py-3 px-4 font-medium text-surface-800">
                        {{ \Carbon\Carbon::parse($presensi->tanggal)->translatedFormat('d M Y') }}
                    </td>
                    <td class="py-3 px-4">
                        <p class="font-semibold text-surface-900">{{ $presensi->pesertaDidik->orang->nama ?? '-' }}</p>
                    </td>
                    <td class="py-3 px-4 text-surface-700">
                        {{ $presensi->rombel->nama ?? '-' }}
                    </td>
                    <td class="py-3 px-4 text-center">
                        @if($presensi->status === 'HADIR')
                            <x-badge variant="success">HADIR</x-badge>
                        @elseif($presensi->status === 'SAKIT')
                            <x-badge variant="info">SAKIT</x-badge>
                        @elseif($presensi->status === 'IZIN')
                            <x-badge variant="warning">IZIN</x-badge>
                        @else
                            <x-badge variant="danger">ALPHA</x-badge>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-surface-600">
                        {{ $presensi->keterangan ?? '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-12 text-center text-surface-400">
                        <i data-lucide="calendar-x" class="w-10 h-10 mx-auto mb-3 text-surface-300"></i>
                        <p class="font-medium text-surface-500">Belum ada data kehadiran.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($presensis->hasPages())
        <div class="mt-4 pt-4 border-t border-surface-100">
            {{ $presensis->links() }}
        </div>
    @endif
</x-card>
@endsection
