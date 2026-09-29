<script setup>
import { reactive, ref, watch } from 'vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    roles: {
        type: Array,
        required: true,
    },
    permissions: {
        type: Array,
        required: true,
    },
    permissionRecords: {
        type: Array,
        required: true,
    },
});

const page = usePage();
const statusMessage = ref('');
const activeTab = ref('roles');
const createForm = useForm({
    name: '',
    permissions: [],
});
const createPermissionForm = useForm({ name: '' });
const roleForms = reactive({});
const permissionForms = reactive({});

watch(
    () => props.roles,
    (roles) => {
        roles.forEach((role) => {
            const values = {
                name: role.name,
                permissions: [...role.permissions],
            };

            if (!roleForms[role.id]) {
                roleForms[role.id] = useForm(values);
                return;
            }

            Object.assign(roleForms[role.id], values);
            roleForms[role.id].defaults(values);
        });
    },
    { immediate: true },
);

watch(
    () => props.permissionRecords,
    (permissions) => {
        permissions.forEach((permission) => {
            const values = { name: permission.name };

            if (!permissionForms[permission.id]) {
                permissionForms[permission.id] = useForm(values);
                return;
            }

            Object.assign(permissionForms[permission.id], values);
            permissionForms[permission.id].defaults(values);
        });
    },
    { immediate: true },
);

const labelFor = (permission) => permission
    .split(' ')
    .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ');

const createRole = () => {
    statusMessage.value = '';
    createForm.post(route('admin.roles.store'), {
        preserveScroll: true,
        onSuccess: () => {
            createForm.reset();
            statusMessage.value = 'Role created.';
        },
    });
};

const saveRole = (role) => {
    const form = roleForms[role.id];
    statusMessage.value = '';
    form.patch(route('admin.roles.update', role.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.defaults();
            form.reset();
            statusMessage.value = 'Role updated.';
        },
    });
};

const deleteRole = (role) => {
    if (!window.confirm(`Delete the ${role.name} role?`)) {
        return;
    }

    statusMessage.value = '';
    router.delete(route('admin.roles.destroy', role.id), {
        preserveScroll: true,
        onSuccess: () => {
            statusMessage.value = 'Role deleted.';
        },
    });
};

const createPermission = () => {
    statusMessage.value = '';
    createPermissionForm.post(route('admin.permissions.store'), {
        preserveScroll: true,
        onSuccess: () => {
            createPermissionForm.reset();
            statusMessage.value = 'Permission created.';
        },
    });
};

const savePermission = (permission) => {
    const form = permissionForms[permission.id];
    statusMessage.value = '';
    form.patch(route('admin.permissions.update', permission.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.defaults();
            form.reset();
            statusMessage.value = 'Permission updated.';
        },
    });
};

