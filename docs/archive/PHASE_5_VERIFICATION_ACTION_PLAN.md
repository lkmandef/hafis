# HAFIS — PHASE 5 VERIFICATION ACTION PLAN
## Pre-Release, Field UAT & Production Readiness Roadmap

**Repository:** `C:\laragon\www\aparcek-8`  
**Project:** HAFIS — Hospital APAR Fire-safety Information System  
**Environment:** Laravel 13, PHP 8.5, Tailwind CSS v4, Vite 8, SQLite  
**Auditor:** Senior QA Engineer, Application Security Reviewer, Release Readiness Auditor  
**Audit Reference:** `PHASE_5_VERIFICATION_AUDIT.md`  
**Status Target:** Bertahap dari `CONDITIONALLY READY FOR FIELD UAT` menuju `PRODUCTION GO-LIVE`

---

## 1. STRUKTUR & TATA KELOLA ACTION PLAN

Action plan ini memuat daftar tindakan wajib yang disusun berdasarkan temuan kesenjangan (*gaps*) dan risiko yang teridentifikasi dalam audit verifikasi Phase 5. Seluruh tindakan dikelompokkan ke dalam 4 tahapan gerbang rilis (*release gates*):

1. **Gate 1: Prasyarat Sebelum Field UAT (Pre-Field UAT)**
2. **Gate 2: Eksekusi & Penerimaan UAT (Field UAT Execution & Acceptance)**
3. **Gate 3: Pengetatan Produksi (Production Deployment Hardening)**
4. **Gate 4: Serah Terima Operasional (Operational Handover & Maintenance)**

### Skala Prioritas:
- **P0 (Critical / Blocker):** Tindakan mutlak yang harus selesai sebelum melangkah ke tahap berikutnya; kegagalan menyelesaikan ini dapat membatalkan pengujian atau memicu insiden keamanan.
- **P1 (High):** Tindakan sangat penting terkait akurasi data, fungsionalitas utama, dan keandalan sistem.
- **P2 (Medium):** Tindakan pendukung untuk kenyamanan penggunaan, mitigasi risiko marginal, atau optimalisasi alur kerja.
- **P3 (Low):** Tindakan dokumentasi, penyempurnaan kosmetik, atau perbaikan jangka panjang.

---

## 2. GATE 1: PRASYARAT SEBELUM FIELD UAT (PRE-FIELD UAT)

| ID | Prioritas | Temuan & Dampak | Bukti / Alasan | Langkah Tindak Lanjut | Kriteria Penyelesaian | Penanggung Jawab | Status |
|---|---|---|---|---|---|---|---|
| **ACT-01** | **P0** | Konfigurasi `APP_URL` saat ini merujuk ke `localhost:8000`, sehingga smartphone petugas tidak dapat membuka aplikasi melalui Wi-Fi RS. | `config/app.php` & `.env` aktif. | 1. Tentukan IP statis komputer server lokal (misal: `192.168.1.150`).<br>2. Perbarui `APP_URL=http://192.168.1.150:8000` di `.env` pengujian.<br>3. Pastikan firewall Windows mengizinkan incoming connection port 8000. | Browser smartphone (Android & iOS) dapat memuat halaman login via IP LAN. | IT Support / DevOps | **PENDING EXECUTION** |
| **ACT-02** | **P0** | Database operasional `database.sqlite` memuat 173 APAR riil yang berisiko terotori atau tercampur data pengujian inspeksi lapangan. | File `database/database.sqlite` adalah single source of truth data APAR RS. | 1. Salin file `database/database.sqlite` ke `database/database_pre_uat_snapshot.sqlite`.<br>2. Verifikasi ukuran dan keterbacaan file backup cadangan. | File snapshot cadangan tersimpan di direktori aman sebelum inspeksi pertama dimulai. | QA Lead / Database Admin | **PENDING EXECUTION** |
| **ACT-03** | **P1** | Stiker QR fisik belum diuji keterbacaan optiknya di lapangan nyata. | Skenario `UAT-B01` berstatus `BLOCKED — FIELD TEST REQUIRED`. | 1. Cetak 5 sampel stiker label QR APAR (misal: APAR-G1-01 s/d APAR-G1-05).<br>2. Pasang pada unit APAR di Gedung A Lantai 1.<br>3. Pastikan laminasi stiker tidak menimbulkan refleksi silau berlebih. | 5 stiker fisik terpasang rapi dan siap dipindai kamera smartphone penguji. | Tim K3RS / Logistik | **PENDING EXECUTION** |
| **ACT-04** | **P1** | Petugas penguji lapangan memerlukan akun operasional dengan peran yang sesuai agar tidak menggunakan akun superadmin saat inspeksi. | Database saat ini hanya memuat 1 akun admin (`lukmanoman17`). | 1. Buat 2 akun penguji khusus: 1 akun Petugas Lapangan (`role:petugas`) dan 1 akun Koordinator K3RS.<br>2. Catat kredensial sementara dan serahkan ke penguji. | Penguji dapat login dengan peran masing-masing tanpa privilege admin berlebih. | System Administrator | **PENDING EXECUTION** |

