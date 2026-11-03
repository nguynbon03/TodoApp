<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import {
    Check,
    CheckCircle2,
    Circle,
    ListTodo,
    Pencil,
    Plus,
    RefreshCw,
    Trash2,
    X,
} from '@lucide/vue';
import { computed, onMounted, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useTodosApi } from '@/composables/useTodosApi';
import type { TodoFilter, TodoItem } from '@/types';

type Props = {
    todos?: TodoItem[];
};

const props = withDefaults(defineProps<Props>(), {
    todos: () => [],
});

const {
    filter,
    loading,
    submitting,
    busyId,
    errorMessage,
    lastApiCall,
    totalCount,
    completedCount,
    activeCount,
    completionPercent,
    filteredTodos,
    fetchTodos,
    createTodo,
    toggleTodo,
    updateTodoTitle,
    deleteTodo,
} = useTodosApi(props.todos);

const newTitle = ref('');
const editingId = ref<number | null>(null);
const editTitle = ref('');

const filterTabs: { value: TodoFilter; label: string }[] = [
    { value: 'all', label: 'Tất cả' },
    { value: 'active', label: 'Đang làm' },
    { value: 'completed', label: 'Đã xong' },
];

const statCards = computed(() => [
    {
        label: 'Tổng công việc',
        value: totalCount.value,
        cardClass: 'bg-slate-50 dark:bg-zinc-800/60',
        labelClass: 'text-slate-500 dark:text-zinc-400',
        valueClass: 'text-slate-900 dark:text-zinc-100',
    },
    {
        label: 'Đang thực hiện',
        value: activeCount.value,
        cardClass: 'bg-amber-50/70 dark:bg-amber-950/30',
        labelClass: 'text-amber-700 dark:text-amber-300',
        valueClass: 'text-amber-700 dark:text-amber-400',
    },
    {
        label: 'Đã hoàn thành',
        value: completedCount.value,
        cardClass: 'bg-emerald-50/70 dark:bg-emerald-950/30',
        labelClass: 'text-emerald-700 dark:text-emerald-300',
        valueClass: 'text-emerald-700 dark:text-emerald-400',
    },
]);

async function onCreateTodo(): Promise<void> {
    if (submitting.value) {
        return;
    }

    const isCreated = await createTodo(newTitle.value);

    if (isCreated) {
        newTitle.value = '';
    }
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
    if (editTitle.value.trim() === todo.title) {
        cancelEdit();
        return;
    }

    const isUpdated = await updateTodoTitle(todo, editTitle.value);

    if (isUpdated) {
        cancelEdit();
    }
}

onMounted(() => {
    if (props.todos.length === 0) {
        void fetchTodos();
    }
});
</script>

