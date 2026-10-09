# Architecture

## System Overview

HAFIS (Hospital APAR Fire-safety Information System) is a Laravel 13 server-rendered web application for managing portable fire extinguisher (APAR) assets in a hospital. It follows a layer-first organization: controllers, models, views, and services are grouped by type under their respective Laravel conventional directories, not by feature. Data flows from the browser through named routes → controllers → Eloquent models → Blade views. All state is in the database; there is no client-side state management layer. The frontend is plain Blade with Tailwind CSS v4 compiled by Vite, plus vanilla JS for interactive widgets (QR scanner, floor-plan markers).

**Runtime:** Laravel 13.34.0 • PHP 8.5.10 • Vite 8 • Tailwind CSS 4

---

## Folder Structure

```
app/
├── Http/
│   └── Controllers/        # One controller per module; no sub-namespaces
├── Models/                 # Eloquent models, accessors, and relationship definitions
├── Providers/              # AppServiceProvider only
└── Services/               # Stateless utility classes (currently: QrCodeService)

resources/
├── views/
│   ├── components/         # Anonymous Blade components (badge, button, card, etc.)
│   │   └── layouts/        # app.blade.php (main shell), print.blade.php
│   ├── apar/               # APAR master data views (index, create, edit, show, cetak-qr)
│   ├── jadwal/             # Inspection schedule views
│   ├── pemeriksaan/        # Physical inspection views
│   ├── tindak-lanjut/      # Follow-up & maintenance views
│   ├── penggantian/        # APAR replacement views
│   ├── laporan/            # Reporting & recap views
│   ├── scan/               # QR scan views
│   ├── lokasi/             # Location structure views (gedung, lantai, ruangan)
│   ├── penempatan/         # Floor-plan placement views
│   └── dashboard.blade.php # Dashboard (single file, all sections)
├── css/                    # Tailwind entry point
└── js/
    └── app.js              # JS entry point (minimal; per-view JS is inline in Blade)

database/
├── migrations/             # Standard Laravel timestamp-prefixed migrations
├── factories/              # UserFactory only (other models created directly in tests)
└── seeders/

routes/
└── web.php                 # All routes in one file, grouped by prefix/module

tests/
├── Feature/                # HTTP-level feature tests (one file per module)
└── Unit/                   # (empty)
```

---

## Layer Rules

- **Controllers** may import Models, Services, and Laravel facades. They do not import other controllers.
- **Models** may import other Models (for relationships) and Services (e.g., `QrCodeService` in `Apar`). They do not import Controllers.
- **Services** are stateless and have no imports from `App\Http` or `App\Models` — they receive plain scalar arguments.
- **Blade views** receive data only via controller `compact()` calls; they do not query the database directly.
- There is no repository layer. Controllers call Eloquent directly.

---

## Data Flow

```
Browser request
    ↓
routes/web.php  (named route → Controller@method)
    ↓
Controller      (validates input, queries Eloquent with eager loads)
    ↓
Eloquent Model  (relationships, accessors, SoftDeletes)
    ↓ (read)              ↑ (write: create/update/delete)
database/database.sqlite
    ↓
Controller returns view('module.action', compact(...))
    ↓
Blade view      (uses anonymous components, accesses model accessors)
    ↓
HTML response
```

For QR generation: `Apar::getQrSvgAttribute()` delegates to `QrCodeService::generateSvg()`, which is called inline in Blade.

---

## Routing Conventions

All routes are defined in `routes/web.php` with no authentication middleware. Routes are organized in named prefix groups:

| Prefix | Name prefix | Controller |
|--------|-------------|------------|
| `/` | `dashboard` | `DashboardController` |
| `/apar` | `apar.` | `AparController` |
| `/jadwal` | `jadwal.` | `JadwalController` |
| `/scan` | `scan.` | `ScanController` |
| `/pemeriksaan` | `pemeriksaan.` | `PemeriksaanController` |
| `/tindak-lanjut` | `tindak-lanjut.` | `TindakLanjutController` |
| `/penggantian` | `penggantian.` | `PenggantianAparController` |
| `/laporan` | `laporan.` | `LaporanController` |
| `/lokasi` | `lokasi.` | `LokasiController` + `DenahController` |
| `/penempatan` | `penempatan.` | `PenempatanController` |

