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
        <div class="mx-auto max-w-7xl space-y-6 p-4 sm:p-6">
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

            <section class="border-y border-gray-200 py-5 dark:border-gray-700">
                <div class="mb-4 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Create role</h2>
                    </div>
                    <button
                        type="submit"
                        form="create-role-form"
                        :disabled="createForm.processing"
                        class="inline-flex items-center gap-2 rounded-md bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800 disabled:cursor-wait disabled:opacity-60"
                    >
                        Create role
                    </button>
                </div>

                <form id="create-role-form" @submit.prevent="createRole" class="space-y-4">
                    <div class="max-w-sm">
                        <label for="new-role-name" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Role name</label>
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

                    <fieldset>
                        <legend class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Permissions</legend>
                        <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-4">
                            <label
                                v-for="permission in permissions"
                                :key="permission"
                                class="flex items-center gap-2 rounded border border-gray-200 px-3 py-2 text-sm text-gray-700 dark:border-gray-700 dark:text-gray-300"
                            >
                                <input v-model="createForm.permissions" type="checkbox" :value="permission" class="rounded border-gray-300 text-purple-700 focus:ring-purple-600" />
                                <span>{{ labelFor(permission) }}</span>
                            </label>
                        </div>
                    </fieldset>
                    <p v-if="createForm.errors.permissions" class="text-sm text-rose-600">{{ createForm.errors.permissions }}</p>
                </form>
            </section>

            <section class="space-y-3" aria-labelledby="existing-roles-heading">
                <div class="flex items-center justify-between">
                    <h2 id="existing-roles-heading" class="text-base font-semibold text-gray-900 dark:text-white">Existing roles</h2>
                </div>

                <article v-for="role in roles" :key="role.id" class="overflow-hidden rounded-md border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                    <form @submit.prevent="saveRole(role)">
                        <div class="flex flex-wrap items-end justify-between gap-3 border-b border-gray-200 px-4 py-4 dark:border-gray-700">
                            <div class="min-w-48 flex-1">
                                <label :for="`role-name-${role.id}`" class="mb-1 block text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Role name</label>
                                <input
                                    :id="`role-name-${role.id}`"
                                    v-model="roleForms[role.id].name"
                                    type="text"
                                    required
                                    maxlength="50"
                                    :readonly="role.name === 'admin'"
                                    class="w-full max-w-sm rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500 read-only:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:read-only:bg-gray-900"
                                />
                                <p v-if="roleForms[role.id].errors.name" class="mt-1 text-sm text-rose-600">{{ roleForms[role.id].errors.name }}</p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ role.users_count }} assigned {{ role.users_count === 1 ? 'account' : 'accounts' }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button
                                    type="submit"
                                    :disabled="roleForms[role.id].processing"
                                    class="rounded-md bg-purple-700 px-3 py-2 text-sm font-semibold text-white hover:bg-purple-800 disabled:opacity-60"
                                >
                                    Save changes
                                </button>
                                <button
                                    type="button"
                                    :disabled="role.name === 'admin' || role.users_count > 0"
                                    :title="role.name === 'admin' ? 'The admin role is protected' : role.users_count ? 'Remove assigned accounts before deleting this role' : 'Delete role'"
                                    class="rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-rose-700 hover:bg-rose-50 disabled:cursor-not-allowed disabled:opacity-40 dark:border-gray-600 dark:text-rose-300 dark:hover:bg-rose-950/30"
                                    @click="deleteRole(role)"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>

                        <div class="p-4">
                            <fieldset>
                                <legend class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Assigned permissions</legend>
                                <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-4">
                                    <label
                                        v-for="permission in permissions"
                                        :key="permission"
                                        class="flex items-center gap-2 rounded border border-gray-200 px-3 py-2 text-sm text-gray-700 dark:border-gray-700 dark:text-gray-300"
                                    >
                                        <input
                                            v-model="roleForms[role.id].permissions"
                                            type="checkbox"
                                            :value="permission"
                                            :disabled="role.name === 'admin' && permission === 'manage roles'"
                                            class="rounded border-gray-300 text-purple-700 focus:ring-purple-600"
                                        />
                                        <span>{{ labelFor(permission) }}</span>
                                    </label>
                                </div>
                            </fieldset>
                            <p v-if="roleForms[role.id].errors.permissions" class="mt-2 text-sm text-rose-600">{{ roleForms[role.id].errors.permissions }}</p>
                        </div>
                    </form>
                </article>
            </section>

            <section class="border-t border-gray-200 pt-5 dark:border-gray-700" aria-labelledby="permissions-heading">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 id="permissions-heading" class="text-base font-semibold text-gray-900 dark:text-white">Permissions</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ permissionRecords.length }} available</p>
                    </div>
                </div>

                <form @submit.prevent="createPermission" class="mb-4 flex flex-wrap items-end gap-3">
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
                    <button
                        type="submit"
                        :disabled="createPermissionForm.processing"
                        class="rounded-md bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800 disabled:cursor-wait disabled:opacity-60"
                    >
                        Create permission
                    </button>
                </form>

                <div class="divide-y divide-gray-200 border-y border-gray-200 dark:divide-gray-700 dark:border-gray-700">
                    <form
                        v-for="permission in permissionRecords"
                        :key="permission.id"
                        @submit.prevent="savePermission(permission)"
                        class="flex flex-wrap items-end gap-3 py-3"
                    >
                        <div class="min-w-56 flex-1">
                            <label :for="`permission-name-${permission.id}`" class="mb-1 block text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Permission name</label>
                            <input
                                :id="`permission-name-${permission.id}`"
                                v-model="permissionForms[permission.id].name"
                                type="text"
                                required
                                maxlength="100"
                                :readonly="permission.name === 'manage roles'"
                                class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500 read-only:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:read-only:bg-gray-900"
                            />
                            <p v-if="permissionForms[permission.id].errors.name" class="mt-1 text-sm text-rose-600">{{ permissionForms[permission.id].errors.name }}</p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ permission.roles_count }} roles · {{ permission.users_count }} users
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                type="submit"
                                :disabled="permissionForms[permission.id].processing"
                                class="rounded-md bg-purple-700 px-3 py-2 text-sm font-semibold text-white hover:bg-purple-800 disabled:opacity-60"
                            >
                                Save
                            </button>
                            <button
                                type="button"
                                :disabled="permission.name === 'manage roles' || permission.roles_count > 0 || permission.users_count > 0"
                                :title="permission.name === 'manage roles' ? 'This permission is protected' : permission.roles_count || permission.users_count ? 'Remove assignments before deleting' : 'Delete permission'"
                                class="rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-rose-700 hover:bg-rose-50 disabled:cursor-not-allowed disabled:opacity-40 dark:border-gray-600 dark:text-rose-300 dark:hover:bg-rose-950/30"
                                @click="deletePermission(permission)"
                            >
                                Delete
                            </button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>