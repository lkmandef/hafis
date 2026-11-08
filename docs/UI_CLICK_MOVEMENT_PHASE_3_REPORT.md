# HAFIS - Phase 3 Investigation and Stabilization Report: Sidebar Navigation and Content Movement

**Target Repository:** `C:\laragon\www\aparcek-8`  
**Application:** HAFIS (Hospital APAR & Fire Inspection System)  
**Date:** 2026-11-02  
**Investigation Mode:** Chromium CDP Headless Instrumentation + Live PerformanceObserver  
**Status:** Root Cause Verified and Stabilized  

---

## 1. Executive Summary

In Phase 3, we investigated the reported issue where the main content area momentarily shifts during page refreshes and when navigating through sidebar menus.

Using live headless Chromium instrumentation via Chrome DevTools Protocol (CDP) and `PerformanceObserver({ type: 'layout-shift', buffered: true })`, we traced bounding rectangles, computed styles, and font swap lifecycles across multiple pages (Dashboard, Data APAR, Hasil Inspeksi, and Struktur Lokasi) and under live click-navigation conditions.

### Key Investigation Findings:
1. **The Sidebar Container (`#app-sidebar`) is Completely Static:**
   - On desktop viewports (1440x900), `#app-sidebar` remained exactly at `x: 0, y: 0, width: 256px, height: 900px` (`dx = 0px, dy = 0px`) across initial render, Vite load, and font readiness.
   - The desktop suppression rule `lg:transition-none` applied in Phase 1 prevented any sidebar slide-in transition.
2. **The Main Content Shell (`div.flex-1`) is Completely Static:**
   - Positioned at `x: 256px, y: 0px` with stable width `1169px` (1440px viewport minus 256px sidebar minus 15px reserved by `scrollbar-gutter: stable`). Position and width did not shift (`dx = 0px, dy = 0px`).
3. **The Exact Movement Mechanism (Font Swap Advance-Width Divergence):**
   - At approximately 400ms to 750ms after navigation, Vite webfonts (`Inter` and `JetBrains Mono`) arrive and swap for their fallback fonts.
   - Canvas 2D measurement revealed that the fallback font (`Segoe UI` configured with `size-adjust: 102.5%`) was 5% to 9% narrower than true `Inter` (e.g., `"Tambah APAR"`: 74.9px vs 79.9px; `"RSUD dr. H. Moch. Ansari Saleh"`: 169.6px vs 179.7px). Furthermore, because the `@font-face` definition omitted a `font-weight` range, Chromium clamped all fallback weights to 400.
   - **Horizontal Jitter in Right-Aligned Containers (`justify-between`):**
     - In the Topbar (`<x-topbar />`), the right profile section is anchored to the right. When text inside widened from 332px to 338px, its left coordinate jumped leftward: `x: 1061 -> 1056` (`dx = -5px`).
     - In the Page Header (`<x-page-header />`), action buttons are anchored to the right. When buttons widened by 20px, their left edge jumped leftward: `x: 960 -> 940` (`dx = -20px`) on Dashboard and `x: 1249 -> 1243` (`dx = -6px`) on APAR.
   - **Horizontal Jitter in Sequential Inline Chips:**
     - In the status filter bars, as preceding chips widened, trailing chips were pushed rightward: `dx = +7px`, `dx = +17px`, `dx = +20px`.
   - Because these shifts occur at the top of the main content area immediately adjacent to the sidebar right after clicking a navigation link or refreshing, the user visually perceives the content header jumping.

### Minimal Targeted Solution:
- Fallback font metrics in `resources/css/app.css` were calibrated with empirical Canvas measurements:
  - `Inter Fallback`: `size-adjust` tuned from `102.5%` to `108%`, and `font-weight: 100 900;` added. Average phrase width divergence dropped from 5.61px down to 1.11px.
  - `JetBrains Mono Fallback`: `size-adjust` tuned from `106%` to `109%`, and `font-weight: 100 900;` added.
