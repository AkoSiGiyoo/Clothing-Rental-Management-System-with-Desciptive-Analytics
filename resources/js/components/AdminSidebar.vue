<script setup>
defineProps({
    navigation: {
        type: Array,
        required: true,
    },
    open: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close']);

const navigation = [
    { label: 'Dashboard', icon: 'fa-gauge-high', active: true },
    { label: 'Manage Clothing', icon: 'fa-shirt', active: false },
    { label: 'Clothing Categories', icon: 'fa-receipt', active: false },
    { label: 'Inventory', icon: 'fa-shirt', active: false },
    { label: 'Rental', icon: 'fa-file-lines', active: false },
    { label: 'Customer', icon: 'fa-rotate-left', active: false },
    { label: 'Reports & Analytics', icon: 'fa-rotate-left', active: false },
    { label: 'Payment', icon: 'fa-rotate-left', active: false },
];
</script>

<template>
    <aside
        class="fixed inset-y-0 left-0 z-40 w-[260px] border-r border-slate-800 bg-slate-950 px-4 py-6 text-white transition-transform duration-300 lg:static lg:h-screen lg:translate-x-0 lg:overflow-hidden lg:border-r lg:px-5"
        :class="open ? 'translate-x-0' : '-translate-x-full'"
    >
        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[var(--color-brand-500)] text-lg font-bold">
                <i class="fa-solid fa-shirt" aria-hidden="true" />
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-white/50">Admin Panel</p>
                <p class="mt-1 text-base font-semibold">Clothing Rental</p>
            </div>
            <button
                type="button"
                class="ml-auto inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 text-white/70 lg:hidden"
                @click="emit('close')"
            >
                <i class="fa-solid fa-xmark" aria-hidden="true" />
            </button>
        </div>

        <nav class="mt-8 space-y-2">
            <a
                v-for="item in navigation"
                :key="item.label"
                href="#"
                class="flex items-center justify-between rounded-2xl px-3 py-3 text-sm font-medium transition"
                :class="item.active
                    ? 'bg-white text-slate-950'
                    : 'text-white/70 hover:bg-white/8 hover:text-white'"
                @click="emit('close')"
            >
                <span class="flex items-center gap-3">
                    <i :class="['fa-solid', item.icon]" aria-hidden="true" />
                    <span>{{ item.label }}</span>
                </span>
                <span
                    v-if="item.active"
                    class="h-2 w-2 rounded-full bg-[var(--color-brand-500)]"
                />
            </a>
        </nav>
    </aside>
</template>