---

## 3. GATE 2: EKSEKUSI & PENERIMAAN UAT (FIELD UAT EXECUTION & ACCEPTANCE)

| ID | Prioritas | Temuan & Dampak | Bukti / Alasan | Langkah Tindak Lanjut | Kriteria Penyelesaian | Penanggung Jawab | Status |
|---|---|---|---|---|---|---|---|
| **ACT-05** | **P0** | 4 skenario inti fisik belum diuji secara langsung di perangkat smartphone riil. | Dokumen `PHASE_5_UAT_CHECKLIST.md` (skenario B01, C03, F02, G02). | 1. Jalankan pemindaian QR di lokasi fisik (UAT-B01).<br>2. Uji interaksi tombol form dengan gestur navigasi ponsel (UAT-C03).<br>3. Verifikasi kesesuaian posisi marker denah dengan ruangan nyata (UAT-F02).<br>4. Uji koneksi pada koridor dengan sinyal Wi-Fi lemah (UAT-G02). | Keempat skenario fisik terisi dengan data aktual, bukti foto, dan status lulus. | Lead QA & Petugas K3RS | **PENDING EXECUTION** |
| **ACT-06** | **P1** | Validasi performa kamera scanner HTML5 pada kondisi cahaya koridor/basement redup (< 50 lux). | Kondisi pencahayaan riil rumah sakit bervariasi. | 1. Uji pemindaian QR di area basement / koridor malam.<br>2. Jika kamera gagal fokus otomatis, verifikasi bahwa fitur fallback pencarian kode manual berfungsi sempurna. | Penguji tetap dapat membuka form inspeksi baik via QR maupun fallback kode manual. | QA Tester | **PENDING EXECUTION** |
| **ACT-07** | **P1** | Data inspeksi uji coba berpotensi mencemari riwayat asli sebelum rilis resmi. | Aktivitas UAT menghasilkan record pada tabel `pemeriksaans` dan `riwayat_apars`. | 1. Tandai atau catat seluruh transaksi pemeriksaan yang dibuat selama sesi UAT.<br>2. Setelah sesi UAT selesai dan didokumentasikan, restore database dari snapshot ACT-02 atau hapus record uji coba secara selektif. | Database bersih dari catatan inspeksi palsu/uji coba sebelum rilis resmi dimulai. | Database Admin | **PENDING EXECUTION** |
| **ACT-08** | **P0** | Ketiadaan persetujuan tertulis (*formal sign-off*) dari pemangku kepentingan rumah sakit. | Standar kepatuhan rilis operasional RSUD. | 1. Presentasikan hasil pengujian lapangan kepada Kepala Instalasi K3RS.<br>2. Buat dan tanda tangani Berita Acara Penerimaan UAT (*UAT Acceptance Sign-Off Sheet*). | Lembar persetujuan UAT bertandatangan resmi Kepala K3RS dan Koordinator IT. | Koordinator K3RS & Lead QA | **PENDING EXECUTION** |

---

## 4. GATE 3: PENGETATAN PRODUKSI (PRODUCTION DEPLOYMENT HARDENING)

