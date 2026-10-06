<script setup>
import { useForm } from '@inertiajs/vue3';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({});

const form = useForm({
    category: '',
    subject: '',
    message: '',
});

const submit = () => {
    form.post(route('chats.store'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="New Support Chat" />

    <AuthenticatedLayout>
        <div class="py-6">
            <div class="mx-auto max-w-2xl px-4 sm:px-6">
                <div class="mb-6 flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">New Support Chat</h1>
                    <Link :href="route('chats.index')" class="text-sm text-emerald-700 hover:text-emerald-500">Back to chats</Link>
                </div>

                <form @submit.prevent="submit" class="space-y-5 rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                    <div>
                        <label for="category" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                        <select id="category" v-model="form.category" required class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                            <option value="">Select a category</option>
                            <option value="registry">Registry</option>
                            <option value="bursary">Bursary</option>
                            <option value="exams_records">Exams & Records</option>
                            <option value="complaints">Complaints</option>
                        </select>
                        <p v-if="form.errors.category" class="mt-1 text-sm text-rose-600">{{ form.errors.category }}</p>
                    </div>

                    <div>
                        <label for="subject" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Subject</label>
                        <input id="subject" v-model="form.subject" type="text" required class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white" />
                        <p v-if="form.errors.subject" class="mt-1 text-sm text-rose-600">{{ form.errors.subject }}</p>
                    </div>

                    <div>
                        <label for="message" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Message</label>
                        <textarea id="message" v-model="form.message" rows="4" required class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white"></textarea>
                        <p v-if="form.errors.message" class="mt-1 text-sm text-rose-600">{{ form.errors.message }}</p>
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <Link :href="route('chats.index')" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">Cancel</Link>
                        <button type="submit" :disabled="form.processing" class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500 disabled:opacity-60">Start Chat</button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
