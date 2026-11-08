# DOKUMEN MASTER ACUAN DATA K3RS (SINGLE SOURCE OF TRUTH)

**RSUD dr. H. Moch Ansari Saleh – Triwulan III Tahun 2026**
*File ini adalah standar acuan resmi (ground truth) untuk sinkronisasi database, seeder, dan antarmuka web.*
* **Hierarki Gedung & Lantai:** Mengacu pada struktur hierarki dokumen Gedung.
* **Data APAR (Jumlah, Bobot & Tipe):** Mengacu mutlak pada data resmi **Laporan Triwulan Ke-3 (Tabel 12: Total 239 Unit)**.

---

## 1. STRUKTUR HIERARKI GEDUNG, LANTAI, RUANGAN & INVENTARIS APAR

> **Keterangan Format:**
> * Kolom Powder: $25\text{ kg}$, $6\text{ kg}$, $3\text{ kg}$
> * Kolom $\text{CO}_2$: $7\text{ kg}$, $5\text{ kg}$, $3\text{ kg}$
> * Total data tabung terverifikasi: **239 unit** (175 Terpasang di Lapangan + 64 Stok Sediaan Gudang K3RS).

| No | Gedung / Instalasi | Lantai / Zona | Ruangan / Titik Lokasi | Powder 25kg | Powder 6kg | Powder 3kg | CO2 7kg | CO2 5kg | CO2 3kg | Total APAR | Kondisi |
| :--- | :--- | :--- | :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :--- |
| **1** | **POS SECURITY** | Area Luar | POS PANTAU SECURITY | 0 | 1 | 0 | 0 | 0 | 0 | 1 | Baik/Terisi |
| **2** | **GEDUNG INSTALASI RAWAT JALAN (IRJA)** | Lt 1 (Poliklinik) | IRJA Lt. 1 | 0 | 6 | 0 | 0 | 0 | 0 | 6 | Baik/Terisi |
| **3** | GEDUNG INSTALASI RAWAT JALAN (IRJA) | Lt 1 (Poliklinik) | KASIR | 0 | 1 | 0 | 0 | 0 | 0 | 1 | Baik/Terisi |
| **4** | GEDUNG INSTALASI RAWAT JALAN (IRJA) | Lt 1 (Poliklinik) | REKAM MEDIS | 0 | 1 | 0 | 0 | 0 | 2 | 3 | Baik/Terisi |
| **5** | GEDUNG INSTALASI RAWAT JALAN (IRJA) | Lt 1 (Poliklinik) | FISIOTERAPI | 0 | 0 | 0 | 1 | 0 | 0 | 1 | Baik/Terisi |
| **6** | GEDUNG INSTALASI RAWAT JALAN (IRJA) | Lt 2 (Manajemen) | IRJA LT 2 | 0 | 2 | 0 | 0 | 0 | 0 | 2 | Baik/Terisi |
| **7** | GEDUNG INSTALASI RAWAT JALAN (IRJA) | Lt 2 (Manajemen) | RESEPCIONIST Lt 2 | 0 | 1 | 0 | 0 | 0 | 0 | 1 | Baik/Terisi |
| **8** | GEDUNG INSTALASI RAWAT JALAN (IRJA) | Lt 2 (Manajemen) | KOMITE MEDIK | 0 | 1 | 0 | 0 | 0 | 0 | 1 | Baik/Terisi |
| **9** | GEDUNG INSTALASI RAWAT JALAN (IRJA) | Lt 2 (Manajemen) | AUDITORIUM | 0 | 1 | 0 | 1 | 0 | 0 | 2 | Baik/Terisi |
| **10** | GEDUNG INSTALASI RAWAT JALAN (IRJA) | Lt 2 (Manajemen) | MANAJEMEN + Rg ARSIP KEPEGAWAIAN | 0 | 2 | 0 | 1 | 0 | 1 | 4 | Baik/Terisi |
| **11** | **GEDUNG IGD** | Lt 1 (Operasional) | IGD Lt. 1 | 0 | 3 | 0 | 0 | 0 | 1 | 4 | Baik/Terisi |
| **12** | GEDUNG IGD | Lt 1 (Operasional) | STROKE CENTER | 0 | 1 | 0 | 0 | 0 | 1 | 2 | Baik/Terisi |
| **13** | GEDUNG IGD | Lt 1 (Operasional) | 119 (Pos PSC 119) | 0 | 1 | 0 | 0 | 0 | 0 | 1 | Baik/Terisi |
| **-** | GEDUNG IGD | Lt 2 (Rawat Inap) | *(Belum ada titik APAR terpasang)* | 0 | 0 | 0 | 0 | 0 | 0 | 0 | Kosong |
| **14** | **GEDUNG FARMASI & BUSINESS CENTER** | Lt 1 | INSTALASI FARMASI | 0 | 5 | 0 | 0 | 0 | 0 | 5 | Baik/Terisi |
| **15** | GEDUNG FARMASI & BUSINESS CENTER | Lt 2 | IT DALAM | 0 | 0 | 0 | 1 | 0 | 0 | 1 | Baik/Terisi |
| **16** | GEDUNG FARMASI & BUSINESS CENTER | Lt 2 | IT LUAR | 0 | 1 | 0 | 0 | 0 | 0 | 1 | Baik/Terisi |
| **17** | GEDUNG FARMASI & BUSINESS CENTER | Lt 2 | K3RS | 0 | 0 | 0 | 0 | 1 | 0 | 1 | Baik/Terisi |
| **18** | **GEDUNG EMERALD** | Lt 1 | EMERALD Lt 1 | 0 | 5 | 0 | 0 | 0 | 0 | 5 | Baik/Terisi |
| **19** | GEDUNG EMERALD | Lt 2 | EMERALD Lt 2 | 0 | 4 | 0 | 0 | 0 | 0 | 4 | Baik/Terisi |
| **20** | GEDUNG EMERALD | Lt 3 | EMERALD Lt 3 | 0 | 4 | 0 | 0 | 0 | 0 | 4 | Baik/Terisi |
| **21** | GEDUNG EMERALD | Lt 4 | EMERALD Lt 4 | 0 | 5 | 0 | 0 | 0 | 0 | 5 | Baik/Terisi |
| **22** | GEDUNG EMERALD | Lt 5 | EMERALD Lt 5 | 0 | 6 | 0 | 0 | 0 | 0 | 6 | Baik/Terisi |
| **23** | **GEDUNG NILAM** | Lt 1 | NILAM Lt 1 | 0 | 4 | 0 | 0 | 0 | 0 | 4 | Baik/Terisi |
| **24** | GEDUNG NILAM | Lt 2 | NILAM Lt 2 | 0 | 4 | 0 | 0 | 0 | 0 | 4 | Baik/Terisi |
| **25** | GEDUNG NILAM | Lt 3 | NILAM Lt 3 | 0 | 4 | 0 | 0 | 0 | 0 | 4 | Baik/Terisi |
| **26** | **GEDUNG ALEXANDRI** | Lt 1 | ALEXANDRI Lt 1 | 0 | 5 | 0 | 0 | 0 | 0 | 5 | Baik/Terisi |
| **27** | GEDUNG ALEXANDRI | Lt 2 | ALEXANDRI Lt 2 | 0 | 5 | 0 | 0 | 0 | 0 | 5 | Baik/Terisi |
| **28** | GEDUNG ALEXANDRI | Lt 3 | ALEXANDRI Lt 3 | 0 | 5 | 0 | 0 | 0 | 0 | 5 | Baik/Terisi |
| **29** | GEDUNG ALEXANDRI | Lt 4 | ALEXANDRI Lt 4 | 0 | 5 | 0 | 0 | 0 | 0 | 5 | Baik/Terisi |
| **30** | **GEDUNG KUMALA** | Lt 1 | KUMALA Lt 1 | 0 | 1 | 1 | 0 | 0 | 0 | 2 | Baik/Terisi |
| **31** | GEDUNG KUMALA | Lt 2 | KUMALA Lt 2 | 0 | 1 | 1 | 0 | 0 | 0 | 2 | Baik/Terisi |
| **32** | GEDUNG KUMALA | Lt 3 | KUMALA Lt 3 | 0 | 1 | 1 | 0 | 0 | 0 | 2 | Baik/Terisi |
| **33** | GEDUNG KUMALA | Lt 4 (Kamar Operasi) | KUMALA Lt 4 (OK) S/D MUTIARA Lt 4 | 0 | 0 | 0 | 2 | 0 | 4 | 6 | Baik/Terisi |
| **34** | GEDUNG KUMALA | Lt 4 (Kamar Operasi) | DEPO OK | 0 | 1 | 0 | 0 | 0 | 0 | 1 | Baik/Terisi |
| **35** | GEDUNG KUMALA | Lt 4 (Kamar Operasi) | OK CITO | 0 | 0 | 0 | 0 | 0 | 2 | 2 | Baik/Terisi |
| **36** | GEDUNG KUMALA | Lt 4 (Burn Center) | BURN CENTER | 0 | 1 | 0 | 0 | 0 | 0 | 1 | Baik/Terisi |
| **37** | GEDUNG KUMALA | Lt 5 | KUMALA Lt 5 | 0 | 1 | 1 | 0 | 0 | 0 | 2 | Baik/Terisi |
| **38** | **GEDUNG IBS (MUTIARA)** | Lt 1 | MUTIARA Lt 1 HD | 0 | 3 | 0 | 0 | 0 | 4 | 7 | Baik/Terisi |
| **39** | GEDUNG IBS (MUTIARA) | Lt 2 | MUTIARA Lt 2 Bayi | 0 | 0 | 0 | 0 | 0 | 4 | 4 | Baik/Terisi |
| **40** | GEDUNG IBS (MUTIARA) | Lt 3 | MUTIARA Lt 3 ICU | 0 | 0 | 0 | 1 | 0 | 3 | 4 | Baik/Terisi |
| **41** | **INSTALASI FORENSIK & MEDIKOLEGAL** | Lt 1 | KAMAR JENAZAH | 0 | 1 | 0 | 0 | 0 | 0 | 1 | Baik/Terisi |
| **42** | **GEDUNG DIAGNOSTIK** | Lt 1 | RADIOLOGI | 0 | 0 | 0 | 1 | 2 | 2 | 5 | Baik/Terisi |
| **43** | GEDUNG DIAGNOSTIK | Lt 1 | POLI ESWL | 0 | 0 | 0 | 1 | 0 | 0 | 1 | Baik/Terisi |
| **44** | GEDUNG DIAGNOSTIK | Lt 2 | LABORATORIUM | 0 | 0 | 0 | 1 | 0 | 2 | 3 | Baik/Terisi |
| **45** | **INSTALASI GIZI** | Lt 1 | INSTALASI GIZI LT 1 | 1 | 0 | 0 | 4 | 0 | 0 | 5 | Baik/Terisi |
| **46** | INSTALASI GIZI | Lt 2 | INSTALASI GIZI LT 2 | 0 | 1 | 0 | 0 | 0 | 0 | 1 | Baik/Terisi |
| **47** | **INSTALASI CSSD & LAUNDRY** | Lt 1 | LAUNDRY LT 1 | 1 | 0 | 0 | 1 | 2 | 0 | 4 | Baik/Terisi |
| **48** | INSTALASI CSSD & LAUNDRY | Lt 2 | CSSD LT 2 | 0 | 1 | 0 | 2 | 0 | 0 | 3 | Baik/Terisi |
| **49** | INSTALASI CSSD & LAUNDRY | Lt 1 (Samping/Luar) | GAS MEDIS (BELAKANG CSSD) | 0 | 1 | 0 | 0 | 0 | 0 | 1 | Baik/Terisi |
| **50** | **DEPO FARMASI RAWAT INAP** | Lt 1 | DEPO RAWAT INAP | 0 | 1 | 0 | 0 | 0 | 0 | 1 | Baik/Terisi |
| **51** | **INSTALASI LOGISTIK & IPSRS** | Lt 1 | GUDANG LOGISTIK | 0 | 2 | 0 | 0 | 0 | 0 | 2 | Baik/Terisi |
| **52** | INSTALASI LOGISTIK & IPSRS | Lt 1 | IPSRS | 0 | 1 | 0 | 0 | 0 | 0 | 1 | Baik/Terisi |
| **53** | INSTALASI LOGISTIK & IPSRS | Lt 1 | GENZET | 1 | 0 | 0 | 0 | 1 | 0 | 2 | Baik/Terisi |
| **54** | **IRNA JIWA / POLIKLINIK** | Lantai Standar | POLI JIWA DAN PSIKOLOGI | 0 | 3 | 0 | 0 | 0 | 0 | 3 | Baik/Terisi |
| **55** | **GEDUNG INCENERATOR** | Lt 1 | INCENERATOR | 0 | 2 | 0 | 0 | 1 | 0 | 3 | Baik/Terisi |
| **56** | **GEDUNG SANITASI** | Lt 1 | GUDANG LIMBAH B3 | 0 | 1 | 0 | 0 | 0 | 0 | 1 | Baik/Terisi |
| **57** | GEDUNG SANITASI | Lt 1 | C S FREEZEER (SANITASI) | 0 | 1 | 0 | 0 | 0 | 0 | 1 | Baik/Terisi |
| **58** | GEDUNG SANITASI | Lt 1 | INSTALASI SANITASI | 0 | 1 | 0 | 0 | 0 | 0 | 1 | Baik/Terisi |
| **59** | **LIFT** | Fasilitas Gedung | LIFT (Total terpasang di unit lift) | 0 | 0 | 0 | 0 | 0 | 8 | 8 | Baik/Terisi |
| **60** | **GUDANG K3RS** | Area Logistik | GUDANG K3RS (STOK SEDIAAN) | 0 | 64 | 0 | 0 | 0 | 0 | 64 | Stok Sediaan |
| **-** | **GEDUNG NON-AKTIF / KOSONG** | Area Khusus | JAMRUD | 0 | 0 | 0 | 0 | 0 | 0 | 0 | Gedung Kosong |
| **-** | GEDUNG NON-AKTIF / KOSONG | Area Khusus | YAKUT | 0 | 0 | 0 | 0 | 0 | 0 | 0 | Gedung Kosong |
| **-** | GEDUNG NON-AKTIF / KOSONG | Area Khusus | GIOK | 0 | 0 | 0 | 0 | 0 | 0 | 0 | Gedung Kosong |
| **-** | GEDUNG NON-AKTIF / KOSONG | Area Khusus | KANTIN | 0 | 0 | 0 | 0 | 0 | 0 | 0 | Gedung Kosong |
| **TOTAL** | **REKAPITULASI RESMI (TABEL 12)** | \- | **TOTAL KESELURUHAN** | **3** | **168** | **8** | **17** | **9** | **34** | **239** | **FIX (Triwulan III)** |

