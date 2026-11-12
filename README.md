<p align="center">
  <img src="public/images/brand/hafis-logo.svg" alt="HAFIS Logo" width="360" />
</p>

<p align="center">
  <img src="public/images/brand/rs.svg" alt="RSUD dr. H. Moch. Ansari Saleh" width="56" height="56" />
  <br>
  <strong>Hospital APAR & Fire Inspection System</strong><br>
  Sistem Digitalisasi Inspeksi, Manajemen Aset Proteksi Kebakaran & Tagging QR Code Terpadu<br>
  <strong>Instalasi K3RS • RSUD dr. H. Moch. Ansari Saleh Banjarmasin</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12" />
  <img src="https://img.shields.io/badge/PHP-8.5-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.5" />
  <img src="https://img.shields.io/badge/Tailwind_CSS-v4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind CSS" />
  <img src="https://img.shields.io/badge/PHPUnit_Tests-16%20Passed-10B981?style=for-the-badge&logo=checkmarx&logoColor=white" alt="PHPUnit Passed" />
  <img src="https://img.shields.io/badge/K3RS_Standard-Permenaker_04%2F1980-0F172A?style=for-the-badge&logo=shield&logoColor=white" alt="K3RS Standard" />
</p>

---

## 📌 Gambaran Umum

**HAFIS** (*Hospital APAR & Fire Inspection System*) adalah platform digital operasional yang dikembangkan khusus untuk **Instalasi Keselamatan dan Kesehatan Kerja Rumah Sakit (K3RS) RSUD dr. H. Moch. Ansari Saleh Banjarmasin**. 

Sistem ini menggantikan pencatatan kartu gantung manual dengan ekosistem digital terpadu: mulai dari penerbitan stiker QR Code fisik tabung, pemindaian kamera mobile oleh petugas di lapangan, pencatatan checklist 7 parameter fisik APAR, hingga audit trail penggantian tabung dan rekapitulasi kepatuhan akreditasi rumah sakit (Standar MFK KARS/SNARS).

---

## ⚡ Fitur Utama

### 1. Label Stiker Aset Standar Resmi (*Official Hospital Safety Asset Tag*)
* **Multi-Format Cetak**: Mendukung cetak 1 stiker (kiri atas), 2 stiker (paruh atas), dan format pas label thermal.
* **Cetak Massal A4 (4 Stiker per Lembar)**: Otomatis membagi armada APAR ke dalam kuadran simetris ($105 \times 148.5\text{ mm}$) dengan panduan potong silang presisi (`✂ POTONG`) dan jarak aman bebas benturan.
* **Filter Dinamis Terintegrasi**: Tombol cetak langsung menyaring unit berdasarkan pencarian teks hierarkis gedung/ruangan maupun dropdown lokasi K3RS.
* **Ekspor Resolusi Tinggi**: Generator bawaan untuk mengunduh stiker format PNG HD 300 DPI dan format vektor SVG asli.

### 2. Inspeksi Lapangan & Scanner QR Kamera Mobile
* **Akses Cepat di Smartphone**: Petugas K3RS cukup memindai stiker QR pada tabung menggunakan kamera browser tanpa perlu memasang aplikasi pihak ketiga.
* **Checklist Fisik 7 Titik Parameter**:
  1. Kondisi Tabung (karat, penyok, kebersihan).
  2. Pin Pengaman & Segel Utuh.
  3. Selang / Hose (kelenturan, retak).
  4. Corong / Nozzle (kebersihan, sumbatan).
  5. Jarum Manometer / Pressure Gauge (zona hijau).
  6. Berat Media Pemadam (sesuai spesifikasi tara).
  7. Posisi Pemasangan (ketinggian 120–140 cm, bebas halangan).

### 3. Hierarki Lokasi Gedung Rumah Sakit
* Struktur lokasi terstandarisasi 3 tingkat: **Gedung $\rightarrow$ Lantai $\rightarrow$ Ruangan / Area Detail**.
* Sinkronisasi cascading dropdown secara real-time saat penempatan tabung baru maupun pemindahan unit APAR.

### 4. Siklus Hidup & Jejak Penggantian (*Replacement Audit Trail*)
* Alur penggantian resmi ketika tabung rusak, tekanan turun, atau mendekati masa kadaluarsa (EXP).
* Mendukung penggantian dengan kode identitas baru maupun pemindahan stiker lama (*same-code replacement*) dengan riwayat audit utuh.

