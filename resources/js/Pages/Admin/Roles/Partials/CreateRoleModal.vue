<script setup>
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    show: { type: Boolean, default: false },
    permissions: { type: Array, required: true },
});

const emit = defineEmits(['close', 'saved']);
const form = useForm({ name: '', permissions: [] });

watch(() => props.show, (show) => {
    if (show) {
        form.reset();
        form.clearErrors();
    }
});

const submit = () => {
    form.post(route('admin.roles.store'), {
        preserveScroll: true,
        onSuccess: () => {
            emit('saved', 'Role created.');
            emit('close');
        },
    });
};
</script>

<template>
    <Modal :show="show" max-width="2xl" @close="emit('close')">
        <form @submit.prevent="submit" class="space-y-5 p-6">
            <header>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Create role</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Choose a unique role name and its initial permissions.</p>
            </header>

            <div>
                <label for="create-role-name" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Role name</label>
                <input id="create-role-name" v-model="form.name" type="text" required maxlength="50" placeholder="request-reviewer" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                <p v-if="form.errors.name" class="mt-1 text-sm text-rose-600">{{ form.errors.name }}</p>
            </div>

            <fieldset>
                <legend class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Initial permissions</legend>
                <div class="grid max-h-72 gap-2 overflow-y-auto sm:grid-cols-2">
                    <label v-for="permission in permissions" :key="permission" class="flex items-center gap-2 rounded border border-gray-200 px-3 py-2 text-sm text-gray-700 dark:border-gray-700 dark:text-gray-300">
                        <input v-model="form.permissions" type="checkbox" :value="permission" class="rounded border-gray-300 text-purple-700 focus:ring-purple-600" />
                        {{ permission }}
                    </label>
                </div>
                <p v-if="form.errors.permissions" class="mt-1 text-sm text-rose-600">{{ form.errors.permissions }}</p>
            </fieldset>

            <footer class="flex justify-end gap-2 border-t border-gray-200 pt-4 dark:border-gray-700">
                <button type="button" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700" @click="emit('close')">Cancel</button>
                <button type="submit" :disabled="form.processing" class="rounded-md bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800 disabled:opacity-60">Create role</button>
            </footer>
        </form>
    </Modal>
</template>