---

## 2. REKAPITULASI TABUNG BERDASARKAN DISTRIBUSI

1. **APAR Terpasang di Lapangan:**
   * Powder $25\text{ kg}$: 3 unit
   * Powder $6\text{ kg}$: 104 unit
   * Powder $3\text{ kg}$: 8 unit
   * $\text{CO}_2$ $7\text{ kg}$: 17 unit
   * $\text{CO}_2$ $5\text{ kg}$: 9 unit
   * $\text{CO}_2$ $3\text{ kg}$: 34 unit
   * **Subtotal Terpasang:** **175 unit**

2. **APAR Stok Sediaan (Gudang K3RS):**
   * Powder $6\text{ kg}$: **64 unit**

3. **Total Akhir (Terpasang + Stok Gudang):**
   $$\text{Total APAR} = 175 + 64 = 239\text{ unit}$$

---

## 3. INVENTARIS & MONITORING HYDRANT (61 TITIK)

| No | Lokasi Hydrant | Fisik Box | Kebersihan | Status Kelengkapan Komponen | Status Operasional |
| :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | Nilam lt 1 | Baik | Kotor | Nozzle hilang | Tidak dapat difungsikan |
| 2 | Nilam lt 1 | Baik | Bersih | Ujung nozzle hilang, ujung set selang hilang, coupling atas hilang | Tidak dapat difungsikan |
| 3 | Nilam lt 2 | Baik | Bersih | Coupling atas bawah hilang, ujung selang set hilang | Tidak dapat difungsikan |
| 4 | Nilam lt 2 | Baik | Bersih | Ujung nozzle, set coupling selang hilang, coupling bawah hilang | Tidak dapat difungsikan |
| 5 | Nilam lt 3 | Baik | Bersih | Coupling bawah hilang dan nozzle set | Tidak dapat difungsikan |
| 6 | Nilam lt 3 | Baik | Bersih | Set coupling atas bawah hilang dan nozzle set | Tidak dapat difungsikan |
| 7 | Alexandri 1 | Baik | Bersih | Nozzle set hilang, ujung set selang hilang, coupling atas bawah hilang | Tidak dapat difungsikan |
| 8 | Alexandri 1 | Baik | Bersih | Coupling bawah hilang, coupling atas hilang, ujung set selang hilang, ujung nozzle set | Tidak dapat difungsikan |
| 9 | Alexandri 2 | Baik | Bersih | Coupling atas bawah hilang | Tidak dapat difungsikan |
| 10 | Alexandri 2 | Baik | Bersih | Nozel set, coupling atas bawah hilang, ujung selang set hilang | Tidak dapat difungsikan |
| 11 | Alexandri 3 | Baik | Bersih | Set selang hilang, coupling atas bawah hilang | Tidak dapat difungsikan |
| 12 | Alexandri 3 | Baik | Bersih | Nozel set, coupling atas bawah hilang, ujung slang set hilang | Tidak dapat difungsikan |
| 13 | Alexandri 4 | Baik | Bersih | Nozzle set, selang hilang, set coupling atas bawah hilang | Tidak dapat difungsikan |
| 14 | Alexandri 4 | Baik | Bersih | Ujung nozzle, stop kran bawah hilang, set coupling atas bawah hilang, sisir hilang | Tidak dapat difungsikan |
| 15 | Kumala 1 (Belakang) | Baik | Bersih | Nozzle lengkap, selang ada, coupling atas ada, coupling bawah hilang | Tidak dapat difungsikan |
| 16 | Kumala 1 (Depan) | Baik | Bersih | Set Hydrant lengkap | Tidak dapat difungsikan |
| 17 | Kumala 2 (Depan) | Baik | Bersih | Nozzle dan set coupling selang hilang | Tidak dapat difungsikan |
| 18 | Kumala 2 (Belakang) | Baik | Bersih | Nozzle set hilang, set ujung selang hilang, coupling atas bawah hilang | Tidak dapat difungsikan |
| 19 | Kumala 3 (Depan) | Baik | Bersih | Ujung nozzle hilang dan set coupling atas bawah hilang | Tidak dapat difungsikan |
| 20 | Kumala 3 (Belakang) | Baik | Bersih | Habis, perlengkapan hilang semua / sisa kran atas bawah | Tidak dapat difungsikan |
| 21 | Kumala 4 (OK) Depan | Baik | Bersih | Lengkap | Tidak dapat difungsikan |
| 22 | Kumala 4 (OK) Tengah | Baik | Bersih | Kran bawah set hilang | Tidak dapat difungsikan |
| 23 | Kumala 4 (OK) Belakang | Baik | Bersih | Nozzle set hilang | Tidak dapat difungsikan |
| 24 | Kumala 5 | Baik | Bersih | Lengkap | Tidak dapat difungsikan |
| 25 | Mutiara 1 VK Bersalin (Depan) | Baik | Bersih | Nozzle set lengkap, selang ada, coupling atas ada, coupling bawah hilang, kran lengkap | Tidak ada suplai distribusi air |
| 26 | Mutiara 1 VK Bersalin (Belakang) | Kran Rusak | Bersih | Nozzle set lengkap, selang ada, set coupling atas hilang, coupling bawah ada, kran lengkap | Tidak ada suplai distribusi air |
| 27 | Mutiara 2 Bayi (Depan) | Baik | Bersih | Nozzle set hilang, set coupling atas bawah hilang | Tidak ada suplai distribusi air |
| 28 | Mutiara 2 Bayi (Belakang) | Baik | Bersih | Peralatan hilang semua, kran atas bawah ada | Tidak ada suplai distribusi air |
| 29 | Mutiara 3 ICU (Depan) | Baik | Bersih | Ujung nozzle hilang, coupling bawah dan kran bawah hilang | Tidak ada suplai distribusi air |
| 30 | Mutiara 3 ICU (Belakang) | Baik | Bersih | Lengkap | Tidak ada suplai distribusi air |
| 31 | Yakut | Baik | Bersih | Kran bawah hilang diganti DOP | Tidak dapat difungsikan |
| 32 | Instalasi Gizi Lt 1 | Baik | Bersih | Peralatan hilang semua | Tidak dapat difungsikan |
| 33 | Instalasi Gizi Lt 2 | Baik | Bersih | Nozzle set hilang | Tidak dapat difungsikan |
| 34 | Laundry | Baik | Bersih | Lengkap | Tidak dapat difungsikan |
| 35 | CSSD Lt 2 | Baik | Bersih | Lengkap | Tidak dapat difungsikan |
| 36 | CSSD Lt 3 | Baik | Kotor | Peralatan hilang semua | Tidak dapat difungsikan |
| 37 | Emerald Lt 1 | Baik | Kotor | Lengkap | Tidak dapat difungsikan |
| 38 | Emerald Lt 1 Samping Lift | Baik | Kotor | Nozzle set hilang, ujung set selang hilang, coupling atas hilang, coupling bawah hilang | Tidak dapat difungsikan |
| 39 | Emerald Lt 1 Samping Arsip | Baik | Bersih | Ujung nozzle, ujung set selang hilang, coupling atas bawah hilang | Tidak dapat difungsikan |
| 40 | Emerald Lt 2 | Baik | Bersih | Coupling atas bawah hilang, nozzle set hilang, ujung selang 1 hilang | Tidak dapat difungsikan |
| 41 | Emerald Lt 2 | Baik | Bersih | Lengkap | Tidak dapat difungsikan |
| 42 | Emerald Lt 2 | Baik | Bersih | Ujung kepala nozzle hilang, coupling atas bawah hilang | Tidak dapat difungsikan |
| 43 | Emerald Lt 3 | Baik | Bersih | Nozzle, break glass hilang, coupling bawah hilang, coupling atas hilang, ujung selang set hilang | Tidak dapat difungsikan |
| 44 | Emerald Lt 3 | Baik | Bersih | Coupling bawah hilang | Tidak dapat difungsikan |
| 45 | Emerald Lt 3 | Baik | Bersih | Coupling atas bawah hilang, ujung selang set hilang | Tidak dapat difungsikan |
| 46 | Emerald Lt 4 | Baik | Bersih | Nozzle, set coupling selang hilang, coupling bawah hilang | Tidak dapat difungsikan |
| 47 | Emerald Lt 4 | Baik | Bersih | Ujung nozzle, set coupling selang hilang, coupling bawah hilang | Tidak dapat difungsikan |
| 48 | Emerald Lt 4 | Baik | Bersih | Lengkap | Tidak dapat difungsikan |
| 49 | Emerald Lt 5 | Baik | Bersih | Coupling bawah hilang | Tidak dapat difungsikan |
| 50 | Hydrant Luar 1 (Dekat Genset) | Baik | Kotor | Peralatan hilang semua | Tidak dapat difungsikan |
| 51 | Hydrant Luar 2 (Belakang G. Emerald) | Baik | Kotor | Peralatan hilang semua | Tidak dapat difungsikan |
| 52 | Hydrant Luar 3 (Poli Paru) | Baik | Kotor | Peralatan hilang semua | Tidak dapat difungsikan |
| 53 | Hydrant Luar 4 (Depan G. Emerald) | Baik | Kotor | Peralatan hilang tersisa selang yang terpotong | Tidak dapat difungsikan |
| 54 | Hydrant Luar 5 (Depan Nilam) | Baik | Kotor | Peralatan hilang semua | Tidak dapat difungsikan |
| 55 | Hydrant Luar 6 (Belakang Nilam) | Baik | Kotor | Peralatan hilang semua | Tidak dapat difungsikan |
| 56 | Hydrant Luar 7 (Depan G. Alexandri) | Baik | Kotor | Peralatan hilang semua | Tidak dapat difungsikan |
| 57 | Hydrant Luar 8 (Depan G. Mutiara) | Baik | Kotor | Tanpa box hydrant | Tidak dapat difungsikan |
| 58 | Radiologi 1 | Baik | Bersih | Nozzle set hilang, set coupling atas bawah hilang | Tidak ada suplai distribusi air |
| 59 | Radiologi 2 | Baik | Bersih | Lengkap | Tidak ada suplai distribusi air |
| 60 | Laboratorium 1 | Baik | Bersih | Nozel set, coupling atas bawah hilang, ujung selang set hilang | Tidak ada suplai distribusi air |
| 61 | Laboratorium 2 | Baik | Bersih | Lengkap | Tidak ada suplai distribusi air |

