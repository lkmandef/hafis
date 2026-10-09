# Domain Context

## Application Overview

HAFIS (Hospital APAR Fire-safety Information System) is a hospital fire-extinguisher (APAR) asset management system used by the K3RS (Keselamatan dan Kesehatan Kerja Rumah Sakit — Hospital Occupational Health and Safety) unit. It tracks every portable fire extinguisher unit across a hospital's buildings, manages periodic inspection schedules, records physical inspection checklists, logs follow-up maintenance actions, and documents unit replacements. The system covers the full lifecycle of an APAR unit from registration to decommissioning. It operates in Indonesian and is designed for a hospital environment with a fixed hierarchy of buildings, floors, and rooms.

---

## Core Domain Model

**Apar** — a single physical fire extinguisher unit  
- `kode` (unique, human-assigned, e.g. `APAR-IGD-01`): the label on the physical unit  
- `qr_code` (unique, system-generated, format `HAFIS-QR-{CLEANCODE}-{RANDOM6}`): what the QR sticker encodes  
- `nomor_register`: optional external registration number (from a procurement or compliance register)  
- `ruangan_id`: the room where the unit is physically mounted — the only location field; building and floor are inferred upward through `ruangan → lantai → gedung`  
- `posisi_detail`: free-text description of exact position within the room (e.g. "Dinding barat samping pintu masuk")  
- `koordinat_x`, `koordinat_y`: pixel coordinates of the unit's marker on the floor-plan image, nullable when not yet plotted  
- `jenis_media`: type of extinguishing agent — one of: Dry Chemical Powder, CO2 (Carbon Dioxide), Foam (AFFF), Clean Agent, Wet Chemical  
- `kapasitas`: capacity as a free-text string (e.g. "6 kg", "5 kg")  
- `tanggal_kadaluarsa`: expiry date of the extinguishing medium or cylinder  
- `status`: condition state — `baik` | `perhatian` | `rusak` | `tidak_aktif`  
- `replaced_by_apar_id` / `replaces_apar_id`: self-referential FKs forming the replacement chain  
- `alasan_nonaktif`: reason for deactivation, most commonly `sudah_diganti` (replaced by a new unit)  
- Uses `SoftDeletes` — deleted records are hidden from normal views but retained for audit

**Gedung** — a hospital building  
- `kode` (unique, auto-generated from abbreviation if not supplied, e.g. `GDG-RS`)  
- `status`: `aktif` | `nonaktif`  
- Contains `Lantai` records

**Lantai** — a floor within a building  
- `nomor_lantai` (integer): unique per building  
- `nama`: display label (e.g. "Lantai 1 Poliklinik")  
- Can have one optional `Denah` (floor-plan image)

**Ruangan** — a room or area on a floor  
- `nama`: unique per floor  
- `kode`: optional short code (e.g. `R-IGD-01`)  
- `kategori`: free-text category (e.g. `rawat_inap`, `pelayanan`, `umum`) — not an enum; inferred from test data

**Denah** — a floor-plan image for a floor  
- `file_path`, `mime_type`, `file_size`: uploaded image file  
- One per floor; uploading a new one replaces the old one (inferred from `DenahController::store`)  
- APAR units are plotted on this image using `koordinat_x` / `koordinat_y` pixel coordinates

**Jadwal** — a named inspection schedule  
- `tipe_target`: `semua` (all active APAR), `gedung` (all APAR in one building), `custom` (manually selected APAR)  
- `status`: `draft` | `belum_dimulai` | `berjalan` | `selesai` | `dibatalkan`  
- Links to APAR via pivot table `jadwal_apars` with per-unit `status_pemeriksaan`: `belum_diperiksa` | `sudah_diperiksa`  
- `dibuat_oleh`: free-text name of the schedule creator (not a User FK — unauthenticated field)

**Pemeriksaan** — a completed physical inspection of one APAR unit  
- `jadwal_id`: nullable — inspections can be ad-hoc (not tied to a schedule)  
- `pemeriksa_nama`: free-text name of the inspector  
- `hasil_keseluruhan`: `baik` | `perlu_tindakan`  
- `status`: always `selesai` once stored (no draft state in practice; the `draft` value exists in the migration comment but is not used)  
- `foto_path`: optional documentary photo  
- Contains multiple `PemeriksaanDetail` line items

