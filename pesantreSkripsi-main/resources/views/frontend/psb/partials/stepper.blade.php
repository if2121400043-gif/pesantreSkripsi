@php
    $steps = [
        1 => ['title' => __('Formulir'), 'icon' => 'file-text'],
        2 => ['title' => __('Upload Berkas'), 'icon' => 'upload-cloud'],
        3 => ['title' => __('Selesai'), 'icon' => 'check-circle'],
    ];
@endphp

<div class="mb-10 w-full max-w-3xl mx-auto px-4">
    <div class="flex items-center justify-between relative">
        <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-surface-200 dark:bg-surface-800 rounded-full z-0"></div>
        
        {{-- Progress Line --}}
        @php
            $progressWidth = '0%';
            if($currentStep == 2) $progressWidth = '50%';
            if($currentStep == 3) $progressWidth = '100%';
        @endphp
        <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-gradient-to-r from-primary-500 to-secondary-500 rounded-full z-0 transition-all duration-500" style="width: {{ $progressWidth }}"></div>

        @foreach($steps as $step => $data)
            <div class="relative z-10 flex flex-col items-center gap-2">
                @if($step < $currentStep)
                    <div class="w-10 h-10 rounded-full bg-primary-600 text-white flex items-center justify-center shadow-lg shadow-primary-500/30">
                        <i data-lucide="check" class="w-5 h-5"></i>
                    </div>
                @elseif($step == $currentStep)
                    <div class="w-10 h-10 rounded-full bg-white dark:bg-surface-900 border-4 border-primary-500 text-primary-600 dark:text-primary-400 flex items-center justify-center shadow-lg shadow-primary-500/20">
                        <i data-lucide="{{ $data['icon'] }}" class="w-4 h-4"></i>
                    </div>
                @else
                    <div class="w-10 h-10 rounded-full bg-white dark:bg-surface-900 border-2 border-surface-200 dark:border-surface-700 text-surface-400 dark:text-surface-600 flex items-center justify-center">
                        <i data-lucide="{{ $data['icon'] }}" class="w-4 h-4"></i>
                    </div>
                @endif
                <span class="text-xs font-bold {{ $step <= $currentStep ? 'text-primary-700 dark:text-primary-400' : 'text-surface-400 dark:text-surface-600' }}">{{ $data['title'] }}</span>
            </div>
        @endforeach
    </div>
</div>