---

## 4. INDIKATOR INSIDEN K3RS (TRIWULAN III 2026)

* **Kecelakaan Kerja:** 4 Kasus Non-Fatal (2 Tertusuk Jarum, 1 Terpeleset, 1 Tertimpa Benda Berat).
* **Faktor Investigasi:** 2 Kasus *Unsafe Action*, 2 Kasus *Unsafe Condition*.
* **Kejadian Sentinel Alat Medis:** 0 Kasus.
* **Karyawan Terpapar Covid-19:** 0 Kasus.
* **Tumpahan B3 & Infeksius:** Total 116 Kasus (105 Darah, 7 B3 Kimia, 4 Urin). Paling banyak terjadi di OK CITO (54 kasus) dan IGD (25 kasus).

---

## 5. PEDOMAN INTEGRITAS UNTUK DEVELOPER / AI (ANTIGRAVITY)

1. **Hierarki Gedung & Lantai:**
   * Selalu buat relasi berjenjang: `Building` $\rightarrow$ `Floor` $\rightarrow$ `Room / Point Location` $\rightarrow$ `APAR Items`.
   * Lantai yang tidak memiliki APAR (seperti **Lantai 2 IGD**) tetap harus terdaftar di tabel master lantai demi kelengkapan gedung, namun titik lokasi dan counter APAR-nya bernilai `0`.
   * Gedung kosong (**Jamrud, Yakut, Giok, Kantin**) dimasukkan ke master gedung dengan status `Kosong / Non-Aktif` dan total unit `0`.

2. **Entitas Ruangan IGD:**
   * Di Lantai 1 IGD wajib memuat 3 titik penempatan APAR: `IGD Lt. 1` (4 unit), `STROKE CENTER` (2 unit), dan `119` (1 unit).
   * Ruangan fisik klinis seperti *Ruang Triase* dapat tetap ada di master ruangan fisik klinis, tetapi relasi penempatan APAR-nya menginduk pada zonanya jika tidak memiliki tabung tersendiri.

3. **Spesifikasi Tabung APAR:**
   * Kartu web dilarang hanya menampilkan jumlah angka gelondongan. Wajib memecah berdasarkan relasi spesifik tipe dan beratnya sesuai tabel di atas (contoh: IGD Lt. 1 = 3 unit Powder 6 kg + 1 unit $\text{CO}_2$ 3 kg).
   * Total unit di sistem harus menghasilkan angka pasti: **239 unit** (175 unit di lapangan + 64 unit stok di gudang K3RS).