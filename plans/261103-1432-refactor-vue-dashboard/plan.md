# Plan: Chuẩn Hóa Code Vue 3 (`Dashboard.vue`) Theo Chuẩn Vue Official Style Guide & Vite+ (`vp check`)

> **Date**: 2026-11-03 14:32  
> **Project**: `/Users/nguynbon03/todoapp`  
> **Target File**: `resources/js/pages/Dashboard.vue` (và tùy chọn tách sub-components nếu chọn Phương án B/C)  
> **Status**: `COMPLETED ([x] DONE)`

---

## 1. 🌐 Bảng Dẫn Chứng Search Web & GitHub Thực Tế (Live Citations & URLs)

1. **[Vue.js Official Style Guide — Priority B: Multi-Attribute Elements (`max-attributes-per-line`)](https://vuejs.org/style-guide/rules-strongly-recommended.html#multi-attribute-elements)** — Quy định chính thức của đội ngũ cốt lõi Vue (Evan You): Các thẻ có nhiều hơn 1 thuộc tính (attribute/prop/event) BẮT BUỘC phải xuống dòng, mỗi dòng 1 thuộc tính để dễ đọc và dễ nhìn diff khi review code trên Git.
2. **[ESLint Plugin Vue — `vue/singleline-html-element-content-newline` & `vue/max-attributes-per-line`](https://eslint.vuejs.org/rules/max-attributes-per-line.html)** — Quy tắc chuẩn công nghiệp: cấm nhồi nhét `<Button ...><Check class="..." /></Button>` hoặc 16 biến destructure trên cùng 1 dòng dài hơn 100 ký tự.
3. **[Laravel Official Vue Starter Kit (`laravel/vue-starter-kit`)](https://github.com/laravel/vue-starter-kit)** & ngay trong chính dự án tại [`resources/js/pages/teams/Index.vue`](file:///Users/nguynbon03/todoapp/resources/js/pages/teams/Index.vue#L10-L120) — Sử dụng `type Props = { ... }`, xuống dòng từng import/destructure dài, mỗi prop trên 1 dòng và kiểm tra tự động bằng lệnh `npm run check` (`vp check`).

---

## 2. 🔍 Chẩn Đoán 6 Lỗi "Viết Dồn 1 Dòng" Trong `Dashboard.vue` Khiến Mentor Nhắc Nhở

| # | Vị trí dòng (`@Lstart-end`) | Lỗi hiện tại trong `Dashboard.vue` | Chuẩn Chuyên Gia Vue 3 (Evan You / Anthony Fu / Laravel Vue Kit) |
|---|---|---|---|
| **1** | [`@L16`](file:///Users/nguynbon03/todoapp/resources/js/pages/Dashboard.vue#L16) | **Destructure 16 biến trên 1 dòng dài 246 ký tự**: `const { todos, filter, loading, submitting, ... } = useTodosApi(props.todos);` | Xuống dòng từng biến hoặc gom nhóm theo chú thích (`// State`, `// Computed`, `// Actions`), tối đa `80-100` ký tự/dòng. |
| **2** | [`@L3`](file:///Users/nguynbon03/todoapp/resources/js/pages/Dashboard.vue#L3) | **Import 10 icon trên 1 dòng dài**: `import { Check, CheckCircle2, ... } from '@lucide/vue';` | Khi import `>= 5` phần tử phải tách mỗi phần tử 1 dòng (giống hệt `resources/js/pages/teams/Index.vue#L10-L15`). |
| **3** | [`@L22-L42`](file:///Users/nguynbon03/todoapp/resources/js/pages/Dashboard.vue#L22-L42) | **Viết `if (...) return ...` tắt trên cùng 1 dòng & đặt `onMounted` lộn xộn trước hàm xử lý**: `onMounted(() => { void fetchTodos(); });` và `if (await createTodo(...)) newTitle.value = '';` | Đặt `onMounted` xuống cuối `<script setup>` (theo thứ tự chuẩn: Imports ➔ Props ➔ Composables/State ➔ Computed ➔ Functions ➔ Lifecycle Hooks) và luôn mở ngoặc nhọn `{ ... }` nhiều dòng cho `if`. |
| **4** | [`@L120-L137`](file:///Users/nguynbon03/todoapp/resources/js/pages/Dashboard.vue#L120-L137) | **Lặp lại 3 nút Filter và 3 thẻ Thống kê thủ công trên 1 dòng**: 3 dòng `<Button size="sm" :variant="filter === ..." @click="filter = ...">` viết dồn ngang | Khai báo mảng hằng số `filterOptions` / `statCards` trong `<script setup>` rồi dùng `v-for` trong `<template>` để code gọn một nửa và không lặp lại (DRY). |
| **5** | [`@L147-L163`](file:///Users/nguynbon03/todoapp/resources/js/pages/Dashboard.vue#L147-L163) | **Nhồi 5–6 props + thẻ con vào 1 dòng dài 195 ký tự**: `<Button size="icon" variant="ghost" class="..." :disabled="..." @click="..."><Pencil class="size-4" /></Button>` | Tuân thủ `vue/max-attributes-per-line`: Xuống dòng từng prop (`size`, `variant`, `class`, `:disabled`, `@click`) và tách thẻ con `<Pencil />` ra dòng riêng. |
| **6** | [`@L145-L165`](file:///Users/nguynbon03/todoapp/resources/js/pages/Dashboard.vue#L145-L165) | **Logic sửa tại chỗ (`editingId`, `editTitle`, `startEdit`, `saveEdit`, `cancelEdit`) trộn lẫn trong trang chính `Dashboard.vue`** | Có thể giữ gọn trong file hoặc tách thành component con `<TodoItemRow />` sử dụng `defineProps` & `defineEmits` chuẩn SRP. |

---

## 3. ⚖️ 3 Phương Án Cải Tiến (Options Matrix)

- **Phương Án A — Chuẩn Hóa Ngay Trong 1 File `Dashboard.vue` (Giữ nguyên 1 file, format chuẩn Vue Style Guide + `v-for` cho Stats/Filter)**
- **Phương Án B ⭐ (Khuyên Dùng) — Chuẩn Hóa `Dashboard.vue` + Chạy `vp check --fix` + Gom mảng `statCards` & `filterTabs` chuẩn DRY (Gọn, Sạch, Dễ Giải Thích Cho Mentor)**
- **Phương Án C — Tách Component Con (`TodoStatsCard.vue` + `TodoItemRow.vue`) + Chuẩn Hóa `Dashboard.vue`**

---

## 4. Các Bước Thực Thi (Sau Khi User Duyệt)
- [x] **Phase 1**: Chuẩn hóa `<script setup>` và `<template>` của `resources/js/pages/Dashboard.vue` theo phương án B ⭐ (Official Vue 3 Style Guide + DRY `v-for` cho `statCards` & `filterTabs` + thứ tự 7 mục trong `<script setup>`).
- [x] **Phase 2**: Chạy `npx vp check` (0 warnings, 0 errors), `npm run types:check` (`vue-tsc --noEmit` pass 100%), và `php artisan test --filter=TodoCrudTest` (7/7 tests, 43 assertions pass).
- [x] **Phase 3**: Cập nhật `[x] DONE` vào `plans/plan.md` và `docs/WBS.md`.
