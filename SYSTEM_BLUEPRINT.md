# SYSTEM_BLUEPRINT.md — HAFIS (Hospital APAR & Fire Inspection System)
**Dokumen Arsitektur & Rencana Teknis Redesign Sistem K3RS**  
*RSUD dr. H. Moch. Ansari Saleh Banjarmasin*  
*Versi: 2.0 (Pre-Implementation Final Blueprint)*  
*Status: CANONICAL REFERENCE — Read-Only Before Phase Execution*

---

## 1. Executive Summary

HAFIS (*Hospital APAR & Fire Inspection System*) adalah sistem informasi tata kelola dan inspeksi keselamatan proteksi kebakaran aktif berbasis web yang dirancang untuk unit K3RS (*Keselamatan dan Kesehatan Kerja Rumah Sakit*) di RSUD dr. H. Moch. Ansari Saleh Banjarmasin.

Berdasarkan audit komprehensif terhadap codebase eksisting (Laravel 13, PHP 8.5, SQLite) dan hasil rekonsiliasi data operasional riil dalam `LOCATION_APAR_MASTER.md`, ditemukan kesenjangan mendasar antara sistem versi awal dan kebutuhan regulasi K3RS di lapangan:
1. **Model Operasional Penjadwalan vs Periode Kalender Bulanan**: Sistem awal menggunakan siklus *admin manual scheduling* (`jadwals` dan `jadwal_apars`). K3RS di lapangan membutuhkan **kartu kendali inspeksi 12 bulan otomatis (Januari–Desember)** per unit APAR tanpa ketergantungan pembuatan jadwal oleh admin.
2. **Hierarki Lokasi Kaku vs Lokasi Riil K3RS**: Sistem awal memaksakan struktur 3 tingkat berantai (`Gedung → Lantai → Ruangan`). Data lapangan K3RS mencakup 64 titik lokasi beragam (lantai gedung, instalasi, koridor luar, area utilitas, ruang operasional, dan gudang stok) yang bersifat semi-flat dan tidak seragam.
3. **Parameter Checklist**: Checklist awal memuat 10 item teknis panjang, sedangkan K3RS menggunakan 5 parameter fisik ringkas: **Tekanan, Pin/Segel, Handle, Kondisi Fisik, Nozel**, ditambah paraf petugas terverifikasi.
4. **Atribut Non-Fungsional**: Field `nomor_register` terbukti tidak digunakan oleh K3RS dan menambah friksi input. Fitur `Pergantian APAR` tidak diinginkan di menu utama operasional rutin, namun wajib dipertahankan di backend dan modul laporan untuk pembuktian siklus aset dan kebutuhan laporan akademik/PKL.

Dokumen ini menjadi **acuan tunggal kebenaran arsitektural (*Single Source of Truth*)** sebelum migrasi database, refactoring controller, pembaruan view Blade, dan eksekusi seeder data riil dijalankan.

---

## 2. Current Architecture (Arsitektur Eksisting)

### 2.1 Stack Teknologi & Runtime
- **Framework**: Laravel 13.34.0 (Application Framework)
- **Runtime**: PHP 8.5.10
- **Database**: SQLite 3 (`database/database.sqlite`)
- **Frontend / Styling**: Blade Server-Side Rendering, Tailwind CSS v4, Vite 8, Vanilla JavaScript
- **Vector Rendering**: `bacon/bacon-qr-code` (inline SVG generator tanpa API eksternal)
- **Kamera Scanner**: HTML5 Video Stream + BarcodeDetector API dengan fallback pustaka `jsQR`

### 2.2 Layering & Struktur File Eksisting
- **Controllers** (`app/Http/Controllers/`):
  - `AparController.php`: Pengelolaan master APAR (CRUD, pencarian, cetak QR).
  - `JadwalController.php`: Pembuatan jadwal inspeksi, pemilihan target APAR, sinkronisasi status jadwal.
  - `ScanController.php`: Handler kamera QR scanner dan lookup fleksibel (QR code, kode APAR, nomor register).
  - `PemeriksaanController.php`: Alur inspeksi (mulai, checklist 10 item, penyimpanan, hasil).
  - `TindakLanjutController.php`: Pencatatan remediasi temuan fisik (`tidak_baik`).
  - `PenggantianAparController.php`: Pencatatan unit afkir/rusak dan unit pengganti baru.
  - `LaporanController.php`: Agregasi 5 modul laporan, tampilan print, dan export stream CSV.
  - `LokasiController.php`, `DenahController.php`, `PenempatanController.php`: Hierarki gedung-lantai-ruangan dan plotting koordinat titik APAR pada denah SVG/PNG.
  - `DashboardController.php`: Metrik ringkasan eksekutif dan progress jadwal aktif.
- **Models** (`app/Models/`):
  - 11 model: `Apar`, `Jadwal`, `Pemeriksaan`, `PemeriksaanDetail`, `TindakLanjut`, `PenggantianApar`, `Gedung`, `Lantai`, `Ruangan`, `Denah`, `User`.
- **Database Schema** (`database/migrations/`):
  - 12 file migrasi terdaftar, mencakup tabel: `users`, `cache`, `jobs`, `gedungs`, `lantais`, `ruangans`, `apars`, `denahs`, `jadwals`, `jadwal_apars`, `pemeriksaans`, `pemeriksaan_details`, `tindak_lanjuts`, `penggantian_apars`.

---

## 3. Current Problems & Pain Points (Analisis Masalah Sistem Eksisting)

1. **Bottleneck Admin pada Jadwal Manual**:
   Jika admin K3RS tidak membuat dokumen jadwal di awal bulan, petugas lapangan tidak dapat melakukan inspeksi terjadwal. Di lapangan, 11 dari 12 data pemeriksaan eksisting di database memiliki status ad-hoc (`jadwal_id = NULL`), membuktikan bahwa workflow jadwal tidak cocok dengan pola kerja petugas.
2. **Ketiadaan Konsep Kartu Kendali 12 Bulan**:
   Pemeriksaan saat ini hanya dicatat sebagai log kronologis datar (*timestamped stream*). Tidak ada matriks tahunan per tabung (Januari s.d. Desember) yang memperlihatkan bulan mana yang sudah diperiksa dan bulan mana yang terlewat.
