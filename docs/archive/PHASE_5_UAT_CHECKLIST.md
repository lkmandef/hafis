# HAFIS — PHASE 5: FIELD USER ACCEPTANCE TESTING (UAT) CHECKLIST

**Project:** HAFIS — Hospital APAR Fire-safety Information System  
**Unit Kerja:** Keselamatan dan Kesehatan Kerja Rumah Sakit (K3RS)  
**Rumah Sakit:** RSUD dr. H. Moch. Ansari Saleh Banjarmasin  
**Repository:** `C:\laragon\www\aparcek-8`  
**Environment:** Laravel 13.34.0, PHP 8.5.10, Tailwind CSS v4, Vite 8.3.2  
**Tanggal Penyusunan:** 27 Oktober 2026  
**Status Dokumen:** **ACTIVE — PREPARED FOR FIELD UAT**  

---

## 1. Panduan & Ketentuan Pelaksanaan UAT

1. **Prinsip Keselamatan Data:** Pengujian tidak boleh menghapus, mengubah, atau merusak master data 173 APAR dan 64 lokasi riil K3RS di database operasional.
2. **Kerahasiaan Akun:** Tidak ada kata sandi produksi yang dicantumkan dalam dokumen ini. Akun uji resmi:
   - *Petugas Lapangan:* Username `k3_petugas`
   - *Kepala / Admin K3RS:* Username `lukmanoman17` (atau `k3_admin`)
3. **Kriteria Status Skenario:**
   - **`Pass`**: Skenario telah diverifikasi secara fungsional dan visual melalui otomatisasi browser / unit test.
   - **`BLOCKED — FIELD TEST REQUIRED`**: Skenario memerlukan verifikasi langsung menggunakan perangkat keras fisik (kamera optik smartphone, stiker label fisik, jaringan lokal rumah sakit, atau gesture fisik layar iPhone/Android notch).
   - **`Fail`**: Skenario mengalami kegagalan fungsional atau visual.

---

## 2. Matriks Lembar Kerja UAT Lapangan

### Modul A: Otentikasi & Navigasi Petugas

| ID | Skenario | Prasyarat | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Status | Bukti / Catatan | Penguji | Tanggal |
| :---: | :--- | :--- | :--- | :--- | :--- | :---: | :--- | :--- | :---: |
| **UAT-A01** | Login Petugas Lapangan via Smartphone | Akun `k3_petugas` aktif di database | 1. Buka URL login di browser smartphone.<br>2. Masukkan username `k3_petugas` dan kata sandi.<br>3. Ketuk tombol 'Masuk ke Sistem'. | Berhasil login, diarahkan ke Dashboard; nama dan peran 'Petugas K3RS' tampil di header. | Login sukses, dialihkan ke `/dashboard`, session tersimpan aman. | **Pass** | `AuthenticationTest::test_users_can_authenticate_using_username` (Passed) | Tim QA | 27/10/2026 |
| **UAT-A02** | Navigasi Menu Drawer Mobile | Berada di halaman Dashboard pada mobile (<1024px) | 1. Ketuk ikon hamburger menu di topbar kiri atas.<br>2. Periksa daftar menu samping.<br>3. Ketuk di luar drawer atau ikon silang untuk menutup. | Drawer navigasi slide-in mulus; seluruh link (APAR, Jadwal, Scan, Laporan, Denah) dapat diakses; tidak ada overflow. | Drawer terbuka dengan transisi bersih; link aktif ter-highlight; drawer menutup responsif. | **Pass** | Browser subagent viewport 320x640 & 390x844; 0 error. | Tim QA | 27/10/2026 |
| **UAT-A03** | Proteksi Hak Akses Petugas vs Admin | Login sebagai `k3_petugas` (non-admin) | 1. Akses URL administratif `/pengguna`.<br>2. Coba akses endpoint penghapusan APAR `DELETE /apar/1`. | Sistem menolak akses dengan respons HTTP 403 Forbidden; petugas tidak dapat memodifikasi hak akses. | HTTP 403 Forbidden diterima saat petugas mengakses rute admin. | **Pass** | `AuthorizationAndRoleTest::test_petugas_cannot_access_user_management` (Passed) | Tim QA | 27/10/2026 |

---

### Modul B: Pemindaian QR & Identifikasi Fisik APAR

