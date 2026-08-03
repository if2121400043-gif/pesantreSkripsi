@extends('layouts.app')

@section('title', 'Broadcast WhatsApp')

@section('breadcrumb')
<span class="text-surface-900 font-medium">Broadcast WhatsApp</span>
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-surface-900 font-heading">📢 Broadcast WhatsApp</h1>
        <p class="text-sm text-surface-500 mt-1">Kirim pesan massal ke Wali Santri via WhatsApp (Fonnte API).</p>
    </div>
    <div class="flex items-center gap-2 px-4 py-2 rounded-xl bg-green-50 border border-green-200">
        <svg class="w-5 h-5 text-green-600" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        <span class="text-sm font-semibold text-green-700">{{ $totalWali }} Wali Terjangkau</span>
    </div>
</div>
@endsection

@section('content')

@if(session('success'))
    <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 flex items-center gap-3">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
@endif

@if(session('error') || $errors->any())
    <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-start gap-3">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <div class="font-medium">
            @if(session('error'))
                {{ session('error') }}
            @else
                Pesan gagal dikirim. Periksa kembali form Anda.
            @endif
        </div>
    </div>
@endif

<form action="{{ route('admin.broadcast-wa.send') }}" method="POST" id="form-broadcast"
      onsubmit="return startBroadcast()">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Panel Kiri: Form --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Target Penerima --}}
            <x-card title="🎯 Target Penerima">
                <div class="space-y-3">
                    <label class="flex items-center gap-3 p-3 rounded-lg border-2 border-surface-200 hover:border-primary-300 cursor-pointer transition-colors has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50">
                        <input type="radio" name="target" value="semua" checked
                               class="text-primary-600 focus:ring-primary-500" onchange="updateTarget()">
                        <div>
                            <p class="font-semibold text-surface-900">Semua Wali Santri Aktif</p>
                            <p class="text-xs text-surface-500">Kirim ke seluruh wali yang anaknya berstatus aktif ({{ $totalWali }} wali)</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 rounded-lg border-2 border-surface-200 hover:border-primary-300 cursor-pointer transition-colors has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50">
                        <input type="radio" name="target" value="lembaga"
                               class="text-primary-600 focus:ring-primary-500" onchange="updateTarget()">
                        <div class="flex-1">
                            <p class="font-semibold text-surface-900">Per Lembaga</p>
                            <select name="lembaga_id" id="select-lembaga" disabled
                                    class="mt-1 w-full px-3 py-2 rounded-lg border border-surface-300 bg-white text-sm disabled:opacity-50 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                                <option value="">Pilih Lembaga...</option>
                                @foreach($lembagas as $lembaga)
                                    <option value="{{ $lembaga->id }}">{{ $lembaga->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 rounded-lg border-2 border-surface-200 hover:border-primary-300 cursor-pointer transition-colors has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50">
                        <input type="radio" name="target" value="rombel"
                               class="text-primary-600 focus:ring-primary-500" onchange="updateTarget()">
                        <div class="flex-1">
                            <p class="font-semibold text-surface-900">Per Rombongan Belajar</p>
                            <select name="rombel_id" id="select-rombel" disabled
                                    class="mt-1 w-full px-3 py-2 rounded-lg border border-surface-300 bg-white text-sm disabled:opacity-50 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                                <option value="">Pilih Rombel...</option>
                                @foreach($rombels as $rombel)
                                    <option value="{{ $rombel->id }}">{{ $rombel->nama }} ({{ $rombel->lembaga->nama ?? '-' }})</option>
                                @endforeach
                            </select>
                        </div>
                    </label>
                </div>
            </x-card>

            {{-- Isi Pesan --}}
            <x-card title="✏️ Isi Pesan">
                {{-- Template Cepat --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-surface-700 mb-1">Template Cepat</label>
                    <select id="template-select" onchange="applyTemplate()"
                            class="w-full px-3 py-2 rounded-lg border border-surface-300 bg-white text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        <option value="">— Tulis pesan manual —</option>
                        <option value="pengumuman_umum">📋 Pengumuman Umum</option>
                        <option value="pengingat_spp">💰 Pengingat SPP</option>
                        <option value="rapat_wali">🤝 Undangan Rapat Wali</option>
                        <option value="libur">📅 Pemberitahuan Libur</option>
                        <option value="info_kegiatan">🎯 Info Kegiatan Pesantren</option>
                    </select>
                </div>

                {{-- Textarea Pesan --}}
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1">
                        Pesan <span class="text-danger-500">*</span>
                    </label>
                    <textarea name="pesan" id="pesan-textarea" rows="10" required
                              class="w-full px-4 py-3 rounded-lg border border-surface-300 bg-white text-sm font-mono leading-relaxed focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 resize-y"
                              placeholder="Tulis pesan Anda di sini..."></textarea>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <span class="text-xs text-surface-500 mr-1 self-center">Variabel:</span>
                        <button type="button" onclick="insertVar('{nama_santri}')" class="px-2 py-1 rounded text-xs font-mono bg-primary-100 text-primary-700 hover:bg-primary-200 transition-colors">{nama_santri}</button>
                        <button type="button" onclick="insertVar('{nama_wali}')" class="px-2 py-1 rounded text-xs font-mono bg-primary-100 text-primary-700 hover:bg-primary-200 transition-colors">{nama_wali}</button>
                        <button type="button" onclick="insertVar('{link_portal}')" class="px-2 py-1 rounded text-xs font-mono bg-primary-100 text-primary-700 hover:bg-primary-200 transition-colors">{link_portal}</button>
                    </div>
                </div>
            </x-card>
        </div>

        {{-- Panel Kanan: Preview & Kirim (sticky agar tombol selalu terlihat) --}}
        <div class="lg:col-span-1">
        <div class="lg:sticky lg:top-20 space-y-4">

            {{-- Preview Pesan --}}
            <x-card title="📱 Preview Pesan" class="border-t-4 border-t-green-500">
                <div class="bg-[#e5ddd5] rounded-lg p-3 max-h-[250px] overflow-y-auto" id="preview-area">
                    <div class="bg-white rounded-lg p-3 shadow-sm max-w-[95%] text-sm leading-relaxed whitespace-pre-wrap" id="preview-bubble">
                        <span class="text-surface-400 italic">Tulis pesan untuk melihat preview...</span>
                    </div>
                    <div class="text-right mt-1">
                        <span class="text-[0.6rem] text-surface-500">{{ now()->format('H:i') }} ✓✓</span>
                    </div>
                </div>
            </x-card>

            {{-- Estimasi --}}
            <div style="background-color: #fffbeb; border: 1px solid #fde68a; padding: 10px; border-radius: 8px; margin-bottom: 12px;">
                <p style="font-size: 12px; color: #92400e;">
                    ⏱️ {{ $totalWali }} pesan × 2 detik = <strong>±{{ max(1, ceil($totalWali * 2 / 60)) }} menit</strong>
                </p>
            </div>

            {{-- Tombol Kirim --}}
            <button type="submit" id="btn-send"
                    style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 14px; border-radius: 12px; font-size: 16px; font-weight: 700; color: white; background: linear-gradient(to right, #22c55e, #16a34a); border: none; cursor: pointer; box-shadow: 0 4px 14px rgba(34,197,94,0.35); transition: opacity 0.2s;"
                    onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                <span id="btn-send-text">Kirim Broadcast</span>
            </button>

            <p style="font-size: 10px; color: #9ca3af; text-align: center; margin-top: 8px;">Jeda 2 detik/pesan • Duplikat dilewati • Jangan tutup halaman</p>
        </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    // ═══════════════════════════════════════════
    // Template Messages
    // ═══════════════════════════════════════════
    const templates = {};
    templates.pengumuman_umum = "Assalamu'alaikum Wr. Wb.\n\nYth. {nama_wali},\nWali dari ananda *{nama_santri}*.\n\nKami dari Pesantren Nurul Furqon ingin menyampaikan informasi penting berikut:\n\n[Tulis pengumuman di sini]\n\nUntuk informasi lebih lanjut, silakan akses Portal Wali di:\n{link_portal}\n\nJazakumullahu Khairan.\nWassalamu'alaikum Wr. Wb.";
    templates.pengingat_spp = "Assalamu'alaikum Wr. Wb.\n\n🔔 *PENGINGAT PEMBAYARAN*\n\nYth. {nama_wali},\nWali dari ananda *{nama_santri}*.\n\nKami mengingatkan bahwa ananda masih memiliki tagihan yang belum lunas. Mohon segera melakukan pembayaran di kantor Tata Usaha pesantren.\n\nCek detail tagihan di Portal Wali:\n{link_portal}\n\nJazakumullahu Khairan.\nWassalamu'alaikum Wr. Wb.";
    templates.rapat_wali = "Assalamu'alaikum Wr. Wb.\n\n🤝 *UNDANGAN RAPAT WALI SANTRI*\n\nYth. {nama_wali},\nWali dari ananda *{nama_santri}*.\n\nDengan hormat, kami mengundang Bapak/Ibu untuk hadir pada:\n\n📅 Hari/Tanggal: [Hari, Tanggal]\n🕐 Waktu: [Jam] WITA\n📍 Tempat: Aula Pesantren Nurul Furqon\n📋 Agenda: [Agenda rapat]\n\nKehadiran Bapak/Ibu sangat kami harapkan.\n\nWassalamu'alaikum Wr. Wb.";
    templates.libur = "Assalamu'alaikum Wr. Wb.\n\n📅 *PEMBERITAHUAN LIBUR*\n\nYth. {nama_wali},\nWali dari ananda *{nama_santri}*.\n\nKami informasikan bahwa pesantren akan libur pada:\n\n📅 Tanggal: [Tanggal mulai] s/d [Tanggal selesai]\n📋 Keterangan: [Alasan libur]\n\nSantri dapat dijemput mulai tanggal [tanggal] dan kembali masuk pada tanggal [tanggal].\n\nWassalamu'alaikum Wr. Wb.";
    templates.info_kegiatan = "Assalamu'alaikum Wr. Wb.\n\n🎯 *INFO KEGIATAN PESANTREN*\n\nYth. {nama_wali},\nWali dari ananda *{nama_santri}*.\n\nKami informasikan bahwa pesantren akan mengadakan kegiatan:\n\n📋 Kegiatan: [Nama kegiatan]\n📅 Tanggal: [Tanggal]\n📍 Tempat: [Tempat]\n📝 Catatan: [Catatan tambahan]\n\nPantau perkembangan ananda di Portal Wali:\n{link_portal}\n\nWassalamu'alaikum Wr. Wb.";

    // ═══════════════════════════════════════════
    // Functions
    // ═══════════════════════════════════════════
    function applyTemplate() {
        const key = document.getElementById('template-select').value;
        const textarea = document.getElementById('pesan-textarea');
        if (key && templates[key]) {
            textarea.value = templates[key];
        }
        updatePreview();
    }

    function insertVar(variable) {
        const textarea = document.getElementById('pesan-textarea');
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        textarea.value = text.substring(0, start) + variable + text.substring(end);
        textarea.selectionStart = textarea.selectionEnd = start + variable.length;
        textarea.focus();
        updatePreview();
    }

    function updateTarget() {
        const target = document.querySelector('input[name="target"]:checked').value;
        document.getElementById('select-lembaga').disabled = target !== 'lembaga';
        document.getElementById('select-rombel').disabled = target !== 'rombel';
    }

    function updatePreview() {
        const text = document.getElementById('pesan-textarea').value;
        const bubble = document.getElementById('preview-bubble');

        if (!text.trim()) {
            bubble.innerHTML = '<span class="text-surface-400 italic">Tulis pesan untuk melihat preview...</span>';
            return;
        }

        // Replace variables with example values
        let preview = text
            .replace(/\{nama_santri\}/g, '<span style="background:#dcfce7;padding:1px 4px;border-radius:4px;font-weight:600;">[Nama Santri]</span>')
            .replace(/\{nama_wali\}/g, '<span style="background:#dbeafe;padding:1px 4px;border-radius:4px;font-weight:600;">[Nama Wali]</span>')
            .replace(/\{link_portal\}/g, '<span style="color:#2563eb;text-decoration:underline;">{{ url("/portal/beranda") }}</span>')
            .replace(/\*(.*?)\*/g, '<strong>$1</strong>')
            .replace(/_(.*?)_/g, '<em>$1</em>');

        bubble.innerHTML = preview;
    }

    function startBroadcast() {
        const pesan = document.getElementById('pesan-textarea').value.trim();
        if (!pesan) {
            alert('Pesan tidak boleh kosong!');
            return false;
        }

        const target = document.querySelector('input[name="target"]:checked').value;
        let targetLabel = 'semua wali santri aktif';
        if (target === 'lembaga') {
            const sel = document.getElementById('select-lembaga');
            if (!sel.value) { alert('Pilih lembaga terlebih dahulu!'); return false; }
            targetLabel = 'wali santri di ' + sel.options[sel.selectedIndex].text;
        } else if (target === 'rombel') {
            const sel = document.getElementById('select-rombel');
            if (!sel.value) { alert('Pilih rombel terlebih dahulu!'); return false; }
            targetLabel = 'wali santri di ' + sel.options[sel.selectedIndex].text;
        }

        if (!confirm(`📢 Anda akan mengirim broadcast WhatsApp ke ${targetLabel}.\n\nProses ini tidak dapat dibatalkan.\nLanjutkan?`)) {
            return false;
        }

        // Ubah tombol ke loading state
        const btn = document.getElementById('btn-send');
        const btnText = document.getElementById('btn-send-text');
        // Jangan gunakan btn.disabled = true; karena kadang mengganggu submit di beberapa browser
        btn.style.pointerEvents = 'none';
        btn.style.background = 'linear-gradient(to right, #f59e0b, #d97706)';
        btn.style.cursor = 'wait';
        btnText.innerHTML = '⏳ Sedang Mengirim... Jangan Tutup Halaman!';

        // Buat form terlihat non-aktif tapi TETAP submit nilainya (JANGAN pakai .disabled = true)
        document.getElementById('form-broadcast').style.pointerEvents = 'none';
        document.getElementById('form-broadcast').style.opacity = '0.7';

        return true;
    }

    // Live preview
    document.getElementById('pesan-textarea').addEventListener('input', updatePreview);
</script>
@endpush
@endsection
