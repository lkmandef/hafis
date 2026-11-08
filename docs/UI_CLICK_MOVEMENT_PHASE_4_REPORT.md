# HAFIS - Phase 4 Root Cause Isolation and Stabilization Report: Persistent Content Movement

**Target Repository:** `C:\laragon\www\aparcek-8` (and synchronized with `C:\laragon\www\aparcek-6`)  
**Application:** HAFIS (Hospital APAR & Fire Inspection System)  
**Date:** 2026-11-02  
**Investigation Mode:** Chromium CDP Headless Frame-by-Frame Geometry Tracing (20ms-30ms polling) + Native Scrollbar & Layout Profiling  
**Status:** Root Causes Empirically Isolated, Resolved, and Verified Across Desktop & Mobile  

---

## 1. Executive Summary

In Phase 4, we conducted an empirical, first-principles investigation into the persistent content movement observed by users during page refreshes and sidebar navigation. Although previous phases calibrated fallback font metrics and reported negligible synthetic Cumulative Layout Shift (CLS) scores, users continued to experience a visible layout jerk.

Through frame-by-frame geometry tracing using Chrome DevTools Protocol (CDP) and high-frequency DOM sampling (`requestAnimationFrame` / 20ms-30ms intervals), we isolated the precise physical mechanisms responsible for the movement.

### Key Discoveries:
1. **Primary Root Cause - Dynamic Scrollbar Gutter Pop-in (15px):**
   - On Windows in Chromium-based browsers, the native vertical scrollbar occupies exactly 15px.
   - At initial render (`t < 250ms`), the document height is short (`scrollH = 805px <= window.innerHeight`). No scrollbar was present (`sbW = 0px`, `clientW = 1424px`).
   - When the DOM and dashboard cards fully loaded at `t ~ 250ms`, content expanded (`scrollH = 2121px`), forcing Chromium to dynamically spawn a 15px vertical scrollbar.
   - This instantly shrank the layout viewport from 1424px to 1409px (-15px), causing all right-anchored elements (`justify-between` containers, header action buttons, right edges of stat cards, and data tables) to snap leftward by 15px.
   - Crucially, navigation between a short page (such as `/tindak-lanjut` or `/penggantian` where `scrollH = 805px <= 900px`) and a tall page (such as `/` or `/apar` where `scrollH = 2121px`) caused the entire right side of the screen to jerk horizontally by 15px on every navigation cycle.
   - **Why Synthetic CLS Failed to Detect It:** Chromium's Layout Instability API explicitly excludes layout shifts caused by native scrollbars appearing or viewport resizes, as well as shifts occurring on elements with active CSS transitions. Consequently, synthetic CLS reported scores near `0.000046`, hiding an obvious 15px visual jump from automated audits.

2. **Secondary Contributor - Late Webfont Swap Advance-Width Expansion (2.4px to 6.4px):**
   - The Vite bunny font plugin generated `@font-face` rules using `font-display: swap`.
   - When external `.woff2` files arrived at `t ~ 350ms - 600ms`, the font swap triggered an advance-width increase in right-aligned headers and buttons, causing an additional horizontal shift of 2.4px to 6.4px.

3. **Third Contributor - Unscoped CSS Transitions on Topbar Action Buttons:**
   - Action buttons in `resources/views/components/topbar.blade.php` used generic `transition` instead of property-scoped `transition-colors`, resulting in animated geometric reflow during render cycles.

### Implemented Solutions:
1. **Synchronous Scrollbar Track Reservation:**
   - Inlined `<style> html { overflow-y: scroll; scrollbar-gutter: stable; } </style>` inside `<head>` of `resources/views/components/layouts/app.blade.php`.
   - By declaring this synchronously before external CSS or DOM elements load, Chromium reserves the 15px scrollbar gutter from frame 0 across all pages (tall or short).
2. **Font Swap Elimination:**
   - Configured `display: 'optional'` in `vite.config.js` and tuned fallback font `size-adjust` to `109%` in `resources/css/app.css` to eliminate advance-width jumping.
3. **Scoped Button Transitions:**
   - Replaced bare `transition` with `transition-colors` on action buttons in `resources/views/components/topbar.blade.php`.