3. **Data Lokasi yang Dipaksakan**:
   Tabel `apars` memiliki foreign key wajib `ruangan_id` (`NOT NULL`). Jika data K3RS hanya menyebutkan "GENZET" atau "KUMALA Lt 5" tanpa nama ruangan, sistem lama memaksa pembuatan entitas palsu (*dummy room*) agar foreign key terpenuhi.
4. **Checklist Terlalu Panjang**:
   10 item evaluasi saat ini memperlambat proses inspeksi petugas di lapangan yang harus memeriksa puluhan tabung setiap hari dengan perangkat mobile.
5. **Redudansi Field Nomor Register**:
   Field `nomor_register` tampil di form create, edit, tabel index, detail show, scanner lookup, dan ekspor CSV, padahal K3RS tidak menggunakannya sama sekali.
6. **Menu Pergantian APAR Mengaburkan Fokus Harian**:
   Menu ini hadir di sidebar navigasi utama, padahal pergantian fisik tabung adalah peristiwa insidental yang jarang terjadi dibandingkan inspeksi rutin.

---

## 4. Target Domain Model (Model Domain Baru)

Arsitektur baru merestrukturisasi rantai domain menjadi **alur linier sederhana**:

```text
┌─────────────────┐
│     LOKASI      │ (Master Titik/Area K3RS: 64 Lokasi)
└────────┬────────┘
         │ 1 Lokasi memiliki 0 s.d. n APAR
         ▼
┌─────────────────┐
│      APAR       │ (Aset Tabung: Terpasang vs Stok Sediaan)
└────────┬────────┘
         │ 1 APAR memiliki 12 Periode Pemeriksaan / Tahun
         ▼
┌─────────────────┐
│   PEMERIKSAAN   │ (Log Bulanan: apar_id + tahun + bulan)
│     BULANAN     │ (Hasil Keseluruhan + Paraf Petugas)
└────────┬────────┘
         │ 1 Pemeriksaan memiliki 5 Detail Item Evaluasi
         ▼
┌─────────────────┐
│   PEMERIKSAAN   │ (Tekanan, Pin/Segel, Handle,
│     DETAIL      │  Kondisi Fisik, Nozel)
└────────┬────────┘
         │ jika terdapat hasil = 'tidak_baik'
         ▼
┌─────────────────┐
│  TINDAK LANJUT  │ (Perbaikan, Isi Ulang, Pemeliharaan)
└─────────────────┘
```

**Prinsip Utama Domain Baru**:
- **Pemeriksaan Bulanan Otomatis**: Identitas pemeriksaan ditentukan oleh kombinasi tuple `(apar_id, periode_tahun, periode_bulan)`.
- **Independen dari Jadwal Manual**: Sistem otomatis mengetahui bulan dan tahun berjalan saat scanner dibuka atau form diakses.
- **Isolasi Stok Sediaan**: Membedakan tabung terpasang operasional dengan tabung cadangan di Gudang K3RS.

---

## 5. Location Model (Master Lokasi Target)

### 5.1 Karakteristik Data Lokasi
Sistem HAFIS tidak lagi memaksakan relasi bertingkat `Gedung → Lantai → Ruangan`. Lokasi diperlakukan sebagai **entitas master operasional mandiri** yang merangkum titik penempatan APAR sesuai terminologi K3RS.

### 5.2 Skema Tabel Target (`lokasis`)
Tabel baru atau hasil adaptasi master lokasi dirancang sebagai berikut:
- `id`: unsigned big integer (Primary Key)
- `nama`: string(150), unique — nama lokasi resmi K3RS (contoh: `EMERALD Lt 1`, `MUTIARA Lt 1 HD`, `IT DALAM`, `IPSRS`, `GENZET`)
- `kode`: string(50), nullable, unique — kode singkatan lokasi untuk pembentukan kode APAR (contoh: `EMR1`, `ITD`, `GIZ1`)
- `kategori`: string(60), nullable — klasifikasi area (Pelayanan Medis, Rawat Inap, Penunjang, Utilitas, Administrasi, Gudang, Khusus)
- `status`: string(20), default `aktif` — nilai enum: `aktif` (memiliki APAR terpasang), `kosong` (0 APAR), `stok` (khusus Gudang K3RS)
- `keterangan`: text, nullable — rincian posisi atau catatan konfirmasi K3RS (contoh: "Area luar ruang IT / koridor")
- `timestamps`

### 5.3 Rekapitulasi Data Master Lokasi Berdasarkan `LOCATION_APAR_MASTER.md`
Total 64 titik lokasi K3RS:
1. **Lokasi Terpasang Aktif**: 59 lokasi (173 installed/active APAR units).
2. **Lokasi Nol APAR (`kosong`)**: 4 lokasi (`JAMRUD`, `YAKUT`, `GIOK`, `KANTIN` — 0 APAR). Disimpan di master, dilarang membuat APAR dummy.
3. **Lokasi Khusus Persediaan (`stok`)**: 1 lokasi (`GUDANG K3RS` — 64 unit stok sediaan).
Total aset keseluruhan: 237 total APAR assets (173 installed/active + 64 stock).

---

## 6. APAR Model (Master Aset Unit)

### 6.1 Identitas & Atribut Model `Apar`
- `id`: bigint (Primary Key)
- `lokasi_id`: foreignId constrained ke `lokasis`
- `kode`: string(50), unique — identitas penomoran internal sistem HAFIS (contoh: `APAR-EMR1-01`). **Catatan:** Kode ini adalah identitas sistem HAFIS untuk penelusuran QR dan operasional digital, bukan nomor seri fisik resmi pabrikan/K3RS.
- `qr_code`: string(100), unique — payload QR sistem (format: `HAFIS-QR-{KODE}-{RANDOM6}`)
- `posisi_detail`: string(255), nullable — deskripsi posisi spesifik di titik lokasi
- `jenis_media`: string(50) — bersumber dari Tabel 12 *LAPORAN TRIWULAN KE-3 2026*:
  - `Dry Chemical Powder`: 115 unit
  - `CO2 (Carbon Dioxide)`: 58 unit
  - Total: 173 unit terpasang
