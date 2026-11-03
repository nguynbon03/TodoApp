# TodoApp — Master Work Breakdown Structure (`WBS.md`)

> **Repository**: `https://github.com/nguynbon03/TodoApp`  
> **Stack**: Laravel 13 (PHP 8.5) + SQLite + Vue 3.5 Composition API + Tailwind CSS v4 + Pest v4  
> **Status**: `100% COMPLETED` (`[x] DONE`)

---

## Phase 1: Database Migration, Eloquent Model & Factory
- [x] **DONE** — Create `database/migrations/2026_03_11_000001_create_todos_table.php` with `user_id` foreign key (`cascadeOnDelete`), `title`, `completed`, and ESR composite index `(user_id, completed, created_at)`.
- [x] **DONE** — Implement `app/Models/Todo.php` with `declare(strict_types=1)`, `$fillable`, boolean casting for `completed`, and `user(): BelongsTo` relationship.
- [x] **DONE** — Add `todos(): HasMany` relation to `app/Models/User.php`.
- [x] **DONE** — Create `database/factories/TodoFactory.php` with `completed()` and `pending()` states.

## Phase 2: 5-Layer Clean Architecture (FormRequests, Actions, Resource, Controller)
- [x] **DONE** — Implement `IndexTodoRequest`, `StoreTodoRequest`, and `UpdateTodoRequest` under `app/Http/Requests/Todo/` with input trimming and strict validation rules.
- [x] **DONE** — Implement single-responsibility Actions under `app/Actions/Todo/`:
  - [x] **DONE** — `ListTodosAction.php` (scoped query + filter + search + single-source aggregate counts).
  - [x] **DONE** — `CreateTodoAction.php` (DB transaction + default `completed = false`).
  - [x] **DONE** — `UpdateTodoAction.php` (partial title/completed update).
  - [x] **DONE** — `DeleteTodoAction.php` (transactional delete).
- [x] **DONE** — Implement `app/Http/Resources/TodoResource.php` returning `{ id, title, completed, createdAt, updatedAt }`.
- [x] **DONE** — Implement `app/Http/Controllers/Api/TodoController.php` with Anti-IDOR ownership guard (`abort_if($todo->user_id !== $request->user()->id, 403)`).

## Phase 3: Stateful Axios REST API Routing (`routes/api.php`)
- [x] **DONE** — Register `/api/todos` REST endpoints (`GET`, `POST`, `PATCH /api/todos/{todo}`, `DELETE /api/todos/{todo}`) in `routes/api.php` using `['web', 'auth', 'throttle:todos']` middleware.
- [x] **DONE** — Configure `todos` rate limiter in `AppServiceProvider.php` and register API routing in `bootstrap/app.php`.

## Phase 4: Standalone Vue 3.5 Composition API Frontend (`Dashboard.vue` & `useTodosApi.ts`)
- [x] **DONE** — Build `resources/js/composables/useTodosApi.ts` with 4-State FSM (`idle | loading | success | error`), normalized reactive state, Axios CSRF/credentials config, filter/search query sync, and optimistic toggle/delete rollback.
- [x] **DONE** — Build `resources/js/pages/Dashboard.vue` following the 60-30-10 Calm Daylight palette, 4 summary metric cards (`Total`, `Pending`, `Completed`, `Completion Rate`), filter pills (`All`, `Active`, `Completed`), search input, inline edit, and accessible ARIA states.

## Phase 5: Automated QA, Pest Feature Suite & GitHub Publication
- [x] **DONE** — Implement `tests/Feature/Todo/TodoCrudTest.php` covering 15/15 Pest feature tests (CRUD, validation, filter/search, single-source stats, unauthenticated guard, and Anti-IDOR/BOLA cross-user protection).
- [x] **DONE** — Verify production build (`npm run build`) and full test suite (`php artisan test`).
- [x] **DONE** — Update Lean 3+2 documentation (`docs/ARCHITECTURE.md`, `docs/API_CONTRACT.md`, `docs/WBS.md`, `plans/plan.md`) and publish to `https://github.com/nguynbon03/TodoApp`.
