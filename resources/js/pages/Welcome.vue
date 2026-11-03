<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, CheckSquare, ShieldCheck, Zap } from '@lucide/vue';
import { dashboard, login, register } from '@/routes';
</script>

<template>
    <Head title="TodoWorkspace - Quản lý công việc" />

    <div
        class="flex min-h-svh flex-col items-center justify-center bg-slate-50/70 p-6 text-slate-900 lg:p-10 dark:bg-zinc-950 dark:text-zinc-100"
    >
        <main
            class="w-full max-w-2xl rounded-2xl border border-slate-200/80 bg-white p-8 shadow-sm sm:p-10 dark:border-zinc-800 dark:bg-zinc-900"
        >
            <div class="flex flex-col items-center text-center">
                <div
                    class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-600/10 text-emerald-600 ring-1 ring-emerald-600/20 dark:bg-emerald-500/15 dark:text-emerald-400"
                >
                    <CheckSquare class="size-6" />
                </div>

                <span
                    class="mb-2 inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700 ring-1 ring-indigo-600/15 dark:bg-indigo-950/60 dark:text-indigo-300"
                >
                    TodoWorkspace • Vue 3 + Laravel
                </span>

                <h1
                    class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl dark:text-zinc-50"
                >
                    Không gian quản lý công việc tối giản &amp; tập trung
                </h1>

                <p
                    class="mt-3 max-w-lg text-sm leading-relaxed text-slate-600 sm:text-base dark:text-zinc-400"
                >
                    Tổ chức danh sách việc cần làm hàng ngày nhanh chóng, rõ ràng và đồng bộ tức thì cho từng tài khoản cá nhân.
                </p>

                <div class="mt-7 flex flex-wrap items-center justify-center gap-3">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="$page.props.currentTeam ? dashboard($page.props.currentTeam.slug) : dashboard()"
                        class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white shadow-xs transition-all hover:bg-emerald-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600"
                    >
                        <span>Vào Bảng Công Việc (Dashboard)</span>
                        <ArrowRight class="size-4" />
                    </Link>

                    <template v-else>
                        <Link
                            :href="login()"
                            class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white shadow-xs transition-all hover:bg-emerald-500"
                        >
                            Đăng nhập (Log in)
                        </Link>
                        <Link
                            :href="register()"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-6 py-2.5 text-sm font-semibold text-slate-800 shadow-2xs transition-all hover:bg-slate-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 dark:hover:bg-zinc-700"
                        >
                            Đăng ký tài khoản mới (Register)
                        </Link>
                    </template>
                </div>

                <div
                    class="mt-8 grid w-full grid-cols-1 gap-3 border-t border-slate-100 pt-6 sm:grid-cols-3 dark:border-zinc-800"
                >
                    <div
                        class="flex items-center justify-center gap-2 rounded-xl bg-slate-50 px-3.5 py-2.5 text-xs font-medium text-slate-700 dark:bg-zinc-800/60 dark:text-zinc-300"
                    >
                        <CheckSquare class="size-4 shrink-0 text-emerald-600 dark:text-emerald-400" />
                        <span>Quản lý công việc nhanh</span>
                    </div>
                    <div
                        class="flex items-center justify-center gap-2 rounded-xl bg-slate-50 px-3.5 py-2.5 text-xs font-medium text-slate-700 dark:bg-zinc-800/60 dark:text-zinc-300"
                    >
                        <ShieldCheck class="size-4 shrink-0 text-indigo-600 dark:text-indigo-400" />
                        <span>Bảo mật tài khoản riêng biệt</span>
                    </div>
                    <div
                        class="flex items-center justify-center gap-2 rounded-xl bg-slate-50 px-3.5 py-2.5 text-xs font-medium text-slate-700 dark:bg-zinc-800/60 dark:text-zinc-300"
                    >
                        <Zap class="size-4 shrink-0 text-amber-500" />
                        <span>Đồng bộ tức thì với Vue 3 + Laravel</span>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
