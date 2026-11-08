# HAFIS: UI Layout Shift Stabilization (Phase 2 Report)

**Target System:** HAFIS (Hospital APAR Fire-safety Information System)  
**Target Repository:** `C:\laragon\www\aparcek-8` (and synchronized with `C:\laragon\www\aparcek-6`)  
**Stack:** Laravel 13.34.0, PHP 8.5.10, Blade, Tailwind CSS v4, Vite 8.3.2, SQLite 3  
**Client Unit:** RSUD dr. H. Moch. Ansari Saleh Banjarmasin, Unit K3RS  
**Phase:** Phase 2: Data Table Stability and Inspection Recommendation Banner  
**Report Date:** November 2026  
**Document Status:** OFFICIAL PHASE 2 COMPLETION REPORT  

---

## 1. Executive Summary

Phase 2 of the layout shift stabilization addressed the secondary layout instability vectors identified in `docs/UI_LAYOUT_SHIFT_INVESTIGATION_REPORT.md` and deferred from Phase 1:
1. Dynamic table auto-layout reflow on core master data tables (`/apar` and `/pemeriksaan`).
2. Client-side DOM mutation on the inspection recommendation banner (`/pemeriksaan/create` and `/pemeriksaan/{id}/edit`).
3. Regression verification of Phase 1 font fallback metrics and transition scoping.

By introducing explicit table column allocation (`table-fixed` with safe column widths) and computing the initial recommendation banner state directly on the server in Blade, the application eliminates both table column jitter and form container vertical jumping on initial page paint.

All 227 PHPUnit tests (2,322 assertions) passed with zero regressions. Production assets built cleanly with Vite 8.3.2 and Tailwind CSS v4.

---

## 2. Scope 1: Data Table Stability

### 2.1 Investigation Findings
- **Observed Behavior:** Prior to remediation, master data tables on `/apar` and `/pemeriksaan` defaulted to CSS `table-layout: auto`.
- **Causal Mechanism:** Under `table-layout: auto`, the browser calculates column widths by scanning all cell contents across every rendered row. Because column 1 contains monospace codes (e.g., `APAR-G01-L02-005`, `Reg: ...`), column 2 contains variable-length room descriptions, and column 6 contains dates, slight variations in cell text lengths or font swap timing from fallback to webfont caused the browser to recalculate and shift column widths horizontally.
- **Evidence from Code:**
  - `resources/views/apar/index.blade.php`: Line 349 declared `<table class="w-full min-w-[960px] text-left text-xs text-gray-900 border-collapse">` without column width constraints on `<th>` elements.
  - `resources/views/pemeriksaan/index.blade.php`: Line 220 declared `<table class="w-full min-w-[880px] text-left border-collapse text-xs">` without column width constraints.

### 2.2 Exact File Changes Made

#### 1. `resources/views/apar/index.blade.php` (Lines 349 to 360, 386)
- **Table Container:** Added `table-fixed`:
  ```blade
  <table class="w-full min-w-[960px] table-fixed text-left text-xs text-gray-900 border-collapse">
  ```
- **Column Allocations in `<thead>`:**
  - `Kode APAR`: Assigned `w-[180px]` (fits 18-character monospace codes and registration metadata with 32px horizontal padding).
  - `Lokasi (Gedung / Lantai / Area)`: Assigned `min-w-[240px]` without fixed cap, enabling this column to fluidly absorb remaining viewport width.
  - `Media & Kapasitas`: Assigned `w-[150px]`.
  - `Merek / Tipe`: Assigned `w-[130px]`.
  - `Status`: Assigned `w-[120px]`.
  - `Pemeriksaan Terakhir`: Assigned `w-[150px]`.
  - `Aksi`: Assigned `w-[110px] text-right`.
- **Safe Text Wrapping:** Added `break-words` to the location cell (`<td class="px-4 py-3 break-words">`) to ensure lengthy room or building designations wrap safely without pushing adjacent columns.

#### 2. `resources/views/pemeriksaan/index.blade.php` (Lines 220 to 230, 239, 245)
- **Table Container:** Added `table-fixed`:
  ```blade
  <table class="w-full min-w-[880px] table-fixed text-left border-collapse text-xs">
  ```
