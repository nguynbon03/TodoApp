import axios, { isAxiosError } from 'axios';
import { computed, ref } from 'vue';
import type { TodoFilter, TodoItem } from '@/types';

export const apiClient = axios.create({
    baseURL: '/api',
    headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
    withCredentials: true,
    withXSRFToken: true,
});

function extractErrorMessage(err: unknown): string {
    if (isAxiosError(err)) {
        return err.response?.data?.message || 'Có lỗi xảy ra khi gọi API.';
    }
    return 'Có lỗi xảy ra khi gọi API.';
}

export function useTodosApi(initialTodos: TodoItem[] = []) {
    const todos = ref<TodoItem[]>([...initialTodos]);
    const filter = ref<TodoFilter>('all');
    const loading = ref(false);
    const submitting = ref(false);
    const busyId = ref<number | null>(null);
    const errorMessage = ref<string | null>(null);
    const lastApiCall = ref<string>('GET /api/todos (Khởi tạo)');

    const totalCount = computed(() => todos.value.length);
    const completedCount = computed(
        () => todos.value.filter((t) => t.completed).length,
    );
    const activeCount = computed(() => totalCount.value - completedCount.value);
    const completionPercent = computed(() =>
        totalCount.value === 0
            ? 0
            : Math.round((completedCount.value / totalCount.value) * 100),
    );
    const filteredTodos = computed(() => {
        if (filter.value === 'active')
            return todos.value.filter((t) => !t.completed);
        if (filter.value === 'completed')
            return todos.value.filter((t) => t.completed);
        return todos.value;
    });

    async function fetchTodos(): Promise<void> {
        loading.value = true;
        errorMessage.value = null;
        try {
            const res = await apiClient.get<{ data: TodoItem[] }>('/todos');
            todos.value = res.data.data;
            lastApiCall.value = 'GET /api/todos → 200 OK';
        } catch (err) {
            errorMessage.value = extractErrorMessage(err);
        } finally {
            loading.value = false;
        }
    }

    async function createTodo(title: string): Promise<boolean> {
        const trimmed = title.trim();
        if (!trimmed) {
            errorMessage.value = 'Vui lòng nhập nội dung công việc.';
            return false;
        }
        submitting.value = true;
        errorMessage.value = null;
        try {
            const res = await apiClient.post<{ data: TodoItem }>('/todos', {
                title: trimmed,
            });
            todos.value.unshift(res.data.data);
            lastApiCall.value = 'POST /api/todos → 201 Created';
            return true;
        } catch (err) {
            errorMessage.value = extractErrorMessage(err);
            return false;
        } finally {
            submitting.value = false;
        }
    }

    async function toggleTodo(todo: TodoItem): Promise<void> {
        busyId.value = todo.id;
        errorMessage.value = null;
        try {
            const res = await apiClient.patch<{ data: TodoItem }>(
                `/todos/${todo.id}`,
                {
                    completed: !todo.completed,
                },
            );
            const idx = todos.value.findIndex((t) => t.id === todo.id);
            if (idx !== -1) todos.value[idx] = res.data.data;
            lastApiCall.value = `PATCH /api/todos/${todo.id} → 200 OK`;
        } catch (err) {
            errorMessage.value = extractErrorMessage(err);
        } finally {
            busyId.value = null;
        }
    }

    async function updateTodoTitle(
        todo: TodoItem,
        title: string,
    ): Promise<boolean> {
        const trimmed = title.trim();
        if (!trimmed) {
            errorMessage.value = 'Tiêu đề công việc không được để trống.';
            return false;
        }
        busyId.value = todo.id;
        errorMessage.value = null;
        try {
            const res = await apiClient.patch<{ data: TodoItem }>(
                `/todos/${todo.id}`,
                {
                    title: trimmed,
                },
            );
            const idx = todos.value.findIndex((t) => t.id === todo.id);
            if (idx !== -1) todos.value[idx] = res.data.data;
            lastApiCall.value = `PATCH /api/todos/${todo.id} → 200 OK`;
            return true;
        } catch (err) {
            errorMessage.value = extractErrorMessage(err);
            return false;
        } finally {
            busyId.value = null;
        }
    }

    async function deleteTodo(todo: TodoItem): Promise<void> {
        busyId.value = todo.id;
        errorMessage.value = null;
        try {
            await apiClient.delete(`/todos/${todo.id}`);
            todos.value = todos.value.filter((t) => t.id !== todo.id);
            lastApiCall.value = `DELETE /api/todos/${todo.id} → 200 OK`;
        } catch (err) {
            errorMessage.value = extractErrorMessage(err);
        } finally {
            busyId.value = null;
        }
    }

    return {
        todos,
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
    };
}
