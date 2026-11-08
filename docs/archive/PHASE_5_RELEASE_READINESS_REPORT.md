# HAFIS — PHASE 5: RELEASE READINESS REPORT

**Project:** HAFIS — Hospital APAR Fire-safety Information System  
**Unit Kerja:** Keselamatan dan Kesehatan Kerja Rumah Sakit (K3RS)  
**Rumah Sakit:** RSUD dr. H. Moch. Ansari Saleh Banjarmasin  
**Repository:** `C:\laragon\www\aparcek-8`  
**Environment:** Laravel 13.34.0, PHP 8.5.10, Tailwind CSS v4, Vite 8.3.2, SQLite 3  
**Tanggal Evaluasi:** 27 Oktober 2026  

---

## 1. Status Kesiapan Rilis (Release Status)

### Status Resmi: **`READY FOR FIELD UAT`**

### Justifikasi Berdasarkan Bukti Faktual:
1. **Keutuhan Kode & Aset:** Seluruh perbaikan responsivitas mobile (Fase 3) dan pengujian integrasi (Fase 4) telah terkompilasi bersih menggunakan Vite 8 (`npm run build`, exit code 0) dan Blade view cache (`php artisan view:cache`, exit code 0).
2. **Hasil Test Suite:** Seluruh 25 berkas feature test dalam repositori lulus 100% (**224 tests, 2.311 assertions, 0 failure, 0 error**).
3. **Simulasi Browser Multi-Perangkat:** Evaluasi rendering live pada 8 ukuran viewport (320px s.d. 1440px) mencatat `scrollWidth <= clientWidth` (0 overflow horizontal) dan 0 error konsol JavaScript.
4. **Alasan Tidak Langsung `UAT ACCEPTED` atau Production Deploy:**
   Sesuai prinsip tata kelola sistem keselamatan rumah sakit, status **`UAT ACCEPTED`** dan rilis produksi mensyaratkan validasi fisik langsung di lapangan oleh petugas K3RS yang berwenang (uji kamera optik pada stiker label di dinding lorong RS, uji gestur fisik layar smartphone, dan penandatanganan Berita Acara UAT). Oleh karena itu, aplikasi saat ini berada pada status definitif **`READY FOR FIELD UAT`** (siap untuk uji coba lapangan).

---

## 2. Backup dan Prosedur Pemulihan (Backup & Disaster Recovery)

### 2.1 Lokasi & Komponen Data yang Wajib Dicadangkan
1. **Basis Data Operasional:**
   - Berkas: `database/database.sqlite` (berisi 173 APAR terdaftar, 64 lokasi K3RS, log pemeriksaan, dan tindak lanjut).
2. **Berkas Media Gambar:**
   - Direktori denah: `storage/app/public/denah/`
   - Direktori foto temuan pemeriksaan: `storage/app/public/pemeriksaan/`
   - Symlink publik: `public/storage`

### 2.2 Prosedur Backup Rutin (Cold & Hot Snapshot)
Sebelum pelaksanaan Field UAT dan sebelum migrasi produksi, jalankan perintah pencadangan snapshot:
```powershell
# Buat direktori cadangan berstempel waktu
$timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$backupDir = "C:\laragon\backups\hafis_$timestamp"
New-Item -ItemType Directory -Path $backupDir -Force

# Cadangkan basis data SQLite (dalam keadaan server idle)
Copy-Item "C:\laragon\www\aparcek-8\database\database.sqlite" "$backupDir\database.sqlite"

# Cadangkan seluruh berkas upload pengguna
Copy-Item "C:\laragon\www\aparcek-8\storage\app\public" "$backupDir\storage_public" -Recurse

# Catat metadata SHA256 checksum untuk verifikasi integritas
Get-FileHash "$backupDir\database.sqlite" -Algorithm SHA256 | Out-File "$backupDir\checksum.txt"
```

### 2.3 Prosedur Pemulihan Cepat (Disaster Recovery / Restore)
Jika terjadi anomali integritas data saat pengujian lapangan:
1. Hentikan web server lokal / PHP service sementara:
   ```powershell
   # Matikan proses web server sementara
   Stop-Process -Name "php" -Force
   ```
