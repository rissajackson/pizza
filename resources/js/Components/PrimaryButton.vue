<template>
    <button
        :disabled="disabled"
        class="inline-flex items-center rounded-md border border-transparent px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900"
        :class="[
            disabled ? 'border-gray-900 border-2 hover:bg-gray-500 text-gray-300 cursor-not-allowed' : '',
            bgColorClass
        ]"
    >
        <slot />
    </button>
</template>

<script setup>
const props = defineProps({
    disabled: {
        type: Boolean,
        default: false,
    },
    bgColorClass: {
        type: String,
        default: 'bg-blue-500 hover:bg-blue-700', // Default active state color.
    }
});
</script>
