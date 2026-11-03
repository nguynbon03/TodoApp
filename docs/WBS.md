# TodoApp — Master Work Breakdown Structure (`WBS.md`)

> **Repository**: `https://github.com/nguynbon03/TodoApp`  
> **Stack**: Laravel 13 (PHP 8.5) + SQLite + Vue 3.5 Composition API + Tailwind CSS v4 + Pest v4  
> **Status**: `100% COMPLETED` (`[x] DONE`)

---

## Phase 1: Database Migration, Eloquent Model & Factory
- [x] **DONE** — Create `database/migrations/2026_11_03_000001_create_todos_table.php` with `title`, `completed`, timestamps, and composite index `(completed, created_at)`.
- [x] **DONE** — Implement standalone `app/Models/Todo.php` with `$fillable = ['title', 'completed']` and boolean casting for `completed`.
- [x] **DONE** — Create `database/factories/TodoFactory.php` with default and `completed()` states.

## Phase 2: 5-Layer Clean Architecture (FormRequests, Actions, Resource, Controllers)
- [x] **DONE** — Implement `StoreTodoRequest` and `UpdateTodoRequest` under `app/Http/Requests/Todos/` with strict validation rules (`required|string|max:255`).
- [x] **DONE** — Implement single-responsibility Actions under `app/Actions/Todos/`:
  - [x] **DONE** — `CreateTodo.php` (trims `title`, sets `completed = false`).
  - [x] **DONE** — `UpdateTodo.php` (trims `title` if present, updates `title`/`completed`).
  - [x] **DONE** — `DeleteTodo.php` (deletes `Todo` record).
- [x] **DONE** — Implement `app/Http/Resources/TodoResource.php` returning `{ id, title, completed, createdAt }`.
- [x] **DONE** — Implement `DashboardController.php` and `Api/TodoApiController.php` sharing `TodoResource`.

## Phase 3: Standalone Web & Axios REST API Routing (`routes/web.php` & `routes/api.php`)
- [x] **DONE** — Configure `GET /` and `GET /dashboard` directly to `DashboardController` in `routes/web.php`.
- [x] **DONE** — Register `/api/todos` REST resource endpoints (`GET`, `POST`, `PATCH /api/todos/{todo}`, `DELETE /api/todos/{todo}`) in `routes/api.php`.

## Phase 4: Standalone Vue 3.5 Composition API Frontend (`Dashboard.vue` & `useTodosApi.ts`)
- [x] **DONE** — Build `resources/js/composables/useTodosApi.ts` managing reactive state, filters (`all`, `pending`, `completed`), computed counters, and Axios CRUD calls.
- [x] **DONE** — Build `resources/js/pages/Dashboard.vue` following the 60-30-10 Calm Daylight palette, 4 summary metric cards, GPU `scaleX` progress bar, filter tabs, inline editing, and Vue 3 Official Style Guide formatting.

## Phase 5: Starter-Kit Boilerplate Purge & Zero-Bloat Verification
- [x] **DONE** — Remove all unused starter-kit files (`User`, `Team`, `TeamInvitation`, `Passkey`, `TwoFactor`, `Settings`, `Fortify`, unused `resources/js/components/*`, `layouts/*`, `pages/auth/*`, `pages/settings/*`, `pages/teams/*`, and starter tests).
- [x] **DONE** — Update `tests/Feature/TodoCrudTest.php` to thoroughly test `GET /`, `GET /dashboard`, `GET /api/todos`, `POST /api/todos`, `PATCH /api/todos/{todo}`, `DELETE /api/todos/{todo}`, `422` validation errors, and `404` not-found responses without `User`/`Team` boilerplate.
- [x] **DONE** — Sync `README.md`, `docs/ARCHITECTURE.md`, `docs/API_CONTRACT.md`, `docs/WBS.md`, and `plans/plan.md` with the pruned standalone architecture.
