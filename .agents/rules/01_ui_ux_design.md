# Rule: Frontend UI, UX & Design System Guidelines

This rule governs all interface, visual layout, color palette, typography, accessibility, and styling operations in this project (specifically for Blade views, Tailwind CSS, components, and client-side interactions).

---

## 1. Trigger Conditions
Automatically active whenever:
- User mentions UI words: `ui`, `tampilan`, `layout`, `desain`, `warna`, `font`, `tombol`, `form`, `tabel`, `dashboard`, `modal`, `sidebar`, `navbar`, `card`, `responsive`, `mobile`.
- Active file is in `resources/views/**`, `resources/css/**`, `resources/js/**`.
- Writing or refactoring Tailwind CSS utility classes.

---

## 2. Integrated Skills & Operational Standards

### A. Surfaces & Visual Polish (`better-ui` + `antislop-ui`)
- **Radii**: Match the existing design token scale (e.g. `rounded-xl`, `rounded-2xl`). Do not arbitrarily mix sharp corners with pill shapes without clear hierarchical rationale.
- **Elevation & Shadows**: Prefer soft, diffused shadows (`shadow-sm`, `shadow-md shadow-gray-200/50`) or subtle 1px border lines (`border border-slate-200/80 dark:border-slate-800`) over heavy dark box-shadows.
- **Interactive Feedback**: All clickable elements must define distinct `:hover`, `:active`, `:focus-visible`, and `:disabled` states with smooth transitions (`transition-all duration-150 ease-in-out`).
- **Anti-Slop Visual Check**: Avoid generic AI cliches:
  - Do NOT put glowing purple/pink gradients on standard business dashboards.
  - Do NOT nest cards inside cards inside cards with borders around everything.
  - Keep icons purposefully sized (`w-4 h-4` or `w-5 h-5`) and optically centered with text.

### B. Color & Contrast Harmony (`better-colors` + `antislop-human`)
- **Semantic Color Tokens**: Group colors into functional roles:
  - `primary`: Main brand accent (e.g., emerald, indigo, sky, or slate as established in the app).
  - `surface` & `background`: Neutral background layers (`bg-white dark:bg-slate-900`, `bg-slate-50 dark:bg-slate-950`).
  - `status`: Green for success/active, amber/yellow for pending/warning, red for error/overdue, sky/blue for info.
- **Contrast Ratios**: Body text must maintain at least 4.5:1 contrast against its background (WCAG AA). Muted text (`text-slate-500`) must remain easily legible (at least 3:1 for large text / badges).
- **Badge & Pill Colors**: Use subtle tinted backgrounds with deeper saturated foregrounds (e.g., `bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20`).

### C. Layout & Adaptive Flow (`better-layout` + `antislop-layoutmobile`)
- **Spacing Scale**: Enforce consistent Tailwind multiples (e.g., `gap-2`, `gap-4`, `gap-6`, `p-4`, `p-6`). Do not use random arbitrary pixel values like `p-[17px]`.
- **Responsive Stacking**: Design mobile-first or verify screen reflow:
  - Tables must wrap gracefully or provide an explicit horizontal scroll container (`overflow-x-auto`) with sticky headers or key columns where needed.
  - Sidebars must support collapsible or off-canvas drawer states on `< md:` viewports.
  - Grid structures should step cleanly: `grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4`.
- **Tap Targets**: Mobile interactive targets must be at least 44x44px or have sufficient padding (`py-2.5 px-4`).

### D. Typography & Numeric Data (`better-typography`)
- **Type Scale**: Maintain a structured scale: `text-xs` (captions/tags), `text-sm` (body/tables/form inputs), `text-base` (lead body), `text-lg`/`text-xl` (section titles), `text-2xl`+ (page titles).
- **Tabular Figures for Numbers**: For dates, times, currencies, and IDs, use monospace or `tabular-nums` (`font-mono` or `tracking-tight font-medium tabular-nums`) so columns align properly in tables and badges.
- **Truncation & Wrapping**: Protect cards and tables from breaking with long content: use `truncate`, `line-clamp-2`, or `break-words`.

### E. Accessibility & Form Inputs (`better-accessibility`)
- **Form Controls**: Every input must have a linked `<label for="...">` or `aria-label`.
- **Focus Rings**: Never remove focus outlines without replacing them with accessible custom rings (`focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-1`).
- **Semantic HTML**: Use `<button type="button">` or `<a href="...">` instead of clickable `<div>` or `<span>`.

---

## 3. Pre-Delivery Checklist
Before completing any UI change:
- [ ] Are mobile viewports tested (no unintentional horizontal scroll)?
- [ ] Are hover, active, and focus rings working?
- [ ] Are text colors sufficiently contrasted?
- [ ] Are empty states and loading/disabled states considered?

