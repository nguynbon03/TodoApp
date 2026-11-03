# 🏛️ SYSTEM ARCHITECTURE & INVARIANTS (`todoapp`)

- **Project Name**: `todoapp`
- **Root Path**: `/Users/nguynbon03/todoapp`
- **Repository**: `https://github.com/nguynbon03/TodoApp`
- **Stack**: Laravel 13 + SQLite + Inertia v3 + Vue 3.5 (`<script setup lang="ts">`) + Tailwind CSS v4 + Axios REST API + Pest v4
- **Architecture Pattern**: Standalone 5-Layer Clean Architecture (Zero Starter-Kit Boilerplate)

---

## 1. Architectural Overview (5-Layer Clean Architecture)

Dự án đã được tỉa gọn 100% các module starter-kit không cần thiết (`User`, `Team`, `TeamInvitation`, `Passkey`, `TwoFactor`, `Settings`, `Sidebar`, `shadcn/ui` dư thừa) để tập trung duy nhất vào nghiệp vụ **Todo App**:

1. **Layer 1 — Presentation UI (`resources/js/`)**:
   - [`resources/js/pages/Dashboard.vue`](file:///Users/nguynbon03/todoapp/resources/js/pages/Dashboard.vue): Giao diện quản lý công việc Calm Daylight (60-30-10), gồm 4 thẻ thống kê (`Tổng số việc`, `Đang làm`, `Đã hoàn thành`, `Tiến độ`), thanh tiến độ GPU `scaleX`, bộ lọc (`all | pending | completed`), thêm mới, sửa trực tiếp (inline edit), đổi trạng thái và xóa.
   - [`resources/js/composables/useTodosApi.ts`](file:///Users/nguynbon03/todoapp/resources/js/composables/useTodosApi.ts): Composable quản lý state danh sách công việc và giao tiếp Axios với `/api/todos`.
2. **Layer 2 — Routing & Thin Controllers (`routes/`, `app/Http/Controllers/`)**:
   - [`routes/web.php`](file:///Users/nguynbon03/todoapp/routes/web.php): Định tuyến `GET /` và `GET /dashboard` vào [`DashboardController.php`](file:///Users/nguynbon03/todoapp/app/Http/Controllers/DashboardController.php).
   - [`routes/api.php`](file:///Users/nguynbon03/todoapp/routes/api.php): Định tuyến `Route::apiResource('todos', TodoApiController::class)` cho 4 thao tác `index`, `store`, `update`, `destroy`.
3. **Layer 3 — Input Validation (`app/Http/Requests/Todos/`)**:
   - [`StoreTodoRequest.php`](file:///Users/nguynbon03/todoapp/app/Http/Requests/Todos/StoreTodoRequest.php): Kiểm tra `title` (`required|string|max:255`).
   - [`UpdateTodoRequest.php`](file:///Users/nguynbon03/todoapp/app/Http/Requests/Todos/UpdateTodoRequest.php): Kiểm tra `title` (`sometimes|required|string|max:255`) và `completed` (`sometimes|boolean`).
4. **Layer 4 — Domain Actions & Persistence (`app/Actions/Todos/`, `app/Models/Todo.php`)**:
   - [`CreateTodo.php`](file:///Users/nguynbon03/todoapp/app/Actions/Todos/CreateTodo.php), [`UpdateTodo.php`](file:///Users/nguynbon03/todoapp/app/Actions/Todos/UpdateTodo.php), [`DeleteTodo.php`](file:///Users/nguynbon03/todoapp/app/Actions/Todos/DeleteTodo.php): Các class thực thi nghiệp vụ đơn nhiệm, tự động `trim()` tiêu đề và thao tác với Eloquent Model [`Todo.php`](file:///Users/nguynbon03/todoapp/app/Models/Todo.php).
5. **Layer 5 — Serialization Contract (`app/Http/Resources/TodoResource.php`)**:
   - [`TodoResource.php`](file:///Users/nguynbon03/todoapp/app/Http/Resources/TodoResource.php): Chuẩn hóa đầu ra `{ id, title, completed, createdAt }` đồng nhất cho cả SSR Inertia và JSON REST API.

---

## 2. Canonical Directory Map (Pruned Zero-Bloat Layout)

| Path | Purpose |
|---|---|
| `app/Actions/Todos/{CreateTodo,UpdateTodo,DeleteTodo}.php` | Domain Actions xử lý tạo, cập nhật và xóa Todo |
| `app/Http/Controllers/DashboardController.php` | Render trang `Dashboard` kèm danh sách `todos` khởi tạo |
| `app/Http/Controllers/Api/TodoApiController.php` | REST API Controller cho `/api/todos` |
| `app/Http/Requests/Todos/{StoreTodoRequest,UpdateTodoRequest}.php` | FormRequest kiểm duyệt dữ liệu đầu vào |
| `app/Http/Resources/TodoResource.php` | JSON Resource chuẩn hóa cấu trúc dữ liệu Todo |
| `app/Models/Todo.php` | Eloquent Model ánh xạ bảng `todos` |
| `database/migrations/2026_11_03_000001_create_todos_table.php` | Migration bảng `todos` kèm composite index `(completed, created_at)` |
| `database/factories/TodoFactory.php` | Factory phục vụ kiểm thử tự động |
| `resources/js/pages/Dashboard.vue` | Giao diện Todo App chính (Vue 3.5 Composition API) |
| `resources/js/composables/useTodosApi.ts` | Axios client & state composable |
| `resources/js/types/todos.ts` | Định nghĩa kiểu TypeScript `TodoItem`, `TodoFilter` |
| `tests/Feature/TodoCrudTest.php` | Bộ kiểm thử Feature Pest v4 toàn diện |

---

## 3. Core Architectural Invariants

1. **Zero Starter-Kit Dead Code**: Không tồn tại model, migration, controller hay Vue component thừa nào ngoài phạm vi Todo App.
2. **Single Source of Serialization**: Cả `DashboardController` và `TodoApiController` đều sử dụng `TodoResource` để đảm bảo kiểu dữ liệu khớp 100% với `TodoItem` trong TypeScript.
3. **Database Indexing**: Bảng `todos` được đánh chỉ mục kép `(completed, created_at)` phục vụ truy vấn lọc và sắp xếp mới nhất.
