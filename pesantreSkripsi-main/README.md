# Sistem Informasi Manajemen Pondok Pesantren Nurul Furqon

<p align="center">
  <img src="public/logo.png" alt="Logo PP Nurul Furqon" width="120">
</p>

<p align="center">
  <strong>Aplikasi berbasis web untuk manajemen operasional Pondok Pesantren secara terpadu.</strong><br>
  Dibangun sebagai bagian dari Tugas Akhir (Skripsi).
</p>

---

## 📋 Deskripsi Proyek

Sistem Informasi Manajemen Pondok Pesantren Nurul Furqon adalah aplikasi web *full-stack* yang dirancang untuk mendigitalisasi dan mengotomatisasi seluruh proses administrasi di lingkungan Pondok Pesantren, mulai dari pendataan santri, akademik, keuangan, hingga kepesantrenan. Aplikasi ini memiliki beberapa portal pengguna yang disesuaikan dengan peran masing-masing *stakeholder*.

## ✨ Fitur Utama

### 🌐 Website Publik (Frontend)
- **Landing Page** — Halaman beranda informatif dengan statistik santri, tenaga pendidik, dan rombongan belajar.
- **Profil Pesantren** — Menampilkan visi, misi, dan informasi umum pesantren.
- **Berita & Kegiatan** — Portal berita dengan kategori, *view count*, dan sistem *slug*.
- **Penerimaan Santri Baru (PSB)** — Formulir pendaftaran online dengan sistem gelombang, *honeypot anti-spam*, dan *math captcha*.
- **Upload Berkas** — Pengunggahan dokumen persyaratan PSB secara digital.

### 🛡️ Panel Admin (Super Admin)
- **Dashboard Dinamis** — Grafik statistik santri per lembaga (Chart.js), distribusi asrama, rekapitulasi tagihan bulan berjalan, dan *feed* aktivitas terbaru.
- **Manajemen Data Induk** — CRUD lengkap untuk: Orang, Peserta Didik, Pegawai, Lembaga, Asrama, Kamar, Wilayah (Provinsi/Kabupaten/Kecamatan/Desa).
- **Manajemen Akademik** — Tahun Pelajaran, Rombongan Belajar (Rombel), Mata Pelajaran, Jadwal Pelajaran, Penempatan Santri.
- **Manajemen Keuangan** — Komponen Biaya, Tagihan, Pembayaran, dan Rekapitulasi.
- **Manajemen PSB** — Gelombang Pendaftaran, Verifikasi Calon Santri, dan Dokumen PSB.
- **Manajemen Kedisiplinan** — Jenis Pelanggaran, Catatan Pelanggaran, Catatan Prestasi.
- **Presensi & Penilaian** — Presensi Kelas dan Penilaian (Rapor).
- **Perizinan Keluar** — Manajemen izin keluar santri.
- **Manajemen Pengguna** — User, Role, dan Permission (RBAC).
- **Konfigurasi** — Pengaturan profil pesantren.
- **Berita** — Manajemen konten berita dan artikel.

### 👨‍🏫 Portal Guru
- **Dashboard Guru** — Jadwal mengajar hari ini, total kelas diampu, dan amanah wali kelas.
- **Presensi** — Input presensi per jadwal pelajaran.
- **Penilaian** — Input nilai siswa per jadwal pelajaran.
- **Kedisiplinan** — Pencatatan pelanggaran dan prestasi santri dengan *search* santri.

### 👨‍👩‍👧‍👦 Portal Wali Santri
- **Beranda** — Ringkasan tagihan belum lunas dan profil data anak/santri.
- **Tagihan** — Rincian tagihan dan status pembayaran per anak.
- **Presensi** — Riwayat kehadiran anak di kelas.
- **Kedisiplinan** — Catatan pelanggaran dan prestasi anak.

## 🛠️ Teknologi yang Digunakan

| Komponen        | Teknologi                                        |
|-----------------|--------------------------------------------------|
| **Framework**   | Laravel 13.x (PHP 8.x)                          |
| **Database**    | SQLite (development) / MySQL (production-ready)  |
| **Frontend**    | Blade Templates, Vanilla CSS, Chart.js, Lucide Icons |
| **Auth**        | Laravel built-in Auth + Custom RBAC (Role-Based Access Control) |
| **Storage**     | Laravel Filesystem (local/public)                |

## 🗄️ Arsitektur Database

Sistem ini memiliki **13 file migrasi** yang terstruktur dan diurutkan berdasarkan dependensi:

