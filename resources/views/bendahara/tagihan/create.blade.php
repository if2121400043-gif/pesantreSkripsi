@extends('layouts.app')

@section('title', 'Generate Tagihan Massal')

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <div class="flex items-center gap-2 text-sm text-surface-500 mb-2">
            <a href="{{ route('bendahara.tagihan.index') }}" class="hover:text-primary-600 transition-colors">Data Tagihan</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <span class="text-surface-900 font-medium">Generate Massal</span>
        </div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">Generate Tagihan Baru</h1>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <x-card title="Form Generate Tagihan Massal">
        <form action="{{ route('bendahara.tagihan.store') }}" method="POST">
            @csrf
            <div class="space-y-6">
                
                {{-- Detail Tagihan --}}
                <div class="bg-surface-50 p-4 rounded-xl border border-surface-200 space-y-4">
                    <h3 class="font-bold text-surface-900 flex items-center gap-2 mb-2">
                        <i data-lucide="receipt" class="w-5 h-5 text-primary-500"></i>
                        1. Informasi Tagihan
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Komponen Biaya <span class="text-danger-500">*</span></label>
                            <select name="komponen_biaya_id" id="komponen_biaya_id" required class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                                <option value="" disabled selected>Pilih Biaya...</option>
                                @foreach($komponenBiayas as $biaya)
                                    <option value="{{ $biaya->id }}" data-jenis="{{ $biaya->jenis }}">
                                        {{ $biaya->nama }} - Rp {{ number_format($biaya->nominal, 0, ',', '.') }} ({{ $biaya->jenis }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div id="bulan-container" class="hidden">
                            <label class="block text-sm font-medium text-surface-700 mb-1">Bulan Tagihan <span class="text-danger-500">*</span></label>
                            <input type="month" name="bulan" id="bulan" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                            <p class="text-[0.65rem] text-surface-500 mt-1">Hanya wajib untuk jenis tagihan Bulanan (Contoh SPP).</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Jatuh Tempo (Opsional)</label>
                            <input type="date" name="jatuh_tempo" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        </div>
                    </div>
                </div>

                {{-- Target Santri --}}
                <div class="bg-surface-50 p-4 rounded-xl border border-surface-200 space-y-4">
                    <h3 class="font-bold text-surface-900 flex items-center gap-2 mb-2">
                        <i data-lucide="users" class="w-5 h-5 text-primary-500"></i>
                        2. Target Penerima Tagihan
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Tahun Ajaran <span class="text-danger-500">*</span></label>
                            <select name="tahun_pelajaran_id" required class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                                @foreach($tahuns as $tahun)
                                    <option value="{{ $tahun->id }}" {{ $tahun->is_active ? 'selected' : '' }}>
                                        {{ $tahun->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1">Lembaga Target (Opsional)</label>
                            <select name="lembaga_id" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                                <option value="">Seluruh Santri Aktif</option>
                                @foreach($lembagas as $lembaga)
                                    <option value="{{ $lembaga->id }}">{{ $lembaga->nama }}</option>
                                @endforeach
                            </select>
                            <p class="text-[0.65rem] text-surface-500 mt-1">Kosongkan jika tagihan ditujukan untuk semua santri di semua unit.</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 bg-warning-50 p-4 rounded-xl border border-warning-200">
                    <i data-lucide="alert-triangle" class="w-6 h-6 text-warning-500 shrink-0"></i>
                    <p class="text-sm text-warning-800">
                        <strong>Perhatian:</strong> Sistem otomatis mencegah duplikasi tagihan. Jika seorang santri sudah memiliki tagihan untuk komponen, tahun ajaran, dan bulan yang sama, sistem tidak akan membuat tagihan ganda.
                    </p>
                </div>
                
                <div class="flex justify-end pt-4">
                    <a href="{{ route('bendahara.tagihan.index') }}" class="btn-secondary mr-3">Batal</a>
                    <button type="submit" class="btn-primary" onclick="return confirm('Proses ini akan men-generate tagihan untuk banyak santri sekaligus. Lanjutkan?')">Generate Tagihan</button>
                </div>
            </div>
        </form>
    </x-card>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectBiaya = document.getElementById('komponen_biaya_id');
        const bulanContainer = document.getElementById('bulan-container');
        const inputBulan = document.getElementById('bulan');
        
        function checkJenis() {
            const selected = selectBiaya.options[selectBiaya.selectedIndex];
            if (selected && selected.dataset.jenis === 'BULANAN') {
                bulanContainer.classList.remove('hidden');
                inputBulan.required = true;
            } else {
                bulanContainer.classList.add('hidden');
                inputBulan.required = false;
                inputBulan.value = '';
            }
        }
        
        selectBiaya.addEventListener('change', checkJenis);
        checkJenis(); // Initialize on load
    });
</script>
@endpush
