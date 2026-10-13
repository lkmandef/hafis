# LOCATION_APAR_MASTER.md

## 1. Tujuan Dokumen

Dokumen ini adalah **master referensi data lokasi dan APAR HAFIS** berdasarkan data aktual yang diperoleh dari unit K3RS RSUD dr. H. Moch. Ansari Saleh.

Dokumen ini menjadi sumber acuan sebelum melakukan:

- pembuatan master lokasi;
- pembuatan data APAR aktual;
- pembuatan kode APAR;
- pembuatan QR Code;
- migration/seeder;
- redesign pemeriksaan bulanan;
- dashboard;
- laporan;
- pengembangan fitur denah/penempatan.

**Penting:** Jangan membuat atau mengubah data berdasarkan asumsi developer/AI apabila belum dikonfirmasi kepada K3RS.

---

# 2. Prinsip Struktur Lokasi

HAFIS **tidak memaksakan struktur hierarkis**:

```text
Gedung
└── Lantai
    └── Ruangan
        └── APAR
```

Data K3RS menunjukkan bahwa lokasi APAR terdiri dari berbagai jenis area, antara lain:

- gedung/lantai;
- instalasi;
- ruangan;
- area pelayanan;
- koridor/area luar ruangan;
- area utilitas;
- gudang;
- area khusus.

Karena itu struktur domain HAFIS menggunakan konsep sederhana:

```text
Lokasi
└── APAR
    └── Pemeriksaan Bulanan
```

Setiap APAR memiliki satu `lokasi_id`.

`Lokasi` tidak wajib dikategorikan sebagai Gedung, Lantai, atau Ruangan.

---

# 3. Aturan Nama Lokasi

Nama lokasi utama harus mengikuti data K3RS sedekat mungkin.

Contoh:

```text
EMERALD Lt 1
EMERALD Lt 2
IT DALAM
IT LUAR
IPSRS
GENZET
LIFT
GAS MEDIS (BELAKANG CSSD)
```

Jangan mengubah nama menjadi struktur baru seperti:

```text
Gedung Emerald
Lantai 1
Ruangan IT
```

kecuali K3RS secara resmi memberikan struktur tersebut.

Jika diperlukan informasi tambahan, gunakan field `keterangan`, bukan mengubah nama lokasi utama.

Contoh:

```text
Nama lokasi : IT LUAR
Keterangan  : Area luar ruang IT / koridor
```

---

# 4. Status Data Lokasi

Gunakan konsep:

```text
aktif
kosong
```

Lokasi dengan jumlah APAR `0` tetap boleh menjadi master lokasi, tetapi **tidak boleh dibuatkan APAR dummy**.

Contoh:

```text
JAMRUD → 0 APAR
YAKUT  → 0 APAR
GIOK   → 0 APAR
KANTIN → 0 APAR
```

---

# 5. Master Lokasi dan Jumlah APAR