| No | Migrasi | Deskripsi |
|----|---------|-----------|
| 1  | `create_wilayah_tables` | Provinsi, Kabupaten, Kecamatan, Desa |
| 2  | `create_pesantren_tables` | Data Pesantren dan Lembaga |
| 3  | `create_orang_table` | Data induk orang (santri, pegawai, wali) |
| 4  | `create_peserta_pegawai_tables` | Peserta Didik & Pegawai |
| 5  | `create_asrama_tables` | Asrama, Kamar, Penempatan Mukim |
| 6  | `create_keluarga_kesehatan_tables` | Hubungan Keluarga & Data Kesehatan |
| 7  | `create_akademik_tables` | Rombel, Mata Pelajaran, Jadwal |
| 8  | `create_auth_rbac_tables` | User, Role, Permission, UserRole |
| 9  | `create_psb_tables` | Gelombang PSB, Calon Santri, Dokumen |
| 10 | `create_keuangan_tables` | Komponen Biaya, Tagihan, Pembayaran |
| 11 | `create_penilaian_tables` | Nilai Rapor |
| 12 | `create_kedisiplinan_tables` | Jenis Pelanggaran, Catatan Pelanggaran, Prestasi, Perizinan |
| 13 | `create_beritas_table` | Konten Berita/Artikel |

Total terdapat **37 model Eloquent** yang saling berelasi.

## 📁 Struktur Proyek

```
app/
├── Http/Controllers/
│   ├── Admin/          # 28 controller untuk panel admin
│   ├── Auth/           # Autentikasi (Login, Register, dll.)
│   ├── Frontend/       # Website publik & PSB
│   ├── Guru/           # Portal guru (Dashboard, Presensi, Penilaian, Kedisiplinan)
│   └── Portal/         # Portal wali santri
├── Models/             # 37 model Eloquent
resources/views/
├── admin/              # Halaman panel admin
├── frontend/           # Website publik (home, profil, berita, PSB)
├── guru/               # Portal guru
├── portal/             # Portal wali santri
├── layouts/            # Layout utama (app, sidebar)
└── components/         # Blade components (card, modal, dll.)
database/
├── migrations/         # 13 file migrasi terstruktur
└── seeders/            # CSV sumber data wilayah
```
└── seeders/            # Folder ini hanya menyimpan data sumber CSV wilayah
## 🚀 Cara Instalasi & Menjalankan (Development)

### Prasyarat
- PHP >= 8.3
- Composer
- Node.js >= 18 & NPM
- MySQL / MariaDB

### Langkah Instalasi (Lokal)

```bash
# 1. Clone repository
git clone https://github.com/achzulfizibyan/web-nurul-furqon.git
cd web-nurul-furqon

# 2. Install dependensi PHP
composer install

# 3. Install dependensi frontend
npm install

# 4. Salin file environment
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Buat database MySQL, lalu sesuaikan .env:
#    DB_DATABASE=db_nurulfurqon
#    DB_USERNAME=root
#    DB_PASSWORD=

# 7. Jalankan migrasi
php artisan migrate

# 8. Buat symbolic link untuk storage
php artisan storage:link

# 9. Jalankan server development
composer dev
# Atau manual:
# php artisan serve  (terminal 1)
# npm run dev        (terminal 2)
```

Aplikasi akan berjalan di `http://127.0.0.1:8000`.

---

## 🌐 Panduan Deploy ke Server (Production/Hosting)

### Kebutuhan Server

| Komponen | Minimum |
|---|---|
| **PHP** | >= 8.3 dengan ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `gd` |
| **Database** | MySQL 8.0+ / MariaDB 10.6+ |
| **Web Server** | Apache (dengan `mod_rewrite`) atau Nginx |
| **Node.js** | >= 18 (untuk build asset saja) |
| **Composer** | >= 2.x |
| **Disk** | Minimal 500MB |

### Langkah Deploy

```bash
# 1. Upload/clone project ke server
git clone https://github.com/achzulfizibyan/web-nurul-furqon.git /var/www/pesantren
cd /var/www/pesantren

# 2. Copy template environment production & edit
cp .env.production .env
nano .env
# → Isi APP_URL dengan domain/IP server
# → Isi DB_DATABASE, DB_USERNAME, DB_PASSWORD
# → Isi FONNTE_TOKEN jika butuh notifikasi WhatsApp

# 3. Jalankan script deploy otomatis
chmod +x deploy.sh
./deploy.sh
```

Script `deploy.sh` akan otomatis menjalankan:
- ✅ `composer install --optimize-autoloader --no-dev`
- ✅ `npm install && npm run build`
- ✅ `php artisan key:generate`
- ✅ `php artisan migrate --force`
- ✅ `php artisan storage:link`
- ✅ `php artisan config:cache && route:cache && view:cache`
- ✅ Set permission folder `storage/` & `bootstrap/cache/`

### Atau Deploy Manual (Tanpa Script)

