# TodoApp — Master Cumulative Plan (`plans/plan.md`)

> **Project**: TodoApp (Laravel 13 + SQLite + Vue 3.5 + Tailwind CSS v4)  
> **Repository**: `https://github.com/nguynbon03/TodoApp`  
> **Architecture**: Standalone 5-Layer Clean Architecture + Vue 3.5 Composition API via Axios REST API (Zero Starter-Kit Boilerplate)

---

## Cumulative Iterations & Milestones Rollup

| Iteration / Phase | Plan Path | Scope Summary | Status |
|---|---|---|---|
| **Phase 1 — Database & Domain Model** | `plans/260311-1410-todo-crud-clean-arch/plan.md` | `todos` migration with composite index `(completed, created_at)`, `Todo` Eloquent model, and `TodoFactory` | `[x] DONE` |
| **Phase 2 — 5-Layer Clean Backend** | `plans/260311-1410-todo-crud-clean-arch/plan.md` | `StoreTodoRequest`, `UpdateTodoRequest`, `CreateTodo`, `UpdateTodo`, `DeleteTodo`, `TodoResource`, `DashboardController`, and `TodoApiController` | `[x] DONE` |
| **Phase 3 — Axios REST API & Web Routing** | `plans/260311-1410-todo-crud-clean-arch/plan.md` | `routes/web.php` (`/` & `/dashboard`) and `routes/api.php` (`/api/todos` CRUD) | `[x] DONE` |
| **Phase 4 — Vue 3.5 Calm Daylight UI** | `plans/261103-1432-refactor-vue-dashboard/plan.md` | `useTodosApi.ts` composable & `Dashboard.vue` refactored to Vue 3 Official Style Guide (`vp check` compliant) | `[x] DONE` |
| **Phase 5 — Zero-Bloat Starter-Kit Prune & Standalone QA** | `plans/261103-1615-todoapp-pr-clean-prune/plan.md` | Pruned 100% of unused `User`/`Team`/`Passkey`/`2FA`/`Settings`/`Sidebar` starter-kit boilerplate; updated standalone Pest suite (`TodoCrudTest.php`) and synced all docs | `[x] DONE` |

---

## Key Architectural Invariants Verified
- [x] **DONE** — **Zero Starter-Kit Bloat**: Only Todo App files remain across `app/`, `routes/`, `resources/js/`, `database/`, and `tests/`.
- [x] **DONE** — **5-Layer Separation**: Thin `TodoApiController` delegates validation to `FormRequest`, business logic to `app/Actions/Todos/*`, persistence to `Todo` model, and serialization to `TodoResource`.
- [x] **DONE** — **Unified Resource Serialization**: `DashboardController` and `TodoApiController` share `TodoResource` (`{ id, title, completed, createdAt }`).
- [x] **DONE** — **Automated Feature Test Coverage**: `tests/Feature/TodoCrudTest.php` verifies `GET /`, `GET /dashboard`, `GET /api/todos`, `POST /api/todos`, `PATCH /api/todos/{todo}`, `DELETE /api/todos/{todo}`, `422` validation rules, and `404` handling.
