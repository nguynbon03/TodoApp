# TodoApp — API & Data Contract (`API_CONTRACT.md`)

> **Repository**: `https://github.com/nguynbon03/TodoApp`  
> **Architecture**: 5-Layer Clean Architecture (Routes → FormRequests → Actions → Eloquent Models → API Resources)  
> **Authentication**: Laravel Stateful SPA Auth (`web` + `auth` middleware via Axios CSRF cookie & session)

---

## 1. REST API Endpoints (`routes/api.php`)

All endpoints are prefixed with `/api` and protected by `['web', 'auth']` middleware + `throttle:todos` rate limiter. Every endpoint enforces **Anti-IDOR / BOLA Ownership Invariant** (`$todo->user_id === $request->user()->id`, returning `403 Forbidden` on mismatch).

| Method | Endpoint | Action Class | Validation (`FormRequest`) | Response Status & Payload |
|---|---|---|---|---|
| `GET` | `/api/todos` | `ListTodosAction` | `IndexTodoRequest` (`filter`: `all\|active\|completed`, `search`: `nullable\|string\|max:120`) | `200 OK` — `{ "data": TodoResource[], "meta": { "total": int, "active": int, "completed": int } }` |
| `POST` | `/api/todos` | `CreateTodoAction` | `StoreTodoRequest` (`title`: `required\|string\|min:1\|max:255`) | `201 Created` — `{ "data": TodoResource }` |
| `PATCH` | `/api/todos/{todo}` | `UpdateTodoAction` | `UpdateTodoRequest` (`title`: `sometimes\|required\|string\|min:1\|max:255`, `completed`: `sometimes\|boolean`) | `200 OK` — `{ "data": TodoResource }` (`403` if non-owner) |
| `DELETE` | `/api/todos/{todo}` | `DeleteTodoAction` | Ownership check in controller/policy | `204 No Content` (`403` if non-owner) |

---

## 2. JSON Resource Schema (`App\Http\Resources\TodoResource`)

```json
{
  "id": 1,
  "title": "Ship Laravel 13 + Vue 3.5 TodoApp",
  "completed": false,
  "createdAt": "2026-11-03T07:20:00.000000Z",
  "updatedAt": "2026-11-03T07:20:00.000000Z"
}
```

| Field | Type | Nullable | Description |
|---|---|---|---|
| `id` | `integer` | No | Auto-incrementing primary key |
| `title` | `string` | No | Trimmed task title (`1..255` characters) |
| `completed` | `boolean` | No | Completion state (`true` or `false`) |
| `createdAt` | `string (ISO-8601)` | No | UTC creation timestamp |
| `updatedAt` | `string (ISO-8601)` | No | UTC last-update timestamp |

---

## 3. SQLite Database Schema (`todos` Table)

```sql
CREATE TABLE "todos" (
  "id" integer PRIMARY KEY AUTOINCREMENT NOT NULL,
  "user_id" integer NOT NULL,
  "title" varchar(255) NOT NULL,
  "completed" tinyint(1) NOT NULL DEFAULT '0',
  "created_at" datetime,
  "updated_at" datetime,
  FOREIGN KEY("user_id") REFERENCES "users"("id") ON DELETE CASCADE
);

-- ESR Rule Composite B-Tree Index (Equality: user_id, completed -> Sort: created_at)
CREATE INDEX "todos_user_id_completed_created_at_index"
  ON "todos" ("user_id", "completed", "created_at");
```

| Column | Type | Constraints | Notes |
|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | `PRIMARY KEY AUTOINCREMENT` | Unique row identifier |
| `user_id` | `BIGINT UNSIGNED` | `NOT NULL, FOREIGN KEY -> users(id) CASCADE` | Tenant/owner isolation key |
| `title` | `VARCHAR(255)` | `NOT NULL` | Todo title |
| `completed` | `BOOLEAN` | `NOT NULL DEFAULT false` | Completion flag |
| `created_at` | `TIMESTAMP` | `NULLABLE` | Created timestamp |
| `updated_at` | `TIMESTAMP` | `NULLABLE` | Updated timestamp |