- `kapasitas`: string(30) — bersumber dari rincian Tabel 12:
  - Powder: `25 kg` (3 unit), `6 kg` (108 unit), `3 kg` (4 unit)
  - CO2: `7 kg` (17 unit), `5 kg` (7 unit), `3 kg` (34 unit)
  - Total: 173 unit terpasang
- `merek`: string(100), nullable — nilai tersimpan adalah `NULL` karena dokumen sumber triwulan asli tidak mencantumkan merek APAR. Sistem menampilkan label UI `Belum terdata` saat merek bernilai kosong/null. Dilarang mengasumsikan merek tertentu (seperti Servvo) tanpa bukti fisik.
- `tipe_model`: string(100), nullable
- `status`: string(30), default `baik` — state aset:
  - `baik`: Siap pakai, kondisi prima.
  - `perhatian`: Ditemukan anomali minor dari inspeksi fisik, memerlukan monitor.
  - `rusak`: Rusak berat / bocor / hilang tekanan, perlu tindakan segera atau penggantian.
  - `tidak_aktif`: Dinonaktifkan sementara.
  - `sudah_diganti`: Unit lama yang telah digantikan oleh unit baru via penggantian.
- `tanggal_pemasangan`: date, nullable
- `tanggal_kadaluarsa`: date, nullable
- `tanggal_pemeriksaan_terakhir`: date, nullable
- `keterangan`: text, nullable
- `replaced_by_apar_id`, `replaces_apar_id`, `alasan_nonaktif`, `tanggal_nonaktif`: atribut pelacak riwayat penggantian unit (dipertahankan).
- `softDeletes` & `timestamps`

### 6.2 Pemisahan Tegas: APAR Terpasang vs Stok Sediaan
- **APAR Terpasang** (173 installed/active APAR units): Tersebar di 59 lokasi aktif, terikat ke lokasi operasional, wajib memiliki stiker QR, dan wajib mengikuti siklus 12 bulan inspeksi bulanan.
- **Stok Sediaan** (64 unit di Gudang K3RS): Dicatat terpisah sebagai persediaan logistik K3RS pada lokasi `GUDANG K3RS`. Sesuai fakta dokumen triwulan, 64 unit stok **bukan APAR terpasang operasional** dan tidak didaftarkan sebagai record APAR aktif yang diinspeksi bulanan, guna mencegah distorsi kepatuhan inspeksi.
- **Grand Total Aset**: 237 unit aset (173 unit terpasang + 64 unit stok sediaan).

### 6.3 Standarisasi Format Kode APAR & Batasan Alokasi Atribut
Pola penamaan resmi:
```text
APAR-{KODE_LOKASI}-{NOMOR_URUT_2DIGIT}
```
Contoh:
- Lokasi `EMERALD Lt 1` (kode `EMR1`, kuota 5 APAR: 3 Powder 6kg, 2 CO2 3kg):
  `APAR-EMR1-01` (Powder 6kg), `APAR-EMR1-02` (Powder 6kg), `APAR-EMR1-03` (Powder 6kg), `APAR-EMR1-04` (CO2 3kg), `APAR-EMR1-05` (CO2 3kg).
- Lokasi `IT DALAM` (kode `ITD`, kuota 1 APAR: 1 CO2 3kg):
  `APAR-ITD-01`
- Lokasi `IT LUAR` (kode `ITL`, kuota 1 APAR: 1 Powder 6kg):
  `APAR-ITL-01`

**Keterbatasan Alokasi Atribut Per Record**:
Dokumen sumber (Tabel 12) hanya menyajikan jumlah agregat per lokasi dan kategori kapasitas/media, tanpa identitas atau nomor seri fisik individual per tabung. Oleh karena itu, pengaitan kombinasi media dan kapasitas ke nomor urut kode APAR pada database HAFIS merupakan **alokasi administratif deterministik**, bukan hasil verifikasi fisik nomor seri tabung di lapangan. Apabila nantinya K3RS melakukan sensus fisik nomor seri, data administratif ini dapat dimutakhirkan tanpa mengubah jumlah agregat per lokasi.

---

## 7. Monthly Inspection Model (Pemeriksaan Bulanan)

### 7.1 Strategi Entitas
Menggunakan **Opsi A: Reuse tabel `pemeriksaans`** dengan penambahan koordinat periode kalender.

### 7.2 Atribut & Constraint Tabel `pemeriksaans`
- `id`: bigint (Primary Key)
- `apar_id`: foreignId constrained ke `apars` (cascadeOnDelete)
- `periode_tahun`: smallint (not null, contoh: `2026`)
- `periode_bulan`: tinyint (not null, rentang `1` s.d. `12`)
- `tanggal_pemeriksaan`: dateTime (not null) — waktu aktual eksekusi fisik
- `pemeriksa_nama`: string(100) (not null) — nama petugas pelaksana (sekaligus bertindak sebagai **Paraf Digital**)
- `user_id`: foreignId nullable constrained ke `users` (nullOnDelete)
- `hasil_keseluruhan`: string(30) (not null, `baik` | `perlu_tindakan`)
- `status`: string(20), default `selesai`
- `catatan`: text, nullable
- `foto_path`: string, nullable
- `jadwal_id`: foreignId nullable constrained ke `jadwals` (nullOnDelete — dipertahankan sebagai legacy field)
- `timestamps`

### 7.3 Integritas Data & Proteksi Duplikasi
- **Unique Constraint**:
  ```sql
  UNIQUE (apar_id, periode_tahun, periode_bulan)
  ```
- **Business Rule Validation**:
  Satu tabung APAR hanya boleh memiliki satu record pemeriksaan selesai (*completed inspection*) per bulan kalender. Jika petugas memeriksa ulang pada bulan yang sama:
  - Sistem menampilkan hasil pemeriksaan yang sudah ada dengan opsi melihat rincian;
  - Jika diperlukan perbaikan data temuan, dilakukan melalui alur modul Tindak Lanjut atau revisi terotorisasi, bukan membuat duplikasi record bulanan.

