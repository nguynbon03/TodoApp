# Plan: Laravel 13 + Vue 3 (Inertia v3 + Fortify + Wayfinder) Auth & Todo CRUD

- **Project**: `/Users/nguynbon03/todoapp`
- **Date**: 2026-11-03 08:20
- **Status**: Stage 1 — Waiting for User Approval

## 1. Hiện trạng Codebase (`/Users/nguynbon03/todoapp`)
- **Đăng nhập / Đăng ký / Đăng xuất (Auth)**: Đã có sẵn **100% chuẩn chính hãng** từ `laravel/fortify` + `laravel/vue-starter-kit`:
  - Trang Đăng nhập: `/login` (`resources/js/pages/auth/Login.vue`)
  - Trang Đăng ký: `/register` (`resources/js/pages/auth/Register.vue`)
  - Nút Đăng xuất: `POST /logout` nằm sẵn ở góc dưới thanh Sidebar (`resources/js/components/UserMenuContent.vue`).
- **Chuẩn Laravel 13 trong repo**:
  - Model dùng PHP 8 Attributes `#[Fillable([...])]` (`app/Models/User.php`).
  - Route sau khi đăng nhập đi qua prefix `{current_team}/dashboard` (`routes/web.php`).
  - Frontend dùng **Vue 3 `<script setup lang="ts">`** + **Inertia v3 `useForm`** + **Wayfinder** + **Tailwind CSS v4 / Reka UI**.

## 2. Kiến trúc CRUD Todo tối giản, hiệu quả & đúng chuẩn Logic
- **Database & Model (`Todo`)**:
  - Migration `create_todos_table`: `id`, `user_id` (foreignId cascadeOnDelete), `title` (string 255), `completed` (boolean default false), `timestamps` + Composite Index `(user_id, completed, created_at)`.
  - Model `app/Models/Todo.php`: Sử dụng `#[Fillable(['user_id', 'title', 'completed'])]`, `casts(): ['completed' => 'boolean']`, và quan hệ `user()`.
  - Quan hệ trong `app/Models/User.php`: thêm `hasMany(Todo::class)`.
- **Validation & Security (Anti-IDOR / Ownership Check)**:
  - `app/Http/Requests/Todo/StoreTodoRequest.php`: validate `title` (`required|string|max:255`).
  - `app/Http/Requests/Todo/UpdateTodoRequest.php`: validate `title` (`sometimes|required|string|max:255`) và `completed` (`sometimes|boolean`).
  - **Anti-IDOR**: Mọi thao tác sửa/đổi trạng thái/xóa (`update`, `destroy`) đều kiểm tra `abort_if($todo->user_id !== $request->user()->id, 403)` (hoặc truy vấn qua `$request->user()->todos()`).
- **Controller & Single-Action / Clean Logic (`app/Http/Controllers/TodoController.php` & `DashboardController.php`)**:
  - Hiển thị danh sách Todo của đúng User đang đăng nhập ngay trên trang `Dashboard` (`/{current_team}/dashboard`) để đăng nhập xong là dùng được ngay lập tức, kèm bộ lọc `All / Active / Completed` và thống kê nhanh.
  - 3 endpoint chuẩn RESTful:
    1. `POST /{current_team}/todos` (`todos.store`) — Tạo mới Todo.
    2. `PATCH /{current_team}/todos/{todo}` (`todos.update`) — Đánh dấu hoàn thành (Toggle) hoặc sửa tiêu đề Todo inline.
    3. `DELETE /{current_team}/todos/{todo}` (`todos.destroy`) — Xóa Todo.
- **Frontend Vue 3 (`resources/js/pages/Dashboard.vue`)**:
  - Sử dụng `useForm` của `@inertiajs/vue3` (4-State FSM: khóa nút khi `processing`, tự xóa ô nhập khi `onSuccess`, hiển thị lỗi validate rõ ràng).
  - Cho phép: Thêm công việc, Tick hoàn thành/chưa hoàn thành, Sửa trực tiếp tên công việc (Edit inline), Xóa công việc, và Lọc theo trạng thái.

## 3. Các Phase Thực Thi (Stage 2 sau khi duyệt)
1. **Phase 1 — Database & Model**: Tạo migration `todos`, model `app/Models/Todo.php`, cập nhật `app/Models/User.php`, chạy `php artisan migrate`.
2. **Phase 2 — Backend Requests, Controller & Routes**: Tạo `StoreTodoRequest`, `UpdateTodoRequest`, `TodoController`, cập nhật `DashboardController` và `routes/web.php`, chạy `php artisan wayfinder:generate`.
3. **Phase 3 — Frontend Vue 3 CRUD UI & Pest Tests**: Cập nhật `resources/js/pages/Dashboard.vue` với giao diện CRUD Todo sạch đẹp + viết Pest test kiểm chứng đầy đủ Auth + CRUD + Anti-IDOR.
