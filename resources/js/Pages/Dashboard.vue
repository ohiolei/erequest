<script setup>
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

defineProps({
    isAdmin: {
        type: Boolean,
        required: true,
    },
});

const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="mx-auto max-w-7xl space-y-6 py-6 sm:py-8">
            <header class="flex flex-wrap items-end justify-between gap-4 border-b border-gray-200 pb-5 dark:border-gray-700">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-purple-700 dark:text-purple-300">
                        {{ isAdmin ? 'Administration' : 'Student portal' }}
                    </p>
                    <h1 class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
                        Welcome, {{ user?.name }}
                    </h1>
                </div>
                <span class="rounded-full bg-gray-200 px-3 py-1 text-xs font-medium capitalize text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    {{ isAdmin ? 'Administrator' : 'User' }}
                </span>
            </header>

            <section aria-labelledby="quick-actions-heading" class="space-y-3">
                <div>
                    <h2 id="quick-actions-heading" class="text-base font-semibold text-gray-900 dark:text-white">Quick actions</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ isAdmin ? 'Manage portal accounts and access.' : 'Manage your account details.' }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <Link
                        v-if="isAdmin && page.props.auth.canManageUsers"
                        :href="route('admin.users.index')"
                        class="inline-flex items-center gap-2 rounded-md bg-purple-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-purple-800"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-5.5-3.72M9 20H2v-2a4 4 0 017.5-2M16 3.13a4 4 0 010 7.75M8 3.13a4 4 0 000 7.75M12 14a4 4 0 100-8 4 4 0 000 8z" />
                        </svg>
                        Manage users
                    </Link>
                    <Link
                        v-if="isAdmin && page.props.auth.canManageRoles"
                        :href="route('admin.roles.index')"
                        class="inline-flex items-center gap-2 rounded-md border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 4v-2m0 2a2 2 0 100 4m0-4a2 2 0 110 4m12-8v-2m0 2a2 2 0 100 4m0-4a2 2 0 110 4M4 10h16M4 18h16" />
                        </svg>
                        Manage roles
                    </Link>
                    <Link
                        v-if="!isAdmin"
                        :href="route('profile.edit')"
                        class="inline-flex items-center gap-2 rounded-md bg-purple-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-purple-800"
                    >
                        Update profile
                    </Link>
                </div>
            </section>

            <section aria-labelledby="account-heading" class="border-t border-gray-200 pt-5 dark:border-gray-700">
                <h2 id="account-heading" class="text-base font-semibold text-gray-900 dark:text-white">Account</h2>
                <dl class="mt-3 grid gap-x-8 gap-y-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <dt class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Email</dt>
                        <dd class="mt-1 break-all text-sm text-gray-900 dark:text-gray-100">{{ user?.email }}</dd>
                    </div>
                    <div v-if="user?.matric_no">
                        <dt class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Matric number</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ user.matric_no }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Access level</dt>
                        <dd class="mt-1 text-sm capitalize text-gray-900 dark:text-gray-100">{{ isAdmin ? 'Administrator' : 'User' }}</dd>
                    </div>
                </dl>
            </section>
            </div>
    </AuthenticatedLayout>
</template>