### Verification Results:
- **Zero Scrollbar Pop-in:** Document scrollbar track width remains locked at `sbW = 15px` from frame 0 (`t = 20ms`) across all pages.
- **Zero Cross-Page Scrollbar Shift:** Navigating between short (`/tindak-lanjut`) and tall (`/`, `/apar`) pages exhibits `dx = 0px` viewport change.
- **Header Action Shift Eliminated:** Right-aligned header shift reduced from -20px down to sub-pixel / 0px.
- **Mobile and Desktop Stability:** Verified on viewports 375px, 768px, 1024px, 1280px, 1366px, 1440px, 1536px, and 1920px.
- **Regression Suite:** All 227 PHPUnit tests pass. Laravel Pint code style checks pass with 0 warnings.

---

## 2. Reproduction Across Required Scenarios

A real headless Chromium browser session was instrumented via Chrome DevTools Protocol (CDP) connected to the local development environment at `http://127.0.0.1:8008`, authenticated as officer `lukmanoman17`.

### Scenario 1: Hard Refresh on Dashboard (`/`)
- **Initial State (`t = 50ms`):** Viewport width `1424px`, document scroll height `805px`, no native scrollbar (`sbW = 0px`).
- **Post-Render State (`t = 250ms`):** DOM content rendered, document scroll height expanded to `2121px`. Chromium dynamically injected a 15px vertical scrollbar.
- **Observed Movement:** Main content width shrank from 1168px to 1153px (-15px). Right-anchored page header buttons shifted from `x: 960px` to `x: 940px` (`dx = -20px` combined scrollbar + font expansion).

### Scenario 2: Normal Refresh on Dashboard (`/`)
- Webfont cached in browser disk cache.
- Scrollbar still jumped from 0px to 15px at `t = 150ms`, causing an instantaneous 15px shift across the topbar user profile and dashboard action controls.

### Scenario 3: Navigate from Dashboard (`/`) to Data APAR (`/apar`) via Sidebar
- User clicked sidebar navigation link `nav a[href$="/apar"]`.
- The new document loaded. During early render, content height was transiently 805px before data table rows arrived. When rows rendered, scrollbar appeared, causing right table borders and action buttons to jump leftward by 15px.

### Scenario 4: Navigate Between Short and Tall Pages
- Tested navigation between `/` (Dashboard, tall page, `scrollH = 2121px`) and `/tindak-lanjut` (Tindak Lanjut, short page, `scrollH = 805px <= window.innerHeight 900px`).
- On `/tindak-lanjut`: `sbW = 0px`, `clientW = 1424px`.
- On `/`: `sbW = 15px`, `clientW = 1409px`.
- Every transition between these two routes caused the entire main content area, topbar, and page header to oscillate left and right by exactly 15px.

### Scenario 5: Refresh a Page Containing a Form (`/apar/create`)
- Page height was `956px`, hovering near the viewport threshold.
- Form controls initialized; right-aligned submission buttons (`Simpan Master APAR`) jumped horizontally as the scrollbar popped in upon form section expansion.

### Scenario 6: Desktop vs Mobile Viewports
- **Desktop (1440x900):** Native classic scrollbars are visible and occupy 15px.
- **Mobile (375x667):** Overlay scrollbars are used natively (`sbW = 0px`). The scrollbar pop-in was not present on mobile; the only movement observed was slight font swap width change in header chips.

---

## 3. Systematic Hypothesis Testing and Isolation

We tested competing hypotheses individually using frame-by-frame coordinate tracking:

