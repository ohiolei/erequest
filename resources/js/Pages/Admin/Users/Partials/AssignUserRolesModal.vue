<script setup>
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    show: { type: Boolean, default: false },
    user: { type: Object, default: null },
    roles: { type: Array, required: true },
});

const emit = defineEmits(['close', 'saved']);
const form = useForm({ roles: [] });

watch(() => [props.show, props.user], ([show, user]) => {
    if (show && user) {
        const values = { roles: [...user.roles] };
        form.defaults(values);
        form.reset();
        form.clearErrors();
    }
});

const submit = () => {
    form.patch(route('core.user_manager.users.roles.update', props.user.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit('saved', 'User roles updated.');
            emit('close');
        },
    });
};
</script>

<template>
    <Modal :show="show" max-width="lg" @close="emit('close')">
        <form @submit.prevent="submit" class="space-y-5 p-6">
            <header>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Assign roles</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Select the roles for {{ user?.name }}.</p>
            </header>

            <fieldset>
                <legend class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Available roles</legend>
                <div class="space-y-2">
                    <label v-for="role in roles" :key="role" class="flex items-center gap-3 rounded border border-gray-200 px-3 py-2 text-sm text-gray-700 dark:border-gray-700 dark:text-gray-300">
                        <input v-model="form.roles" type="checkbox" :value="role" class="rounded border-gray-300 text-purple-700 focus:ring-purple-600" />
                        {{ role }}
                    </label>
                </div>
                <p v-if="form.errors.roles" class="mt-2 text-sm text-rose-600">{{ form.errors.roles }}</p>
            </fieldset>

            <footer class="flex justify-end gap-2 border-t border-gray-200 pt-4 dark:border-gray-700">
                <button type="button" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700" @click="emit('close')">Cancel</button>
                <button type="submit" :disabled="form.processing" class="rounded-md bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800 disabled:opacity-60">Save roles</button>
            </footer>
        </form>
    </Modal>
</template>