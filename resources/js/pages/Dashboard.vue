<script setup>
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";
import AdminNavbar from "../components/AdminNavbar.vue";
import AdminSidebar from "../components/AdminSidebar.vue";
import ChartCard from "../components/ChartCard.vue";
import StatCard from "../components/StatCard.vue";
import { buildAdminNavigation } from "../data/adminNavigation";

const sidebarOpen = ref(false);
const navigation = buildAdminNavigation("dashboard");

const stats = [
    {
        title: "Total Customers",
        value: "1,248",
        note: "Registered customer accounts",
        accent: "brand",
        delta: "+8.1%",
        status: "This month",
        icon: "fa-users",
    },
    {
        title: "Total Rentals",
        value: "3,682",
        note: "Processed rental transactions",
        accent: "amber",
        delta: "+12.4%",
        status: "Active",
        icon: "fa-receipt",
    },
    {
        title: "Total Revenue",
        value: "PHP 124,500",
        note: "Gross income from rentals",
        accent: "ink",
        delta: "+18.4%",
        status: "Collected",
        icon: "fa-sack-dollar",
    },
    {
        title: "Available Clothes",
        value: "864",
        note: "Ready for reservation or pickup",
        accent: "brand",
        delta: "74% stock",
        status: "In stock",
        icon: "fa-shirt",
    },
    {
        title: "Currently Rented Clothes",
        value: "219",
        note: "Items currently checked out",
        accent: "amber",
        delta: "26% used",
        status: "Ongoing",
        icon: "fa-box-open",
    },
    {
        title: "Late Returns",
        value: "17",
        note: "Accounts requiring follow-up",
        accent: "alert",
        delta: "+3 today",
        status: "Attention",
        icon: "fa-triangle-exclamation",
    },
];

const revenuePoints = [
    { x: 20, y: 78 },
    { x: 72, y: 92 },
    { x: 124, y: 58 },
    { x: 176, y: 84 },
    { x: 228, y: 40 },
    { x: 280, y: 56 },
];

const rentalBars = [
    { label: "Mon", value: 52 },
    { label: "Tue", value: 70 },
    { label: "Wed", value: 48 },
    { label: "Thu", value: 84 },
    { label: "Fri", value: 76 },
    { label: "Sat", value: 92 },
    { label: "Sun", value: 58 },
];

const alerts = [
    { label: "Collections due today", value: "12 pickups", tone: "brand" },
    { label: "Late return follow-ups", value: "17 accounts", tone: "alert" },
    { label: "Top rental category", value: "Formal wear", tone: "amber" },
    { label: "Most available stock", value: "Barong set", tone: "ink" },
];

const activity = [
    {
        customer: "Maria Santos",
        action: "Reserved gown package",
        meta: "5 minutes ago",
    },
    {
        customer: "Alden Cruz",
        action: "Returned barong set",
        meta: "18 minutes ago",
    },
    {
        customer: "Jasmine Lee",
        action: "Payment confirmed",
        meta: "41 minutes ago",
    },
    {
        customer: "Rico Mendoza",
        action: "Marked overdue return",
        meta: "1 hour ago",
    },
];

const toneClasses = {
    brand: "bg-[var(--color-brand-100)]/70 text-[var(--color-brand-700)]",
    amber: "bg-[rgba(209,139,47,0.18)] text-[#9c5b18]",
    alert: "bg-[rgba(178,74,45,0.16)] text-[var(--color-alert-500)]",
    ink: "bg-[rgba(18,32,24,0.08)] text-[var(--color-ink-900)]",
};
</script>