---

## 8. Checklist Model (Parameter Fisik K3RS)

### 8.1 Evaluasi Item Fisik (Tabel `pemeriksaan_details`)
Struktur tabel `pemeriksaan_details` tetap dipertahankan (`pemeriksaan_id`, `item_nama`, `hasil`, `catatan`, `urutan`, `timestamps`).

Daftar 10 item checklist lama digantikan oleh **5 Parameter Utama K3RS**:
1. **Tekanan**: Memeriksa jarum manometer berada di zona hijau (atau berat tabung CO2 sesuai toleransi).
2. **Pin / Segel**: Memeriksa safety pin terpasang kokoh dan segel plastik/timah utuh belum putus.
3. **Handle**: Memeriksa tuas penekan dan pegangan bebas macet, tidak berkarat, dan tidak bengkok.
4. **Kondisi Fisik**: Memeriksa tabung bebas korosi, cat tidak mengelupas parah, tidak penyok, dan label instruksi terbaca.
5. **Nozel**: Memeriksa corong/selang fleksibel, tidak retak, dan lubang semprot tidak tersumbat kotoran/serangga.

### 8.2 Parameter Paraf
- Pada form manual K3RS, kolom terakhir adalah "Paraf".
- Dalam HAFIS digital, paraf diwadahi secara otomatis melalui:
  - Input field nama pemeriksa (`pemeriksa_nama`);
  - Akun login pencatat (`user_id`);
  - Timestamp ISO `tanggal_pemeriksaan`.
  Ketiga data ini ditampilkan sebagai identitas otentikasi pemeriksa yang sah di kartu inspeksi.

### 8.3 Logika Evaluasi Otomatis
- Setiap item memiliki pilihan hasil: `baik`, `tidak_baik`, `tidak_diperiksa`.
- **Aturan Tegas**: Jika terdapat minimal 1 parameter bernilai `tidak_baik`, maka `hasil_keseluruhan` otomatis bernilai **`perlu_tindakan`**.
- Tabung dengan hasil `perlu_tindakan` otomatis menaikkan status aset tabung dari `baik` menjadi **`perhatian`**, serta membuka opsi pembuatan tiket di modul `tindak_lanjuts`.

---

## 9. Monthly Matrix UX (Kartu Kendali Inspeksi 12 Bulan)

Pada halaman Detail APAR (`/apar/{id}`) dan Laporan Hasil Inspeksi, sistem menyajikan representasi visual **Kartu Kendali Bulanan**:

```text
KARTU KENDALI INSPEKSI APAR — TAHUN 2026
Unit: APAR-EMR1-01 | Lokasi: EMERALD Lt 1 | Media: Powder 6 kg

┌─────┬─────┬─────┬─────┬─────┬─────┬─────┬─────┬─────┬─────┬─────┬─────┐
│ JAN │ FEB │ MAR │ APR │ MEI │ JUN │ JUL │ AGS │ SEP │ OKT │ NOV │ DES │
├─────┼─────┼─────┼─────┼─────┼─────┼─────┼─────┼─────┼─────┼─────┼─────┤
│  ✓  │  ✓  │  ✓  │  ✓  │  ✓  │  ✓  │  ✓  │  ✓  │  ✓  │  !  │  ○  │  -  │
└─────┴─────┴─────┴─────┴─────┴─────┴─────┴─────┴─────┴─────┴─────┴─────┘
Keterangan:
[ ✓ ] Hijau     = Sudah Diperiksa (Kondisi Baik)
[ ! ] Merah     = Sudah Diperiksa (Ada Temuan / Perlu Tindakan)
[ ○ ] Kuning    = Bulan Berjalan (Belum Diperiksa — Actionable)
[ - ] Abu-abu   = Bulan Mendatang / Terlewat
```

**Interaktivitas UI**:
- Mengklik bulan yang berstatus **Sudah Diperiksa** (`✓` / `!`): Membuka modal ringkas atau halaman detail hasil checklist bulan tersebut (`pemeriksaan.show`).
- Mengklik bulan berjalan yang **Belum Diperiksa** (`○`): Membuka form input checklist inspeksi bulan berjalan (`pemeriksaan.create`).

---

## 10. Scanner Flow (Alur Pemindaian Lapangan)

### 10.1 Alur Pengguna Lapangan (*Field UX Flow*)

```text
Petugas Lapangan Buka Menu /scan di Smartphone
                      ↓
Arahkan Kamera ke Stiker QR APAR (atau Input Kode Manual)
                      ↓
ScanController@cari mengidentifikasi Unit APAR
                      ↓
Sistem Mengecek Pemeriksaan pada Periode Bulan Berjalan (Tahun & Bulan Saat Ini)
                      ├── Belum Ada Pemeriksaan Bulan Ini?
                      │   └── LANGSUNG BUKA FORM CHECKLIST (/pemeriksaan/create)
                      │       (Form 5 Item K3RS, tombol besar, auto-fill tanggal & paraf)
                      │       ↓
                      │       Simpan Hasil Inspeksi
                      │       ↓
                      │       Notifikasi Sukses & Tampil Ringkasan Bulan Berjalan
                      │
                      └── Sudah Ada Pemeriksaan Bulan Ini?
                          └── BUKA LEMBAR HASIL INSPEKSI (/pemeriksaan/{id})
                              (Menampilkan info: "Unit telah diperiksa pada tanggal DD/MM/YYYY")
```

### 10.2 Preservasi Komponen Teknis Scanner
Penyempurnaan alur di atas **tidak mengubah**:
- Format string QR: `HAFIS-QR-{KODE}-{RANDOM6}`.
- Dukungan deteksi kamera: Engine native `BarcodeDetector` dengan fallback library `jsQR.min.js`.
- Penanganan Secure Context: Otomatis mendeteksi HTTPS / localhost untuk izin kamera `navigator.mediaDevices.getUserMedia`.
- Manual input fallback: Kolom input text kode APAR tetap tersedia jika kamera kotor/rusak.

---

## 11. Dashboard Architecture