| ID | Prioritas | Temuan & Dampak | Bukti / Alasan | Langkah Tindak Lanjut | Kriteria Penyelesaian | Penanggung Jawab | Status |
|---|---|---|---|---|---|---|---|
| **ACT-09** | **P0** | `APP_DEBUG=true` aktif, berisiko mengekspos variabel lingkungan, struktur database, dan stack trace jika terjadi unhandled error. | `config/app.php` saat ini mengevaluasi `APP_DEBUG=true`. | 1. Di server produksi, set `APP_DEBUG=false` pada file `.env`.<br>2. Set `APP_ENV=production`.<br>3. Uji tampilan halaman 404 dan 500 kustom yang aman dari kebocoran info. | Error sistem menampilkan pesan ramah pengguna tanpa stack trace teknis. | DevOps / Webmaster | **PENDING EXECUTION** |
| **ACT-10** | **P0** | File database SQLite (`database.sqlite`) rentan diunduh publik jika web root web server salah terkonfigurasi. | SQLite menyimpan seluruh data dalam satu berkas flat file. | 1. Pastikan Web Root Nginx/Apache mengarah tepat ke folder `public/`, bukan root project.<br>2. Tambahkan rule blokir pada konfigurasi web server untuk ekstensi `.sqlite`, `.db`, `.env`.<br>3. Atur permission file `database.sqlite` hanya dapat dibaca/tulis oleh user `www-data` (chmod 600 / 660). | Permintaan HTTP langsung ke `http://domain/database/database.sqlite` menghasilkan 403 Forbidden atau 404 Not Found. | Security Engineer / Sysadmin | **PENDING EXECUTION** |
| **ACT-11** | **P1** | Akses via HTTP biasa mengekspos session cookie dan kredensial login pada jaringan Wi-Fi rumah sakit. | Standar keamanan data medis & fasilitas rumah sakit. | 1. Pasang sertifikat SSL/TLS (HTTPS) pada server web.<br>2. Set `SESSION_SECURE_COOKIE=true` pada `.env` produksi.<br>3. Konfigurasi HTTP redirect otomatis ke HTTPS. | Seluruh komunikasi klien-server terenkripsi dan cookie memiliki flag `Secure; HttpOnly`. | IT Network / Sysadmin | **PENDING EXECUTION** |
| **ACT-12** | **P1** | Optimalisasi cache framework Laravel untuk performa respon sub-detik di jaringan rumah sakit. | Laravel 13 best practices untuk performa produksi. | 1. Jalankan `php artisan config:cache`.<br>2. Jalankan `php artisan route:cache`.<br>3. Jalankan `php artisan view:cache`.<br>4. Jalankan `npm run build` untuk memastikan asset bundle terkompilasi optimal. | Seluruh konfigurasi dan route ter-cache; waktu respon server < 200ms. | DevOps Engineer | **PENDING EXECUTION** |

---

## 5. GATE 4: SERAH TERIMA OPERASIONAL (OPERATIONAL HANDOVER & MAINTENANCE)

| ID | Prioritas | Temuan & Dampak | Bukti / Alasan | Langkah Tindak Lanjut | Kriteria Penyelesaian | Penanggung Jawab | Status |
|---|---|---|---|---|---|---|---|
| **ACT-13** | **P0** | Belum ada backup terjadwal otomatis yang melindungi database dari kerusakan hardware server. | Bab 3 `PHASE_5_RELEASE_READINESS_REPORT.md`. | 1. Siapkan cron job harian pada OS server (misal: setiap pukul 01:00 WIB).<br>2. Skrip menyalin `database.sqlite` ke direktori backup lokal dan off-site / NAS rumah sakit.<br>3. Terapkan retensi backup selama 30 hari kalender. | File backup otomatis terbentuk setiap hari di folder penyimpanan sekunder. | IT Infrastructure | **PENDING EXECUTION** |
| **ACT-14** | **P1** | Belum pernah dilakukan uji coba pemulihan bencana (*Disaster Recovery Restore Drill*) dari file cadangan. | Rekomendasi audit keamanan & integritas data. | 1. Simulasikan skenario database corrupt di server staging.<br>2. Lakukan prosedur restore dari file backup hari sebelumnya.<br>3. Verifikasi integritas data APAR dan histori pemeriksaan setelah pemulihan. | Sistem berhasil pulih sepenuhnya dengan Recovery Time Objective (RTO) < 15 menit. | Database Admin / QA Lead | **PENDING EXECUTION** |
| **ACT-15** | **P2** | Petugas lapangan belum terbiasa dengan alur checklist 5-poin digital dan alur penanganan unit rusak. | Perubahan proses bisnis dari kartu gantung manual ke aplikasi web. | 1. Lakukan sesi sosialisasi dan pelatihan 60 menit bagi petugas K3RS.<br>2. Simulasikan alur inspeksi normal dan alur pelaporan APAR bertekanan turun / segel putus. | Seluruh petugas lapangan lulus uji mandiri simulasi inspeksi 1 unit APAR. | Koordinator K3RS | **PENDING EXECUTION** |
| **ACT-16** | **P3** | Kebutuhan panduan cepat lapangan saat petugas menemui kendala di lokasi tanpa bantuan IT. | Kemudahan operasional harian (*user enablement*). | 1. Susun lembar panduan 1 halaman (*Quick Reference Card / SOP Lapangan*).<br>2. Laminasi panduan dan letakkan di posko K3RS. | Panduan ringkas tercetak dan tersedia bagi seluruh petugas jaga. | QA Technical Writer | **PENDING EXECUTION** |