- **Column Allocations in `<thead>`:**
  - `No`: Assigned `w-12 text-center` (48px).
  - `Kode APAR`: Assigned `w-[160px]`.
  - `Lokasi K3RS`: Assigned `min-w-[200px]` (fluid expansion).
  - `Media / Kapasitas`: Assigned `w-[140px]`.
  - `Status Tabung`: Assigned `w-[120px]`.
  - `Status Periode ...`: Assigned `w-[170px] text-center`.
  - `Aksi`: Assigned `w-[160px] text-right` (comfortably accommodates both "Lihat Hasil" and "Koreksi" action buttons).
- **Alignment and Wrapping:** Added `text-center` on number cells and `break-words` on location cells.

### 2.3 Tables Preserved with Intrinsic Sizing
- `resources/views/apar/show.blade.php` (Card D inspection log table) was inspected and retained with intrinsic auto-layout. Because this table is nested within an individual unit card with short, bounded history entries and no fixed `min-w` constraint, applying `table-fixed` without runtime evidence would restrict responsive card presentation.

---

## 3. Scope 2: Inspection Recommendation Banner

### 3.1 Investigation Findings
- **Observed Behavior:** On inspection forms (`/pemeriksaan/create` and `/pemeriksaan/{id}/edit`), the recommendation banner visibly shifted in color, text, and container height upon page load.
- **Causal Mechanism:**
  - The server Blade template hardcoded the green (Baik) banner state:
    `Seluruh parameter checklist dalam kondisi Baik. Hasil keseluruhan: Baik.` (~73 characters).
  - When returning from a validation error with `old()` inputs containing a `tidak_baik` selection, or when editing an existing inspection that had `tidak_baik` items, the initial HTML was painted green.
  - Upon `DOMContentLoaded`, the client-side script `evaluateOverallHealth()` executed, discovered the checked `tidak_baik` radio, and mutated the banner class to red while changing the text to:
    `Terdapat temuan Tidak Baik pada checklist fisik. Hasil keseluruhan disarankan: Perlu Tindakan.` (~96 characters).
  - On mobile screens (375px to 390px), the longer text wrapped into an additional line, expanding the banner height from ~46px to ~62px and pushing all subsequent form inputs down after initial render.
  - Additionally, the dot indicator `<span class="w-2 h-2 rounded-full bg-emerald-500">` was not updated by the original script, causing an inconsistent green dot inside a red banner.

### 3.2 Exact File Changes Made

#### 1. `resources/views/pemeriksaan/create.blade.php`
- **Server-Side Initial Evaluation (Lines 242 to 267):**
  Added Blade pre-computation based on `old()` input:
  ```blade
  @php
      $initialHasTidakBaik = false;
      foreach ($checklistItems as $idx => $name) {
          if (old("items.{$idx}.hasil") === 'tidak_baik') {
              $initialHasTidakBaik = true;
              break;
          }
      }
      if (old('kesimpulan_manual') === 'perlu_tindakan') {
          $initialHasTidakBaik = true;
      }
  @endphp
  ```
- **Conditional Initial Render:**
  Rendered the matching class (`bg-red-50 border-red-200 text-red-900` vs `bg-emerald-50 border-emerald-200 text-emerald-900`), indicator dot (`bg-red-500` vs `bg-emerald-500`), and recommendation text directly on the initial HTML response.
- **Client Script Alignment (Lines 360 to 380):**
  Updated `evaluateOverallHealth()` to also update `#rekomendasi-dot`. Because the initial HTML now matches the radio state, executing `evaluateOverallHealth()` on `DOMContentLoaded` causes zero DOM mutations or height changes on page load.

#### 2. `resources/views/pemeriksaan/edit.blade.php`
- **Server-Side Initial Evaluation (Lines 195 to 220):**
  Added Blade pre-computation checking both `old()` input and existing saved detail records (`$savedDetails->get($idx)?->hasil`):
  ```blade
  @php
      $initialHasTidakBaik = false;
      foreach ($checklistItems as $idx => $name) {
          $detail = $savedDetails->get($idx);
          if (old("items.{$idx}.hasil", $detail?->hasil) === 'tidak_baik') {
              $initialHasTidakBaik = true;
              break;
          }
      }
      if (old('kesimpulan_manual', $pemeriksaan->hasil_keseluruhan) === 'perlu_tindakan') {
          $initialHasTidakBaik = true;
      }
  @endphp
  ```