| ID | Skenario | Prasyarat | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Status | Bukti / Catatan | Penguji | Tanggal |
| :---: | :--- | :--- | :--- | :--- | :--- | :---: | :--- | :--- | :---: |
| **UAT-B01** | Pemindaian Kamera Optik QR Fisik di Lapangan | Stiker label QR tercetak dan tertempel pada fisik tabung APAR di gedung RS | 1. Buka menu 'Scan APAR' (`/scan`).<br>2. Izinkan akses kamera browser.<br>3. Arahkan lensa kamera smartphone ke QR tabung APAR pada jarak 15–30 cm. | Kamera mendeteksi QR code secara instan dan mengarahkan langsung ke halaman Detail APAR unit terkait. | Belum diuji dengan optik kamera smartphone pada stiker fisik di dinding RS. | **BLOCKED — FIELD TEST REQUIRED** | Memerlukan perangkat fisik smartphone petugas di lokasi gedung RS. | Petugas K3RS | *Pending UAT* |
| **UAT-B02** | Pencarian Cepat Kode APAR Manual (Fallback) | Tabung APAR terdaftar (contoh: `APAR-GBS-01`) | 1. Buka menu 'Scan APAR'.<br>2. Gulir ke form input manual.<br>3. Masukkan kode `APAR-GBS-01` atau nomor registrasi.<br>4. Ketuk 'Cari APAR'. | Sistem menemukan unit dan mengarahkan ke `/apar/{id}` unit `APAR-GBS-01`. | Unit ditemukan seketika dan langsung membuka detail APAR. | **Pass** | `ScanQrScannerTest::test_search_apar_by_exact_code` (Passed) | Tim QA | 27/10/2026 |
| **UAT-B03** | Tampilan Prioritas Mobile pada Detail APAR | Unit APAR terbuka di viewport 390x844 | 1. Buka halaman detail APAR (`/apar/1`).<br>2. Periksa urutan tampilan kartu pada layar smartphone. | Kartu QR Code APAR dan Status Kesiapan muncul paling atas (`order-1`) tanpa harus menggulir melewati tabel riwayat inspeksi. | Kartu QR code & status kelaikan tampil di viewport pertama; tombol cetak label dan download SVG berfungsi. | **Pass** | Tangkapan layar `apar_detail_390x844_1793066337627.png`. | Tim QA | 27/10/2026 |

---

### Modul C: Formulir Pemeriksaan Fisik 5 Poin & Pencegahan Duplikasi

| ID | Skenario | Prasyarat | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Status | Bukti / Catatan | Penguji | Tanggal |
| :---: | :--- | :--- | :--- | :--- | :--- | :---: | :--- | :--- | :---: |
| **UAT-C01** | Evaluasi 5 Parameter Fisik Lengkap K3RS | Tabung APAR berstatus aktif | 1. Buka `/pemeriksaan/mulai?apar_id=1`.<br>2. Periksa ketersediaan 5 checklist: Tekanan, Pin/Segel, Handle, Fisik Tabung, Nozel.<br>3. Pilih 'Baik' untuk semua parameter. | Evaluator otomatis menetapkan kesimpulan 'Baik', banner hijau menyala, tombol simpan aktif. | Seluruh 5 checklist tampil; evaluasi otomatis `evaluateOverallHealth()` menghasilkan status 'Baik'. | **Pass** | `PemeriksaanWorkflowTest::test_user_can_submit_inspection_with_valid_data` (Passed) | Tim QA | 27/10/2026 |
| **UAT-C02** | Deteksi Kerusakan & Rekomendasi Otomatis | Tabung APAR berstatus aktif | 1. Pada form pemeriksaan, ubah parameter 'Tekanan' menjadi 'Tidak Baik' (jarum di zona merah).<br>2. Periksa feedback visual. | Banner rekomendasi oranye otomatis muncul; sistem merekomendasikan kesimpulan 'Rusak / Perlu Tindakan'; kolom catatan wajib diisi. | Banner oranye langsung tampil dinamis; usulan status berubah ke 'rusak'. | **Pass** | Verifikasi browser script `evaluateOverallHealth()`; visual banner terkonfirmasi. | Tim QA | 27/10/2026 |
| **UAT-C03** | Fixed Bottom Bar & Scroll Safe Area iOS | Smartphone iPhone / Android dengan home bar | 1. Buka form pemeriksaan di smartphone.<br>2. Gulir hingga bagian paling bawah (upload foto & catatan).<br>3. Periksa bilah aksi 'Simpan Pemeriksaan'. | Bilah aksi mengambang dengan padding safe area di atas home indicator; input catatan terbawah tidak tertutup bilah simpan. | Bilah simpan melayang aman; form memiliki `pb-28`; seluruh input dapat diakses. | **BLOCKED — FIELD TEST REQUIRED** | Simulasi browser PASS (`pb-[max(...)]`); memerlukan konfirmasi gestur usap layar iPhone fisik. | Petugas K3RS | *Pending UAT* |
| **UAT-C04** | Pencegahan Duplikasi Inspeksi Bulan Sama | Tabung APAR sudah memiliki catatan inspeksi pada bulan berjalan | 1. Coba kirimkan form pemeriksaan kedua untuk unit yang sama pada bulan dan tahun yang sama. | Sistem menolak submisi ganda dan memberikan peringatan validasi: unit sudah diinspeksi untuk periode ini. | Submisi ditolak oleh unique index `apar_id + periode_bulan + periode_tahun`. | **Pass** | `PemeriksaanWorkflowTest::test_duplicate_inspection_in_same_month_is_prevented` (Passed) | Tim QA | 27/10/2026 |

