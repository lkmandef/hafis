# Rule: Laravel Backend Architecture, Eloquent & API Standards

This rule governs backend PHP code, Eloquent queries, models, controllers, migrations, form requests, policies, and test suites in this project.

---

## 1. Trigger Conditions
Automatically active whenever:
- User mentions backend concepts: `controller`, `model`, `migration`, `eloquent`, `query`, `route`, `api`, `seeder`, `factory`, `n+1`, `form request`, `database`, `relasi`.
- Active file is in `app/**`, `routes/**`, `database/**`, `config/**`, `tests/**`.
- Editing or creating `.php` files.

---

## 2. Integrated Skills & Operational Standards

### A. Eloquent & Query Optimization (`laravel-best-practices`)
- **Prevent N+1 Queries**: Always eager load relationships using `with([...])` when displaying collections or lists in Blade views or API resources.
- **Select Specific Columns**: For large datasets, avoid `select *`; specify needed fields or use `chunkById()` for memory efficiency.
- **Database Transactions**: Wrap multi-table write operations in `DB::transaction(function () { ... });` to guarantee atomic integrity.
- **Use Database Tools**: Inspect schemas using `database-schema` and test queries using `database-query` before writing raw tinker code.

### B. Controller & Routing Discipline (`laravel-best-practices` + `infer-conventions`)
- **Thin Controllers**: Keep controllers focused on HTTP lifecycle (input reception, authorization check, service/action invocation, response return). Offload complex business logic to dedicated Action classes, Services, or Eloquent Model methods.
- **Form Request Validation**: Move non-trivial validation out of controller closures into dedicated `FormRequest` classes via `php artisan make:request <Name>Request`.
- **Named Routes**: Always generate links using `route('name', ...)` instead of hardcoded URLs.
- **RESTful Resource Controllers**: Follow standard resourceful action names (`index`, `create`, `store`, `show`, `edit`, `update`, `destroy`).

### C. PHP 8.5 Language Rules
- **Constructor Promotion**: Use constructor property promotion: `public function __construct(public MyService $service) {}`.
- **Strict Return Types & Parameter Hints**: Explicitly type-hint all arguments and return types: `public function update(UpdateJadwalRequest $request, Jadwal $jadwal): RedirectResponse`.
- **Enum Keys**: Use `TitleCase` for enum values (`Status::Active`, `Priority::High`).
- **Code Style with Pint**: Always run `vendor/bin/pint --dirty --format agent` or Pint formatter on modified PHP files before concluding changes.

### D. Testing & Quality Enforcement (`testing-best-practices`)
- **Feature Tests First**: Write feature tests for user-facing flows using `php artisan make:test --phpunit <Name>Test`.
- **Model Factories**: Seed data via model factories with semantic state methods (`User::factory()->admin()->create()`).
- **Narrow Test Execution**: Run targeted tests with `php artisan test --filter=<TestName>` or `vendor/bin/phpunit --filter=<TestName>` to verify changes rapidly.

---

## 3. Pre-Delivery Checklist
Before completing any backend change:
- [ ] Are relationships eager-loaded to avoid N+1?
- [ ] Are inputs validated securely?
- [ ] Are DB mutations protected with transactions where appropriate?
- [ ] Has Pint formatted the PHP code cleanly?

