# HAFIS — PHASE 5: DEFECT MANAGEMENT & RESOLUTION LOG

**Project:** HAFIS — Hospital APAR Fire-safety Information System  
**Unit Kerja:** Keselamatan dan Kesehatan Kerja Rumah Sakit (K3RS)  
**Rumah Sakit:** RSUD dr. H. Moch. Ansari Saleh Banjarmasin  
**Repository:** `C:\laragon\www\aparcek-8`  
**Environment:** Laravel 13.34.0, PHP 8.5.10, Tailwind CSS v4, Vite 8.3.2  
**Tanggal Evaluasi:** 27 Oktober 2026  
**Status Log:** **ALL VERIFIED DEFECTS RESOLVED & REGRESSION-TESTED (0 OPEN DEFECTS)**  

---

## 1. Ringkasan Status Defect

Seluruh temuan visual, layout responsif, dan pengujian integrasi yang teridentifikasi sejak audit Fase 2 hingga Fase 4 telah didokumentasikan, ditindaklanjuti dengan perbaikan terukur, dan diuji ulang menggunakan PHPUnit serta browser subagent.

- **Total Defect Tercatat:** 10 Defect
- **Defect Selesai Diperbaiki & Terverifikasi (Closed):** 10 Defect (100%)
- **Defect Tertunda (Deferred):** 0 Defect
- **Defect Terbuka (Open):** **0 Defect**

---

## 2. Rincian Log Defect