**PemeriksaanDetail** — one line item in an inspection checklist  
- `item_nama`: the checklist question text (copied from `Pemeriksaan::DEFAULT_CHECKLIST_ITEMS` constant at submission time)  
- `hasil`: `baik` | `tidak_baik` | `tidak_diperiksa`  
- `urutan`: display order index  
- Can be linked to one or more `TindakLanjut` records

**TindakLanjut** — a follow-up maintenance or remediation action  
- Links to both `Pemeriksaan` (the parent inspection) and optionally `PemeriksaanDetail` (the specific finding)  
- Can also be created independently without a parent inspection  
- `jenis_tindakan`: `perbaikan` | `pengisian_ulang` | `pembersihan` | `penggantian_komponen` | `pemeriksaan_ulang` | `lainnya`  
- `prioritas`: `rendah` | `sedang` | `tinggi`  
- `status`: `belum_ditindaklanjuti` | `dalam_proses` | `selesai`  
- `petugas_pelaksana`: free-text name of the technician (not a User FK)

**PenggantianApar** — a historical record of one APAR being replaced by another  
- Links `apar_lama_id → apar_baru_id`  
- `alasan`: `rusak` | `tidak_layak` | `kadaluarsa` | `hasil_pemeriksaan` | `penggantian_unit` | `lainnya`  
- This record is separate from the `Apar` model's own FK fields — both exist for different query purposes

---

## User Roles

<!-- TODO: add domain context — no authentication or role system is currently implemented -->

The application has no login or role enforcement. All routes are publicly accessible. However, the domain implies at least two actors based on field names and workflow design:

| Actor | Role in system | Evidence |
|-------|---------------|----------|
| Petugas K3RS | Creates APAR records, manages schedules, views reports | `dibuat_oleh` default value, `pemeriksa_nama` default `'Petugas K3RS'` |
| Teknisi / Petugas Lapangan | Performs physical inspections, executes maintenance | `petugas_pelaksana` field on `TindakLanjut`, `petugas_nama` on `PenggantianApar` |

In the current codebase these are free-text names, not authenticated user records.

---

## Business Rules

**Inspection rules**
1. An APAR with `status = tidak_aktif` cannot be inspected. Attempts to open the inspection form redirect to the APAR detail page with a warning. (Source: `PemeriksaanController::mulai()`, `create()`, `store()`)
2. If any single checklist item result is `tidak_baik`, the overall inspection result (`hasil_keseluruhan`) is automatically forced to `perlu_tindakan`, regardless of a manual override. (Source: `PemeriksaanController::store()` L171–179)
3. When `hasil_keseluruhan = perlu_tindakan` and the APAR's current status is `baik`, the APAR status is automatically upgraded to `perhatian`. Status is never automatically downgraded by an inspection — only upward transitions happen automatically. (Source: `PemeriksaanController::store()` L213–217)
4. An APAR can only be inspected once per `Jadwal`. A second attempt to inspect the same APAR on the same schedule redirects to the first inspection's result page. (Source: `PemeriksaanController::mulai()`, `create()`, `store()`)
5. An APAR can be inspected across multiple schedules. Each produces a separate `Pemeriksaan` record with its own checklist.
6. Ad-hoc inspections (no `jadwal_id`) are allowed. They do not affect any schedule's progress or status.

**Schedule rules**
7. When the last uninspected APAR in a `Jadwal` is inspected, the `Jadwal.status` is automatically set to `selesai`. (Source: `PemeriksaanController::store()` L231–237)
8. `Jadwal::sinkronkanStatus()` auto-transitions status between `belum_dimulai` and `berjalan` based on whether today is before or on/after `tanggal_mulai`. This runs on every call to the jadwal index page.
9. A jadwal in `selesai` or `dibatalkan` status is terminal — `sinkronkanStatus()` will not modify it.
10. `Apar::getActiveJadwalAttribute()` returns the running schedule (`status = berjalan`) where the APAR is still `belum_diperiksa`. If the APAR is already `sudah_diperiksa` on all running schedules, it falls back to the most recent running schedule.

