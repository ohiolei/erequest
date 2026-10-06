<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ImpersonationBanner from '@/Components/ImpersonationBanner.vue';
import LoadingOverlay from '@/Components/LoadingOverlay.vue';
import UserAccountMenu from '@/Components/UserAccountMenu.vue';
import { useTheme } from '@/Composables/useTheme';

const page = usePage();
const user = computed(() => page.props.auth.user);
const userIsActive = computed(() => (user.value?.status ?? 'active') === 'active');
const { colorMode, setColorMode } = useTheme();
</script>

<template>
    <div class="flex h-screen flex-col overflow-hidden bg-gray-100 dark:bg-gray-900">
        <LoadingOverlay />
        <div class="shrink-0">
            <ImpersonationBanner />
        </div>

        <header class="relative z-30 shrink-0 border-b border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex h-14 items-center justify-between px-4">
                <div class="flex items-center gap-6">
                    <Link :href="route('dashboard')" class="flex items-center gap-2">
                        <img src="/assets/images/logo1.png" alt="" class="h-8 w-8 shrink-0 object-contain" />
                        <span class="text-lg font-semibold text-gray-800 dark:text-gray-100">Tasfued E Request</span>
                    </Link>
                    <nav class="hidden items-center gap-4 sm:flex">
                        <Link
                            :href="route('dashboard')"
                            :class="[
                                'text-sm transition',
                                route().current('dashboard')
                                    ? 'font-semibold text-purple-700 dark:text-purple-300'
                                    : 'text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white',
                            ]"
                        >
                            Dashboard
                        </Link>
                        <Link
                            :href="route('chats.index')"
                            :class="[
                                'text-sm transition',
                                route().current('chats.*')
                                    ? 'font-semibold text-purple-700 dark:text-purple-300'
                                    : 'text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white',
                            ]"
                        >
                            My chats
                        </Link>
                    </nav>
                </div>

                <div class="relative z-40 flex items-center gap-3">
                    <Link
                        :href="page.props.chat_route"
                        class="relative inline-flex items-center rounded-md p-2 text-gray-700 transition hover:bg-gray-50 hover:text-gray-900 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white"
                        aria-label="My support chats"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        <span
                            v-if="page.props.chat_unread_count > 0"
                            class="absolute -right-1 -top-1 inline-flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-rose-600 px-1 text-[10px] font-semibold leading-none text-white"
                        >
                            {{ page.props.chat_unread_count > 99 ? '99+' : page.props.chat_unread_count }}
                        </span>
                    </Link>

                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-md px-2 py-1.5 text-sm text-gray-700 transition hover:bg-gray-50 hover:text-gray-900 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white"
                        @click="setColorMode(colorMode === 'dark' ? 'light' : 'dark')"
                        :aria-label="colorMode === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'"
                    >
                        <svg v-if="colorMode === 'dark'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364 6.364-1.414-1.414M7.05 7.05 5.636 5.636m12.728 0-1.414 1.414M7.05 16.95l-1.414 1.414M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                        </svg>
                    </button>

                    <UserAccountMenu :user-is-active="userIsActive">
                        <template #trigger>
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-md px-2 py-1.5 text-sm text-gray-700 transition hover:bg-gray-50 hover:text-gray-900 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white"
                            >
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center overflow-hidden rounded-full bg-gray-300 dark:bg-gray-600">
                                    <img
                                        v-if="user?.image_url"
                                        :src="user.image_url"
                                        alt=""
                                        loading="lazy"
                                        decoding="async"
                                        class="h-full w-full object-cover"
                                    />
                                    <svg v-else class="h-6 w-6 text-gray-500 dark:text-gray-300" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                    </svg>
                                </div>
                                <div class="hidden text-left sm:block">
                                    <div class="font-medium leading-tight text-gray-800 dark:text-gray-100">{{ user?.name }}</div>
                                    <div class="text-xs leading-tight text-gray-500 dark:text-gray-400">{{ user?.email }}</div>
                                </div>
                                <svg class="hidden h-4 w-4 text-gray-400 sm:block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </template>
                    </UserAccountMenu>
                </div>
            </div>
        </header>

        <main class="min-h-0 flex-1 overflow-y-auto px-3 sm:px-4">
            <slot />
        </main>

        <footer class="flex shrink-0 items-center justify-end border-t border-gray-200 bg-white px-4 py-2 dark:border-gray-700 dark:bg-gray-800">
            <span class="text-xs text-gray-400">Powered by <span class="font-semibold text-gray-600 dark:text-gray-300">EduTAMS</span></span>
        </footer>
    </div>
</template>