| No | Nama Lokasi K3RS | Jumlah | Status | Candidate Code | Catatan |
|---:|---|---:|---|---|---|
| 1 | POS PANTAU SECURITY | 1 | aktif | TBD | Perlu finalisasi kode |
| 2 | IGD Lt. 1 | 4 | aktif | TBD | Nama mengikuti data K3RS |
| 3 | STROKE CENTER | 2 | aktif | TBD | Perlu finalisasi kode |
| 4 | IRJA Lt. 1 | 6 | aktif | TBD | Nama mengikuti data K3RS |
| 5 | KASIR | 1 | aktif | TBD | Perlu finalisasi kode |
| 6 | REKAM MEDIS | 3 | aktif | TBD | Perlu finalisasi kode |
| 7 | FISIOTERAPI | 1 | aktif | TBD | Perlu finalisasi kode |
| 8 | IRJA LT 2 | 2 | aktif | TBD | Perlu finalisasi kode |
| 9 | KOMITE MEDIK | 1 | aktif | TBD | Perlu finalisasi kode |
| 10 | AUDITORIUM | 2 | aktif | TBD | Perlu finalisasi kode |
| 11 | MANAJEMEN + Rg ARSIP KEPEGAWAIAN | 4 | aktif | TBD | Nama cukup dipertahankan sebagai lokasi gabungan |
| 12 | INSTALASI FARMASI | 5 | aktif | TBD | Perlu finalisasi kode |
| 13 | EMERALD Lt 1 | 5 | aktif | EMR1? | Candidate only |
| 14 | EMERALD Lt 2 | 4 | aktif | EMR2? | Candidate only |
| 15 | EMERALD Lt 3 | 4 | aktif | EMR3? | Candidate only |
| 16 | EMERALD Lt 4 | 5 | aktif | EMR4? | Candidate only |
| 17 | EMERALD Lt 5 | 6 | aktif | EMR5? | Candidate only |
| 18 | NILAM Lt 1 | 4 | aktif | TBD | Perlu finalisasi kode |
| 19 | NILAM Lt 2 | 4 | aktif | TBD | Perlu finalisasi kode |
| 20 | NILAM Lt 3 | 4 | aktif | TBD | Perlu finalisasi kode |
| 21 | DEPO RAWAT INAP | 1 | aktif | TBD | Perlu finalisasi kode |
| 22 | ALEXANDRI Lt 1 | 5 | aktif | TBD | Perlu finalisasi kode |
| 23 | ALEXANDRI Lt 2 | 5 | aktif | TBD | Perlu finalisasi kode |
| 24 | ALEXANDRI Lt 3 | 5 | aktif | TBD | Perlu finalisasi kode |
| 25 | ALEXANDRI Lt 4 | 5 | aktif | TBD | Perlu finalisasi kode |
| 26 | JAMRUD | 0 | kosong | TBD | Jangan membuat APAR dummy |
| 27 | YAKUT | 0 | kosong | TBD | Jangan membuat APAR dummy |
| 28 | GIOK | 0 | kosong | TBD | Jangan membuat APAR dummy |
| 29 | POLI JIWA DAN PSIKOLOGI | 3 | aktif | TBD | Perlu finalisasi kode |
| 30 | KUMALA Lt 1 | 2 | aktif | TBD | Perlu finalisasi kode |
| 31 | KUMALA Lt 2 | 2 | aktif | TBD | Perlu finalisasi kode |
| 32 | KUMALA Lt 3 | 2 | aktif | TBD | Perlu finalisasi kode |
| 33 | KUMALA Lt 4 (OK) s.d. Gedung Mutiara Lt 4 | 6 | aktif | TBD | **Ambigu; jangan dipecah tanpa konfirmasi K3RS** |
| 34 | DEPO OK | 1 | aktif | TBD | Perlu finalisasi kode |
| 35 | KUMALA Lt 5 | 2 | aktif | TBD | Perlu finalisasi kode |
| 36 | POLI ESWL | 1 | aktif | TBD | Perlu konfirmasi klasifikasi lokasi |
| 37 | RADIOLOGI | 5 | aktif | TBD | Perlu konfirmasi klasifikasi lokasi |
| 38 | LABORATORIUM | 3 | aktif | TBD | Perlu konfirmasi klasifikasi lokasi |
| 39 | MUTIARA Lt 1 HD | 7 | aktif | TBD | Nama mengikuti data K3RS |
| 40 | MUTIARA Lt 2 Bayi | 4 | aktif | TBD | Nama mengikuti data K3RS |
| 41 | MUTIARA Lt 3 ICU | 4 | aktif | TBD | Nama mengikuti data K3RS |
| 42 | LAUNDRY LT 1 | 4 | aktif | TBD | Perlu finalisasi kode |
| 43 | CSSD LT 2 | 3 | aktif | TBD | Candidate CSD2? |
| 44 | BURN CENTER | 1 | aktif | TBD | Perlu konfirmasi klasifikasi lokasi |
| 45 | INSTALASI GIZI LT 1 | 5 | aktif | GIZ1? | Candidate only |
| 46 | INSTALASI GIZI LT 2 | 1 | aktif | GIZ2? | Candidate only |
| 47 | IT DALAM | 1 | aktif | ITD | Area dalam ruang IT |
| 48 | IT LUAR | 1 | aktif | ITL | Area luar ruang IT / koridor |
| 49 | GUDANG LIMBAH B3 | 1 | aktif | TBD | Perlu finalisasi kode |
| 50 | C S FREEZEER (SANITASI) | 1 | aktif | TBD | Nama perlu dipertahankan sesuai sumber sampai dikonfirmasi |
| 51 | INSTALASI SANITASI | 1 | aktif | TBD | Perlu finalisasi kode |
| 52 | INCENERATOR | 3 | aktif | TBD | Perlu finalisasi kode |
| 53 | GUDANG LOGISTIK | 2 | aktif | TBD | Perlu finalisasi kode |
| 54 | K3RS | 1 | aktif | TBD | Perlu finalisasi kode |
| 55 | IPSRS | 1 | aktif | TBD | Perlu finalisasi kode |
| 56 | GENZET | 2 | aktif | TBD | Perlu finalisasi kode |
| 57 | KAMAR JENAZAH | 1 | aktif | TBD | Perlu finalisasi kode |
| 58 | LIFT | 8 | aktif | TBD | Perlu konfirmasi konteks lokasi |
| 59 | KANTIN | 0 | kosong | TBD | Jangan membuat APAR dummy |
| 60 | RESEPCIONIST Lt 2 | 1 | aktif | TBD | Perlu konfirmasi gedung/area |
| 61 | GAS MEDIS (BELAKANG CSSD) | 1 | aktif | TBD | Area utilitas |
| 62 | 119 | 1 | aktif | TBD | Perlu konfirmasi arti/nama area |
| 63 | GUDANG K3RS | 64 | stok | STOCK | **64 unit STOK SEDIAAN, bukan otomatis APAR terpasang** |
| 64 | OK CITO | 2 | aktif | TBD | Perlu finalisasi kode |