### 5. Kepatuhan Regulasi K3RS & Pelaporan
* Formulir dan rekapitulasi data dirancang mengacu pada **Permenaker No. 04/MEN/1980** tentang Syarat-Syarat Pemasangan dan Pemeliharaan APAR.
* Memenuhi instrumen Manajemen Fasilitas dan Keselamatan (MFK) akreditasi rumah sakit Kementerian Kesehatan & KARS.

---

## 🛠️ Arsitektur & Teknologi

| Komponen | Spesifikasi | Keterangan |
| :--- | :--- | :--- |
| **Framework** | Laravel 12.x | Backend engine & REST controller |
| **Bahasa** | PHP 8.5+ | Typed properties, match expressions, modern syntax |
| **Styling** | Tailwind CSS v4 & Alpine.js | Desain antarmuka berstandar hospital design system |
| **QR Code Engine** | BaconQrCode & HTML5 Canvas | Render SVG murni dan rasterisasi 300 DPI |
| **Testing Suite** | PHPUnit 11 | Feature tests untuk master data, alur ganti, & cetak massal |
| **Code Standard** | Laravel Pint | Penataan format kode otomatis standar PSR-12 |

---

## 🚀 Panduan Instalasi & Penerapan di Rumah Sakit

### Prasyarat Sistem
* **PHP** $\ge$ 8.2 (Direkomendasikan PHP 8.3 – 8.5)
* Ekstensi PHP wajib: `pdo`, `pdo_sqlite`, `sqlite3`, `mbstring`, `openssl`, `xml`, `gd` (atau `imagick`), `curl`, `fileinfo`
* **Composer** $\ge$ 2.7
* **Node.js** $\ge$ 18.x / 20.x & **NPM**

---

### Langkah Instalasi Pertama Kali (Fresh Clone)

1. **Clone Repository**
   ```bash
   git clone https://github.com/lkmandef/hafis.git
   cd hafis
   ```

2. **Pasang Dependensi Backend**
   ```bash
   composer install
   ```

3. **Jalankan Inisialisasi Otomatis (Setup)**
   ```bash
   composer run setup
   ```
   > Perintah ini secara otomatis akan:
   > - Menyiapkan berkas lingkungan `.env` dari `.env.example`.
   > - Membuat kunci aplikasi enkripsi (`php artisan key:generate`).
   > - Membuat basis data SQLite kosong di `database/database.sqlite`.
   > - Menjalankan migrasi skema tabel (`php artisan migrate --force`).
   > - Mengisi data master resmi rumah sakit dari berkas CSV (`MasterDataSeeder`) meliputi 173 APAR aktif, hierarki gedung, lantai, ruangan, lokasi, denah arsitektur, dan master checklist 5 parameter.
   > - Menginisialisasi direktori storage publik, membuat tautan simbolik (`storage:link`), dan menyalin aset denah arsitektur bawaan.

4. **Buat Akun Administrator & Petugas Lapangan**
   ```bash
   # Mode Interaktif (Sangat Direkomendasikan)
   php artisan hafis:setup-admin
   ```
   > Sistem tidak menyediakan password bawaan (default password) demi keamanan rumah sakit. Anda akan diminta memasukkan nama, username, email, dan kata sandi baru (minimal 8 karakter) untuk akun Admin (Kepala K3RS) dan Petugas Lapangan.
   >
   > *Untuk instalasi otomatis (CI/CD atau non-interaktif), Anda dapat menyediakan argumen CLI atau variabel environment:*
   > ```bash
   > php artisan hafis:setup-admin --no-interaction \
   >   --admin-name="Kepala K3RS" --admin-username="admin" --admin-email="admin@rsudansarisaleh.id" --admin-password="PasswordKuat123!" \
   >   --petugas-name="Petugas K3RS" --petugas-username="petugas" --petugas-email="petugas@rsudansarisaleh.id" --petugas-password="PasswordKuat123!"
   > ```

5. **Pasang & Kompilasi Aset Frontend**
   ```bash
   npm install
   npm run build
   ```

6. **Jalankan Aplikasi**
   ```bash
   php artisan serve
   ```
   Buka peramban di: `http://127.0.0.1:8000`

