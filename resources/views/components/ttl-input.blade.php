@props([
    'tempatName' => 'tempat_lahir',
    'tanggalName' => 'tanggal_lahir',
    'tempatValue' => '',
    'tanggalValue' => '',
    'tempatLabel' => 'Tempat Lahir',
    'tanggalLabel' => 'Tanggal Lahir',
    'required' => false,
])

@php
    $oldTempat = old($tempatName, $tempatValue);
    $oldTanggal = old($tanggalName, $tanggalValue);

    $defDay = '';
    $defMonth = '';
    $defYear = '';

    if ($oldTanggal) {
        try {
            $carbonDate = \Carbon\Carbon::parse($oldTanggal);
            $defDay = $carbonDate->format('d');
            $defMonth = $carbonDate->format('m');
            $defYear = $carbonDate->format('Y');
        } catch (\Exception $e) {}
    }

    $months = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];

    $currentYear = (int) date('Y');
    $years = range($currentYear, 1950);
@endphp

<div class="grid grid-cols-1 md:grid-cols-12 gap-4">
    {{-- Tempat Lahir --}}
    <div class="md:col-span-5">
        <label class="block text-xs font-bold text-surface-700 mb-1">
            {{ $tempatLabel }} @if($required)<span class="text-danger-500">*</span>@endif
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-surface-400">
                <i data-lucide="map-pin" class="w-4 h-4"></i>
            </div>
            <input 
                type="text" 
                name="{{ $tempatName }}" 
                value="{{ $oldTempat }}" 
                placeholder="Kota / Kab Tempat Lahir" 
                class="w-full pl-9 rounded-xl border border-surface-300 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500"
                {{ $required ? 'required' : '' }}
            >
        </div>
    </div>

    {{-- Tanggal Lahir (Triple Dropdown Cepat) --}}
    <div class="md:col-span-7">
        <label class="block text-xs font-bold text-surface-700 mb-1">
            {{ $tanggalLabel }} @if($required)<span class="text-danger-500">*</span>@endif
        </label>
        
        {{-- Hidden synced input for YYYY-MM-DD --}}
        <input type="hidden" name="{{ $tanggalName }}" id="ttl_hidden_{{ $tanggalName }}" value="{{ $oldTanggal }}">

        <div class="grid grid-cols-3 gap-2" x-data="ttlPicker('{{ $tanggalName }}', '{{ $defDay }}', '{{ $defMonth }}', '{{ $defYear }}')">
            {{-- Tanggal --}}
            <div>
                <select x-model="day" @change="updateValue" class="w-full rounded-xl border border-surface-300 bg-white px-2 py-2.5 text-xs sm:text-sm font-semibold text-surface-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    <option value="" disabled>Tgl</option>
                    @for($d = 1; $d <= 31; $d++)
                        @php $dStr = str_pad($d, 2, '0', STR_PAD_LEFT); @endphp
                        <option value="{{ $dStr }}">{{ $dStr }}</option>
                    @endfor
                </select>
            </div>

            {{-- Bulan --}}
            <div>
                <select x-model="month" @change="updateValue" class="w-full rounded-xl border border-surface-300 bg-white px-2 py-2.5 text-xs sm:text-sm font-semibold text-surface-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    <option value="" disabled>Bulan</option>
                    @foreach($months as $mNum => $mName)
                        <option value="{{ $mNum }}">{{ $mName }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Tahun --}}
            <div>
                <select x-model="year" @change="updateValue" class="w-full rounded-xl border border-surface-300 bg-white px-2 py-2.5 text-xs sm:text-sm font-semibold text-surface-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    <option value="" disabled>Tahun</option>
                    @foreach($years as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<script>
    if (typeof ttlPicker !== 'function') {
        function ttlPicker(inputName, initialDay, initialMonth, initialYear) {
            return {
                day: initialDay || '',
                month: initialMonth || '',
                year: initialYear || '',
                updateValue() {
                    const hiddenInput = document.getElementById('ttl_hidden_' + inputName);
                    if (this.year && this.month && this.day) {
                        hiddenInput.value = `${this.year}-${this.month}-${this.day}`;
                    } else {
                        hiddenInput.value = '';
                    }
                }
            }
        }
    }
</script>
