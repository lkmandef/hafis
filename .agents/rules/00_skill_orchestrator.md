# Master Rule: Autonomous Skill Orchestrator & Auto-Activation Engine

This rule enforces autonomous detection, reading, and application of all installed workspace skills. You MUST NOT wait for the user to explicitly call or invoke a skill by name. Whenever the context of a request, the active document, or the touched codebase matches a skill's domain, you must automatically read and follow its guidance.

---

## 1. Skill Auto-Activation Protocol

On EVERY user interaction, follow this 4-step pipeline:

```
[User Prompt & Context]
         │
         ▼
1. Detect Domain & Trigger Keywords (UI, Backend, Copy, Component, Workflow)
         │
         ▼
2. Resolve Relevant Skills from `.agents/skills/` and `.agent/skills/`
         │
         ▼
3. Auto-Read `SKILL.md` (and reference docs) via `read_file` if not in memory
         │
         ▼
4. Synthesize Guidelines into Plan, Code, and Final Response
```

### Golden Rules:
1. **Never wait for manual invocation**: If the user asks "perbaiki tampilan form", "rapikan layout", "buat migration", or "tambahkan controller", activate the corresponding skills instantly.
2. **Combine matching skills**: Complex tasks require multiple skills working together (e.g., adding a Blade form requires `better-ui` + `better-layout` + `better-colors` + `better-accessibility` + `tailwindcss-development` + `antislop-ui`).
3. **Preserve context efficiency**: Read the core `SKILL.md` first. Only read deep subfiles (e.g. `animations.md`, `contrast.md`) when directly tackling that specific topic.
4. **Report active skills**: At the end of your response, list the active skills applied using a subtle badge format: `> 💡 **Skills Applied**: \`skill-one\`, \`skill-two\``.

---

## 2. Global Domain Matrix & Skill Triggers

| Domain | Trigger Keywords / File Context | Primary Skills to Activate |
| :--- | :--- | :--- |
| **UI Polishing & Surfaces** | `tampilan`, `ui`, `card`, `shadow`, `border`, `radius`, `modal`, `navbar`, `sidebar`, `table`, `elevation` | `.agents/skills/better-ui`<br>`.agents/skills/antislop-ui` |
| **Color System & Contrast** | `warna`, `color`, `palette`, `dark mode`, `contrast`, `badge color`, `gradient`, `accent` | `.agents/skills/better-colors`<br>`.agents/skills/antislop-human` |
| **Layout & Spacing** | `layout`, `grid`, `flex`, `spacing`, `padding`, `margin`, `gap`, `alignment`, `responsive`, `mobile`, `overflow` | `.agents/skills/better-layout`<br>`.agents/skills/antislop-layoutmobile` |
| **Typography & Text Style** | `font`, `typography`, `text size`, `heading`, `truncate`, `line-height`, `font-bold`, `angka`, `format` | `.agents/skills/better-typography` |
| **Accessibility (a11y)** | `accessibility`, `a11y`, `keyboard`, `focus`, `aria`, `screen reader`, `tabindex`, `label`, `contrast` | `.agents/skills/better-accessibility`<br>`.agents/skills/antislop-human` |
| **Tailwind CSS Implementation** | `tailwind`, `class`, `blade`, `css`, `styling`, `hover:`, `focus:`, `sm:`, `md:`, `lg:` | `.agents/skills/tailwindcss-development` |
| **UI Copywriting & UX Writing** | `copy`, `label`, `error message`, `placeholder`, `empty state`, `button text`, `kalimat`, `teks` | `.agents/skills/better-writing`<br>`.agents/skills/antislop-copywriting` |
| **Clean Code & Anti-Slop** | Any code creation/edit, prompt to remove boilerplate, comments cleanup | `.agents/skills/antislop`<br>`.agents/skills/antislop-code` |
| **Component States & Variants** | `component`, `button variant`, `dropdown`, `loading state`, `disabled state`, `skeleton`, `edge case` | `.agents/skills/state-machine`<br>`.agents/skills/variant`<br>`.agents/skills/break` |
| **Laravel Backend & Eloquent** | `controller`, `model`, `migration`, `eloquent`, `query`, `route`, `service`, `form request`, `n+1` | `.agents/skills/laravel-best-practices`<br>`.agents/skills/infer-conventions` |
| **Testing & Quality** | `test`, `phpunit`, `pest`, `feature test`, `unit test`, `assert`, `factory` | `.agents/skills/testing-best-practices` |
| **Performance & Optimization** | `slow`, `lambat`, `optimize`, `speed`, `lazy load`, `core web vitals`, `asset bundle` | `.agents/skills/performance` |
| **Design to Code** | `figma`, `design`, `screenshot`, `mockup`, `tirukan`, `clone ui` | `.agents/skills/build-design`<br>`.agents/skills/explain-interface` |
| **Product & Specs (BMAD)** | `spec`, `prd`, `brief`, `architecture`, `stories`, `sprint`, `epic`, `retrospective` | `.agent/skills/bmad-*` |

---

## 3. Autonomous Execution Workflow

Whenever a task touches any file:
1. **Blade Templates (`resources/views/**/*.blade.php`)**:
   - MUST activate: `better-ui`, `better-colors`, `better-layout`, `better-typography`, `better-accessibility`, `tailwindcss-development`, `antislop-ui`.
   - Never produce generic AI placeholders (e.g. purple gradient blobs, nested unnecessary cards, meaningless icon wraps).
2. **PHP Backend Files (`app/**/*.php`, `routes/**/*.php`, `database/**/*.php`)**:
   - MUST activate: `laravel-best-practices`, `infer-conventions`, `antislop-code`.
   - Prevent N+1 queries, use FormRequests, maintain strict typing and TitleCase enums.
3. **UI Text / Messages / Validations**:
   - MUST activate: `better-writing`, `antislop-copywriting`.
   - Speak directly to Indonesian/English user context with clarity and purpose; no AI generic hype.

See domain-specific rules in `.agents/rules/` for detailed operational instructions.

