<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
    subtitle: {
        type: String,
        required: true,
    },
    points: {
        type: Array,
        required: true,
    },
    bars: {
        type: Array,
        required: true,
    },
});

const toPath = (points) =>
    points
        .map((point, index) => `${index === 0 ? 'M' : 'L'} ${point.x} ${point.y}`)
        .join(' ');
</script>

<template>
    <section class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-[0_16px_36px_rgba(15,23,42,0.08)]">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <p class="text-lg font-semibold tracking-[-0.02em] text-slate-900">{{ title }}</p>
                <p class="mt-1 text-sm text-slate-500">{{ subtitle }}</p>
            </div>

            <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-slate-600">
                <i class="fa-solid fa-chart-column" aria-hidden="true" />
                <span>Charts &amp; Analytics</span>
            </span>
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-[1.25fr_0.9fr]">
            <div class="rounded-[24px] border border-slate-200 bg-slate-50 p-5">
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">
                            <i class="fa-solid fa-coins mr-2" aria-hidden="true" />
                            Revenue Trend
                        </p>
                        <p class="mt-2 text-3xl font-semibold tracking-[-0.04em] text-slate-900">PHP 124,500</p>
                    </div>

                    <p class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                        +18.4%
                    </p>
                </div>

                <svg viewBox="0 0 320 180" class="mt-6 h-52 w-full">
                    <defs>
                        <linearGradient id="trend-fill" x1="0%" x2="0%" y1="0%" y2="100%">
                            <stop offset="0%" stop-color="rgba(86,124,73,0.36)" />
                            <stop offset="100%" stop-color="rgba(86,124,73,0.04)" />
                        </linearGradient>
                    </defs>

                    <path
                        d="M 20 140 L 20 78 L 72 92 L 124 58 L 176 84 L 228 40 L 280 56 L 280 140 Z"
                        fill="url(#trend-fill)"
                    />
                    <path
                        :d="toPath(points)"
                        fill="none"
                        stroke="var(--color-brand-500)"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="6"
                    />

                    <g>
                        <circle
                            v-for="(point, index) in points"
                            :key="index"
                            :cx="point.x"
                            :cy="point.y"
                            r="5"
                            fill="white"
                            stroke="var(--color-brand-500)"
                            stroke-width="4"
                        />
                    </g>
                </svg>
            </div>

            <div class="rounded-[24px] bg-slate-900 p-5 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-white/60">
                            <i class="fa-solid fa-chart-simple mr-2" aria-hidden="true" />
                            Rental Activity
                        </p>
                        <p class="mt-2 text-2xl font-semibold tracking-[-0.04em]">Weekly volume</p>
                    </div>
                    <span class="rounded-full border border-white/15 px-3 py-1 text-xs font-semibold text-white/70">
                        7 Days
                    </span>
                </div>

                <div class="mt-8 flex h-52 items-end gap-3">
                    <div
                        v-for="(bar, index) in bars"
                        :key="bar.label"
                        class="flex flex-1 flex-col items-center gap-3"
                    >
                        <div class="flex w-full items-end justify-center rounded-t-[18px] bg-white/6 px-2">
                            <div
                                class="w-full rounded-t-[18px] bg-gradient-to-b from-[var(--color-accent-500)] to-[var(--color-brand-500)]"
                                :class="index % 2 === 0 ? 'animate-pulse-line' : ''"
                                :style="{ height: `${bar.value}%` }"
                            />
                        </div>
                        <span class="text-xs font-medium text-white/60">{{ bar.label }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