**Replacement rules**
11. An APAR can only be replaced once. Once `replaced_by_apar_id` is set (or `is_replaced` is true), the replacement form is blocked. (Source: `PenggantianAparController::create()`, `store()`)
12. An APAR with `status = tidak_aktif` cannot be replaced. (Source: `PenggantianAparController::create()`)
13. Unit replacement is atomic: the new APAR is created, the old APAR is deactivated (`status = tidak_aktif`, `alasan_nonaktif = sudah_diganti`), and the `PenggantianApar` record is created in a single `DB::transaction`. (Source: `PenggantianAparController::store()`)
14. The new APAR always starts with `status = baik` regardless of the reason for the replacement. (Source: `PenggantianAparController::store()` L208)
15. When a replacement happens, the new APAR is automatically added to any active (`berjalan`) schedule where the old APAR was still `belum_diperiksa`. The old APAR's pivot note is updated to record the replacement. (Source: `PenggantianAparController::store()` L234–253)
16. Floor-plan coordinates can optionally be transferred from the old APAR to the new one (`salin_titik_denah` checkbox). If transferred, the old APAR's coordinates are cleared so one physical location has only one active unit marker. (Source: `PenggantianAparController::store()` L183–193)
17. After replacement, the old APAR record is never deleted — it remains in the database as a historical record with `status = tidak_aktif`. Its full inspection and maintenance history remains visible on its detail page. (Source: `PenggantianAparTest::test_b_histori_lama_tetap_ada()`)

**QR code rules**
18. Each APAR has two identifiers: a human-readable `kode` (e.g. `APAR-IGD-01`) and a system-generated `qr_code` (e.g. `HAFIS-QR-APARIGD01-XYZ123`). The QR sticker encodes the `qr_code` value, not the `kode`.
19. `ScanController::cari()` resolves a scan payload against `qr_code`, `kode`, and `nomor_register` (case-insensitive). It also handles full URL payloads (e.g. if a QR code encodes a URL instead of a raw identifier).

**Location rules**
20. Location is hierarchical and strictly enforced: `Ruangan` must belong to the selected `Lantai`, and `Lantai` must belong to the selected `Gedung`. Mismatches produce validation errors. (Source: validation closures in `AparController` and `PenggantianAparController`)
21. `nomor_lantai` is unique per building. `Ruangan.nama` is unique per floor.

---

## User Workflows

**1. Register a new APAR**  
Create the location structure first (`Gedung → Lantai → Ruangan` via `/lokasi`), then register the APAR at `/apar/create`. The system generates a `qr_code` identifier. Print the QR sticker from `/apar/{id}/cetak-qr` and affix it to the physical unit.

**2. Plot APAR on floor plan**  
Upload a floor-plan image for a floor at `/lokasi`. Then go to `/penempatan` to place APAR unit markers on the image by clicking to set `koordinat_x` / `koordinat_y`. Each floor can have one denah image.

**3. Schedule an inspection round (Jadwal)**  
Create a `Jadwal` at `/jadwal/create`, specifying date range and target scope (`semua` / `gedung` / `custom`). The schedule remains in `belum_dimulai` until its start date, then auto-transitions to `berjalan`.

**4. Inspect an APAR unit (core workflow)**  
Inspector scans the QR sticker using the camera at `/scan`, or manually enters the `kode`. The system identifies the unit and its active schedule, then opens the preparation page (`/pemeriksaan/mulai`). The inspector confirms and submits the 10-item physical checklist at `/pemeriksaan/create`. On save:
- The inspection record is created with all checklist items
- The APAR's `tanggal_pemeriksaan_terakhir` is updated
- If any item is `tidak_baik`, APAR status upgrades to `perhatian`
- The pivot entry in `jadwal_apars` is marked `sudah_diperiksa`
- If all APAR in the schedule are now `sudah_diperiksa`, the Jadwal closes automatically

