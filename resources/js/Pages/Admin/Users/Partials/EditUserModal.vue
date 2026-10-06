<script setup>
import { computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    show: { type: Boolean, default: false },
    user: { type: Object, default: null },
});

const emit = defineEmits(['close', 'saved']);
const form = useForm({ fname: '', mname: '', lname: '', email: '', matric_no: '', staff_number: '' });

watch(() => [props.show, props.user], ([show, user]) => {
    if (show && user) {
        const values = {
            fname: user.fname,
            mname: user.mname ?? '',
            lname: user.lname ?? '',
            email: user.email,
            matric_no: user.matric_no ?? '',
            staff_number: user.staff_number ?? '',
        };
        form.defaults(values);
        form.reset();
        form.clearErrors();
    }
});

const isStudent = computed(() => props.user?.roles?.includes('student'));

const submit = () => {
    form.patch(route('core.user_manager.users.update', props.user.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit('saved', 'User profile updated.');
            emit('close');
        },
    });
};
</script>

<template>
    <Modal :show="show" max-width="lg" @close="emit('close')">
        <form @submit.prevent="submit" class="space-y-5 p-6">
            <header>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Edit profile</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update {{ user?.name }}'s profile details.</p>
            </header>

            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label for="edit-user-fname" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">First name</label>
                    <input id="edit-user-fname" v-model="form.fname" required autocomplete="given-name" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                    <p v-if="form.errors.fname" class="mt-1 text-sm text-rose-600">{{ form.errors.fname }}</p>
                </div>
                <div>
                    <label for="edit-user-mname" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Middle name <span class="font-normal text-gray-500">(optional)</span></label>
                    <input id="edit-user-mname" v-model="form.mname" autocomplete="additional-name" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                    <p v-if="form.errors.mname" class="mt-1 text-sm text-rose-600">{{ form.errors.mname }}</p>
                </div>
                <div>
                    <label for="edit-user-lname" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Last name <span class="font-normal text-gray-500">(optional for existing single-name accounts)</span></label>
                    <input id="edit-user-lname" v-model="form.lname" autocomplete="family-name" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                    <p v-if="form.errors.lname" class="mt-1 text-sm text-rose-600">{{ form.errors.lname }}</p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div class="sm:col-span-2">
                    <label for="edit-user-email" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                    <input id="edit-user-email" v-model="form.email" type="email" required autocomplete="email" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                    <p v-if="form.errors.email" class="mt-1 text-sm text-rose-600">{{ form.errors.email }}</p>
                </div>
                <div v-if="isStudent">
                    <label for="edit-user-matric" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Matric number</label>
                    <input id="edit-user-matric" v-model="form.matric_no" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                    <p v-if="form.errors.matric_no" class="mt-1 text-sm text-rose-600">{{ form.errors.matric_no }}</p>
                </div>
                <div v-else>
                    <label for="edit-user-staff" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Staff number</label>
                    <input id="edit-user-staff" v-model="form.staff_number" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                    <p v-if="form.errors.staff_number" class="mt-1 text-sm text-rose-600">{{ form.errors.staff_number }}</p>
                </div>
            </div>

            <footer class="flex justify-end gap-2 border-t border-gray-200 pt-4 dark:border-gray-700">
                <button type="button" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700" @click="emit('close')">Cancel</button>
                <button type="submit" :disabled="form.processing" class="rounded-md bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800 disabled:opacity-60">Save profile</button>
            </footer>
        </form>
    </Modal>
</template>