{{-- PWA Install Banner (Emerald Theme & Mobile Android Friendly) --}}
<div id="pwa-install-banner" class="fixed bottom-4 left-4 right-4 md:left-auto md:right-6 md:max-w-md z-[9999] transition-all duration-300 transform translate-y-0 opacity-100" style="display: none;">
    <div class="bg-surface-900/95 backdrop-blur-md text-white p-4 rounded-2xl shadow-2xl border border-emerald-500/30 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-md">
                <i data-lucide="download" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-xs font-extrabold text-white font-heading">Aplikasi PP Nurul Furqon</p>
                <p class="text-[0.7rem] text-surface-300 leading-tight">Pasang aplikasi di Android/iOS untuk akses instan.</p>
            </div>
        </div>
        <div class="flex items-center gap-1.5 shrink-0">
            <button onclick="installPWA()" class="px-3.5 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold transition-all shadow-md active:scale-95">
                Pasang
            </button>
            <button onclick="dismissPWA()" class="p-2 rounded-xl text-surface-400 hover:text-white transition-colors" title="Tutup">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    </div>
</div>

<script>
let deferredPrompt;
window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    const banner = document.getElementById('pwa-install-banner');
    if (banner && !sessionStorage.getItem('pwa-banner-closed')) {
        banner.style.display = 'block';
    }
});

function installPWA() {
    const banner = document.getElementById('pwa-install-banner');
    if (deferredPrompt) {
        deferredPrompt.prompt();
        deferredPrompt.userChoice.then((choiceResult) => {
            if (choiceResult.outcome === 'accepted') {
                console.log('[PWA] User accepted the install prompt');
            }
            deferredPrompt = null;
        });
    }
    if (banner) banner.style.display = 'none';
}

function dismissPWA() {
    const banner = document.getElementById('pwa-install-banner');
    if (banner) banner.style.display = 'none';
    sessionStorage.setItem('pwa-banner-closed', '1');
}
</script>