Dashboard dialihkan dari orientasi jadwal ke orientasi **Kepatuhan Inspeksi Kalender Bulanan**.

### 11.1 Metrik Utama Dashboard Target
1. **Ringkasan Kepatuhan Bulan Berjalan** (misal: *Oktober 2026*):
   - `Total APAR Wajib Periksa`: Jumlah seluruh unit APAR terpasang berstatus aktif.
   - `Sudah Diperiksa Bulan Ini`: Count unit yang memiliki record `pemeriksaans` pada bulan dan tahun berjalan.
   - `Belum Diperiksa Bulan Ini`: Selisih unit yang belum memiliki record di bulan berjalan.
   - `Persentase Kepatuhan`: `(Sudah Diperiksa / Total Wajib Periksa) * 100%`.
2. **Kondisi Fisik Tabung Terkini**:
   - `Kondisi Baik`: Unit tanpa anomali.
   - `Perlu Perhatian`: Unit dengan temuan minor dari pemeriksaan terakhir.
   - `Rusak / Tindakan`: Unit rusak yang memerlukan perbaikan/pengisian segera.
3. **Temuan & Tindak Lanjut Aktif**:
   - Jumlah temuan bulan ini yang belum diselesaikan oleh tim pemeliharaan.
4. **Daftar APAR Belum Diperiksa Bulan Ini (Quick-Action Table)**:
   - Menampilkan daftar unit yang belum diinspeksi beserta lokasi fisiknya agar petugas dapat langsung menuju titik tabung.

---

## 12. Reports Architecture (5 Modul Laporan Wajib)

Sistem mempertahankan seluruh 5 modul laporan dengan penyesuaian parameter:

1. **Laporan Master Data APAR** (`/laporan/apar`):
   - Menampilkan inventaris seluruh tabung, lokasi, jenis media, kapasitas, masa berlaku.
   - Menghapus kolom `nomor_register` dari tabel web, layout print, dan CSV export.
2. **Laporan Riwayat & Hasil Pemeriksaan APAR** (`/laporan/pemeriksaan`):
   - Mengganti filter `jadwal_id` menjadi filter **Bulan** (Jan–Des) dan **Tahun**.
   - Menyediakan 2 mode tampilan:
     - Mode Log Detail: Daftar kronologis pemeriksaan per transaksi.
     - Mode Matriks Tahunan: Tabel matriks rekap 12 bulan (Jan–Des) per unit APAR, ideal untuk pelaporan resmi akreditasi rumah sakit dan Kemenkes.
3. **Laporan Temuan Pemeriksaan Fisik** (`/laporan/temuan`):
   - Memfilter rincian parameter checklist yang bernilai `tidak_baik`.
   - Menghubungkan langsung temuan dengan status pengerjaan tiket perbaikan di modul tindak lanjut.
4. **Laporan Tindak Lanjut & Pemeliharaan** (`/laporan/tindak-lanjut`):
   - Dokumentasi historis perbaikan tabung, pengisian ulang media, penggantian selang/pin, dan uji tekan.
5. **Laporan Penggantian APAR** (`/laporan/penggantian`):
   - Dokumentasi penonaktifan unit lama afkir dan unit pengganti baru.
   - **Wajib dipertahankan** sebagai bukti pelaporan tata kelola aset dan pemenuhan laporan ilmiah PKL.

---

## 13. Legacy Schedule Strategy (Penanganan Jadwal Lama)

### 13.1 Analisis Dependensi Eksisting
Tabel `jadwals` dan `jadwal_apars` saat ini terhubung di berbagai titik:
- Foreign key `pemeriksaans.jadwal_id` (bertipe `nullable`).
- Metode `Apar::jadwals()` dan `Apar::getActiveJadwalAttribute()`.
- Metode controller `PemeriksaanController@mulai`, `PemeriksaanController@store`, dan `DashboardController@index`.

### 13.2 Strategi Transisi Aman (Deprecation without Breaking)
1. **Navigasi Sidebar**: Link menu `Jadwal Inspeksi` dialihkan label dan rutenya menjadi `Hasil Inspeksi` (`/pemeriksaan` atau `/hasil-inspeksi`).
2. **Skema Database**: Kolom `pemeriksaans.jadwal_id` tetap dipertahankan sebagai field nullable. Tabel `jadwals` dan `jadwal_apars` **tidak di-drop**.
3. **Controller Decoupling**:
   - `PemeriksaanController@store` tidak lagi mewajibkan parameter `jadwal_id`. Jika `jadwal_id` tidak dikirim, nilai kolom diisi `null`.
   - Logika penutupan jadwal otomatis (`Jadwal.status = selesai`) dijadikan conditional legacy: hanya dieksekusi jika `jadwal_id` memang ada.
4. **Hasil**: Data historis jadwal lama tetap terbaca di database tanpa mengganggu alur operasional bulanan yang baru.

---

## 14. Nomor Register Strategy (Strategi Penghapusan Nomor Register)

### 14.1 Status Dependensi Saat Ini
Field `nomor_register` terdapat pada:
- Database: Kolom nullable `apars.nomor_register`.
- Model: `$fillable` di `Apar.php`.
- Controllers: Query filter di `AparController`, `PenggantianAparController`, `ScanController`, `TindakLanjutController`, `LaporanController`.
- Views: 8 file Blade (form create/edit, kartu info, rekap laporan, dashboard).
- Tests: 7 file PHPUnit feature test.

### 14.2 Rencana 3 Tahap Pembersihan Aman
- **Tahap 1 (UI Level — Aman 100%)**:
  Hapus elemen `<input name="nomor_register">` dari seluruh form Blade (`create`, `edit`, `ganti`). Hapus baris teks/badge `nomor_register` dari tampilan show, card, dashboard, dan laporan.
- **Tahap 2 (Controller & Model Level)**:
  Hapus aturan validasi `'nomor_register' => ...` di form request / controller. Buat nilai default `null` saat insert/update.
- **Tahap 3 (Database & Test Cleanup — Tahap Akhir)**:
  Setelah seluruh controller dan unit test disesuaikan agar tidak lagi meng-assert string `REG-xxx`, jalankan migration baru:
  ```php
  Schema::table('apars', function (Blueprint $table) {
      $table->dropColumn('nomor_register');
  });
  ```