**5. Record a follow-up action (Tindak Lanjut)**  
From an inspection result or independently, create a `TindakLanjut` at `/tindak-lanjut/create`. Record the type of action, the technician's name, and the date. Update status to `dalam_proses` then `selesai` as work progresses.

**6. Replace a unit (Penggantian)**  
From the APAR detail page, click "Ganti Unit". Enter the new unit's details. On save, the old unit is deactivated, the new unit is registered, the replacement event is recorded, and the new unit inherits any open schedule assignments.

---

## Domain Terminology

| Indonesian term | English equivalent | Notes |
|-----------------|-------------------|-------|
| APAR | Portable fire extinguisher | Alat Pemadam Api Ringan |
| K3RS | Hospital H&S unit | Keselamatan dan Kesehatan Kerja Rumah Sakit |
| Jadwal | Inspection schedule | A named round covering multiple APAR units |
| Pemeriksaan | Physical inspection | A completed checklist for one unit |
| Temuan | Finding / defect | A checklist item with `hasil = tidak_baik` |
| Tindak Lanjut | Follow-up action / maintenance | Remediation work triggered by a finding |
| Penggantian | Replacement | Retiring one APAR and registering a successor |
| Denah | Floor plan | An uploaded image of a building floor |
| Penempatan | Placement / positioning | The act of marking APAR coordinates on the denah |
| Gedung | Building | Top level of the location hierarchy |
| Lantai | Floor | Second level; identified by `nomor_lantai` (integer) |
| Ruangan | Room or area | Lowest level of the location hierarchy |
| Kode | Unit code | Human-assigned label, on the physical unit |
| QR Code | QR identifier | System-generated, encoded in the sticker |
| Status: `baik` | Good condition | Default status for active units |
| Status: `perhatian` | Needs attention | Auto-set when a `tidak_baik` finding is recorded |
| Status: `rusak` | Damaged | Manual escalation beyond `perhatian` |
| Status: `tidak_aktif` | Decommissioned | Set when a unit is replaced or manually deactivated |
| Sudah Diganti | Already replaced | Specific deactivation reason distinguishing replacement from other causes |
| Hasil Keseluruhan | Overall inspection result | `baik` or `perlu_tindakan` |
| Pemeriksa | Inspector | Free-text name on the inspection record |
| Petugas Pelaksana | Maintenance technician | Free-text name on the follow-up record |
| Kadaluarsa | Expired | Medium or cylinder has passed its expiry date |

---

## What This Application Does Not Do

- **No authentication or user login.** There are no login pages, no sessions, and no role checks. The `User` model and `user_id` FKs exist but are populated only when `auth()->id()` is non-null, which is always null in the current setup.
- **No automated inspection reminders or alerts.** No email, notification, or push system exists.
- **No APAR condition escalation workflow.** There is no path in the code to auto-escalate an APAR from `perhatian` to `rusak` — that transition is manual only.
- **No multi-hospital support.** The hierarchy is flat: one application instance = one hospital.
- **No import/export from external asset systems.** APAR records must be entered manually.
- **No checklist template management.** The 10 checklist items are hardcoded as `Pemeriksaan::DEFAULT_CHECKLIST_ITEMS`. Users cannot add, remove, or rename checklist items without a code change.
- **No QR code scanning of external formats.** The scanner resolves the payload against internal `qr_code`, `kode`, and `nomor_register` fields; it does not interpret external barcodes or third-party RFID data.
- **No APAR soft-delete restore UI.** SoftDeletes is implemented on `Apar` but no restore route or UI exists.
- **No CSV/Excel import.** `LaporanController` provides streaming CSV export. Import is absent.

---

## Integration Points

| Service | Purpose | Package |
|---------|---------|---------|
| BaconQrCode | SVG QR code generation | `bacon/bacon-qr-code ^3.1` |
| jsQR (JS library) | In-browser QR code decoding from camera feed | `jsqr ^1.4.0` in `package.json` |
| Laravel Multiplex (optional) | <!-- TODO: verify --> WebSocket / Echo broadcasting; listed as `optionalDependencies` in `package.json` — not confirmed in use | `@laravel/multiplex ^0.4.1` |

No third-party APIs, cloud services, or databases beyond the local SQLite file are integrated.
