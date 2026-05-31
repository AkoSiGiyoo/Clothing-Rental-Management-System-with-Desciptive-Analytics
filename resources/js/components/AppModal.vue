<script setup>
const props = defineProps({
    open: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: "",
    },
    subtitle: {
        type: String,
        default: "",
    },
    maxWidthClass: {
        type: String,
        default: "max-w-xl",
    },
    panelClass: {
        type: String,
        default: "",
    },
    bodyClass: {
        type: String,
        default: "px-6 py-6",
    },
    showHeader: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(["close"]);

function close() {
    // Keep close behavior consistent for overlay click and close button.
    emit("close");
}
</script>

<template>
    <div
        v-if="open"
        class="fixed inset-0 z-40 flex items-center justify-center bg-slate-950/40 px-4 py-6 backdrop-blur-[2px] sm:px-6"
        @click.self="close"
    >
        <div :class="`w-full rounded-[32px] bg-white shadow-2xl ${props.maxWidthClass} ${props.panelClass}`">
            <div
                v-if="showHeader"
                class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5"
            >
                <div>
                    <p v-if="title" class="text-lg font-semibold tracking-[-0.02em] text-slate-900">
                        {{ title }}
                    </p>
                    <p v-if="subtitle" class="mt-1 text-sm text-slate-500">
                        {{ subtitle }}
                    </p>
                </div>
                <button
                    type="button"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 text-slate-500"
                    @click="close"
                >
                    <i class="fa-solid fa-xmark" aria-hidden="true" />
                </button>
            </div>
            <div v-else class="flex justify-end px-6 pt-5">
                <button
                    type="button"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 text-slate-500"
                    @click="close"
                >
                    <i class="fa-solid fa-xmark" aria-hidden="true" />
                </button>
            </div>

            <div :class="props.bodyClass">
                <slot />
            </div>
        </div>
    </div>
</template>