---

# 6. APAR Terpasang vs Stok Sediaan

Data K3RS mencatat:

```text
GUDANG K3RS
64
STOK SEDIAAN
```

Angka `64` **tidak boleh langsung dianggap sebagai 64 APAR terpasang**.

HAFIS harus membedakan:

```text
APAR TERPASANG
└── Digunakan di lokasi
└── Wajib mengikuti pemeriksaan bulanan

STOK SEDIAAN
└── Persediaan
└── Belum otomatis masuk pemeriksaan APAR terpasang
```

Status final 64 unit stok harus dikonfirmasi dengan K3RS:

**Apakah 64 unit stok juga perlu memiliki QR dan pemeriksaan bulanan, atau hanya menjadi data stok/persediaan?**

Sebelum ada jawaban, jangan membuat 64 record APAR aktif.

---

# 7. Aturan Kode APAR

Format target:

```text
APAR-{KODE_LOKASI}-{NO_URUT}
```

Contoh:

```text
APAR-ITD-01
APAR-ITL-01
```

Untuk lokasi bertingkat, candidate format:

```text
EMERALD LT 1 → APAR-EMR1-01
EMERALD LT 2 → APAR-EMR2-01
EMERALD LT 3 → APAR-EMR3-01
```

Namun kode tersebut masih berstatus:

```text
CANDIDATE
```

dan **belum menjadi kode resmi** sampai aturan penamaan disetujui.

Nomor urut dimulai dari `01` pada setiap kode lokasi.

Contoh:

```text
EMR1:
APAR-EMR1-01
APAR-EMR1-02
APAR-EMR1-03
APAR-EMR1-04
APAR-EMR1-05
```

Jangan menggunakan nomor register lama sebagai bagian kode APAR.

---

# 8. Kandidat Kode Lokasi yang Sudah Jelas

Beberapa lokasi yang relatif jelas dapat menggunakan candidate code berikut:

| Lokasi | Candidate Code | Status |
|---|---|---|
| EMERALD Lt 1 | EMR1 | Candidate |
| EMERALD Lt 2 | EMR2 | Candidate |
| EMERALD Lt 3 | EMR3 | Candidate |
| EMERALD Lt 4 | EMR4 | Candidate |
| EMERALD Lt 5 | EMR5 | Candidate |
| IT DALAM | ITD | Candidate |
| IT LUAR | ITL | Candidate |
| INSTALASI GIZI LT 1 | GIZ1 | Candidate |
| INSTALASI GIZI LT 2 | GIZ2 | Candidate |
| CSSD LT 2 | CSD2 | Candidate |
| GUDANG K3RS | STOCK | Khusus stok, bukan APAR terpasang |

Semua kode lainnya tetap `TBD` sampai aturan prefix disepakati.

---

# 9. Lokasi yang Tidak Boleh Diinterpretasikan oleh AI

AI/developer **tidak boleh mengambil keputusan sendiri** terhadap lokasi berikut:

```text
DEPO OK
POLI ESWL
RADIOLOGI
LABORATORIUM
BURN CENTER
IT DALAM
IT LUAR
K3RS
GUDANG K3RS
119
RESEPCIONIST Lt 2
LIFT
KUMALA Lt 4 (OK) s.d. Gedung Mutiara Lt 4
```

