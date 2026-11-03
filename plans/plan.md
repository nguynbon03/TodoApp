# TodoApp — Master Cumulative Plan (`plans/plan.md`)

> **Project**: TodoApp (Laravel 13 + SQLite + Vue 3.5 + Tailwind CSS v4)  
> **Repository**: `https://github.com/nguynbon03/TodoApp`  
> **Architecture**: Option B ⭐ — 5-Layer Clean Architecture + Standalone Vue 3.5 Composition API via Axios REST API

---

## Cumulative Iterations & Milestones Rollup

| Iteration / Phase | Plan Path | Scope Summary | Status |
|---|---|---|---|
| **Phase 1 — Database & Domain Model** | `plans/260311-1410-todo-crud-clean-arch/plan.md` | `todos` migration with ESR composite index `(user_id, completed, created_at)`, strict `Todo` Eloquent model, `TodoFactory`, and `User::todos()` relation | `[x] DONE` |
| **Phase 2 — 5-Layer Clean Backend** | `plans/260311-1410-todo-crud-clean-arch/plan.md` | `IndexTodoRequest`, `StoreTodoRequest`, `UpdateTodoRequest`, `ListTodosAction`, `CreateTodoAction`, `UpdateTodoAction`, `DeleteTodoAction`, `TodoResource`, and `TodoController` with Anti-IDOR guard | `[x] DONE` |
| **Phase 3 — Stateful Axios REST API** | `plans/260311-1410-todo-crud-clean-arch/plan.md` | `routes/api.php` (`/api/todos` CRUD) with stateful session auth + `throttle:todos` rate limiting | `[x] DONE` |
| **Phase 4 — Vue 3.5 Calm Daylight UI** | `plans/260311-1410-todo-crud-clean-arch/plan.md` | `useTodosApi.ts` composable (4-State FSM + optimistic updates) & `Dashboard.vue` (60-30-10 Calm Daylight palette, 4 summary cards, filter/search, inline edit) | `[x] DONE` |
| **Phase 5 — Pest Tests & GitHub Release** | `plans/260311-1410-todo-crud-clean-arch/plan.md` | 15/15 passing Pest feature tests (`TodoCrudTest.php`), Vite asset build, Lean 3+2 docs sync, and GitHub publication to `https://github.com/nguynbon03/TodoApp` | `[x] DONE` |

---

## Key Architectural Invariants Verified
- [x] **DONE** — **5-Layer Separation**: Thin `TodoController` delegates validation to `FormRequest`, business logic to `app/Actions/Todo/*`, persistence to `Todo` model, and serialization to `TodoResource`.
- [x] **DONE** — **Anti-IDOR / BOLA Protection**: Cross-tenant `PATCH` and `DELETE` requests strictly return `403 Forbidden`.
- [x] **DONE** — **ESR Composite Index**: `(user_id, completed, created_at)` index eliminates full-table scans on filtered user queries.
- [x] **DONE** — **4-State Frontend FSM**: `useTodosApi.ts` models `idle -> loading -> success | error` with optimistic toggle/delete rollback.
- [x] **DONE** — **GitHub Publication**: Repository documented and ready for push to `https://github.com/nguynbon03/TodoApp`.
