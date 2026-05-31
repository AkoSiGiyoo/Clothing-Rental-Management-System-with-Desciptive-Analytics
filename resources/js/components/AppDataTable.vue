<script setup>
import { computed, ref, watch } from "vue";

const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
    columns: {
        type: Array,
        required: true,
    },
    initialSortKey: {
        type: String,
        default: "",
    },
    initialSortDirection: {
        type: String,
        default: "asc",
    },
    pageSize: {
        type: Number,
        default: 10,
    },
});

const currentPage = ref(1);
const perPage = ref(props.pageSize);
const sortKey = ref(props.initialSortKey || props.columns.find((column) => column.sortable)?.key || "");
const sortDirection = ref(props.initialSortDirection === "desc" ? "desc" : "asc");

const totalPages = computed(() => Math.max(1, Math.ceil(props.items.length / perPage.value)));

const sortedItems = computed(() => {
    if (!sortKey.value) {
        return [...props.items];
    }

    const sorted = [...props.items].sort((left, right) => {
        const leftValue = left?.[sortKey.value];
        const rightValue = right?.[sortKey.value];

        if (leftValue == null && rightValue == null) return 0;
        if (leftValue == null) return 1;
        if (rightValue == null) return -1;

        if (typeof leftValue === "number" && typeof rightValue === "number") {
            return leftValue - rightValue;
        }

        if (!Number.isNaN(Number(leftValue)) && !Number.isNaN(Number(rightValue))) {
            return Number(leftValue) - Number(rightValue);
        }

        return String(leftValue).localeCompare(String(rightValue), undefined, {
            sensitivity: "base",
            numeric: true,
        });
    });

    return sortDirection.value === "desc" ? sorted.reverse() : sorted;
});

const paginatedItems = computed(() => {
    const start = (currentPage.value - 1) * perPage.value;
    return sortedItems.value.slice(start, start + perPage.value);
});

watch(
    () => props.items,
    () => {
        if (currentPage.value > totalPages.value) {
            currentPage.value = totalPages.value;
        }
    },
    { deep: true },
);

watch(perPage, () => {
    currentPage.value = 1;
});

function toggleSort(columnKey) {
    if (!columnKey) return;

    if (sortKey.value === columnKey) {
        sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc";
        return;
    }

    sortKey.value = columnKey;
    sortDirection.value = "asc";
}

function goToPage(page) {
    if (page < 1 || page > totalPages.value) return;
    currentPage.value = page;
}

function pageButtonClass(page) {
    return page === currentPage.value
        ? "border-slate-900 bg-slate-900 text-white"
        : "border-slate-200 text-slate-700 hover:border-slate-300";
}
</script>

<template>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr class="text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                    <th
                        v-for="column in columns"
                        :key="column.key"
                        class="px-5 py-4"
                        :class="[column.align === 'right' ? 'text-right' : '']"
                    >
                        <button
                            v-if="column.sortable"
                            type="button"
                            class="inline-flex items-center gap-2"
                            @click="toggleSort(column.key)"
                        >
                            <span>{{ column.label }}</span>
                            <span v-if="sortKey === column.key">{{ sortDirection === "asc" ? "↑" : "↓" }}</span>
                        </button>
                        <span v-else>{{ column.label }}</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <tr v-for="item in paginatedItems" :key="item.id">
                    <td
                        v-for="column in columns"
                        :key="`${item.id}-${column.key}`"
                        class="px-5 py-4"
                        :class="[column.align === 'right' ? 'text-right' : '']"
                    >
                        <slot :name="`cell-${column.key}`" :item="item">
                            <span class="text-sm text-slate-700">{{ item[column.key] }}</span>
                        </slot>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="flex flex-col gap-3 border-t border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="text-sm text-slate-500">
            Showing
            {{ items.length === 0 ? 0 : (currentPage - 1) * perPage + 1 }}
            -
            {{ Math.min(currentPage * perPage, items.length) }}
            of
            {{ items.length }}
        </div>

        <div class="flex items-center gap-2">
            <label class="text-sm text-slate-500">Rows</label>
            <select
                v-model.number="perPage"
                class="rounded-lg border border-slate-200 px-2 py-1 text-sm outline-none"
            >
                <option :value="5">5</option>
                <option :value="10">10</option>
                <option :value="25">25</option>
            </select>
        </div>

        <div class="flex items-center gap-2">
            <button
                type="button"
                class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-700 disabled:cursor-not-allowed disabled:opacity-50"
                :disabled="currentPage <= 1"
                @click="goToPage(currentPage - 1)"
            >
                Prev
            </button>
            <button
                v-for="page in totalPages"
                :key="page"
                type="button"
                class="rounded-lg border px-3 py-1.5 text-sm"
                :class="pageButtonClass(page)"
                @click="goToPage(page)"
            >
                {{ page }}
            </button>
            <button
                type="button"
                class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-700 disabled:cursor-not-allowed disabled:opacity-50"
                :disabled="currentPage >= totalPages"
                @click="goToPage(currentPage + 1)"
            >
                Next
            </button>
        </div>
    </div>
</template>
