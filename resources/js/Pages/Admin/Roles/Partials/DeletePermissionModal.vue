<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    show: { type: Boolean, default: false },
    permission: { type: Object, default: null },
});

const emit = defineEmits(['close', 'saved']);
const form = useForm({});
const canDelete = computed(() => props.permission
    && props.permission.name !== 'manage roles'
    && props.permission.roles_count === 0
    && props.permission.users_count === 0);

const submit = () => {
    if (!canDelete.value) return;

    form.delete(route('admin.permissions.destroy', props.permission.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit('saved', 'Permission deleted.');
            emit('close');
        },
    });
};
</script>

<template>
    <Modal :show="show" max-width="md" @close="emit('close')">
        <form @submit.prevent="submit" class="space-y-5 p-6">
            <header>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Delete permission</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Delete <span class="font-semibold">{{ permission?.name }}</span>?
                </p>
                <p v-if="permission?.name === 'manage roles'" class="mt-2 text-sm text-amber-700 dark:text-amber-300">This permission is required to manage roles and cannot be deleted.</p>
                <p v-else-if="permission && !canDelete" class="mt-2 text-sm text-amber-700 dark:text-amber-300">
                    Remove this permission from {{ permission.roles_count }} roles and {{ permission.users_count }} users before deleting it.
                </p>
                <p v-if="form.errors.permission" class="mt-2 text-sm text-rose-600">{{ form.errors.permission }}</p>
            </header>

            <footer class="flex justify-end gap-2 border-t border-gray-200 pt-4 dark:border-gray-700">
                <button type="button" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700" @click="emit('close')">Cancel</button>
                <button type="submit" :disabled="!canDelete || form.processing" class="rounded-md bg-rose-700 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-800 disabled:cursor-not-allowed disabled:opacity-50">Delete permission</button>
            </footer>
        </form>
    </Modal>
</template>