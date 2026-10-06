<script setup>
import { computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    show: { type: Boolean, default: false },
    roles: { type: Array, required: true },
    permissions: { type: Array, required: true },
    defaultRoles: { type: Array, default: () => ['student'] },
});

const emit = defineEmits(['close', 'saved']);
const form = useForm({
    fname: '',
    mname: '',
    lname: '',
    email: '',
    matric_no: '',
    staff_number: '',
    password: '',
    password_confirmation: '',
    roles: props.defaultRoles,
    permissions: [],
});

const isStudent = computed(() => props.defaultRoles.includes('student'));

watch(() => [props.show, props.defaultRoles], ([show, defaultRoles]) => {
    if (show) {
        form.defaults({ roles: defaultRoles });
        form.reset();
        form.clearErrors();
    }
});

const submit = () => {
    form.post(route('core.user_manager.users.store'), {
        preserveScroll: true,
        onSuccess: () => {
            emit('saved', 'User created.');
            emit('close');
        },
    });
};
</script>

<template>
    <Modal :show="show" max-width="3xl" @close="emit('close')">
        <form @submit.prevent="submit" class="space-y-5 p-6">
            <header>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Add user</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Create an account and set its initial access.</p>
            </header>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="create-user-fname" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">First name</label>
                    <input id="create-user-fname" v-model="form.fname" required autocomplete="given-name" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                    <p v-if="form.errors.fname" class="mt-1 text-sm text-rose-600">{{ form.errors.fname }}</p>
                </div>
                <div>
                    <label for="create-user-mname" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Middle name <span class="font-normal text-gray-500">(optional)</span></label>
                    <input id="create-user-mname" v-model="form.mname" autocomplete="additional-name" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                    <p v-if="form.errors.mname" class="mt-1 text-sm text-rose-600">{{ form.errors.mname }}</p>
                </div>
                <div>
                    <label for="create-user-lname" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Last name</label>
                    <input id="create-user-lname" v-model="form.lname" required autocomplete="family-name" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                    <p v-if="form.errors.lname" class="mt-1 text-sm text-rose-600">{{ form.errors.lname }}</p>
                </div>
                <div>
                    <label for="create-user-email" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                    <input id="create-user-email" v-model="form.email" type="email" required autocomplete="email" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                    <p v-if="form.errors.email" class="mt-1 text-sm text-rose-600">{{ form.errors.email }}</p>
                </div>
                <div v-if="isStudent">
                    <label for="create-user-matric" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Matric number</label>
                    <input id="create-user-matric" v-model="form.matric_no" autocomplete="off" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                    <p v-if="form.errors.matric_no" class="mt-1 text-sm text-rose-600">{{ form.errors.matric_no }}</p>
                </div>
                <div v-else>
                    <label for="create-user-staff" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Staff number</label>
                    <input id="create-user-staff" v-model="form.staff_number" autocomplete="off" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                    <p v-if="form.errors.staff_number" class="mt-1 text-sm text-rose-600">{{ form.errors.staff_number }}</p>
                </div>
                <div>
                    <label for="create-user-password" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Temporary password</label>
                    <input id="create-user-password" v-model="form.password" type="password" required minlength="8" autocomplete="new-password" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                    <p v-if="form.errors.password" class="mt-1 text-sm text-rose-600">{{ form.errors.password }}</p>
                </div>
                <div class="sm:col-span-2">
                    <label for="create-user-password-confirmation" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm password</label>
                    <input id="create-user-password-confirmation" v-model="form.password_confirmation" type="password" required minlength="8" autocomplete="new-password" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                </div>
            </div>

            <fieldset>
                <legend class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Roles</legend>
                <div class="flex flex-wrap gap-x-5 gap-y-2">
                    <label v-for="role in roles.filter((role) => role !== 'student')" :key="role" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <input v-model="form.roles" type="checkbox" :value="role" class="rounded border-gray-300 text-purple-700 focus:ring-purple-600" />
                        {{ role }}
                    </label>
                </div>
                <p v-if="form.errors.roles" class="mt-1 text-sm text-rose-600">{{ form.errors.roles }}</p>
            </fieldset>

            <fieldset>
                <legend class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Direct permissions</legend>
                <div class="grid max-h-40 gap-2 overflow-y-auto sm:grid-cols-2">
                    <label v-for="permission in permissions" :key="permission" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <input v-model="form.permissions" type="checkbox" :value="permission" class="rounded border-gray-300 text-purple-700 focus:ring-purple-600" />
                        {{ permission }}
                    </label>
                </div>
                <p v-if="form.errors.permissions" class="mt-1 text-sm text-rose-600">{{ form.errors.permissions }}</p>
            </fieldset>

            <footer class="flex justify-end gap-2 border-t border-gray-200 pt-4 dark:border-gray-700">
                <button type="button" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700" @click="emit('close')">Cancel</button>
                <button type="submit" :disabled="form.processing" class="rounded-md bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800 disabled:opacity-60">Create user</button>
            </footer>
        </form>
    </Modal>
</template>