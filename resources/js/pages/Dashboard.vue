<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Check, CheckCircle2, Circle, ListTodo, LogOut, Pencil, Plus, RefreshCw, Trash2, X } from '@lucide/vue';
import { computed, onMounted, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useTodosApi } from '@/composables/useTodosApi';
import { logout } from '@/routes';
import type { TodoItem } from '@/types';

const props = withDefaults(defineProps<{ todos?: TodoItem[] }>(), { todos: () => [] });

const page = usePage();
const user = computed(() => page.props.auth?.user);

const { todos, filter, loading, submitting, busyId, errorMessage, lastApiCall, totalCount, completedCount, activeCount, completionPercent, filteredTodos, fetchTodos, createTodo, toggleTodo, updateTodoTitle, deleteTodo } = useTodosApi(props.todos);

const newTitle = ref('');
const editingId = ref<number | null>(null);
const editTitle = ref('');

onMounted(() => { void fetchTodos(); });

async function onCreateTodo(): Promise<void> {
    if (submitting.value) return;
    if (await createTodo(newTitle.value)) newTitle.value = '';
}

function startEdit(todo: TodoItem): void {
    editingId.value = todo.id;
    editTitle.value = todo.title;
}

function cancelEdit(): void {
    editingId.value = null;
    editTitle.value = '';
}

async function saveEdit(todo: TodoItem): Promise<void> {
    if (editTitle.value.trim() === todo.title) return cancelEdit();
    if (await updateTodoTitle(todo, editTitle.value)) cancelEdit();
}
</script>