const deletePermission = (permission) => {
    if (!window.confirm(`Delete the ${permission.name} permission?`)) {
        return;
    }

    statusMessage.value = '';
    router.delete(route('admin.permissions.destroy', permission.id), {
        preserveScroll: true,
        onSuccess: () => {
            statusMessage.value = 'Permission deleted.';
        },
    });
};
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
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ roles.length }} roles · {{ permissions.length }} permissions</p>
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
                    Roles <span class="ml-1 text-xs">{{ roles.length }}</span>
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
                    Permissions <span class="ml-1 text-xs">{{ permissionRecords.length }}</span>
                </button>
            </div>

            <section id="roles-panel" role="tabpanel" aria-labelledby="roles-tab" v-show="activeTab === 'roles'" class="space-y-5">
                <form @submit.prevent="createRole" class="flex flex-wrap items-end gap-3 border-b border-gray-200 pb-5 dark:border-gray-700">
                    <div class="min-w-56 flex-1">
                        <label for="new-role-name" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">New role</label>
                        <input
                            id="new-role-name"
                            v-model="createForm.name"
                            type="text"
                            required
                            maxlength="50"
                            placeholder="request-reviewer"
                            class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                        />
                        <p v-if="createForm.errors.name" class="mt-1 text-sm text-rose-600">{{ createForm.errors.name }}</p>
                    </div>
                    <button type="submit" :disabled="createForm.processing" class="rounded-md bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800 disabled:opacity-60">
                        Create role
                    </button>
                    <fieldset class="w-full">
                        <legend class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Initial permissions</legend>
                        <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-4">
                            <label v-for="permission in permissions" :key="permission" class="flex items-center gap-2 rounded border border-gray-200 px-3 py-2 text-sm text-gray-700 dark:border-gray-700 dark:text-gray-300">
                                <input v-model="createForm.permissions" type="checkbox" :value="permission" class="rounded border-gray-300 text-purple-700 focus:ring-purple-600" />
                                <span>{{ labelFor(permission) }}</span>
                            </label>
                        </div>
                    </fieldset>
                    <p v-if="createForm.errors.permissions" class="w-full text-sm text-rose-600">{{ createForm.errors.permissions }}</p>
                </form>

                <div class="overflow-x-auto rounded-md border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                    <table class="w-full min-w-[850px] divide-y divide-gray-200 text-left text-sm dark:divide-gray-700">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-4 py-3">Role</th>
                                <th scope="col" class="px-4 py-3">Accounts</th>
                                <th scope="col" class="px-4 py-3">Permissions</th>
                                <th scope="col" class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="role in roles" :key="role.id" class="align-top">
                                <td class="w-56 px-4 py-3">
                                    <label :for="`role-name-${role.id}`" class="sr-only">Role name</label>
                                    <input
                                        :id="`role-name-${role.id}`"
                                        v-model="roleForms[role.id].name"
                                        type="text"
                                        required
                                        maxlength="50"
                                        :readonly="role.name === 'admin'"
                                        class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500 read-only:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:read-only:bg-gray-900"
                                    />
                                    <p v-if="roleForms[role.id].errors.name" class="mt-1 text-xs text-rose-600">{{ roleForms[role.id].errors.name }}</p>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-gray-600 dark:text-gray-300">{{ role.users_count }}</td>
                                <td class="px-4 py-3">
                                    <details class="group">
                                        <summary class="w-fit cursor-pointer select-none rounded border border-gray-300 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                                            {{ roleForms[role.id].permissions.length }} selected
                                        </summary>
                                        <fieldset class="mt-3 grid gap-2 sm:grid-cols-2">
                                            <legend class="sr-only">Role permissions</legend>
                                            <label v-for="permission in permissions" :key="permission" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                                <input
                                                    v-model="roleForms[role.id].permissions"
                                                    type="checkbox"
                                                    :value="permission"
                                                    :disabled="role.name === 'admin' && permission === 'manage roles'"
                                                    class="rounded border-gray-300 text-purple-700 focus:ring-purple-600"
                                                />
                                                <span>{{ labelFor(permission) }}</span>
                                            </label>
                                        </fieldset>
                                        <p v-if="roleForms[role.id].errors.permissions" class="mt-2 text-xs text-rose-600">{{ roleForms[role.id].errors.permissions }}</p>
                                    </details>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <button type="button" :disabled="roleForms[role.id].processing" class="rounded-md bg-purple-700 px-3 py-1.5 text-sm font-semibold text-white hover:bg-purple-800 disabled:opacity-60" @click="saveRole(role)">
                                            Save
                                        </button>
                                        <button
                                            type="button"
                                            :disabled="role.name === 'admin' || role.users_count > 0"
                                            :title="role.name === 'admin' ? 'The admin role is protected' : role.users_count ? 'Remove assigned accounts before deleting this role' : 'Delete role'"
                                            class="rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-rose-700 hover:bg-rose-50 disabled:cursor-not-allowed disabled:opacity-40 dark:border-gray-600 dark:text-rose-300 dark:hover:bg-rose-950/30"
                                            @click="deleteRole(role)"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="roles.length === 0">
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">No roles have been created.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="permissions-panel" role="tabpanel" aria-labelledby="permissions-tab" v-show="activeTab === 'permissions'" class="space-y-5">
                <form @submit.prevent="createPermission" class="flex flex-wrap items-end gap-3 border-b border-gray-200 pb-5 dark:border-gray-700">
                    <div class="min-w-56 flex-1">
                        <label for="new-permission-name" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">New permission</label>
                        <input
                            id="new-permission-name"
                            v-model="createPermissionForm.name"
                            type="text"
                            required
                            maxlength="100"
                            placeholder="approve requests"
                            class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                        />
                        <p v-if="createPermissionForm.errors.name" class="mt-1 text-sm text-rose-600">{{ createPermissionForm.errors.name }}</p>
                    </div>
                    <button type="submit" :disabled="createPermissionForm.processing" class="rounded-md bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800 disabled:opacity-60">
                        Create permission
                    </button>
                </form>

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
                            <tr v-for="permission in permissionRecords" :key="permission.id">
                                <td class="px-4 py-3">
                                    <label :for="`permission-name-${permission.id}`" class="sr-only">Permission name</label>
                                    <input
                                        :id="`permission-name-${permission.id}`"
                                        v-model="permissionForms[permission.id].name"
                                        type="text"
                                        required
                                        maxlength="100"
                                        :readonly="permission.name === 'manage roles'"
                                        class="w-full max-w-md rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500 read-only:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:read-only:bg-gray-900"
                                    />
                                    <p v-if="permissionForms[permission.id].errors.name" class="mt-1 text-xs text-rose-600">{{ permissionForms[permission.id].errors.name }}</p>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-gray-600 dark:text-gray-300">
                                    {{ permission.roles_count }} roles · {{ permission.users_count }} users
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <button type="button" :disabled="permissionForms[permission.id].processing" class="rounded-md bg-purple-700 px-3 py-1.5 text-sm font-semibold text-white hover:bg-purple-800 disabled:opacity-60" @click="savePermission(permission)">
                                            Save
                                        </button>
                                        <button
                                            type="button"
                                            :disabled="permission.name === 'manage roles' || permission.roles_count > 0 || permission.users_count > 0"
                                            :title="permission.name === 'manage roles' ? 'This permission is protected' : permission.roles_count || permission.users_count ? 'Remove assignments before deleting' : 'Delete permission'"
                                            class="rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-rose-700 hover:bg-rose-50 disabled:cursor-not-allowed disabled:opacity-40 dark:border-gray-600 dark:text-rose-300 dark:hover:bg-rose-950/30"
                                            @click="deletePermission(permission)"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="permissionRecords.length === 0">
                                <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500">No permissions have been created.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>