---

## 15. Replacement Strategy (Strategi Fitur Penggantian APAR)

### 15.1 Kebijakan Domain
K3RS menyatakan formulir `Pergantian APAR` tidak diperlukan dalam alur kerja utama harian petugas. Namun, secara akademis dan teknis pelaporan aset, alur penggantian tabung rusak permanen/afkir adalah komponen vital penilaian PKL.

### 15.2 Solusi Arsitektural: *Hidden from Navigation, Fully Retained in Backend*
1. **Navigasi Sidebar**: Tag tautan `<!-- Penggantian Unit APAR -->` di `resources/views/components/sidebar.blade.php` dihapus/disembunyikan dari menu navigasi utama.
2. **Backend & Routing**:
   - Route `Route::prefix('penggantian')` dan `Route::get('/apar/{id}/ganti')` di `routes/web.php` **tetap aktif 100%**.
   - `PenggantianAparController.php` dan model `PenggantianApar.php` **tetap utuh**.
3. **Titik Akses Alternatif**:
   - Tombol sekunder "Ganti APAR" tetap dapat diakses di halaman detail tabung yang bersangkutan (`apar/show.blade.php`) hanya jika unit berstatus rusak/afkir (`$apar->can_be_replaced`).
   - Modul `Laporan Penggantian APAR` tetap dapat diakses di menu Laporan (`/laporan/penggantian`) untuk verifikasi akademis penguji sidang PKL.

---

## 16. Denah & Floor Plan Strategy (Strategi Fitur Penempatan Denah)

### 16.1 Audit Kondisi Denah Saat Ini
- Sistem memiliki tabel `denahs` (1 record denah SVG pada `lantai_id = 1`) dan modul `PenempatanController`.
- Dari 6 APAR eksisting, hanya 1 unit (`APAR-IGD-01`) yang telah memiliki nilai koordinat denah (`koordinat_x = 62.5`, `koordinat_y = 37.2`).
- Data K3RS dalam `LOCATION_APAR_MASTER.md` tidak menyediakan file gambar denah untuk 64 titik lokasi yang ada.

### 16.2 Rekomendasi Arsitektural
1. **Komponen Non-Blocking**: Fitur denah dan penempatan titik koordinat **tidak boleh menjadi prasyarat (*blocking requirement*)** pendaftaran maupun inspeksi APAR. Kolom `koordinat_x` dan `koordinat_y` pada tabel `apars` tetap bersifat `nullable()`.
2. **Penetapan Status**: Fitur denah ditetapkan sebagai **fitur sekunder / visual penunjang (*secondary visual enhancement*)**.
3. **Adaptasi Masa Depan**: Modul penempatan denah tetap dipertahankan kodenya tanpa memaksa K3RS mengunggah gambar denah jika berkas gambar rumah sakit belum tersedia secara resmi.

---

## 17. Target Database Model (Rancangan Skema Database Final)

### 17.1 Ringkasan Perubahan Tabel

| Nama Tabel | Status Arsitektur | Perubahan Kolom & Peran |
|---|---|---|
| `lokasis` | **Refactor / Tabel Baru** | Master titik lokasi K3RS: `id`, `nama`, `kode`, `kategori`, `status` (`aktif`/`kosong`/`stok`), `keterangan`. |
| `apars` | **Dipertahankan & Refactor** | Tambah `lokasi_id` (FK nullable ke `lokasis`). Pertahankan `ruangan_id` sementara untuk backward compatibility. Kolom `nomor_register` ditandai deprecated. Kolom penggantian unit tetap ada. |
| `pemeriksaans` | **Dipertahankan & Diperluas** | Tambah kolom `periode_tahun` (smallint) dan `periode_bulan` (tinyint). Unique constraint `(apar_id, periode_tahun, periode_bulan)`. Kolom `jadwal_id` tetap ada sebagai nullable legacy. |
| `pemeriksaan_details` | **Dipertahankan** | Skema tabel tetap sama. Nilai baris `item_nama` beralih ke 5 item fisik K3RS: Tekanan, Pin/Segel, Handle, Kondisi Fisik, Nozel. |
| `tindak_lanjuts` | **Dipertahankan 100%** | Skema tetap sama, mencatat tiket perbaikan dari temuan `pemeriksaan_details`. |
| `penggantian_apars`| **Dipertahankan 100%** | Skema tetap sama, mencatat audit pergantian tabung afkir ke unit baru. |
| `jadwals` & `jadwal_apars` | **Legacy (Preserved)** | Tabel tidak dihapus dari database fisik guna menjamin integritas data masa lalu. |
| `gedungs`, `lantais`, `ruangans` | **Secondary / Legacy** | Dipertahankan tanpa menghambat pendaftaran lokasi baru. |
| `denahs` | **Dipertahankan Opsional** | Menampung gambar denah lantai jika tersedia. |

### 17.2 Skema Definisi Migrasi Tambahan (Target Migration)

```php
// Migration 1: Tambah kolom periode pada pemeriksaans
Schema::table('pemeriksaans', function (Blueprint $table) {
    $table->smallInteger('periode_tahun')->nullable()->after('jadwal_id');
    $table->tinyInteger('periode_bulan')->nullable()->after('periode_tahun');
    
    $table->unique(['apar_id', 'periode_tahun', 'periode_bulan'], 'pemeriksaan_apar_periode_unique');
    $table->index(['periode_tahun', 'periode_bulan'], 'pemeriksaan_periode_index');
});

// Migration 2: Tabel master lokasi baru
Schema::create('lokasis', function (Blueprint $table) {
    $table->id();
    $table->string('nama', 150)->unique();
    $table->string('kode', 50)->nullable()->unique();
    $table->string('kategori', 60)->nullable();
    $table->string('status', 20)->default('aktif'); // aktif, kosong, stok
    $table->text('keterangan')->nullable();
    $table->timestamps();
});

// Migration 3: Hubungkan apars ke lokasis
Schema::table('apars', function (Blueprint $table) {
    $table->foreignId('lokasi_id')->nullable()->after('qr_code')->constrained('lokasis')->nullOnDelete();
});
```

