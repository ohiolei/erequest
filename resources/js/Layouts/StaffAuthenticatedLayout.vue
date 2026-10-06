<script setup>
import { ref, computed, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ImpersonationBanner from '@/Components/ImpersonationBanner.vue';
import LoadingOverlay from '@/Components/LoadingOverlay.vue';
import UserAccountMenu from '@/Components/UserAccountMenu.vue';
import { useTheme } from '@/Composables/useTheme';

const page = usePage();
const user = computed(() => page.props.auth.user);
const userIsActive = computed(() => (user.value?.status ?? 'active') === 'active');
const { colorMode, setColorMode } = useTheme();

const sidebarOpen = ref(true);
const expandedMenus = ref([]);
const navItems = computed(() => page.props.sidebar_menu ?? []);

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};

const isActive = (routeName) => routeName && route().current(routeName);
const isParentActive = (item) => item.children?.some((child) => isActive(child.route)) ?? false;

const toggleMenu = (key) => {
    expandedMenus.value = expandedMenus.value.includes(key)
        ? expandedMenus.value.filter((expandedKey) => expandedKey !== key)
        : [...expandedMenus.value, key];
};

const isMenuExpanded = (key) => expandedMenus.value.includes(key);

watch(
    () => page.url,
    () => {
        const activeParents = navItems.value
            .filter(isParentActive)
            .map((item) => item.key);

        expandedMenus.value = [...new Set([...expandedMenus.value, ...activeParents])];
    },
    { immediate: true },
);
</script>

<template>
    <div class="h-screen bg-gray-100 dark:bg-gray-900 flex flex-col overflow-hidden">
        <LoadingOverlay />
        <div class="shrink-0">
            <ImpersonationBanner />
        </div>
        <!-- Top Header -->
        <header class="relative z-30 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm shrink-0">
            <div class="flex items-center justify-between h-14 px-4">
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2">
                        <img src="/assets/images/logo1.png" alt="" class="h-8 w-8 shrink-0 object-contain" />
                        <span class="text-lg font-semibold text-gray-800 dark:text-gray-100">Tasfued E Request</span>
                    </div>
                    <button
                        type="button"
                        @click="toggleSidebar"
                        class="p-1.5 rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200 transition"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>

                <div class="relative z-40 flex items-center gap-3">
                    <Link
                        v-if="page.props.chat_route"
                        :href="page.props.chat_route"
                        class="relative inline-flex items-center gap-2 rounded-md px-2 py-1.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        <span
                            v-if="page.props.chat_unread_count > 0"
                            class="absolute -top-1 -right-1 inline-flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-rose-600 px-1 text-[10px] font-semibold leading-none text-white"
                        >
                            {{ page.props.chat_unread_count > 99 ? '99+' : page.props.chat_unread_count }}
                        </span>
                    </Link>

                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-md px-2 py-1.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition"
                        @click="setColorMode(colorMode === 'dark' ? 'light' : 'dark')"
                        :aria-label="colorMode === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'"
                    >
                        <svg v-if="colorMode === 'dark'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364 6.364-1.414-1.414M7.05 7.05 5.636 5.636m12.728 0-1.414 1.414M7.05 16.95l-1.414 1.414M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                        </svg>
                    </button>

                    <UserAccountMenu :user-is-active="userIsActive">
                        <template #trigger>
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-md px-2 py-1.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition"
                            >
                                <div class="w-8 h-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center overflow-hidden shrink-0">
                                    <img
                                        v-if="user?.image_url"
                                        :src="user.image_url"
                                        alt=""
                                        loading="lazy"
                                        decoding="async"
                                        class="w-full h-full object-cover"
                                    />
                                    <svg v-else class="w-6 h-6 text-gray-500 dark:text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                    </svg>
                                </div>
                                <div class="hidden sm:block text-left">
                                    <div class="font-medium text-gray-800 dark:text-gray-100 leading-tight">{{ user?.name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 leading-tight">{{ user?.email }}</div>
                                </div>
                                <svg class="hidden sm:block w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </template>
                    </UserAccountMenu>
                </div>
            </div>
        </header>

        <div class="flex flex-1 min-h-0 overflow-hidden">
            <!-- Sidebar -->
            <aside
                :class="[
                    'bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-all duration-300 overflow-y-auto shrink-0 h-full',
                    sidebarOpen ? 'w-64' : 'w-0 overflow-hidden',
                ]"
            >
                <div class="p-4">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Module Navigation</p>
                    <nav class="space-y-0.5">
                        <template v-for="item in navItems" :key="item.key">
                            <div v-if="item.children?.length">
                                <button
                                    type="button"
                                    :aria-expanded="isMenuExpanded(item.key)"
                                    :aria-controls="`menu-${item.key}`"
                                    :class="[
                                        'w-full flex items-center justify-between px-3 py-2.5 text-sm rounded-md transition',
                                        isParentActive(item)
                                            ? 'bg-purple-50 text-purple-700 font-medium dark:bg-purple-900/40 dark:text-purple-200'
                                            : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700',
                                    ]"
                                    @click="toggleMenu(item.key)"
                                >
                                    <span class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                        </svg>
                                        {{ item.label }}
                                    </span>
                                    <svg :class="['h-4 w-4 transition-transform', isMenuExpanded(item.key) ? 'rotate-90' : '']" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7" />
                                    </svg>
                                </button>
                                <div v-show="isMenuExpanded(item.key)" :id="`menu-${item.key}`" class="ml-6 border-l border-gray-200 pl-2 dark:border-gray-600">
                                    <Link
                                        v-for="child in item.children"
                                        :key="child.key"
                                        :href="route(child.route)"
                                        :class="[
                                            'block rounded-md px-3 py-2 text-sm transition',
                                            isActive(child.route)
                                                ? 'bg-purple-50 font-medium text-purple-700 dark:bg-purple-900/40 dark:text-purple-200'
                                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-100',
                                        ]"
                                    >
                                        {{ child.label }}
                                    </Link>
                                </div>
                            </div>
                            <Link
                                v-else-if="item.route"
                                :href="route(item.route)"
                                :class="[
                                    'flex items-center gap-2.5 px-3 py-2.5 text-sm rounded-md transition',
                                    isActive(item.route)
                                        ? 'bg-purple-50 text-purple-700 font-medium dark:bg-purple-900/40 dark:text-purple-200'
                                        : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700',
                                ]"
                            >
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                                {{ item.label }}
                            </Link>
                        </template>
                    </nav>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="flex flex-1 flex-col min-h-0 overflow-hidden bg-gray-100 dark:bg-gray-900">
                <main class="flex-1 overflow-y-auto px-3 sm:px-4">
                    <slot />
                </main>

                <!-- Footer -->
                <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-2 px-4 flex justify-end items-center shrink-0">
                    <span class="text-xs text-gray-400">Powered by <span class="font-semibold text-gray-600 dark:text-gray-300">EduTAMS</span></span>
                </footer>
            </div>
        </div>
    </div>
</template>
