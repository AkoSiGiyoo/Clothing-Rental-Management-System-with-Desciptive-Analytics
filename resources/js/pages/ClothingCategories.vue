<script setup>
import { computed, ref } from "vue";
import { Head, router, useForm } from "@inertiajs/vue3";
import AdminNavbar from "../components/AdminNavbar.vue";
import AdminSidebar from "../components/AdminSidebar.vue";
import AppDataTable from "../components/AppDataTable.vue";
import { buildAdminNavigation } from "../data/adminNavigation";

const props = defineProps({
    categories: {
        type: Array,
        required: true,
    },
    filters: {
        type: Object,
        required: true,
    },
    stats: {
        type: Object,
        required: true,
    },
});

const navigation = buildAdminNavigation("categories");
const sidebarOpen = ref(false);
const modalOpen = ref(false);
const editingId = ref(null);
const search = ref(props.filters.search ?? "");

const form = useForm({
    name: "",
});

const title = computed(() => (editingId.value ? "Edit category" : "Add category"));
const subtitle = computed(() =>
    editingId.value
        ? "Update the category name used by clothing records."
        : "Create categories such as Barong or Filipiniana for your clothing catalog.",
);

const categoryTableColumns = [
    { key: "name", label: "Category", sortable: true },
    { key: "clothing_items_count", label: "Clothing items", sortable: true },
    { key: "created_at", label: "Created", sortable: true },
    { key: "actions", label: "Actions", sortable: false, align: "right" },
];

function resetForm() {
    form.defaults({ name: "" });
    form.reset();
    form.clearErrors();
    editingId.value = null;
}

function openCreate() {
    resetForm();
    modalOpen.value = true;
}

function openEdit(category) {
    editingId.value = category.id;
    form.defaults({
        name: category.name,
    });
    form.reset();
    form.clearErrors();
    modalOpen.value = true;
}

function closeModal() {
    modalOpen.value = false;
    resetForm();
}

function submit() {
    const options = {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    };

    if (editingId.value) {
        form.put(`/clothing-categories/${editingId.value}`, options);
        return;
    }

    form.post("/clothing-categories", options);
}

function removeCategory(category) {
    if (category.clothing_items_count > 0) {
        return;
    }

    if (!window.confirm(`Delete category "${category.name}"?`)) {
        return;
    }

    router.delete(`/clothing-categories/${category.id}`, {
        preserveScroll: true,
    });
}