---

## 18. Migration Strategy (Rencana Migrasi 9 Fase Bertahap)

Implementasi wajib dilakukan dalam **9 fase sekuensial yang terisolasi** untuk menjamin tidak ada fase yang mematahkan fungsi sistem:

```text
Phase 1: Database Extension (Periode Pemeriksaan)
    ↓
Phase 2: Master Lokasi Implementation
    ↓
Phase 3: Seeding Master Lokasi & Master APAR Aktual K3RS
    ↓
Phase 4: Refactor Pemeriksaan Model & Checklist 5-Item
    ↓
Phase 5: Refactor Scanner & Direct Monthly Inspection Flow
    ↓
Phase 6: Redesign Detail APAR (12-Month Control Card UX)
    ↓
Phase 7: Navigation & Sidebar Cleanup (Hide Penggantian, Rename Hasil Inspeksi)
    ↓
Phase 8: Dashboard & Reports Refactoring (Monthly Focus)
    ↓
Phase 9: Comprehensive Automated Testing & Regression Verification
```

### Rincian Dependensi Antar-Fase:
- **Phase 1** harus selesai sebelum Phase 4 (karena controller membutuhkan kolom `periode_tahun` dan `periode_bulan`).
- **Phase 2** harus selesai sebelum Phase 3 (master APAR membutuhkan master lokasi).
- **Phase 3** harus selesai sebelum Phase 5 & 6 (pengujian UI kartu kendali membutuhkan APAR aktual).
- **Phase 4 & 5** harus tuntas sebelum Phase 8 (metrik dashboard dan filter laporan mengagregasi data pemeriksaan bulanan).
- **Phase 9** memverifikasi seluruh fungsionalitas dan memastikan seluruh test suite PHPUnit berstatus hijau.

---

## 19. Data Migration Strategy (Manajemen Data Dummy vs Data Riil)

### 19.1 Kondisi Data Eksisting Saat Ini
- 6 unit APAR dummy (`APAR-IGD-01`, `APAR-IGD-02`, `APAR-NONAKTIF-01`, `APAR-TEST-E`, `APAR-IRJ-001`, `Emerald-001`).
- 1 jadwal dummy (`Inspeksi APAR October 2026`).
- 12 log pemeriksaan dummy (hasil pengujian pengembang).
- 0 tindak lanjut, 0 penggantian.

### 19.2 Matriks Keputusan Data (*Data Action Matrix*)

| Data Entitas | Tindakan | Justifikasi |
|---|---|---|
| **6 APAR Dummy** | **REPLACE / CLEANUP** | Merupakan data uji coba pengembang. Digantikan sepenuhnya oleh data tabung aktual dari `LOCATION_APAR_MASTER.md`. |
| **1 Jadwal Dummy** | **PRESERVE (Arsip)** | Dibiarkan tersimpan di database sebagai bukti riwayat pengujian fase awal. |
| **12 Pemeriksaan Dummy** | **MIGRATE / BACKUP** | Kolom `periode_tahun` dan `periode_bulan` pada 12 data lama diisi otomatis berdasarkan `YEAR(tanggal_pemeriksaan)` dan `MONTH(tanggal_pemeriksaan)`. |
| **Master Lokasi K3RS** | **INSERT (Baru)** | Diisi dari 64 titik lokasi dalam `LOCATION_APAR_MASTER.md` via `LokasiAktualSeeder`. |
| **Master APAR Aktual** | **INSERT (Baru)** | Diisi 173 installed/active APAR units dengan penomoran deterministik `APAR-{LOKASI}-{NO}` via `AparAktualSeeder`. Total aset rumah sakit adalah 237 total APAR assets (173 installed/active + 64 stock di Gudang K3RS). |
| **64 Stok Gudang K3RS** | **HOLD (Stok)** | Ditandai sebagai status persediaan; tidak diikutsertakan dalam target inspeksi operasional sebelum verifikasi K3RS. |

---

## 20. Test Strategy (Strategi Verifikasi & Pengujian Otomatis)

### 20.1 Test Suite Eksisting yang Wajib Tetap Hijau
- `tests/Feature/AparMasterDataTest.php` (CRUD master data APAR).
- `tests/Feature/ScanQrScannerTest.php` (Deteksi QR dan routing pencarian).
- `tests/Feature/TindakLanjutTest.php` (Pencatatan perbaikan temuan).
- `tests/Feature/PenggantianAparTest.php` (Siklus penggantian aset).
- `tests/Feature/LaporanTest.php` (Ekspor dan cetak 5 laporan).

### 20.2 Test Suite Baru yang Wajib Ditambahkan (`MonthlyInspectionTest.php`)
1. **Proteksi Duplikasi Bulan Sama**:
   Memastikan `POST /pemeriksaan` untuk `APAR A` pada `Bulan X Tahun Y` yang kedua kalinya ditolak/dialihkan dengan notifikasi proteksi duplikasi.
2. **Kelayakan Bulan Berbeda**:
   Memastikan `APAR A` berhasil diinspeksi pada `Bulan X`, dan kemudian berhasil diinspeksi kembali pada `Bulan X+1` tanpa konflik.
3. **Multi-Unit Bulan Sama**:
   Memastikan `APAR A` dan `APAR B` dapat diinspeksi secara independen pada bulan yang sama.
4. **Auto-Transition Status Temuan**:
   Memastikan jika salah satu dari 5 parameter bernilai `tidak_baik`, maka status `hasil_keseluruhan` otomatis `perlu_tindakan` dan status APAR ter-upgrade ke `perhatian`.
5. **Scanner Direct Routing**:
   Memastikan pemindaian QR pada APAR yang belum diinspeksi di bulan berjalan langsung mengarahkan ke formulir checklist bulan tersebut.

---

## 21. Risks & Mitigation Register (Manajemen Risiko)

