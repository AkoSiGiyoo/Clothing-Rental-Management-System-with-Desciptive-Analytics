<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
    value: {
        type: String,
        required: true,
    },
    note: {
        type: String,
        required: true,
    },
    accent: {
        type: String,
        default: 'brand',
    },
    delta: {
        type: String,
        default: '',
    },
    status: {
        type: String,
        default: 'Updated',
    },
    icon: {
        type: String,
        default: 'fa-chart-line',
    },
});

const accentClasses = {
    brand: 'bg-[var(--color-brand-500)]/12 text-[var(--color-brand-700)]',
    amber: 'bg-[rgba(209,139,47,0.16)] text-[#9c5b18]',
    alert: 'bg-[rgba(178,74,45,0.14)] text-[var(--color-alert-500)]',
    ink: 'bg-[rgba(18,32,24,0.08)] text-[var(--color-ink-900)]',
};

const progressWidths = {
    brand: '74%',
    amber: '68%',
    alert: '34%',
    ink: '82%',
};
</script>

<template>
    <article class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-[0_12px_30px_rgba(15,23,42,0.06)]">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                    <i :class="['fa-solid', icon, 'text-slate-400']" aria-hidden="true" />
                    <span>
                    {{ title }}
                    </span>
                </p>
                <p class="mt-4 text-3xl font-semibold tracking-[-0.04em] text-slate-900">
                    {{ value }}
                </p>
            </div>

            <span
                class="rounded-full px-3 py-1 text-xs font-semibold"
                :class="accentClasses[accent] ?? accentClasses.brand"
            >
                {{ status }}
            </span>
        </div>

        <div class="mt-4 flex items-center justify-between gap-4">
            <p class="text-sm text-slate-500">
                {{ note }}
            </p>

            <p
                v-if="delta"
                class="whitespace-nowrap text-sm font-semibold"
                :class="accent === 'alert' ? 'text-[var(--color-alert-500)]' : 'text-[var(--color-brand-700)]'"
            >
                {{ delta }}
            </p>
        </div>

        <div class="mt-4 h-2 rounded-full bg-slate-100">
            <div
                class="h-2 rounded-full"
                :class="accent === 'alert'
                    ? 'bg-[var(--color-alert-500)]'
                    : accent === 'amber'
                        ? 'bg-[var(--color-accent-500)]'
                        : accent === 'ink'
                            ? 'bg-[var(--color-ink-900)]'
                            : 'bg-[var(--color-brand-500)]'"
                :style="{ width: progressWidths[accent] ?? progressWidths.brand }"
            />
        </div>

        <div class="mt-4 flex items-end justify-between gap-4 text-xs text-slate-400">
            <span>System metric</span>
            <span>{{ title }}</span>
        </div>
    </article>
</template>