- **Results:**
  - Dashboard CLS reduced from `0.00045` to `0.000069` (85% reduction); Topbar right profile shift eliminated (0px).
  - APAR Index CLS reduced from `0.00071` to `0.000046` (93.5% reduction); Page header action shift eliminated (0px).
  - Hasil Inspeksi CLS reduced from `0.00076` to `0.000005` (99.3% reduction).
  - Struktur Lokasi CLS reduced from `0.00025` to `0.000007` (97.2% reduction).
  - Tablet (iPad 768x1024) and Mobile (iPhone SE 375x667): CLS = `0.000000` (Zero shifts).
  - Subsequent navigation clicks in-session: CLS = `0.000000` (Zero shifts).
  - All 227 automated PHPUnit tests pass.

---

## 2. Methodology and Reproduction Steps

### 2.1 Instrumentation Architecture
To observe the layout lifecycle at millisecond granularity without subjective human observation:
1. Google Chrome was spawned in headless mode (`--headless=new`, `--window-size=1440,900`, `--disable-gpu`) connected via Chrome DevTools Protocol (CDP) WebSocket.
2. Injected a custom `PerformanceObserver` snippet into `Page.addScriptToEvaluateOnNewDocument` prior to navigation:
   ```javascript
   const observer = new PerformanceObserver((list) => {
       for (const entry of list.getEntries()) {
           if (!entry.hadRecentInput) {
               // Record entry.value, entry.startTime, previousRect, and currentRect
           }
       }
   });
   observer.observe({ type: 'layout-shift', buffered: true });
   ```
3. Authenticated test session as officer `lukmanoman17` via `/login`.
4. Monitored element bounding boxes (`x`, `y`, `width`, `height`) and deltas (`dx`, `dy`) across full document lifecycle (0ms to 2000ms).

### 2.2 Probed URLs and Actions
1. **Initial Page Refreshes:**
   - `http://127.0.0.1:8008/` (Dashboard)
   - `http://127.0.0.1:8008/apar` (Data APAR)
   - `http://127.0.0.1:8008/pemeriksaan` (Hasil Inspeksi)
   - `http://127.0.0.1:8008/lokasi` (Struktur Lokasi)
2. **Sidebar Menu Navigation Clicks:**
   - Programmatic click on `nav a[href$="/apar"]` from Dashboard.
   - Repeated multi-cycle navigation between all main sidebar menus.
3. **Responsive Breakpoints:**
   - Desktop HD: 1440x900
   - Tablet (iPad): 768x1024
   - Mobile (iPhone SE): 375x667

---

## 3. Investigation Evidence and Root Cause Analysis

### 3.1 Verification that Sidebar Does Not Shift
Element `#app-sidebar` was tracked at 50ms, 150ms, 400ms, and 1500ms:
- Initial Rect: `x: 0, y: 0, w: 256, h: 900`
- Final Rect: `x: 0, y: 0, w: 256, h: 900`
- Delta: `dx: 0px, dy: 0px, dw: 0px, dh: 0px`

The sidebar container remains completely rigid. It does not cause the perceived content movement.

### 3.2 Verification that Main Content Shell Does Not Shift
Element `div.flex-1` (the main content shell) was tracked:
- Initial Rect: `x: 256, y: 0, w: 1169, h: 900`
- Final Rect: `x: 256, y: 0, w: 1169, h: 900`
- Delta: `dx: 0px, dy: 0px, dw: 0px, dh: 0px`

The main shell position does not change.

### 3.3 Empirical Discovery: What Actually Moved
LayoutShift events captured in the baseline run showed consistent horizontal movement concentrated in two specific visual zones directly adjacent to the sidebar:

#### Zone A: Topbar Right Profile Section (`DIV.flex.items-center.gap-2.sm:gap-3.shrink-0`)
- **Container Structure:** `header.flex.items-center.justify-between`
- **Initial Rect (Fallback font):** `x: 1061, y: 12, w: 332, h: 38`
- **Final Rect (Inter font loaded):** `x: 1056, y: 12, w: 338, h: 38`
- **Delta:** `dx = -5px, dy = 0px` (Width increased by 6px, causing left coordinate to jump leftward by 5px).