| Risiko Teknis / Operasional | Dampak | Probabilitas | Rencana Mitigasi |
|---|---|---|---|
| **Kegagalan Foreign Key pada Penghapusan Jadwal** | Tinggi | Rendah | Tidak menghapus tabel `jadwals` atau kolom `jadwal_id`. Kolom dibuat nullable sehingga alur baru berjalan mandiri tanpa memutus relasi data lama. |
| **Kerusakan Pemindaian QR di Lapangan** | Tinggi | Rendah | Menggunakan payload QR deterministik berbasis kode unik `HAFIS-QR-{KODE}-{RANDOM}`. Handler scanner di `ScanController` mempertahankan pencarian ganda (QR code dan kode teks). |
| **Distorsi Angka Kepatuhan oleh 64 Stok Sediaan** | Tinggi | Sedang | Mengisolasi 64 unit stok di Gudang K3RS dalam status `stok/persediaan`. Query dashboard hanya menghitung APAR berstatus `aktif/terpasang`. |
| **Ambiguitas Lokasi Gabungan (misal Kumala 4 s.d. Mutiara 4)** | Sedang | Tinggi | Menyimpan nama lokasi apa adanya sesuai dokumen sumber. Jangan memecah lokasi secara sepihak sebelum ada instruksi tertulis K3RS. |
| **Error Test Suite Akibat Penghapusan Nomor Register** | Sedang | Sedang | Menghapus dependensi pengujian secara terencana di Phase 9 setelah form UI dibersihkan. |
| **Beban Kognitif Petugas di Layar Smartphone Kecil** | Sedang | Sedang | Menerapkan UI mobile-first dengan dial antislop: tombol opsi Baik/Tidak Baik berukuran minimal 48px, kontras tinggi, dan bebas elemen dekoratif AI-slop yang tidak fungsional. |

---

## 22. Implementation Order (Urutan Langkah Kerja Eksekusi)

Berikut urutan langkah kerja teknis yang akan dieksekusi saat fase implementasi dibuka:

1. **Step 1**: Buat migration penambahan kolom `periode_tahun` dan `periode_bulan` pada tabel `pemeriksaans`.
2. **Step 2**: Buat migration tabel `lokasis` dan penambahan foreign key `lokasi_id` pada `apars`.
3. **Step 3**: Buat seeder `LokasiAktualSeeder` berisi 64 titik lokasi dari `LOCATION_APAR_MASTER.md`.
4. **Step 4**: Buat seeder `AparAktualSeeder` berisi master tabung terpasang dengan kode standar `APAR-{LOKASI}-{NO}`.
5. **Step 5**: Update model `Pemeriksaan` (konstanta 5 checklist item K3RS) dan refactor `PemeriksaanController@store` untuk menetapkan periode otomatis.
6. **Step 6**: Refactor `ScanController@cari` agar otomatis mendeteksi status inspeksi bulan berjalan dan melakukan direct-routing.
7. **Step 7**: Modifikasi halaman Detail APAR (`apar/show.blade.php`) untuk merender visual Kartu Kendali Matriks 12 Bulan.
8. **Step 8**: Ubah `resources/views/components/sidebar.blade.php`: sembunyikan `Pergantian APAR`, ubah menu `Jadwal Inspeksi` menjadi `Hasil Inspeksi`.
9. **Step 9**: Refactor `DashboardController` dan `resources/views/dashboard.blade.php` ke metrik kepatuhan bulanan.
10. **Step 10**: Refactor `LaporanController` (filter bulan & tahun pada Laporan Pemeriksaan) dan bersihkan elemen UI `nomor_register`.
11. **Step 11**: Tulis dan jalankan test otomatis PHPUnit (`MonthlyInspectionTest`) hingga seluruh test suite berstatus PASS.

---

## 23. Open Questions & TBD (Catatan Klarifikasi K3RS)

Daftar poin yang tetap berstatus `TBD` dan memerlukan konfirmasi resmi dari pihak K3RS sebelum data dikunci permanen:

1. **Status Operasional 64 Stok Sediaan di Gudang K3RS**:
   - Apakah 64 unit ini adalah stok tabung cadangan yang baru ditempel stiker saat dipasang menggantikan tabung di ruangan, ataukah tabung di gudang ini juga wajib memiliki stiker QR dan diperiksa berkala setiap bulan?
2. **Pemisahan Kumala Lt 4 (OK) s.d. Gedung Mutiara Lt 4 (Jumlah: 6 APAR)**:
   - Apakah 6 tabung ini dicatat sebagai satu kesatuan area koridor penghubung, atau sebenarnya terbagi (misal: 3 di Kumala Lt 4 dan 3 di Mutiara Lt 4)?
3. **Klarifikasi Titik Tanpa Identitas Gedung**:
   - `RESEPCIONIST Lt 2` (1 APAR): Berada di gedung mana (IRJ, Emerald, atau Mutiara)?
   - `LIFT` (8 APAR): Apakah 8 tabung ini berada di satu gedung tertentu atau tersebar di lobi lift berbagai gedung/lantai?
   - `119` (1 APAR): Apa nama resmi ruangan ini (apakah PSC 119 di IGD)?
4. **Finalisasi Kode Singkatan Lokasi (Prefix)**:
   - Menunggu persetujuan resmi K3RS untuk daftar candidate prefix (`EMR1` s.d. `EMR5`, `ITD`, `ITL`, `GIZ1`, `GIZ2`, `CSD2`) sebelum kode fisik dicetak pada stiker QR.

---

## 24. DOCUMENT STATUS

- **Source of truth**: `LOCATION_APAR_MASTER.md`
- **Installed/active APAR**: 173
- **Stock APAR**: 64
- **Grand total assets**: 237
- **Blueprint aggregate consistency**: PASS
- **Location ambiguity status**: TBD / pending K3RS confirmation
- **Implementation status**: READY AFTER FINAL K3RS DATA CONFIRMATION

> [!IMPORTANT]
> Status *"READY AFTER FINAL K3RS DATA CONFIRMATION"* **bukan** berarti implementasi kode/database dapat langsung dimulai secara otomatis. Status ini menandakan bahwa konsistensi dokumen dan angka agregat telah terverifikasi secara presisi (PASS), sementara keputusan operasional K3RS yang masih terbuka/ambigu tetap didokumentasikan secara eksplisit sebagai TBD.
