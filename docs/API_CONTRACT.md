# TodoApp — API & Data Contract (`API_CONTRACT.md`)

> **Repository**: `https://github.com/nguynbon03/TodoApp`  
> **Architecture**: Standalone 5-Layer Clean Architecture (Routes → FormRequests → Actions → Eloquent Model → API Resource)

---

## 1. Web & REST API Endpoints (`routes/web.php` & `routes/api.php`)

| Method | Endpoint | Controller / Action | Validation (`FormRequest`) | Response Status & Payload |
|---|---|---|---|---|
| `GET` | `/` | `DashboardController::__invoke` | — | `200 OK` — Inertia `Dashboard` page (`props.todos`: `TodoResource[]`) |
| `GET` | `/dashboard` | `DashboardController::__invoke` | — | `200 OK` — Inertia `Dashboard` page (`props.todos`: `TodoResource[]`) |
| `GET` | `/api/todos` | `TodoApiController::index` | — | `200 OK` — `{ "data": TodoResource[] }` |
| `POST` | `/api/todos` | `TodoApiController::store` → `CreateTodo` | `StoreTodoRequest` (`title`: `required\|string\|max:255`) | `201 Created` — `{ "data": TodoResource }` (`422` on validation error) |
| `PATCH` | `/api/todos/{todo}` | `TodoApiController::update` → `UpdateTodo` | `UpdateTodoRequest` (`title`: `sometimes\|required\|string\|max:255`, `completed`: `sometimes\|boolean`) | `200 OK` — `{ "data": TodoResource }` (`404` if not found, `422` if invalid) |
| `DELETE` | `/api/todos/{todo}` | `TodoApiController::destroy` → `DeleteTodo` | Route Model Binding (`Todo $todo`) | `200 OK` — `{ "message": "Đã xóa công việc thành công." }` (`404` if not found) |

---

## 2. JSON Resource Schema (`App\Http\Resources\TodoResource`)

```json
{
  "id": 1,
  "title": "Hoàn thiện TodoApp Laravel 13 + Vue 3.5",
  "completed": false,
  "createdAt": "1 phút trước"
}
```

| Field | Type | Nullable | Description |
|---|---|---|---|
| `id` | `integer` | No | Khóa chính tự tăng |
| `title` | `string` | No | Tiêu đề công việc đã `trim()` (`1..255` ký tự) |
| `completed` | `boolean` | No | Trạng thái hoàn thành (`true` hoặc `false`) |
| `createdAt` | `string` | No | Thời gian tạo tương đối dễ đọc (`diffForHumans()`, mặc định `'Vừa xong'`) |

---

## 3. SQLite Database Schema (`todos` Table)

```sql
CREATE TABLE "todos" (
  "id" integer PRIMARY KEY AUTOINCREMENT NOT NULL,
  "title" varchar(255) NOT NULL,
  "completed" tinyint(1) NOT NULL DEFAULT '0',
  "created_at" datetime,
  "updated_at" datetime
);

CREATE INDEX "todos_completed_created_at_index"
  ON "todos" ("completed", "created_at");
```

| Column | Type | Constraints | Notes |
|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | `PRIMARY KEY AUTOINCREMENT` | Định danh duy nhất của công việc |
| `title` | `VARCHAR(255)` | `NOT NULL` | Nội dung công việc |
| `completed` | `BOOLEAN` | `NOT NULL DEFAULT false` | Cờ hoàn thành công việc |
| `created_at` | `TIMESTAMP` | `NULLABLE` | Thời điểm tạo |
| `updated_at` | `TIMESTAMP` | `NULLABLE` | Thời điểm cập nhật gần nhất |
