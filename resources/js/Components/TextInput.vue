<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm disabled:bg-gray-100 disabled:text-gray-600 disabled:cursor-not-allowed disabled:opacity-90 disabled:focus:border-gray-300 disabled:focus:ring-0"
        v-model="model"
        ref="input"
    />
</template>