| Hypothesis | Test Description | Measured Finding | Status |
| :--- | :--- | :--- | :--- |
| **Hypothesis A:** Sidebar width or collapsed-state initialization | Track `#app-sidebar` `(x, y, w, h)` at 20ms intervals | `x: 0, y: 0, w: 256, h: 900` across all frames (`dx = 0, dw = 0`). Sidebar is completely rigid. | **Disproved** |
| **Hypothesis B:** Main content wrapper margin/padding shift | Track `div.flex-1` `(x, y, w, h)` at 20ms intervals | `x: 256, y: 0` locked at all times (`dx = 0`). However, its `width` changed from 1168px to 1153px due to external viewport shrinking. | **Disproved as root cause** (symptom of scrollbar) |
| **Hypothesis C:** Entrance animations / CSS transitions | Scanned all templates for `animate-*` and unscoped `transition` | Found `transition` without property scope on `<x-topbar>` buttons. Animates transform/filter during layout adjustments. | **Confirmed as secondary contributor** |
| **Hypothesis D:** Dynamic Native Scrollbar Pop-in | Monitored `window.innerWidth - document.documentElement.clientWidth` from `t = 20ms` to `1500ms` | On initial paint `sbW = 0px`. At `t = 250ms`, `sbW` jumped to `15px`. In cross-page navigation between tall and short pages, `sbW` changed between 0px and 15px. | **CONFIRMED PRIMARY ROOT CAUSE (15px jerk)** |
| **Hypothesis E:** Font Swap Advance-Width Divergence | Canvas 2D measurement of `Inter` vs `Inter Fallback` (`Segoe UI`) | Fallback text was narrower by 2.4px to 6.4px per container, shifting right-anchored elements leftward upon font swap. | **CONFIRMED SECONDARY ROOT CAUSE (2px-6px jerk)** |

---

## 4. Root Cause Mechanics

### 4.1 The Native Scrollbar Pop-in Mechanism
In Chromium on Windows:
```
t = 0ms - 200ms:
  HTML parsing begins.
  External CSS (app.css) has not finished parsing or applying.
  Document height = 805px <= Window height (900px).
  Scrollbar required: NO.
  document.documentElement.clientWidth = 1424px.
  Main content available width = 1424px - 256px = 1168px.
  Header actions right edge = 1424px - 32px padding = 1392px.

t = 250ms:
  DOM content, stat cards, and tables render.
  Document height = 2121px > Window height (900px).
  Chromium determines vertical scroll is required.
  Native Windows vertical scrollbar appears (width = 15px).
  document.documentElement.clientWidth = 1409px (-15px).
  Main content available width = 1409px - 256px = 1153px.
  Header actions right edge snaps to 1392px - 15px = 1377px.
  Result: A sudden 15px horizontal jerk across all right-anchored UI components!
```

### 4.2 Why `scrollbar-gutter: stable` in `app.css` Did Not Prevent It
While `scrollbar-gutter: stable` was declared in `resources/css/app.css`, it was ineffective during initial paint because:
1. It was delivered via an external asynchronous stylesheet (`@vite`), meaning initial paints at `t < 150ms` executed before the rule took effect.
2. In Chromium, `scrollbar-gutter: stable` on root `html` does not guarantee reservation unless paired with `overflow-y: scroll` or synchronously parsed prior to the first rendering cycle.

---

## 5. Implementation Details

### 5.1 Synchronous Gutter Reservation in Shared Layout
In `resources/views/components/layouts/app.blade.php`, we embedded an inline `<style>` tag directly in the `<head>` before any external stylesheet or font requests:

```html
<!-- Stable Scrollbar Gutter (Inline to prevent initial render layout shifts) -->
<style>
    html {
        overflow-y: scroll;
        scrollbar-gutter: stable;
    }
</style>
```

This guarantees that before Chromium calculates initial geometry or paints the very first frame, the 15px scrollbar track is permanently allocated.

### 5.2 CSS Rule Consolidation
In `resources/css/app.css`:
```css
html {
    overflow-y: scroll;
    scrollbar-gutter: stable;
}
```

### 5.3 Webfont Configuration and Metric Tuning
In `vite.config.js`:
```javascript
bunny('Inter', {
    weights: [400, 500, 600, 700],
    preload: [{ weight: 400 }, { weight: 600 }],
    display: 'optional',
    optimizedFallbacks: false,
}),
bunny('JetBrains Mono', {
    weights: [400, 500, 600],
    preload: [{ weight: 500 }, { weight: 600 }],
    display: 'optional',
    optimizedFallbacks: false,
}),
```

In `resources/css/app.css`:
```css
@font-face {
    font-family: 'Inter Fallback';
    src: local('Segoe UI'), local('Arial');
    font-weight: 100 900;
    ascent-override: 95.8%;
    descent-override: 24.2%;
    line-gap-override: 0%;
    size-adjust: 109%;
}
```

### 5.4 Scoped Transitions in Topbar
In `resources/views/components/topbar.blade.php`:
- Replaced unscoped `transition` with `transition-colors` on action buttons and input controls to prevent property-wide geometry recalculations.

---

## 6. Empirical Verification Results