Jika struktur tambahan dibutuhkan, tanyakan kepada K3RS.

Contoh:

```text
IT LUAR
```

jangan otomatis diubah menjadi:

```text
Gedung IT
Lantai X
Ruangan IT Luar
```

Karena informasi yang tersedia hanya menyatakan bahwa lokasi tersebut adalah `IT LUAR`.

---

# 10. Lokasi Nol APAR

Lokasi berikut tetap boleh disimpan sebagai master lokasi:

```text
JAMRUD
YAKUT
GIOK
KANTIN
```

Jumlah APAR:

```text
0
```

Tidak boleh dibuatkan:

```text
APAR-JMR-01
APAR-YKT-01
APAR-GIO-01
APAR-KTN-01
```

sampai K3RS menyatakan memang ada APAR.

---

# 11. Aturan Pemeriksaan Bulanan

Setiap APAR terpasang akan memiliki pemeriksaan berdasarkan:

```text
APAR
+
TAHUN
+
BULAN
```

Contoh:

```text
APAR-EMR1-01
    ├── Januari 2026
    ├── Februari 2026
    ├── Maret 2026
    ├── April 2026
    ├── Mei 2026
    ├── Juni 2026
    ├── Juli 2026
    ├── Agustus 2026
    ├── September 2026
    ├── Oktober 2026
    ├── November 2026
    └── Desember 2026
```

Pemeriksaan menggunakan lima parameter K3RS:

1. Tekanan
2. Pin/Segel
3. Handle
4. Kondisi Fisik
5. Nozel

Ditambah:

```text
Tanggal pemeriksaan
Pemeriksa
Paraf
Hasil keseluruhan
Catatan
Foto (opsional)
```

---

# 12. Konsep Hasil Inspeksi

Menu operasional:

```text
Hasil Inspeksi
```

bukan:

```text
Jadwal Inspeksi
```

Tidak diperlukan admin untuk membuat jadwal pemeriksaan bulanan secara manual.

Sistem menentukan periode berdasarkan:

```text
tahun + bulan
```

Ketika APAR dipindai:

```text
Belum diperiksa bulan ini
        ↓
Form pemeriksaan bulan berjalan

Sudah diperiksa bulan ini
        ↓
Hasil pemeriksaan bulan berjalan
```

---

# 13. Status APAR

Status APAR dan hasil pemeriksaan harus tetap dibedakan.

Contoh status APAR:

```text
Baik
Perlu Perhatian
Rusak / Perlu Tindakan
Nonaktif
Sudah Diganti
```

Hasil pemeriksaan bulanan:

```text
Baik
Perlu Tindakan
```

Jangan membuat status baru hanya karena ada variasi penulisan di data sumber.

---

# 14. Nomor Register

K3RS menyatakan bahwa `Nomor Register` tidak diperlukan untuk operasional HAFIS.

Konsekuensi target:

- jangan tampilkan Nomor Register pada form utama;
- jangan tampilkan Nomor Register pada detail APAR;
- jangan tampilkan Nomor Register pada laporan utama;
- jangan gunakan Nomor Register sebagai identitas APAR;
- jangan gunakan Nomor Register sebagai kode QR.

Namun field/database lama **jangan langsung dihapus** sebelum seluruh dependency aplikasi diaudit dan dibersihkan.

---

# 15. Menu Pergantian APAR

K3RS tidak membutuhkan menu operasional `Pergantian APAR`.

Target UX:

```text
Menu utama
├── Dashboard
├── Data APAR
├── Hasil Inspeksi
├── Scan Cek
├── Riwayat Pemeriksaan
└── Laporan
```

Fitur pergantian APAR:

- tidak ditampilkan sebagai menu utama;
- backend/data tetap dipertahankan;
- laporan penggantian tetap dipertahankan untuk kebutuhan akademik/PKL;
- jangan menghapus tabel atau controller sebelum dependency selesai diaudit.

---

# 16. Aturan Penting untuk Seeder & Data Master

Seeder APAR aktual dan data master harus:

1. Menggunakan data lokasi dari dokumen ini dan rincian media/kapasitas dari Tabel 12 *LAPORAN TRIWULAN KE-3 2026*:
   - Total 173 APAR terpasang: 115 Dry Chemical Powder, 58 CO2 (Carbon Dioxide).
   - Rincian kapasitas Powder: 25 kg (3 unit), 6 kg (108 unit), 3 kg (4 unit).
   - Rincian kapasitas CO2: 7 kg (17 unit), 5 kg (7 unit), 3 kg (34 unit).