---

## 6. JADWAL & MATRIKS EKSEKUSI TAHAPAN

```
+-------------------------------------------------------------------------------+
| MINGGU 1: PRE-FIELD UAT & HARDWARE READINESS                                  |
| [ACT-01] Konfigurasi IP LAN & Aksesibilitas Smartphone                        |
| [ACT-02] Snapshot Backup Database SQLite                                      |
| [ACT-03] Cetak & Pasang Sampel Stiker QR Fisik (5 Unit)                       |
| [ACT-04] Pembuatan Kredensial Penguji Lapangan K3RS                          |
+-------------------------------------------------------------------------------+
                                    |
                                    v
+-------------------------------------------------------------------------------+
| MINGGU 2: FIELD UAT EXECUTION & ACCEPTANCE                                    |
| [ACT-05] Eksekusi 4 Skenario Fisik (UAT-B01, UAT-C03, UAT-F02, UAT-G02)      |
| [ACT-06] Validasi Scanner pada Cahaya Rendah & Refleksi                      |
| [ACT-07] Rollback / Sanitasi Data Uji Coba Lapangan                           |
| [ACT-08] Penandatanganan Berita Acara Penerimaan UAT (Sign-Off)               |
+-------------------------------------------------------------------------------+
                                    |
                                    v
+-------------------------------------------------------------------------------+
| MINGGU 3: PRODUCTION HARDENING & GO-LIVE                                      |
| [ACT-09] Pengetatan Lingkungan (APP_ENV=production, APP_DEBUG=false)          |
| [ACT-10] Proteksi File SQLite & Web Server Permission                         |
| [ACT-11] Pemasangan SSL / HTTPS & Secure Cookie Enforcing                    |
| [ACT-12] Eksekusi Build Production & Optimize Cache                           |
+-------------------------------------------------------------------------------+
                                    |
                                    v
+-------------------------------------------------------------------------------+
| MINGGU 4: OPERATIONAL HANDOVER & CONTINUOUS MONITORING                        |
| [ACT-13] Penjadwalan Backup Otomatis Harian (Cron/Task Scheduler)             |
| [ACT-14] Uji Coba Pemulihan Bencana (DR Restore Drill)                        |
| [ACT-15] Pelatihan Petugas Lapangan K3RS                                      |
| [ACT-16] Distribusi Quick Reference Card SOP Lapangan                         |
+-------------------------------------------------------------------------------+
```

---

## 7. KRITERIA KELUARAN MENUJU STATUS `READY FOR PRODUCTION`

Aplikasi HAFIS hanya dapat dinaikkan statusnya menjadi **`READY FOR PRODUCTION`** apabila seluruh syarat berikut terpenuhi secara faktual:

1. **Seluruh Item Prioritas P0 Selesai (100% Closed):**
   - ACT-01 (IP Aksesibilitas HP) -> Selesai
   - ACT-02 (Snapshot Backup) -> Selesai
   - ACT-05 (4 Skenario Fisik UAT) -> Selesai (Status: Pass)
   - ACT-08 (Berita Acara UAT Bertandatangan) -> Selesai
   - ACT-09 (`APP_DEBUG=false`) -> Selesai
   - ACT-10 (Proteksi File SQLite) -> Selesai
   - ACT-13 (Backup Otomatis Harian) -> Selesai
2. **Defect Kritis Lapangan Nol:** Tidak ada open defect dengan severity Critical (P0) atau High (P1) yang ditemukan selama sesi pengujian fisik.
3. **Persetujuan Bersama:** Ditandatanganinya lembar persetujuan rilis oleh perwakilan Instalasi K3RS dan Departemen Teknologi Informasi Rumah Sakit.

---
*Dokumen ini merupakan tindak lanjut resmi dari PHASE_5_VERIFICATION_AUDIT.md dan berlaku sebagai acuan kerja lintas tim (QA, Dev, IT Support, dan K3RS) menuju implementasi operasional penuh.*