2. Timpa berkas basis data dari snapshot cadangan:
   ```powershell
   Copy-Item "$backupDir\database.sqlite" "C:\laragon\www\aparcek-8\database\database.sqlite" -Force
   ```
3. Bersihkan cache aplikasi Laravel:
   ```powershell
   php artisan optimize:clear
   ```
4. Nyalakan kembali web server dan lakukan verifikasi integritas tabel APAR.

---

## 3. Pemisahan Data Uji dan Data Operasional (Data Segregation)

Untuk mencegah kontaminasi pada data riil 173 unit tabung APAR RSUD dr. H. Moch. Ansari Saleh:
1. **Isolasi Lingkungan Pengujian Otomatis (PHPUnit):**
   - Berkas konfigurasi `phpunit.xml` menggunakan koneksi in-memory atau database terisolasi dengan trait `RefreshDatabase`. Pengujian otomatis tidak pernah menyentuh atau menghapus berkas `database/database.sqlite` operasional.
2. **Penandaan Transaksi Selama Field UAT:**
   - Selama pelaksanaan Field UAT oleh petugas, gunakan unit tabung sampel yang telah disepakati (contoh: `APAR-GBS-01` di Gedung Bedah Sentral).
   - Catatan pemeriksaan uji coba lapangan harus ditandai dengan awalan teks `[UAT-TEST]` pada kolom catatan/uraian agar mudah diidentifikasi atau dibersihkan sebelum Go-Live jika dikehendaki manajemen.
3. **Larangan Operasi Destruktif:**
   - Dilarang menjalankan perintah `php artisan migrate:fresh` atau `migrate:reset` pada basis data operasional tanpa persetujuan tertulis dari Kepala K3RS.

---

## 4. Lingkungan & Konfigurasi Runtime (Environment Checklist)

Berikut perbandingan konfigurasi saat ini (UAT Readiness) terhadap konfigurasi target Production Go-Live:

| Parameter Konfigurasi | Nilai Saat Ini (Field UAT) | Nilai Target (Production Go-Live) | Tindakan Pra-Rilis |
| :--- | :--- | :--- | :--- |
| `APP_ENV` | `local` | `production` | Ubah ke `production` pada `.env` saat resmi diluncurkan. |
| `APP_DEBUG` | `true` | `false` | **Wajib disetel `false`** untuk mencegah kebocoran stack trace error ke pengguna. |
| `APP_URL` | `http://localhost:8000` | `https://hafis.rsansarisaleh.id` (atau IP Intranet RS) | Sesuaikan dengan domain/FQDN intranet rumah sakit. |
| `APP_KEY` | Terkonfigurasi aman (Base64) | Terkonfigurasi aman | Pertahankan, jangan generate ulang di tengah jalan. |
| `DB_CONNECTION` | `sqlite` | `sqlite` | Pastikan permissions berkas `.sqlite` hanya dapat dibaca oleh user web server (IIS/Nginx/Apache). |
| `SESSION_SECURE_COOKIE`| `false` | `true` (jika menggunakan HTTPS) | Aktifkan saat SSL/TLS sudah terpasang. |
| `Vite Production Build` | `public/build/` aktif | `public/build/` aktif | Aset CSS & JS telah terkompilasi optimal (Vite 8). |

---

## 5. Keamanan, Autentikasi, Otorisasi, dan Proteksi Endpoint

1. **Keamanan Kredensial:**
   - Password disimpan menggunakan algoritma hashing standar industri `bcrypt` (`Hash::make()`). Tidak ada kata sandi yang tersimpan dalam bentuk teks biasa (*plain text*).
2. **Proteksi Akses Berlapis (Role-Based Access Control):**
   - Middleware `auth`: Menolak akses tamu tanpa sesi login yang valid ke seluruh modul operasional.
   - Middleware `role:admin`: Membatasi fitur destruktif (manajemen pengguna `/pengguna`, penghapusan APAR `DELETE /apar/{id}`, dan penataan ulang struktur denah) khusus untuk akun Kepala K3RS. Petugas lapangan dibatasi pada hak akses inspeksi fisik dan pemantauan.
3. **Proteksi Form & Modifikasi Data:**
   - Seluruh form submisi `POST`, `PUT`, `PATCH`, dan `DELETE` dilengkapi token proteksi Cross-Site Request Forgery (`@csrf`).
