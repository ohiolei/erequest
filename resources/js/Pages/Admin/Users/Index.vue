<script setup>
import { reactive, ref, watch } from 'vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    users: {
        type: Object,
        required: true,
    },
    roles: {
        type: Array,
        required: true,
    },
    filters: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const showCreateForm = ref(false);
const statusMessage = ref('');
const filters = reactive({
    search: props.filters.search ?? '',
    role: props.filters.role ?? '',
});
const createForm = useForm({
    name: '',
    email: '',
    matric_no: '',
    password: '',
    password_confirmation: '',
    roles: ['student'],
});
const userForms = reactive({});

watch(
    () => props.users.data,
    (users) => {
        users.forEach((user) => {
            const values = {
                name: user.name,
                email: user.email,
                matric_no: user.matric_no ?? '',
                roles: [...user.roles],
            };

            if (!userForms[user.id]) {
                userForms[user.id] = useForm(values);
                return;
            }

            Object.assign(userForms[user.id], values);
            userForms[user.id].defaults(values);
        });
    },
    { immediate: true },
);

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

const createUser = () => {
    statusMessage.value = '';
    createForm.post(route('admin.users.store'), {
        onSuccess: () => {
            createForm.reset();
            statusMessage.value = 'User created.';
            showCreateForm.value = false;
        },
    });
};

const saveUser = (user) => {
    const form = userForms[user.id];
    statusMessage.value = '';
    form.patch(route('admin.users.update', user.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.defaults();
            form.reset();
            statusMessage.value = 'User updated.';
        },
    });
};

const deleteUser = (user) => {
    if (!window.confirm(`Delete the account for ${user.name}?`)) {
        return;
    }

    statusMessage.value = '';
    router.delete(route('admin.users.destroy', user.id), {
        preserveScroll: true,
        onSuccess: () => {
            statusMessage.value = 'User deleted.';
        },
    });
};

const paginationLabel = (label) => label
    .replaceAll('&laquo;', '‹')
    .replaceAll('&raquo;', '›');
</script>

