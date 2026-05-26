<script setup>
defineEmits(['toggle-sidebar']);

</script>

<template>
    <header class="border-b border-slate-200 bg-white px-2 py-2 sm:px-5 lg:px-6">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <button
                    type="button"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-slate-50 text-slate-700 lg:hidden"
                    @click="$emit('toggle-sidebar')"
                >
                    <i class="fa-solid fa-bars text-sm" aria-hidden="true" />
                </button>
            </div>

            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-end">

                <details class="group relative self-start sm:self-auto">
                    <summary class="flex cursor-pointer list-none items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2 shadow-sm transition hover:border-slate-300">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-900 text-sm font-semibold text-white">
                            <i class="fa-solid fa-user" aria-hidden="true" />
                        </div>
                        <div class="text-left">
                            <p class="text-sm font-semibold text-slate-900">Admin User</p>
                            <p class="text-xs text-slate-500">Administrator</p>
                        </div>
                        <i class="fa-solid fa-chevron-down text-sm text-slate-400 transition group-open:rotate-180" aria-hidden="true" />
                    </summary>

                    <div class="absolute left-0 z-20 mt-3 w-60 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_20px_40px_rgba(15,23,42,0.12)] sm:left-auto sm:right-0">
                        <div class="border-b border-slate-200 px-4 py-4">
                            <p class="text-sm font-semibold text-slate-900">Admin User</p>
                            <p class="mt-1 text-xs text-slate-500">admin@clothrental.local</p>
                        </div>

                        <div class="p-2">
                            <button type="button" class="flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                                <span class="flex items-center gap-3">
                                    <i class="fa-regular fa-user text-slate-400" aria-hidden="true" />
                                    <span>Profile</span>
                                </span>
                                <i class="fa-solid fa-angle-right text-slate-400" aria-hidden="true" />
                            </button>
                            <button type="button" class="flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                                <span class="flex items-center gap-3">
                                    <i class="fa-solid fa-gear text-slate-400" aria-hidden="true" />
                                    <span>Account Settings</span>
                                </span>
                                <i class="fa-solid fa-angle-right text-slate-400" aria-hidden="true" />
                            </button>
                            <button type="button" class="flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-sm font-medium text-[var(--color-alert-500)] transition hover:bg-[rgba(178,74,45,0.08)]">
                                <span class="flex items-center gap-3">
                                    <i class="fa-solid fa-right-from-bracket" aria-hidden="true" />
                                    <span>Log Out</span>
                                </span>
                                <i class="fa-solid fa-angle-right" aria-hidden="true" />
                            </button>
                        </div>
                    </div>
                </details>
            </div>
        </div>
    </header>
</template>