Several legacy URL aliases (e.g., `/riwayat → /laporan`, `/apar/detail → /apar/{first}`) are defined with inline closure redirects.

Route parameters use both `{id}` (resolved manually in the controller, accepting numeric ID or `kode` string) and Eloquent route model binding (`{jadwal}`, `{pemeriksaan}`, `{tindakLanjut}`). <!-- TODO: verify — binding approach is inconsistent across controllers -->

---

## Domain Model

The core entity is **Apar** (fire extinguisher unit). All other modules relate to it:

```
Gedung (building)
  └── Lantai (floor)          belongsTo Gedung
        └── Ruangan (room)    belongsTo Lantai
              └── Apar        belongsTo Ruangan
                    ├── Jadwal (inspection schedule)   BelongsToMany via jadwal_apars pivot
                    ├── Pemeriksaan (inspection log)   HasMany
                    ├── TindakLanjut (follow-up)       HasMany
                    ├── PenggantianApar (replacement)  HasOne (keluar) / HasOne (masuk)
                    └── Denah (floor plan image)       via Lantai → HasOne
```

`Apar` uses `SoftDeletes`. No other model does.

`PenggantianApar` links `apar_lama_id → apar_baru_id` and cross-updates `Apar.replaced_by_apar_id` / `Apar.replaces_apar_id` on the related units.

---

## Status State Machines

**Apar.status** (string column):
- Values: `baik`, `perhatian`, `rusak`, `tidak_aktif`
- Transitions are manual via form update. No enforced state machine.
- Constants: `Apar::STATUS_BAIK`, `STATUS_PERHATIAN`, `STATUS_RUSAK`, `STATUS_TIDAK_AKTIF`

**Jadwal.status**:
- Values: `draft`, `belum_dimulai`, `berjalan`, `selesai`, `dibatalkan`
- `Jadwal::sinkronkanStatus()` auto-transitions between `belum_dimulai` and `berjalan` based on `tanggal_mulai`. Called by controllers when displaying a jadwal.

**TindakLanjut.status**:
- Values: `belum_ditindaklanjuti`, `dalam_proses`, `selesai`
- Constants: `TindakLanjut::STATUS_BELUM_DITINDAKLANJUTI`, `STATUS_DALAM_PROSES`, `STATUS_SELESAI`

---

## View Layer Conventions

- Layout shell: `resources/views/components/layouts/app.blade.php`. All views use `<x-layouts.app>`.
- Anonymous Blade components in `resources/views/components/`: `<x-badge>`, `<x-button>`, `<x-card>`, `<x-input>`, `<x-select>`, `<x-textarea>`, `<x-stat-card>`, `<x-table>`, `<x-page-header>`, `<x-sidebar>`, `<x-topbar>`.
- Print layout: `resources/views/components/layouts/print.blade.php` — used by `apar.cetak-qr`.
- Module views mirror the controller action names: `index`, `create`, `edit`, `show` under `resources/views/{module}/`.

---

## Key Conventions

| Concern | Convention |
|---------|------------|
| File naming | PascalCase for PHP classes; kebab-case for Blade view files |
| Model constants | `STATUS_*` and `*_OPTIONS` as `public const` on the model |
| Accessors | Defined as `get{Name}Attribute()` (Laravel 8 style), not the newer `Attribute::make()` |
| Validation | Inline in controller methods via `$request->validate([...], [...])` with Indonesian error messages |
| Flash messages | `->with('success', '...')` on redirects; displayed in the layout shell |
| Eager loading | Always specified at the controller call site; no global `$with` on models |
| QR identifiers | Format: `HAFIS-QR-{CLEANCODE}-{RANDOM6}` — generated in `AparController::store()` |
| Tests | PHPUnit feature tests; `RefreshDatabase`; models created inline (no domain factories) |
| Localization | UI labels and validation messages are in Indonesian; no `lang/` files used |
| Database | SQLite (`database/database.sqlite`) for all environments |