<template>
    <Head title="Dashboard" />

    <main class="h-screen overflow-hidden bg-slate-100 text-slate-900">
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 z-30 bg-slate-950/50 lg:hidden"
            @click="sidebarOpen = false"
        />

        <div class="grid h-full lg:grid-cols-[260px_minmax(0,1fr)]">
            <AdminSidebar
                :navigation="navigation"
                :open="sidebarOpen"
                @close="sidebarOpen = false"
            />

            <div class="min-w-0 overflow-y-auto">
                <AdminNavbar
                    title="Dashboard"
                    subtitle="Operational overview for clothing rentals, stock, and collections."
                    @toggle-sidebar="sidebarOpen = true"
                />

                <div class="px-4 py-6 sm:px-6 lg:px-8">
                    <section
                        class="mb-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-3"
                    >
                        <div
                            class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-[0_10px_24px_rgba(15,23,42,0.05)]"
                        >
                            <p
                                class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500"
                            >
                                Revenue
                            </p>
                            <p
                                class="mt-2 text-xl font-semibold text-slate-900"
                            >
                                PHP 124,500
                            </p>
                        </div>
                        <div
                            class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-[0_10px_24px_rgba(15,23,42,0.05)]"
                        >
                            <p
                                class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500"
                            >
                                Rentals
                            </p>
                            <p
                                class="mt-2 text-xl font-semibold text-slate-900"
                            >
                                219 active
                            </p>
                        </div>
                        <div
                            class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-[0_10px_24px_rgba(15,23,42,0.05)]"
                        >
                            <p
                                class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500"
                            >
                                Late Returns
                            </p>
                            <p
                                class="mt-2 text-xl font-semibold text-[var(--color-alert-500)]"
                            >
                                17 pending
                            </p>
                        </div>
                    </section>

                    <section class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                        <StatCard
                            v-for="stat in stats"
                            :key="stat.title"
                            :title="stat.title"
                            :value="stat.value"
                            :note="stat.note"
                            :accent="stat.accent"
                            :delta="stat.delta"
                            :status="stat.status"
                            :icon="stat.icon"
                        />
                    </section>

                    <section
                        class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1.45fr)_minmax(320px,360px)]"
                    >
                        <ChartCard
                            title="Charts & Analytics"
                            subtitle="Track revenue movement and weekly rental volume from the admin view."
                            :points="revenuePoints"
                            :bars="rentalBars"
                        />

                        <div
                            class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-[0_16px_36px_rgba(15,23,42,0.08)] sm:p-6"
                        >
                            <div
                                class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                            >
                                <div class="min-w-0">
                                    <p
                                        class="text-lg font-semibold tracking-[-0.02em] text-slate-900"
                                    >
                                        Operational Notes
                                    </p>
                                    <p class="mt-1 text-sm text-slate-500">
                                        Current admin reminders
                                    </p>
                                </div>
                                <span
                                    class="inline-flex items-center gap-2 self-start rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-slate-600"
                                    ><i
                                        class="fa-regular fa-calendar"
                                        aria-hidden="true"
                                    />
                                    <span>Today</span></span
                                >
                            </div>

                            <div class="mt-5 space-y-3">
                                <article
                                    v-for="item in alerts"
                                    :key="item.label"
                                    class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4"
                                >
                                    <div
                                        class="flex items-start justify-between gap-4"
                                    >
                                        <div>
                                            <p
                                                class="font-medium text-slate-900"
                                            >
                                                {{ item.label }}
                                            </p>
                                            <p
                                                class="mt-1 text-sm text-slate-500"
                                            >
                                                Updated from current activity
                                                feed
                                            </p>
                                        </div>
                                        <span
                                            class="rounded-full px-3 py-1 text-xs font-semibold"
                                            :class="toneClasses[item.tone]"
                                        >
                                            {{ item.value }}
                                        </span>
                                    </div>
                                </article>
                            </div>
                        </div>
                    </section>

                    <section
                        class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1.15fr)_minmax(0,0.85fr)]"
                    >
                        <div
                            class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-[0_16px_36px_rgba(15,23,42,0.08)] sm:p-6"
                        >
                            <div
                                class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                            >
                                <div class="min-w-0">
                                    <p
                                        class="text-lg font-semibold tracking-[-0.02em] text-slate-900"
                                    >
                                        Recent Activity
                                    </p>
                                    <p class="mt-1 text-sm text-slate-500">
                                        Latest actions recorded in the system
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-2 self-start rounded-full border border-slate-200 px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.16em] text-slate-600"
                                >
                                    <i
                                        class="fa-solid fa-clock-rotate-left"
                                        aria-hidden="true"
                                    />
                                    <span>View Log</span>
                                </button>
                            </div>

                            <div
                                class="mt-5 overflow-hidden rounded-2xl border border-slate-200"
                            >
                                <div
                                    v-for="entry in activity"
                                    :key="`${entry.customer}-${entry.meta}`"
                                    class="flex flex-col gap-2 border-b border-slate-200 bg-white px-4 py-4 last:border-b-0 sm:flex-row sm:items-center sm:justify-between sm:px-5"
                                >
                                    <div>
                                        <p class="font-medium text-slate-900">
                                            {{ entry.customer }}
                                        </p>
                                        <p class="mt-1 text-sm text-slate-500">
                                            {{ entry.action }}
                                        </p>
                                    </div>
                                    <span
                                        class="text-sm font-medium text-slate-400 sm:text-right"
                                        >{{ entry.meta }}</span
                                    >
                                </div>
                            </div>
                        </div>

                        <div
                            class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-[0_16px_36px_rgba(15,23,42,0.08)] sm:p-6"
                        >
                            <p
                                class="text-lg font-semibold tracking-[-0.02em] text-slate-900"
                            >
                                Admin Summary
                            </p>
                            <p class="mt-1 text-sm text-slate-500">
                                Core dashboard coverage for operations,
                                inventory, and collection monitoring.
                            </p>

                            <div class="mt-6 space-y-4">
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <p class="text-sm text-slate-500">
                                        Customer visibility
                                    </p>
                                    <p
                                        class="mt-2 text-2xl font-semibold tracking-[-0.03em] text-slate-900"
                                    >
                                        1,248 profiles monitored
                                    </p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <p class="text-sm text-slate-500">
                                        Inventory status
                                    </p>
                                    <p
                                        class="mt-2 text-2xl font-semibold tracking-[-0.03em] text-slate-900"
                                    >
                                        864 items available now
                                    </p>
                                </div>
                                <div
                                    class="rounded-2xl bg-slate-900 p-4 text-white"
                                >
                                    <p class="text-sm text-white/60">
                                        Attention required
                                    </p>
                                    <p
                                        class="mt-2 text-2xl font-semibold tracking-[-0.03em]"
                                    >
                                        17 late returns need action
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </main>
</template>
