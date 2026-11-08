# CURRENT SYSTEM & LOCATION ARCHITECTURE AUDIT
**Sistem Informasi Proteksi Kebakaran dan Pemantauan APAR Rumah Sakit (HAFIS)**
**RSUD dr. H. Moch. Ansari Saleh Banjarmasin**

- **Dokumen Referensi:** `docs/CURRENT_SYSTEM_LOCATION_AUDIT.md`
- **Tanggal Audit:** 31 Oktober 2026
- **Status Audit:** SELESAI (READ-ONLY AUDIT: ZERO CODE / DB MUTATION)
- **Auditor:** DeepMind Advanced Agentic Coding (Antigravity)
- **Target Repository:** `C:\laragon\www\aparcek-8` dan `C:\laragon\www\aparcek-6`
- **Basis Data:** SQLite 3.53.4 (`database/database.sqlite`)

---

## 1. Executive Summary

Audit arsitektur sistem dan struktur lokasi HAFIS Fase 1D dilakukan secara menyeluruh terhadap skema basis data aktif, model Eloquent, pengendali (*controllers*), tampilan antarmuka (*Blade views*), *seeder*, berkas migrasi, dan berkas uji (*feature tests*). Audit ini bertujuan memverifikasi kesiapan teknis sistem sebelum dilakukan rekonsiliasi master lokasi dan migrasi skema basis data.

### Ringkasan Jawaban 8 Pertanyaan Utama Audit:

1. **Bagaimana struktur lokasi saat ini diimplementasikan?**
   Sistem saat ini terpecah menjadi dua implementasi paralel yang tidak sinkron:
   - **Struktur Hierarkis Fisik:** `Gedung` -> `Lantai` -> `Ruangan` -> `APAR` (dikelola di modul Struktur Lokasi `/lokasi`, modul Denah `/penempatan`, dan form pendaftaran `/apar/create`).
   - **Struktur Flat Operasional K3RS:** Tabel mandiri `lokasis` (64 master lokasi K3RS) yang langsung terhubung ke `APAR` via `lokasi_id` (digunakan pada nomor registrasi unit, workflow Inspeksi Bulanan `/pemeriksaan`, dan pelaporan K3RS).
2. **Bagaimana relasi Gedung, Lantai, Ruangan, dan APAR di database?**
   - `gedungs` (1:N) -> `lantais` (via `lantais.gedung_id` FK CASCADE).
   - `lantais` (1:N) -> `ruangans` (via `ruangans.lantai_id` FK CASCADE). Ruangan tidak memiliki kolom `gedung_id`.
   - `ruangans` (1:N) -> `apars` (via `apars.ruangan_id` FK NOT NULL CASCADE).
   - `lokasis` (1:N) -> `apars` (via `apars.lokasi_id` FK NULLABLE SET NULL).
3. **Apakah sistem mendukung gedung dengan jumlah lantai berbeda?**
   **YA.** Sistem mendukung sepenuhnya. Skema database menggunakan relasi dinamis 1:N antar gedung dan lantai dengan validasi nomor lantai unik per gedung (`unique:lantais,nomor_lantai` scoped to `gedung_id`). Data aktif membuktikan: Gedung Emerald memiliki 5 lantai, Alexandri 4 lantai, Nilam 3 lantai, IRJ 2 lantai, dan Logistik 1 lantai.
4. **Apakah lantai dapat memiliki banyak ruangan?**
   **YA.** Relasi `lantais` ke `ruangans` adalah `hasMany` (1:N). Di database aktif, Lantai 1 Gedung IGD menampung 3 ruangan, dan Lantai 1 Gedung IRJ menampung 2 ruangan.
5. **Apakah ruangan dapat ditambahkan tanpa lantai?**
   **TIDAK.** Di database, kolom `ruangans.lantai_id` berstatus `NOT NULL` dengan *foreign key constraint* ke `lantais(id)`. Pada form input `LokasiController::storeRuangan`, `lantai_id` berstatus `required, exists:lantais,id`.
6. **Apakah APAR benar-benar terhubung ke ruangan fisik yang tepat?**
   **TIDAK (ANOMALI KRITIS).** Dari 173 unit APAR aktif di basis data, **seluruh 173 unit (100%)** memiliki nilai `ruangan_id = 1` yang menunjuk ke **Ruang IGD (Lantai 1, Gedung IGD)**. Tidak ada satu pun APAR yang terhubung ke 8 ruangan fisik lainnya atau 16 gedung lainnya melalui relasi struktural `ruangan_id`.
7. **Apakah terdapat data dummy, relasi salah, atau data yang tidak konsisten?**
   **YA.** Ditemukan relasi salah masif pada seluruh APAR terhadap `ruangan_id = 1` akibat *hardcoded fallback* pada seeder `AparAktualSeeder.php`. Selain itu, form tambah APAR (`AparController::store`) tidak menyimpan `lokasi_id`, sehingga APAR yang didaftarkan lewat web tidak dapat diinspeksi di modul pemeriksaan bulanan K3RS.
8. **Apakah struktur saat ini sudah cocok untuk master lokasi berdasarkan inventaris APAR?**
   **BELUM COCOK.** Sistem saat ini belum siap beroperasi dengan hierarki penuh tanpa rekonsiliasi. Struktur flat 64 titik K3RS di `lokasis` belum terpetakan ke tabel hierarki fisik (`gedungs`, `lantais`, `ruangans`), menyebabkan 14 dari 17 gedung kosong ruangan, modul denah menumpuk seluruh APAR di satu lantai, dan filter laporan gedung mengembalikan data kosong.

---

## 2. Kondisi Stack dan Struktur Sistem

### 2.1 Lingkungan Runtime dan Framework
| Komponen | Versi Terpasang | Konfigurasi Aktif |
|---|---|---|
| **Framework** | Laravel 13.34.0 | Environment: `local`, Debug: `true` |
| **Bahasa Pemrograman** | PHP 8.5.10 | Extension: `pdo_sqlite`, `gd`, `mbstring`, `fileinfo` |
| **Basis Data** | SQLite 3.53.4 | Driver: `sqlite`, File: `database/database.sqlite` |
| **Frontend Compiler** | Vite 8 + Tailwind CSS v4 | PostCSS/Vite asset bundle |
| **Operating System** | Windows NT (Laragon stack) | Shell: PowerShell |
| **Timezone & Locale** | Asia/Makassar (`WITA`) | Locale: `id`, Fallback: `id` |

### 2.2 Inventaris Tabel Basis Data Aktif
Berdasarkan query langsung ke `sqlite_master` pada basis data aktif (`database/database.sqlite`), terdapat **21 tabel**:

| No | Nama Tabel | Kategori | Jumlah Record Aktif | Fungsi Sistem |
|---:|---|---|---:|---|
| 1 | `gedungs` | Struktur Lokasi | 17 | Master gedung rumah sakit |
| 2 | `lantais` | Struktur Lokasi | 40 | Tingkat lantai fisik gedung |
| 3 | `ruangans` | Struktur Lokasi | 9 | Ruangan / area fisik di lantai |
| 4 | `lokasis` | Master Lokasi K3RS | 64 | Master titik lokasi operasional K3RS |
| 5 | `denahs` | Spasial / Denah | 1 | File denah lantai (SVG/PNG/JPG) |
| 6 | `apars` | Aset Utama | 173 | Master data unit tabung APAR |
| 7 | `pemeriksaans` | Operasional K3RS | 7 | Header inspeksi fisik APAR bulanan |
| 8 | `pemeriksaan_details` | Operasional K3RS | 35 | Rincian 5 item checklist inspeksi fisik |
| 9 | `jadwals` | Penjadwalan | 1 | Jadwal inspeksi berkala |
| 10 | `jadwal_apars` | Penjadwalan | 0 | Pivot jadwal dan APAR target |
| 11 | `tindak_lanjuts` | Pemeliharaan | 0 | Tiket tindak lanjut temuan inspeksi |
| 12 | `penggantian_apars` | Pemeliharaan | 0 | Riwayat penggantian unit APAR rusak |
| 13 | `users` | Autentikasi | 1 | Pengguna sistem (Petugas K3RS) |
| 14 | `password_reset_tokens`| Autentikasi | 0 | Token reset kata sandi |
| 15 | `sessions` | Sesi Pengguna | 2 | Sesi login berbasis database |
| 16 | `cache` | Framework | 0 | Cache sistem |
| 17 | `cache_locks` | Framework | 0 | Lock concurrency cache |
| 18 | `jobs` | Queue | 0 | Antrean pekerjaan latar belakang |
| 19 | `job_batches` | Queue | 0 | Batch queue jobs |
| 20 | `failed_jobs` | Queue | 0 | Log antrean gagal |
| 21 | `migrations` | Database Version | 17 | Riwayat migrasi Laravel |

