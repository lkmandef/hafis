# Rule: Code Hygiene, Anti-Slop & Human Copywriting

This rule enforces high standards of code hygiene, eliminates generic AI boilerplate/slop, and ensures human-centered interface copy throughout the codebase.

---

## 1. Trigger Conditions
Automatically active whenever:
- Creating, editing, or refactoring code in ANY file.
- Writing copy, interface labels, toast notifications, error messages, placeholders, or empty states.
- Reviewing code for merge readiness.

---

## 2. Integrated Skills & Operational Standards

### A. Code Comment Hygiene (`antislop-code` + `antislop`)
- **No Stating the Obvious**: Never write redundant comments explaining simple code syntax:
  - ❌ BAD: `// Get all active users` followed by `User::where('active', true)->get();`
  - ❌ BAD: `// Return view` followed by `return view('dashboard');`
  - ❌ BAD: `// Loop through items` followed by `foreach ($items as $item)`
- **Comment "Why", Not "What"**: Only write comments when explaining a non-obvious business constraint, a workaround for an external bug, a security boundary, or a performance trade-off.
- **No AI Watermarks**: Never leave AI disclaimers, generated-by comments, or self-congratulatory notes in code comments.

### B. Anti-Slop Interface & Visual Design (`antislop` + `antislop-ui`)
- **No Generic AI Aesthetic Cliches**:
  - Avoid gratuitous rainbow or purple-to-indigo gradient buttons unless specifically aligned with product branding.
  - Avoid excessive decorative illustrations that serve no functional purpose.
  - Avoid endless nested containers (`div` inside `div` inside `card`) that add noise without semantic meaning.
- **Intentional Density**: Provide comfortable breathing room (`gap-4`, `p-5`) without making components look sparse or bloated.

### C. Human Copywriting & Microcopy (`antislop-copywriting` + `better-writing`)
- **Clear, Punchy & Grounded**: Write copy that sounds like a thoughtful human teammate:
  - ❌ AI Fluff: *"Embark on your journey and effortlessly organize your schedule with our cutting-edge solution."*
  - ✅ Human & Direct: *"Kelola jadwal aparatur dengan mudah dan pantau status harian di sini."*
- **Action-Oriented Buttons**: Labels should state exactly what will happen:
  - ❌ Vague: *"Submit"*, *"OK"*, *"Process"*
  - ✅ Specific: *"Simpan Jadwal"*, *"Hapus Data"*, *"Unduh Laporan PDF"*
- **Helpful Error Messages**: Explain what happened and how to resolve it:
  - ❌ Unhelpful: *"Error: Invalid input"*
  - ✅ Actionable: *"Tanggal kegiatan tidak boleh lebih awal dari hari ini. Silakan periksa kembali."*
- **Empathetic Empty States**: An empty state should tell the user what is missing and provide a direct call-to-action to create the first item.

---

## 3. Pre-Delivery Checklist
- [ ] Have all redundant/narrative code comments been removed?
- [ ] Is UI text natural, helpful, and concise?
- [ ] Are buttons and CTAs specific about their action?

