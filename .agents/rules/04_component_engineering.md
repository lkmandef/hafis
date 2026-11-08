# Rule: Component Engineering, States, Variants & Resilience

This rule governs the lifecycle, state coverage, visual variants, resilience testing, and performance optimization for frontend components.

---

## 1. Trigger Conditions
Automatically active whenever:
- Creating or refactoring UI components (Blade components in `resources/views/components/**`, Vue/Alpine components, interactive widgets).
- Handling complex UI states (forms, modals, interactive tables, dropdowns, filter bars).
- Optimizing performance, diagnosing lag, or reviewing component edge cases.

---

## 2. Integrated Skills & Operational Standards

### A. Comprehensive State Machine (`state-machine`)
Every interactive component or data-driven view must account for all essential states:
1. **Idle / Default**: Normal appearance with typical data.
2. **Hover / Active / Focused**: Responsive feedback on user interaction.
3. **Loading / Pending**: Disabled state or skeleton loader (`animate-pulse`) when fetching or submitting.
4. **Empty State**: Friendly illustration/icon, clear explanation, and primary action button when collection is empty.
5. **Error State**: Clear, inline feedback with recovery actions (e.g. "Coba Lagi" button or field-level validation message).
6. **Disabled / Read-Only**: Dimmed visual appearance (`opacity-50 cursor-not-allowed`) with interactions blocked.

### B. Component Variants & Consistency (`variant`)
- Design components with clean, composable variant properties:
  - Sizes: `sm`, `md` (default), `lg`.
  - Intents: `primary`, `secondary`, `outline`, `ghost`, `danger`.
- Encapsulate styling logic inside Blade component classes or clean conditional classes rather than copy-pasting raw markup repeatedly.

### C. Stress Testing & Edge Cases (`break`)
Before finalizing a component, verify it against extreme scenarios:
- **Length Stress**: What happens when a user enters a 100-character name? Does it break the layout? (Use `truncate` or `break-words`).
- **Zero / Null Stress**: What if an optional relation is null? (Use null-safe operators `$item->user?->name ?? '-'`).
- **Viewport Stress**: Does the component render cleanly on a 360px mobile screen without overflowing?

### D. Interface Review & Web Performance (`interface-review` + `performance`)
- Avoid layout shifts (CLS) by giving images and icons explicit width/height dimensions.
- Use Vite asset bundling efficiently. Avoid importing entire bulky libraries when a lightweight native solution or small utility suffices.
- Defer non-critical JavaScript and keep DOM trees lightweight.

---

## 3. Pre-Delivery Checklist
- [ ] Are empty, loading, and error states designed and handled?
- [ ] Does long or missing data break the component layout?
- [ ] Are component props structured cleanly for reusability?

