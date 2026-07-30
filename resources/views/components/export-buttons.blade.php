@props([
    'title' => 'Export Data',
    'routePrint' => '#',
    'routeExcel' => '#',
    'routePdf' => '#',
])

<div class="flex items-center gap-1.5">
    @if($routePrint !== '#')
    <button onclick="window.print()" class="px-2.5 py-1.5 rounded-lg border border-surface-200 bg-white text-surface-700 hover:bg-surface-50 text-xs font-semibold flex items-center gap-1.5 shadow-sm transition-all" title="Cetak Halaman Ini">
        <i data-lucide="printer" class="w-3.5 h-3.5 text-surface-500"></i>
        <span class="hidden sm:inline">Cetak</span>
    </button>
    @endif

    @if($routeExcel !== '#')
    <a href="{{ $routeExcel }}" class="px-2.5 py-1.5 rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-semibold flex items-center gap-1.5 transition-all" title="Export Excel">
        <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5 text-emerald-600"></i>
        <span class="hidden sm:inline">Excel</span>
    </a>
    @endif

    @if($routePdf !== '#')
    <a href="{{ $routePdf }}" class="px-2.5 py-1.5 rounded-lg border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100 text-xs font-semibold flex items-center gap-1.5 transition-all" title="Export PDF">
        <i data-lucide="file-text" class="w-3.5 h-3.5 text-rose-600"></i>
        <span class="hidden sm:inline">PDF</span>
    </a>
    @endif
</div>
