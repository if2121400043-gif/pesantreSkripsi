{{-- Modal Provinsi --}}
<div id="modal-provinsi" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-surface-900/50 backdrop-blur-sm" onclick="closeAllModals()"></div>
    <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
            <div class="px-6 py-4 border-b border-surface-100 bg-surface-50 flex justify-between items-center">
                <h3 class="font-bold text-surface-900" id="modal-provinsi-title">Tambah Provinsi</h3>
                <button onclick="closeAllModals()" class="text-surface-400 hover:text-surface-600"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form id="form-provinsi" method="POST" action="{{ route('admin.wilayah.provinsi.store') }}">
                @csrf
                <input type="hidden" name="_method" id="prov-method" value="POST">
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Kode Provinsi <span class="text-danger-500">*</span></label>
                        <input type="text" name="kode" id="prov-kode" required maxlength="10" placeholder="Contoh: 35" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Nama Provinsi <span class="text-danger-500">*</span></label>
                        <input type="text" name="nama" id="prov-nama" required maxlength="100" placeholder="Contoh: JAWA TIMUR" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    </div>
                </div>
                <div class="px-6 py-4 bg-surface-50 border-t border-surface-100 flex justify-end gap-3">
                    <button type="button" onclick="closeAllModals()" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Kabupaten --}}
<div id="modal-kabupaten" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-surface-900/50 backdrop-blur-sm" onclick="closeAllModals()"></div>
    <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
            <div class="px-6 py-4 border-b border-surface-100 bg-surface-50 flex justify-between items-center">
                <h3 class="font-bold text-surface-900" id="modal-kab-title">Tambah Kabupaten/Kota</h3>
                <button onclick="closeAllModals()" class="text-surface-400 hover:text-surface-600"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form id="form-kabupaten" method="POST" action="{{ route('admin.wilayah.kabupaten.store') }}">
                @csrf
                <input type="hidden" name="_method" id="kab-method" value="POST">
                <input type="hidden" name="provinsi_id" value="{{ $selectedProvinsi->id ?? '' }}">
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Kode <span class="text-danger-500">*</span></label>
                        <input type="text" name="kode" id="kab-kode" required maxlength="10" placeholder="Contoh: 3517" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Nama Kabupaten/Kota <span class="text-danger-500">*</span></label>
                        <input type="text" name="nama" id="kab-nama" required maxlength="100" placeholder="Contoh: KAB. JOMBANG" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    </div>
                </div>
                <div class="px-6 py-4 bg-surface-50 border-t border-surface-100 flex justify-end gap-3">
                    <button type="button" onclick="closeAllModals()" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Kecamatan --}}
<div id="modal-kecamatan" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-surface-900/50 backdrop-blur-sm" onclick="closeAllModals()"></div>
    <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
            <div class="px-6 py-4 border-b border-surface-100 bg-surface-50 flex justify-between items-center">
                <h3 class="font-bold text-surface-900" id="modal-kec-title">Tambah Kecamatan</h3>
                <button onclick="closeAllModals()" class="text-surface-400 hover:text-surface-600"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form id="form-kecamatan" method="POST" action="{{ route('admin.wilayah.kecamatan.store') }}">
                @csrf
                <input type="hidden" name="_method" id="kec-method" value="POST">
                <input type="hidden" name="kabupaten_id" value="{{ $selectedKabupaten->id ?? '' }}">
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Kode <span class="text-danger-500">*</span></label>
                        <input type="text" name="kode" id="kec-kode" required maxlength="10" placeholder="Contoh: 351709" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Nama Kecamatan <span class="text-danger-500">*</span></label>
                        <input type="text" name="nama" id="kec-nama" required maxlength="100" placeholder="Contoh: PETERONGAN" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    </div>
                </div>
                <div class="px-6 py-4 bg-surface-50 border-t border-surface-100 flex justify-end gap-3">
                    <button type="button" onclick="closeAllModals()" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Desa --}}