function applySearch() {
    router.get(
        "/clothing-categories",
        { search: search.value },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
}
</script>

<template>
    <Head title="Clothing Categories" />

    <main class="h-screen overflow-hidden bg-slate-100 text-slate-900">
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 z-30 bg-slate-950/50 lg:hidden"
            @click="sidebarOpen = false"
        />

        <div
            v-if="modalOpen"
            class="fixed inset-0 z-40 flex items-center justify-center bg-slate-950/40 px-4 py-6 backdrop-blur-[2px] sm:px-6"
            @click.self="closeModal"
        >
            <div class="w-full max-w-xl rounded-[32px] bg-white shadow-2xl">
                <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5">
                    <div>
                        <p class="text-lg font-semibold tracking-[-0.02em] text-slate-900">
                            {{ title }}
                        </p>
                        <p class="mt-1 text-sm text-slate-500">
                            {{ subtitle }}
                        </p>
                    </div>
                    <button
                        type="button"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 text-slate-500"
                        @click="closeModal"
                    >
                        <i class="fa-solid fa-xmark" aria-hidden="true" />
                    </button>
                </div>

                <form class="space-y-5 px-6 py-6" @submit.prevent="submit">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Category name</span>
                        <input
                            v-model="form.name"
                            type="text"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-[var(--color-brand-500)]"
                            placeholder="Example: Barong, Filipiniana"
                        />
                        <p v-if="form.errors.name" class="mt-2 text-sm text-[var(--color-alert-500)]">
                            {{ form.errors.name }}
                        </p>
                    </label>

                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-5">
                        <button
                            type="button"
                            class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600"
                            @click="closeModal"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="rounded-full bg-slate-900 px-5 py-2 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:opacity-60"
                            :disabled="form.processing"
                        >
                            {{ editingId ? "Save changes" : "Create category" }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="grid h-full lg:grid-cols-[260px_minmax(0,1fr)]">
            <AdminSidebar
                :navigation="navigation"
                :open="sidebarOpen"
                @close="sidebarOpen = false"
            />

            <div class="min-w-0 overflow-y-auto">
                <AdminNavbar
                    title="Clothing Categories"
                    subtitle="Maintain the category list used by your clothing catalog."
                    @toggle-sidebar="sidebarOpen = true"
                />

                <div class="px-4 py-6 sm:px-6 lg:px-8">
                    <section class="grid gap-4 md:grid-cols-3">
                        <article class="glass-panel p-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Total categories</p>
                            <p class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-slate-900">{{ stats.total }}</p>
                        </article>
                        <article class="glass-panel p-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Used categories</p>
                            <p class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-[var(--color-brand-700)]">{{ stats.used }}</p>
                        </article>
                        <article class="glass-panel p-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Unused categories</p>
                            <p class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-[var(--color-alert-500)]">{{ stats.unused }}</p>
                        </article>
                    </section>

                    <section class="glass-panel mt-6 p-5 sm:p-6">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div class="min-w-0">
                                <span class="eyebrow">Category Library</span>
                                <p class="section-title mt-3">Manage clothing categories</p>
                                <p class="mt-1 text-sm text-slate-500">
                                    Add categories like Barong, Filipiniana, Gown, or Uniform and keep naming consistent.
                                </p>
                            </div>

                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                                <form class="flex items-center gap-2" @submit.prevent="applySearch">
                                    <input
                                        v-model="search"
                                        type="text"
                                        placeholder="Search category name..."
                                        class="w-full min-w-0 rounded-full border border-slate-200 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-[var(--color-brand-500)] sm:w-72"
                                    />
                                    <button
                                        type="submit"
                                        class="rounded-full border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700"
                                    >
                                        Search
                                    </button>
                                </form>

                                <button
                                    type="button"
                                    class="rounded-full bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white"
                                    @click="openCreate"
                                >
                                    Add category
                                </button>
                            </div>
                        </div>

                        <div v-if="categories.length" class="mt-6 overflow-hidden rounded-[24px] border border-slate-200 bg-white">
                            <AppDataTable
                                :items="categories"
                                :columns="categoryTableColumns"
                                initial-sort-key="name"
                            >
                                <template #cell-name="{ item }">
                                    <p class="font-semibold text-slate-900 text-left">{{ item.name }}</p>
                                </template>
                                <template #cell-clothing_items_count="{ item }">
                                    <span class="text-sm text-slate-600">{{ item.clothing_items_count }}</span>
                                </template>
                                <template #cell-created_at="{ item }">
                                    <span class="text-sm text-slate-500">{{ item.created_at }}</span>
                                </template>
                                <template #cell-actions="{ item }">
                                    <div class="flex justify-end gap-2">
                                        <button
                                            type="button"
                                            class="rounded-full border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700"
                                            @click="openEdit(item)"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            type="button"
                                            class="rounded-full border px-3 py-2 text-sm font-semibold"
                                            :class="item.clothing_items_count > 0
                                                ? 'cursor-not-allowed border-slate-200 text-slate-400'
                                                : 'border-[var(--color-alert-500)]/30 text-[var(--color-alert-500)]'"
                                            :disabled="item.clothing_items_count > 0"
                                            @click="removeCategory(item)"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </template>
                            </AppDataTable>
                        </div>

                        <div
                            v-else
                            class="soft-grid mt-6 rounded-[24px] border border-dashed border-slate-300 bg-white/65 px-6 py-12 text-center"
                        >
                            <p class="text-lg font-semibold text-slate-900">No categories found</p>
                            <p class="mt-2 text-sm text-slate-500">
                                Create your first clothing category such as Barong or Filipiniana.
                            </p>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </main>
</template>