---

## 🔄 Prosedur Pembaruan Sistem (Update)

Ketika tim IT rumah sakit menarik pembaruan kode versi terbaru dari repositori:

```bash
# 1. Tarik pembaruan kode
git pull origin main

# 2. Perbarui dependensi backend
composer install --no-dev --optimize-autoloader

# 3. Jalankan migrasi skema baru jika ada
php artisan migrate --force

# 4. Muat penambahan data master baru tanpa menimpa data yang telah ada
php artisan db:seed --class=MasterDataSeeder --force

# 5. Bangun ulang aset antarmuka frontend
npm install
npm run build

# 6. Bersihkan cache aplikasi
php artisan optimize:clear
```

---

## 💾 Prosedur Cadangan & Pemulihan (Backup & Restore)

Untuk menjamin kelangsungan data keselamatan rumah sakit, pihak IT RS dianjurkan melakukan backup rutin terjadwal terhadap 2 komponen utama:

1. **Berkas Basis Data SQLite**:
   * Lokasi: `database/database.sqlite`
   * Berisi seluruh riwayat inspeksi, checklist, tiket tindak lanjut, akun pengguna, dan perubahan status tabung APAR.
2. **Berkas Lampiran Foto & Dokumen**:
   * Lokasi: `storage/app/public/`
   * Berisi foto dokumentasi fisik temuan inspeksi lapangan, foto bukti penanganan tindak lanjut, dan berkas denah.

### Contoh Perintah Pencadangan Manual:
```bash
# Simpan salinan bertanggal ke direktori backup lokal/eksternal
copy database\database.sqlite D:\BACKUP_HAFIS\database_backup_%date:~10,4%%date:~4,2%%date:~7,2%.sqlite
xcopy /E /I /Y storage\app\public D:\BACKUP_HAFIS\storage_public_%date:~10,4%%date:~4,2%%date:~7,2%
```

---

## ⚠️ Peringatan Penting: Kebijakan Data Operasional

> [!IMPORTANT]
> **Data Operasional Tidak Disimpan di Repositori Git**
> 
> * Repositori ini secara ketat mengabaikan berkas `.env` dan `database/*.sqlite` untuk melindungi privasi, kredensial pengguna, serta riwayat operasional rumah sakit.
> * Database hasil `composer run setup` hanya berisi **Data Master Awal** (173 unit tabung APAR riil terdaftar, struktur gedung & lokasi K3RS, serta master checklist).
> * Riwayat inspeksi lapangan, tiket tindak lanjut, log aktivitas, dan akun pengguna operasional tersimpan secara eksklusif pada server lokal rumah sakit Anda dan **TIDAK AKAN** pernah terunggah ke repositori publik/git. Pastikan prosedur backup di atas dijalankan secara berkala.

---

## 🧪 Pengujian Otomatis

Seluruh logika bisnis, cascading lokasi, filter pencarian gedung, dan layout cetak A4 dilindungi oleh automated feature test:

```bash
# Menjalankan seluruh pengujian
php artisan test

# Menjalankan pengujian spesifik master data & cetak QR
php artisan test --filter=AparMasterDataTest

# Memeriksa dan memformat standar kode
vendor/bin/pint --format agent
```

---

## 👥 Pengguna & Hak Akses

| Peran (*Role*) | Akses & Wewenang |
| :--- | :--- |
| **Kepala / Admin K3RS** | Master Data APAR, cetak stiker massal, konfigurasi checklist, manajemen user, dan ekspor laporan keselamatan. |
| **Petugas Lapangan / Inspector** | Akses cepat scanner QR kamera, pengisian form checklist inspeksi bulanan, dan pengajuan penggantian unit. |

---

## 🏛️ Institusi

Dikembangkan dan diterapkan untuk operasional keselamatan proteksi kebakaran di:
* **Rumah Sakit**: RSUD dr. H. Moch. Ansari Saleh Banjarmasin
* **Unit Kerja**: Instalasi Keselamatan dan Kesehatan Kerja Rumah Sakit (K3RS)
* **Wilayah**: Pemerintah Provinsi Kalimantan Selatan

---

<p align="center">
  <small>© 2026 HAFIS — Hospital APAR & Fire Inspection System. RSUD dr. H. Moch. Ansari Saleh.</small>
</p>
