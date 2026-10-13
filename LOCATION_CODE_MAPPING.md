# LOCATION_CODE_MAPPING.md — HAFIS Master Location Code Nomenclature

## 1. Konteks dan Tujuan

Dokumen ini mendokumentasikan pemetaan kode lokasi definitif untuk seluruh **64 lokasi master K3RS RSUD dr. H. Moch. Ansari Saleh Banjarmasin** pada sistem HAFIS.

Kode lokasi ini digunakan sebagai segmen dasar pembentukan kode unik APAR fisik:
```text
APAR-{KODE_LOKASI}-{NOMOR_URUT}
```
Contoh:
- `APAR-SEC-01`
- `APAR-IGD1-01`
- `APAR-EMR1-01`
- `APAR-ITD-01`

Payload QR Code resmi mengikuti format terstandarisasi:
```text
HAFIS-QR-APAR-{KODE_LOKASI}-{NOMOR_URUT}
```
Contoh:
- `HAFIS-QR-APAR-SEC-01`
- `HAFIS-QR-APAR-EMR1-01`

---

## 2. Prinsip Penamaan dan Resolusi Kolisi

1. **Determinisme dan Stabilitas:**
   - Seluruh kode lokasi bersifat statis dan konsisten antar-eksekusi seeder.
   - Tidak ada kode yang dihasilkan secara acak atau bergantung pada urutan query dinamis.
2. **Keterbacaan dan Singkatan Lazim:**
   - Menggunakan singkatan medis/rumah sakit yang mudah dikenali oleh petugas K3RS dan teknisi lapangan (contoh: `IGD`, `IRJ`, `RM`, `CSD`, `GIZ`, `ICU`, `HD`, `LB3`).
3. **Pembeda Bertingkat (Multi-Floor Differentiation):**
   - Lokasi bertingkat pada gedung yang sama dibedakan secara eksplisit dengan sufiks nomor lantai (contoh: Gedung Emerald: `EMR1` s.d. `EMR5`, Gedung Nilam: `NLM1` s.d. `NLM3`, Gedung Alexandri: `ALX1` s.d. `ALX4`, Gedung Kumala: `KML1` s.d. `KML5`).
4. **Resolusi Kolisi Nama Mirip:**
   - `IRJA Lt. 1` (`IRJ1`) vs `IRJA LT 2` (`IRJ2`).
   - `INSTALASI GIZI LT 1` (`GIZ1`) vs `INSTALASI GIZI LT 2` (`GIZ2`).
   - `IT DALAM` (`ITD`) vs `IT LUAR` (`ITL`).
   - `DEPO OK` (`DOK`) vs `DEPO RAWAT INAP` (`DRI`).
   - `MUTIARA Lt 1 HD` (`MTR1`), `MUTIARA Lt 2 Bayi` (`MTR2`), `MUTIARA Lt 3 ICU` (`MTR3`).
   - `INSTALASI FARMASI` (`FAR`) vs `FISIOTERAPI` (`FIS`).
   - `INSTALASI SANITASI` (`SAN`) vs `C S FREEZEER (SANITASI)` (`CSF`).
   - `K3RS` (`K3RS`) vs `GUDANG K3RS` (`GK3`).
5. **Lokasi Ambigu & Khusus:**
   - `119`: Pos/Layanan Darurat 119 menggunakan kode `119` (`APAR-119-01`).
   - `KUMALA Lt 4 (OK) s.d. Gedung Mutiara Lt 4`: Lokasi gabungan transisi dipertahankan utuh dengan kode `KML4` (6 unit APAR).
   - `GUDANG K3RS`: Lokasi stok persediaan 64 unit menggunakan kode `GK3` (status `stok`, 0 unit APAR terpasang operasional).
   - Lokasi Kosong (0 APAR): `JAMRUD` (`JMR`), `YAKUT` (`YKT`), `GIOK` (`GIO`), `KANTIN` (`KTN`) terdaftar di master tanpa record APAR dummy.

---

## 3. Tabel Pemetaan Lengkap 64 Lokasi