---

## 3. Database Schema Audit

Pemeriksaan komparatif dilakukan antara berkas migrasi (`database/migrations/`) dan struktur aktual SQLite via `PRAGMA table_info`, `PRAGMA foreign_key_list`, dan `PRAGMA index_list`:

### 3.1 Tabel `gedungs`
- **File Migrasi:** `2026_10_03_041012_create_lokasi_tables.php` (baris 15:22)
- **Primary Key:** `id` (INTEGER, AUTOINCREMENT)

| Kolom | Tipe Data | Nullable | Default | Foreign Key | Index / Unique |
|---|---|:---:|---|---|---|
| `id` | INTEGER | Tidak | Auto | - | PRIMARY KEY |
| `kode` | VARCHAR(30) | Tidak | - | - | UNIQUE (`gedungs_kode_unique`) |
| `nama` | VARCHAR(120) | Tidak | - | - | UNIQUE (`gedungs_nama_unique`) |
| `keterangan` | TEXT | Ya | NULL | - | - |
| `status` | VARCHAR(20) | Tidak | `'aktif'` | - | - |
| `created_at` | DATETIME | Ya | NULL | - | - |
| `updated_at` | DATETIME | Ya | NULL | - | - |

### 3.2 Tabel `lantais`
- **File Migrasi:** `2026_10_03_041012_create_lokasi_tables.php` (baris 25:34)
- **Primary Key:** `id` (INTEGER, AUTOINCREMENT)

| Kolom | Tipe Data | Nullable | Default | Foreign Key | Index / Unique |
|---|---|:---:|---|---|---|
| `id` | INTEGER | Tidak | Auto | - | PRIMARY KEY |
| `gedung_id` | INTEGER | Tidak | - | `gedungs(id)` ON DELETE CASCADE | INDEX |
| `nomor_lantai`| INTEGER | Tidak | - | - | COMPOUND UNIQUE (`lantais_gedung_id_nomor_lantai_unique`) |
| `nama` | VARCHAR(100) | Tidak | - | - | - |
| `keterangan` | TEXT | Ya | NULL | - | - |
| `created_at` | DATETIME | Ya | NULL | - | - |
| `updated_at` | DATETIME | Ya | NULL | - | - |

### 3.3 Tabel `ruangans`
- **File Migrasi:** `2026_10_03_041012_create_lokasi_tables.php` (baris 37:47)
- **Primary Key:** `id` (INTEGER, AUTOINCREMENT)

| Kolom | Tipe Data | Nullable | Default | Foreign Key | Index / Unique |
|---|---|:---:|---|---|---|
| `id` | INTEGER | Tidak | Auto | - | PRIMARY KEY |
| `lantai_id` | INTEGER | Tidak | - | `lantais(id)` ON DELETE CASCADE | INDEX |
| `kode` | VARCHAR(50) | Ya | NULL | - | - |
| `nama` | VARCHAR(150) | Tidak | - | - | COMPOUND UNIQUE (`ruangans_lantai_id_nama_unique`) |
| `kategori` | VARCHAR(60) | Ya | NULL | - | - |
| `keterangan` | TEXT | Ya | NULL | - | - |
| `created_at` | DATETIME | Ya | NULL | - | - |
| `updated_at` | DATETIME | Ya | NULL | - | - |

> [!IMPORTANT]
> Tabel `ruangans` **tidak memiliki kolom `gedung_id`**. Relasi ke gedung bersifat bertingkat (*indirect*) melalui tabel perantara `lantais`. Ruangan **wajib** memiliki `lantai_id` (`NOT NULL`).

### 3.4 Tabel `lokasis`
- **File Migrasi:** `2026_10_08_000001_create_lokasis_table.php` (baris 14:23)
- **Primary Key:** `id` (INTEGER, AUTOINCREMENT)

| Kolom | Tipe Data | Nullable | Default | Foreign Key | Index / Unique |
|---|---|:---:|---|---|---|
| `id` | INTEGER | Tidak | Auto | - | PRIMARY KEY |
| `nama` | VARCHAR(150) | Tidak | - | - | INDEX (`lokasis_nama_index`) |
| `kode` | VARCHAR(50) | Ya | NULL | - | - |
| `kategori` | VARCHAR(60) | Ya | NULL | - | - |
| `status` | VARCHAR(20) | Tidak | `'aktif'` | - | - |
| `keterangan` | TEXT | Ya | NULL | - | - |
| `created_at` | DATETIME | Ya | NULL | - | - |
| `updated_at` | DATETIME | Ya | NULL | - | - |

> [!NOTE]
> Tabel `lokasis` bersifat *flat* (berdiri sendiri tanpa foreign key ke `gedungs`, `lantais`, maupun `ruangans`). Kolom `kode` tidak memiliki konstrain `UNIQUE` di tingkat skema basis data, meskipun pada seeder diupayakan unik.

### 3.5 Tabel `apars`
- **File Migrasi:**
  - Pembuatan: `2026_10_03_052141_create_apars_table.php`
  - Tambah Koordinat: `2026_10_03_053739_add_koordinat_to_apars_table.php`
  - Tambah Relasi Penggantian: `2026_10_03_104150_create_penggantian_apars_table.php`
  - Tambah Relasi Lokasi: `2026_10_08_000002_add_lokasi_id_to_apars_table.php`
- **Primary Key:** `id` (INTEGER, AUTOINCREMENT)

| Kolom | Tipe Data | Nullable | Default | Foreign Key | Index / Unique |
|---|---|:---:|---|---|---|
| `id` | INTEGER | Tidak | Auto | - | PRIMARY KEY |
| `kode` | VARCHAR(50) | Tidak | - | - | UNIQUE (`apars_kode_unique`) |
| `nomor_register` | VARCHAR(100) | Ya | NULL | - | - |
| `qr_code` | VARCHAR(100) | Tidak | - | - | UNIQUE (`apars_qr_code_unique`) |
| `ruangan_id` | INTEGER | **Tidak** | - | `ruangans(id)` ON DELETE CASCADE | INDEX |
| `lokasi_id` | INTEGER | **Ya** | NULL | `lokasis(id)` ON DELETE SET NULL | INDEX |
| `posisi_detail` | VARCHAR(255) | Ya | NULL | - | - |
| `jenis_media` | VARCHAR(50) | Tidak | - | - | - |
| `kapasitas` | VARCHAR(30) | Tidak | - | - | - |
| `merek` | VARCHAR(100) | Ya | NULL | - | - |
| `tipe_model` | VARCHAR(100) | Ya | NULL | - | - |
| `status` | VARCHAR(30) | Tidak | `'baik'` | - | - |
| `tanggal_pemasangan` | DATE | Ya | NULL | - | - |
| `tanggal_kadaluarsa` | DATE | Ya | NULL | - | - |
| `tanggal_pemeriksaan_terakhir`| DATE | Ya | NULL | - | - |
| `koordinat_x` | NUMERIC(5,2)| Ya | NULL | - | - |
| `koordinat_y` | NUMERIC(5,2)| Ya | NULL | - | - |
| `replaced_by_apar_id` | INTEGER | Ya | NULL | `apars(id)` ON DELETE SET NULL | - |
| `replaces_apar_id` | INTEGER | Ya | NULL | `apars(id)` ON DELETE SET NULL | - |
| `alasan_nonaktif` | VARCHAR(255) | Ya | NULL | - | - |
| `tanggal_nonaktif` | DATE | Ya | NULL | - | - |
| `keterangan` | TEXT | Ya | NULL | - | - |
| `deleted_at` | DATETIME | Ya | NULL | - | Soft Deletes |
| `created_at` | DATETIME | Ya | NULL | - | - |
| `updated_at` | DATETIME | Ya | NULL | - | - |