<template>
    <Head title="TodoVue — Quản lý Công việc (Vue 3 + Axios)" />

    <div class="min-h-screen bg-slate-50 text-slate-900 dark:bg-zinc-950 dark:text-zinc-100">
        <!-- Custom Top Navbar -->
        <header class="sticky top-0 z-20 border-b border-slate-200/80 bg-white/90 backdrop-blur dark:border-zinc-800 dark:bg-zinc-900/90">
            <div class="mx-auto flex max-w-5xl flex-wrap items-center justify-between gap-3 px-4 py-3.5">
                <div class="flex flex-wrap items-center gap-2.5">
                    <div class="flex size-9 items-center justify-center rounded-lg bg-emerald-600 text-white shadow-xs">
                        <ListTodo class="size-5" />
                    </div>
                    <span class="text-lg font-bold tracking-tight">TodoVue</span>
                    <span class="rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600 dark:bg-zinc-800 dark:text-zinc-300">
                        Vue 3 + Axios + routes/api.php
                    </span>
                    <span class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 font-mono text-xs text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/60 dark:text-emerald-300">
                        {{ lastApiCall }}
                    </span>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    <div v-if="user" class="hidden text-right text-xs sm:block">
                        <span class="font-semibold text-slate-800 dark:text-zinc-200">{{ user?.name }}</span>
                        <span class="mx-1 text-slate-400">•</span>
                        <span class="text-slate-500 dark:text-zinc-400">{{ user?.email }}</span>
                    </div>
                    <Button variant="outline" size="sm" :disabled="loading" @click="fetchTodos">
                        <RefreshCw class="mr-1.5 size-3.5" :class="{ 'animate-spin': loading }" />
                        Làm mới API (GET /api/todos)
                    </Button>
                    <Link
                        :href="logout()"
                        method="post"
                        as="button"
                        class="inline-flex h-8 items-center gap-1.5 rounded-md border border-red-200 bg-red-50 px-3 text-xs font-semibold text-red-600 transition hover:bg-red-100 dark:border-red-900/60 dark:bg-red-950/50 dark:text-red-400"
                    >
                        <LogOut class="size-3.5" />
                        Đăng xuất
                    </Link>
                </div>
            </div>
        </header>

        <!-- Main Centered Container -->
        <main class="mx-auto max-w-3xl space-y-6 px-4 py-8">
            <!-- Stats & Progress Card -->
            <section class="rounded-xl border border-slate-200/80 bg-white p-5 shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
                <div class="grid grid-cols-3 gap-3 text-center">
                    <div class="rounded-lg bg-slate-50 p-3 dark:bg-zinc-800/60">
                        <p class="text-xs text-slate-500 dark:text-zinc-400">Tổng công việc</p>
                        <p class="mt-1 text-2xl font-bold">{{ totalCount }}</p>
                    </div>
                    <div class="rounded-lg bg-amber-50/70 p-3 dark:bg-amber-950/30">
                        <p class="text-xs text-amber-700 dark:text-amber-300">Đang thực hiện</p>
                        <p class="mt-1 text-2xl font-bold text-amber-700 dark:text-amber-400">{{ activeCount }}</p>
                    </div>
                    <div class="rounded-lg bg-emerald-50/70 p-3 dark:bg-emerald-950/30">
                        <p class="text-xs text-emerald-700 dark:text-emerald-300">Đã hoàn thành</p>
                        <p class="mt-1 text-2xl font-bold text-emerald-700 dark:text-emerald-400">{{ completedCount }}</p>
                    </div>
                </div>
                <div class="mt-4 space-y-1.5">
                    <div class="flex justify-between text-xs font-medium text-slate-600 dark:text-zinc-400">
                        <span>Tiến độ hoàn thành</span>
                        <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ completionPercent }}%</span>
                    </div>
                    <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-zinc-800">
                        <div class="h-full rounded-full bg-emerald-600 transition-all duration-200" :style="{ width: `${completionPercent}%` }" />
                    </div>
                </div>
            </section>

            <!-- Create Todo Form & List Card -->
            <section class="rounded-xl border border-slate-200/80 bg-white p-5 shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
                <form class="flex flex-col gap-2.5 sm:flex-row" @submit.prevent="onCreateTodo">
                    <Input v-model="newTitle" placeholder="Nhập công việc cần làm..." class="flex-1" :disabled="submitting" />
                    <Button type="submit" :disabled="submitting || !newTitle.trim()" class="bg-emerald-600 text-white hover:bg-emerald-700">
                        <Plus class="mr-1.5 size-4" />
                        + Thêm công việc
                    </Button>
                </form>

                <p v-if="errorMessage" class="mt-2.5 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-medium text-red-600 dark:border-red-900/50 dark:bg-red-950/40 dark:text-red-400">
                    {{ errorMessage }}
                </p>

                <!-- Filter Tabs -->
                <div class="mt-5 flex items-center justify-between border-b border-slate-100 pb-3 dark:border-zinc-800">
                    <div class="flex gap-1.5">
                        <Button size="sm" :variant="filter === 'all' ? 'default' : 'ghost'" @click="filter = 'all'">Tất cả</Button>
                        <Button size="sm" :variant="filter === 'active' ? 'default' : 'ghost'" @click="filter = 'active'">Đang làm</Button>
                        <Button size="sm" :variant="filter === 'completed' ? 'default' : 'ghost'" @click="filter = 'completed'">Đã xong</Button>
                    </div>
                </div>

                <!-- Todo List -->
                <div v-if="filteredTodos.length === 0" class="py-10 text-center text-sm text-slate-500 dark:text-zinc-400">
                    Chưa có công việc nào trong mục này.
                </div>
                <ul v-else class="mt-3 divide-y divide-slate-100 dark:divide-zinc-800">
                    <li v-for="todo in filteredTodos" :key="todo.id" class="flex items-center justify-between gap-3 py-3">
                        <div class="flex min-w-0 flex-1 items-center gap-3">
                            <button type="button" :disabled="busyId === todo.id" class="text-slate-400 transition hover:text-emerald-600 disabled:opacity-50" @click="toggleTodo(todo)">
                                <CheckCircle2 v-if="todo.completed" class="size-5 text-emerald-600" />
                                <Circle v-else class="size-5" />
                            </button>

                            <div v-if="editingId === todo.id" class="flex flex-1 items-center gap-2">
                                <Input v-model="editTitle" class="h-8 flex-1 text-sm" @keyup.enter="saveEdit(todo)" @keyup.esc="cancelEdit" />
                                <Button size="sm" variant="default" :disabled="busyId === todo.id" @click="saveEdit(todo)"><Check class="size-3.5" /></Button>
                                <Button size="sm" variant="ghost" @click="cancelEdit"><X class="size-3.5" /></Button>
                            </div>
                            <span v-else class="truncate text-sm font-medium" :class="todo.completed ? 'text-slate-400 line-through dark:text-zinc-500' : 'text-slate-800 dark:text-zinc-100'" @dblclick="startEdit(todo)">
                                {{ todo.title }}
                            </span>
                        </div>
                        <div v-if="editingId !== todo.id" class="flex shrink-0 items-center gap-1">
                            <Button size="icon" variant="ghost" class="size-8 text-slate-500 hover:text-slate-900" :disabled="busyId === todo.id" @click="startEdit(todo)"><Pencil class="size-4" /></Button>
                            <Button size="icon" variant="ghost" class="size-8 text-red-500 hover:bg-red-50 hover:text-red-600" :disabled="busyId === todo.id" @click="deleteTodo(todo)"><Trash2 class="size-4" /></Button>
                        </div>
                    </li>
                </ul>
            </section>
        </main>
    </div>
</template>
