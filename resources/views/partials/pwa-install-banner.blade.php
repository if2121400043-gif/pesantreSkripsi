{{-- PWA Install Banner --}}
<div id="pwa-install-banner" style="display:none; position:fixed; bottom:0; left:0; right:0; background:linear-gradient(135deg,#667eea 0%,#764ba2 100%); color:#fff; padding:12px 20px; z-index:9999; box-shadow:0 -2px 10px rgba(0,0,0,0.2);">
    <div style="display:flex; align-items:center; justify-content:space-between; max-width:600px; margin:0 auto;">
        <div style="display:flex; align-items:center; gap:12px;">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2l0 12m0 0l-4-4m4 4l4-4M4 18h16"/></svg>
            <span style="font-size:14px; font-weight:500;">Pasang aplikasi untuk akses lebih cepat</span>
        </div>
        <div style="display:flex; gap:8px;">
            <button onclick="installPWA()" style="background:#fff; color:#667eea; border:none; padding:6px 16px; border-radius:6px; font-weight:600; cursor:pointer; font-size:13px;">Pasang</button>
            <button onclick="dismissPWA()" style="background:transparent; color:#fff; border:1px solid rgba(255,255,255,0.4); padding:6px 12px; border-radius:6px; cursor:pointer; font-size:13px;">Nanti</button>
        </div>
    </div>
</div>

<script>
let deferredPrompt;
window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    if (!localStorage.getItem('pwa-dismissed')) {
        document.getElementById('pwa-install-banner').style.display = 'block';
    }
});

function installPWA() {
    if (deferredPrompt) {
        deferredPrompt.prompt();
        deferredPrompt.userChoice.then(() => { deferredPrompt = null; });
    }
    document.getElementById('pwa-install-banner').style.display = 'none';
}

function dismissPWA() {
    document.getElementById('pwa-install-banner').style.display = 'none';
    localStorage.setItem('pwa-dismissed', '1');
}
</script>