<template>
    <Head title="User Manager" />

    <AuthenticatedLayout>
        <div class="mx-auto max-w-7xl space-y-5 p-4 sm:p-6">
            <header class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-purple-700 dark:text-purple-300">Administration</p>
                    <h1 class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">User Manager</h1>
                </div>
                <button
                    type="button"
                    class="rounded-md bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800"
                    @click="showCreateForm = !showCreateForm"
                >
                    {{ showCreateForm ? 'Close form' : 'Add user' }}
                </button>
            </header>

            <p v-if="statusMessage" role="status" class="border-l-4 border-emerald-500 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-200">
                {{ statusMessage }}
            </p>
            <p v-if="page.props.errors?.user" role="alert" class="border-l-4 border-rose-500 bg-rose-50 px-4 py-3 text-sm text-rose-800 dark:bg-rose-950/40 dark:text-rose-200">
                {{ page.props.errors.user }}
            </p>

            <form v-if="showCreateForm" @submit.prevent="createUser" class="space-y-4 border-y border-gray-200 py-5 dark:border-gray-700">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">New account</h2>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <label for="new-user-name" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Full name</label>
                        <input id="new-user-name" v-model="createForm.name" required autocomplete="name" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                        <p v-if="createForm.errors.name" class="mt-1 text-sm text-rose-600">{{ createForm.errors.name }}</p>
                    </div>
                    <div>
                        <label for="new-user-email" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <input id="new-user-email" v-model="createForm.email" type="email" required autocomplete="email" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                        <p v-if="createForm.errors.email" class="mt-1 text-sm text-rose-600">{{ createForm.errors.email }}</p>
                    </div>
                    <div>
                        <label for="new-user-matric" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Matric number</label>
                        <input id="new-user-matric" v-model="createForm.matric_no" autocomplete="off" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                        <p v-if="createForm.errors.matric_no" class="mt-1 text-sm text-rose-600">{{ createForm.errors.matric_no }}</p>
                    </div>
                    <div>
                        <label for="new-user-password" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Temporary password</label>
                        <input id="new-user-password" v-model="createForm.password" type="password" required minlength="8" autocomplete="new-password" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                        <p v-if="createForm.errors.password" class="mt-1 text-sm text-rose-600">{{ createForm.errors.password }}</p>
                    </div>
                    <div>
                        <label for="new-user-password-confirmation" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm password</label>
                        <input id="new-user-password-confirmation" v-model="createForm.password_confirmation" type="password" required minlength="8" autocomplete="new-password" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                    </div>
                </div>
                <fieldset>
                    <legend class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Roles</legend>
                    <div class="flex flex-wrap gap-x-5 gap-y-2">
                        <label v-for="role in roles" :key="role" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                            <input v-model="createForm.roles" type="checkbox" :value="role" class="rounded border-gray-300 text-purple-700 focus:ring-purple-600" />
                            {{ role }}
                        </label>
                    </div>
                    <p v-if="createForm.errors.roles" class="mt-1 text-sm text-rose-600">{{ createForm.errors.roles }}</p>
                </fieldset>
                <button type="submit" :disabled="createForm.processing" class="rounded-md bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800 disabled:opacity-60">
                    Create account
                </button>
            </form>

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
                <table class="w-full min-w-[1050px] divide-y divide-gray-200 text-left text-sm dark:divide-gray-700">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-3">User</th>
                            <th scope="col" class="px-4 py-3">Matric number</th>
                            <th scope="col" class="px-4 py-3">Roles</th>
                            <th scope="col" class="px-4 py-3">Joined</th>
                            <th scope="col" class="px-4 py-3">Email verified</th>
                            <th scope="col" class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="user in users.data" :key="user.id" class="align-top">
                            <td class="min-w-64 px-4 py-3">
                                <label :for="`user-name-${user.id}`" class="sr-only">Name</label>
                                <input :id="`user-name-${user.id}`" v-model="userForms[user.id].name" required class="mb-2 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                                <label :for="`user-email-${user.id}`" class="sr-only">Email</label>
                                <input :id="`user-email-${user.id}`" v-model="userForms[user.id].email" type="email" required class="w-full rounded-md border-gray-300 text-sm text-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300" />
                                <p v-if="userForms[user.id].errors.name" class="mt-1 text-xs text-rose-600">{{ userForms[user.id].errors.name }}</p>
                                <p v-if="userForms[user.id].errors.email" class="mt-1 text-xs text-rose-600">{{ userForms[user.id].errors.email }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <label :for="`user-matric-${user.id}`" class="sr-only">Matric number</label>
                                <input :id="`user-matric-${user.id}`" v-model="userForms[user.id].matric_no" class="w-40 rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                                <p v-if="userForms[user.id].errors.matric_no" class="mt-1 text-xs text-rose-600">{{ userForms[user.id].errors.matric_no }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <details>
                                    <summary class="w-fit cursor-pointer select-none rounded border border-gray-300 px-3 py-1.5 text-sm text-gray-700 dark:border-gray-600 dark:text-gray-200">{{ userForms[user.id].roles.length }} roles</summary>
                                    <fieldset class="mt-2 space-y-2">
                                        <legend class="sr-only">User roles</legend>
                                        <label v-for="role in roles" :key="role" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                            <input v-model="userForms[user.id].roles" type="checkbox" :value="role" class="rounded border-gray-300 text-purple-700 focus:ring-purple-600" />
                                            {{ role }}
                                        </label>
                                    </fieldset>
                                </details>
                                <p v-if="userForms[user.id].errors.roles" class="mt-1 text-xs text-rose-600">{{ userForms[user.id].errors.roles }}</p>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-gray-600 dark:text-gray-300">{{ user.created_at }}</td>
                            <td class="whitespace-nowrap px-4 py-3">
                                <span :class="user.email_verified ? 'text-emerald-700 dark:text-emerald-300' : 'text-gray-500 dark:text-gray-400'">{{ user.email_verified ? 'Yes' : 'No' }}</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <button type="button" :disabled="userForms[user.id].processing" class="rounded-md bg-purple-700 px-3 py-1.5 text-sm font-semibold text-white hover:bg-purple-800 disabled:opacity-60" @click="saveUser(user)">Save</button>
                                    <button
                                        type="button"
                                        :disabled="user.id === page.props.auth.user.id"
                                        :title="user.id === page.props.auth.user.id ? 'You cannot delete your own account' : 'Delete account'"
                                        class="rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-rose-700 hover:bg-rose-50 disabled:cursor-not-allowed disabled:opacity-40 dark:border-gray-600 dark:text-rose-300 dark:hover:bg-rose-950/30"
                                        @click="deleteUser(user)"
                                    >Delete</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="users.data.length === 0">
                            <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No users match these filters.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <nav v-if="users.links.length > 3" aria-label="User list pages" class="flex flex-wrap justify-end gap-1">
                <Link
                    v-for="link in users.links"
                    :key="link.label"
                    :href="link.url ?? '#'
                    "
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
    </AuthenticatedLayout>
</template>