> [!CAUTION]
> Kolom `ruangan_id` berstatus `NOT NULL` dengan perilaku bahaya `ON DELETE CASCADE`. Sedangkan `lokasi_id` berstatus `NULLABLE` dengan `ON DELETE SET NULL`. Keberadaan dua kolom lokasi ini mencerminkan fragmentasi arsitektur sistem.

### 3.6 Tabel `denahs`
- **File Migrasi:** `2026_10_03_053728_create_denahs_table.php`
- **Primary Key:** `id` (INTEGER, AUTOINCREMENT)

| Kolom | Tipe Data | Nullable | Default | Foreign Key | Index / Unique |
|---|---|:---:|---|---|---|
| `id` | INTEGER | Tidak | Auto | - | PRIMARY KEY |
| `lantai_id` | INTEGER | Tidak | - | `lantais(id)` ON DELETE CASCADE | UNIQUE (`denahs_lantai_id_unique`) |
| `file_path` | VARCHAR(255) | Tidak | - | - | - |
| `nama_file_asli` | VARCHAR(255) | Ya | NULL | - | - |
| `mime_type` | VARCHAR(50) | Ya | NULL | - | - |
| `file_size` | INTEGER | Ya | NULL | - | - |
| `keterangan` | TEXT | Ya | NULL | - | - |
| `created_at` | DATETIME | Ya | NULL | - | - |
| `updated_at` | DATETIME | Ya | NULL | - | - |

### 3.7 Tabel `pemeriksaans` dan `pemeriksaan_details`
- **File Migrasi:** `2026_10_03_065352_create_pemeriksaans_tables.php`, `2026_10_08_000003_add_periode_to_pemeriksaans_table.php`, `2026_10_12_000001_add_unique_index_to_pemeriksaans_table.php`

**Tabel `pemeriksaans`:**
- Primary Key: `id` (INTEGER, AUTOINCREMENT)
- Foreign Keys:
  - `apar_id` -> `apars(id)` ON DELETE CASCADE
  - `jadwal_id` -> `jadwals(id)` ON DELETE SET NULL
  - `user_id` -> `users(id)` ON DELETE SET NULL
- Unique & Index:
  - Unique Compound: `pemeriksaan_apar_periode_unique` (`apar_id`, `periode_tahun`, `periode_bulan`)
  - Index Compound: `pemeriksaan_periode_index` (`periode_tahun`, `periode_bulan`)

**Tabel `pemeriksaan_details`:**
- Primary Key: `id` (INTEGER, AUTOINCREMENT)
- Foreign Keys:
  - `pemeriksaan_id` -> `pemeriksaans(id)` ON DELETE CASCADE
- Kolom Item: `item_nama`, `hasil` (`baik`/`rusak`), `catatan`, `urutan`

> [!NOTE]
> Tabel `pemeriksaans` **tidak menyimpan snapshot `lokasi_id`, `ruangan_id`, `lantai_id`, maupun `gedung_id`**. Penelusuran lokasi pemeriksaan bergantung pada relasi ke tabel induk `apars` via `apar_id`.

### 3.8 Tabel Terkait Lainnya
- **`jadwals`:** Foreign keys: `gedung_id` -> `gedungs(id)` ON DELETE SET NULL, `user_id` -> `users(id)` ON DELETE SET NULL.
- **`jadwal_apars`:** Pivot table. Foreign keys: `jadwal_id` -> `jadwals(id)` ON DELETE CASCADE, `apar_id` -> `apars(id)` ON DELETE CASCADE. Unique compound: (`jadwal_id`, `apar_id`).
- **`tindak_lanjuts`:** Foreign keys: `apar_id` -> `apars(id)` ON DELETE CASCADE, `pemeriksaan_id` -> `pemeriksaans(id)` ON DELETE SET NULL, `pemeriksaan_detail_id` -> `pemeriksaan_details(id)` ON DELETE SET NULL, `user_id` -> `users(id)` ON DELETE SET NULL.
- **`penggantian_apars`:** Foreign keys: `apar_lama_id` -> `apars(id)` ON DELETE CASCADE, `apar_baru_id` -> `apars(id)` ON DELETE CASCADE, `user_id` -> `users(id)` ON DELETE SET NULL.

---

## 4. Entity Relationship Diagram (ERD) Aktual

Diagram relasi entitas aktual di basis data saat ini:

```mermaid
erDiagram
    GEDUNG ||--o{ LANTAI : "1:N (hasMany, CASCADE)"
    LANTAI ||--o{ RUANGAN : "1:N (hasMany, CASCADE)"
    LANTAI ||--o| DENAH : "1:1 (hasOne, CASCADE)"
    
    RUANGAN ||--o{ APAR : "1:N (ruangan_id: NOT NULL CASCADE - HARDCODED 1)"
    LOKASI ||--o{ APAR : "1:N (lokasi_id: NULLABLE SET NULL - 59 K3RS)"
    
    APAR ||--o{ PEMERIKSAAN : "1:N (apar_id: CASCADE)"
    PEMERIKSAAN ||--o{ PEMERIKSAAN_DETAIL : "1:N (pemeriksaan_id: CASCADE)"
    APAR ||--o{ JADWAL_APAR : "1:N (apar_id: CASCADE)"
    JADWAL ||--o{ JADWAL_APAR : "1:N (jadwal_id: CASCADE)"
    JADWAL ||--o{ PEMERIKSAAN : "1:N (jadwal_id: SET NULL)"
    GEDUNG ||--o{ JADWAL : "1:N (gedung_id: SET NULL)"
    
    APAR ||--o{ TINDAK_LANJUT : "1:N (apar_id: CASCADE)"
    APAR ||--o| PENGGANTIAN_APAR : "1:1 (apar_lama_id / apar_baru_id: CASCADE)"
    
    GEDUNG {
        int id PK
        string kode UK
        string nama UK
        string status
    }
    
    LANTAI {
        int id PK
        int gedung_id FK
        int nomor_lantai
        string nama
    }
    
    RUANGAN {
        int id PK
        int lantai_id FK
        string kode
        string nama
        string kategori
    }
    
    LOKASI {
        int id PK
        string nama
        string kode
        string status
    }
    
    APAR {
        int id PK
        string kode UK
        string qr_code UK
        int ruangan_id FK "NOT NULL (100 persen menunjuk ke ID 1)"
        int lokasi_id FK "NULLABLE (59 lokasi operasional K3RS)"
        float koordinat_x "NULL (0 persen terpasang)"
        float koordinat_y "NULL (0 persen terpasang)"
        string jenis_media
        string kapasitas
        string status
    }

    DENAH {
        int id PK
        int lantai_id FK UK
        string file_path
    }
    
    PEMERIKSAAN {
        int id PK
        int apar_id FK
        int periode_tahun
        int periode_bulan
        string hasil_keseluruhan
    }
```

