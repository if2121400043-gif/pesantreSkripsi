{{-- Floating Toast Notification Container --}}
@if(session('success') || session('error') || session('info') || session('warning'))
<div id="toast-notification-container" class="fixed top-5 right-5 z-[120] flex flex-col gap-2 max-w-sm w-full pointer-events-auto transition-all duration-300 transform translate-y-0 opacity-100">
    @if(session('success'))
    <div class="toast-item flex items-center justify-between p-4 rounded-xl bg-surface-900 text-white shadow-2xl border border-emerald-500/30 backdrop-blur-md relative overflow-hidden">
        <div class="flex items-center gap-3 pr-2">
            <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                <i data-lucide="check-circle-2" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-white font-heading">Berhasil!</p>
                <p class="text-xs text-surface-200 mt-0.5 leading-snug">{{ session('success') }}</p>
            </div>
        </div>
        <button onclick="closeToast(this.parentElement)" class="text-surface-400 hover:text-white p-1 rounded-lg transition-colors">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
        <div class="absolute bottom-0 left-0 h-1 bg-emerald-500" style="animation: toast-progress 4s linear forwards;"></div>
    </div>
    @endif

    @if(session('error'))
    <div class="toast-item flex items-center justify-between p-4 rounded-xl bg-surface-900 text-white shadow-2xl border border-rose-500/30 backdrop-blur-md relative overflow-hidden">
        <div class="flex items-center gap-3 pr-2">
            <div class="w-8 h-8 rounded-lg bg-rose-500/20 text-rose-400 flex items-center justify-center shrink-0">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-white font-heading">Terjadi Kesalahan</p>
                <p class="text-xs text-surface-200 mt-0.5 leading-snug">{{ session('error') }}</p>
            </div>
        </div>
        <button onclick="closeToast(this.parentElement)" class="text-surface-400 hover:text-white p-1 rounded-lg transition-colors">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
        <div class="absolute bottom-0 left-0 h-1 bg-rose-500" style="animation: toast-progress 4s linear forwards;"></div>
    </div>
    @endif
</div>

<style>
@keyframes toast-progress {
    from { width: 100%; }
    to { width: 0%; }
}
</style>

<script>
function closeToast(el) {
    if (!el) return;
    el.classList.add('opacity-0', 'translate-x-4');
    setTimeout(() => el.remove(), 300);
}
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        const container = document.getElementById('toast-notification-container');
        if (container) {
            container.classList.add('opacity-0', 'translate-x-4');
            setTimeout(() => container.remove(), 300);
        }
    }, 4000);
});
</script>
@endif