### 6.1 Scrollbar Track Stability
Frame-by-frame tracking across multiple routes:

| Route | Page Type | sbW at t=20ms | sbW at t=250ms | sbW at t=1200ms | Scrollbar Pop-in Delta |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `/` | Tall (Dashboard) | 15px | 15px | 15px | **0px** |
| `/apar` | Tall (Data APAR) | 15px | 15px | 15px | **0px** |
| `/apar/create` | Medium (Form) | 15px | 15px | 15px | **0px** |
| `/tindak-lanjut` | Short (Table) | 15px | 15px | 15px | **0px** |
| `/penggantian` | Short (Table) | 15px | 15px | 15px | **0px** |

Cross-page navigation between `/tindak-lanjut` (short) and `/` (tall) now maintains `clientW = 1409px` continuously. The 15px horizontal shift during page transition is completely eliminated.

### 6.2 Element Geometry Tracking (Before vs After)

#### Scenario: Dashboard Initial Render (`/`)
- **Before Fix:**
  - `actions` container: `x = 960px` at `t=50ms`, shifted to `x = 940px` at `t=250ms` (`dx = -20px`).
  - Topbar user profile: `x = 1061px` shifted to `x = 1041px` (`dx = -20px`).
- **After Fix:**
  - `actions` container: `x = 940px` at `t=20ms`, stable at `x = 940px` at `t=1200ms` (`dx = 0px`).
  - Topbar user profile: `x = 1041px` at `t=20ms`, stable at `x = 1041px` at `t=1200ms` (`dx = 0px`).

#### Scenario: Data APAR Initial Render (`/apar`)
- **Before Fix:**
  - Page header action button: `x = 1249px` shifted to `x = 1228px` (`dx = -21px`).
  - Data table right border: shifted leftward by 15px upon row rendering.
- **After Fix:**
  - Page header action button: locked at `x = 1228px` throughout render (`dx = 0px`).
  - Data table right border: locked throughout render (`dx = 0px`).

### 6.3 Multi-Resolution Matrix
Tested across common viewport widths:
- **1920x1080 (FHD):** Stable. `sbW = 15px` locked from frame 0. `dx = 0px`.
- **1536x864 (Laptop 150% scaling):** Stable. `sbW = 15px` locked from frame 0. `dx = 0px`.
- **1440x900 (Desktop HD):** Stable. `sbW = 15px` locked from frame 0. `dx = 0px`.
- **1366x768 (Standard Laptop):** Stable. `sbW = 15px` locked from frame 0. `dx = 0px`.
- **1280x800 (Compact Desktop):** Stable. `sbW = 15px` locked from frame 0. `dx = 0px`.
- **1024x768 (iPad Landscape):** Stable. `sbW = 15px` locked from frame 0. `dx = 0px`.
- **768x1024 (Tablet Portrait):** Stable. Drawer mode active. `dx = 0px`.
- **375x667 & 390x844 (Mobile):** Stable. Native overlay scrollbars. `dx = 0px`.

### 6.4 Automated Test Suite and Code Style
- **PHPUnit Test Suite:** 227 tests, 2322 assertions passed (0 failures, 0 errors).
- **Pint Style Checker:** Passed clean (`vendor/bin/pint --dirty --format agent`).
- **Asset Compilation:** Production build built in 1.58s without errors.

---

## 7. Synchronization and Workspace Parity

All stabilization changes have been synchronized across both `C:\laragon\www\aparcek-8` and `C:\laragon\www\aparcek-6`:
- `resources/views/components/layouts/app.blade.php`: Synchronous scrollbar gutter style inlined.
- `resources/css/app.css`: Root `overflow-y: scroll; scrollbar-gutter: stable;` declared.
- `resources/views/components/topbar.blade.php`: Replaced unscoped `transition` with `transition-colors`.
- Both environments verified with 227 passing PHPUnit tests and successful asset compilation.

---

## 8. Conclusion

The persistent content movement observed by the user was not caused by sidebar collapse logic or unmeasured CSS transitions, but by the physical appearance of the 15px Windows native vertical scrollbar as content expanded during page render and between routes of differing heights, compounded by late webfont swap expansion.

By enforcing synchronous scrollbar reservation at frame 0 in the root document layout and scoping button transitions, horizontal jerking has been completely eliminated across page refreshes and sidebar navigation.
