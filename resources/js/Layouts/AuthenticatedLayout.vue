<script setup>
import { ref, computed, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ImpersonationBanner from '@/Components/ImpersonationBanner.vue';
import LoadingOverlay from '@/Components/LoadingOverlay.vue';
import UserAccountMenu from '@/Components/UserAccountMenu.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const canManageRoles = computed(() => page.props.auth.canManageRoles);
const userIsActive = computed(() => (user.value?.status ?? 'active') === 'active');

const sidebarOpen = ref(true);
const expandedMenus = ref([]);

const navItems = computed(() => page.props.sidebar_menu ?? []);

const collectActiveKeys = (items) => {
    const keys = [];

    items.forEach((item) => {
        if (!item.children?.length) {
            return;
        }

        const hasActiveChild = item.children.some((child) => {
            if (child.route && route().current(child.route)) {
                return true;
            }

            return (child.children ?? []).some((nested) => nested.route && route().current(nested.route));
        });

        if (hasActiveChild && item.key) {
            keys.push(item.key);
        }
    });

    return keys;
};

watch(
    () => page.url,
    () => {
        const activeKeys = collectActiveKeys(navItems.value);
        expandedMenus.value = [...new Set([...expandedMenus.value, ...activeKeys])];
    },
    { immediate: true }
);

watch(
    () => navItems.value.length,
    () => {
        const activeKeys = collectActiveKeys(navItems.value);
        expandedMenus.value = [...new Set([...expandedMenus.value, ...activeKeys])];
    }
);

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};

const toggleMenu = (key) => {
    const index = expandedMenus.value.indexOf(key);
    if (index === -1) {
        expandedMenus.value.push(key);
    } else {
        expandedMenus.value.splice(index, 1);
    }
};

const isMenuExpanded = (key) => expandedMenus.value.includes(key);

const isActive = (routeName) => routeName && route().current(routeName);

const isChildActive = (routeName) => routeName && route().current(routeName);

const isParentActive = (item) => {
    if (!item.children?.length) {
        return false;
    }

    return item.children.some((child) => {
        if (child.route && route().current(child.route)) {
            return true;
        }

        return (child.children ?? []).some((nested) => nested.route && route().current(nested.route));
    });
};
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
                        <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6L21 9 12 3zm0 2.18l6 3.27-6 3.27-6-3.27 6-3.27zM5 12.82l5 2.73v5.18l-5-2.73v-5.18zm14 0v5.18l-5 2.73v-5.18l5-2.73z"/>
                            </svg>
                        </div>
                        <span class="text-lg font-semibold text-gray-800 dark:text-gray-100">TASFUED</span>
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
                        <Link
                            v-if="canManageRoles"
                            :href="route('admin.roles.index')"
                            :class="[
                                'flex items-center gap-2.5 px-3 py-2.5 text-sm rounded-md transition',
                                isActive('admin.roles.index')
                                    ? 'bg-purple-50 text-purple-700 font-medium dark:bg-purple-900/40 dark:text-purple-200'
                                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700',
                            ]"
                        >
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 4v-2m0 2a2 2 0 100 4m0-4a2 2 0 110 4m12-8v-2m0 2a2 2 0 100 4m0-4a2 2 0 110 4M4 10h16M4 18h16" />
                            </svg>
                            Roles & Permissions
                        </Link>
                        <template v-for="item in navItems" :key="item.key || item.label">
                            <!-- Item with children -->
                            <div v-if="item.children?.length">
                                <button
                                    @click="toggleMenu(item.key)"
                                    :class="[
                                        'w-full flex items-center justify-between px-3 py-2.5 text-sm rounded-md transition',
                                        isParentActive(item)
                                            ? 'bg-purple-50 text-purple-700 font-medium dark:bg-purple-900/40 dark:text-purple-200'
                                            : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700',
                                    ]"
                                >
                                    <span class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                        {{ item.label }}
                                    </span>
                                    <svg
                                        :class="['w-4 h-4 text-gray-400 transition-transform', isMenuExpanded(item.key) ? 'rotate-90' : '']"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                                <div v-show="isMenuExpanded(item.key)" class="ml-6 border-l border-gray-200 dark:border-gray-600 pl-2 space-y-0.5">
                                    <template v-for="child in item.children" :key="child.label">
                                        <div v-if="child.children?.length" class="space-y-0.5">
                                            <p class="px-3 pt-2 pb-1 text-[11px] font-semibold uppercase tracking-wide text-gray-400">
                                                {{ child.label }}
                                            </p>
                                            <Link
                                                v-for="nested in child.children"
                                                :key="`${child.label}-${nested.label}`"
                                                :href="route(nested.route)"
                                                :class="[
                                                    'block px-3 py-2 text-sm rounded-md transition',
                                                    isChildActive(nested.route)
                                                        ? 'bg-purple-50 text-purple-700 font-medium dark:bg-purple-900/40 dark:text-purple-200'
                                                        : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100',
                                                ]"
                                            >
                                                {{ nested.label }}
                                            </Link>
                                        </div>
                                        <Link
                                            v-else
                                            :href="route(child.route)"
                                            :class="[
                                                'block px-3 py-2 text-sm rounded-md transition',
                                                isChildActive(child.route)
                                                    ? 'bg-purple-50 text-purple-700 font-medium dark:bg-purple-900/40 dark:text-purple-200'
                                                    : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100',
                                            ]"
                                        >
                                            {{ child.label }}
                                        </Link>
                                    </template>
                                </div>
                            </div>

                            <!-- Regular nav item -->
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
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                                {{ item.label }}
                            </Link>
                        </template>
                    </nav>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="flex flex-1 flex-col min-h-0 overflow-hidden bg-gray-100 dark:bg-gray-900">
                <main class="flex-1 overflow-y-auto">
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