---

## 5. Eloquent Relationship Audit

Pemeriksaan model-model Eloquent di direktori `app/Models/`:

### 5.1 Model `Gedung` (`app/Models/Gedung.php`)
- `lantais()`: `HasMany(Lantai::class, 'gedung_id')->orderBy('nomor_lantai')` (Baris 26:29).
- `ruangans()`: `HasManyThrough(Ruangan::class, Lantai::class, 'gedung_id', 'lantai_id')` (Baris 34:37).
- `getAparsCountAttribute()`: Menghitung APAR via `Apar::whereHas('ruangan.lantai', fn($q) => $q->where('gedung_id', $this->id))->count()` (Baris 42:45).
  - *Temuan:* Karena seluruh 173 APAR memiliki `ruangan_id = 1` (Gedung IGD), maka `$gedungIGD->apars_count` bernilai **173**, sedangkan untuk 16 gedung lainnya selalu bernilai **0**.

### 5.2 Model `Lantai` (`app/Models/Lantai.php`)
- `gedung()`: `BelongsTo(Gedung::class, 'gedung_id')` (Baris 28:31).
- `denah()`: `HasOne(Denah::class, 'lantai_id')` (Baris 36:39).
- `ruangans()`: `HasMany(Ruangan::class, 'lantai_id')->orderBy('nama')` (Baris 52:55).
- `apars()`: `HasManyThrough(Apar::class, Ruangan::class, 'lantai_id', 'ruangan_id')` (Baris 60:63).
  - *Temuan:* Hanya Lantai 1 Gedung IGD yang menghasilkan 173 APAR melalui relasi ini; 39 lantai lainnya menghasilkan 0 APAR.

### 5.3 Model `Ruangan` (`app/Models/Ruangan.php`)
- `lantai()`: `BelongsTo(Lantai::class, 'lantai_id')` (Baris 27:30).
- `apars()`: `HasMany(Apar::class, 'ruangan_id')` (Baris 35:38).
- `getGedungAttribute()`: Mengakses `$this->lantai?->gedung` (Baris 43:46). Tidak ada relasi langsung ke gedung.

### 5.4 Model `Lokasi` (`app/Models/Lokasi.php`)
- `apars()`: `HasMany(Apar::class, 'lokasi_id')` (Baris 38:41).
- *Temuan:* Model ini tidak memiliki relasi ke `Gedung`, `Lantai`, maupun `Ruangan`. Model ini berdiri terisolasi sebagai daftar flat.

### 5.5 Model `Apar` (`app/Models/Apar.php`)
- `lokasi()`: `BelongsTo(Lokasi::class, 'lokasi_id')` (Baris 88:91) [Diberi komentar: "Relasi: APAR berada di satu Titik Lokasi K3RS (Arsitektur Baru)"].
- `ruangan()`: `BelongsTo(Ruangan::class, 'ruangan_id')` (Baris 173:176).
- `getLantaiAttribute()`: `$this->ruangan?->lantai` (Baris 181:184).
- `getGedungAttribute()`: `$this->ruangan?->lantai?->gedung` (Baris 189:192).
- `getLokasiNamaAttribute()`: `$this->lokasi?->nama ?? $this->ruangan?->nama ?? '-'` (Baris 197:200).
- `getLokasiLengkapAttribute()` (Baris 213:224):
  ```php
  if ($this->lokasi) {
      return $this->lokasi->nama;
  }
  $gedungNama = $this->gedung?->nama ?? '-';
  $lantaiNama = $this->lantai?->nama ?? '-';
  $ruanganNama = $this->ruangan?->nama ?? '-';
  return "{$gedungNama}, {$lantaiNama}, {$ruanganNama}";
  ```
  *Temuan Signifikan:* Accessor ini menyembunyikan inkonsistensi data. Jika `lokasi_id` ada, accessor hanya mengembalikan teks nama lokasi K3RS (contoh: "ALEXANDRI Lt 1"), sehingga pengguna di antarmuka tidak menyadari bahwa di kolom basis data, APAR tersebut sebenarnya terhubung ke `Ruang IGD`.

---

## 6. Struktur Lokasi CRUD Audit (UI dan CRUD)

Audit dilakukan terhadap `app/Http/Controllers/LokasiController.php` dan tampilan antarmuka `resources/views/lokasi/index.blade.php`:

### 6.1 Fitur Gedung
- **Tambah Gedung (`storeGedung` L74:L101):** Berfungsi normal. Validasi nama unik (`unique:gedungs,nama`). Otomatisasi pembuatan kode `GDG-{ABBREVIATION}` jika kode dikosongkan.
- **Edit Gedung (`updateGedung` L103:L120):** Berfungsi normal dengan validasi pengecualian ID aktif (`Rule::unique()->ignore`).
- **Hapus Gedung (`destroyGedung` L122:L128):** **SANGAT BERBAHAYA (CRITICAL).** Tidak ada pengecekan apakah gedung memiliki lantai, ruangan, atau APAR terkait. Langsung memanggil `$gedung->delete()`. Karena constraint cascade, menghapus gedung akan memicu penghapusan berantai pada lantai, ruangan, seluruh APAR di dalamnya, dan seluruh riwayat pemeriksaannya.
- **Metrik Ditampilkan:** Menampilkan total gedung terdaftar (saat ini 17 gedung).

### 6.2 Fitur Lantai
- **Tambah Lantai (`storeLantai` L134:L156):** Memerlukan `gedung_id`. Terdapat validasi nomor lantai unik per gedung (`unique:lantais,nomor_lantai` scoped to `gedung_id`).
- **Edit Lantai (`updateLantai` L158:L179):** Berfungsi normal dengan validasi compound unique yang mengecualikan ID aktif.
- **Hapus Lantai (`destroyLantai` L181:L187):** **SANGAT BERBAHAYA (CRITICAL).** Menjalankan `$lantai->delete()`, yang secara kaskade menghapus seluruh ruangan di bawahnya dan seluruh APAR yang terhubung ke ruangan tersebut.
- **Dukungan Jumlah Lantai Fleksibel:** Sistem **sepenuhnya mendukung** gedung dengan jumlah lantai berbeda. Di database saat ini, Gedung Emerald memiliki 5 lantai, Alexandri 4 lantai, Nilam 3 lantai, IRJ 2 lantai, dan Logistik 1 lantai.
- **Penanganan Lantai Tanpa Ruangan:** Sistem mendukung lantai tanpa ruangan (saat ini 35 dari 40 lantai tidak memiliki ruangan).

### 6.3 Fitur Ruangan / Area
- **Tambah Ruangan (`storeRuangan` L193:L216):** Memerlukan `lantai_id`. Validasi nama unik per lantai (`unique:ruangans,nama` scoped to `lantai_id`). Ruangan tidak dapat ditambahkan tanpa memilih lantai.
- **Edit Ruangan (`updateRuangan` L218:L240):** Berfungsi normal.
- **Hapus Ruangan (`destroyRuangan` L242:L248):** **SANGAT BERBAHAYA (CRITICAL).** Tidak ada proteksi terhadap unit APAR yang terpasang di ruangan tersebut. Menghapus ruangan akan memicu `ON DELETE CASCADE` pada tabel `apars`. Menghapus `Ruangan ID 1` (`Ruang IGD`) akan langsung melenyapkan seluruh 173 APAR di sistem beserta seluruh riwayat pemeriksaannya.
- **Penanganan Ruangan Tanpa APAR:** Didukung penuh. 8 ruangan lainnya saat ini memiliki 0 APAR.

---

## 7. APAR Location Relationship Audit

