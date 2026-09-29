<script setup>
import LoadingOverlay from '@/Components/LoadingOverlay.vue';

defineProps({
    wide: {
        type: Boolean,
        default: false,
    },
    subtitle: {
        type: String,
        default: 'Sign in to your account',
    },
});
</script>

<template>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex flex-col">
        <LoadingOverlay />
        <!-- Header -->
        <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="max-w-7xl mx-auto flex items-center h-14 px-4 sm:px-6">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6L21 9 12 3zm0 2.18l6 3.27-6 3.27-6-3.27 6-3.27zM5 12.82l5 2.73v5.18l-5-2.73v-5.18zm14 0v5.18l-5 2.73v-5.18l5-2.73z"/>
                        </svg>
                    </div>
                    <span class="text-lg font-semibold text-gray-800 dark:text-gray-100">E-REQUEST</span>
                </div>
            </div>
        </header>

        <!-- Main -->
        <main class="flex-1 flex items-center justify-center px-4 py-12">
            <div :class="['w-full', wide ? 'max-w-4xl' : 'max-w-md']">
                <div v-if="!wide" class="text-center mb-8">
                    <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100 tracking-wide uppercase">
                        E-REQUEST
                    </h1>
                    <p v-if="subtitle" class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ subtitle }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 px-6 py-8">
                    <slot />
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-3 px-4 flex justify-center items-center">
            <span class="text-xs text-gray-400">
                Powered by <span class="font-semibold text-gray-600 dark:text-gray-300">EduTAMS</span>
            </span>
        </footer>
    </div>
</template>