#### Zone B: Page Header Actions Container (`DIV.w-full.sm:w-auto.flex...`)
- **Container Structure:** `x-page-header` has `justify-between` with right-aligned `<x-slot:actions>`.
- **Dashboard Action Container:**
  - Initial Rect (Fallback font): `x: 960, y: 113, w: 433, h: 40`
  - Final Rect (Inter font loaded): `x: 940, y: 113, w: 453, h: 40`
  - Delta: `dx = -20px, dy = 0px` (Container widened by 20px, left edge shifted leftward by 20px).
- **APAR Index Action Container:**
  - Initial Rect (Fallback font): `x: 1249, y: 103, w: 144, h: 40`
  - Final Rect (Inter font loaded): `x: 1243, y: 103, w: 150, h: 40`
  - Delta: `dx = -6px, dy = 0px` (Container widened by 6px, left edge shifted leftward by 6px).

#### Zone C: Sequential Status Filter Chips (`apar/index.blade.php`)
- **Chip 1 ("Semua APAR"):** anchored at left, width grew from 100.0px to 104.5px.
- **Chip 2 ("Unit Aktif"):** shifted rightward: `x: 637 -> 644` (`dx = +7px`).
- **Chip 3 ("Perlu Perhatian"):** shifted rightward: `x: 880 -> 897` (`dx = +17px`).
- **Chip 4 ("Kondisi Rusak"):** shifted rightward: `x: 1022 -> 1042` (`dx = +20px`).

### 3.4 Root Cause: Canvas 2D Text Width Measurement
Using Chromium Canvas 2D text measurement on live UI text strings, we measured the exact advance widths:

| String | Inter 400 | Segoe UI (102.5% fallback) | Divergence (102.5%) | Segoe UI (108% calibrated) | Divergence (108%) |
|---|---|---|---|---|---|
| `"Tambah APAR"` | 79.9px | 74.9px | **-5.0px** | 79.0px | **-0.9px** |
| `"Semua APAR (173)"` | 104.5px | 100.0px | **-4.5px** | 105.5px | **+1.0px** |
| `"Unit Aktif (137)"` | 83.6px | 81.6px | **-2.0px** | 86.0px | **+2.4px** |
| `"Perlu Perhatian (18)"` | 110.9px | 105.1px | **-5.8px** | 110.9px | **0.0px** |
| `"Kondisi Rusak (14)"` | 104.0px | 98.7px | **-5.3px** | 104.1px | **+0.1px** |
| `"RSUD dr. H. Moch. Ansari Saleh"` | 179.7px | 169.6px | **-10.1px** | 178.9px | **-0.8px** |
| `"Petugas K3RS"` | 80.3px | 73.7px | **-6.6px** | 77.7px | **-2.6px** |
| **Average Divergence** | - | - | **5.61px** | - | **1.11px** |

Additionally, the existing `@font-face` definition lacked a `font-weight` descriptor, causing Chromium to default to `font-weight: 400` only. When buttons and badges requested `font-medium` (500) or `font-semibold` (600), the browser could not apply the fallback correctly to bold text, widening the gap further.

---

## 4. Minimal Targeted Correction

The correction was strictly confined to `resources/css/app.css` without modifying markup, layout components, or print stylesheets:

```css
/* Metric-optimized font fallbacks to eliminate layout shift during font swap */
@font-face {
    font-family: 'Inter Fallback';
    src: local('Segoe UI'), local('Arial');
    font-weight: 100 900;
    ascent-override: 95.8%;
    descent-override: 24.2%;
    line-gap-override: 0%;
    size-adjust: 108%;
}

@font-face {
    font-family: 'JetBrains Mono Fallback';
    src: local('Consolas'), local('Courier New');
    font-weight: 100 900;
    ascent-override: 90%;
    descent-override: 27%;
    line-gap-override: 0%;
    size-adjust: 109%;
}
```

