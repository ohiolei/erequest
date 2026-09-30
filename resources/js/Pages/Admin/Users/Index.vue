<script setup>
import { reactive, ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Dropdown from '@/Components/Dropdown.vue';
import AssignUserPermissionsModal from './Partials/AssignUserPermissionsModal.vue';
import AssignUserRolesModal from './Partials/AssignUserRolesModal.vue';
import CreateUserModal from './Partials/CreateUserModal.vue';
import DeleteUserModal from './Partials/DeleteUserModal.vue';
import EditUserModal from './Partials/EditUserModal.vue';

const props = defineProps({
    users: {
        type: Object,
        required: true,
    },
    roles: {
        type: Array,
        required: true,
    },
    permissions: {
        type: Array,
        required: true,
    },
    filters: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const statusMessage = ref('');
const activeModal = ref(null);
const selectedUser = ref(null);
const filters = reactive({
    search: props.filters.search ?? '',
    role: props.filters.role ?? '',
});

const filterUsers = () => {
    const query = {};
    if (filters.search.trim()) query.search = filters.search.trim();
    if (filters.role) query.role = filters.role;

    router.get(route('admin.users.index'), query, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const resetFilters = () => {
    filters.search = '';
    filters.role = '';
    filterUsers();
};

const openUserModal = (modal, user = null) => {
    selectedUser.value = user;
    activeModal.value = modal;
};

const closeModal = () => {
    activeModal.value = null;
    selectedUser.value = null;
};

const userSaved = (message) => {
    statusMessage.value = message;
};

const paginationLabel = (label) => label
    .replaceAll('&laquo;', '‹')
    .replaceAll('&raquo;', '›');
</script>

<template>
    <Head title="User Manager" />

    <AuthenticatedLayout>
        <div class="w-full max-w-none space-y-5 py-4 sm:py-6">
            <header class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-purple-700 dark:text-purple-300">Administration</p>
                    <h1 class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">User Manager</h1>
                </div>
                <button
                    type="button"
                    class="rounded-md bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800"
                    @click="openUserModal('create')"
                >
                    Add user
                </button>
            </header>

            <p v-if="statusMessage" role="status" class="border-l-4 border-emerald-500 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-200">
                {{ statusMessage }}
            </p>
            <p v-if="page.props.errors?.user" role="alert" class="border-l-4 border-rose-500 bg-rose-50 px-4 py-3 text-sm text-rose-800 dark:bg-rose-950/40 dark:text-rose-200">
                {{ page.props.errors.user }}
            </p>

            <form @submit.prevent="filterUsers" class="flex flex-wrap items-end gap-3 border-b border-gray-200 pb-4 dark:border-gray-700">
                <div class="min-w-56 flex-1">
                    <label for="user-search" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Search users</label>
                    <input id="user-search" v-model="filters.search" type="search" placeholder="Name, email, or matric number" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                </div>
                <div class="w-full sm:w-52">
                    <label for="user-role-filter" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                    <select id="user-role-filter" v-model="filters.role" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                        <option value="">All roles</option>
                        <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
                    </select>
                </div>
                <button type="submit" class="rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600">Search</button>
                <button type="button" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800" @click="resetFilters">Reset</button>
            </form>

            <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                <p>{{ users.total }} users</p>
                <p>Showing {{ users.from ?? 0 }}–{{ users.to ?? 0 }}</p>
            </div>

            <div class="overflow-x-auto rounded-md border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                <table class="w-full min-w-[900px] table-fixed divide-y divide-gray-200 text-left text-sm dark:divide-gray-700 xl:min-w-0">
                    <colgroup>
                        <col class="w-[24%]" />
                        <col class="w-[12%]" />
                        <col class="w-[14%]" />
                        <col class="w-[16%]" />
                        <col class="w-[10%]" />
                        <col class="w-[10%]" />
                        <col class="w-[14%]" />
                    </colgroup>
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-3">User</th>
                            <th scope="col" class="px-4 py-3">Matric number</th>
                            <th scope="col" class="px-4 py-3">Roles</th>
                            <th scope="col" class="px-4 py-3">Direct permissions</th>
                            <th scope="col" class="px-4 py-3">Joined</th>
                            <th scope="col" class="px-4 py-3">Email verified</th>
                            <th scope="col" class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="user in users.data" :key="user.id" class="align-top">
                            <td class="min-w-0 px-2 py-3 lg:px-3">
                                <p class="truncate font-medium text-gray-900 dark:text-gray-100">{{ user.name }}</p>
                                <p class="mt-1 truncate text-sm text-gray-600 dark:text-gray-400">{{ user.email }}</p>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-gray-600 dark:text-gray-300">
                                {{ user.matric_no || '—' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-gray-600 dark:text-gray-300">
                                {{ user.roles.length }} {{ user.roles.length === 1 ? 'role' : 'roles' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-gray-600 dark:text-gray-300">
                                {{ user.permissions.length }} {{ user.permissions.length === 1 ? 'direct' : 'direct' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-gray-600 dark:text-gray-300">{{ user.created_at }}</td>
                            <td class="whitespace-nowrap px-4 py-3">
                                <span :class="user.email_verified ? 'text-emerald-700 dark:text-emerald-300' : 'text-gray-500 dark:text-gray-400'">{{ user.email_verified ? 'Yes' : 'No' }}</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right">
                                <Dropdown align="right" width="56" content-classes="bg-white py-1 dark:bg-gray-800">
                                    <template #trigger>
                                        <button type="button" class="inline-flex items-center gap-2 rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                                            Actions
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6" />
                                            </svg>
                                        </button>
                                    </template>
                                    <template #content>
                                        <button type="button" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" @click="openUserModal('edit', user)">
                                            Edit profile
                                        </button>
                                        <button type="button" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" @click="openUserModal('roles', user)">
                                            Assign roles
                                        </button>
                                        <button type="button" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" @click="openUserModal('permissions', user)">
                                            Direct permissions
                                        </button>
                                        <button
                                            type="button"
                                            :disabled="user.id === page.props.auth.user.id"
                                            :title="user.id === page.props.auth.user.id ? 'You cannot delete your own account' : 'Delete account'"
                                            class="block w-full px-4 py-2 text-left text-sm text-rose-700 hover:bg-rose-50 disabled:cursor-not-allowed disabled:opacity-40 dark:text-rose-300 dark:hover:bg-rose-950/30"
                                            @click="openUserModal('delete', user)"
                                        >
                                            Delete account
                                        </button>
                                    </template>
                                </Dropdown>
                            </td>
                        </tr>
                        <tr v-if="users.data.length === 0">
                            <td colspan="7" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No users match these filters.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <nav v-if="users.links.length > 3" aria-label="User list pages" class="flex flex-wrap justify-end gap-1">
                <Link
                    v-for="link in users.links"
                    :key="link.label"
                    :href="link.url ?? '#'"
                    :aria-current="link.active ? 'page' : undefined"
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

        <CreateUserModal
            :show="activeModal === 'create'"
            :roles="roles"
            :permissions="permissions"
            @close="closeModal"
            @saved="userSaved"
        />
        <EditUserModal
            :show="activeModal === 'edit'"
            :user="selectedUser"
            @close="closeModal"
            @saved="userSaved"
        />
        <AssignUserRolesModal
            :show="activeModal === 'roles'"
            :user="selectedUser"
            :roles="roles"
            @close="closeModal"
            @saved="userSaved"
        />
        <AssignUserPermissionsModal
            :show="activeModal === 'permissions'"
            :user="selectedUser"
            :permissions="permissions"
            @close="closeModal"
            @saved="userSaved"
        />
        <DeleteUserModal
            :show="activeModal === 'delete'"
            :user="selectedUser"
            @close="closeModal"
            @saved="userSaved"
        />
    </AuthenticatedLayout>
</template>