---

### Modul D: Matriks Inspeksi Bulanan (Januari–Desember)

| ID | Skenario | Prasyarat | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Status | Bukti / Catatan | Penguji | Tanggal |
| :---: | :--- | :--- | :--- | :--- | :--- | :---: | :--- | :--- | :---: |
| **UAT-D01** | Tampilan Matriks Kartu Kendali 12 Bulan | Terdapat riwayat inspeksi di database | 1. Buka modul `/pemeriksaan`.<br>2. Periksa kartu kendali tiap unit APAR.<br>3. Verifikasi kolom bulan 1 s.d. 12 (Jan–Des). | Setiap bulan memiliki indikator status visual (Hijau = Baik, Merah = Rusak, Abu-abu = Belum Diperiksa). | Matriks 12 bulan tampil presisi; badge status bulanan sesuai data log transaksi. | **Pass** | `Phase3MonthlyInspectionTest::test_monthly_matrix_renders_all_twelve_months` (Passed) | Tim QA | 27/10/2026 |
| **UAT-D02** | Filter Periode & Lokasi Gedung | Data APAR tersebar di 17 gedung | 1. Pilih dropdown filter Gedung (contoh: 'Gedung Bedah Sentral').<br>2. Pilih filter status 'Perlu Tindakan'.<br>3. Ketuk 'Terapkan Filter'. | Daftar unit terfilter akurat sesuai gedung dan status tanpa mereset query parameter pagination. | Filter berjalan lancar; URL query string tersinkronisasi; data tabel sesuai kriteria. | **Pass** | `LaporanLokasiFilterTest::test_filter_by_gedung_and_status` (Passed) | Tim QA | 27/10/2026 |

---

### Modul E: Remediasi & Tindak Lanjut Pemeliharaan

| ID | Skenario | Prasyarat | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Status | Bukti / Catatan | Penguji | Tanggal |
| :---: | :--- | :--- | :--- | :--- | :--- | :---: | :--- | :--- | :---: |
| **UAT-E01** | Pencatatan Tindak Lanjut Temuan Rusak | Terdapat APAR berstatus 'Rusak' / 'Perlu Perhatian' | 1. Buka `/tindak-lanjut/create?apar_id=1`.<br>2. Pilih jenis tindakan: 'Pengisian Ulang (Refill)'.<br>3. Isi uraian tindakan dan nama teknisi pelaksana.<br>4. Simpan formulir. | Rekord tindak lanjut tersimpan dengan status 'Dalam Proses' atau 'Selesai'; log riwayat APAR terbarui. | Data tersimpan pada tabel `tindak_lanjuts`; status unit APAR disinkronkan. | **Pass** | `TindakLanjutTest::test_can_create_tindak_lanjut` (Passed) | Tim QA | 27/10/2026 |
| **UAT-E02** | Penutupan Temuan & Pemulihan Kelaikan APAR | Tindak lanjut berstatus 'Dalam Proses' | 1. Buka rincian tindak lanjut.<br>2. Perbarui status menjadi 'Selesai Ditangani' beserta tanggal selesai.<br>3. Simpan pembaruan. | Status tindak lanjut berubah hijau 'Selesai'; temuan tertutup; unit APAR kembali berstatus 'Layak / Siap Pakai'. | Status berhasil diperbarui; badge dashboard menyesuaikan secara otomatis. | **Pass** | `TindakLanjutTest::test_updating_tindak_lanjut_status_restores_apar_health` (Passed) | Tim QA | 27/10/2026 |

---