### Why this minimal fix works:
1. `size-adjust: 108%` scales the fallback advance width to match `Inter` across standard Latin alphanumeric characters within ~1px.
2. `font-weight: 100 900;` ensures the fallback is utilized across all weights (regular 400, medium 500, semibold 600, bold 700) rather than falling back to unadjusted system fonts.
3. `size-adjust: 109%` for `JetBrains Mono Fallback` scales `Consolas` (raw 90.7px for 14-char code) to 98.9px, precisely matching `JetBrains Mono 500` (99.0px).

---

## 5. Verification Results

### 5.1 Chromium PerformanceObserver CLS Comparison

| Page / Scenario | Baseline CLS | Post-Fix CLS | Delta / Improvement |
|---|---|---|---|
| **Dashboard** (`/`) | 0.000452 | 0.000070 | **85% reduction** (Topbar right section shift eliminated) |
| **Data APAR** (`/apar`) | 0.000713 | 0.000046 | **93.5% reduction** (Header button shift eliminated) |
| **Hasil Inspeksi** (`/pemeriksaan`) | 0.000763 | 0.000005 | **99.3% reduction** (Table and chip shifts eliminated) |
| **Struktur Lokasi** (`/lokasi`) | 0.000249 | 0.000008 | **97.2% reduction** (All badge row shifts eliminated) |
| **Click Navigation** (Dashboard -> APAR) | 0.000694 | 0.000046 | **93.3% reduction** |
| **Tablet Viewport** (768x1024) | - | **0.000000** | **Zero layout shifts** |
| **Mobile Viewport** (375x667) | - | **0.000000** | **Zero layout shifts** |
| **Subsequent Navigation Cycles** (Cached) | - | **0.000000** | **Zero layout shifts** |

All measured CLS values are well below the Google Core Web Vitals "Good" threshold of `0.1000` (over 1,400 to 20,000 times below the threshold).

### 5.2 Automated Regression Tests
- **PHPUnit Test Suite:**
  - Command: `php vendor/phpunit/phpunit/phpunit`
  - Result: `{"tool":"phpunit","result":"passed","tests":227,"passed":227,"assertions":2322,"duration_ms":62997}`
  - All 227 unit and feature tests passed without any failures or regressions.
- **Production Asset Compilation:**
  - Command: `npm run build`
  - Result: Built in 3.09s (`public/build/assets/app-Cg4i_B_C.css`: 80.14 kB).
- **Code Style (Pint):**
  - Command: `vendor/bin/pint --dirty --format agent`
  - Result: All files formatted.

---

## 6. Remaining Observations and Edge Case Notes

1. **Top-Bar Date Widget Advance Micro-Shift (3px):**
   - On initial uncached render, the localized Indonesian date string (`"Senin, 02 November 2026"`, 25 characters) expands by ~3px upon font swap. This causes the adjoining clock separator dot (`•`) to shift right by 3px (`dx = 3px`). The resulting CLS score is `0.000005`, which is visually imperceptible.
2. **Subsequent Navigation Stability:**
   - Once fonts are loaded into the browser memory during the first page view of a user session, all subsequent sidebar navigation clicks experience `CLS = 0.000000` with 0 shift events.
3. **Mobile and Tablet Drawer:**
   - On viewports <= 1024px, the sidebar is an off-canvas drawer that opens only when triggered. It has zero impact on initial content geometry.

---

## 7. Deliverable Summary

- **Root Cause Confirmed:** The sidebar container was not moving. The movement was horizontal jitter in right-aligned topbar and header action containers caused by advance-width discrepancy during local webfont swap (`Inter` vs uncalibrated `Segoe UI`).
- **Fix Implemented:** Empirical fallback calibration (`size-adjust: 108%` and `109%`, with `font-weight: 100 900;`) in `resources/css/app.css`.
- **Status:** Verified stable across desktop, tablet, and mobile with 227 passing tests.
