# Rule: BMAD Agile & Product Lifecycle Workflows

This rule integrates the BMAD (Breakthrough Method for Agile AI Development) skill suite found in `.agent/skills/`. It coordinates product discovery, specification writing, technical architecture, sprint tracking, and code review workflows.

---

## 1. Trigger Conditions
Automatically active whenever:
- Planning features, writing PRDs, drafting specifications, or creating product briefs.
- Discussing system architecture, database design spine, or technical solution designs.
- Breaking down features into epics and user stories or managing sprint status.
- Conducting structured multi-perspective code reviews or retrospectives.
- User invokes BMAD personas (John/PM, Winston/Architect, Amelia/Dev, Sally/UX Designer, Mary/Analyst) or requests party mode.

---

## 2. Integrated Skills & Workflow Stages

| Phase | Activity | Primary BMAD Skill(s) |
| :--- | :--- | :--- |
| **Discovery & Ideation** | Testing half-formed ideas, competitive/domain research, brainstorming | `bmad-forge-idea`<br>`bmad-deep-recon`<br>`bmad-brainstorming` |
| **Product Definition** | PRD creation, product briefs, Amazon-style PRFAQ | `bmad-prd`<br>`bmad-product-brief`<br>`bmad-prfaq` |
| **UX & Experience** | Visual design specifications (DESIGN.md) and behavior flows (EXPERIENCE.md) | `bmad-ux`<br>`bmad-agent-ux-designer` |
| **Architecture** | System architecture spine, data models, technical trade-offs | `bmad-architecture`<br>`bmad-agent-architect` |
| **Decomposition** | Distilling specs, generating epics and user stories, sprint tracking | `bmad-spec`<br>`bmad-create-epics-and-stories`<br>`bmad-sprint-planning` |
| **Implementation** | Feature implementation, unattended development loops | `bmad-build`<br>`bmad-build-auto`<br>`bmad-agent-dev` |
| **Review & Quality** | Parallel multi-perspective reviews, walkthroughs, automated E2E tests | `bmad-code-review`<br>`bmad-review`<br>`bmad-walkthrough`<br>`bmad-qa-generate-e2e-tests` |
| **Retrospective & Course Correction** | Assessing sprint changes, reviewing completed epics | `bmad-correct-course`<br>`bmad-retrospective` |

---

## 3. Operational Protocol
When a BMAD workflow is triggered:
1. Identify the appropriate artifact template from the relevant skill's `assets/` or `references/` folder.
2. Read the skill instructions before generating the artifact.
3. Adhere to the established document conventions without unnecessary fluff.

