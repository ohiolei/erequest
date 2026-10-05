<script setup>
import { useForm } from '@inertiajs/vue3';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({ chat: Object });

const form = useForm({
    message: '',
});

const submit = () => {
    form.post(`/admin/chats/${props.chat.id}/messages`, {
        onSuccess: () => form.reset('message'),
        preserveScroll: true,
    });
};

const updateStatus = () => {
    form.post(`/admin/chats/${props.chat.id}/status`, {
        preserveScroll: true,
    });
};

const statusClass = (status) => {
    const map = {
        open: 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
        in_progress: 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
        resolved: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
        closed: 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300',
    };
    return map[status] || 'bg-gray-100 text-gray-700';
};

const getInitials = (name) => {
    const text = String(name || '');
    return text.split(' ').filter(Boolean).map((part) => part.charAt(0)).join('').slice(0, 2).toUpperCase();
};
</script>

<template>
    <Head :title="`${chat.subject} - Support Chat`" />

    <AuthenticatedLayout>
        <div class="flex flex-col" style="min-height: calc(100vh - 8rem);">
            <div class="flex items-center justify-between border-b border-gray-200 bg-white px-4 py-3 dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-sm font-semibold text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
                        {{ getInitials(chat.user.name) }}
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">{{ chat.user.name }}</h2>
                        <p class="text-xs text-gray-500 capitalize dark:text-gray-400">{{ chat.category.replace('_', ' ') }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span :class="['rounded-full px-2.5 py-0.5 text-xs font-medium capitalize', statusClass(chat.status)]">
                        {{ chat.status.replace('_', ' ') }}
                    </span>
                    <select v-model="chat.status" @change="updateStatus" class="rounded-md border-gray-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                        <option value="open">Open</option>
                        <option value="in_progress">In Progress</option>
                        <option value="resolved">Resolved</option>
                        <option value="closed">Closed</option>
                    </select>
                    <Link href="/admin/chats" class="text-xs text-emerald-700 hover:text-emerald-500 dark:text-emerald-400">Back</Link>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto bg-gray-50 p-4 dark:bg-gray-900">
                <div class="mx-auto max-w-3xl space-y-4">
                    <div v-if="!chat.messages?.length" class="flex flex-col items-center justify-center py-20 text-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-400">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                        </div>
                        <p class="mt-3 text-sm font-medium text-gray-900 dark:text-gray-100">No messages yet</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Start the conversation by sending a reply below.</p>
                    </div>

                    <div v-for="msg in chat.messages" :key="msg.id" class="flex flex-col gap-1" :class="msg.user.id === $page.props.auth.user.id ? 'items-end' : 'items-start'">
                        <div class="flex items-end gap-2" :class="msg.user.id === $page.props.auth.user.id ? 'flex-row-reverse' : 'flex-row'">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-semibold" :class="msg.user.id === $page.props.auth.user.id ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200'">
                                {{ getInitials(msg.user.name) }}
                            </div>
                            <div class="max-w-[75%] rounded-2xl px-4 py-2.5 text-sm shadow-sm" :class="msg.user.id === $page.props.auth.user.id ? 'rounded-br-sm bg-emerald-600 text-white' : 'rounded-bl-sm bg-white text-gray-900 dark:bg-gray-800 dark:text-gray-100'">
                                <p class="text-xs font-semibold mb-1" :class="msg.user.id === $page.props.auth.user.id ? 'text-emerald-100' : 'text-gray-500'">{{ msg.user.name }}</p>
                                <p class="leading-relaxed">{{ msg.message }}</p>
                            </div>
                        </div>
                        <span class="text-[11px] text-gray-400" :class="msg.user.id === $page.props.auth.user.id ? 'mr-10' : 'ml-10'">{{ msg.created_at }}</span>
                    </div>
                </div>
            </div>

            <form v-if="chat.status !== 'closed'" @submit.prevent="submit" class="border-t border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                <div class="mx-auto max-w-3xl">
                    <div class="flex items-end gap-3">
                        <div class="relative flex-1">
                            <textarea
                                id="message"
                                v-model="form.message"
                                rows="1"
                                required
                                placeholder="Type your reply..."
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                @input="(e) => { e.target.rows = 1; e.target.rows = Math.min(6, e.target.scrollHeight / 40); }"
                            ></textarea>
                            <p v-if="form.errors.message" class="mt-1 text-xs text-rose-600">{{ form.errors.message }}</p>
                        </div>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-600 text-white hover:bg-emerald-500 disabled:opacity-60"
                            title="Send reply"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
            <div v-else class="border-t border-gray-200 bg-white p-4 text-center dark:border-gray-700 dark:bg-gray-800">
                <p class="text-sm text-gray-500">This chat is closed.</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
