@props([
    'name' => 'tanggal',
    'value' => '',
    'label' => 'Tanggal Pertemuan',
    'required' => false,
    'autoSubmit' => false,
])

@php
    $now = \Carbon\Carbon::now('Asia/Makassar');
    $todayStr = $now->format('Y-m-d');
    
    $rawVal = old($name, $value) ?: $todayStr;
    
    try {
        $cDate = \Carbon\Carbon::parse($rawVal);
    } catch (\Exception $e) {
        $cDate = $now;
    }

    $defDay = $cDate->format('d');
    $defMonth = $cDate->format('m');
    $defYear = $cDate->format('Y');
    $oldValue = $cDate->format('Y-m-d');

    $months = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];

    $currentYear = (int) $now->format('Y');
    $years = range($currentYear + 2, 2020);
@endphp

<div>
    @if($label)
        <label class="block text-xs font-bold text-surface-700 mb-1">
            {{ $label }} @if($required)<span class="text-danger-500">*</span>@endif
        </label>
    @endif

    {{-- Hidden synced input --}}
    <input type="hidden" name="{{ $name }}" id="datepicker_hidden_{{ $name }}" value="{{ $oldValue }}">

    <div class="grid grid-cols-3 gap-1.5 min-w-[240px]" x-data="datePickerComp('{{ $name }}', '{{ $defDay }}', '{{ $defMonth }}', '{{ $defYear }}', {{ $autoSubmit ? 'true' : 'false' }})">
        {{-- Tanggal --}}
        <div>
            <select x-model="day" @change="updateValue" class="w-full rounded-xl border border-surface-300 bg-white px-2 py-2 text-xs font-semibold text-surface-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                <option value="" disabled>Tgl</option>
                @for($d = 1; $d <= 31; $d++)
                    @php $dStr = str_pad($d, 2, '0', STR_PAD_LEFT); @endphp
                    <option value="{{ $dStr }}" {{ $defDay === $dStr ? 'selected' : '' }}>{{ $dStr }}</option>
                @endfor
            </select>
        </div>

        {{-- Bulan --}}
        <div>
            <select x-model="month" @change="updateValue" class="w-full rounded-xl border border-surface-300 bg-white px-2 py-2 text-xs font-semibold text-surface-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                <option value="" disabled>Bulan</option>
                @foreach($months as $mNum => $mName)
                    <option value="{{ $mNum }}" {{ $defMonth === $mNum ? 'selected' : '' }}>{{ $mName }}</option>
                @endforeach
            </select>
        </div>

        {{-- Tahun --}}
        <div>
            <select x-model="year" @change="updateValue" class="w-full rounded-xl border border-surface-300 bg-white px-2 py-2 text-xs font-semibold text-surface-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                <option value="" disabled>Tahun</option>
                @foreach($years as $y)
                    @php $yStr = (string)$y; @endphp
                    <option value="{{ $yStr }}" {{ $defYear === $yStr ? 'selected' : '' }}>{{ $yStr }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<script>
    if (typeof datePickerComp !== 'function') {
        function datePickerComp(inputName, initialDay, initialMonth, initialYear, shouldAutoSubmit) {
            return {
                day: initialDay || '',
                month: initialMonth || '',
                year: initialYear || '',
                init() {
                    if (initialDay && initialMonth && initialYear) {
                        this.day = initialDay;
                        this.month = initialMonth;
                        this.year = initialYear;
                    }
                },
                updateValue(event) {
                    const hiddenInput = document.getElementById('datepicker_hidden_' + inputName);
                    if (this.year && this.month && this.day) {
                        hiddenInput.value = `${this.year}-${this.month}-${this.day}`;
                        if (shouldAutoSubmit) {
                            const form = hiddenInput.closest('form');
                            if (form) form.submit();
                        }
                    } else {
                        hiddenInput.value = '';
                    }
                }
            }
        }
    }
</script>