4. **Pencegahan Brute-Force:**
   - Endpoint login dilengkapi pembatasan laju request (*Rate Limiting*) maksimal 5 percobaan gagal berturut-turut sebelum akun/IP dibekukan sementara.

---

## 6. Kesiapan Akses Smartphone melalui Jaringan Lapangan

1. **Topologi Jaringan Lapangan:**
   - Petugas K3RS akan mengakses aplikasi melalui browser smartphone (Chrome Mobile / Safari Mobile) yang terhubung ke jaringan Wi-Fi Intranet Rumah Sakit atau jaringan seluler APN internal.
2. **Optimasi Tampilan Layar Sempit:**
   - Topbar telah dirampingkan hingga lebar 246px, menjamin margin aman 74px pada layar 320px tanpa horizontal scroll.
   - Tabel operasional telah dilengkapi kartu mobile vertikal (`sm:hidden`) sehingga petugas dapat melakukan input inspeksi dengan satu tangan.
3. **Kenyamanan Sentuhan (Ergonomi Lapangan):**
   - Seluruh tombol kontrol utama memiliki area sentuh minimum 36px hingga 44px, meminimalisir kesalahan tekan saat petugas menggunakan sarung tangan inspeksi atau berada di area darurat.

---

## 7. Prosedur Rollback & Penanggung Jawab Operasional

### 7.1 Matriks Penanggung Jawab (Stakeholders)

| Peran | Pihak / Jabatan | Tanggung Jawab Utama |
| :--- | :--- | :--- |
| **Owner Sistem & Konten** | Kepala Unit K3RS RSUD dr. H. Moch. Ansari Saleh | Menyetujui hasil UAT, memvalidasi akurasi lokasi APAR fisik, menandatangani Berita Acara. |
| **User Operasional Lapangan** | Petugas Lapangan K3RS | Melakukan uji coba pemindaian QR fisik di lorong RS dan pengisian checklist inspeksi 5 poin. |
| **Pengembang & Administrator Sistem** | Tim IT / Pengembang HAFIS | Mengawal teknis server, memantau log error, melakukan backup berkala, dan mengeksekusi rollback jika diperlukan. |

### 7.2 Prosedur Rollback Cepat (Jika Terjadi Kegagalan Kritis Lapangan)
1. Jika antarmuka atau server mengalami kegagalan fungsi fatal di tengah inspeksi:
   - Ambil berkas cadangan snapshot terakhir dari folder `C:\laragon\backups\`.
   - Restore database SQLite dan direktori storage upload.
   - Jalankan `php artisan view:clear` dan `php artisan cache:clear`.
   - Beralih sementara ke SOP pencatatan manual berbasis formulir kertas darurat K3RS hingga isu teknis terselesaikan.

---

## 8. Kesimpulan & Roadmap Menuju Production Go-Live

Aplikasi HAFIS telah lulus seluruh verifikasi teknis pra-rilis. Tidak ada defect terbuka yang menghalangi pengujian. Langkah selanjutnya yang direkomendasikan adalah pelaksanaan uji coba lapangan terstruktur sesuai panduan pada `PHASE_5_UAT_CHECKLIST.md`.

```
[ FASE 3: Implementasi Responsivitas ] (SELESAI)
                   │
                   ▼
[ FASE 4: Final QA & Test Suite ] (SELESAI — 224 Tests Passed)
                   │
                   ▼
[ FASE 5: UAT Prep & Release Readiness ] (SELESAI — STATUS: READY FOR FIELD UAT)
                   │
                   ▼
┌────────────────────────────────────────────────────────┐
│              LANGKAH OPERASIONAL BERIKUTNYA            │
├────────────────────────────────────────────────────────┤
│ 1. Uji Lapangan Bersama Petugas K3RS (Field UAT)      │
│    - Scan stiker QR fisik di 3 titik gedung RS         │
│    - Verifikasi pengisian form & safe area di HP fisik │
│ 2. Penandatanganan Berita Acara UAT                    │
│ 3. Konfigurasi Produksi (.env: APP_DEBUG=false)       │
│ 4. Deployment Intranet & Sosialisasi Petugas K3RS      │
└────────────────────────────────────────────────────────┘
```