2. Tidak membuat data dummy.
3. Tidak membuat APAR untuk lokasi dengan jumlah `0` (JAMRUD, YAKUT, GIOK, KANTIN).
4. Tidak memasukkan 64 unit stok di Gudang K3RS sebagai APAR terpasang aktif (stok sediaan adalah metadata logistik, bukan unit terpasang).
5. Menghasilkan kode APAR secara deterministik (`APAR-{KODE_LOKASI}-{URUT}`).
6. Menjamin kode APAR unik.
7. Menjamin QR unik.
8. Tidak menggunakan `nomor_register` sebagai identitas baru.
9. Tidak mengarang gedung/lantai/ruangan.
10. Tidak mengubah nama lokasi tanpa dasar data K3RS.
11. **Merek APAR**: Dokumen sumber asli tidak mencantumkan merek APAR maupun nomor seri fisik per unit. Merek disimpan sebagai `NULL` di database dan ditampilkan sebagai label `Belum terdata` pada antarmuka pengguna. Dilarang mengasumsikan merek tertentu (seperti Servvo) tanpa bukti fisik.
12. **Identitas Kode APAR**: Kode seperti `APAR-EMR1-01` adalah identitas administratif yang di-generate oleh sistem HAFIS untuk penelusuran digital dan penempelan QR, bukan nomor seri fisik resmi dari pabrikan.
13. **Batasan Alokasi Per Record**: Karena dokumen sumber hanya menyajikan data agregat per lokasi dan kategori kapasitas/media, pengaitan kombinasi media/kapasitas ke nomor urut APAR pada lokasi multi-kategori merupakan **alokasi administratif deterministik**, bukan verifikasi nomor seri fisik individual per tabung.

---

# 17. Hal yang Masih Membutuhkan Konfirmasi K3RS

Sebelum implementasi data final, konfirmasi minimal:

### A. 64 unit STOK SEDIAAN

Apakah:

```text
64 unit
```

merupakan:

- stok APAR yang belum terpasang; atau
- APAR yang memang berada di Gudang K3RS dan tetap harus diperiksa bulanan?

### B. KUMALA Lt 4

Data:

```text
KUMALA Lt 4 (OK) s.d. Gedung Mutiara Lt 4
Jumlah: 6
```

Apakah ini memang satu lokasi dalam pencatatan K3RS, atau sebenarnya terdiri dari dua lokasi?

### C. IT DALAM / IT LUAR

Konfirmasi:

```text
IT DALAM = ruang IT
IT LUAR  = area luar ruang IT / koridor
```

Jika benar, HAFIS cukup menggunakan keduanya sebagai dua master lokasi tanpa struktur Gedung/Lantai/Ruangan.

### D. RESEPCIONIST Lt 2

Gedung/area mana yang dimaksud?

### E. 119

Apa nama/area resmi lokasi `119`?

### F. LIFT

Apakah 8 APAR tersebut memang dicatat sebagai satu lokasi `LIFT`, atau sebenarnya tersebar pada beberapa area/lantai?

### G. Kode lokasi

Konfirmasi aturan kode seperti:

```text
EMR1
EMR2
EMR3
ITD
ITL
GIZ1
GIZ2
CSD2
```

atau apakah K3RS memiliki kode resmi sendiri.

---

# 18. Status Dokumen

Status saat ini:

```text
DATA DISCOVERY / PRE-IMPLEMENTATION
```

Dokumen ini **belum menjadi instruksi untuk mengubah database**.

Sebelum migration/seeder dijalankan:

```text
LOCATION_APAR_MASTER.md
        ↓
Konfirmasi K3RS
        ↓
Finalisasi kode lokasi
        ↓
Finalisasi jumlah APAR
        ↓
Finalisasi status stok
        ↓
SYSTEM_BLUEPRINT.md
        ↓
Migration + Seeder
```

---

# 19. Prinsip Utama

> **HAFIS harus mengikuti cara K3RS mengelola lokasi APAR, bukan memaksa data K3RS mengikuti struktur database lama HAFIS.**

Jika informasi tidak tersedia:

```text
JANGAN MENEBak.
JANGAN MEMBUAT STRUKTUR PALSU.
TANDAI SEBAGAI TBD.
KONFIRMASI KE K3RS.
```