| ID | Nama Lokasi K3RS | Kode Lokasi | Target APAR | Status | Alasan Singkatan & Catatan Resolusi Kolisi |
|---:|---|---|---:|---|---|
| 1 | POS PANTAU SECURITY | `SEC` | 1 | aktif | Pos Keamanan / Security |
| 2 | IGD Lt. 1 | `IGD1` | 4 | aktif | Instalasi Gawat Darurat Lantai 1 |
| 3 | STROKE CENTER | `STR` | 2 | aktif | Unit Layanan Stroke Center |
| 4 | IRJA Lt. 1 | `IRJ1` | 6 | aktif | Instalasi Rawat Jalan Lantai 1 |
| 5 | KASIR | `KAS` | 1 | aktif | Loket Kasir Pembayaran |
| 6 | REKAM MEDIS | `RM` | 3 | aktif | Instalasi Rekam Medis |
| 7 | FISIOTERAPI | `FIS` | 1 | aktif | Instalasi Rehabilitasi Medik / Fisioterapi |
| 8 | IRJA LT 2 | `IRJ2` | 2 | aktif | Instalasi Rawat Jalan Lantai 2 |
| 9 | KOMITE MEDIK | `KM` | 1 | aktif | Ruang Komite Medik |
| 10 | AUDITORIUM | `AUD` | 2 | aktif | Gedung Pertemuan Auditorium |
| 11 | MANAJEMEN + Rg ARSIP KEPEGAWAIAN | `MNJ` | 4 | aktif | Gedung Manajemen & Arsip Kepegawaian |
| 12 | INSTALASI FARMASI | `FAR` | 5 | aktif | Instalasi Farmasi Sentral |
| 13 | EMERALD Lt 1 | `EMR1` | 5 | aktif | Gedung Emerald Lantai 1 |
| 14 | EMERALD Lt 2 | `EMR2` | 4 | aktif | Gedung Emerald Lantai 2 |
| 15 | EMERALD Lt 3 | `EMR3` | 4 | aktif | Gedung Emerald Lantai 3 |
| 16 | EMERALD Lt 4 | `EMR4` | 5 | aktif | Gedung Emerald Lantai 4 |
| 17 | EMERALD Lt 5 | `EMR5` | 6 | aktif | Gedung Emerald Lantai 5 |
| 18 | NILAM Lt 1 | `NLM1` | 4 | aktif | Gedung Nilam Lantai 1 |
| 19 | NILAM Lt 2 | `NLM2` | 4 | aktif | Gedung Nilam Lantai 2 |
| 20 | NILAM Lt 3 | `NLM3` | 4 | aktif | Gedung Nilam Lantai 3 |
| 21 | DEPO RAWAT INAP | `DRI` | 1 | aktif | Depo Farmasi Rawat Inap |
| 22 | ALEXANDRI Lt 1 | `ALX1` | 5 | aktif | Gedung Alexandri Lantai 1 |
| 23 | ALEXANDRI Lt 2 | `ALX2` | 5 | aktif | Gedung Alexandri Lantai 2 |
| 24 | ALEXANDRI Lt 3 | `ALX3` | 5 | aktif | Gedung Alexandri Lantai 3 |
| 25 | ALEXANDRI Lt 4 | `ALX4` | 5 | aktif | Gedung Alexandri Lantai 4 |
| 26 | JAMRUD | `JMR` | 0 | kosong | Ruang Jamrud (Master kosong, 0 APAR) |
| 27 | YAKUT | `YKT` | 0 | kosong | Ruang Yakut (Master kosong, 0 APAR) |
| 28 | GIOK | `GIO` | 0 | kosong | Ruang Giok (Master kosong, 0 APAR) |
| 29 | POLI JIWA DAN PSIKOLOGI | `JIW` | 3 | aktif | Poliklinik Jiwa dan Psikologi |
| 30 | KUMALA Lt 1 | `KML1` | 2 | aktif | Gedung Kumala Lantai 1 |
| 31 | KUMALA Lt 2 | `KML2` | 2 | aktif | Gedung Kumala Lantai 2 |
| 32 | KUMALA Lt 3 | `KML3` | 2 | aktif | Gedung Kumala Lantai 3 |
| 33 | KUMALA Lt 4 (OK) s.d. Gedung Mutiara Lt 4 | `KML4` | 6 | aktif | Area Transisi Kumala Lt 4 s.d. Mutiara Lt 4 (dipertahankan utuh) |
| 34 | DEPO OK | `DOK` | 1 | aktif | Depo Farmasi Kamar Operasi (OK) |
| 35 | KUMALA Lt 5 | `KML5` | 2 | aktif | Gedung Kumala Lantai 5 |
| 36 | POLI ESWL | `ESWL` | 1 | aktif | Poliklinik ESWL (Pemecah Batu Ginjal) |
| 37 | RADIOLOGI | `RAD` | 5 | aktif | Instalasi Radiologi & Pencitraan |
| 38 | LABORATORIUM | `LAB` | 3 | aktif | Instalasi Laboratorium Patologi & Klinis |
| 39 | MUTIARA Lt 1 HD | `MTR1` | 7 | aktif | Gedung Mutiara Lantai 1 Unit Hemodialisa |
| 40 | MUTIARA Lt 2 Bayi | `MTR2` | 4 | aktif | Gedung Mutiara Lantai 2 Ruang Perawatan Bayi |
| 41 | MUTIARA Lt 3 ICU | `MTR3` | 4 | aktif | Gedung Mutiara Lantai 3 Ruang ICU |
| 42 | LAUNDRY LT 1 | `LND1` | 4 | aktif | Instalasi Laundry / Binatu Rumah Sakit |
| 43 | CSSD LT 2 | `CSD2` | 3 | aktif | Central Sterile Services Department Lantai 2 |
| 44 | BURN CENTER | `BRN` | 1 | aktif | Unit Perawatan Luka Bakar (Burn Center) |
| 45 | INSTALASI GIZI LT 1 | `GIZ1` | 5 | aktif | Instalasi Gizi & Dapur Utama Lantai 1 |
| 46 | INSTALASI GIZI LT 2 | `GIZ2` | 1 | aktif | Instalasi Gizi Lantai 2 |
| 47 | IT DALAM | `ITD` | 1 | aktif | Ruang Server & IT Internal |
| 48 | IT LUAR | `ITL` | 1 | aktif | Selasar / Area Koridor Luar IT |
| 49 | GUDANG LIMBAH B3 | `LB3` | 1 | aktif | Gudang Penyimpanan Sementara Limbah Medis B3 |
| 50 | C S FREEZEER (SANITASI) | `CSF` | 1 | aktif | Cold Storage / Freezer Unit Sanitasi |
| 51 | INSTALASI SANITASI | `SAN` | 1 | aktif | Kantor Instalasi Kesehatan Lingkungan & Sanitasi |
| 52 | INCENERATOR | `INC` | 3 | aktif | Area Pengolahan Sampah Medis Incenerator |
| 53 | GUDANG LOGISTIK | `LOG` | 2 | aktif | Gudang Logistik Umum & Barang |
| 54 | K3RS | `K3RS` | 1 | aktif | Kantor / Sekretariat Komite K3RS |
| 55 | IPSRS | `IPSR` | 1 | aktif | Instalasi Pemeliharaan Sarana RS (IPSRS) |
| 56 | GENZET | `GEN` | 2 | aktif | Ruang Mesin Pembangkit Listrik (Genset) |
| 57 | KAMAR JENAZAH | `JNZ` | 1 | aktif | Instalasi Forensik & Kamar Jenazah |
| 58 | LIFT | `LFT` | 8 | aktif | Area Penempatan APAR Selasar/Lobi Lift Vertikal |
| 59 | KANTIN | `KTN` | 0 | kosong | Area Kantin Rumah Sakit (Master kosong, 0 APAR) |
| 60 | RESEPCIONIST Lt 2 | `RSP2` | 1 | aktif | Area Meja Resepsionis Lantai 2 |
| 61 | GAS MEDIS (BELAKANG CSSD) | `GAS` | 1 | aktif | Sentral Distribusi Gas Medis Belakang CSSD |
| 62 | 119 | `119` | 1 | aktif | Pos / Pusat Layanan Gawat Darurat PSC 119 |
| 63 | GUDANG K3RS | `GK3` | 0 | stok | Gudang Penyimpanan Stok Sediaan K3RS (64 unit stok, bukan target terpasang) |
| 64 | OK CITO | `OKC` | 2 | aktif | Kamar Operasi Cito / Bedah Darurat |

---

## 4. Rekapitulasi Distribusi Master Data

- **Total Baris Lokasi Master:** 64
- **Lokasi Aktif Ber-APAR:** 59 lokasi (173 unit APAR terpasang)
- **Lokasi Kosong Sah (0 APAR):** 4 lokasi (`JMR`, `YKT`, `GIO`, `KTN`)
- **Lokasi Stok Sediaan:** 1 lokasi (`GK3`, 64 unit stok sediaan metadata)
- **Total Unit APAR Terpasang:** Tepat 173 unit (independen `01` s.d. `N` di setiap kode lokasi)
