<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({ chats: Object, categories: Array, filters: Object, allowedCategories: Array });
</script>

<template>
    <Head title="Support Chats" />

    <AuthenticatedLayout>
        <div class="py-6">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Support Chats</h1>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage and respond to student enquiries by category.</p>
                    </div>
                </div>

                <div v-if="chats.data.length === 0" class="rounded-xl border border-dashed border-gray-300 bg-white p-12 text-center dark:border-gray-700 dark:bg-gray-800">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400 dark:bg-gray-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                    </div>
                    <p class="mt-3 text-sm font-medium text-gray-900 dark:text-gray-100">No chats yet</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Chats matching your categories will appear here.</p>
                </div>

                <div v-else class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Student</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Category</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Subject</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Assigned To</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Last Updated</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="chat in chats.data" :key="chat.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="whitespace-nowrap px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ chat.user }}</td>
                                <td class="whitespace-nowrap px-4 py-3 capitalize text-gray-600 dark:text-gray-300">{{ chat.category.replace('_', ' ') }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ chat.subject }}</td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    <span class="rounded-full px-2.5 py-0.5 text-xs font-medium capitalize" :class="{
                                        'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300': chat.status === 'open',
                                        'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300': chat.status === 'in_progress',
                                        'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300': chat.status === 'resolved',
                                        'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300': chat.status === 'closed',
                                    }">
                                        {{ chat.status.replace('_', ' ') }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-gray-600 dark:text-gray-300">{{ chat.assigned_to || '—' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-gray-500 dark:text-gray-400">{{ chat.last_message_at }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <Link :href="route('admin.chats.show', chat.id)" class="inline-flex items-center gap-1 rounded-md bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-500">
                                        Open
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="chats.links" class="mt-4 flex justify-end gap-1">
                    <Link v-for="link in chats.links" :key="link.label" :href="link.url ?? '#'" :class="['rounded border px-3 py-2 text-center text-sm', link.active ? 'border-emerald-700 bg-emerald-700 text-white' : 'border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300', !link.url ? 'pointer-events-none opacity-40' : '']">
                        {{ link.label }}
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