```bash
# Install dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# Setup Laravel
php artisan key:generate
php artisan migrate --force
php artisan storage:link

# Optimasi
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Permission (Linux)
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Konfigurasi Web Server

#### Apache (Shared Hosting / cPanel)
- Set **Document Root** ke folder `public/`
- Pastikan `mod_rewrite` aktif (file `.htaccess` sudah disertakan)

#### Nginx
```nginx
server {
    listen 80;
    server_name namadomain.com;
    root /var/www/pesantren/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Setup Queue Worker (Opsional)
Jika menggunakan fitur notifikasi WhatsApp:
```bash
# Jalankan via supervisor atau systemd
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

### Setup Cron Job (Opsional)
```bash
* * * * * cd /var/www/pesantren && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🔑 Akun Default

| Role          | Username / Email        | Password   |
|---------------|-------------------------|------------|
| Super Admin   | `admin@pesantren.id`    | `password` |
| Guru          | `guru1`                 | `password` |
| Wali Santri   | `wali1`                 | `password` |

> ⚠️ **Penting:** Segera ganti password akun default setelah deploy ke production!

## 📐 Alur Pengembangan (Track Record)

Berikut adalah kronologi dan tahapan pembangunan sistem ini:

### Fase 1: Perancangan Arsitektur & Database
- Merancang Entity Relationship Diagram (ERD) dengan 37 entitas utama.
- Menyusun 13 file migrasi berurutan berdasarkan dependensi antar tabel.
- Menerapkan *soft deletes*, *enum constraints*, dan *foreign key* yang ketat pada skema database.
- Membangun 37 model Eloquent beserta relasi (`BelongsTo`, `HasOne`, `HasMany`).

### Fase 2: Sistem Autentikasi & Otorisasi (RBAC)
- Mengimplementasikan sistem login berbasis username/email.
- Membangun Custom Role-Based Access Control (RBAC) dengan 4 role: `SUPER_ADMIN`, `GURU`, `OPERATOR_LEMBAGA`, dan `SANTRI`.
- Menerapkan middleware proteksi route per role untuk setiap portal.

### Fase 3: Panel Admin (Backend CRUD)
- Membangun 28 controller admin untuk mengelola seluruh entitas data.
- Mengimplementasikan CRUD lengkap dengan validasi, relasi Eloquent, dan pagination.
- Modul yang dibangun: Data Induk, Akademik, Keuangan, PSB, Kedisiplinan, Presensi, Penilaian, Perizinan, Berita, dan Manajemen Pengguna.

### Fase 4: Website Publik (Frontend)
- Membangun landing page responsif dengan desain modern (glassmorphism, gradient, animasi).
- Mengintegrasikan statistik dinamis (total santri, tenaga pendidik, rombel).
- Membangun sistem PSB online: landing → formulir → upload berkas → konfirmasi.
- Menambahkan proteksi anti-spam (*honeypot* + *math captcha*) pada formulir PSB.
- Membangun halaman berita dengan sistem *slug*, *view count*, dan pagination.

### Fase 5: Dashboard Admin Dinamis
- Mengganti seluruh data *dummy/hardcoded* pada dashboard admin dengan query database real-time.
- Mengimplementasikan grafik Chart.js untuk visualisasi **Santri per Lembaga** (bar chart putra/putri) dan **Distribusi Asrama** (doughnut chart).
- Membangun modul **Rekapitulasi Tagihan** bulan berjalan dengan progress bar persentase.
- Mengimplementasikan *feed* **Aktivitas Terbaru** yang menggabungkan log dari berbagai tabel (santri baru, pembayaran, pelanggaran, perizinan).

### Fase 6: Portal Guru
- Membangun dashboard guru dengan jadwal mengajar hari ini, total kelas diampu, dan info wali kelas.
- Mengimplementasikan fitur *quick-action*: isi presensi dan input nilai langsung dari dashboard.
- Membangun modul pencatatan kedisiplinan (pelanggaran & prestasi) dengan *live search* santri.
- Memperbaiki bug *case-sensitive* pada query nama hari untuk sinkronisasi dengan database constraint.

### Fase 7: Portal Wali Santri
- Membangun beranda portal wali dengan ringkasan tagihan dan profil data anak.
- Mengimplementasikan halaman rincian tagihan, riwayat presensi, dan catatan kedisiplinan.
- Menghubungkan akun wali dengan data santri melalui tabel `hubungan_keluarga`.
- Menambahkan *accessor* `nama` pada model `Orang` untuk kompatibilitas field `nama_lengkap` di seluruh view.

### Fase 8: Penyempurnaan & Data Seeding
- Melengkapi profil pesantren (visi, misi) agar halaman profil tidak kosong.
- Membuat data *dummy* berita untuk demonstrasi halaman berita.
- Mengaktifkan gelombang PSB agar portal pendaftaran bisa digunakan.
- Membuat akun demo untuk setiap role (admin, guru, wali santri) beserta data pendukungnya.

## 📄 Lisensi

Proyek ini dikembangkan untuk keperluan akademik (Skripsi) dan menggunakan framework [Laravel](https://laravel.com) yang dilisensikan di bawah [MIT License](https://opensource.org/licenses/MIT).

---

<p align="center">
  Dikembangkan dengan ❤️ oleh <strong>Ach. Zulfi Zibyan</strong><br>
  Sebagai Tugas Akhir (Skripsi) — 2026
</p>

