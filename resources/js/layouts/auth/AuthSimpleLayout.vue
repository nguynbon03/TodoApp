<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { CheckSquare, ListTodo } from '@lucide/vue';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="flex min-h-svh flex-col items-center justify-center gap-6 bg-slate-50/70 p-6 md:p-10 dark:bg-zinc-950"
    >
        <div class="w-full max-w-md">
            <div
                class="flex flex-col gap-6 rounded-2xl border border-slate-200/80 bg-card p-6 shadow-sm sm:p-8 dark:border-zinc-800"
            >
                <div class="flex flex-col items-center gap-3">
                    <Link
                        :href="home()"
                        class="group flex items-center gap-2.5 font-semibold tracking-tight text-slate-900 transition-opacity hover:opacity-90 dark:text-zinc-100"
                    >
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-600/10 text-emerald-600 ring-1 ring-emerald-600/20 dark:bg-emerald-500/15 dark:text-emerald-400"
                        >
                            <CheckSquare class="size-5" />
                        </div>
                        <span class="text-lg font-bold tracking-tight">
                            TodoWorkspace
                        </span>
                    </Link>
                    <div class="space-y-1.5 text-center">
                        <h1 class="text-xl font-semibold tracking-tight text-foreground">
                            {{ title }}
                        </h1>
                        <p class="text-center text-sm text-muted-foreground">
                            {{ description }}
                        </p>
                    </div>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