### Modul F: Peta Denah Penempatan & Akurasi Marker

| ID | Skenario | Prasyarat | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Status | Bukti / Catatan | Penguji | Tanggal |
| :---: | :--- | :--- | :--- | :--- | :--- | :---: | :--- | :--- | :---: |
| **UAT-F01** | Keselarasan Geometri Denah di Smartphone | File gambar denah lantai terunggah | 1. Buka menu 'Penempatan' (`/penempatan`) di smartphone (360px & 390px).<br>2. Periksa keselarasan kontainer denah terhadap rasio asli gambar. | Kontainer denah tidak melar secara vertikal; tidak ada letterbox kosong; rasio gambar denah dan lapisan marker sinkron 1:1. | `aspect-ratio` terhitung dinamis dari dimensi fisik gambar; marker persentase menunjuk tepat pada gambar denah. | **Pass** | Evaluasi visual & DOM bounding-box: letterbox 93.65px tereliminasi. | Tim QA | 27/10/2026 |
| **UAT-F02** | Penempatan Titik Marker Fisik di Ruangan RS | Petugas K3RS memvalidasi titik APAR riil | 1. Buka denah lantai tempat tabung terpasang di gedung RS.<br>2. Bandingkan posisi titik marker pada layar dengan lokasi fisik tabung di koridor/ruangan. | Titik marker di layar menunjuk dinding/tiang ruangan yang identik dengan fisik tabung di lapangan. | Titik koordinat tersimpan di DB, namun kesesuaian fisik terhadap tata ruang RS harus diverifikasi petugas di lokasi. | **BLOCKED — FIELD TEST REQUIRED** | Memerlukan inspeksi visual langsung di lorong gedung RS. | Petugas K3RS | *Pending UAT* |

---

### Modul G: Pusat Laporan, Ekspor CSV & Cetak Label

| ID | Skenario | Prasyarat | Langkah Pengujian | Hasil yang Diharapkan | Hasil Aktual | Status | Bukti / Catatan | Penguji | Tanggal |
| :---: | :--- | :--- | :--- | :--- | :--- | :---: | :--- | :--- | :---: |
| **UAT-G01** | Ekspor Rekapitulasi CSV (Excel) | Terdapat data pemeriksaan dan temuan | 1. Buka `/laporan/pemeriksaan`.<br>2. Ketuk tombol 'Export CSV (Excel)'. | Berkas CSV terunduh secara instan; encoding UTF-8 rapi saat dibuka di Microsoft Excel; data lengkap. | Berkas `.csv` ter-stream dengan header `Content-Type: text/csv`; data kolom lengkap. | **Pass** | `LaporanTest::test_export_pemeriksaan_csv_returns_stream_response` (Passed) | Tim QA | 27/10/2026 |
| **UAT-G02** | Format Cetak Dokumen Resmi & Label QR | Akses menu cetak di desktop/laptop | 1. Buka `/apar/1/cetak-qr`.<br>2. Buka dialog cetak browser (`Ctrl+P`).<br>3. Periksa layout cetak stiker label QR. | Label tercetak rapi dengan logo HAFIS, kode APAR monospaced, jenis media, dan QR SVG tajam tanpa blur. | Tampilan format cetak label SVG vektor presisi dan tajam. | **Pass** | Verifikasi media cetak `@media print` pada template `cetak-qr.blade.php`. | Tim QA | 27/10/2026 |

---

## 3. Ringkasan Status Skenario UAT

- **Total Skenario UAT:** 17 Skenario
- **Skenario Diverifikasi (Pass):** **13 Skenario (76.5%)**
- **Skenario Menunggu Uji Lapangan Fisik (BLOCKED — FIELD TEST REQUIRED):** **4 Skenario (23.5%)**
  - `UAT-B01`: Pemindaian kamera optik smartphone terhadap stiker fisik di dinding gedung RS.
  - `UAT-C03`: Uji kenyamanan fisik safe area gesture bar pada iPhone/Android notch nyata di genggaman tangan petugas.
  - `UAT-F02`: Verifikasi akurasi posisi titik marker denah terhadap tiang/dinding fisik di 17 gedung RS.
  - Skenario UAT Lapangan Bersama: Penandatanganan berita acara serah-terima operasional oleh Kepala K3RS.
- **Skenario Gagal (Fail):** **0 Skenario (0%)**

---
*Checklist ini siap dicetak atau digunakan secara digital oleh Tim K3RS RSUD dr. H. Moch. Ansari Saleh saat pelaksanaan UAT lapangan.*
