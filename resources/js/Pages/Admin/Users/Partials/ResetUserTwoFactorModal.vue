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
    form.post(route('core.user_manager.users.reset-2fa', props.user.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit('saved', '2FA has been reset for this user.');
            emit('close');
        },
    });
};
</script>

<template>
    <Modal :show="show" max-width="md" @close="emit('close')">
        <form @submit.prevent="submit" class="space-y-5 p-6">
            <header>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Reset 2FA</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Clear any existing 2FA setup for <span class="font-semibold">{{ user?.name }}</span>. This will force the user to set up 2FA again on their next login.
                </p>
                <p v-if="form.errors.user" class="mt-2 text-sm text-rose-600">{{ form.errors.user }}</p>
            </header>

            <footer class="flex justify-end gap-2 border-t border-gray-200 pt-4 dark:border-gray-700">
                <button type="button" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700" @click="emit('close')">Cancel</button>
                <button type="submit" :disabled="form.processing" class="rounded-md bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800 disabled:opacity-60">Reset 2FA</button>
            </footer>
        </form>
    </Modal>
</template>
