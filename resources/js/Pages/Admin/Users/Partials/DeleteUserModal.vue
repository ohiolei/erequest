<script setup>
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    show: { type: Boolean, default: false },
    user: { type: Object, default: null },
});

const emit = defineEmits(['close', 'saved']);
const form = useForm({});

const submit = () => {
    form.delete(route('core.user_manager.users.destroy', props.user.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit('saved', 'User account deleted.');
            emit('close');
        },
    });
};
</script>

<template>
    <Modal :show="show" max-width="md" @close="emit('close')">
        <form @submit.prevent="submit" class="space-y-5 p-6">
            <header>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Delete user</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Delete <span class="font-semibold">{{ user?.name }}</span>’s account? This action cannot be undone.
                </p>
                <p v-if="form.errors.user" class="mt-2 text-sm text-rose-600">{{ form.errors.user }}</p>
            </header>

            <footer class="flex justify-end gap-2 border-t border-gray-200 pt-4 dark:border-gray-700">
                <button type="button" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700" @click="emit('close')">Cancel</button>
                <button type="submit" :disabled="form.processing" class="rounded-md bg-rose-700 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-800 disabled:opacity-60">Delete account</button>
            </footer>
        </form>
    </Modal>
</template>