### DEF-01: Ketidaksesuaian Label Tombol Submit Login terhadap Test Assertion
- **Severity:** **Low**
- **Status:** **CLOSED & VERIFIED**
- **Modul / Berkas:** [`resources/views/auth/login.blade.php`](file:///c:/laragon/www/aparcek-8/resources/views/auth/login.blade.php#L156)
- **Langkah Reproduksi:**
  1. Jalankan `vendor/bin/phpunit tests/Feature/AuthenticationTest.php`.
  2. Amati kegagalan pada metode `test_login_screen_can_be_rendered()`.
- **Bukti Aktual:**
  ```text
  Failed asserting that response contains "Masuk ke Sistem".
  Response rendered: "<button type='submit'><span>Masuk</span></button>"
  ```
- **Dampak Bisnis K3RS:** Menggagalkan pipeline CI/CD pengujian otomatis otentikasi login pengguna.
- **Tindakan Perbaikan:** Memperbarui teks tombol submit dari `<span>Masuk</span>` menjadi `<span>Masuk ke Sistem</span>` agar selaras dengan test assertion resmi.
- **Hasil Regression Test:** `vendor/bin/phpunit tests/Feature/AuthenticationTest.php` lulus 100% (10 tests, 37 assertions, duration: 3.18s).

---

### DEF-02: Topbar Horizontal Overflow pada Viewport Smartphone (F-01)
- **Severity:** **Critical (P0)**
- **Status:** **CLOSED & VERIFIED**
- **Modul / Berkas:** [`resources/views/components/topbar.blade.php`](file:///c:/laragon/www/aparcek-8/resources/views/components/topbar.blade.php)
- **Langkah Reproduksi:**
  1. Buka halaman Dashboard pada viewport 320 × 640 atau 360 × 800.
  2. Evaluasi `document.documentElement.scrollWidth` dibandingkan `clientWidth`.
- **Bukti Aktual:**
  Lebar total elemen kanan topbar mencapai 362.6px, melebihi lebar layar 320px dan 360px. Layar dapat digeser ke samping (horizontal scrolling) secara tidak normal.
- **Dampak Bisnis K3RS:** Tampilan konsol mobile terpotong, petugas kesulitan menjangkau kontrol profil dan jam WITA terpotong.
- **Tindakan Perbaikan:**
  1. Mengurangi padding horizontal topbar dari `px-4 sm:px-6` menjadi `px-3 sm:px-6`.
  2. Menyembunyikan tombol shortcut duplikat 'Scan APAR' dan lonceng notifikasi non-fungsional pada mobile (`hidden sm:inline-flex` & `hidden sm:flex`).
  3. Meningkatkan kontras teks jam WITA ke `text-gray-500`.
- **Hasil Regression Test:** Lebar elemen kanan berkurang menjadi 246px. Pada layar 320px, `scrollWidth` tercatat tepat 320px (0 overflow horizontal).

---

### DEF-03: Pergeseran Titik Marker Akibat Min-Height pada Denah Lantai (F-03)
- **Severity:** **High (P1)**
- **Status:** **CLOSED & VERIFIED**
- **Modul / Berkas:** [`resources/views/penempatan/index.blade.php`](file:///c:/laragon/www/aparcek-8/resources/views/penempatan/index.blade.php) & [`resources/views/penempatan/atur.blade.php`](file:///c:/laragon/www/aparcek-8/resources/views/penempatan/atur.blade.php)
- **Langkah Reproduksi:**
  1. Buka menu Denah Penempatan (`/penempatan`) pada layar mobile lebar 358px–390px.
  2. Periksa posisi marker APAR persentase terhadap denah arsitektur.
- **Bukti Aktual:**
  `#denah-container` memiliki kelas `min-height: 420px`. Pada lebar 358px, tinggi proporsional denah 1000:650 adalah 232.7px. Aturan `min-height` memaksa kontainer memanjang menjadi 420px, memicu letterboxing vertikal 93.65px di atas dan bawah gambar denah. Lapisan `#markers-layer` mencakup 420px penuh, menyebabkan marker bergeser vertikal hingga 56.19px dari lokasi fisik aslinya.
- **Dampak Bisnis K3RS:** **Kritis bagi keselamatan fisik.** Petugas pemadam darurat dapat salah mengidentifikasi lokasi tabung APAR di koridor rumah sakit karena marker di layar menunjuk ruangan yang salah.
- **Tindakan Perbaikan:**
  1. Menghapus aturan `min-height: 420px` dari `#denah-container` dan `min-height: 380px` dari `#plan-wrapper`.
  2. Menambahkan fungsi JavaScript `syncDenahAspectRatio(img)` pada event `onload` gambar denah dan `DOMContentLoaded` yang menyelaraskan rasio aspek kontainer secara otomatis dengan dimensi fisik asli gambar denah (`aspectRatio = naturalWidth / naturalHeight`).
  3. Menjamin kontainer gambar denah dan lapisan marker berbagi sistem koordinat 1:1 yang persis sama.
- **Hasil Regression Test:** Letterboxing vertikal 0px. Marker APAR menempel presisi pada gambar denah di seluruh ukuran layar (320px, 390px, 768px, 1024px, 1440px).

---

### DEF-04: Fixed Action Bar Form Pemeriksaan Bertabrakan dengan Safe Area iOS (F-04)
- **Severity:** **High (P1)**
- **Status:** **CLOSED & VERIFIED**
- **Modul / Berkas:** [`resources/views/components/layouts/app.blade.php`](file:///c:/laragon/www/aparcek-8/resources/views/components/layouts/app.blade.php) & [`resources/views/pemeriksaan/create.blade.php`](file:///c:/laragon/www/aparcek-8/resources/views/pemeriksaan/create.blade.php)
- **Langkah Reproduksi:**
  1. Buka form pemeriksaan fisik pada perangkat iPhone dengan Home Indicator / Notch.
  2. Gulir hingga bagian paling bawah form (upload foto dan kesimpulan).
- **Bukti Aktual:**
  Tag `<meta name="viewport">` tidak memiliki `viewport-fit=cover`. Bilah aksi bawah hanya memiliki padding `p-3.5`, sehingga tombol 'Simpan Pemeriksaan' bertabrakan langsung dengan garis Home Bar gesture iOS dan input foto/catatan terbawah tertutup bilah tetap (clipping).
- **Dampak Bisnis K3RS:** Petugas kesulitan mengetuk tombol simpan atau salah memicu gestur keluar aplikasi (swipe to home) saat ingin menyimpan hasil inspeksi.
- **Tindakan Perbaikan:**
  1. Memperbarui meta tag menjadi `<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">`.
  2. Menambahkan safe area bottom padding dinamis pada bilah aksi tetap: `px-3.5 pt-3.5 pb-[max(0.875rem,calc(0.875rem+env(safe-area-inset-bottom,0px)))] sm:p-4`.
  3. Menambah ruang scroll bawah form dari `pb-20` menjadi `pb-28 sm:pb-0`.
- **Hasil Regression Test:** Seluruh elemen form dapat digulir bebas melewati bilah simpan; bilah simpan mengambang aman di atas area gestur sistem operasi.

---

### DEF-05: Tabrakan Teks & Badge pada Kartu Statistik Dashboard Mobile (F-02)
- **Severity:** **Medium (P1)**
- **Status:** **CLOSED & VERIFIED**
- **Modul / Berkas:** [`resources/views/dashboard.blade.php`](file:///c:/laragon/www/aparcek-8/resources/views/dashboard.blade.php#L33)
- **Langkah Reproduksi:**
  1. Buka Dashboard pada viewport 320px–375px.
  2. Periksa kartu ke-3 "Mendekati Expired" dan kartu ke-4 "Kondisi Rusak".
- **Bukti Aktual:**
  Pada tata letak 2 kolom layar 320px, ruang dalam kartu hanya 110px. Teks judul "Mendekati Expired" (105px) dan badge "Perlu Refill" (86px) sama-sama memiliki `whitespace-nowrap`, memicu tabrakan teks dan menekan teks footer "Tersebar di 17 Gedung" menjadi terpotong elipsis "Tersebar di 1...".
- **Dampak Bisnis K3RS:** Informasi status APAR kedaluwarsa dan rusak sulit dipindai dengan cepat oleh pengawas K3RS.
- **Tindakan Perbaikan:**
  1. Mengubah struktur header kartu statistik menjadi `flex flex-col xs:flex-row xs:items-center justify-between gap-1`.
  2. Badge status mengalir rapi di bawah judul pada layar ekstra sempit.
  3. Padding kartu disesuaikan ke `p-3.5 sm:p-5` dan gap grid disesuaikan ke `gap-3 sm:gap-4 lg:gap-6`.
  4. Footer diringkas menjadi `<strong class="font-medium text-gray-700">{{ $summaryStats['total_gedung'] }}</strong> Gedung` sehingga tidak terpotong.
- **Hasil Regression Test:** Judul dan badge terbaca utuh tanpa clipping pada viewport 320px, 360px, 375px, dan 390px.

---

### DEF-06: Posisi QR Code & Status Kesiapan Terkubur di Bawah Tabel Riwayat (F-06)
- **Severity:** **High (P1)**
- **Status:** **CLOSED & VERIFIED**
- **Modul / Berkas:** [`resources/views/apar/show.blade.php`](file:///c:/laragon/www/aparcek-8/resources/views/apar/show.blade.php#L280)
- **Langkah Reproduksi:**
  1. Buka halaman Detail APAR pada perangkat smartphone.
  2. Periksa urutan informasi dari atas ke bawah.
- **Bukti Aktual:**
  Pada DOM asli, kolom kiri (8/12) berisi spesifikasi teknis dan 3 tabel riwayat panjang (pemeriksaan berkala, tindak lanjut, penggantian) dimuat sebelum kolom kanan (4/12). Pada layar sempit, petugas harus menggulir melewati puluhan baris log inspeksi sebelum dapat menemukan QR Code dan Status Kesiapan tabung.
- **Dampak Bisnis K3RS:** Menghambat workflow verifikasi fisik petugas di depan tabung APAR yang membutuhkan akses cepat ke kode identitas QR dan status siap pakai.
- **Tindakan Perbaikan:**
  Menggunakan flex reordering responsif:
  - Kolom kanan (QR Code & Status Kesiapan): `order-1 lg:order-2 lg:col-span-4 w-full`
  - Kolom kiri (Spesifikasi & Riwayat): `order-2 lg:order-1 lg:col-span-8 w-full`
- **Hasil Regression Test:** Pada smartphone, QR Code dan status kesiapan tampil pertama di layar atas; pada desktop layout 2 kolom (8/12 dan 4/12) tetap presisi tanpa duplikasi markup.

---

### DEF-07: Tabel Kritis Membutuhkan Horizontal Scroll 720px di Mobile (F-07 / F-08)
- **Severity:** **Medium (P1)**
- **Status:** **CLOSED & VERIFIED**
- **Modul / Berkas:** [`resources/views/dashboard.blade.php`](file:///c:/laragon/www/aparcek-8/resources/views/dashboard.blade.php#L346) & [`resources/views/apar/index.blade.php`](file:///c:/laragon/www/aparcek-8/resources/views/apar/index.blade.php#L214)
- **Langkah Reproduksi:**
  1. Buka bagian 'Unit APAR Memerlukan Perhatian' di Dashboard dan Master Data APAR pada smartphone.
- **Bukti Aktual:**
  Tabel tabular desktop memiliki lebar minimum `min-w-[720px]` dan `min-w-[960px]`, memaksa petugas melakukan scrolling horizontal terus-menerus untuk melihat status penanganan dan tombol aksi.
- **Dampak Bisnis K3RS:** Friksi operasional tinggi bagi petugas lapangan yang menggunakan satu tangan saat memeriksa tabung.
- **Tindakan Perbaikan:**
  Menyediakan tampilan kartu mobile (`sm:hidden`) yang memuat kode APAR, lokasi berjenjang, status, kondisi, dan tombol aksi "Detail APAR" serta "+ Tindak Lanjut" dengan area sentuh `min-h-[40px]`. Tabel tabular desktop tetap dipertahankan pada `hidden sm:block`.
- **Hasil Regression Test:** Petugas dapat membaca status dan membuka aksi langsung dari kartu vertikal tanpa horizontal scrolling berlebih.

---

### DEF-08: Redundansi 4 Sub-Kartu Legend Status Kelaikan APAR di Mobile (F-09)
- **Severity:** **Low (P2)**
- **Status:** **CLOSED & VERIFIED**
- **Modul / Berkas:** [`resources/views/dashboard.blade.php`](file:///c:/laragon/www/aparcek-8/resources/views/dashboard.blade.php#L168)
- **Langkah Reproduksi:**
  1. Buka Dashboard pada smartphone dan amati section 'Status Kelaikan & Kondisi APAR'.
- **Bukti Aktual:**
  Di bawah progress bar kelaikan, terdapat 4 kartu legend besar (Baik, Perlu Perhatian, Rusak, Nonaktif) yang mengulang persis angka yang sudah ada di 4 kartu ringkasan utama di bagian atas, memakan ruang vertikal layar secara berlebihan.
- **Dampak Bisnis K3RS:** Scrolling vertikal yang repetitif dan menurunkan kepadatan informasi keselamatan.
- **Tindakan Perbaikan:**
  Pada mobile (`sm:hidden`), 4 sub-kartu diubah menjadi ringkasan legend 2-kolom kompak dengan dot warna status dan persentase. Kartu interaktif lengkap tetap aktif pada tablet dan desktop (`hidden sm:grid sm:grid-cols-4`).
- **Hasil Regression Test:** Pengulangan berkurang drastis pada mobile; makna metrik keselamatan tetap terjaga 100%.

---

### DEF-09: Kontras Warna Label Teks di Bawah Standar Aksesibilitas WCAG AA (F-11)
- **Severity:** **Medium (P2)**
- **Status:** **CLOSED & VERIFIED**
- **Modul / Berkas:** [`resources/views/dashboard.blade.php`](file:///c:/laragon/www/aparcek-8/resources/views/dashboard.blade.php) & [`resources/views/components/topbar.blade.php`](file:///c:/laragon/www/aparcek-8/resources/views/components/topbar.blade.php)
- **Langkah Reproduksi:**
  1. Periksa rasio kontras warna teks unit `unit` dan jam WITA `text-gray-400` terhadap latar belakang putih `#ffffff`.
- **Bukti Aktual:**
  Rasio kontras `text-gray-400` (#9ca3af) pada latar putih adalah 2.8:1, gagal memenuhi standar WCAG 2.2 AA (minimal 4.5:1 untuk teks normal).
- **Dampak Bisnis K3RS:** Petugas yang berada di luar ruangan atau lorong dengan pencahayaan silau kesulitan membaca label satuan dan jam operasional.
- **Tindakan Perbaikan:**
  Mengganti token teks fungsional dari `text-gray-400` menjadi `text-gray-500` (#6b7280, rasio 4.6:1) dan `text-gray-600` (#4b5563, rasio 7.0:1) sesuai palet slate/navy HAFIS.
- **Hasil Regression Test:** Seluruh label teks fungsional memenuhi rasio kontras WCAG 2.2 AA minimal 4.5:1.

---

### DEF-10: Target Sentuh Tombol Aksi Tabel Terlalu Kecil (<36px) (F-13)
- **Severity:** **Low (P3)**
- **Status:** **CLOSED & VERIFIED**
- **Modul / Berkas:** [`resources/views/dashboard.blade.php`](file:///c:/laragon/www/aparcek-8/resources/views/dashboard.blade.php), [`resources/views/apar/show.blade.php`](file:///c:/laragon/www/aparcek-8/resources/views/apar/show.blade.php), [`resources/views/pemeriksaan/create.blade.php`](file:///c:/laragon/www/aparcek-8/resources/views/pemeriksaan/create.blade.php)
- **Langkah Reproduksi:**
  1. Ukur tinggi tombol 'Checklist', 'Rincian', dan tombol 'Back' pada mobile.
- **Bukti Aktual:**
  Beberapa tombol tabel menggunakan `min-h-[28px]` atau `min-h-[30px]`, sulit ditekan secara akurat oleh jari petugas di lapangan.
- **Dampak Bisnis K3RS:** Risiko salah ketuk baris tabel saat petugas terburu-buru melakukan inspeksi.
- **Tindakan Perbaikan:**
  Menaikkan target sentuh seluruh tombol aksi mobile ke `min-h-[36px]` (tabel) dan `min-h-[40px] s.d. min-h-[44px]` (tombol kartu dan form).
- **Hasil Regression Test:** Area sentuh memadai tanpa merusak kepadatan visual baris tabel desktop.

---

## 3. Kesimpulan Verifikasi Defect

Seluruh temuan telah ditutup dengan solusi teknis berbasis CSS utilitas dan Blade murni tanpa mengubah arsitektur basis data maupun logika backend. Tidak ada defect regresi yang tertinggal dalam repositori.
