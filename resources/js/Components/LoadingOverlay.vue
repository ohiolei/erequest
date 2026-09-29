<script setup>
import { useLoading } from '@/Composables/useLoading';

defineProps({
    show: {
        type: Boolean,
        default: undefined,
    },
    message: {
        type: String,
        default: undefined,
    },
    local: {
        type: Boolean,
        default: false,
    },
});

const { isLoading, label } = useLoading();
</script>

<template>
    <Teleport to="body" :disabled="local">
        <Transition
            enter-active-class="transition-opacity duration-150"
            enter-from-class="opacity-0"
            leave-active-class="transition-opacity duration-150"
            leave-to-class="opacity-0"
        >
            <div
                v-if="show ?? isLoading"
                :class="[
                    'flex items-center justify-center bg-white/70 backdrop-blur-[1px] z-[100]',
                    local ? 'absolute inset-0' : 'fixed inset-0',
                ]"
                role="status"
                aria-live="polite"
                aria-busy="true"
            >
                <div class="flex flex-col items-center gap-3 rounded-lg bg-white px-6 py-5 shadow-lg border border-gray-100">
                    <svg class="h-8 w-8 animate-spin text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                    </svg>
                    <p class="text-sm font-medium text-gray-700">{{ message ?? label }}</p>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