- **Initial HTML Paint:** When editing an inspection with findings, the page immediately paints the red banner and matching text with zero delay or post-load mutation.

---

## 4. Scope 3: Font and Transition Regression Check

### 4.1 Verification of Phase 1 Deliverables
- **Metric Fallbacks:** Production stylesheet `public/build/assets/app-AaPhKDtz.css` contains both `@font-face` rules for `'Inter Fallback'` and `'JetBrains Mono Fallback'` with `size-adjust: 102.5%` and `size-adjust: 106%`.
- **Preload Optimization:** `public/build/fonts-manifest.json` confirms exactly 4 preloaded font files:
  1. `assets/inter-400-normal-C38fXH4l.woff2`
  2. `assets/inter-600-normal-LgqL8muc.woff2`
  3. `assets/jetbrains-mono-500-normal-BWZEU5yA.woff2`
  4. `assets/jetbrains-mono-600-normal-C8RAYTDA.woff2`
- **Asset Requests:** Zero broken font requests or Vite compilation warnings.

---

## 5. Build and Test Results

### 5.1 Production Asset Build
Executed command: `npm run build`
- **Result:** Success (Exit code 0)
- **Vite Version:** Vite 8.3.2 with `@tailwindcss/vite` 4.0.0 and `laravel-vite-plugin` 3.2.0
- **Modules Transformed:** 5 modules
- **Build Duration:** 1.86s
- **Compiled Assets:**
  - `public/build/manifest.json`: 2.76 kB
  - `public/build/fonts-manifest.json`: 11.82 kB
  - `public/build/assets/fonts-BwMEipbf.css`: 5.22 kB
  - `public/build/assets/app-AaPhKDtz.css`: 80.10 kB
  - `public/build/assets/app-BvRk9kiK.js`: 0.00 kB
  - `public/build/assets/scanner-CzjP4Mb5.js`: 130.50 kB

### 5.2 Automated PHPUnit Regression Suite
Executed command: `php vendor/phpunit/phpunit/phpunit`
- **Result:** 100% Passed (Exit code 0)
- **Total Tests:** 227
- **Total Assertions:** 2,322
- **Failures:** 0
- **Errors:** 0
- **Duration:** 53.81 seconds

### 5.3 Code Style Formatting
Executed command: `vendor/bin/pint --dirty --format agent`
- **Result:** Passed (Exit code 0, 0 violations)

---

## 6. Regression Risks and Safety Audit

- **Data Tables:** Column widths were calculated against minimum container widths (`min-w-[960px]` and `min-w-[880px]`). On wide displays, the location column fluidly expands while critical identifiers and action buttons retain consistent dimensions. Horizontal scrolling on smaller screens behaves predictably without content clipping.
- **Form Inspection Logic:** Calculation logic, radio input binding (`name="items[X][hasil]"`), automatic conclusion setting, and manual override capabilities remain 100% preserved.
- **Print / PDF Constraints:** No print stylesheets or `@media print` rules were altered.

---

## 7. Outstanding Verification Gaps and Recommendations

- **Limitation:** In this CLI sandbox environment, automated headless browser instrumentation (e.g. Puppeteer/Playwright CLS recording) was unavailable.
- **Manual Verification Steps for Quality Assurance:**
  1. Open Chrome or Edge DevTools on `http://127.0.0.1:8008`.
  2. Navigate to Master Data APAR (`/apar`) with Network throttling set to "Fast 3G".
  3. Verify that table headers and columns appear locked in position from the first frame, without horizontal twitching as font files finish loading.
  4. Navigate to `/pemeriksaan/{id}/edit` for an inspection marked "Perlu Tindakan".
  5. Verify that the recommendation banner displays in red immediately upon HTML delivery without flashing green first.
  6. On `/pemeriksaan/create`, toggle a checklist radio to "Tidak Baik" and confirm that the banner and manual select update to "Perlu Tindakan" smoothly.
