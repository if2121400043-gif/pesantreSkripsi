@extends('frontend.layouts.app')

@section('title', __('Cek Status Pendaftaran - PSB'))

@section('content')
<section class="py-12 bg-surface-50 dark:bg-surface-950 min-h-screen">
    <div class="max-w-xl mx-auto px-4">
        <div class="bg-white dark:bg-surface-900 rounded-3xl shadow-xl border border-surface-100 dark:border-surface-800 p-8">
            <h1 class="text-2xl font-extrabold text-surface-900 dark:text-white mb-2">{{ __('Cek Status Pendaftaran') }}</h1>
            <p class="text-sm text-surface-500 dark:text-surface-400 mb-6">{{ __('Masukkan nomor pendaftaran dan NIK calon santri.') }}</p>

            @if($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-danger-50 text-danger-700 text-sm">{{ $errors->first() }}</div>
            @endif

            <form method="GET" action="{{ route('frontend.psb.status') }}" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">{{ __('Nomor Pendaftaran') }}</label>
                    <input name="no_pendaftaran" value="{{ request('no_pendaftaran') }}" required maxlength="30"
                           class="w-full rounded-xl border-surface-300 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">{{ __('NIK') }}</label>
                    <input name="nik" value="{{ request('nik') }}" required inputmode="numeric" maxlength="16" pattern="[0-9]{16}"
                           class="w-full rounded-xl border-surface-300 dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                </div>
                <button class="w-full px-5 py-3 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-bold">{{ __('Periksa Status') }}</button>
            </form>

            @if(request()->filled('no_pendaftaran') && request()->filled('nik'))
                <div class="mt-8 p-5 rounded-2xl {{ $result ? 'bg-info-50 text-info-900' : 'bg-warning-50 text-warning-900' }}">
                    @if($result)
                        <p class="font-bold mb-3">{{ $result->nama_lengkap }}</p>
                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between gap-4"><dt>{{ __('Nomor Pendaftaran') }}</dt><dd class="font-semibold">{{ $result->no_pendaftaran }}</dd></div>
                            <div class="flex justify-between gap-4"><dt>{{ __('Status') }}</dt><dd class="font-semibold">{{ $result->workflow_status_label }}</dd></div>
                            <div class="flex justify-between gap-4"><dt>{{ __('Diperbarui') }}</dt><dd>{{ optional($result->updated_at)->format('d M Y H:i') }}</dd></div>
                        </dl>
                    @else
                        <p class="font-semibold">{{ __('Data pendaftaran tidak ditemukan. Periksa kembali nomor pendaftaran dan NIK Anda.') }}</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
