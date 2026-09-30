<script setup>
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    show: { type: Boolean, default: false },
});

const emit = defineEmits(['close', 'saved']);
const form = useForm({ name: '' });

watch(() => props.show, (show) => {
    if (show) {
        form.reset();
        form.clearErrors();
    }
});

const submit = () => {
    form.post(route('admin.permissions.store'), {
        preserveScroll: true,
        onSuccess: () => {
            emit('saved', 'Permission created.');
            emit('close');
        },
    });
};
</script>

<template>
    <Modal :show="show" max-width="lg" @close="emit('close')">
        <form @submit.prevent="submit" class="space-y-5 p-6">
            <header>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Create permission</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Use a short action name, such as “approve requests”.</p>
            </header>

            <div>
                <label for="create-permission-name" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Permission name</label>
                <input id="create-permission-name" v-model="form.name" type="text" required maxlength="100" placeholder="approve requests" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                <p v-if="form.errors.name" class="mt-1 text-sm text-rose-600">{{ form.errors.name }}</p>
            </div>

            <footer class="flex justify-end gap-2 border-t border-gray-200 pt-4 dark:border-gray-700">
                <button type="button" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700" @click="emit('close')">Cancel</button>
                <button type="submit" :disabled="form.processing" class="rounded-md bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800 disabled:opacity-60">Create permission</button>
            </footer>
        </form>
    </Modal>
</template>