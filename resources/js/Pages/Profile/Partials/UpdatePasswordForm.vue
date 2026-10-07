<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header class="mb-6 flex items-start gap-4 border-b border-gray-100 pb-5 dark:border-gray-700">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4m-2 0h12a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2v-6a2 2 0 012-2zm6 4v2" />
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Password & sign-in</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Choose a strong password you don't use on other sites.</p>
            </div>
        </header>

        <form @submit.prevent="updatePassword" class="grid gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2 sm:max-w-xl">
                <InputLabel for="current_password" value="Current password" class="dark:!text-gray-300" />

                <TextInput
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    class="mt-1 block w-full bg-white text-gray-900 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                    autocomplete="current-password"
                />

                <InputError
                    :message="form.errors.current_password"
                    class="mt-2"
                />
            </div>

            <div>
                <InputLabel for="password" value="New password" class="dark:!text-gray-300" />

                <TextInput
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full bg-white text-gray-900 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                    autocomplete="new-password"
                />

                <InputError :message="form.errors.password" class="mt-2" />
            </div>

            <div>
                <InputLabel
                    for="password_confirmation"
                    value="Confirm new password"
                    class="dark:!text-gray-300"
                />

                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full bg-white text-gray-900 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                    autocomplete="new-password"
                />

                <InputError
                    :message="form.errors.password_confirmation"
                    class="mt-2"
                />
            </div>

            <div class="flex flex-wrap items-center gap-4 border-t border-gray-100 pt-5 dark:border-gray-700 sm:col-span-2">
                <PrimaryButton :disabled="form.processing">
                    {{ form.processing ? 'Updating...' : 'Update password' }}
                </PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        role="status"
                        class="text-sm font-medium text-emerald-700 dark:text-emerald-300"
                    >
                        Password updated.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