<template>
    <Head title="TodoVue — Quản lý Công việc (Vue 3 + Axios)" />

    <div
        class="min-h-screen bg-slate-50 text-slate-900 dark:bg-zinc-950 dark:text-zinc-100"
    >
        <!-- Custom Top Navbar -->
        <header
            class="sticky top-0 z-20 border-b border-slate-200/80 bg-white/90 backdrop-blur dark:border-zinc-800 dark:bg-zinc-900/90"
        >
            <div
                class="mx-auto flex max-w-5xl flex-wrap items-center justify-between gap-3 px-4 py-3.5"
            >
                <div class="flex flex-wrap items-center gap-2.5">
                    <div
                        class="flex size-9 items-center justify-center rounded-lg bg-emerald-600 text-white shadow-xs"
                    >
                        <ListTodo class="size-5" />
                    </div>
                    <span class="text-lg font-bold tracking-tight"
                        >TodoVue</span
                    >
                    <span
                        class="rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600 dark:bg-zinc-800 dark:text-zinc-300"
                    >
                        Vue 3 + Axios + routes/api.php
                    </span>
                    <span
                        class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 font-mono text-xs text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/60 dark:text-emerald-300"
                    >
                        {{ lastApiCall }}
                    </span>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    <Button
                        variant="outline"
                        size="sm"
                        :disabled="loading"
                        @click="fetchTodos"
                    >
                        <RefreshCw
                            class="mr-1.5 size-3.5 motion-reduce:animate-none"
                            :class="{ 'animate-spin': loading }"
                        />
                        Làm mới API (GET /api/todos)
                    </Button>
                </div>
            </div>
        </header>

        <!-- Main Centered Container -->
        <main class="mx-auto max-w-3xl space-y-6 px-4 py-8">
            <!-- Stats & Progress Card -->
            <section
                class="rounded-xl border border-slate-200/80 bg-white p-5 shadow-xs dark:border-zinc-800 dark:bg-zinc-900"
            >
                <div class="grid grid-cols-3 gap-3 text-center">
                    <div
                        v-for="stat in statCards"
                        :key="stat.label"
                        class="rounded-lg p-3"
                        :class="stat.cardClass"
                    >
                        <p class="text-xs" :class="stat.labelClass">
                            {{ stat.label }}
                        </p>
                        <p
                            class="mt-1 text-2xl font-bold"
                            :class="stat.valueClass"
                        >
                            {{ stat.value }}
                        </p>
                    </div>
                </div>
                <div class="mt-4 space-y-1.5">
                    <div
                        class="flex justify-between text-xs font-medium text-slate-600 dark:text-zinc-400"
                    >
                        <span>Tiến độ hoàn thành</span>
                        <span
                            class="font-semibold text-emerald-600 dark:text-emerald-400"
                            >{{ completionPercent }}%</span
                        >
                    </div>
                    <div
                        class="h-2 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-zinc-800"
                    >
                        <div
                            class="h-full origin-left rounded-full bg-emerald-600 transition-transform duration-200 motion-reduce:transition-none"
                            :style="{
                                transform: `scaleX(${completionPercent / 100})`,
                            }"
                        />
                    </div>
                </div>
            </section>

            <!-- Create Todo Form & List Card -->
            <section
                class="rounded-xl border border-slate-200/80 bg-white p-5 shadow-xs dark:border-zinc-800 dark:bg-zinc-900"
            >
                <form
                    class="flex flex-col gap-2.5 sm:flex-row"
                    @submit.prevent="onCreateTodo"
                >
                    <Input
                        v-model="newTitle"
                        placeholder="Nhập công việc cần làm..."
                        class="flex-1"
                        :disabled="submitting"
                    />
                    <Button
                        type="submit"
                        :disabled="submitting || !newTitle.trim()"
                        class="bg-emerald-600 text-white hover:bg-emerald-700"
                    >
                        <Plus class="mr-1.5 size-4" />
                        Thêm công việc
                    </Button>
                </form>

                <p
                    v-if="errorMessage"
                    class="mt-2.5 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-medium text-red-600 dark:border-red-900/50 dark:bg-red-950/40 dark:text-red-400"
                >
                    {{ errorMessage }}
                </p>

                <!-- Filter Tabs -->
                <div
                    class="mt-5 flex items-center justify-between border-b border-slate-100 pb-3 dark:border-zinc-800"
                >
                    <div class="flex gap-1.5">
                        <Button
                            v-for="tab in filterTabs"
                            :key="tab.value"
                            size="sm"
                            :variant="
                                filter === tab.value ? 'default' : 'ghost'
                            "
                            @click="filter = tab.value"
                        >
                            {{ tab.label }}
                        </Button>
                    </div>
                </div>

                <!-- Todo List -->
                <div
                    v-if="filteredTodos.length === 0"
                    class="py-10 text-center text-sm text-slate-500 dark:text-zinc-400"
                >
                    Chưa có công việc nào trong mục này.
                </div>
                <ul
                    v-else
                    class="mt-3 divide-y divide-slate-100 dark:divide-zinc-800"
                >
                    <li
                        v-for="todo in filteredTodos"
                        :key="todo.id"
                        class="flex items-center justify-between gap-3 py-3"
                    >
                        <div class="flex min-w-0 flex-1 items-center gap-3">
                            <button
                                type="button"
                                :disabled="busyId === todo.id"
                                class="rounded-full text-slate-400 transition-colors hover:text-emerald-600 focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:outline-none disabled:opacity-50"
                                @click="toggleTodo(todo)"
                            >
                                <CheckCircle2
                                    v-if="todo.completed"
                                    class="size-5 text-emerald-600"
                                />
                                <Circle v-else class="size-5" />
                            </button>

                            <div
                                v-if="editingId === todo.id"
                                class="flex flex-1 items-center gap-2"
                            >
                                <Input
                                    v-model="editTitle"
                                    class="h-8 flex-1 text-sm"
                                    @keyup.enter="saveEdit(todo)"
                                    @keyup.esc="cancelEdit"
                                />
                                <Button
                                    size="sm"
                                    variant="default"
                                    :disabled="busyId === todo.id"
                                    @click="saveEdit(todo)"
                                >
                                    <Check class="size-3.5" />
                                </Button>
                                <Button
                                    size="sm"
                                    variant="ghost"
                                    @click="cancelEdit"
                                >
                                    <X class="size-3.5" />
                                </Button>
                            </div>
                            <div
                                v-else
                                class="flex min-w-0 flex-1 items-center justify-between gap-2"
                            >
                                <span
                                    class="truncate text-sm font-medium"
                                    :class="
                                        todo.completed
                                            ? 'text-slate-400 line-through dark:text-zinc-500'
                                            : 'text-slate-800 dark:text-zinc-100'
                                    "
                                    @dblclick="startEdit(todo)"
                                >
                                    {{ todo.title }}
                                </span>
                                <span
                                    v-if="todo.createdAt"
                                    class="shrink-0 text-xs text-slate-400 dark:text-zinc-500"
                                >
                                    {{ todo.createdAt }}
                                </span>
                            </div>
                        </div>
                        <div
                            v-if="editingId !== todo.id"
                            class="flex shrink-0 items-center gap-1"
                        >
                            <Button
                                size="icon"
                                variant="ghost"
                                class="size-8 text-slate-500 hover:text-slate-900"
                                :disabled="busyId === todo.id"
                                @click="startEdit(todo)"
                            >
                                <Pencil class="size-4" />
                            </Button>
                            <Button
                                size="icon"
                                variant="ghost"
                                class="size-8 text-red-500 hover:bg-red-50 hover:text-red-600"
                                :disabled="busyId === todo.id"
                                @click="deleteTodo(todo)"
                            >
                                <Trash2 class="size-4" />
                            </Button>
                        </div>
                    </li>
                </ul>
            </section>
        </main>
    </div>
</template>