### 7.1 Statistik Record APAR pada Basis Data Aktif
- **Total Unit APAR di Basis Data:** 173 unit.
- **Unit APAR Aktif:** 173 unit (100%).
- **Unit APAR Nonaktif / Soft-Deleted (`deleted_at` NOT NULL):** 0 unit (0%).
- **Kondisi Operasional:**
  - Status `baik`: 172 unit (99.4%).
  - Status `perhatian`: 1 unit (0.6%) -> `APAR-ALX2-01` (terdapat temuan inspeksi perlu tindakan pada 13 Oktober 2026).
  - Status `rusak`: 0 unit.
  - Status `tidak_aktif`: 0 unit.
- **APAR tanpa `ruangan_id`:** 0 unit (karena kolom `NOT NULL`).
- **APAR tanpa `lokasi_id`:** 0 unit.
- **APAR dengan Relasi Ruangan Tidak Sesuai / Dummy:** **173 unit (100%)**. Seluruh APAR menunjuk ke `ruangan_id = 1` (`Ruang IGD`).
- **APAR yang Koordinat Denahnya Belum Terpasang:** **173 unit (100%)**.

### 7.2 Tabel Distribusi APAR Berdasarkan Struktur Fisik Aktual (Hierarki Gedung -> Lantai -> Ruangan)

Berikut adalah tabel audit lengkap dari seluruh gedung, lantai, dan ruangan yang ada di basis data aktif saat ini:

| Gedung | Lantai | Ruangan | Jumlah APAR | Kondisi Relasi Aktual |
|---|---|---|---:|---|
| **Gedung IGD** | Lantai 1 | **Ruang IGD** | **173** | **ANOMALI KRITIS: Seluruh 173 APAR RS terhubung ke sini** |
| Gedung IGD | Lantai 1 | Ruang Triase | 0 | Kosong (0 APAR via `ruangan_id`) |
| Gedung IGD | Lantai 1 | Stroke Center | 0 | Kosong (0 APAR via `ruangan_id`) |
| Gedung IGD | Lantai 2 IGD | *Tidak Ada Ruangan* | 0 | Lantai kosong (belum ada ruangan) |
| Gedung Instalasi Rawat Jalan | Lantai 1 | Karidor | 0 | Kosong (0 APAR via `ruangan_id`) |
| Gedung Instalasi Rawat Jalan | Lantai 1 | Poliklinis | 0 | Kosong (0 APAR via `ruangan_id`) |
| Gedung Instalasi Rawat Jalan | Lantai 2 | Manajemen | 0 | Kosong (0 APAR via `ruangan_id`) |
| Gedung Emerald | Lantai 1 | Karidor | 0 | Kosong (0 APAR via `ruangan_id`) |
| Gedung Emerald | Lantai 2 s.d. 5 | *Tidak Ada Ruangan* | 0 | 4 Lantai kosong |
| Gedung Nilam | Lantai 1 s.d. 3 | *Tidak Ada Ruangan* | 0 | 3 Lantai kosong |
| Gedung Alexandri | Lantai 1 s.d. 4 | *Tidak Ada Ruangan* | 0 | 4 Lantai kosong |
| Gedung Giok | Lantai 1 s.d. 2 | *Tidak Ada Ruangan* | 0 | 2 Lantai kosong |
| Gedung Gudang Logistik | Lantai 1 | *Tidak Ada Ruangan* | 0 | Lantai kosong |
| Gedung IPSRS | Lantai 1 | *Tidak Ada Ruangan* | 0 | Lantai kosong |
| Gedung Gizi | Lantai 1 s.d. 2 | *Tidak Ada Ruangan* | 0 | 2 Lantai kosong |
| Gedung CSSD | Lantai 1 s.d. 3 | *Tidak Ada Ruangan* | 0 | 3 Lantai kosong |
| Gedung IBS | Lantai 1 s.d. 4 | *Tidak Ada Ruangan* | 0 | 4 Lantai kosong |
| Gedung Kumala | Lantai 1 s.d. 5 | *Tidak Ada Ruangan* | 0 | 5 Lantai kosong |
| Gedung Diagnostik | Lantai 1 | Laboratorium | 0 | Kosong (0 APAR via `ruangan_id`) |
| Gedung Diagnostik | Lantai 1 | Radiologi | 0 | Kosong (0 APAR via `ruangan_id`) |
| Gedung Farmasi | Lantai 1 s.d. 2 | *Tidak Ada Ruangan* | 0 | 2 Lantai kosong |
| Gedung Jenazah | Lantai 1 | *Tidak Ada Ruangan* | 0 | Lantai kosong |
| Gedung Dinas | Lantai 1 | *Tidak Ada Ruangan* | 0 | Lantai kosong |
| Gedung Insinerator | Lantai 1 | *Tidak Ada Ruangan* | 0 | Lantai kosong |

### 7.3 Tabel Distribusi APAR Berdasarkan Master Lokasi K3RS (`lokasis` via `lokasi_id`)

Berbeda dengan tabel hierarkis fisik di atas, distribusi APAR berdasarkan master `lokasis` mencerminkan alokasi inventaris K3RS yang sebenarnya:

