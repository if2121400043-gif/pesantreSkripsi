{{-- Keyboard Shortcuts Modal (Triggered by Shift + ? or Alt + Hotkeys) --}}
<div id="keyboard-shortcuts-modal" class="hidden fixed inset-0 z-[110] flex items-center justify-center p-4 bg-surface-900/60 backdrop-blur-sm transition-all animate-fade-in">
    <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl border border-surface-200 overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b border-surface-100 bg-surface-50/50 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center">
                    <i data-lucide="command" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-surface-900 font-heading">Tombol Pintas (Keyboard Shortcuts)</h3>
                    <p class="text-[0.7rem] text-surface-400">Navigasi super cepat tanpa mouse</p>
                </div>
            </div>
            <button onclick="toggleShortcutsModal(false)" class="p-1 text-surface-400 hover:text-surface-600 rounded-lg">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="p-5 space-y-3 text-xs">
            <div class="flex justify-between items-center py-2 border-b border-surface-100">
                <span class="text-surface-700 font-medium">Pencarian Universal Menu</span>
                <kbd class="px-2 py-1 bg-surface-100 border border-surface-300 rounded font-mono font-bold text-surface-700">Ctrl + K</kbd>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-surface-100">
                <span class="text-surface-700 font-medium">Buka Dashboard Utama</span>
                <kbd class="px-2 py-1 bg-surface-100 border border-surface-300 rounded font-mono font-bold text-surface-700">Alt + D</kbd>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-surface-100">
                <span class="text-surface-700 font-medium">Buka Data Peserta Didik / Santri</span>
                <kbd class="px-2 py-1 bg-surface-100 border border-surface-300 rounded font-mono font-bold text-surface-700">Alt + S</kbd>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-surface-100">
                <span class="text-surface-700 font-medium">Buka Tagihan Keuangan</span>
                <kbd class="px-2 py-1 bg-surface-100 border border-surface-300 rounded font-mono font-bold text-surface-700">Alt + K</kbd>
            </div>
            <div class="flex justify-between items-center py-2">
                <span class="text-surface-700 font-medium">Buka Bantuan Shortcuts Ini</span>
                <kbd class="px-2 py-1 bg-surface-100 border border-surface-300 rounded font-mono font-bold text-surface-700">Shift + ?</kbd>
            </div>
        </div>

        <div class="px-5 py-3 bg-surface-50 border-t border-surface-100 text-center text-[0.7rem] text-surface-400">
            Tekan <kbd class="px-1 py-0.5 bg-white border rounded">ESC</kbd> untuk menutup petunjuk ini
        </div>
    </div>
</div>

<script>
function toggleShortcutsModal(show) {
    const modal = document.getElementById('keyboard-shortcuts-modal');
    if (!modal) return;
    if (show) modal.classList.remove('hidden');
    else modal.classList.add('hidden');
}

document.addEventListener('keydown', (e) => {
    // Ignore inside input/textarea
    if (['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) return;

    if (e.key === '?' || (e.shiftKey && e.key === '/')) {
        e.preventDefault();
        toggleShortcutsModal(true);
    } else if (e.altKey && (e.key === 'd' || e.key === 'D')) {
        e.preventDefault();
        window.location.href = "{{ url('/admin/dashboard') }}";
    } else if (e.altKey && (e.key === 's' || e.key === 'S')) {
        e.preventDefault();
        window.location.href = "{{ url('/admin/peserta-didik') }}";
    } else if (e.altKey && (e.key === 'k' || e.key === 'K')) {
        e.preventDefault();
        window.location.href = "{{ url('/admin/tagihan') }}";
    } else if (e.key === 'Escape') {
        toggleShortcutsModal(false);
    }
});
</script>
