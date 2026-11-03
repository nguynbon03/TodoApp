export type TodoItem = {
    id: number;
    title: string;
    completed: boolean;
    createdAt: string;
};

export type TodoFilter = 'all' | 'active' | 'completed';
