<script setup>
import { ref } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Dropdown from '@/Components/Dropdown.vue';
import AttachPermissionsModal from './Partials/AttachPermissionsModal.vue';
import CreateRoleModal from './Partials/CreateRoleModal.vue';
import DeleteRoleModal from './Partials/DeleteRoleModal.vue';
import EditRoleModal from './Partials/EditRoleModal.vue';
import DeletePermissionModal from './Partials/DeletePermissionModal.vue';
import EditPermissionModal from './Partials/EditPermissionModal.vue';

const props = defineProps({
    roles: {
        type: Object,
        required: true,
    },
    permissions: {
        type: Array,
        required: true,
    },
    permissionRecords: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const statusMessage = ref('');
const activeTab = ref('roles');
const activeModal = ref(null);
const selectedRole = ref(null);
const activePermissionModal = ref(null);
const selectedPermission = ref(null);

const openRoleModal = (modal, role = null) => {
    selectedRole.value = role;
    activeModal.value = modal;
};

const openActionModal = (event, modal, role) => {
    event.currentTarget.closest('details').open = false;
    openRoleModal(modal, role);
};

const closeModal = () => {
    activeModal.value = null;
    selectedRole.value = null;
};

const showSavedMessage = (message) => {
    statusMessage.value = message;
};

const openPermissionModal = (modal, permission = null) => {
    selectedPermission.value = permission;
    activePermissionModal.value = modal;
};

const closePermissionModal = () => {
    activePermissionModal.value = null;
    selectedPermission.value = null;
};

const permissionSaved = (message) => {
    statusMessage.value = message;
};

const paginationLabel = (label) => label
    .replaceAll('&laquo;', '‹')
    .replaceAll('&raquo;', '›');
</script>

<template>
    <Head title="Roles & Permissions" />

    <AuthenticatedLayout>
        <div class="mx-auto max-w-7xl space-y-6 py-4 sm:py-6">
            <header class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-purple-700 dark:text-purple-300">Administration</p>
                    <h1 class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">Roles & Permissions</h1>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ roles.total }} roles · {{ permissionRecords.total }} permissions</p>
            </header>

            <p v-if="statusMessage" role="status" class="border-l-4 border-emerald-500 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-200">
                {{ statusMessage }}
            </p>
            <p v-if="page.props.errors?.role" role="alert" class="border-l-4 border-rose-500 bg-rose-50 px-4 py-3 text-sm text-rose-800 dark:bg-rose-950/40 dark:text-rose-200">
                {{ page.props.errors.role }}
            </p>
            <p v-if="page.props.errors?.permission" role="alert" class="border-l-4 border-rose-500 bg-rose-50 px-4 py-3 text-sm text-rose-800 dark:bg-rose-950/40 dark:text-rose-200">
                {{ page.props.errors.permission }}
            </p>

            <div role="tablist" aria-label="Role and permission management" class="flex border-b border-gray-200 dark:border-gray-700">
                <button
                    id="roles-tab"
                    type="button"
                    role="tab"
                    aria-controls="roles-panel"
                    :aria-selected="activeTab === 'roles'"
                    :class="['border-b-2 px-4 py-3 text-sm font-medium', activeTab === 'roles' ? 'border-purple-700 text-purple-700 dark:text-purple-300' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200']"
                    @click="activeTab = 'roles'"
                >
                    Roles <span class="ml-1 text-xs">{{ roles.total }}</span>
                </button>
                <button
                    id="permissions-tab"
                    type="button"
                    role="tab"
                    aria-controls="permissions-panel"
                    :aria-selected="activeTab === 'permissions'"
                    :class="['border-b-2 px-4 py-3 text-sm font-medium', activeTab === 'permissions' ? 'border-purple-700 text-purple-700 dark:text-purple-300' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200']"
                    @click="activeTab = 'permissions'"
                >
                    Permissions <span class="ml-1 text-xs">{{ permissionRecords.total }}</span>
                </button>
            </div>

            <section id="roles-panel" role="tabpanel" aria-labelledby="roles-tab" v-show="activeTab === 'roles'" class="space-y-5">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 pb-4 dark:border-gray-700">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Roles</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Edit role details, attach permissions, or remove unused roles.</p>
                    </div>
                    <button type="button" class="rounded-md bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800" @click="openRoleModal('create')">
                        Add role
                    </button>
                </div>

                <div class="overflow-x-auto rounded-md border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                    <table class="w-full min-w-[850px] divide-y divide-gray-200 text-left text-sm dark:divide-gray-700">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-4 py-3">Role</th>
                                <th scope="col" class="px-4 py-3">Accounts</th>
                                <th scope="col" class="px-4 py-3">Attached permissions</th>
                                <th scope="col" class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="role in roles.data" :key="role.id" class="align-top">
                                <td class="whitespace-nowrap px-4 py-3 font-medium text-gray-900 dark:text-gray-100">
                                    {{ role.name }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-gray-600 dark:text-gray-300">{{ role.users_count }}</td>
                                <td class="px-4 py-3">
                                    <span class="text-gray-600 dark:text-gray-300">{{ role.permissions.length }}</span>
                                    <span class="ml-1 text-gray-500 dark:text-gray-400">{{ role.permissions.length === 1 ? 'permission' : 'permissions' }}</span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <Dropdown align="right" width="48" content-classes="bg-white py-1 dark:bg-gray-800">
                                        <template #trigger>
                                            <button type="button" class="inline-flex items-center gap-2 rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                                                Actions
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6" />
                                                </svg>
                                            </button>
                                        </template>
                                        <template #content>
                                            <button type="button" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" @click="openRoleModal('edit', role)">
                                                Edit role
                                            </button>
                                            <button type="button" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" @click="openRoleModal('permissions', role)">
                                                Attach permissions
                                            </button>
                                            <button
                                                type="button"
                                                :disabled="role.name === 'admin' || role.users_count > 0"
                                                :title="role.name === 'admin' ? 'The admin role is protected' : role.users_count ? 'Remove assigned accounts before deleting this role' : 'Delete role'"
                                                class="block w-full px-4 py-2 text-left text-sm text-rose-700 hover:bg-rose-50 disabled:cursor-not-allowed disabled:opacity-40 dark:text-rose-300 dark:hover:bg-rose-950/30"
                                                @click="openRoleModal('delete', role)"
                                            >
                                                Delete role
                                            </button>
                                        </template>
                                    </Dropdown>
                                </td>
                            </tr>
                            <tr v-if="roles.data.length === 0">
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">No roles have been created.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Showing {{ roles.from ?? 0 }}–{{ roles.to ?? 0 }} of {{ roles.total }} roles
                    </p>
                    <nav aria-label="Role list pages" class="flex flex-wrap justify-end gap-1">
                        <Link
                            v-for="link in roles.links"
                            :key="`roles-${link.label}`"
                            :href="link.url ?? '#'"
                            :aria-current="link.active ? 'page' : undefined"
                            preserve-state
                            preserve-scroll
                            :class="[
                                'min-w-9 rounded border px-3 py-2 text-center text-sm',
                                link.active ? 'border-purple-700 bg-purple-700 text-white' : 'border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800',
                                !link.url ? 'pointer-events-none opacity-40' : '',
                            ]"
                        >
                            {{ paginationLabel(link.label) }}
                        </Link>
                    </nav>
                </div>
            </section>

            <section id="permissions-panel" role="tabpanel" aria-labelledby="permissions-tab" v-show="activeTab === 'permissions'" class="space-y-5">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 pb-4 dark:border-gray-700">
                    <div class="min-w-56 flex-1">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Permissions</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage access permissions.</p>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-md border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                    <table class="w-full min-w-[650px] divide-y divide-gray-200 text-left text-sm dark:divide-gray-700">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-4 py-3">Permission</th>
                                <th scope="col" class="px-4 py-3">Assigned to</th>
                                <th scope="col" class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="permission in permissionRecords.data" :key="permission.id">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">
                                    {{ permission.name }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-gray-600 dark:text-gray-300">
                                    {{ permission.roles_count }} roles · {{ permission.users_count }} users
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <Dropdown align="right" width="48" content-classes="bg-white py-1 dark:bg-gray-800">
                                        <template #trigger>
                                            <button type="button" class="inline-flex items-center gap-2 rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                                                Actions
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6" />
                                                </svg>
                                            </button>
                                        </template>
                                        <template #content>
                                            <button type="button" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" @click="openPermissionModal('edit', permission)">
                                                Edit permission
                                            </button>
                                            <button type="button" class="block w-full px-4 py-2 text-left text-sm text-rose-700 hover:bg-rose-50 dark:text-rose-300 dark:hover:bg-rose-950/30" @click="openPermissionModal('delete', permission)">
                                                Delete permission
                                            </button>
                                        </template>
                                    </Dropdown>
                                </td>
                            </tr>
                            <tr v-if="permissionRecords.data.length === 0">
                                <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500">No permissions have been created.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Showing {{ permissionRecords.from ?? 0 }}–{{ permissionRecords.to ?? 0 }} of {{ permissionRecords.total }} permissions
                    </p>
                    <nav aria-label="Permission list pages" class="flex flex-wrap justify-end gap-1">
                        <Link
                            v-for="link in permissionRecords.links"
                            :key="`permissions-${link.label}`"
                            :href="link.url ?? '#'"
                            :aria-current="link.active ? 'page' : undefined"
                            preserve-state
                            preserve-scroll
                            :class="[
                                'min-w-9 rounded border px-3 py-2 text-center text-sm',
                                link.active ? 'border-purple-700 bg-purple-700 text-white' : 'border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800',
                                !link.url ? 'pointer-events-none opacity-40' : '',
                            ]"
                        >
                            {{ paginationLabel(link.label) }}
                        </Link>
                    </nav>
                </div>
            </section>
        </div>

        <CreateRoleModal
            :show="activeModal === 'create'"
            :permissions="permissions"
            @close="closeModal"
            @saved="showSavedMessage"
        />
        <EditRoleModal
            :show="activeModal === 'edit'"
            :role="selectedRole"
            @close="closeModal"
            @saved="showSavedMessage"
        />
        <AttachPermissionsModal
            :show="activeModal === 'permissions'"
            :role="selectedRole"
            :permissions="permissions"
            @close="closeModal"
            @saved="showSavedMessage"
        />
        <DeleteRoleModal
            :show="activeModal === 'delete'"
            :role="selectedRole"
            @close="closeModal"
            @saved="showSavedMessage"
        />
        <EditPermissionModal
            :show="activePermissionModal === 'edit'"
            :permission="selectedPermission"
            @close="closePermissionModal"
            @saved="permissionSaved"
        />
        <DeletePermissionModal
            :show="activePermissionModal === 'delete'"
            :permission="selectedPermission"
            @close="closePermissionModal"
            @saved="permissionSaved"
        />
    </AuthenticatedLayout>
</template>