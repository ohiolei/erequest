<script setup>
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { useTheme } from '@/Composables/useTheme';

defineProps({
    show: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close']);

const { themes, themeId, setTheme, colorModes, colorMode, setColorMode } = useTheme();

const close = () => emit('close');
</script>

<template>
    <Modal :show="show" max-width="md" @close="close">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Settings</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Choose your appearance and theme color.
            </p>

            <div class="mt-6">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Appearance</p>
                <div class="grid grid-cols-2 gap-2">
                    <button
                        v-for="mode in colorModes"
                        :key="mode.id"
                        type="button"
                        class="inline-flex items-center justify-center gap-1.5 rounded-md border px-3 py-2 text-sm font-medium transition"
                        :class="
                            colorMode === mode.id
                                ? 'border-purple-600 bg-purple-50 text-purple-700 dark:border-purple-400 dark:bg-purple-900/40 dark:text-purple-200'
                                : 'border-gray-200 text-gray-600 hover:bg-gray-50 dark:border-gray-500 dark:text-gray-300 dark:hover:bg-gray-600'
                        "
                        :aria-pressed="colorMode === mode.id"
                        @click="setColorMode(mode.id)"
                    >
                        <svg
                            v-if="mode.id === 'light'"
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364 6.364-1.414-1.414M7.05 7.05 5.636 5.636m12.728 0-1.414 1.414M7.05 16.95l-1.414 1.414M16 12a4 4 0 11-8 0 4 4 0 018 0z"
                            />
                        </svg>
                        <svg
                            v-else
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"
                            />
                        </svg>
                        {{ mode.label }}
                    </button>
                </div>
            </div>

            <div class="mt-6">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Theme color</p>
                <div class="flex flex-wrap gap-2">
                    <button
                        v-for="theme in themes"
                        :key="theme.id"
                        type="button"
                        :title="theme.label"
                        :aria-label="`Use ${theme.label} theme`"
                        :aria-pressed="themeId === theme.id"
                        class="h-8 w-8 rounded-full border-2 transition focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-gray-400"
                        :class="
                            themeId === theme.id
                                ? 'border-gray-800 dark:border-white scale-110'
                                : 'border-transparent hover:scale-105'
                        "
                        :style="{ backgroundColor: theme.swatch }"
                        @click="setTheme(theme.id)"
                    />
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <SecondaryButton type="button" @click="close">Close</SecondaryButton>
            </div>
        </div>
    </Modal>
</template>