| ID | Kode | Nama Lokasi K3RS | Status Lokasi | Target APAR | APAR Aktual | Status Verifikasi |
|---:|---|---|:---:|---:|---:|:---:|
| 1 | SEC | POS PANTAU SECURITY | aktif | 1 | 1 | Sesuai |
| 2 | IGD1 | IGD Lt. 1 | aktif | 4 | 4 | Sesuai |
| 3 | STR | STROKE CENTER | aktif | 2 | 2 | Sesuai |
| 4 | IRJ1 | IRJA Lt. 1 | aktif | 6 | 6 | Sesuai |
| 5 | KAS | KASIR | aktif | 1 | 1 | Sesuai |
| 6 | RM | REKAM MEDIS | aktif | 3 | 3 | Sesuai |
| 7 | FIS | FISIOTERAPI | aktif | 1 | 1 | Sesuai |
| 8 | IRJ2 | IRJA LT 2 | aktif | 2 | 2 | Sesuai |
| 9 | KM | KOMITE MEDIK | aktif | 1 | 1 | Sesuai |
| 10 | AUD | AUDITORIUM | aktif | 2 | 2 | Sesuai |
| 11 | MNJ | MANAJEMEN + Rg ARSIP KEPEGAWAIAN | aktif | 4 | 4 | Sesuai |
| 12 | FAR | INSTALASI FARMASI | aktif | 5 | 5 | Sesuai |
| 13 | EMR1 | EMERALD Lt 1 | aktif | 5 | 5 | Sesuai |
| 14 | EMR2 | EMERALD Lt 2 | aktif | 4 | 4 | Sesuai |
| 15 | EMR3 | EMERALD Lt 3 | aktif | 4 | 4 | Sesuai |
| 16 | EMR4 | EMERALD Lt 4 | aktif | 5 | 5 | Sesuai |
| 17 | EMR5 | EMERALD Lt 5 | aktif | 6 | 6 | Sesuai |
| 18 | NLM1 | NILAM Lt 1 | aktif | 4 | 4 | Sesuai |
| 19 | NLM2 | NILAM Lt 2 | aktif | 4 | 4 | Sesuai |
| 20 | NLM3 | NILAM Lt 3 | aktif | 4 | 4 | Sesuai |
| 21 | DRI | DEPO RAWAT INAP | aktif | 1 | 1 | Sesuai |
| 22 | ALX1 | ALEXANDRI Lt 1 | aktif | 5 | 5 | Sesuai |
| 23 | ALX2 | ALEXANDRI Lt 2 | aktif | 5 | 5 | Sesuai |
| 24 | ALX3 | ALEXANDRI Lt 3 | aktif | 5 | 5 | Sesuai |
| 25 | ALX4 | ALEXANDRI Lt 4 | aktif | 5 | 5 | Sesuai |
| 26 | JMR | JAMRUD | kosong | 0 | 0 | Sesuai (Lokasi Kosong) |
| 27 | YKT | YAKUT | kosong | 0 | 0 | Sesuai (Lokasi Kosong) |
| 28 | GIO | GIOK | kosong | 0 | 0 | Sesuai (Lokasi Kosong) |
| 29 | JIW | POLI JIWA DAN PSIKOLOGI | aktif | 3 | 3 | Sesuai |
| 30 | KML1 | KUMALA Lt 1 | aktif | 2 | 2 | Sesuai |
| 31 | KML2 | KUMALA Lt 2 | aktif | 2 | 2 | Sesuai |
| 32 | KML3 | KUMALA Lt 3 | aktif | 2 | 2 | Sesuai |
| 33 | KML4 | KUMALA Lt 4 (OK) s.d. Gedung Mutiara Lt 4 | aktif | 6 | 6 | Sesuai |
| 34 | DOK | DEPO OK | aktif | 1 | 1 | Sesuai |
| 35 | KML5 | KUMALA Lt 5 | aktif | 2 | 2 | Sesuai |
| 36 | ESWL| POLI ESWL | aktif | 1 | 1 | Sesuai |
| 37 | RAD | RADIOLOGI | aktif | 5 | 5 | Sesuai |
| 38 | LAB | LABORATORIUM | aktif | 3 | 3 | Sesuai |
| 39 | MTR1 | MUTIARA Lt 1 HD | aktif | 7 | 7 | Sesuai |
| 40 | MTR2 | MUTIARA Lt 2 Bayi | aktif | 4 | 4 | Sesuai |
| 41 | MTR3 | MUTIARA Lt 3 ICU | aktif | 4 | 4 | Sesuai |
| 42 | LND1 | LAUNDRY LT 1 | aktif | 4 | 4 | Sesuai |
| 43 | CSD2 | CSSD LT 2 | aktif | 3 | 3 | Sesuai |
| 44 | BRN | BURN CENTER | aktif | 1 | 1 | Sesuai |
| 45 | GIZ1 | INSTALASI GIZI LT 1 | aktif | 5 | 5 | Sesuai |
| 46 | GIZ2 | INSTALASI GIZI LT 2 | aktif | 1 | 1 | Sesuai |
| 47 | ITD | IT DALAM | aktif | 1 | 1 | Sesuai |
| 48 | ITL | IT LUAR | aktif | 1 | 1 | Sesuai |
| 49 | LB3 | GUDANG LIMBAH B3 | aktif | 1 | 1 | Sesuai |
| 50 | CSF | C S FREEZEER (SANITASI) | aktif | 1 | 1 | Sesuai |
| 51 | SAN | INSTALASI SANITASI | aktif | 1 | 1 | Sesuai |
| 52 | INC | INCENERATOR | aktif | 3 | 3 | Sesuai |
| 53 | LOG | GUDANG LOGISTIK | aktif | 2 | 2 | Sesuai |
| 54 | K3RS | K3RS | aktif | 1 | 1 | Sesuai |
| 55 | IPSR | IPSRS | aktif | 1 | 1 | Sesuai |
| 56 | GEN | GENZET | aktif | 2 | 2 | Sesuai |
| 57 | JNZ | KAMAR JENAZAH | aktif | 1 | 1 | Sesuai |
| 58 | LFT | LIFT | aktif | 8 | 8 | Sesuai |
| 59 | KTN | KANTIN | kosong | 0 | 0 | Sesuai (Lokasi Kosong) |
| 60 | RSP2 | RESEPCIONIST Lt 2 | aktif | 1 | 1 | Sesuai |
| 61 | GAS | GAS MEDIS (BELAKANG CSSD) | aktif | 1 | 1 | Sesuai |
| 62 | 119 | 119 | aktif | 1 | 1 | Sesuai |
| 63 | GK3 | GUDANG K3RS | stok | 0 | 0 | Sesuai (Gudang Stok) |
| 64 | OKC | OK CITO | aktif | 2 | 2 | Sesuai |
| **TOTAL** | | **64 Lokasi K3RS** | | **173** | **173** | **100% Cocok** |

---

## 8. Master Data dan Seeder Audit

Pemeriksaan terhadap berkas-berkas seeder di `database/seeders/`:

### 8.1 `DatabaseSeeder.php`
- Hanya berisi inisialisasi `User::factory()->create()` untuk akun pengujian default (`test@example.com`).
- Tidak memanggil seeder lokasi maupun APAR secara otomatis saat `php artisan db:seed` dijalankan tanpa argumen kelas.

### 8.2 `LokasiSeeder.php`
- Dibuat pada 3 Oktober 2026 sebagai representasi draf konsultasi awal.
- Menghasilkan 17 record `Gedung`, 39 record `Lantai`, dan 6 record `Ruangan` (Ruang IGD, Stroke Center, Poliklinis, Manajemen, Radiologi, Laboratorium).
- Gedung-gedung bertingkat seperti Emerald (5 lantai), Nilam (3 lantai), Alexandri (4 lantai), IBS (4 lantai), dan Kumala (5 lantai) dibuat strukturnya tanpa ruangan sama sekali.

### 8.3 `LokasiAktualSeeder.php`
- Dibuat pada 12 Oktober 2026 berdasarkan data resmi Tabel 12 Laporan Triwulan Ke-3 K3RS.
- Mengisi tabel `lokasis` dengan tepat 64 record (59 lokasi aktif ber-APAR, 4 lokasi kosong tanpa APAR: Jamrud, Yakut, Giok, Kantin, dan 1 gudang stok: Gudang K3RS).
- Menetapkan kode singkat standar untuk masing-masing lokasi (contoh: `SEC`, `IGD1`, `STR`, `ALX1`).

### 8.4 `AparAktualSeeder.php`
- Dibuat pada 12 Oktober 2026 untuk mengimpor 173 APAR terpasang dengan format kode deterministik `APAR-{KODE_LOKASI}-{NOMOR_URUT}` dan QR code `HAFIS-QR-APAR-{KODE_LOKASI}-{NOMOR_URUT}`.
- **Akar Masalah (Root Cause):**
  - Baris 28:30: `$legacyRuangan = $this->resolveFallbackLegacyRuangan();`
  - Baris 78 & 89: `'ruangan_id' => $legacyRuangan->id,`
  - Baris 149:171:
    ```php
    private function resolveFallbackLegacyRuangan(): Ruangan
    {
        $ruangan = Ruangan::first(); // Mengambil ID 1 (Ruang IGD)
        ...
        return $ruangan;
    }
    ```
  - Seeder ini secara sengaja memasukkan `ruangan_id = 1` ke setiap record APAR baru maupun yang diperbarui demi memenuhi batasan `NOT NULL` pada kolom `apars.ruangan_id`.
  - Seeder ini memiliki metode `purgeLegacyDummyData()` (baris 110:144) yang menghapus record APAR dummy lama (`APAR-IGD-01`, `APAR-NONAKTIF-01`, `APAR-TEST-E`, `Emerald-001`, dll.) jika masih ditemukan.

### 8.5 `AparAtributKoreksiSeeder.php`
- Berfungsi mengoreksi media pemadam dan kapasitas tabung untuk mencocokkan matriks Tabel 12 K3RS (108 unit Powder 6kg, 4 unit Powder 3kg, 3 unit Powder 25kg, 34 unit CO2 3kg, 7 unit CO2 5kg, 17 unit CO2 7kg = Total 173 unit).
- Seeder ini mempertahankan relasi yang sudah ada tanpa mengubah `ruangan_id` maupun `lokasi_id`.

---

## 9. Denah dan Fitur Pendukung Audit

### 9.1 Status Tabel dan Berkas Denah
- Tabel `denahs` menyimpan relasi 1:1 dengan tabel `lantais` (`lantai_id` berstatus `UNIQUE`).
- Di basis data aktif hanya ada **1 record denah**:
  - `id`: 1
  - `lantai_id`: 1 (Gedung IGD, Lantai 1)
  - `file_path`: `denah/denah-lantai-1.svg`
