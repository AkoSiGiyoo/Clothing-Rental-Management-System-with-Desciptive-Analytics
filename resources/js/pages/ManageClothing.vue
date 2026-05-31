<script setup>
import { computed, ref } from "vue";
import { Head, router, useForm } from "@inertiajs/vue3";
import AdminNavbar from "../components/AdminNavbar.vue";
import AdminSidebar from "../components/AdminSidebar.vue";
import AppDataTable from "../components/AppDataTable.vue";
import AppModal from "../components/AppModal.vue";
import { buildAdminNavigation } from "../data/adminNavigation";

const props = defineProps({
    categories: {
        type: Array,
        required: true,
    },
    clothingItems: {
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

const navigation = buildAdminNavigation("clothing");
const sidebarOpen = ref(false);
const formOpen = ref(false);
const editingId = ref(null);
const search = ref(props.filters.search ?? "");
const imagePreview = ref(null);
const imageInput = ref(null);
const brokenImageIds = ref(new Set());

const emptyState = {
    clothing_category_id: "",
    name: "",
    rental_price: "",
    color: "",
    size: "",
    image: null,
    remove_image: false,
    brand: "",
    status: "available",
};

const form = useForm({ ...emptyState });

const hasCategories = computed(() => props.categories.length > 0);
const formTitle = computed(() =>
    editingId.value ? "Edit clothing item" : "Add clothing item",
);
const formDescription = computed(() =>
    editingId.value
        ? "Update the selected clothing record and image."
        : "Create a new clothing record and upload an image.",
);
const imagePreviewLabel = computed(() =>
    editingId.value ? "Current image" : "Image preview",
);

const statusClasses = {
    available: "bg-[var(--color-brand-100)]/70 text-[var(--color-brand-700)]",
    reserved: "bg-[rgba(209,139,47,0.18)] text-[#9c5b18]",
    rented: "bg-slate-200 text-slate-700",
    maintenance: "bg-[rgba(178,74,45,0.16)] text-[var(--color-alert-500)]",
};

const clothingTableColumns = [
    { key: "name", label: "Clothing", sortable: true },
    { key: "image_url", label: "Photo", sortable: false },
    { key: "category_name", label: "Category", sortable: true },
    { key: "rental_price", label: "Rental Price", sortable: true },
    { key: "status", label: "Availability", sortable: true },
    { key: "actions", label: "Actions", sortable: false, align: "right" },
];

function resetForm() {
    // Normalize form state for both create flow and post-submit cleanup.
    form.defaults({ ...emptyState });
    form.reset();
    form.clearErrors();
    editingId.value = null;
    imagePreview.value = null;

    if (imageInput.value) {
        imageInput.value.value = "";
    }
}

function openCreate() {
    resetForm();
    formOpen.value = true;
}

function openEdit(item) {
    // Pre-fill modal inputs from the selected row and keep image optional.
    editingId.value = item.id;
    form.defaults({
        clothing_category_id: item.category_id,
        name: item.name,
        rental_price: item.rental_price,
        color: item.color ?? "",
        size: item.size ?? "",
        image: null,
        remove_image: false,
        brand: item.brand ?? "",
        status: item.status,
    });
    form.reset();
    form.clearErrors();
    imagePreview.value = item.image_url ?? null;

    if (imageInput.value) {
        imageInput.value.value = "";
    }

    formOpen.value = true;
}

function closeForm() {
    formOpen.value = false;
    resetForm();
}

function submit() {
    // forceFormData is required because this form can include a file upload.
    const options = {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => closeForm(),
    };

    if (editingId.value) {
        form.transform((data) => ({
            ...data,
            _method: "put",
        })).post(`/clothing/${editingId.value}`, options);
        return;
    }

    form.transform((data) => data).post("/clothing", options);
}

function updateImage(event) {
    // Keep preview in sync with selection and preserve existing image on empty input.
    const [file] = event.target.files ?? [];

    form.image = file ?? null;
    form.remove_image = false;

    if (!file) {
        imagePreview.value = editingId.value
            ? (props.clothingItems.find((item) => item.id === editingId.value)
                  ?.image_url ?? null)
            : null;
        return;
    }

    imagePreview.value = URL.createObjectURL(file);
}

function removeImageSelection() {
    // Flag remove_image so backend can delete existing stored image on update.
    form.image = null;
    form.remove_image = true;
    imagePreview.value = null;

    if (imageInput.value) {
        imageInput.value.value = "";
    }
}

function removeItem(item) {
    if (!window.confirm(`Delete "${item.name}"?`)) {
        return;
    }

    router.delete(`/clothing/${item.id}`, {
        preserveScroll: true,
    });
}

function markImageBroken(itemId) {
    brokenImageIds.value.add(itemId);
}

function applySearch() {
    router.get(
        "/clothing",
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
    <Head title="Manage Clothing" />

    <main class="h-screen overflow-hidden bg-slate-100 text-slate-900">
        <!-- Mobile-only overlay used when the admin sidebar is open. -->
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 z-30 bg-slate-950/50 lg:hidden"
            @click="sidebarOpen = false"
        />

        <!-- Reusable modal for creating and editing clothing records. -->
        <AppModal
            :open="formOpen"
            :title="formTitle"
            :subtitle="formDescription"
            max-width-class="max-w-3xl"
            panel-class="max-h-[90vh] overflow-y-auto"
            body-class="px-6 py-6"
            @close="closeForm"
        >
            <form class="space-y-5" @submit.prevent="submit">
                <!-- Main clothing form fields. -->
                <div class="grid gap-5 sm:grid-cols-2">
                    <label class="block sm:col-span-2">
                        <span class="text-sm font-medium text-slate-700"
                            >Clothing name</span
                        >
                        <input
                            v-model="form.name"
                            type="text"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-[var(--color-brand-500)]"
                        />
                        <p
                            v-if="form.errors.name"
                            class="mt-2 text-sm text-[var(--color-alert-500)]"
                        >
                            {{ form.errors.name }}
                        </p>
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-slate-700"
                            >Category</span
                        >
                        <select
                            v-model="form.clothing_category_id"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-[var(--color-brand-500)]"
                            :disabled="!hasCategories"
                        >
                            <option value="">Select category</option>
                            <option
                                v-for="category in categories"
                                :key="category.id"
                                :value="category.id"
                            >
                                {{ category.name }}
                            </option>
                        </select>
                        <p
                            v-if="form.errors.clothing_category_id"
                            class="mt-2 text-sm text-[var(--color-alert-500)]"
                        >
                            {{ form.errors.clothing_category_id }}
                        </p>
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-slate-700"
                            >Status</span
                        >
                        <select
                            v-model="form.status"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-[var(--color-brand-500)]"
                        >
                            <option value="available">Available</option>
                            <option value="reserved">Reserved</option>
                            <option value="rented">Rented</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                        <p
                            v-if="form.errors.status"
                            class="mt-2 text-sm text-[var(--color-alert-500)]"
                        >
                            {{ form.errors.status }}
                        </p>
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-slate-700"
                            >Rental price</span
                        >
                        <input
                            v-model="form.rental_price"
                            type="number"
                            min="0"
                            step="0.01"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-[var(--color-brand-500)]"
                        />
                        <p
                            v-if="form.errors.rental_price"
                            class="mt-2 text-sm text-[var(--color-alert-500)]"
                        >
                            {{ form.errors.rental_price }}
                        </p>
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-slate-700"
                            >Brand</span
                        >
                        <input
                            v-model="form.brand"
                            type="text"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-[var(--color-brand-500)]"
                        />
                        <p
                            v-if="form.errors.brand"
                            class="mt-2 text-sm text-[var(--color-alert-500)]"
                        >
                            {{ form.errors.brand }}
                        </p>
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-slate-700"
                            >Color</span
                        >
                        <input
                            v-model="form.color"
                            type="text"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-[var(--color-brand-500)]"
                        />
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-slate-700"
                            >Size</span
                        >
                        <input
                            v-model="form.size"
                            type="text"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-[var(--color-brand-500)]"
                        />
                    </label>

                    <label class="block sm:col-span-2">
                        <span class="text-sm font-medium text-slate-700"
                            >Clothing image</span
                        >
                        <input
                            ref="imageInput"
                            type="file"
                            accept="image/png,image/jpeg,image/jpg,image/webp"
                            class="mt-2 block w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition file:mr-4 file:rounded-full file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white focus:border-[var(--color-brand-500)]"
                            @change="updateImage"
                        />
                        <p class="mt-2 text-xs text-slate-500">
                            Upload JPG, PNG, or WEBP up to 2 MB.
                        </p>
                        <p
                            v-if="form.errors.image"
                            class="mt-2 text-sm text-[var(--color-alert-500)]"
                        >
                            {{ form.errors.image }}
                        </p>
                    </label>

                    <!-- Image preview panel reflects current upload or stored image. -->
                    <div
                        class="sm:col-span-2 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-4"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-slate-700">
                                    {{ imagePreviewLabel }}
                                </p>
                                <p class="mt-1 text-sm text-slate-500">
                                    Add a thumbnail for the clothing item
                                    listing.
                                </p>
                            </div>
                            <button
                                v-if="imagePreview"
                                type="button"
                                class="rounded-full border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600"
                                @click="removeImageSelection"
                            >
                                Remove image
                            </button>
                        </div>

                        <div
                            class="mt-4 flex h-48 items-center justify-center overflow-hidden rounded-2xl bg-white"
                        >
                            <img
                                v-if="imagePreview"
                                :src="imagePreview"
                                alt="Clothing preview"
                                class="h-full w-full object-cover"
                            />
                            <div
                                v-else
                                class="text-center text-sm text-slate-400"
                            >
                                No image selected
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sticky action row keeps save/cancel visible in the tall modal. -->
                <div
                    class="sticky bottom-0 flex items-center justify-end gap-3 border-t border-slate-200 bg-white pt-5"
                >
                    <button
                        type="button"
                        class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600"
                        @click="closeForm"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="rounded-full bg-slate-900 px-5 py-2 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="form.processing"
                    >
                        {{
                            editingId ? "Save changes" : "Create clothing item"
                        }}
                    </button>
                </div>
            </form>
        </AppModal>

        <!-- Main admin shell: sidebar navigation on the left, page content on the right. -->
        <div class="grid h-full lg:grid-cols-[260px_minmax(0,1fr)]">
            <AdminSidebar
                :navigation="navigation"
                :open="sidebarOpen"
                @close="sidebarOpen = false"
            />

            <div class="min-w-0 overflow-y-auto">
                <AdminNavbar
                    title="Manage Clothing"
                    subtitle="Create, update, and remove clothing records from one admin view."
                    @toggle-sidebar="sidebarOpen = true"
                />

                <div class="px-4 py-6 sm:px-6 lg:px-8">
                    <!-- Summary cards for the current clothing inventory status. -->
                    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <article class="glass-panel p-5">
                            <p
                                class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500"
                            >
                                Total items
                            </p>
                            <p
                                class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-slate-900"
                            >
                                {{ stats.total }}
                            </p>
                        </article>
                        <article class="glass-panel p-5">
                            <p
                                class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500"
                            >
                                Available
                            </p>
                            <p
                                class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-[var(--color-brand-700)]"
                            >
                                {{ stats.available }}
                            </p>
                        </article>
                        <article class="glass-panel p-5">
                            <p
                                class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500"
                            >
                                Rented
                            </p>
                            <p
                                class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-slate-900"
                            >
                                {{ stats.rented }}
                            </p>
                        </article>
                        <article class="glass-panel p-5">
                            <p
                                class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500"
                            >
                                Low stock
                            </p>
                            <p
                                class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-[var(--color-alert-500)]"
                            >
                                {{ stats.low_stock }}
                            </p>
                        </article>
                    </section>

                    <!-- Main content area with filters, actions, and the clothing list. -->
                    <section class="glass-panel mt-6 p-5 sm:p-6">
                        <div
                            class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"
                        >
                            <div class="min-w-0">
                                <span class="eyebrow">List of clothing</span>
                            </div>

                            <div
                                class="flex flex-col gap-3 sm:flex-row sm:items-center"
                            >
                                <form
                                    class="flex items-center gap-2"
                                    @submit.prevent="applySearch"
                                >
                                    <input
                                        v-model="search"
                                        type="text"
                                        placeholder="Search name, brand, color..."
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
                                    Add clothing
                                </button>
                            </div>
                        </div>

                        <!-- Warning state when categories are missing and new items cannot be assigned properly. -->
                        <div
                            v-if="!hasCategories"
                            class="mt-5 rounded-2xl border border-[var(--color-accent-500)]/25 bg-[rgba(209,139,47,0.08)] px-4 py-3 text-sm text-slate-700"
                        >
                            No categories exist yet. Click `Add clothing` and
                            create a category from the modal first.
                        </div>

                        <!-- Data table shown when at least one clothing item exists. -->
                        <div
                            v-if="clothingItems.length"
                            class="mt-6 overflow-hidden rounded-[24px] border border-slate-200 bg-white"
                        >
                            <AppDataTable
                                :items="clothingItems"
                                :columns="clothingTableColumns"
                                initial-sort-key="name"
                            >
                                <template #cell-name="{ item }">
                                    <div class="text-left">
                                        <p class="font-semibold text-slate-900">
                                            {{ item.name }}
                                        </p>
                                        <p class="mt-1 text-sm text-slate-500">
                                            {{ item.brand || "No brand" }}
                                            <span
                                                v-if="item.color || item.size"
                                            >
                                                -
                                                {{ item.color || "No color" }} /
                                                {{ item.size || "No size" }}
                                            </span>
                                        </p>
                                    </div>
                                </template>

                                <template #cell-image_url="{ item }">
                                    <div
                                        class="h-16 w-16 overflow-hidden rounded-2xl bg-slate-100"
                                    >
                                        <img
                                            v-if="
                                                item.image_url &&
                                                !brokenImageIds.has(item.id)
                                            "
                                            :src="item.image_url"
                                            :alt="item.name"
                                            class="h-full w-full object-cover"
                                            @error="markImageBroken(item.id)"
                                        />
                                        <div
                                            v-else
                                            class="flex h-full w-full items-center justify-center text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-400"
                                        >
                                            No image
                                        </div>
                                    </div>
                                </template>

                                <template #cell-category_name="{ item }">
                                    <span class="text-sm text-slate-600">{{
                                        item.category_name
                                    }}</span>
                                </template>

                                <template #cell-rental_price="{ item }">
                                    <span
                                        class="text-sm font-medium text-slate-900"
                                        >PHP {{ item.rental_price }}</span
                                    >
                                </template>

                                <template #cell-status="{ item }">
                                    <span
                                        class="inline-flex rounded-full px-3 py-1 text-xs font-semibold capitalize"
                                        :class="statusClasses[item.status]"
                                    >
                                        {{ item.status }}
                                    </span>
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
                                            class="rounded-full border border-[var(--color-alert-500)]/30 px-3 py-2 text-sm font-semibold text-[var(--color-alert-500)]"
                                            @click="removeItem(item)"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </template>
                            </AppDataTable>
                        </div>

                        <!-- Empty state shown when the list has no items or search results. -->
                        <div
                            v-else
                            class="soft-grid mt-6 rounded-[24px] border border-dashed border-slate-300 bg-white/65 px-6 py-12 text-center"
                        >
                            <p class="text-lg font-semibold text-slate-900">
                                No clothing items found
                            </p>
                            <p class="mt-2 text-sm text-slate-500">
                                Create your first clothing record or adjust the
                                search filter.
                            </p>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </main>
</template>