<div id="modal-desa" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-surface-900/50 backdrop-blur-sm" onclick="closeAllModals()"></div>
    <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
            <div class="px-6 py-4 border-b border-surface-100 bg-surface-50 flex justify-between items-center">
                <h3 class="font-bold text-surface-900" id="modal-desa-title">Tambah Desa/Kelurahan</h3>
                <button onclick="closeAllModals()" class="text-surface-400 hover:text-surface-600"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form id="form-desa" method="POST" action="{{ route('admin.wilayah.desa.store') }}">
                @csrf
                <input type="hidden" name="_method" id="desa-method" value="POST">
                <input type="hidden" name="kecamatan_id" value="{{ $selectedKecamatan->id ?? '' }}">
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Kode <span class="text-danger-500">*</span></label>
                        <input type="text" name="kode" id="desa-kode" required maxlength="15" placeholder="Contoh: 3517092001" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1">Nama Desa/Kelurahan <span class="text-danger-500">*</span></label>
                        <input type="text" name="nama" id="desa-nama" required maxlength="100" placeholder="Contoh: KEPUHKEMBENG" class="w-full rounded-lg border border-surface-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    </div>
                </div>
                <div class="px-6 py-4 bg-surface-50 border-t border-surface-100 flex justify-end gap-3">
                    <button type="button" onclick="closeAllModals()" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function closeAllModals() {
    document.querySelectorAll('[id^="modal-"]').forEach(m => m.classList.add('hidden'));
}

// Provinsi
function openModalProvinsi() {
    document.getElementById('form-provinsi').reset();
    document.getElementById('form-provinsi').action = "{{ route('admin.wilayah.provinsi.store') }}";
    document.getElementById('prov-method').value = 'POST';
    document.getElementById('modal-provinsi-title').innerText = 'Tambah Provinsi';
    document.getElementById('modal-provinsi').classList.remove('hidden');
}
function editProvinsi(data) {
    document.getElementById('form-provinsi').action = `/admin/wilayah/provinsi/${data.id}`;
    document.getElementById('prov-method').value = 'PUT';
    document.getElementById('prov-kode').value = data.kode;
    document.getElementById('prov-nama').value = data.nama;
    document.getElementById('modal-provinsi-title').innerText = 'Edit Provinsi';
    document.getElementById('modal-provinsi').classList.remove('hidden');
}

// Kabupaten
function openModalKabupaten() {
    document.getElementById('form-kabupaten').reset();
    document.getElementById('form-kabupaten').action = "{{ route('admin.wilayah.kabupaten.store') }}";
    document.getElementById('kab-method').value = 'POST';
    document.getElementById('modal-kab-title').innerText = 'Tambah Kabupaten/Kota';
    document.getElementById('modal-kabupaten').classList.remove('hidden');
}
function editKabupaten(data) {
    document.getElementById('form-kabupaten').action = `/admin/wilayah/kabupaten/${data.id}`;
    document.getElementById('kab-method').value = 'PUT';
    document.getElementById('kab-kode').value = data.kode;
    document.getElementById('kab-nama').value = data.nama;
    document.getElementById('modal-kab-title').innerText = 'Edit Kabupaten/Kota';
    document.getElementById('modal-kabupaten').classList.remove('hidden');
}

// Kecamatan
function openModalKecamatan() {
    document.getElementById('form-kecamatan').reset();
    document.getElementById('form-kecamatan').action = "{{ route('admin.wilayah.kecamatan.store') }}";
    document.getElementById('kec-method').value = 'POST';
    document.getElementById('modal-kec-title').innerText = 'Tambah Kecamatan';
    document.getElementById('modal-kecamatan').classList.remove('hidden');
}
function editKecamatan(data) {
    document.getElementById('form-kecamatan').action = `/admin/wilayah/kecamatan/${data.id}`;
    document.getElementById('kec-method').value = 'PUT';
    document.getElementById('kec-kode').value = data.kode;
    document.getElementById('kec-nama').value = data.nama;
    document.getElementById('modal-kec-title').innerText = 'Edit Kecamatan';
    document.getElementById('modal-kecamatan').classList.remove('hidden');
}

// Desa
function openModalDesa() {
    document.getElementById('form-desa').reset();
    document.getElementById('form-desa').action = "{{ route('admin.wilayah.desa.store') }}";
    document.getElementById('desa-method').value = 'POST';
    document.getElementById('modal-desa-title').innerText = 'Tambah Desa/Kelurahan';
    document.getElementById('modal-desa').classList.remove('hidden');
}
function editDesa(data) {
    document.getElementById('form-desa').action = `/admin/wilayah/desa/${data.id}`;
    document.getElementById('desa-method').value = 'PUT';
    document.getElementById('desa-kode').value = data.kode;
    document.getElementById('desa-nama').value = data.nama;
    document.getElementById('modal-desa-title').innerText = 'Edit Desa/Kelurahan';
    document.getElementById('modal-desa').classList.remove('hidden');
}
</script>
@endpush