- 39 lantai lainnya di rumah sakit **belum memiliki berkas denah**.

### 9.2 Penempatan Marker APAR (`PenempatanController.php`)
- Marker APAR ditentukan oleh kolom `koordinat_x` dan `koordinat_y` pada tabel `apars` (tipe `NUMERIC(5,2)`, persentase 0.00% s.d. 100.00%).
- Di basis data aktif saat ini:
  - **APAR dengan titik koordinat:** **0 unit (0%)**.
  - **APAR tanpa koordinat:** **173 unit (100%)**.
- **Kerusakan Logika Penempatan:**
  Di `PenempatanController.php` (baris 90:97):
  ```php
  $selectedLantai->load(['denah', 'ruangans.apars' => fn ($q) => $q->orderBy('kode', 'asc')]);
  $allApars = $selectedLantai->ruangans->flatMap->apars;
  $placedApars = $allApars->filter(fn ($a) => $a->has_titik)->values();
  $unplacedApars = $allApars->filter(fn ($a) => ! $a->has_titik)->values();
  ```
  Karena seluruh 173 APAR terhubung ke `Ruangan ID 1` (Lantai 1 Gedung IGD), maka saat petugas membuka denah Lantai 1 Gedung IGD, sistem menampilkan **173 APAR sekaligus** dalam daftar "Belum Ditempatkan", termasuk APAR Gedung Emerald, Nilam, Lift, dan Insinerator. Sebaliknya, saat petugas beralih ke lantai lain, daftar APAR kosong.

### 9.3 Fitur Laporan dan Rekapitulasi (`LaporanController.php`)
- Filter lokasi di `LaporanController.php` (baris 64:75, 204:214) memprioritaskan `lokasi_id` K3RS, dengan fallback ke `gedung_id` dan `lantai_id`.
- Namun, pada fitur Ekspor Laporan Aset APAR (CSV/Excel) pada baris 135:137:
  ```php
  $a->gedung?->nama ?? '-',
  $a->lantai?->nama ?? '-',
  $a->lokasi?->nama ?? $a->ruangan?->nama ?? '-',
  ```
  File hasil unduhan mencantumkan kolom Gedung sebagai **"Gedung IGD"** dan Lantai sebagai **"Lantai 1"** untuk seluruh 173 unit APAR di rumah sakit.

---

## 10. Data Integrity Audit

Pemeriksaan integritas referensial dan data di database aktif menunjukkan hasil sebagai berikut:

| Aspek Integritas | Kondisi Aktual | Keterangan |
|---|:---:|---|
| **Orphan APAR (`ruangan_id` invalid)** | 0 | Seluruh 173 APAR menunjuk ke ID ruangan yang valid (ID 1). |
| **Orphan APAR (`lokasi_id` invalid)** | 0 | Seluruh 173 APAR menunjuk ke ID lokasi K3RS yang valid (ID 1:64). |
| **Orphan Lantai (`gedung_id` invalid)** | 0 | 40 lantai menunjuk ke gedung yang valid. |
| **Orphan Ruangan (`lantai_id` invalid)** | 0 | 9 ruangan menunjuk ke lantai yang valid. |
| **Orphan Denah (`lantai_id` invalid)** | 0 | 1 denah menunjuk ke Lantai 1 Gedung IGD. |
| **Orphan Pemeriksaan (`apar_id` invalid)** | 0 | Seluruh 7 pemeriksaan memiliki induk APAR yang sah. |
| **Orphan Pemeriksaan Detail** | 0 | Seluruh 35 detail pemeriksaan memiliki induk pemeriksaan yang sah. |
| **Duplikasi Kode APAR (`apars.kode`)** | 0 | Seluruh 173 kode bersifat unik. |
| **Duplikasi QR Code (`apars.qr_code`)** | 0 | Seluruh 173 QR code bersifat unik. |
| **Duplikasi Kode Gedung** | 0 | Seluruh 17 kode gedung bersifat unik. |
| **Duplikasi Nomor Lantai per Gedung** | 0 | Tidak ada lantai bernomor sama dalam gedung yang sama. |
| **Duplikasi Nama Ruangan per Lantai** | 0 | Tidak ada nama ruangan duplikat dalam lantai yang sama. |
| **Integritas Riwayat Pemeriksaan Terhadap Lokasi** | Aman | Pemeriksaan terikat pada unit tabung (`apar_id`), bukan pada lokasi. |

---

## 11. Risiko Perubahan Database (*Change Risk Analysis*)

Jika dilakukan migrasi atau rekonsiliasi struktur lokasi, berikut potensi risiko teknis yang wajib dimitigasi:

1. **Risiko Pemusnahan Data Massal Akibat `ON DELETE CASCADE`:**
   Kolom `apars.ruangan_id` dan `lantais.gedung_id` memiliki konstrain `cascadeOnDelete`. Jika tabel ruangan atau lantai dibersihkan menggunakan `delete()` tanpa melepas constraint foreign key terlebih dahulu, seluruh unit APAR dan riwayat pemeriksaannya akan terhapus otomatis.
2. **Risiko Putusnya Alur Kerja Form Tambah/Edit APAR:**
   Jika `lokasi_id` dihapus demi memaksakan hierarki `ruangan_id`, sistem akan kehilangan relasi ke 64 master titik lokasi K3RS yang saat ini menjadi dasar filter modul Inspeksi Bulanan (`PemeriksaanController`).
3. **Risiko Inkonsistensi Periode Pemeriksaan:**
   Terdapat 7 pemeriksaan aktual periode Oktober 2026 di basis data:
   - ID 21: `APAR-119-01` (Lokasi: 119)
   - ID 22: `APAR-ALX1-01` (Lokasi: ALEXANDRI Lt 1)
   - ID 23: `APAR-ALX1-02` (Lokasi: ALEXANDRI Lt 1)
   - ID 24: `APAR-ALX1-03` (Lokasi: ALEXANDRI Lt 1)
   - ID 25: `APAR-ALX1-04` (Lokasi: ALEXANDRI Lt 1)
   - ID 26: `APAR-ALX1-05` (Lokasi: ALEXANDRI Lt 1)
   - ID 27: `APAR-ALX2-01` (Lokasi: ALEXANDRI Lt 2)
   Setiap perubahan skema pada tabel `apars` tidak boleh mengubah nilai `id` atau `kode` dari unit-unit yang telah memiliki riwayat inspeksi ini.
4. **Risiko Desinkronisasi Modul Denah Spasial:**
   Jika denah di masa depan diunggah per lantai, penempatan marker APAR harus membaca ruangan yang berada di lantai tersebut. Selama relasi APAR masih menumpuk di `ruangan_id = 1`, modul denah untuk 16 gedung lainnya akan tetap tidak dapat digunakan.

---

## 12. Daftar Temuan dan Severity

Rekapitulasi seluruh temuan hasil audit:

| ID Temuan | Severity | Ringkasan Temuan | File & Baris Relevan | Dampak | Rekomendasi Penanganan |
|---|:---:|---|---|---|---|
| **FIND-01** | **CRITICAL** | Konstrain `ON DELETE CASCADE` pada `apars.ruangan_id` mengancam kepunahan data saat CRUD Ruangan/Lantai di antarmuka lokasi. | `database/migrations/2026_10_03_052141_create_apars_table.php` (L19)<br>`app/Http/Controllers/LokasiController.php` (L127, L186, L247) | Menghapus Ruang IGD atau Lantai 1 IGD menghapus permanen 173 APAR dan 7 data inspeksi. | Ganti constraint menjadi `restrictOnDelete()` dan tambahkan validasi pencegahan di controller sebelum entitas lokasi dihapus. |
| **FIND-02** | **CRITICAL** | 100% record APAR (173/173) di database aktif terkunci pada `ruangan_id = 1` (`Ruang IGD`). | `database/seeders/AparAktualSeeder.php` (L28:30, L78, L89, L149:171) | Hierarki gedung dan lantai rusak total. Filter gedung dan ekspor laporan tidak akurat. | Jalankan migrasi rekonsiliasi data untuk memetakan setiap APAR ke ruangan/area yang bersesuaian dengan lokasi K3RS fisiknya. |
| **FIND-03** | **HIGH** | Terjadi perpecahan model domain antara arsitektur hierarkis fisik (`gedungs`, `lantais`, `ruangans`) dan arsitektur flat operasional K3RS (`lokasis`). | `app/Models/Apar.php` (L48, L88:91, L173:176, L213:224)<br>`LOCATION_APAR_MASTER.md` (L23:56) | Duplikasi konsep lokasi yang membingungkan alur pengembangan dan query data. | Harmonisasikan master data dengan menghubungkan tabel `lokasis` ke `lantai_id` dan `gedung_id` atau menaikkan ruangan menjadi representasi area lokasi K3RS. |
| **FIND-04** | **HIGH** | Form pendaftaran APAR baru (`/apar/create`) tidak mengisi `lokasi_id`, sehingga APAR baru tidak akan muncul di modul Inspeksi Bulanan K3RS. | `app/Http/Controllers/AparController.php` (L164:225)<br>`app/Http/Controllers/PemeriksaanController.php` (L53:55) | APAR yang didaftarkan lewat web tidak dapat diinspeksi secara berkala. | Perbarui form pendaftaran APAR agar menyertakan pemilihan titik lokasi K3RS atau mengaitkan otomatis ruangan terpilih dengan lokasi K3RS. |
| **FIND-05** | **HIGH** | Modul Penempatan Denah (`/penempatan`) menumpuk seluruh 173 APAR ke Gedung IGD Lantai 1 dan mengosongkan 16 gedung lainnya. | `app/Http/Controllers/PenempatanController.php` (L90:97) | Fitur denah dan penempatan marker tidak dapat dimanfaatkan untuk gedung lain. | Perbaiki relasi ruangan APAR dan query penempatan denah agar memuat APAR sesuai lantai aktualnya. |
| **FIND-06** | **MEDIUM** | Filter laporan berdasarkan Gedung dan Lantai mengembalikan hasil kosong (0 baris) untuk 16 gedung selain Gedung IGD. | `app/Http/Controllers/LaporanController.php` (L67:72, L207:211) | Rekapitulasi laporan per gedung tidak menyajikan data yang benar. | Sesuaikan query laporan dengan relasi lokasi hasil rekonsiliasi. |
| **FIND-07** | **MEDIUM** | 14 dari 17 gedung di database aktif tidak memiliki satu pun ruangan terdaftar. | `database/seeders/LokasiSeeder.php` (L59:207)<br>Tabel `ruangans` aktif | Petugas tidak dapat mendaftarkan APAR baru pada 14 gedung tersebut via `/apar/create`. | Daftarkan master ruangan/area untuk setiap lantai pada 14 gedung tersebut. |
| **FIND-08** | **MEDIUM** | 0 dari 173 APAR (0%) memiliki titik koordinat denah yang terpetakan. | Kolom `apars.koordinat_x` & `koordinat_y` | Peta sebaran spasial APAR rumah sakit belum berfungsi secara operasional. | Siapkan file denah untuk lantai-lantai utama dan input koordinat fisik APAR. |
| **FIND-09** | **LOW** | Terdapat anomali penambahan lantai manual di luar seeder (`Lantai 2 IGD`, ID: 40). | Tabel `lantais` ID 40 | Struktur lantai lokal mengalami *drift* dari berkas seeder awal. | Masukkan penambahan lantai ke seeder resmi agar lingkungan pengembangan konsisten. |

---

## 13. Rekomendasi Perbaikan

Untuk menyelesaikan permasalahan struktural di atas tanpa menimbulkan *downtime* atau kehilangan data inspeksi operasional, direkomendasikan pendekatan rekonsiliasi terencana:

### Langkah 1: Perlindungan Keamanan Basis Data (*Safety First*)
1. Ubah foreign key constraint pada `apars.ruangan_id` dari `cascadeOnDelete()` menjadi `restrictOnDelete()` atau `nullOnDelete()`.
2. Tambahkan pengecekan validasi pertahanan (*defensive check*) di `LokasiController::destroyRuangan`, `destroyLantai`, dan `destroyGedung` untuk menolak penghapusan entitas jika masih terdapat APAR yang bernaung di dalamnya.

### Langkah 2: Unifikasi Model Lokasi (*Location Model Harmonization*)
Satukan paradigma flat `lokasis` dengan hierarki `gedungs` -> `lantais` -> `ruangans`:
- Lakukan pemetaan resmi (*canonical mapping*) ke-64 lokasi K3RS ke dalam struktur Gedung dan Lantai yang sesuai (misalnya: `EMERALD Lt 1` dipetakan ke Gedung Emerald -> Lantai 1 -> Area/Zona Emerald Lt 1).
- Jadikan tabel `ruangans` sebagai representasi Ruangan/Area yang menampung seluruh titik penempatan APAR fisik.
- Atau alternatifnya, jika K3RS menghendaki konsep flat location, jadikan `lokasis` memiliki relasi opsional ke `lantai_id` dan `gedung_id` agar dapat diproyeksikan ke denah lantai tanpa memaksakan entitas ruangan kaku.

### Langkah 3: Rekonsiliasi Data APAR (*Data Migration Script*)
1. Buat script migrasi deterministik satu kali (*one-time migration*) yang mengupdate kolom `ruangan_id` pada 173 APAR dari nilai default `1` ke ID ruangan/area yang bersesuaian dengan `lokasi_id`-nya.
2. Pastikan nilai `id`, `kode`, `qr_code`, dan riwayat 7 pemeriksaan yang telah ada tetap utuh 100%.

### Langkah 4: Sinkronisasi Form Antarmuka (*UI & Form Alignment*)
1. Perbarui form `apar.create` dan `apar.edit` agar dropdown lokasi otomatis mensinkronkan nilai `gedung_id`, `lantai_id`, dan `ruangan_id`/`lokasi_id`.
2. Harmonisasikan query `PemeriksaanController` dan `PenempatanController` agar konsisten membaca relasi lokasi hasil rekonsiliasi.

---

## 14. Kesimpulan Kesiapan Sistem

Berdasarkan hasil audit menyeluruh ini, ditarik kesimpulan resmi:

> [!WARNING]
> **SISTEM SAAT INI BELUM SIAP UNTUK DIGUNAKAN DENGAN HIERARKI LOKASI PENUH (GEDUNG -> LANTAI -> RUANGAN), KARENA TERJADI KEBOCORAN LOGIKA STRUKTURAL DIMANA 100% UNIT APAR DI-HARDCODE KE RUANG IGD.**

Sistem operasional pemeriksaan bulanan saat ini dapat berjalan hanya karena ditopang oleh tabel paralel `lokasis` (64 master lokasi K3RS). Namun, struktur modul lokasi hierarkis (`/lokasi`), modul denah (`/penempatan`), dan filter laporan (`/laporan`) berada dalam kondisi mengalami anomali struktural.

**Pekerjaan audit Fase 1D dinyatakan SELESAI.**
Sesuai instruksi mutlak, tidak ada berkas kode aplikasi maupun rekaman basis data yang diubah selama proses audit ini. Sistem siap memasuki tahap penyusunan rencana aksi rekonsiliasi master lokasi setelah laporan audit ini ditinjau dan disetujui.
